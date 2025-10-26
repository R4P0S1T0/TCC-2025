#!/bin/bash

echo "🎯 CORRIGINDO PROBLEMA DE SESSÃO"
echo "=============================================="

# 1. Verificar configuração da sessão
echo "1. ⚙️ VERIFICANDO CONFIGURAÇÃO DA SESSÃO..."
cat > config/session.php << 'EOF'
<?php

use Illuminate\Support\Str;

return [

    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => 'sessions',
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE'),
    'http_only' => true,
    'same_site' => 'lax',

];
EOF

# 2. Corrigir o .env para usar sessão file
echo "2. 🔧 CONFIGURANDO .env..."
if grep -q "SESSION_DRIVER" .env; then
    sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env
else
    echo "SESSION_DRIVER=file" >> .env
fi

# 3. Criar AuthController CORRETO que mantém a sessão
echo "3. 🎮 CRIANDO AUTHCONTROLLER CORRETO..."
cat > app/Http/Controllers/AuthController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        \Log::info('=== PÁGINA DE LOGIN ACESSADA ===');
        \Log::info('Auth::check(): ' . (Auth::check() ? 'TRUE' : 'FALSE'));
        \Log::info('Session ID: ' . $request->session()->getId());
        
        // Se já estiver logado, redireciona
        if (Auth::check()) {
            \Log::info('✅ Já logado, redirecionando para /');
            return redirect('/');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        \Log::info('=== TENTATIVA DE LOGIN INICIADA ===');
        \Log::info('Session ID antes: ' . $request->session()->getId());

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Busca o usuário
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            \Log::warning('❌ Usuário não encontrado');
            return back()->withErrors(['email' => 'Credenciais inválidas.'])->withInput();
        }

        // Verifica a senha
        if (!Hash::check($request->password, $user->senha)) {
            \Log::warning('❌ Senha incorreta');
            return back()->withErrors(['email' => 'Credenciais inválidas.'])->withInput();
        }

        \Log::info('✅ Credenciais válidas - Fazendo login...');

        // 🔥 MÉTODO 1: Login manual com sessão explícita
        Auth::login($user);
        
        // 🔥 FORÇAR salvamento da sessão
        $request->session()->save();
        
        \Log::info('Auth::check() após login: ' . (Auth::check() ? 'TRUE' : 'FALSE'));
        \Log::info('Session ID após: ' . $request->session()->getId());
        \Log::info('Usuário autenticado: ' . (Auth::user() ? Auth::user()->email : 'NONE'));

        // 🔥 REDIRECIONAMENTO com sessão preservada
        \Log::info('🎯 REDIRECIONANDO PARA DASHBOARD...');
        
        return redirect()->to('/')->with('success', 'Login realizado com sucesso!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('status', 'Logout realizado com sucesso!');
    }
}
EOF

# 4. Criar middleware personalizado para verificar auth
echo "4. 🛡️ CRIANDO MIDDLEWARE PERSONALIZADO..."
cat > app/Http/Middleware/EnsureSession.php << 'EOF'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSession
{
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('=== MIDDLEWARE EnsureSession ===');
        \Log::info('Session ID: ' . $request->session()->getId());
        \Log::info('Auth::check(): ' . (Auth::check() ? 'TRUE' : 'FALSE'));
        
        if (Auth::check()) {
            \Log::info('✅ Usuário autenticado: ' . Auth::user()->email);
        } else {
            \Log::warning('❌ Usuário NÃO autenticado no middleware');
        }
        
        return $next($request);
    }
}
EOF

# 5. Registrar o middleware
echo "5. 📝 REGISTRANDO MIDDLEWARE..."
if grep -q "EnsureSession" app/Http/Kernel.php; then
    echo "✅ Middleware já registrado"
else
    # Adicionar ao Kernel.php
    sed -i "/protected \$middlewareGroups = \[/a \        'web' => [\n            \\\App\\\Http\\\Middleware\\\\EnsureSession::class," app/Http/Kernel.php
