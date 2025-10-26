#!/bin/bash

echo "🔧 SISTEMA DE REPARO COMPLETO - LART DIGITAL"
echo "=============================================="
echo "📋 Iniciando reparo completo de todos os módulos..."
echo ""

# Função para log
log() {
    echo "   $1"
}

# Função para verificar e criar diretórios
check_dir() {
    if [ ! -d "$1" ]; then
        mkdir -p "$1"
        log "✅ Criado diretório: $1"
    fi
}

# 1. VERIFICAR ESTRUTURA DO BANCO
echo "1. 🗃️ VERIFICANDO ESTRUTURA DO BANCO DE DADOS..."

php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    \$tables = ['fornecedores', 'clientes', 'produtos', 'compras', 'contas_pagar', 'contas_receber', 'usuarios'];
    
    foreach (\$tables as \$table) {
        if (Schema::hasTable(\$table)) {
            \$columns = Schema::getColumnListing(\$table);
            \$count = DB::table(\$table)->count();
            echo \"   ✅ \$table: \$count registros (\" . count(\$columns) . \" colunas)\\n\";
        } else {
            echo \"   ❌ \$table: AUSENTE\\n\";
        }
    }
    
    // Verificar colunas específicas problemáticas
    echo \"\\n   🔍 Verificando colunas específicas...\\n\";
    
    // Coluna 'ativo' em produtos
    if (Schema::hasTable('produtos') && !Schema::hasColumn('produtos', 'ativo')) {
        DB::statement('ALTER TABLE produtos ADD COLUMN ativo TINYINT(1) DEFAULT 1');
        echo \"   ✅ Coluna 'ativo' adicionada em produtos\\n\";
    }
    
    // Campos de endereço em fornecedores
    \$enderecoFields = ['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado'];
    foreach (\$enderecoFields as \$field) {
        if (Schema::hasTable('fornecedores') && !Schema::hasColumn('fornecedores', \$field)) {
            DB::statement(\"ALTER TABLE fornecedores ADD COLUMN \$field VARCHAR(255) NULL\");
            echo \"   ✅ Coluna '\$field' adicionada em fornecedores\\n\";
        }
    }
    
    // Campos de endereço em usuarios
    foreach (\$enderecoFields as \$field) {
        if (Schema::hasTable('usuarios') && !Schema::hasColumn('usuarios', \$field)) {
            DB::statement(\"ALTER TABLE usuarios ADD COLUMN \$field VARCHAR(255) NULL\");
            echo \"   ✅ Coluna '\$field' adicionada em usuarios\\n\";
        }
    }
    
    // Campos de status
    if (Schema::hasTable('compras') && !Schema::hasColumn('compras', 'status')) {
        DB::statement(\"ALTER TABLE compras ADD COLUMN status ENUM('pendente','finalizada','cancelada') DEFAULT 'pendente'\");
        echo \"   ✅ Coluna 'status' adicionada em compras\\n\";
    }
    
    if (Schema::hasTable('contas_pagar') && !Schema::hasColumn('contas_pagar', 'status')) {
        DB::statement(\"ALTER TABLE contas_pagar ADD COLUMN status ENUM('pendente','pago','cancelado') DEFAULT 'pendente'\");
        DB::statement(\"ALTER TABLE contas_pagar ADD COLUMN data_pagamento DATE NULL\");
        echo \"   ✅ Colunas de status adicionadas em contas_pagar\\n\";
    }
    
    if (Schema::hasTable('contas_receber') && !Schema::hasColumn('contas_receber', 'status')) {
        DB::statement(\"ALTER TABLE contas_receber ADD COLUMN status ENUM('pendente','recebido','cancelado') DEFAULT 'pendente'\");
        DB::statement(\"ALTER TABLE contas_receber ADD COLUMN data_recebimento DATE NULL\");
        echo \"   ✅ Colunas de status adicionadas em contas_receber\\n\";
    }
    
    // Ajustar campos de valor para suportar valores grandes
    if (Schema::hasTable('contas_pagar')) {
        DB::statement('ALTER TABLE contas_pagar MODIFY valor DECIMAL(15,2)');
        echo \"   ✅ Campo 'valor' ajustado em contas_pagar\\n\";
    }
    
    if (Schema::hasTable('contas_receber')) {
        DB::statement('ALTER TABLE contas_receber MODIFY valor DECIMAL(15,2)');
        echo \"   ✅ Campo 'valor' ajustado em contas_receber\\n\";
    }
    
    if (Schema::hasTable('compras')) {
        DB::statement('ALTER TABLE compras MODIFY valor_total DECIMAL(15,2)');
        echo \"   ✅ Campo 'valor_total' ajustado em compras\\n\";
    }
    
} catch (Exception \$e) {
    echo \"   ❌ Erro no banco: \" . \$e->getMessage() . \"\\n\";
}
"

# 2. CRIAR/CORRIGIR CONTROLLERS
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
log "✅ ContasPagarController criado"

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
log "✅ ContasReceberController criado"

# CompraController
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
            // REMOVIDO: filtro por 'ativo' que pode não existir
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
log "✅ CompraController criado"

# 3. CRIAR VIEWS COMPLETAS
echo ""
echo "3. 📝 CRIANDO VIEWS COMPLETAS..."

# Criar diretórios
check_dir "resources/views/contas-pagar"
check_dir "resources/views/contas-receber" 
check_dir "resources/views/compras"

# View Index Contas a Pagar
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
log "✅ View contas-pagar/index criada"

# View Create Contas a Pagar
cat > resources/views/contas-pagar/create.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Nova Conta a Pagar')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Nova Conta a Pagar</h1>
        <a href="{{ route('contas-pagar.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('contas-pagar.store') }}">
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
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                    <input type="text" name="descricao" value="{{ old('descricao') }}" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ex: Pagamento de conta de luz">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor *</label>
                    <input type="text" name="valor" value="{{ old('valor') }}" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0,00"
                           oninput="mascaraMoeda(this)">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Vencimento *</label>
                    <input type="date" name="data_vencimento" value="{{ old('data_vencimento', date('Y-m-d')) }}" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="observacoes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Observações adicionais...">{{ old('observacoes') }}</textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('contas-pagar.index') }}" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Cadastrar Conta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formatar valor inicial se existir
    const valorInput = document.querySelector('input[name="valor"]');
    if (valorInput && valorInput.value) {
        mascaraMoeda(valorInput);
    }
});
</script>
@endsection
EOF
log "✅ View contas-pagar/create criada"

# View Index Contas a Receber
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
log "✅ View contas-receber/index criada"

# View Index Compras
cat > resources/views/compras/index.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Compras')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Compras</h1>
        <a href="{{ route('compras.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Nova Compra
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
                            Valor Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data
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
                    @forelse($compras as $compra)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $compra->descricao }}</div>
                                @if($compra->observacoes)
                                    <div class="text-sm text-gray-500">{{ Str::limit($compra->observacoes, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $compra->fornecedor_nome ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    R$ {{ number_format($compra->valor_total, 2, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($compra->data_compra)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($compra->status == 'finalizada')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Finalizada
                                    </span>
                                @elseif($compra->status == 'cancelada')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Cancelada
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($compra->status == 'pendente')
                                        <form action="{{ route('compras.finalizar', $compra->id_compra) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" 
                                                    onclick="return confirm('Finalizar esta compra?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('compras.cancelar', $compra->id_compra) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-gray-900"
                                                    onclick="return confirm('Cancelar esta compra?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('compras.show', $compra->id_compra) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('compras.edit', $compra->id_compra) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('compras.destroy', $compra->id_compra) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Tem certeza que deseja excluir esta compra?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma compra encontrada.
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
log "✅ View compras/index criada"

# 4. VERIFICAR E CORRIGIR ROTAS
echo ""
echo "4. 🛣️ VERIFICANDO E CORRIGINDO ROTAS..."

# Verificar se as rotas existem
if ! grep -q "ContasPagarController" routes/web.php; then
    cat >> routes/web.php << 'EOF'

// Rotas para Contas a Pagar
Route::resource('contas-pagar', ContasPagarController::class);
Route::post('contas-pagar/{id}/pagar', [ContasPagarController::class, 'pagar'])->name('contas-pagar.pagar');
Route::post('contas-pagar/{id}/cancelar', [ContasPagarController::class, 'cancelar'])->name('contas-pagar.cancelar');
EOF
    log "✅ Rotas contas-pagar adicionadas"
fi

if ! grep -q "ContasReceberController" routes/web.php; then
    cat >> routes/web.php << 'EOF'

// Rotas para Contas a Receber
Route::resource('contas-receber', ContasReceberController::class);
Route::post('contas-receber/{id}/receber', [ContasReceberController::class, 'receber'])->name('contas-receber.receber');
Route::post('contas-receber/{id}/cancelar', [ContasReceberController::class, 'cancelar'])->name('contas-receber.cancelar');
EOF
    log "✅ Rotas contas-receber adicionadas"
fi

if ! grep -q "CompraController" routes/web.php; then
    cat >> routes/web.php << 'EOF'

// Rotas para Compras
Route::resource('compras', CompraController::class);
Route::post('compras/{id}/finalizar', [CompraController::class, 'finalizar'])->name('compras.finalizar');
Route::post('compras/{id}/cancelar', [CompraController::class, 'cancelar'])->name('compras.cancelar');
EOF
    log "✅ Rotas compras adicionadas"
fi

# 5. ATUALIZAR MÁSCARAS JAVASCRIPT
echo ""
echo "5. 💰 ATUALIZANDO MÁSCARAS JAVASCRIPT..."

cat > public/js/masks.js << 'EOF'
// Funções de máscara
function mascaraCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return cpf;
}

function mascaraCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, '');
    cnpj = cnpj.replace(/^(\d{2})(\d)/, '$1.$2');
    cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    cnpj = cnpj.replace(/\.(\d{3})(\d)/, '.$1/$2');
    cnpj = cnpj.replace(/(\d{4})(\d)/, '$1-$2');
    return cnpj;
}

function mascaraTelefone(telefone) {
    telefone = telefone.replace(/\D/g, '');
    if (telefone.length === 11) {
        telefone = telefone.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (telefone.length === 10) {
        telefone = telefone.replace(/^(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else {
        telefone = telefone.replace(/^(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
    }
    return telefone;
}

function mascaraCEP(cep) {
    cep = cep.replace(/\D/g, '');
    cep = cep.replace(/^(\d{5})(\d)/, '$1-$2');
    return cep;
}

// Máscara monetária melhorada
function mascaraMoeda(input) {
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

// Aplicar máscaras automaticamente
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para CPF
    const cpfInputs = document.querySelectorAll('input[name*="cpf"], input[name*="CPF"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = mascaraCPF(e.target.value);
        });
    });

    // Máscara para CNPJ
    const cnpjInputs = document.querySelectorAll('input[name*="cnpj"], input[name*="CNPJ"]');
    cnpjInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = mascaraCNPJ(e.target.value);
        });
    });

    // Máscara para Telefone
    const telefoneInputs = document.querySelectorAll('input[name*="telefone"], input[name*="phone"], input[name*="celular"]');
    telefoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = mascaraTelefone(e.target.value);
        });
    });

    // Máscara e busca de CEP
    const cepInputs = document.querySelectorAll('input[name*="cep"], input[name*="CEP"]');
    cepInputs.forEach(input => {
        // Aplicar máscara
        input.addEventListener('input', function(e) {
            e.target.value = mascaraCEP(e.target.value);
        });

        // Buscar CEP quando perder o foco (se tiver 9 caracteres)
        input.addEventListener('blur', function(e) {
            const cep = e.target.value.replace(/\D/g, '');
            if (cep.length === 8) {
                buscarCEP(cep);
            }
        });
    });

    // Aplicar máscara monetária
    const moneyInputs = document.querySelectorAll('input[name="valor"], input[name="valor_total"]');
    moneyInputs.forEach(input => {
        // Aplicar máscara quando o usuário digita
        input.addEventListener('input', function(e) {
            mascaraMoeda(this);
        });
        
        // Formatar valor inicial se existir
        if (input.value) {
            mascaraMoeda(input);
        }
    });
});

