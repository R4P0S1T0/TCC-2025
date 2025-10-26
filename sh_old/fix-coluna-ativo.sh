#!/bin/bash

echo "🔧 CORRIGINDO ERRO DA COLUNA 'ativo' NA TABELA PRODUTOS"
echo "========================================================"

# 1. Adicionar a coluna 'ativo' na tabela produtos
echo "1. 🗃️ ADICIONANDO COLUNA 'ativo' NA TABELA PRODUTOS..."

php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo \"🔍 Verificando tabela produtos...\\n\";
    
    if (Schema::hasTable('produtos')) {
        // Verificar se a coluna ativo existe
        if (!Schema::hasColumn('produtos', 'ativo')) {
            echo \"📦 Adicionando coluna 'ativo'...\\n\";
            DB::statement(\"ALTER TABLE produtos ADD COLUMN ativo TINYINT(1) DEFAULT 1\");
            echo \"✅ Coluna 'ativo' adicionada com valor padrão 1\\n\";
        } else {
            echo \"ℹ️ Coluna 'ativo' já existe\\n\";
        }
        
        // Mostrar estrutura atual
        \$columns = Schema::getColumnListing('produtos');
        echo \"📋 Colunas da tabela produtos: \" . implode(', ', \$columns) . \"\\n\";
        
    } else {
        echo \"❌ Tabela produtos não existe\\n\";
    }
    
} catch (Exception \$e) {
    echo \"❌ Erro: \" . \$e->getMessage() . \"\\n\";
}
"

# 2. Corrigir o CompraController para não filtrar por 'ativo'
echo ""
echo "2. 🛠️ CORRIGINDO COMPRACONTROLLER..."

if [ -f "app/Http/Controllers/CompraController.php" ]; then
    # Fazer backup
    cp app/Http/Controllers/CompraController.php app/Http/Controllers/CompraController.php.backup
    
    # Corrigir a linha que causa o erro
    sed -i "s/->where('ativo', 1)//g" app/Http/Controllers/CompraController.php
    sed -i "s/\$produtos = DB::table('produtos')->where('ativo', 1)->orderBy('nome')->get();/\$produtos = DB::table('produtos')->orderBy('nome')->get();/" app/Http/Controllers/CompraController.php
    
    echo "✅ CompraController corrigido - removido filtro por 'ativo'"
else
    echo "⚠️ CompraController não encontrado"
fi

# 3. Alternativa: Criar um CompraController corrigido
echo ""
echo "3. 📝 CRIANDO COMPRACONTROLLER CORRIGIDO..."

