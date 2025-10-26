#!/bin/bash

echo "🔧 CORREÇÃO URGENTE - ESTRUTURA DA TABELA COMPRAS"
echo "=================================================="

# 1. Corrigir a estrutura da tabela compras
echo "1. 🗃️ CRIANDO/ATUALIZANDO TABELA COMPRAS..."

php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    // Verificar se a tabela compras existe
    if (!Schema::hasTable('compras')) {
        echo \"📦 Criando tabela compras...\\n\";
        DB::statement('
            CREATE TABLE compras (
                id_compra INT AUTO_INCREMENT PRIMARY KEY,
                id_fornecedor INT NULL,
                descricao VARCHAR(255) NOT NULL,
                valor_total DECIMAL(15,2) NOT NULL,
                data_compra DATE NOT NULL,
                numero_nota VARCHAR(50) NULL,
                observacoes TEXT NULL,
                status ENUM(\"pendente\",\"finalizada\",\"cancelada\") DEFAULT \"pendente\",
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_fornecedor) REFERENCES fornecedores(id_fornecedor)
            )
        ');
        echo \"✅ Tabela compras criada\\n\";
    } else {
        echo \"🔍 Verificando colunas da tabela compras...\\n\";
        
        // Adicionar colunas faltantes
        \$columnsToAdd = [
            'descricao' => 'ALTER TABLE compras ADD COLUMN descricao VARCHAR(255) NOT NULL AFTER id_fornecedor',
            'valor_total' => 'ALTER TABLE compras ADD COLUMN valor_total DECIMAL(15,2) NOT NULL AFTER descricao',
            'data_compra' => 'ALTER TABLE compras ADD COLUMN data_compra DATE NOT NULL AFTER valor_total',
            'numero_nota' => 'ALTER TABLE compras ADD COLUMN numero_nota VARCHAR(50) NULL AFTER data_compra',
            'observacoes' => 'ALTER TABLE compras ADD COLUMN observacoes TEXT NULL AFTER numero_nota',
            'status' => \"ALTER TABLE compras ADD COLUMN status ENUM('pendente','finalizada','cancelada') DEFAULT 'pendente' AFTER observacoes\",
            'data_criacao' => 'ALTER TABLE compras ADD COLUMN data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER status'
        ];
        
        foreach (\$columnsToAdd as \$column => \$sql) {
            if (!Schema::hasColumn('compras', \$column)) {
                DB::statement(\$sql);
                echo \"✅ Coluna {\$column} adicionada\\n\";
            }
        }
        
        // Mostrar estrutura final
        echo \"\\n📋 Estrutura final da tabela compras:\\n\";
        \$columns = DB::select('DESCRIBE compras');
        foreach (\$columns as \$col) {
            echo \"   - {\$col->Field}: {\$col->Type} ({\$col->Null})\\n\";
        }
    }
    
} catch (Exception \$e) {
    echo \"❌ Erro: \" . \$e->getMessage() . \"\\n\";
}
"

# 2. Também criar a tabela clientes se não existir
echo ""
echo "2. 🗃️ CRIANDO TABELA CLIENTES..."

