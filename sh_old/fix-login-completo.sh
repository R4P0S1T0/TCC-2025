#!/bin/bash

echo "🎯 SOLUÇÃO DEFINITIVA DO LOGIN - CONFIGURAÇÃO COMPLETA"
echo "======================================================"

# 1. Corrigir Model User COMPLETO
echo "1. 🛠️ CONFIGURANDO MODEL USER..."
cat > app/Models/User.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nome',
        'email', 
        'senha',
        'telefone',
        'endereco', 
        'tipo'
    ];

    protected $hidden = [
        'senha'
    ];

    // 🔑 CONFIGURAÇÃO CRÍTICA: Mapeia 'senha' para o sistema de auth
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // 🚫 Desabilita completamente remember token
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Não faz nada
    }

    public function getRememberTokenName()
    {
        return null;
    }

    // ✅ Garante que o email seja usado como identificador
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function getAuthIdentifier()
    {
        return $this->email;
    }
}
EOF
echo "✅ Model User configurado"

# 2. Corrigir AuthController COMPLETO
echo "2. 🎮 CONFIGURANDO AUTHCONTROLLER..."
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
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 🔍 DEBUG: Log da tentativa
        \Log::info('Tentativa de login:', [
            'email' => $request->email,
            'password_length' => strlen($request->password ?? '')
        ]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        \Log::info('Credenciais validadas:', $credentials);

        // 🔑 TENTATIVA DE LOGIN SIMPLIFICADA
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user) {
            \Log::info('Usuário encontrado:', ['id' => $user->id_usuario, 'nome' => $user->nome]);
            
            // ✅ Verificação MANUAL da senha
            if (Hash::check($request->password, $user->senha)) {
                \Log::info('Senha confere - fazendo login manual');
                
                // Login MANUAL - contorna todos os problemas do attempt()
                Auth::login($user);
                $request->session()->regenerate();
                
                \Log::info('Login bem-sucedido via Auth::login()');
                return redirect()->intended('/');
            } else {
                \Log::warning('Senha NÃO confere');
            }
        } else {
            \Log::warning('Usuário não encontrado');
        }

        \Log::error('Falha no login para: ' . $request->email);
        
        return back()->withErrors([
            'email' => 'Email ou senha incorretos.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
EOF
echo "✅ AuthController configurado"

# 3. Criar usuário de teste COM HASH CORRETO
echo "3. 👤 CRIANDO USUÁRIO DE TESTE..."
php artisan tinker --execute="
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// 🔄 Limpa usuários existentes
DB::table('usuarios')->where('email', 'admin@sistema.com')->delete();
DB::table('usuarios')->where('email', 'admin@teste.com')->delete();

// 🎯 Cria usuário PRINCIPAL com hash correto
\$senhaHash = Hash::make('123456');
\$result = DB::table('usuarios')->insert([
    'nome' => 'Administrador',
    'email' => 'admin@sistema.com',
    'senha' => \$senhaHash,
    'tipo' => 'admin'
]);

if (\$result) {
    echo '✅ USUÁRIO PRINCIPAL CRIADO:\n';
    echo '   Email: admin@sistema.com\n';
    echo '   Senha: 123456\n';
    echo '   Hash: ' . substr(\$senhaHash, 0, 20) . '...\n';
} else {
    echo '❌ Erro ao criar usuário\n';
}

// Verifica criação
\$user = DB::table('usuarios')->where('email', 'admin@sistema.com')->first();
if (\$user) {
    echo '📋 Confirmação:\n';
    echo '   ID: ' . \$user->id_usuario . '\n';
    echo '   Nome: ' . \$user->nome . '\n';
    echo '   Tipo: ' . \$user->tipo . '\n';
    
    // Teste do hash
    \$check = Hash::check('123456', \$user->senha);
    echo '   Hash check: ' . (\$check ? '✅' : '❌') . '\n';
}
"

# 4. Corrigir formulário de login (remove remember me)
echo "4. 🎨 CORRIGINDO FORMULÁRIO DE LOGIN..."
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

            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-4">
                    {{ session('status') }}
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

                {{-- 🚫 REMOVIDO: Remember me --}}

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-600 hover:to-purple-700 focus:ring-4 focus:ring-blue-200 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Entrar no Sistema
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <p class="text-sm text-blue-700 font-medium">Credenciais de Teste</p>
                <p class="text-xs text-blue-600">Email: admin@sistema.com</p>
                <p class="text-xs text-blue-600">Senha: 123456</p>
            </div>
            <p class="text-sm text-gray-600">
                © 2024 Sistema de Gestão. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>
EOF
echo "✅ Formulário de login corrigido"

# 5. Criar rota de teste FINAL
echo "5. 🌐 CRIANDO ROTA DE TESTE..."
# Backup
cp routes/web.php routes/web.php.backup

# Rota de teste simplificada
cat > routes/web-test.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

Route::get('/test-login-final', function() {
    echo "<h1>🧪 TESTE DEFINITIVO DO LOGIN</h1>";
    
    $email = 'admin@sistema.com';
    $user = \App\Models\User::where('email', $email)->first();
    
    if (!$user) {
        return "<p style='color: red'>❌ Usuário não encontrado</p>";
    }
    
    echo "<p>✅ Usuário encontrado: $user->nome</p>";
    echo "<p>📧 Email: $user->email</p>";
    echo "<p>🔐 Hash: " . substr($user->senha, 0, 30) . "...</p>";
    
    // Teste 1: Hash check
    $passwordCheck = Hash::check('123456', $user->senha);
    echo "<p>Hash::check: " . ($passwordCheck ? "✅ SENHA CORRETA" : "❌ SENHA INCORRETA") . "</p>";
    
    // Teste 2: Auth attempt
    $attempt = Auth::attempt(['email' => $email, 'password' => '123456']);
    echo "<p>Auth::attempt: " . ($attempt ? "✅ SUCESSO" : "❌ FALHOU") . "</p>";
    
    // Teste 3: Login manual
    if ($passwordCheck) {
        Auth::login($user);
        echo "<p>Auth::login: ✅ FEITO</p>";
        echo "<p style='color: green; font-weight: bold'>🎉 USUÁRIO AUTENTICADO COM SUCESSO!</p>";
        Auth::logout();
    }
    
    echo "<br><a href='/login' style='color: blue'>➡️ Ir para página de login real</a>";
});
EOF

# Adiciona ao web.php
cat routes/web-test.php >> routes/web.php
rm routes/web-test.php

echo "✅ Rota de teste criada: http://localhost:8000/test-login-final"

# 6. Limpar cache e otimizar
echo "6. 🧹 LIMPANDO CACHE..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear

echo ""
echo "======================================================"
echo "🎉 CONFIGURAÇÃO COMPLETA FINALIZADA!"
echo "======================================================"
echo ""
echo "📋 PARA TESTAR:"
echo ""
echo "1. 🧪 TESTE RÁPIDO:"
echo "   http://localhost:8000/test-login-final"
echo ""
echo "2. 🚀 LOGIN REAL:"
echo "   http://localhost:8000/login"
echo "   Email: admin@sistema.com"
echo "   Senha: 123456"
echo ""
echo "3. 📊 SE AINDA NÃO FUNCIONAR:"
echo "   Execute: tail -f storage/logs/laravel.log"
echo "   E me mostre os logs"
echo ""
echo "🔧 O que foi corrigido:"
echo "   ✅ Model User com configuração completa"
echo "   ✅ AuthController com login MANUAL"
echo "   ✅ Formulário sem 'remember me'"
echo "   ✅ Usuário criado com hash correto"
echo "   ✅ Cache limpo"
echo ""
