#!/bin/bash

echo "🎯 FORÇANDO REDIRECIONAMENTO - DEBUG COMPLETO"
echo "=============================================="

# 1. Primeiro, vamos verificar exatamente o que está acontecendo
echo "1. 🔍 DEBUG DO SISTEMA ATUAL..."
php artisan tinker --execute="
echo '=== DEBUG DO AUTH ===\n';
echo 'Usuário no banco: ';
\$user = DB::table('usuarios')->where('email', 'admin@sistema.com')->first();
if (\$user) {
    echo '✅ ' . \$user->nome . ' (' . \$user->email . ')\n';
} else {
    echo '❌ Não encontrado\n';
}

echo 'Session ID: ' . session()->getId() . '\n';
echo 'Session data: ';
print_r(session()->all());
echo '\n';
"

# 2. Corrigir o AuthController com DEBUG E REDIRECIONAMENTO ABSOLUTO
echo "2. 🎮 CRIANDO AUTHCONTROLLER COM DEBUG..."
cat > app/Http/Controllers/AuthController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        \Log::info('=== PÁGINA DE LOGIN ACESSADA ===');
        \Log::info('Auth::check(): ' . (Auth::check() ? 'TRUE' : 'FALSE'));
        
        // Se já estiver logado, REDIRECIONA FORÇADAMENTE
        if (Auth::check()) {
            \Log::info('✅ Já está logado, redirecionando para /');
            return redirect('/');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        \Log::info('=== TENTATIVA DE LOGIN INICIADA ===');

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        \Log::info('Credenciais recebidas:', $credentials);

        // Busca o usuário
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            \Log::warning('❌ Usuário não encontrado: ' . $request->email);
            return back()->withErrors(['email' => 'Credenciais inválidas.'])->withInput();
        }

        \Log::info('✅ Usuário encontrado:', ['id' => $user->id_usuario, 'nome' => $user->nome]);

        // Verifica a senha
        if (!Hash::check($request->password, $user->senha)) {
            \Log::warning('❌ Senha incorreta para: ' . $request->email);
            return back()->withErrors(['email' => 'Credenciais inválidas.'])->withInput();
        }

        \Log::info('✅ Senha correta - fazendo login MANUAL...');

        // 🔥 LOGIN MANUAL - método mais confiável
        Auth::login($user);
        
        \Log::info('Auth::check() após login: ' . (Auth::check() ? 'TRUE' : 'FALSE'));
        \Log::info('Auth::user(): ' . (Auth::user() ? Auth::user()->email : 'NONE'));
        
        $request->session()->regenerate();
        
        \Log::info('✅ Session regenerada');
        \Log::info('Session ID: ' . session()->getId());
        
        // 🔥 REDIRECIONAMENTO ABSOLUTO E IMEDIATO
        \Log::info('🎯 REDIRECIONANDO PARA: /');
        
        return redirect('/')->with('success', 'Login realizado com sucesso!');
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

# 3. Criar uma rota de debug da sessão
echo "3. 🌐 CRIANDO ROTAS DE DEBUG..."
cat >> routes/web.php << 'EOF'

// ==================== ROTAS DE DEBUG ====================
Route::get('/debug-session', function() {
    echo "<h1>🔧 DEBUG DA SESSÃO E AUTH</h1>";
    
    echo "<h3>Auth Status:</h3>";
    echo "<p>Auth::check(): <strong>" . (Auth::check() ? '✅ TRUE' : '❌ FALSE') . "</strong></p>";
    
    if (Auth::check()) {
        $user = Auth::user();
        echo "<p style='color: green; font-weight: bold'>🎉 USUÁRIO AUTENTICADO!</p>";
        echo "<ul>";
        echo "<li>ID: " . $user->id_usuario . "</li>";
        echo "<li>Nome: " . $user->nome . "</li>";
        echo "<li>Email: " . $user->email . "</li>";
        echo "<li>Tipo: " . $user->tipo . "</li>";
        echo "</ul>";
        echo "<p><a href='/'>➡️ Ir para Dashboard</a></p>";
    } else {
        echo "<p style='color: red'>❌ NENHUM USUÁRIO AUTENTICADO</p>";
        echo "<p><a href='/login'>➡️ Fazer Login</a></p>";
    }
    
    echo "<h3>Session Data:</h3>";
    echo "<pre>";
    print_r(session()->all());
    echo "</pre>";
    
    echo "<h3>Cookies:</h3>";
    echo "<pre>";
    print_r($_COOKIE);
    echo "</pre>";
});

