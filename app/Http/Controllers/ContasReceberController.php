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
            $data = $request->except(['_token', '_method']);
            
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
            $data = $request->except(['_token', '_method']);
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
