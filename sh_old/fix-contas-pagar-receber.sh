#!/bin/bash

echo "🔧 CORRIGINDO CONTAS A PAGAR E CONTAS A RECEBER"
echo "================================================"

# 1. Verificar estrutura de ambas as tabelas
echo "1. 🗃️ VERIFICANDO ESTRUTURA DO BANCO..."
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    \$tables = ['contas_pagar', 'contas_receber'];
    
    foreach (\$tables as \$table) {
        echo \"\\n🔍 Verificando tabela \$table...\\n\";
        
        if (Schema::hasTable(\$table)) {
            \$columns = Schema::getColumnListing(\$table);
            echo \"✅ Tabela \$table existe\\n\";
            echo \"📋 Colunas: \" . implode(', ', \$columns) . \"\\n\";
            
            // Verificar dados existentes
            \$count = DB::table(\$table)->count();
            echo \"📊 Total de registros: \$count\\n\";
            
            if (\$count > 0) {
                \$ultimo = DB::table(\$table)->latest(\\\$table == 'contas_pagar' ? 'id_conta' : 'id_conta_receber')->first();
                echo \"📝 Último registro ID: \" . (\\\$table == 'contas_pagar' ? \$ultimo->id_conta : \$ultimo->id_conta_receber) . \"\\n\";
            }
        } else {
            echo \"❌ Tabela \$table não existe\\n\";
        }
    }
    
} catch (Exception \$e) {
    echo \"❌ Erro: \" . \$e->getMessage() . \"\\n\";
}
"

# 2. Criar/Corrigir ambos os Controllers
echo ""
echo "2. 🛠️ CRIANDO/CORRIGINDO CONTROLLERS..."

# ContasPagarController
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
        Log::info('Dados recebidos no store ContasPagar:', $request->all());
        
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

            Log::info('Dados para inserção ContasPagar:', $data);
            
            $id = DB::table('contas_pagar')->insertGetId($data);
            
            Log::info("Conta a pagar inserida com ID: $id");

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
            Log::error('Erro ao exibir conta a pagar: ' . $e->getMessage());
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
            Log::error('Erro ao carregar edição conta a pagar: ' . $e->getMessage());
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
            Log::error('Erro ao atualizar conta a pagar: ' . $e->getMessage());
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
            Log::error('Erro ao excluir conta a pagar: ' . $e->getMessage());
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
            Log::error('Erro ao cancelar conta a pagar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao cancelar conta.');
        }
    }
}
EOF
echo "✅ ContasPagarController criado/atualizado"

