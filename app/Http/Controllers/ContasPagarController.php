<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContaPagar;
use App\Models\Compra;
use Carbon\Carbon;

class ContasPagarController extends Controller
{
    /**
     * 🔹 Listar contas a pagar
     */
    public function index(Request $request)
    {
        $query = ContaPagar::with('compra');

        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        if ($request->filled('busca_compra')) {
            $termo = trim($request->busca_compra);
            $termoLimpo = ltrim($termo, '#');

            $query->whereHas('compra', function ($q) use ($termoLimpo) {
                $q->where('id_compra', 'like', "%$termoLimpo%")
                    ->orWhere('descricao', 'like', "%$termoLimpo%");
            });
        }

        $contas = $query->orderBy('data_vencimento', 'asc')->paginate(10)->withQueryString();

        return view('contas-pagar.index', compact('contas'));
    }

    /**
     * 🔹 Formulário de criação
     */
    public function create()
    {
        $compras = Compra::whereNotIn('status', ['finalizada', 'cancelada'])
            ->orderByDesc('id_compra')
            ->get();

        return view('contas-pagar.create', compact('compras'));
    }

    /**
     * 🔹 Salvar nova conta
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_compra' => 'nullable|integer|exists:compras,id_compra',
            'valor' => 'required|string|max:30',
            'data_vencimento' => 'required|string|max:20',
            'status' => 'required|string|in:pendente,pago',
            'nota_fiscal' => 'nullable|string|max:50',
        ]);

        // ✅ Converte "R$ 333.333.333,33" -> "333333333.33"
        $valor = trim($request->valor);

        // Remove símbolos de moeda e espaços
        $valor = str_replace(['R$', ' '], '', $valor);

        // Remove pontos de milhar, mantém a vírgula
        $valor = preg_replace('/\.(?=\d{3}(?:,|$))/', '', $valor);

        // Troca vírgula decimal por ponto
        $valor = str_replace(',', '.', $valor);

        // Garante formato decimal válido
        $validated['valor'] = number_format((float) $valor, 2, '.', '');

        // ✅ Converte data BR -> ISO
        if (str_contains($validated['data_vencimento'], '/')) {
            $validated['data_vencimento'] = Carbon::createFromFormat('d/m/Y', $validated['data_vencimento'])->format('Y-m-d');
        }

        $validated['nota_fiscal'] = $request->nota_fiscal;

        ContaPagar::create($validated);

        return redirect()->route('contas-pagar.index')->with('success', 'Conta cadastrada com sucesso!');
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_compra' => 'nullable|integer|exists:compras,id_compra',
            'valor' => 'required|string|max:30',
            'data_vencimento' => 'required|string|max:20',
            'status' => 'required|string|in:pendente,pago',
            'nota_fiscal' => 'nullable|string|max:50',
        ]);

        $valor = trim($request->valor);

        // Remove símbolos de moeda e espaços
        $valor = str_replace(['R$', ' '], '', $valor);

        // Remove pontos de milhar, mantém a vírgula
        $valor = preg_replace('/\.(?=\d{3}(?:,|$))/', '', $valor);

        // Troca vírgula decimal por ponto
        $valor = str_replace(',', '.', $valor);

        // Garante formato decimal válido
        $validated['valor'] = number_format((float) $valor, 2, '.', '');

        if (str_contains($validated['data_vencimento'], '/')) {
            $validated['data_vencimento'] = Carbon::createFromFormat('d/m/Y', $validated['data_vencimento'])->format('Y-m-d');
        }

        $validated['nota_fiscal'] = $request->nota_fiscal;

        ContaPagar::findOrFail($id)->update($validated);

        return redirect()->route('contas-pagar.index')->with('success', 'Conta atualizada com sucesso!');
    }



    /**
     * 🔹 Exibir conta
     */
    public function show($id)
    {
        $conta = ContaPagar::with('compra')->findOrFail($id);
        return view('contas-pagar.show', compact('conta'));
    }

    /**
     * 🔹 Editar conta
     */
    public function edit($id)
    {
        $conta = ContaPagar::findOrFail($id);
        $compras = Compra::whereNotIn('status', ['finalizada', 'cancelada'])
            ->orderByDesc('id_compra')
            ->get();

        return view('contas-pagar.edit', compact('conta', 'compras'));
    }



    /**
     * 🔹 Excluir conta
     */
    public function destroy($id)
    {
        ContaPagar::findOrFail($id)->delete();

        return redirect()->route('contas-pagar.index')
            ->with('success', 'Conta excluída com sucesso!');
    }

    /**
     * 🔹 Alternar status (PAGO ↔ PENDENTE)
     */
    public function toggleStatus($id)
    {
        $conta = ContaPagar::findOrFail($id);
        $conta->status = $conta->status === 'pago' ? 'pendente' : 'pago';
        $conta->save();

        return redirect()->route('contas-pagar.index')
            ->with('success', 'Status atualizado com sucesso!');
    }
}