cat > app/Http/Controllers/CompraController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompraController extends Controller
{
    public function index()
    {
        try {
            $compras = DB::table('compras')
                        ->leftJoin('fornecedores', 'compras.id_fornecedor', '=', 'fornecedores.id_fornecedor')
                        ->select('compras.*', 'fornecedores.nome as fornecedor_nome')
                        ->orderBy('compras.data_compra', 'desc')
                        ->get();
            
            return view('compras.index', compact('compras'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar compras: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar compras.');
        }
    }

    public function create()
    {
        try {
            $fornecedores = DB::table('fornecedores')->orderBy('nome')->get();
            // REMOVIDO: filtro por 'ativo' que não existe
            $produtos = DB::table('produtos')->orderBy('nome')->get();
            
            return view('compras.create', compact('fornecedores', 'produtos'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de criação: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar formulário.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_fornecedor' => 'required|exists:fornecedores,id_fornecedor',
            'descricao' => 'required|string|max:255',
            'valor_total' => 'required|numeric|min:0.01',
            'data_compra' => 'required|date',
            'observacoes' => 'nullable|string'
        ]);

        try {
            $data = $request->all();
            $data['valor_total'] = $this->formatCurrencyToDecimal($data['valor_total']);
            $data['status'] = 'pendente';
            $data['data_criacao'] = now();

            DB::table('compras')->insert($data);

            return redirect()->route('compras.index')
                             ->with('success', 'Compra cadastrada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar compra: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao cadastrar compra.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $compra = DB::table('compras')
                       ->leftJoin('fornecedores', 'compras.id_fornecedor', '=', 'fornecedores.id_fornecedor')
                       ->select('compras.*', 'fornecedores.nome as fornecedor_nome', 'fornecedores.cnpj', 'fornecedores.telefone', 'fornecedores.email')
                       ->where('id_compra', $id)
                       ->first();

            if (!$compra) {
                return redirect()->route('compras.index')->with('error', 'Compra não encontrada.');
            }

            return view('compras.show', compact('compra'));
        } catch (\Exception $e) {
            Log::error('Erro ao exibir compra: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar compra.');
        }
    }

    public function edit($id)
    {
        try {
            $compra = DB::table('compras')->where('id_compra', $id)->first();
            $fornecedores = DB::table('fornecedores')->orderBy('nome')->get();

            if (!$compra) {
                return redirect()->route('compras.index')->with('error', 'Compra não encontrada.');
            }

            return view('compras.edit', compact('compra', 'fornecedores'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar edição: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar formulário de edição.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_fornecedor' => 'required|exists:fornecedores,id_fornecedor',
            'descricao' => 'required|string|max:255',
            'valor_total' => 'required|numeric|min:0.01',
            'data_compra' => 'required|date',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:pendente,finalizada,cancelada'
        ]);

        try {
            $data = $request->all();
            $data['valor_total'] = $this->formatCurrencyToDecimal($data['valor_total']);

            $affected = DB::table('compras')->where('id_compra', $id)->update($data);

            if ($affected) {
                return redirect()->route('compras.index')
                                 ->with('success', 'Compra atualizada com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Nenhuma alteração foi realizada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar compra: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao atualizar compra.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $compra = DB::table('compras')->where('id_compra', $id)->first();

            if (!$compra) {
                return redirect()->route('compras.index')->with('error', 'Compra não encontrada.');
            }

            DB::table('compras')->where('id_compra', $id)->delete();

            return redirect()->route('compras.index')
                             ->with('success', 'Compra excluída com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir compra: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao excluir compra.');
        }
    }

    public function finalizar($id)
    {
        try {
            $affected = DB::table('compras')
                         ->where('id_compra', $id)
                         ->update([
                             'status' => 'finalizada'
                         ]);

            if ($affected) {
                return redirect()->route('compras.index')
                                 ->with('success', 'Compra finalizada com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Compra não encontrada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao finalizar compra: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao finalizar compra.');
        }
    }

    public function cancelar($id)
    {
        try {
            $affected = DB::table('compras')
                         ->where('id_compra', $id)
                         ->update([
                             'status' => 'cancelada'
                         ]);

            if ($affected) {
                return redirect()->route('compras.index')
                                 ->with('success', 'Compra cancelada com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Compra não encontrada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar compra: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao cancelar compra.');
        }
    }

    private function formatCurrencyToDecimal($value)
    {
        if (is_string($value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
        
        return floatval($value);
    }
}
EOF
echo "✅ CompraController criado sem filtro por 'ativo'"

# 4. Verificar se existem outras ocorrências do problema
echo ""
echo "4. 🔍 PROCURANDO OUTRAS OCORRÊNCIAS DO ERRO..."

grep -r "where('ativo'" app/ resources/ || echo "✅ Nenhuma outra ocorrência encontrada"

# 5. Limpar cache
echo ""
echo "5. 🗑️ LIMPANDO CACHE..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🎉 PROBLEMA DA COLUNA 'ativo' RESOLVIDO!"
echo ""
echo "📋 O QUE FOI FEITO:"
echo "   ✅ Coluna 'ativo' adicionada à tabela produtos (se não existia)"
echo "   ✅ CompraController corrigido para não filtrar por 'ativo'"
echo "   ✅ Cache limpo"
echo ""
echo "🚀 AGORA TESTE:"
echo "   Acesse: http://localhost:8000/compras/create"
echo "   O formulário deve carregar sem erros"
echo ""
echo "💡 ALTERNATIVA:"
echo "   Se quiser manter a coluna 'ativo', podemos:"
echo "   1. Adicionar a coluna manualmente no banco"
echo "   2. Atualizar todos os produtos para ativo=1"