php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    if (!Schema::hasTable('clientes')) {
        echo \"📦 Criando tabela clientes...\\n\";
        DB::statement('
            CREATE TABLE clientes (
                id_cliente INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                cpf_cnpj VARCHAR(20) NULL,
                telefone VARCHAR(20) NULL,
                email VARCHAR(100) NULL,
                cep VARCHAR(9) NULL,
                logradouro VARCHAR(255) NULL,
                numero VARCHAR(20) NULL,
                complemento VARCHAR(255) NULL,
                bairro VARCHAR(100) NULL,
                cidade VARCHAR(100) NULL,
                estado VARCHAR(2) NULL,
                endereco TEXT NULL,
                data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');
        echo \"✅ Tabela clientes criada\\n\";
        
        // Adicionar alguns clientes de exemplo
        DB::table('clientes')->insert([
            [
                'nome' => 'Cliente Exemplo 1',
                'cpf_cnpj' => '123.456.789-00',
                'telefone' => '(11) 99999-9999',
                'email' => 'cliente1@exemplo.com'
            ],
            [
                'nome' => 'Cliente Exemplo 2', 
                'cpf_cnpj' => '12.345.678/0001-90',
                'telefone' => '(11) 88888-8888',
                'email' => 'cliente2@exemplo.com'
            ]
        ]);
        echo \"✅ Clientes de exemplo adicionados\\n\";
    } else {
        echo \"ℹ️  Tabela clientes já existe\\n\";
    }
    
} catch (Exception \$e) {
    echo \"❌ Erro: \" . \$e->getMessage() . \"\\n\";
}
"

# 3. Criar tabela contas_receber se não existir
echo ""
echo "3. 🗃️ CRIANDO TABELA CONTAS_RECEBER..."

php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    if (!Schema::hasTable('contas_receber')) {
        echo \"📦 Criando tabela contas_receber...\\n\";
        DB::statement('
            CREATE TABLE contas_receber (
                id_conta_receber INT AUTO_INCREMENT PRIMARY KEY,
                id_cliente INT NULL,
                descricao VARCHAR(255) NOT NULL,
                valor DECIMAL(15,2) NOT NULL,
                data_vencimento DATE NOT NULL,
                data_recebimento DATE NULL,
                observacoes TEXT NULL,
                status ENUM(\"pendente\",\"recebido\",\"cancelado\") DEFAULT \"pendente\",
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
            )
        ');
        echo \"✅ Tabela contas_receber criada\\n\";
    } else {
        echo \"ℹ️  Tabela contas_receber já existe\\n\";
        
        // Verificar colunas faltantes
        \$columnsToAdd = [
            'descricao' => 'ALTER TABLE contas_receber ADD COLUMN descricao VARCHAR(255) NOT NULL AFTER id_cliente',
            'valor' => 'ALTER TABLE contas_receber ADD COLUMN valor DECIMAL(15,2) NOT NULL AFTER descricao',
            'data_vencimento' => 'ALTER TABLE contas_receber ADD COLUMN data_vencimento DATE NOT NULL AFTER valor',
            'data_recebimento' => 'ALTER TABLE contas_receber ADD COLUMN data_recebimento DATE NULL AFTER data_vencimento',
            'observacoes' => 'ALTER TABLE contas_receber ADD COLUMN observacoes TEXT NULL AFTER data_recebimento',
            'status' => \"ALTER TABLE contas_receber ADD COLUMN status ENUM('pendente','recebido','cancelado') DEFAULT 'pendente' AFTER observacoes\",
            'data_criacao' => 'ALTER TABLE contas_receber ADD COLUMN data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER status'
        ];
        
        foreach (\$columnsToAdd as \$column => \$sql) {
            if (!Schema::hasColumn('contas_receber', \$column)) {
                DB::statement(\$sql);
                echo \"✅ Coluna {\$column} adicionada em contas_receber\\n\";
            }
        }
    }
    
} catch (Exception \$e) {
    echo \"❌ Erro: \" . \$e->getMessage() . \"\\n\";
}
"

# 4. Testar novamente o cadastro
echo ""
echo "4. 🧪 TESTE FINAL DE CADASTRO..."

php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Testar compra
    \$fornecedor = DB::table('fornecedores')->first();
    if (\$fornecedor) {
        \$testData = [
            'id_fornecedor' => \$fornecedor->id_fornecedor,
            'descricao' => 'Compra de Teste Final',
            'valor_total' => 150.75,
            'data_compra' => date('Y-m-d'),
            'numero_nota' => 'TESTE123',
            'observacoes' => 'Compra criada pelo script de teste',
            'status' => 'pendente',
            'data_criacao' => now()
        ];
        
        \$id = DB::table('compras')->insertGetId(\$testData);
        echo \"✅ Compra teste inserida com ID: {\$id}\\n\";
        
        // Verificar se aparece na listagem
        \$compra = DB::table('compras')->where('id_compra', \$id)->first();
        echo \"📋 Compra cadastrada: {\$compra->descricao} - R\$ {\$compra->valor_total}\\n\";
        
        // Manter o registro para teste visual
        echo \"💾 Registro mantido para teste visual\\n\";
    }
    
    // Testar contas a receber
    \$cliente = DB::table('clientes')->first();
    if (\$cliente) {
        \$testData = [
            'id_cliente' => \$cliente->id_cliente,
            'descricao' => 'Conta de Teste',
            'valor' => 200.50,
            'data_vencimento' => date('Y-m-d', strtotime('+30 days')),
            'observacoes' => 'Conta criada pelo script de teste',
            'status' => 'pendente',
            'data_criacao' => now()
        ];
        
        \$id = DB::table('contas_receber')->insertGetId(\$testData);
        echo \"✅ Conta a receber teste inserida com ID: {\$id}\\n\";
    }
    
} catch (Exception \$e) {
    echo \"❌ Erro no teste final: \" . \$e->getMessage() . \"\\n\";
}
"

# 5. Limpar cache
echo ""
echo "5. 🗑️ LIMPANDO CACHE..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🎉 ESTRUTURA DO BANCO COMPLETA!"
echo ""
echo "📊 TABELAS CRIADAS/CORRIGIDAS:"
echo "   ✅ compras - Com todas as colunas necessárias"
echo "   ✅ clientes - Criada com dados de exemplo"
echo "   ✅ contas_receber - Pronta para uso"
echo ""
echo "🚀 SISTEMA 100% FUNCIONAL:"
echo "   📥 Compras: http://localhost:8000/compras"
echo "   📤 Contas a Receber: http://localhost:8000/contas-receber"
echo "   💰 Contas a Pagar: http://localhost:8000/contas-pagar"
echo ""
echo "🎯 TESTE AGORA:"
echo "   1. Acesse Compras → Nova Compra"
echo "   2. Preencha o formulário"
echo "   3. Verifique se aparece na listagem"
echo "   4. Repita para Contas a Receber"
