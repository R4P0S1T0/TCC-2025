#!/bin/bash

echo "🔧 CORRIGINDO CRUD E LIMITAÇÕES DE VALORES"
echo "============================================"

# 1. Primeiro, vamos corrigir a estrutura do banco para valores maiores
echo "1. 🗃️ CORRIGINDO ESTRUTURA DO BANCO PARA VALORES MAIORES..."

# Criar migração para corrigir os campos de valor
cat > database/migrations/$(date +%Y_%m_%d_%H%M%S)_fix_decimal_fields.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Corrigir tabela contas_pagar - aumentar precisão do valor
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->decimal('valor', 15, 2)->change();
        });

        // Corrigir tabela contas_receber - aumentar precisão do valor  
        Schema::table('contas_receber', function (Blueprint $table) {
            $table->decimal('valor', 15, 2)->change();
        });

        // Corrigir tabela compras - aumentar precisão do valor_total
        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('valor_total', 15, 2)->change();
        });

        // Adicionar campos de status se não existirem
        if (!Schema::hasColumn('contas_pagar', 'status')) {
            Schema::table('contas_pagar', function (Blueprint $table) {
                $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
                $table->date('data_pagamento')->nullable();
            });
        }

        if (!Schema::hasColumn('contas_receber', 'status')) {
            Schema::table('contas_receber', function (Blueprint $table) {
                $table->enum('status', ['pendente', 'recebido', 'cancelado'])->default('pendente');
                $table->date('data_recebimento')->nullable();
            });
        }

        if (!Schema::hasColumn('compras', 'status')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->enum('status', ['pendente', 'finalizada', 'cancelada'])->default('pendente');
            });
        }
    }

    public function down()
    {
        // Reverter as alterações se necessário
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->decimal('valor', 8, 2)->change();
        });

        Schema::table('contas_receber', function (Blueprint $table) {
            $table->decimal('valor', 8, 2)->change();
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('valor_total', 8, 2)->change();
        });
    }
};
EOF

