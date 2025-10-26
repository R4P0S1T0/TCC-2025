#!/bin/bash

echo "🔧 CORREÇÃO RÁPIDA - SINTAXE PHP"
echo "================================"

# 1. Corrigir a verificação da tabela compras
echo "1. 🗃️ VERIFICANDO ESTRUTURA DA TABELA COMPRAS (CORRIGIDO)..."

php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    if (Schema::hasTable('compras')) {
        echo \"🔍 Estrutura da tabela compras:\\n\";
        \$columns = DB::select('DESCRIBE compras');
        foreach (\$columns as \$col) {
            echo \"   - {\$col->Field}: {\$col->Type} ({\$col->Null})\\n\";
        }
        
        // Verificar se existe a coluna numero_nota
        if (!Schema::hasColumn('compras', 'numero_nota')) {
            echo \"📦 Adicionando coluna numero_nota...\\n\";
            DB::statement('ALTER TABLE compras ADD COLUMN numero_nota VARCHAR(50) NULL');
            echo \"✅ Coluna numero_nota adicionada\\n\";
        }
        
        // Verificar se existe a coluna observacoes
        if (!Schema::hasColumn('compras', 'observacoes')) {
            echo \"📦 Adicionando coluna observacoes...\\n\";
            DB::statement('ALTER TABLE compras ADD COLUMN observacoes TEXT NULL');
            echo \"✅ Coluna observacoes adicionada\\n\";
        }
    } else {
        echo \"❌ Tabela compras não existe\\n\";
    }
} catch (Exception \$e) {
    echo \"❌ Erro: \" . \$e->getMessage() . \"\\n\";
}
"

# 2. Verificar também as outras tabelas importantes
echo ""
echo "2. 🗃️ VERIFICANDO OUTRAS TABELAS..."

php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

\$tables = ['contas_pagar', 'contas_receber', 'fornecedores', 'clientes'];

foreach (\$tables as \$table) {
    if (Schema::hasTable(\$table)) {
        \$count = DB::table(\$table)->count();
        echo \"   ✅ {\$table}: {\$count} registros\\n\";
    } else {
        echo \"   ❌ {\$table}: AUSENTE\\n\";
    }
}
"

# 3. Testar cadastro rápido
echo ""
echo "3. 🧪 TESTANDO CADASTRO RÁPIDO..."

php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Verificar se existe algum fornecedor para testar
    \$fornecedores = DB::table('fornecedores')->count();
    echo \"📊 Fornecedores cadastrados: {\$fornecedores}\\n\";
    
    if (\$fornecedores > 0) {
        // Testar estrutura dos dados
        \$fornecedor = DB::table('fornecedores')->first();
        echo \"📝 Primeiro fornecedor: {\$fornecedor->nome}\\n\";
        
        // Testar inserção simples
        \$testData = [
            'id_fornecedor' => \$fornecedor->id_fornecedor,
            'descricao' => 'Teste Automático',
            'valor_total' => 100.50,
            'data_compra' => date('Y-m-d'),
            'status' => 'pendente',
            'data_criacao' => now()
        ];
        
        \$id = DB::table('compras')->insertGetId(\$testData);
        echo \"✅ Compra teste inserida com ID: {\$id}\\n\";
        
        // Limpar teste
        DB::table('compras')->where('id_compra', \$id)->delete();
        echo \"🧹 Compra teste removida\\n\";
    } else {
        echo \"⚠️  Nenhum fornecedor cadastrado para testar\\n\";
    }
    
} catch (Exception \$e) {
    echo \"❌ Erro no teste: \" . \$e->getMessage() . \"\\n\";
}
"

# 4. Limpar cache final
echo ""
echo "4. 🗑️ LIMPANDO CACHE FINAL..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🎉 SISTEMA PRONTO!"
echo ""
echo "🚀 AGORA TESTE MANUALMENTE:"
echo "   Acesse: http://localhost:8000/compras/create"
echo "   Preencha o formulário e clique em Cadastrar Compra"
echo ""
echo "📞 SE AINDA DER ERRO:"
echo "   Verifique: tail -f storage/logs/laravel.log"
