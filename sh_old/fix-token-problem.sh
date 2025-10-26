#!/bin/bash

echo "🔧 CORREÇÃO URGENTE - PROBLEMA DO _token NO BANCO"
echo "=================================================="

# 1. Corrigir o CompraController para remover o _token
echo "1. 🛠️ CORRIGINDO COMPRACONTROLLER..."

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
            'valor_total' => 'required',
            'data_compra' => 'required|date',
            'observacoes' => 'nullable|string'
        ]);

        try {
            // Remover campos que não existem na tabela
            $data = $request->except(['_token', '_method']);
            
            // Converter valor para decimal
            if (isset($data['valor_total'])) {
                $data['valor_total'] = str_replace(['R$', '.', ','], ['', '', '.'], $data['valor_total']);
                $data['valor_total'] = floatval($data['valor_total']);
            }
            
            $data['status'] = 'pendente';
            $data['data_criacao'] = now();

            Log::info('Dados para inserção Compra:', $data);
            
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
            'valor_total' => 'required',
            'data_compra' => 'required|date',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:pendente,finalizada,cancelada'
        ]);

        try {
            // Remover campos que não existem na tabela
            $data = $request->except(['_token', '_method']);
            
            // Converter valor para decimal
            if (isset($data['valor_total'])) {
                $data['valor_total'] = str_replace(['R$', '.', ','], ['', '', '.'], $data['valor_total']);
                $data['valor_total'] = floatval($data['valor_total']);
            }

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
}
EOF
echo "✅ CompraController corrigido - _token removido"

# 2. Corrigir também os outros controllers
echo ""
echo "2. 🛠️ CORRIGINDO OUTROS CONTROLLERS..."

# ContasPagarController - corrigir store e update
sed -i "s/\$data = \$request->all();/\$data = \$request->except(['_token', '_method']);/" app/Http/Controllers/ContasPagarController.php

# ContasReceberController - corrigir store e update  
sed -i "s/\$data = \$request->all();/\$data = \$request->except(['_token', '_method']);/" app/Http/Controllers/ContasReceberController.php

echo "✅ Todos controllers corrigidos para remover _token"

# 3. Verificar estrutura da tabela compras
echo ""
echo "3. 🗃️ VERIFICANDO ESTRUTURA DA TABELA COMPRAS..."

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
        foreach (\$columns as \$column) {
            echo \"   - \${column->Field}: \${column->Type} (\${column->Null})\\n\";
        }
        
        // Verificar se existe a coluna numero_nota
        if (!Schema::hasColumn('compras', 'numero_nota')) {
            echo \"📦 Adicionando coluna numero_nota...\\n\";
            DB::statement('ALTER TABLE compras ADD COLUMN numero_nota VARCHAR(50) NULL');
            echo \"✅ Coluna numero_nota adicionada\\n\";
        }
    }
} catch (Exception \$e) {
    echo \"❌ Erro: \" . \$e->getMessage() . \"\\n\";
}
"

# 4. Criar view de criação de compras
echo ""
echo "4. 📝 CRIANDO VIEW DE CRIAÇÃO DE COMPRAS..."

mkdir -p resources/views/compras

cat > resources/views/compras/create.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Nova Compra')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Nova Compra</h1>
        <a href="{{ route('compras.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('compras.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fornecedor *</label>
                    <select name="id_fornecedor" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione um fornecedor</option>
                        @foreach($fornecedores as $fornecedor)
                            <option value="{{ $fornecedor->id_fornecedor }}" {{ old('id_fornecedor') == $fornecedor->id_fornecedor ? 'selected' : '' }}>
                                {{ $fornecedor->nome }} - {{ $fornecedor->cnpj }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data da Compra *</label>
                    <input type="date" name="data_compra" value="{{ old('data_compra', date('Y-m-d')) }}" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número da Nota</label>
                    <input type="text" name="numero_nota" value="{{ old('numero_nota') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Número da nota fiscal">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                    <input type="text" name="descricao" value="{{ old('descricao') }}" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Descrição da compra">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor Total *</label>
                    <input type="text" name="valor_total" value="{{ old('valor_total') }}" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0,00"
                           oninput="mascaraMoeda(this)">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="observacoes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Observações adicionais...">{{ old('observacoes') }}</textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('compras.index') }}" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-shopping-cart mr-2"></i>Cadastrar Compra
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formatar valor inicial se existir
    const valorInput = document.querySelector('input[name="valor_total"]');
    if (valorInput && valorInput.value) {
        mascaraMoeda(valorInput);
    }
});
</script>
@endsection
EOF
echo "✅ View compras/create criada"

# 5. Limpar cache
echo ""
echo "5. 🗑️ LIMPANDO CACHE..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🎉 PROBLEMA DO _token RESOLVIDO!"
echo ""
echo "📋 CORREÇÕES APLICADAS:"
echo "   ✅ CompraController corrigido para remover _token dos dados"
echo "   ✅ Todos controllers atualizados com except(['_token', '_method'])"
echo "   ✅ Estrutura da tabela compras verificada"
echo "   ✅ Coluna numero_nota adicionada se não existia"
echo "   ✅ View de criação de compras criada"
echo "   ✅ Cache limpo"
echo ""
echo "🚀 AGORA TESTE:"
echo "   Acesse: http://localhost:8000/compras/create"
echo "   Tente cadastrar uma compra - deve funcionar sem erros!"
echo ""
echo "💡 O problema era que o campo _token do CSRF estava sendo"
echo "   incluído nos dados do formulário e tentando ser inserido"
echo "   no banco, mas essa coluna não existe na tabela."