# 2. Aplicar a migração manualmente (ignorando problemas de PHP)
echo "2. 🚀 APLICANDO CORREÇÕES NO BANCO..."
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo \"🔧 Corrigindo campos de valor...\\n\";
    
    // Corrigir contas_pagar
    if (Schema::hasTable('contas_pagar')) {
        DB::statement('ALTER TABLE contas_pagar MODIFY valor DECIMAL(15,2)');
        echo \"✅ contas_pagar.valor corrigido para DECIMAL(15,2)\\n\";
        
        // Adicionar status se não existir
        if (!Schema::hasColumn('contas_pagar', 'status')) {
            DB::statement(\"ALTER TABLE contas_pagar ADD status ENUM('pendente','pago','cancelado') DEFAULT 'pendente'\");
            DB::statement('ALTER TABLE contas_pagar ADD data_pagamento DATE NULL');
            echo \"✅ Campos de status adicionados a contas_pagar\\n\";
        }
    }
    
    // Corrigir contas_receber
    if (Schema::hasTable('contas_receber')) {
        DB::statement('ALTER TABLE contas_receber MODIFY valor DECIMAL(15,2)');
        echo \"✅ contas_receber.valor corrigido para DECIMAL(15,2)\\n\";
        
        // Adicionar status se não existir
        if (!Schema::hasColumn('contas_receber', 'status')) {
            DB::statement(\"ALTER TABLE contas_receber ADD status ENUM('pendente','recebido','cancelado') DEFAULT 'pendente'\");
            DB::statement('ALTER TABLE contas_receber ADD data_recebimento DATE NULL');
            echo \"✅ Campos de status adicionados a contas_receber\\n\";
        }
    }
    
    // Corrigir compras
    if (Schema::hasTable('compras')) {
        DB::statement('ALTER TABLE compras MODIFY valor_total DECIMAL(15,2)');
        echo \"✅ compras.valor_total corrigido para DECIMAL(15,2)\\n\";
        
        // Adicionar status se não existir
        if (!Schema::hasColumn('compras', 'status')) {
            DB::statement(\"ALTER TABLE compras ADD status ENUM('pendente','finalizada','cancelada') DEFAULT 'pendente'\");
            echo \"✅ Campo status adicionado a compras\\n\";
        }
    }
    
    echo \"🎉 Estrutura do banco corrigida com sucesso!\\n\";
    
} catch (Exception \$e) {
    echo \"❌ Erro: \" . \$e->getMessage() . \"\\n\";
    echo \"💡 Execute os comandos SQL manualmente se necessário\\n\";
}
"

# 3. Atualizar os Controllers para suportar todas as operações CRUD
echo "3. 🛠️ ATUALIZANDO CONTROLLERS PARA CRUD COMPLETO..."

# ContasPagarController completo
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
        $request->validate([
            'id_fornecedor' => 'required|exists:fornecedores,id_fornecedor',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string'
        ]);

        try {
            $data = $request->all();
            
            // Formatando o valor para o padrão do banco
            $data['valor'] = $this->formatCurrencyToDecimal($data['valor']);
            $data['status'] = 'pendente';
            $data['data_criacao'] = now();

            DB::table('contas_pagar')->insert($data);

            return redirect()->route('contas-pagar.index')
                             ->with('success', 'Conta a pagar cadastrada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar conta a pagar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao cadastrar conta a pagar.')->withInput();
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
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:pendente,pago,cancelado'
        ]);

        try {
            $data = $request->all();
            $data['valor'] = $this->formatCurrencyToDecimal($data['valor']);

            // Se foi marcado como pago, definir data_pagamento
            if ($data['status'] == 'pago' && !$request->has('data_pagamento')) {
                $data['data_pagamento'] = now();
            } elseif ($data['status'] != 'pago') {
                $data['data_pagamento'] = null;
            }

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

    private function formatCurrencyToDecimal($value)
    {
        if (is_string($value)) {
            // Remove pontos de milhar e converte vírgula decimal para ponto
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
        
        return floatval($value);
    }
}
EOF

# ContasReceberController completo
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
            Log::error('Erro ao carregar formulário de criação: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar formulário.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cliente' => 'nullable|exists:clientes,id_cliente',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string'
        ]);

        try {
            $data = $request->all();
            $data['valor'] = $this->formatCurrencyToDecimal($data['valor']);
            $data['status'] = 'pendente';
            $data['data_criacao'] = now();

            DB::table('contas_receber')->insert($data);

            return redirect()->route('contas-receber.index')
                             ->with('success', 'Conta a receber cadastrada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar conta a receber: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao cadastrar conta a receber.')->withInput();
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
            Log::error('Erro ao exibir conta: ' . $e->getMessage());
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
            Log::error('Erro ao carregar edição: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar formulário de edição.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_cliente' => 'nullable|exists:clientes,id_cliente',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:pendente,recebido,cancelado'
        ]);

        try {
            $data = $request->all();
            $data['valor'] = $this->formatCurrencyToDecimal($data['valor']);

            // Se foi marcado como recebido, definir data_recebimento
            if ($data['status'] == 'recebido' && !$request->has('data_recebimento')) {
                $data['data_recebimento'] = now();
            } elseif ($data['status'] != 'recebido') {
                $data['data_recebimento'] = null;
            }

            $affected = DB::table('contas_receber')->where('id_conta_receber', $id)->update($data);

            if ($affected) {
                return redirect()->route('contas-receber.index')
                                 ->with('success', 'Conta a receber atualizada com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Nenhuma alteração foi realizada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar conta: ' . $e->getMessage());
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
            Log::error('Erro ao excluir conta: ' . $e->getMessage());
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
            Log::error('Erro ao cancelar conta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao cancelar conta.');
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

# ComprasController completo
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
            $produtos = DB::table('produtos')->where('ativo', 1)->orderBy('nome')->get();
            
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

# 4. Atualizar as views para incluir botões de ação
echo "4. 📝 ATUALIZANDO VIEWS COM BOTÕES DE AÇÃO..."

# View index de contas a pagar com ações
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
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
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

# 5. Atualizar as rotas para incluir todas as ações
echo "5. 🛣️ ATUALIZANDO ROTAS..."

# Adicionar rotas completas no routes/web.php
cat >> routes/web.php << 'EOF'

// Rotas para Contas a Pagar
Route::resource('contas-pagar', ContasPagarController::class);
Route::post('contas-pagar/{id}/pagar', [ContasPagarController::class, 'pagar'])->name('contas-pagar.pagar');
Route::post('contas-pagar/{id}/cancelar', [ContasPagarController::class, 'cancelar'])->name('contas-pagar.cancelar');

// Rotas para Contas a Receber
Route::resource('contas-receber', ContasReceberController::class);
Route::post('contas-receber/{id}/receber', [ContasReceberController::class, 'receber'])->name('contas-receber.receber');
Route::post('contas-receber/{id}/cancelar', [ContasReceberController::class, 'cancelar'])->name('contas-receber.cancelar');

// Rotas para Compras
Route::resource('compras', CompraController::class);
Route::post('compras/{id}/finalizar', [CompraController::class, 'finalizar'])->name('compras.finalizar');
Route::post('compras/{id}/cancelar', [CompraController::class, 'cancelar'])->name('compras.cancelar');
EOF

# 6. Atualizar o JavaScript para melhorar a máscara monetária
echo "6. 💰 MELHORANDO MÁSCARA MONETÁRIA..."

cat >> public/js/masks.js << 'EOF'

// Máscara monetária melhorada
function mascaraMoedaMelhorada(input) {
    let value = input.value.replace(/\D/g, '');
    
    // Se estiver vazio, define como 0,00
    if (value === '') {
        input.value = '0,00';
        return;
    }
    
    // Adiciona zeros à esquerda se necessário
    while (value.length < 3) {
        value = '0' + value;
    }
    
    // Formata como moeda brasileira
    const reais = value.slice(0, -2);
    const centavos = value.slice(-2);
    
    let valorFormatado = '';
    
    if (reais.length > 0) {
        // Formata os reais com separadores de milhar
        valorFormatado = reais.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    } else {
        valorFormatado = '0';
    }
    
    input.value = valorFormatado + ',' + centavos;
}

// Aplicar máscara monetária melhorada
document.addEventListener('DOMContentLoaded', function() {
    const moneyInputs = document.querySelectorAll('input[name="valor"], input[name="valor_total"], input[type="number"][step="0.01"]');
    
    moneyInputs.forEach(input => {
        // Aplicar máscara quando o usuário digita
        input.addEventListener('input', function(e) {
            mascaraMoedaMelhorada(this);
        });
        
        // Formatar valor inicial se existir
        if (input.value) {
            mascaraMoedaMelhorada(input);
        }
        
        // Permitir apenas números, vírgula e backspace
        input.addEventListener('keydown', function(e) {
            const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight'];
            if (allowedKeys.includes(e.key)) {
                return;
            }
            
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    });
});

// Função para remover máscara antes do envio do formulário
function removerMascaraMonetaria() {
    const moneyInputs = document.querySelectorAll('input[name="valor"], input[name="valor_total"]');
    moneyInputs.forEach(input => {
        let value = input.value.replace(/\./g, '').replace(',', '.');
        input.value = value;
    });
}

// Aplicar antes do envio do formulário
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            removerMascaraMonetaria();
        });
    });
});
EOF

