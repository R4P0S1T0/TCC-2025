<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Fornecedor;
use Carbon\Carbon;

class CompraController extends Controller
{
    /** 🔹 Listagem de compras */
    public function index()
    {
        $compras = Compra::select('compras.*', 'fornecedores.nome as fornecedor_nome')
            ->leftJoin('fornecedores', 'compras.id_fornecedor', '=', 'fornecedores.id_fornecedor')
            ->orderByDesc('compras.data_compra')
            ->get();

        return view('compras.index', compact('compras'));
    }

    /** 🔹 Exibir formulário de criação */
    public function create()
    {
        $fornecedores = Fornecedor::orderBy('nome')->get();
        return view('compras.create', compact('fornecedores'));
    }

    /** 🔹 Salvar nova compra */
    public function store(Request $request)
    {
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

        Compra::create($validated);

        return redirect()->route('compras.index')->with('success', 'Compra cadastrada com sucesso!');
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
            } catch (\Exception $e) {
                // ignora erro de formatação
            }
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

        return redirect()->route('compras.index')->with('success', 'Compra atualizada com sucesso!');
    }

    /** 🔹 Excluir compra */
    public function destroy($id)
    {
        Compra::findOrFail($id)->delete();

        return redirect()->route('compras.index')->with('success', 'Compra excluída com sucesso!');
    }

    /** 🔹 Finalizar compra */
    public function finalizar($id)
    {
        $compra = Compra::findOrFail($id);
        $compra->update(['status' => 'finalizada']);

        return redirect()->route('compras.index')->with('success', 'Compra finalizada com sucesso!');
    }

    /** 🔹 Cancelar compra */
    public function cancelar($id)
    {
        $compra = Compra::findOrFail($id);
        $compra->update(['status' => 'cancelada']);

        return redirect()->route('compras.index')->with('success', 'Compra cancelada com sucesso!');
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
