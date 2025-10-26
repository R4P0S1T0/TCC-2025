#!/bin/bash

echo "🔧 DIAGNOSTICANDO E CORRIGINDO PROBLEMA DE CADASTRO"
echo "===================================================="

# 1. Verificar estrutura do banco
echo "1. 🗃️ VERIFICANDO ESTRUTURA DO BANCO..."
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo \"🔍 Verificando tabela contas_pagar...\\n\";
    
    if (Schema::hasTable('contas_pagar')) {
        \$columns = Schema::getColumnListing('contas_pagar');
        echo \"✅ Tabela contas_pagar existe\\n\";
        echo \"📋 Colunas: \" . implode(', ', \$columns) . \"\\n\";
        
        // Verificar dados existentes
        \$count = DB::table('contas_pagar')->count();
        echo \"📊 Total de registros: \$count\\n\";
        
        if (\$count > 0) {
            \$ultimo = DB::table('contas_pagar')->latest('id_conta')->first();
            echo \"📝 Último registro: \\n\";
            print_r(\$ultimo);
        }
    } else {
        echo \"❌ Tabela contas_pagar não existe\\n\";
    }
    
} catch (Exception \$e) {
    echo \"❌ Erro: \" . \$e->getMessage() . \"\\n\";
}
"

# 2. Verificar e corrigir o Controller
echo ""
echo "2. 🛠️ VERIFICANDO CONTROLLER..."