# ContasReceberController
cat > app/Http/Controllers/ContasReceberController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContasReceberController extends Controller
{
    public function index()
    {
        try {
            $contas = DB::table('contas_receber')
                       ->leftJoin('clientes', 'contas_receber.id_cliente', '=', 'clientes.id_cliente')
                       ->select('contas_receber.*', 'clientes.nome as cliente_nome')
                       ->orderBy('contas_receber.data_vencimento', 'asc')
                       ->get();
            
            return view('contas-receber.index', compact('contas'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar contas a receber: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar contas a receber.');
        }
    }

    public function create()
    {
        try {
            $clientes = DB::table('clientes')->orderBy('nome')->get();
            return view('contas-receber.create', compact('clientes'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de criação contas a receber: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar formulário.');
        }
    }

    public function store(Request $request)
    {
        Log::info('Dados recebidos no store ContasReceber:', $request->all());
        
        $request->validate([
            'id_cliente' => 'nullable|exists:clientes,id_cliente',
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

            Log::info('Dados para inserção ContasReceber:', $data);
            
            $id = DB::table('contas_receber')->insertGetId($data);
            
            Log::info("Conta a receber inserida com ID: $id");

            return redirect()->route('contas-receber.index')
                             ->with('success', 'Conta a receber cadastrada com sucesso!');
                             
        } catch (\Exception $e) {
            Log::error('Erro ao criar conta a receber: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Erro ao cadastrar conta a receber: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $conta = DB::table('contas_receber')
                      ->leftJoin('clientes', 'contas_receber.id_cliente', '=', 'clientes.id_cliente')
                      ->select('contas_receber.*', 'clientes.nome as cliente_nome', 'clientes.cpf_cnpj', 'clientes.telefone', 'clientes.email')
                      ->where('id_conta_receber', $id)
                      ->first();

            if (!$conta) {
                return redirect()->route('contas-receber.index')->with('error', 'Conta não encontrada.');
            }

            return view('contas-receber.show', compact('conta'));
        } catch (\Exception $e) {
            Log::error('Erro ao exibir conta a receber: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar conta.');
        }
    }

    public function edit($id)
    {
        try {
            $conta = DB::table('contas_receber')->where('id_conta_receber', $id)->first();
            $clientes = DB::table('clientes')->orderBy('nome')->get();

            if (!$conta) {
                return redirect()->route('contas-receber.index')->with('error', 'Conta não encontrada.');
            }

            return view('contas-receber.edit', compact('conta', 'clientes'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar edição conta a receber: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar formulário de edição.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_cliente' => 'nullable|exists:clientes,id_cliente',
            'descricao' => 'required|string|max:255',
            'valor' => 'required',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:pendente,recebido,cancelado'
        ]);

        try {
            $data = $request->all();
            $data['valor'] = str_replace(['R$', '.', ','], ['', '', '.'], $data['valor']);
            $data['valor'] = floatval($data['valor']);

            $affected = DB::table('contas_receber')->where('id_conta_receber', $id)->update($data);

            if ($affected) {
                return redirect()->route('contas-receber.index')
                                 ->with('success', 'Conta a receber atualizada com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Nenhuma alteração foi realizada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar conta a receber: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao atualizar conta a receber.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $conta = DB::table('contas_receber')->where('id_conta_receber', $id)->first();

            if (!$conta) {
                return redirect()->route('contas-receber.index')->with('error', 'Conta não encontrada.');
            }

            DB::table('contas_receber')->where('id_conta_receber', $id)->delete();

            return redirect()->route('contas-receber.index')
                             ->with('success', 'Conta a receber excluída com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir conta a receber: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao excluir conta a receber.');
        }
    }

    public function receber($id)
    {
        try {
            $affected = DB::table('contas_receber')
                         ->where('id_conta_receber', $id)
                         ->update([
                             'status' => 'recebido',
                             'data_recebimento' => now()
                         ]);

            if ($affected) {
                return redirect()->route('contas-receber.index')
                                 ->with('success', 'Conta marcada como recebida!');
            } else {
                return redirect()->back()->with('error', 'Conta não encontrada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao marcar conta como recebida: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao marcar conta como recebida.');
        }
    }

    public function cancelar($id)
    {
        try {
            $affected = DB::table('contas_receber')
                         ->where('id_conta_receber', $id)
                         ->update([
                             'status' => 'cancelado',
                             'data_recebimento' => null
                         ]);

            if ($affected) {
                return redirect()->route('contas-receber.index')
                                 ->with('success', 'Conta cancelada com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Conta não encontrada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar conta a receber: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao cancelar conta.');
        }
    }
}
EOF
echo "✅ ContasReceberController criado/atualizado"

# 3. Criar/Corrigir ambas as Views Index
echo ""
echo "3. 📝 CRIANDO/CORRIGINDO VIEWS..."

# View Index Contas a Pagar
mkdir -p resources/views/contas-pagar
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

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
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
                                                    onclick="return confirm('Marcar esta conta como paga?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('contas-pagar.cancelar', $conta->id_conta) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-gray-900"
                                                    onclick="return confirm('Cancelar esta conta?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('contas-pagar.show', $conta->id_conta) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('contas-pagar.edit', $conta->id_conta) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('contas-pagar.destroy', $conta->id_conta) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Tem certeza que deseja excluir esta conta?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma conta a pagar encontrada.
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
echo "✅ View contas-pagar/index criada/atualizada"

# View Index Contas a Receber
mkdir -p resources/views/contas-receber
cat > resources/views/contas-receber/index.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Contas a Receber')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Contas a Receber</h1>
        <a href="{{ route('contas-receber.create') }}" 
           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-plus mr-2"></i>Nova Conta
        </a>
    </div>

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

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descrição
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $conta->descricao }}</div>
                                @if($conta->observacoes)
                                    <div class="text-sm text-gray-500">{{ Str::limit($conta->observacoes, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $conta->cliente_nome ?? 'N/A' }}</div>
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
                                @if($conta->status == 'recebido')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Recebido
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
                                        <form action="{{ route('contas-receber.receber', $conta->id_conta_receber) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" 
                                                    onclick="return confirm('Marcar esta conta como recebida?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('contas-receber.cancelar', $conta->id_conta_receber) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-gray-900"
                                                    onclick="return confirm('Cancelar esta conta?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('contas-receber.show', $conta->id_conta_receber) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('contas-receber.edit', $conta->id_conta_receber) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('contas-receber.destroy', $conta->id_conta_receber) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Tem certeza que deseja excluir esta conta?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma conta a receber encontrada.
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
echo "✅ View contas-receber/index criada/atualizada"

# 4. Verificar e corrigir rotas
echo ""
echo "4. 🛣️ VERIFICANDO ROTAS..."
if grep -q "contas-pagar" routes/web.php && grep -q "contas-receber" routes/web.php; then
    echo "✅ Rotas já existem"
else
    echo "📝 Adicionando rotas..."
    cat >> routes/web.php << 'EOF'

// Rotas para Contas a Pagar
Route::resource('contas-pagar', ContasPagarController::class);
Route::post('contas-pagar/{id}/pagar', [ContasPagarController::class, 'pagar'])->name('contas-pagar.pagar');
Route::post('contas-pagar/{id}/cancelar', [ContasPagarController::class, 'cancelar'])->name('contas-pagar.cancelar');

// Rotas para Contas a Receber
Route::resource('contas-receber', ContasReceberController::class);
Route::post('contas-receber/{id}/receber', [ContasReceberController::class, 'receber'])->name('contas-receber.receber');
Route::post('contas-receber/{id}/cancelar', [ContasReceberController::class, 'cancelar'])->name('contas-receber.cancelar');
EOF
    echo "✅ Rotas adicionadas"
fi

# 5. Limpar cache
echo ""
echo "5. 🗑️ LIMPANDO CACHE..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🎉 CORREÇÕES APLICADAS PARA AMBOS OS SISTEMAS!"
echo ""
echo "📋 O QUE FOI FEITO:"
echo "   ✅ ContasPagarController criado/atualizado"
echo "   ✅ ContasReceberController criado/atualizado" 
echo "   ✅ Views index de ambos os sistemas"
echo "   ✅ Rotas verificadas/corrigidas"
echo "   ✅ Cache limpo"
echo ""
echo "🚀 AGORA TESTE:"
echo "   📥 Contas a Pagar: http://localhost:8000/contas-pagar"
echo "   📤 Contas a Receber: http://localhost:8000/contas-receber"
echo ""
echo "🔍 SE AINDA HOUVER PROBLEMAS:"
echo "   Verifique: tail -f storage/logs/laravel.log"