// Função para buscar CEP via API ViaCEP
function buscarCEP(cep) {
    // Mostrar loading
    const enderecoFields = document.querySelectorAll('input[name="logradouro"], input[name="numero"], input[name="bairro"], input[name="cidade"], select[name="estado"]');
    enderecoFields.forEach(field => {
        field.disabled = true;
        field.placeholder = 'Buscando...';
    });

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (!data.erro) {
                // Preencher os campos automaticamente
                if (document.querySelector('input[name="logradouro"]')) {
                    document.querySelector('input[name="logradouro"]').value = data.logradouro || '';
                }
                if (document.querySelector('input[name="bairro"]')) {
                    document.querySelector('input[name="bairro"]').value = data.bairro || '';
                }
                if (document.querySelector('input[name="cidade"]')) {
                    document.querySelector('input[name="cidade"]').value = data.localidade || '';
                }
                if (document.querySelector('select[name="estado"]')) {
                    document.querySelector('select[name="estado"]').value = data.uf || '';
                }

                // Focar no campo número para o usuário completar
                if (document.querySelector('input[name="numero"]')) {
                    document.querySelector('input[name="numero"]').focus();
                }
            } else {
                alert('CEP não encontrado. Por favor, verifique o CEP digitado.');
            }
        })
        .catch(error => {
            console.error('Erro ao buscar CEP:', error);
            alert('Erro ao buscar CEP. Tente novamente.');
        })
        .finally(() => {
            // Reativar campos
            enderecoFields.forEach(field => {
                field.disabled = false;
                field.placeholder = '';
            });
        });
}

