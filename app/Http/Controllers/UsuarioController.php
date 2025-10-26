<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = DB::table('usuarios')->orderBy('nome')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:100',
                'cpf' => 'nullable|string|max:14|unique:usuarios,cpf',
                'email' => 'required|email|max:100|unique:usuarios,email',
                'telefone' => 'nullable|string|max:20',
                'senha' => 'required', 'string','min:8','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\-_])[A-Za-z\d@$!%*?&\-_]{8,}$/','confirmed',
                'tipo' => 'required|in:admin,super-admin',
                'cep' => 'nullable|string|max:9',
                'logradouro' => 'nullable|string|max:255',
                'numero' => 'nullable|string|max:20',
                'complemento' => 'nullable|string|max:255',
                'bairro' => 'nullable|string|max:100',
                'cidade' => 'nullable|string|max:100',
                'estado' => 'nullable|string|max:2',
            ]);

            // 🔐 Criptografa a senha com bcrypt
            $validated['senha'] = Hash::make($validated['senha']);
            unset($validated['senha_confirmation']);

            // 🏠 Monta endereço completo
            $validated['endereco'] = $this->montarEndereco($validated);

            DB::table('usuarios')->insert($validated);

            return redirect()->route('usuarios.index')
                ->with('success', 'Usuário cadastrado com sucesso!');
        } catch (\Throwable $e) {
            Log::error('Erro ao cadastrar usuário: ' . $e->getMessage());
            return back()->with('error', 'Erro ao cadastrar usuário.');
        }
    }

    public function edit($id)
    {
        $usuario = DB::table('usuarios')->where('id_usuario', $id)->first();
        if (!$usuario) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Usuário não encontrado.');
        }

        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        try {
            $usuario = DB::table('usuarios')->where('id_usuario', $id)->first();
            if (!$usuario) {
                return redirect()->route('usuarios.index')
                    ->with('error', 'Usuário não encontrado.');
            }

            $data = $request->validate([
                'nome' => 'required|string|max:100',
                'cpf' => 'nullable|string|max:14|unique:usuarios,cpf,' . $id . ',id_usuario',
                'email' => 'required|email|max:100|unique:usuarios,email,' . $id . ',id_usuario',
                'telefone' => 'nullable|string|max:20',
                'tipo' => 'required|in:admin,super-admin',
                'cep' => 'nullable|string|max:9',
                'logradouro' => 'nullable|string|max:255',
                'numero' => 'nullable|string|max:20',
                'complemento' => 'nullable|string|max:255',
                'bairro' => 'nullable|string|max:100',
                'cidade' => 'nullable|string|max:100',
                'estado' => 'nullable|string|max:2'
            ]);

            // ⚙️ Permite alteração de senha apenas para super-admin logado
            $userLogado = auth()->user();
            if ($request->filled('senha')) {
                if ($userLogado && $userLogado->tipo === 'super-admin') {
                    $request->validate([
                        'senha' => 'required|string|min:6|confirmed',
                    ]);
                    $data['senha'] = Hash::make($request->senha);
                } else {
                    return back()->with('error', 'Apenas Super Admins podem alterar senhas.');
                }
            }

            unset($data['senha_confirmation']);
            $data['endereco'] = $this->montarEndereco($data);

            DB::table('usuarios')->where('id_usuario', $id)->update($data);

            return redirect()->route('usuarios.index')
                ->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());
            return back()->with('error', 'Erro ao atualizar usuário.');
        }
    }

    public function destroy($id)
    {
        DB::table('usuarios')->where('id_usuario', $id)->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }

    private function montarEndereco($data)
    {
        $endereco = $data['logradouro'] ?? '';

        if (!empty($data['numero'])) {
            $endereco .= ', ' . $data['numero'];
        }
        if (!empty($data['complemento'])) {
            $endereco .= ' - ' . $data['complemento'];
        }
        if (!empty($data['bairro'])) {
            $endereco .= ' - ' . $data['bairro'];
        }
        if (!empty($data['cidade']) && !empty($data['estado'])) {
            $endereco .= ' - ' . $data['cidade'] . '/' . strtoupper($data['estado']);
        }
        if (!empty($data['cep'])) {
            $endereco .= ' - CEP: ' . $data['cep'];
        }

        return $endereco ?: null;
    }
}
