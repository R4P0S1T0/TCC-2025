<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FornecedorController extends Controller
{
    /** 🔹 Listagem com filtros */
    public function index(Request $request)
    {
        $query = DB::table('fornecedores');

        // 🔍 Filtros
        if ($request->filled('busca')) {
            $busca = '%' . $request->busca . '%';
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', $busca)
                  ->orWhere('cnpj', 'like', $busca)
                  ->orWhere('email', 'like', $busca)
                  ->orWhere('telefone', 'like', $busca)
                  ->orWhere('cidade', 'like', $busca)
                  ->orWhere('estado', 'like', $busca);
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('cidade')) {
            $query->where('cidade', 'like', '%' . $request->cidade . '%');
        }

        // 🔽 Ordenar por mais recente
        $fornecedores = $query->orderByDesc('id_fornecedor')->get();

        return view('fornecedores.index', compact('fornecedores'));
    }

    /** 🔹 Criar fornecedor */
    public function create()
    {
        return view('fornecedores.create');
    }

    /** 🔹 Armazenar fornecedor */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:100',
            'cnpj' => 'required|string|max:20',
            'telefone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'cep' => 'nullable|string|max:9',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2'
        ]);

        $data['endereco'] = $this->montarEndereco($data);

        DB::table('fornecedores')->insert($data);

        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor cadastrado com sucesso!');
    }

    /** 🔹 Exibir fornecedor */
    public function show($id)
    {
        $fornecedor = DB::table('fornecedores')->where('id_fornecedor', $id)->first();
        return view('fornecedores.show', compact('fornecedor'));
    }

    /** 🔹 Editar fornecedor */
    public function edit($id)
    {
        $fornecedor = DB::table('fornecedores')->where('id_fornecedor', $id)->first();
        return view('fornecedores.edit', compact('fornecedor'));
    }

    /** 🔹 Atualizar fornecedor */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:100',
            'cnpj' => 'required|string|max:20',
            'telefone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'cep' => 'nullable|string|max:9',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2'
        ]);

        $data['endereco'] = $this->montarEndereco($data);

        DB::table('fornecedores')->where('id_fornecedor', $id)->update($data);

        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor atualizado com sucesso!');
    }

    /** 🔹 Excluir fornecedor */
    public function destroy($id)
    {
        DB::table('fornecedores')->where('id_fornecedor', $id)->delete();
        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor excluído com sucesso!');
    }

    /** 🔹 Montar endereço completo */
    private function montarEndereco($data)
    {
        $endereco = $data['logradouro'] . ', ' . $data['numero'];
        if (!empty($data['complemento'])) {
            $endereco .= ' - ' . $data['complemento'];
        }
        $endereco .= ' - ' . $data['bairro'];
        $endereco .= ' - ' . $data['cidade'] . '/' . $data['estado'];
        if (!empty($data['cep'])) {
            $endereco .= ' - CEP: ' . $data['cep'];
        }
        return $endereco;
    }
}