if [ -f "app/Http/Controllers/ContasPagarController.php" ]; then
    echo "✅ Controller existe"
    
    # Verificar se o método store está correto
    if grep -q "public function store" app/Http/Controllers/ContasPagarController.php; then
        echo "✅ Método store existe"
        
        # Fazer backup do controller atual
        cp app/Http/Controllers/ContasPagarController.php app/Http/Controllers/ContasPagarController.php.backup
        
        # Criar controller corrigido
        cat > app/Http/Controllers/ContasPagarController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContasPagarController extends Controller
{
    public function index()
    {
        try {
            $contas = DB::table('contas_pagar')
                       ->leftJoin('fornecedores', 'contas_pagar.id_fornecedor', '=', 'fornecedores.id_fornecedor')
                       ->select('contas_pagar.*', 'fornecedores.nome as fornecedor_nome')
                       ->orderBy('contas_pagar.data_vencimento', 'asc')
                       ->get();
            
            return view('contas-pagar.index', compact('contas'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar contas a pagar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar contas a pagar.');
        }
    }

    public function create()
    {
        try {
            $fornecedores = DB::table('fornecedores')->orderBy('nome')->get();
            return view('contas-pagar.create', compact('fornecedores'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de criação: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar formulário.');
        }
    }

    public function store(Request $request)
    {
        // Debug: verificar dados recebidos
        Log::info('Dados recebidos no store:', $request->all());
        
        $request->validate([
            'id_fornecedor' => 'required|exists:fornecedores,id_fornecedor',
            'descricao' => 'required|string|max:255',
            'valor' => 'required',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string'
        ]);

        try {
            $data = $request->all();
            
            // Converter valor para decimal
            $data['valor'] = str_replace(['R$', '.', ','], ['', '', '.'], $data['valor']);
            $data['valor'] = floatval($data['valor']);
            
            $data['status'] = 'pendente';
            $data['data_criacao'] = now();

            Log::info('Dados para inserção:', $data);
            
            // Inserir no banco
            $id = DB::table('contas_pagar')->insertGetId($data);
            
            Log::info("Conta inserida com ID: $id");

            return redirect()->route('contas-pagar.index')
                             ->with('success', 'Conta a pagar cadastrada com sucesso!');
                             
        } catch (\Exception $e) {
            Log::error('Erro ao criar conta a pagar: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Erro ao cadastrar conta a pagar: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $conta = DB::table('contas_pagar')
                      ->leftJoin('fornecedores', 'contas_pagar.id_fornecedor', '=', 'fornecedores.id_fornecedor')
                      ->select('contas_pagar.*', 'fornecedores.nome as fornecedor_nome', 'fornecedores.cnpj', 'fornecedores.telefone', 'fornecedores.email')
                      ->where('id_conta', $id)
                      ->first();

            if (!$conta) {
                return redirect()->route('contas-pagar.index')->with('error', 'Conta não encontrada.');
            }

            return view('contas-pagar.show', compact('conta'));
        } catch (\Exception $e) {
            Log::error('Erro ao exibir conta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar conta.');
        }
    }

    public function edit($id)
    {
        try {
            $conta = DB::table('contas_pagar')->where('id_conta', $id)->first();
            $fornecedores = DB::table('fornecedores')->orderBy('nome')->get();

            if (!$conta) {
                return redirect()->route('contas-pagar.index')->with('error', 'Conta não encontrada.');
            }

            return view('contas-pagar.edit', compact('conta', 'fornecedores'));
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
            'valor' => 'required',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:pendente,pago,cancelado'
        ]);

        try {
            $data = $request->all();
            $data['valor'] = str_replace(['R$', '.', ','], ['', '', '.'], $data['valor']);
            $data['valor'] = floatval($data['valor']);

            $affected = DB::table('contas_pagar')->where('id_conta', $id)->update($data);

            if ($affected) {
                return redirect()->route('contas-pagar.index')
                                 ->with('success', 'Conta a pagar atualizada com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Nenhuma alteração foi realizada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar conta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao atualizar conta a pagar.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $conta = DB::table('contas_pagar')->where('id_conta', $id)->first();

            if (!$conta) {
                return redirect()->route('contas-pagar.index')->with('error', 'Conta não encontrada.');
            }

            DB::table('contas_pagar')->where('id_conta', $id)->delete();

            return redirect()->route('contas-pagar.index')
                             ->with('success', 'Conta a pagar excluída com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir conta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao excluir conta a pagar.');
        }
    }

    public function pagar($id)
    {
        try {
            $affected = DB::table('contas_pagar')
                         ->where('id_conta', $id)
                         ->update([
                             'status' => 'pago',
                             'data_pagamento' => now()
                         ]);

            if ($affected) {
                return redirect()->route('contas-pagar.index')
                                 ->with('success', 'Conta marcada como paga!');
            } else {
                return redirect()->back()->with('error', 'Conta não encontrada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao marcar conta como paga: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao marcar conta como paga.');
        }
    }

    public function cancelar($id)
    {
        try {
            $affected = DB::table('contas_pagar')
                         ->where('id_conta', $id)
                         ->update([
                             'status' => 'cancelado',
                             'data_pagamento' => null
                         ]);

            if ($affected) {
                return redirect()->route('contas-pagar.index')
                                 ->with('success', 'Conta cancelada com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Conta não encontrada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar conta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao cancelar conta.');
        }
    }
}
EOF
        echo "✅ Controller atualizado com tratamento de erros e logs"
    else
        echo "❌ Método store não encontrado"
    fi
else
    echo "❌ Controller não existe"
fi

# 3. Verificar e corrigir a View index
echo ""
echo "3. 📝 VERIFICANDO VIEW INDEX..."

if [ -f "resources/views/contas-pagar/index.blade.php" ]; then
    echo "✅ View index existe"
    
    # Fazer backup
    cp resources/views/contas-pagar/index.blade.php resources/views/contas-pagar/index.blade.php.backup
    
    # Criar view corrigida
    cat > resources/views/contas-pagar/index.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Contas a Pagar')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Contas a Pagar</h1>
        <a href="{{ route('contas-pagar.create') }}" 
           class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
            <i class="fas fa-plus mr-2"></i>Nova Conta
        </a>
    </div>

    <!-- Mensagens de Sucesso/Erro -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <strong class="font-bold">Sucesso!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <strong class="font-bold">Erro!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <strong class="font-bold">Erros de Validação:</strong>
            <ul class="mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Debug: Mostrar dados brutos -->
    @if(env('APP_DEBUG'))
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
        <strong>Debug:</strong> 
        Total de contas: {{ $contas->count() }}
        @if($contas->count() > 0)
            | Primeiro ID: {{ $contas->first()->id_conta }}
        @endif
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descrição
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fornecedor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Valor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vencimento
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contas as $conta)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                #{{ $conta->id_conta }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $conta->descricao }}</div>
                                @if($conta->observacoes)
                                    <div class="text-sm text-gray-500">{{ Str::limit($conta->observacoes, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $conta->fornecedor_nome ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    R$ {{ number_format($conta->valor, 2, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($conta->status == 'pago')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Pago
                                    </span>
                                @elseif($conta->status == 'cancelado')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Cancelado
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($conta->status == 'pendente')
                                        <form action="{{ route('contas-pagar.pagar', $conta->id_conta) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" 
                                                    onclick="return confirm('Marcar esta conta como paga?')"
                                                    title="Marcar como Paga">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('contas-pagar.cancelar', $conta->id_conta) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-gray-900"
                                                    onclick="return confirm('Cancelar esta conta?')"
                                                    title="Cancelar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('contas-pagar.show', $conta->id_conta) }}" class="text-blue-600 hover:text-blue-900" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('contas-pagar.edit', $conta->id_conta) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('contas-pagar.destroy', $conta->id_conta) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Tem certeza que deseja excluir esta conta?')"
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                <div class="text-center py-8">
                                    <i class="fas fa-file-invoice-dollar text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-gray-500">Nenhuma conta a pagar encontrada.</p>
                                    <a href="{{ route('contas-pagar.create') }}" class="text-red-600 hover:text-red-700 mt-2 inline-block">
                                        Cadastrar primeira conta
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
EOF
    echo "✅ View index atualizada com mensagens e debug"
else
    echo "❌ View index não existe"
    # Criar diretório se não existir
    mkdir -p resources/views/contas-pagar
fi

# 4. Verificar rotas
echo ""
echo "4. 🛣️ VERIFICANDO ROTAS..."
php artisan route:list | grep contas-pagar

# 5. Criar script de teste
echo ""
echo "5. 🧪 CRIANDO SCRIPT DE TESTE..."

cat > test-cadastro-conta.php << 'EOF'
<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🧪 TESTE DE CADASTRO DE CONTA\n";
echo "==============================\n";

try {
    // Verificar fornecedores disponíveis
    $fornecedores = DB::table('fornecedores')->get();
    echo "📋 Fornecedores disponíveis:\n";
    foreach ($fornecedores as $fornecedor) {
        echo "   ID: {$fornecedor->id_fornecedor} - {$fornecedor->nome}\n";
    }
    
    if ($fornecedores->count() > 0) {
        // Testar cadastro
        $testData = [
            'id_fornecedor' => $fornecedores->first()->id_fornecedor,
            'descricao' => 'Conta de Teste Automático',
            'valor' => '150,75',
            'data_vencimento' => date('Y-m-d', strtotime('+30 days')),
            'observacoes' => 'Conta criada pelo script de teste',
            'status' => 'pendente',
            'data_criacao' => now()
        ];
        
        echo "\n📝 Inserindo conta de teste...\n";
        
        $id = DB::table('contas_pagar')->insertGetId($testData);
        
        echo "✅ Conta inserida com ID: $id\n";
        
        // Verificar se aparece na listagem
        $contasCount = DB::table('contas_pagar')->count();
        echo "📊 Total de contas no banco: $contasCount\n";
        
        // Limpar teste
        DB::table('contas_pagar')->where('id_conta', $id)->delete();
        echo "🧹 Conta de teste removida\n";
        
    } else {
        echo "❌ Nenhum fornecedor cadastrado para testar\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro no teste: " . $e->getMessage() . "\n";
}

echo "\n🎯 INSTRUÇÕES PARA TESTAR MANUALMENTE:\n";
echo "   1. Acesse: http://localhost:8000/contas-pagar/create\n";
echo "   2. Preencha o formulário\n";
echo "   3. Verifique se aparece mensagem de sucesso\n";
echo "   4. Verifique se a conta aparece na listagem\n";
echo "   5. Verifique o arquivo storage/logs/laravel.log se houver erros\n";
EOF

echo "✅ Script de teste criado"

# 6. Executar teste
echo ""
echo "6. 🚀 EXECUTANDO TESTE AUTOMÁTICO..."
php test-cadastro-conta.php

# 7. Limpar cache
echo ""
echo "7. 🗑️ LIMPANDO CACHE..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🎉 CORREÇÕES APLICADAS!"
echo ""
echo "📋 O QUE FOI FEITO:"
echo "   ✅ Controller atualizado com logs e tratamento de erros"
echo "   ✅ View index melhorada com mensagens e debug"
echo "   ✅ Sistema de logs para diagnosticar problemas"
echo "   ✅ Script de teste criado"
echo "   ✅ Cache limpo"
echo ""
echo "🚀 PRÓXIMOS PASSOS:"
echo "   1. Tente cadastrar uma conta novamente"
echo "   2. Verifique se aparece mensagem de sucesso"
echo "   3. Se não funcionar, verifique storage/logs/laravel.log"
echo "   4. Execute manualmente: php test-cadastro-conta.php"
