<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Exibe a listagem de clientes
     */
    public function index()
    {
        $clientes = Cliente::orderBy('nome')->paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Exibe o formulário de criação
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Salva um novo cliente no banco
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'         => 'required|string|max:150',
            'email'        => 'nullable|email|max:150',
            'telefone'     => 'nullable|string|max:30',
            'cpf_cnpj'     => 'nullable|string|max:20',
            'cep'          => 'nullable|string|max:9',
            'endereco'     => 'nullable|string|max:255',
            'numero'       => 'nullable|string|max:10',
            'complemento'  => 'nullable|string|max:100',
            'bairro'       => 'nullable|string|max:100',
            'cidade'       => 'nullable|string|max:100',
            'estado'       => 'nullable|string|max:2',
            'status'       => 'required|in:ativo,inativo',
        ]);

        Cliente::create($validated);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Exibe o formulário de edição
     */
    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Atualiza os dados de um cliente existente
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nome'         => 'required|string|max:150',
            'email'        => 'nullable|email|max:150',
            'telefone'     => 'nullable|string|max:30',
            'cpf_cnpj'     => 'nullable|string|max:20',
            'cep'          => 'nullable|string|max:9',
            'endereco'     => 'nullable|string|max:255',
            'numero'       => 'nullable|string|max:10',
            'complemento'  => 'nullable|string|max:100',
            'bairro'       => 'nullable|string|max:100',
            'cidade'       => 'nullable|string|max:100',
            'estado'       => 'nullable|string|max:2',
            'status'       => 'required|in:ativo,inativo',
        ]);

        $cliente = Cliente::findOrFail($id);
        $cliente->update($validated);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Exclui um cliente
     */
    public function destroy($id)
    {
        Cliente::findOrFail($id)->delete();

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }

    /**
     * Exibe os detalhes de um cliente específico
     */
    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.show', compact('cliente'));
    }
}
