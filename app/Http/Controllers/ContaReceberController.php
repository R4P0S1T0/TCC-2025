<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContaReceberController extends Controller
{
    /** 🧾 Listagem */
    public function index(Request $request)
    {
        // Inicia a query base
        $query = \App\Models\ContaReceber::query();

        // 🔹 Filtros dinâmicos
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('descricao', 'like', "%{$busca}%")
                    ->orWhere('cliente_nome', 'like', "%{$busca}%")
                    ->orWhere('id_creceber', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_vencimento', [$request->data_inicio, $request->data_fim]);
        }

        // 🔹 Ordena da mais recente para a mais antiga
        $contas = $query->orderByDesc('id_creceber')->get();

        return view('contas-receber.index', compact('contas'));
    }



    /** 🧩 Formulário de criação */
    public function create()
    {
        // 🔹 Puxa todos os clientes ativos do banco
        $clientes = DB::table('clientes')
            ->where('status', 'ativo')
            ->orderBy('nome', 'asc')
            ->get();

        return view('contas-receber.create', compact('clientes'));
    }

    /** 💾 Armazena uma nova conta */
    public function store(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'cliente_nome' => 'required|string|max:255',
            'valor' => 'required',
            'data_vencimento' => 'required',
        ]);

        // 🔹 Conversão segura do valor
        $valor = preg_replace('/[^\d,\.]/', '', $request->valor);
        if (strpos($valor, ',') !== false && strpos($valor, '.') !== false) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } elseif (strpos($valor, ',') !== false) {
            $valor = str_replace(',', '.', $valor);
        }
        $valor = floatval($valor);

        // 🔹 Conversão segura da data
        try {
            $dataVencimento = preg_match('/\d{2}\/\d{2}\/\d{4}/', $request->data_vencimento)
                ? Carbon::createFromFormat('d/m/Y', $request->data_vencimento)->format('Y-m-d')
                : Carbon::parse($request->data_vencimento)->format('Y-m-d');
        } catch (\Exception $e) {
            $dataVencimento = now()->format('Y-m-d');
        }

        // 🔹 Inserção no banco
        DB::table('contas_receber')->insert([
            'descricao' => $request->descricao,
            'cliente_nome' => $request->cliente_nome,
            'valor' => $valor,
            'data_vencimento' => $dataVencimento,
            'status' => 'pendente',
            'observacoes' => $request->observacoes,
            'id_pedido' => null,
        ]);

        return redirect()->route('contas-receber.index')
            ->with('success', 'Conta a receber cadastrada com sucesso!');
    }

    /** 👁️ Visualiza uma conta */
    public function show($id)
    {
        $conta = DB::table('contas_receber')->where('id_creceber', $id)->first();

        if (!$conta) {
            return redirect()->route('contas-receber.index')
                ->with('error', 'Conta não encontrada.');
        }

        return view('contas-receber.show', compact('conta'));
    }

    /** ✏️ Editar uma conta */
    public function edit($id)
    {
        $conta = DB::table('contas_receber')->where('id_creceber', $id)->first();

        if (!$conta) {
            return redirect()->route('contas-receber.index')
                ->with('error', 'Conta não encontrada.');
        }

        // 🔹 Também traz os clientes para o select
        $clientes = DB::table('clientes')
            ->where('status', 'ativo')
            ->orderBy('nome', 'asc')
            ->get();

        return view('contas-receber.edit', compact('conta', 'clientes'));
    }

    /** 🔄 Atualiza uma conta */
    public function update(Request $request, $id)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'cliente_nome' => 'required|string|max:255',
            'valor' => 'required',
            'data_vencimento' => 'required',
        ]);

        // Conversão do valor
        $valor = preg_replace('/[^\d,\.]/', '', $request->valor);
        if (strpos($valor, ',') !== false && strpos($valor, '.') !== false) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } elseif (strpos($valor, ',') !== false) {
            $valor = str_replace(',', '.', $valor);
        }
        $valor = floatval($valor);

        // Conversão da data
        try {
            $dataVencimento = preg_match('/\d{2}\/\d{2}\/\d{4}/', $request->data_vencimento)
                ? Carbon::createFromFormat('d/m/Y', $request->data_vencimento)->format('Y-m-d')
                : Carbon::parse($request->data_vencimento)->format('Y-m-d');
        } catch (\Exception $e) {
            $dataVencimento = now()->format('Y-m-d');
        }

        DB::table('contas_receber')
            ->where('id_creceber', $id)
            ->update([
                'descricao' => $request->descricao,
                'cliente_nome' => $request->cliente_nome,
                'valor' => $valor,
                'data_vencimento' => $dataVencimento,
                'status' => $request->status ?? 'pendente',
                'observacoes' => $request->observacoes,
            ]);

        return redirect()->route('contas-receber.index')
            ->with('success', 'Conta atualizada com sucesso!');
    }

    /** ❌ Exclui uma conta */
    public function destroy($id)
    {
        DB::table('contas_receber')->where('id_creceber', $id)->delete();

        return redirect()->route('contas-receber.index')
            ->with('success', 'Conta excluída com sucesso!');
    }
}