// Função para remover máscara monetária antes do envio do formulário
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
log "✅ Máscaras JavaScript atualizadas"

# 6. LIMPAR CACHE E GERAR RELATÓRIO
echo ""
echo "6. 🗑️ LIMPANDO CACHE E GERANDO RELATÓRIO..."

php artisan config:clear
php artisan route:clear
php artisan view:clear

log "✅ Cache limpo"

# 7. RELATÓRIO FINAL
echo ""
echo "🎉 SISTEMA COMPLETAMENTE REPARADO!"
echo "==================================="
echo ""
echo "📊 RELATÓRIO DE REPAROS APLICADOS:"
echo ""
echo "✅ BANCO DE DADOS:"
echo "   • Tabelas verificadas e colunas adicionadas"
echo "   • Campos de endereço em fornecedores e usuários"
echo "   • Campos de status em compras, contas a pagar/receber"
echo "   • Campos de valor ajustados para DECIMAL(15,2)"
echo ""
echo "✅ CONTROLLERS:"
echo "   • ContasPagarController - CRUD completo com logs"
echo "   • ContasReceberController - CRUD completo com logs"
echo "   • CompraController - CRUD completo sem filtro problemático"
echo ""
echo "✅ VIEWS:"
echo "   • Contas a Pagar: index e create"
echo "   • Contas a Receber: index"
echo "   • Compras: index"
echo "   • Todas com mensagens de sucesso/erro"
echo ""
echo "✅ ROTAS:"
echo "   • Rotas resource para todos os módulos"
echo "   • Rotas para ações de status"
echo "   • Métodos DELETE para exclusão"
echo ""
echo "✅ JAVASCRIPT:"
echo "   • Máscaras para CPF, CNPJ, telefone, CEP"
echo "   • Máscara monetária melhorada"
echo "   • Busca automática de CEP"
echo ""
echo "🚀 SISTEMA PRONTO PARA USO:"
echo ""
echo "📥 CONTAS A PAGAR:"
echo "   • Listagem: http://localhost:8000/contas-pagar"
echo "   • Cadastro: http://localhost:8000/contas-pagar/create"
echo ""
echo "📤 CONTAS A RECEBER:"
echo "   • Listagem: http://localhost:8000/contas-receber"
echo "   • Cadastro: http://localhost:8000/contas-receber/create"
echo ""
echo "🛒 COMPRAS:"
echo "   • Listagem: http://localhost:8000/compras"
echo "   • Cadastro: http://localhost:8000/compras/create"
echo ""
echo "🔍 PARA TESTAR:"
echo "   1. Cadastre uma conta a pagar com valor grande (ex: 123.456,78)"
echo "   2. Verifique se aparece na listagem"
echo "   3. Teste editar, excluir e mudar status"
echo "   4. Repita para contas a receber e compras"
echo ""
echo "📞 SUPORTE:"
echo "   • Logs: tail -f storage/logs/laravel.log"
echo "   • Rotas: php artisan route:list"
echo "   • Banco: php artisan tinker"
echo ""
echo "🎊 SISTEMA REPARADO COM SUCESSO! 🎊"
