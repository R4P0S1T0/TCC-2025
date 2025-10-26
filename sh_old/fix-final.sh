#!/bin/bash

echo "🎯 CORREÇÃO FINAL - LOGIN E REDIRECIONAMENTO"
echo "=============================================="

# 1. Remover credenciais de teste do login
echo "1. 🎨 REMOVENDO CREDENCIAIS DE TESTE..."
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

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-600 hover:to-purple-700 focus:ring-4 focus:ring-blue-200 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Entrar no Sistema
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                © 2024 Sistema de Gestão. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>
EOF
echo "✅ Credenciais removidas - Login normalizado"

# 2. Corrigir AuthController com redirecionamento FORÇADO
echo "2. 🎮 CORRIGINDO AUTHCONTROLLER..."
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
        // Se já estiver logado, redireciona para dashboard
        if (Auth::check()) {
            return redirect('/');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Busca o usuário
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'Credenciais inválidas.'])->withInput();
        }

        // Verifica a senha
        if (!Hash::check($request->password, $user->senha)) {
            return back()->withErrors(['email' => 'Credenciais inválidas.'])->withInput();
        }

        // 🔥 LOGIN MANUAL - método mais confiável
        Auth::login($user);
        $request->session()->regenerate();

        // 🔥 REDIRECIONAMENTO FORÇADO para a página principal
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
echo "✅ AuthController corrigido"

# 3. Garantir que a rota principal existe e funciona
echo "3. 🛣️ CONFIGURANDO ROTAS..."
cat > routes/web.php << 'EOF'
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ContaPagarController;
use App\Http\Controllers\ContaReceberController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\FluxoCaixaController;
use App\Http\Controllers\CalendarioController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    // Página principal (dashboard)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Módulos do sistema
    Route::resource('fornecedores', FornecedorController::class);
    Route::resource('compras', CompraController::class);
    Route::resource('contas-pagar', ContaPagarController::class);
    Route::resource('contas-receber', ContaReceberController::class);
    Route::resource('usuarios', UsuarioController::class);
    
    // Calendário
    Route::get('calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('calendario/eventos', [CalendarioController::class, 'getEventos'])->name('calendario.eventos');
    Route::post('calendario', [CalendarioController::class, 'store'])->name('calendario.store');
    Route::put('calendario/{id}', [CalendarioController::class, 'update'])->name('calendario.update');
    Route::delete('calendario/{id}', [CalendarioController::class, 'destroy'])->name('calendario.destroy');
    
    // Fluxo de Caixa
    Route::get('fluxo-caixa', [FluxoCaixaController::class, 'index'])->name('fluxo-caixa.index');
    Route::get('fluxo-caixa/export/pdf', [FluxoCaixaController::class, 'exportPdf'])->name('fluxo-caixa.export.pdf');
    Route::get('fluxo-caixa/export/excel', [FluxoCaixaController::class, 'exportExcel'])->name('fluxo-caixa.export.excel');
});
EOF
echo "✅ Rotas configuradas"

# 4. Garantir que DashboardController existe
echo "4. 📊 VERIFICANDO DASHBOARD..."
if [ ! -f "app/Http/Controllers/DashboardController.php" ]; then
    cat > app/Http/Controllers/DashboardController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }
}
EOF
    echo "✅ DashboardController criado"
fi

# 5. Garantir que a view do dashboard existe
echo "5. 🎨 CRIANDO DASHBOARD..."
mkdir -p resources/views/dashboard

cat > resources/views/dashboard/index.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">🎉 Login realizado com sucesso!</h1>
                <p class="text-green-100 mt-2">Bem-vindo ao Sistema de Gestão, {{ auth()->user()->nome }}!</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-check-circle text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 mr-4">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Usuário</p>
                    <p class="text-2xl font-bold text-gray-800">{{ auth()->user()->nome }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 mr-4">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="text-2xl font-bold text-gray-800">Ativo</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 mr-4">
                    <i class="fas fa-rocket text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Sistema</p>
                    <p class="text-2xl font-bold text-gray-800">Online</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 mr-4">
                    <i class="fas fa-calendar text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Data</p>
                    <p class="text-2xl font-bold text-gray-800">{{ now()->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Módulos do Sistema</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('fornecedores.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                <i class="fas fa-truck text-blue-600 text-xl mr-3"></i>
                <span class="font-medium text-gray-700">Fornecedores</span>
            </a>
            <a href="{{ route('compras.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition">
                <i class="fas fa-shopping-cart text-green-600 text-xl mr-3"></i>
                <span class="font-medium text-gray-700">Compras</span>
            </a>
            <a href="{{ route('calendario.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition">
                <i class="fas fa-calendar text-purple-600 text-xl mr-3"></i>
                <span class="font-medium text-gray-700">Calendário</span>
            </a>
            <a href="{{ route('usuarios.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition">
                <i class="fas fa-users text-orange-600 text-xl mr-3"></i>
                <span class="font-medium text-gray-700">Usuários</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
</div>
@endsection
EOF
echo "✅ Dashboard criado"

# 6. Limpar cache
echo "6. 🧹 LIMPANDO CACHE..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "=============================================="
echo "🎉 CORREÇÃO FINALIZADA!"
echo "=============================================="
echo ""
echo "📋 PARA TESTAR:"
echo ""
echo "1. 🔑 FAÇA LOGIN:"
echo "   http://localhost:8000/login"
echo "   Email: admin@sistema.com"
echo "   Senha: 123456"
echo ""
echo "2. ✅ SERÁ REDIRECIONADO PARA:"
echo "   http://localhost:8000/"
echo ""
echo "3. 🔧 SE AINDA NÃO FUNCIONAR:"
echo "   Verifique os logs: tail -f storage/logs/laravel.log"
echo ""