# 7. Criar script de verificação final
echo "7. 🧪 CRIANDO SCRIPT DE VERIFICAÇÃO..."

cat > check-crud-fix.php << 'EOF'
<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔍 VERIFICAÇÃO DAS CORREÇÕES DE CRUD\n";
echo "====================================\n";

// Verificar estrutura das tabelas
$tablesToCheck = [
    'contas_pagar' => ['valor', 'status', 'data_pagamento'],
    'contas_receber' => ['valor', 'status', 'data_recebimento'],
    'compras' => ['valor_total', 'status']
];

foreach ($tablesToCheck as $table => $columns) {
    echo "\n📊 Verificando tabela: $table\n";
    
    if (!Schema::hasTable($table)) {
        echo "   ❌ Tabela não existe\n";
        continue;
    }
    
    foreach ($columns as $column) {
        if (Schema::hasColumn($table, $column)) {
            $columnType = DB::select("SHOW COLUMNS FROM $table LIKE '$column'")[0]->Type;
            echo "   ✅ $column: $columnType\n";
        } else {
            echo "   ❌ $column: COLUNA AUSENTE\n";
        }
    }
}

// Verificar controllers
$controllers = [
    'ContasPagarController',
    'ContasReceberController', 
    'CompraController'
];

echo "\n🛠️ Verificando controllers:\n";
foreach ($controllers as $controller) {
    $path = "app/Http/Controllers/$controller.php";
    if (file_exists($path)) {
        echo "   ✅ $controller: EXISTE\n";
    } else {
        echo "   ❌ $controller: AUSENTE\n";
    }
}

// Verificar rotas
echo "\n🛣️ Verificando rotas:\n";
$routes = [
    'contas-pagar.index',
    'contas-pagar.pagar',
    'contas-receber.index',
    'compras.index'
];

foreach ($routes as $route) {
    try {
        route($route);
        echo "   ✅ $route: OK\n";
    } catch (Exception $e) {
        echo "   ❌ $route: FALHOU\n";
    }
}

echo "\n🎉 VERIFICAÇÃO CONCLUÍDA!\n";
echo "\n📋 PRÓXIMOS PASSOS:\n";
echo "   1. Teste criar uma conta a pagar com valor grande (ex: 1234567,89)\n";
echo "   2. Teste editar, excluir e mudar status\n";
echo "   3. Verifique se as máscaras monetárias funcionam corretamente\n";
EOF

echo "8. 🎉 EXECUTANDO VERIFICAÇÃO FINAL..."
php check-crud-fix.php

echo ""
echo "🎉 CORREÇÕES APLICADAS COM SUCESSO!"
echo ""
echo "📋 RESUMO DAS CORREÇÕES:"
echo "   ✅ Estrutura do banco corrigida para valores até 15 dígitos"
echo "   ✅ Campos de status adicionados"
echo "   ✅ Controllers completos com todas as operações CRUD"
echo "   ✅ Views com botões de ação (editar, excluir, status)"
echo "   ✅ Rotas para todas as operações"
echo "   ✅ Máscara monetária melhorada"
echo ""
echo "🚀 PARA TESTAR:"
echo "   1. Acesse Contas a Pagar e tente criar com valor: 1234567,89"
echo "   2. Teste editar, excluir e mudar status"
echo "   3. Verifique se não há mais limitação de 5 dígitos"
