<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Fornecedor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompraController extends Controller
{
    /** 🔹 Listagem de compras */
    public function index(Request $request)
    {
        $query = Compra::select('compras.*', 'fornecedores.nome as fornecedor_nome')
            ->leftJoin('fornecedores', 'compras.id_fornecedor', '=', 'fornecedores.id_fornecedor');

        // Filtros dinâmicos
        if ($request->filled('status')) {
            $query->where('compras.status', $request->status);
        }

        if ($request->filled('tipo')) {
            $query->where('compras.tipo', $request->tipo);
        }

        if ($request->filled('id_fornecedor')) {
            $query->where('compras.id_fornecedor', $request->id_fornecedor);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('compras.descricao', 'like', "%{$busca}%")
                    ->orWhere('compras.id_compra', 'like', "%{$busca}%")
                    ->orWhere('compras.nota_fiscal', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('compras.data_compra', [$request->data_inicio, $request->data_fim]);
        }

        $compras = $query->orderByDesc('compras.id_compra')->get();
        $fornecedores = Fornecedor::orderBy('nome')->get();

        return view('compras.index', compact('compras', 'fornecedores'));
    }

    /** 🔹 Exibir formulário de criação */
    public function create()
    {
        $fornecedores = Fornecedor::orderBy('nome')->get();
        return view('compras.create', compact('fornecedores'));
    }

    /** 🔹 Salvar nova compra e gerar conta a pagar */
    public function store(Request $request)
    {
        // Normaliza valor e data
        if ($request->filled('valor_total')) {
            $valor = preg_replace('/[^\d,]/', '', $request->valor_total);
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
            $request->merge(['valor_total' => $valor]);
        }

        if ($request->filled('data_compra') && str_contains($request->data_compra, '/')) {
            $data = Carbon::createFromFormat('d/m/Y', $request->data_compra)->format('Y-m-d');
            $request->merge(['data_compra' => $data]);
        }

        // Validação
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'id_fornecedor' => 'nullable|exists:fornecedores,id_fornecedor',
            'valor_total' => 'required|numeric|min:0|max:999999999999.99',
            'nota_fiscal' => 'nullable|string|max:50',
            'tipo' => 'required|string|in:produto,serviço',
            'data_compra' => 'required|date',
            'status' => 'nullable|string|in:pendente,finalizada,cancelada',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $validated['status'] = $validated['status'] ?? 'pendente';

        // Cria compra
        $compra = Compra::create($validated);

        // 🔹 Cria conta a pagar vinculada
        DB::table('contas_pagar')->insert([
            'id_compra' => $compra->id_compra,
            'valor' => $compra->valor_total,
            'nota_fiscal' => $compra->nota_fiscal,
            'data_vencimento' => $compra->data_compra,
            'status' => 'pendente'
        ]);

        return redirect()->route('compras.index')->with('success', 'Compra cadastrada e conta a pagar criada com sucesso!');
    }

    /** 🔹 Editar compra */
    public function edit($id)
    {
        $compra = Compra::findOrFail($id);
        $fornecedores = Fornecedor::orderBy('nome')->get();
        return view('compras.edit', compact('compra', 'fornecedores'));
    }

    /** 🔹 Atualizar compra */
    public function update(Request $request, $id)
    {
        $compra = Compra::findOrFail($id);

        if ($request->filled('valor_total')) {
            $valor = preg_replace('/[^\d,]/', '', $request->valor_total);
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
            $request->merge(['valor_total' => $valor]);
        }

        if ($request->filled('data_compra') && str_contains($request->data_compra, '/')) {
            try {
                $data = Carbon::createFromFormat('d/m/Y', $request->data_compra)->format('Y-m-d');
                $request->merge(['data_compra' => $data]);
            } catch (\Exception $e) {}
        }

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'id_fornecedor' => 'nullable|exists:fornecedores,id_fornecedor',
            'valor_total' => 'required|numeric|min:0|max:999999999999.99',
            'data_compra' => 'required|date',
            'nota_fiscal' => 'nullable|string|max:50',
            'tipo' => 'required|string|in:produto,serviço',
            'status' => 'required|string|in:pendente,finalizada,cancelada',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $compra->update($validated);

        // 🔹 Atualiza conta a pagar correspondente
        DB::table('contas_pagar')
            ->where('id_compra', $id)
            ->update([
                'valor' => $compra->valor_total,
                'nota_fiscal' => $compra->nota_fiscal,
                'data_vencimento' => $compra->data_compra,
            ]);

        return redirect()->route('compras.index')->with('success', 'Compra e conta a pagar atualizadas com sucesso!');
    }

    /** 🔹 Finalizar compra e marcar como paga no contas_pagar */
    public function finalizar($id)
    {
        $compra = Compra::findOrFail($id);
        $compra->update(['status' => 'finalizada']);

        DB::table('contas_pagar')
            ->where('id_compra', $id)
            ->update(['status' => 'pago']);

        return redirect()->route('compras.index')->with('success', 'Compra finalizada e pagamento registrado!');
    }

    /** 🔹 Cancelar compra */
    public function cancelar($id)
    {
        $compra = Compra::findOrFail($id);
        $compra->update(['status' => 'cancelada']);

        DB::table('contas_pagar')
            ->where('id_compra', $id)
            ->update(['status' => 'pendente']);

        return redirect()->route('compras.index')->with('success', 'Compra cancelada.');
    }

    /** 🔹 Excluir compra */
    public function destroy($id)
    {
        $compra = Compra::findOrFail($id);

        if ($compra->status === 'finalizada') {
            return back()->with('error', 'Não é possível excluir uma compra finalizada.');
        }

        $compra->delete();

        return redirect()->route('compras.index')->with('success', 'Compra excluída com sucesso!');
    }

    /** 🔹 Exibir detalhes */
    public function show($id)
    {
        $compra = Compra::with('fornecedor')->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    /** 🔹 Endpoint AJAX — para Contas a Pagar */
    public function dadosAjax($id)
    {
        $compra = Compra::where('status', 'pendente')->find($id);

        if (!$compra) {
            return response()->json(['error' => 'Compra não encontrada ou já finalizada.'], 404);
        }

        return response()->json([
            'valor_total' => number_format($compra->valor_total, 2, ',', '.'),
            'nota_fiscal' => $compra->nota_fiscal ?? '',
            'data_compra' => $compra->data_compra
                ? Carbon::parse($compra->data_compra)->format('d/m/Y')
                : '',
        ]);
    }
}