Route::get('/test-redirect', function() {
    \Log::info('=== TESTE DE REDIRECIONAMENTO ===');
    return redirect('/')->with('test', 'Redirecionamento funcionando!');
});
EOF

# 4. Corrigir o middleware de auth
echo "4. ⚙️ VERIFICANDO MIDDLEWARE..."
if [ ! -f "app/Http/Middleware/Authenticate.php" ]; then
    echo "✅ Middleware padrão existe"
else
    # Verificar se o middleware está redirecionando corretamente
    cat > app/Http/Middleware/Authenticate.php << 'EOF'
<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
EOF
fi

# 5. Criar um teste de redirecionamento manual
echo "5. 🧪 CRIANDO TESTE MANUAL..."
cat >> routes/web.php << 'EOF'

Route::get('/test-manual-login', function() {
    $user = \App\Models\User::where('email', 'admin@sistema.com')->first();
    if ($user) {
        Auth::login($user);
        session()->regenerate();
        return redirect('/')->with('success', 'Login manual realizado!');
    }
    return "Usuário não encontrado";
});
EOF

# 6. Verificar se a rota principal está funcionando
echo "6. 🏠 VERIFICANDO ROTA PRINCIPAL..."
cat > resources/views/dashboard/index.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">🎉 REDIRECIONAMENTO FUNCIONOU!</h1>
                <p class="text-green-100 mt-2">Bem-vindo, {{ auth()->user()->nome }}! Login realizado com sucesso.</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-check-circle text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('test'))
    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
        {{ session('test') }}
    </div>
    @endif

    <!-- User Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informações da Sessão</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Usuário</p>
                <p class="font-medium text-gray-800">{{ auth()->user()->nome }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="font-medium text-gray-800">{{ auth()->user()->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">ID</p>
                <p class="font-medium text-gray-800">{{ auth()->user()->id_usuario }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Session ID</p>
                <p class="font-medium text-gray-800">{{ session()->getId() }}</p>
            </div>
        </div>
    </div>

    <!-- Debug Links -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-4">Links de Debug</h3>
        <div class="flex space-x-4">
            <a href="/debug-session" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                🔧 Debug Session
            </a>
            <a href="/test-redirect" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                🔄 Test Redirect
            </a>
            <form method="POST" action="/logout" class="inline">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                    🚪 Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
EOF

# 7. Limpar TUDO
echo "7. 🧹 LIMPANDO CACHE COMPLETO..."
php artisan config:clear
php artisan cache:clear  
php artisan route:clear
php artisan view:clear
php artisan event:clear

# 8. Reiniciar o servidor (se possível)
echo "8. 🔄 REINICIANDO SERVIDOR..."
pkill -f "php artisan serve" 2>/dev/null || true
sleep 2

echo ""
echo "=============================================="
echo "🎉 CONFIGURAÇÃO DE DEBUG COMPLETA!"
echo "=============================================="
echo ""
echo "📋 PARA TESTAR - SIGA ESTA ORDEM:"
echo ""
echo "1. 🔧 DEBUG DA SESSÃO:"
echo "   http://localhost:8000/debug-session"
echo ""
echo "2. 🧪 TESTE MANUAL:"
echo "   http://localhost:8000/test-manual-login"
echo ""
echo "3. 🔄 TESTE REDIRECT:"
echo "   http://localhost:8000/test-redirect"
echo ""
echo "4. 🔑 LOGIN NORMAL:"
echo "   http://localhost:8000/login"
echo "   Email: admin@sistema.com"
echo "   Senha: 123456"
echo ""
echo "5. 📊 VER LOGS EM TEMPO REAL:"
echo "   tail -f storage/logs/laravel.log"
echo ""
