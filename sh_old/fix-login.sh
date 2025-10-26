#!/bin/bash

echo "🔧 INICIANDO DIAGNÓSTICO E CORREÇÃO DO LOGIN (V2)..."
echo "=============================================="

# Função para corrigir model User
corrigir_model_user() {
    echo "🛠️ CORRIGINDO MODEL USER..."
    
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
    
    protected $fillable = [
        'nome',
        'email', 
        'senha',
        'telefone',
        'endereco', 
        'tipo'
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function getCreatedAtColumn() {
        return null;
    }

    public function getUpdatedAtColumn() {
        return null;
    }

    public function getRememberTokenName()
    {
        return null;
    }
}
EOF
    echo "✅ Model User corrigido"
}

# Função para criar usuário de teste
criar_usuario_teste() {
    echo "👤 CRIANDO USUÁRIO DE TESTE..."
    
    php artisan tinker --execute="
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\DB;
    
    DB::table('usuarios')->where('email', 'admin@teste.com')->delete();
    
    \$userId = DB::table('usuarios')->insertGetId([
        'nome' => 'Admin Teste',
        'email' => 'admin@teste.com',
        'senha' => Hash::make('123456'),
        'tipo' => 'admin',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo \$userId ? '✅ Usuário criado com ID: ' . \$userId : '❌ Erro ao criar usuário';
    echo '\n';
    "
}

# Função para testar autenticação
testar_autenticacao() {
    echo "🔐 TESTANDO AUTENTICAÇÃO..."
    
    php artisan tinker --execute="
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    
    echo '1. Testando Auth::attempt...\n';
    \$attempt = Auth::attempt(['email' => 'admin@teste.com', 'password' => '123456']);
    echo 'Resultado: ' . (\$attempt ? '✅ SUCESSO' : '❌ FALHOU') . '\n\n';
    
    echo '2. Buscando usuário...\n';
    \$user = \App\Models\User::where('email', 'admin@teste.com')->first();
    if (\$user) {
        echo '✅ Usuário encontrado: ' . \$user->nome . '\n';
        echo '3. Verificando senha...\n';
        \$passwordCheck = Hash::check('123456', \$user->senha);
        echo 'Senha confere: ' . (\$passwordCheck ? '✅ SIM' : '❌ NÃO') . '\n';
    }
    "
}

# Função para criar rota de teste CORRETA
criar_rota_teste_correta() {
    echo "🌐 CRIANDO ROTA DE TESTE CORRETA..."
    
    # Backup das rotas atuais
    cp routes/web.php routes/web.php.backup
    
    # Adiciona rota de teste CORRETA
    cat >> routes/web.php << 'EOF'

// ==================== ROTA DE TESTE DO LOGIN ====================
Route::get('/test-login-debug', function() {
    $email = 'admin@teste.com';
    $password = '123456';
    
    echo "<h1>🔧 DEBUG DO LOGIN</h1>";
    
    // Teste 1: Usuário existe?
    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) {
        return "<p style='color: red'>❌ Usuário não encontrado</p>";
    }
    
    echo "<p>✅ Usuário encontrado: $user->nome</p>";
    echo "<p>📧 Email: $user->email</p>";
    echo "<p>🔐 Hash: " . substr($user->senha, 0, 30) . "...</p>";
    
    // Teste 2: Senha confere?
    $passwordCheck = \Illuminate\Support\Facades\Hash::check($password, $user->senha);
    echo "<p>Senha confere: " . ($passwordCheck ? "✅ SIM" : "❌ NÃO") . "</p>";
    
    // Teste 3: Auth attempt
    $attempt = \Illuminate\Support\Facades\Auth::attempt(['email' => $email, 'password' => $password]);
    echo "<p>Auth::attempt: " . ($attempt ? "✅ SUCESSO" : "❌ FALHOU") . "</p>";
    
    if ($attempt) {
        echo "<p style='color: green'>🎉 LOGIN FUNCIONANDO PERFEITAMENTE!</p>";
        \Illuminate\Support\Facades\Auth::logout();
    } else {
        echo "<p style='color: red'>💥 PROBLEMA NO LOGIN</p>";
        echo "<p>getAuthPassword(): " . substr($user->getAuthPassword(), 0, 30) . "...</p>";
    }
    
    echo "<br><a href='/login'>➡️ Ir para página de login</a>";
});
EOF

    echo "✅ Rota de teste criada: http://localhost:8000/test-login-debug"
}

# Função principal
main() {
    echo "=============================================="
    echo "🔧 DIAGNÓSTICO V2 - CORRIGINDO ERROS"
    echo "=============================================="
    
    # 1. Corrigir Model User
    corrigir_model_user
    
    # 2. Criar usuário de teste
    criar_usuario_teste
    
    # 3. Testar autenticação
    testar_autenticacao
    
    # 4. Criar rota de teste CORRETA
    criar_rota_teste_correta
    
    echo ""
    echo "=============================================="
    echo "🎉 CORREÇÃO COMPLETA!"
    echo "=============================================="
    echo ""
    echo "📋 PRÓXIMOS PASSOS:"
    echo ""
    echo "1. 🔍 ACESSE A ROTA DE DEBUG:"
    echo "   http://localhost:8000/test-login-debug"
    echo ""
    echo "2. 🧪 TESTE O LOGIN:"
    echo "   http://localhost:8000/login"
    echo "   Email: admin@teste.com"
    echo "   Senha: 123456"
    echo ""
    echo "3. 📊 SE AINDA NÃO FUNCIONAR:"
    echo "   Me mostre o resultado da rota de debug"
    echo ""
}

# Executar correção
main