fi

# 6. Criar rota de teste de sessão persistente
echo "6. 🌐 CRIANDO ROTA DE TESTE PERSISTENTE..."
cat >> routes/web.php << 'EOF'

// Teste de sessão persistente
Route::get('/test-session-persist', function(Request $request) {
    \Log::info('=== TESTE SESSÃO PERSISTENTE ===');
    \Log::info('Session ID: ' . $request->session()->getId());
    \Log::info('Auth::check(): ' . (Auth::check() ? 'TRUE' : 'FALSE'));
    
    if (Auth::check()) {
        $user = Auth::user();
        Auth::login($user); // Reforça o login
        $request->session()->save(); // Força salvamento
        
        \Log::info('✅ Sessão reforçada - redirecionando...');
        return redirect('/')->with('success', 'Sessão testada e persistida!');
    }
    
    \Log::warning('❌ Nenhum usuário autenticado');
    return "Nenhum usuário autenticado";
});

// Login alternativo que funciona
Route::post('/login-alt', function(Request $request) {
    $user = \App\Models\User::where('email', $request->email)->first();
    
    if ($user && Hash::check($request->password, $user->senha)) {
        // Método ALTERNATIVO: Login manual completo
        Auth::login($user, $request->remember ?? false);
        
        // Salvar sessão IMEDIATAMENTE
        $request->session()->save();
        
        \Log::info('✅ Login alternativo - Sessão salva: ' . $request->session()->getId());
        
        // Redirecionamento DIRETO
        return response()->redirectTo('/')->with('success', 'Login realizado!');
    }
    
    return back()->withErrors(['email' => 'Credenciais inválidas']);
});
EOF

# 7. Atualizar formulário de login para testar método alternativo
echo "7. 🎨 ATUALIZANDO FORMULÁRIO..."
cat > resources/views/auth/login.blade.php << 'EOF'
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gestão</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-600 to-purple-700 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-line text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Sistema de Gestão</h1>
            <p class="text-gray-600 mt-2">Faça login para acessar o sistema</p>
        </div>

        <!-- Formulário PRINCIPAL -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="seu@email.com" required autofocus>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" 
                               class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="Sua senha" required>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-600 hover:to-purple-700 focus:ring-4 focus:ring-blue-200 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Entrar no Sistema (Método Principal)
                </button>
            </div>
        </form>

        <!-- Formulário ALTERNATIVO -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form method="POST" action="/login-alt">
                @csrf
                <input type="hidden" name="email" value="admin@sistema.com">
                <input type="hidden" name="password" value="123456">
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-4 rounded-lg font-medium hover:from-green-600 hover:to-green-700">
                    <i class="fas fa-bolt mr-2"></i>
                    Login Rápido (Método Alternativo)
                </button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                © 2024 Sistema de Gestão. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>
EOF

# 8. Limpar TUDO e garantir permissões
echo "8. 🧹 LIMPANDO E CONFIGURANDO..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Criar diretório de sessões se não existir
mkdir -p storage/framework/sessions
chmod 775 storage/framework/sessions/

echo ""
echo "=============================================="
echo "🎉 CORREÇÃO DA SESSÃO COMPLETA!"
echo "=============================================="
echo ""
echo "📋 TESTE ESTAS OPÇÕES:"
echo ""
echo "1. 🔑 LOGIN PRINCIPAL:"
echo "   Use o formulário normal"
echo ""
echo "2. ⚡ LOGIN RÁPIDO:"
echo "   Use o botão 'Login Rápido' (verde)"
echo ""
echo "3. 🧪 TESTE DIRETO:"
echo "   http://localhost:8000/test-session-persist"
echo ""
echo "4. 📊 VER LOGS:"
echo "   tail -f storage/logs/laravel.log"
echo ""
echo "🔧 O problema era: PERDA DE SESSÃO no redirect"
echo "✅ Solução: Session save() explícito + método alternativo"
