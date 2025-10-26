@extends('layouts.app')
@section('title', 'Editar Cliente')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">Editar Cliente</h1>
        <a href="{{ route('clientes.index') }}"
           class="inline-flex items-center px-4 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition">
            ← Voltar
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 border border-red-200 bg-red-50 text-red-700 text-sm rounded-lg p-4">
            <ul class="list-disc pl-6">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Corrigido: rota com ID do cliente --}}
    <form action="{{ route('clientes.update', $cliente->id_cliente) }}" method="POST" class="space-y-10">
        @csrf
        @method('PUT')

        {{-- DADOS PESSOAIS --}}
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Dados Pessoais</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Nome Completo *</label>
                    <input type="text" name="nome" value="{{ old('nome', $cliente->nome) }}" required
                           placeholder="Digite o nome completo"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">CPF / CNPJ</label>
                    <input type="text" id="cpf_cnpj" name="cpf_cnpj"
                           value="{{ old('cpf_cnpj', $cliente->cpf_cnpj) }}"
                           placeholder="000.000.000-00"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $cliente->email) }}"
                           placeholder="usuario@email.com"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Telefone</label>
                    <input type="text" id="telefone" name="telefone"
                           value="{{ old('telefone', $cliente->telefone) }}"
                           placeholder="(11) 99999-9999"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                </div>
            </div>
        </div>

        {{-- ENDEREÇO --}}
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Endereço</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">CEP</label>
                    <input type="text" id="cep" name="cep" maxlength="9"
                           value="{{ old('cep', $cliente->cep) }}" placeholder="00000-000"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                    <small id="cep-status" class="text-gray-500 text-xs mt-1"></small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Logradouro</label>
                    <input type="text" id="endereco" name="endereco"
                           value="{{ old('endereco', $cliente->endereco) }}"
                           placeholder="Rua, Avenida, etc."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Número</label>
                    <input type="text" name="numero"
                           value="{{ old('numero', $cliente->numero) }}"
                           placeholder="123"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Complemento</label>
                    <input type="text" name="complemento"
                           value="{{ old('complemento', $cliente->complemento) }}"
                           placeholder="Apto, sala etc."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Bairro</label>
                    <input type="text" name="bairro"
                           value="{{ old('bairro', $cliente->bairro) }}"
                           placeholder="Bairro"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Cidade</label>
                    <input type="text" id="cidade" name="cidade"
                           value="{{ old('cidade', $cliente->cidade) }}"
                           placeholder="Cidade"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Estado</label>
                    <input type="text" id="estado" name="estado" maxlength="2"
                           value="{{ old('estado', $cliente->estado) }}" placeholder="UF"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 uppercase focus:ring-2 focus:ring-gray-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Status</label>
                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                        <option value="ativo" {{ old('status', $cliente->status) === 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ old('status', $cliente->status) === 'inativo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- BOTÕES --}}
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
            <a href="{{ route('clientes.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition text-sm">
                Cancelar
            </a>
            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition text-sm">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

{{-- Máscaras e consulta CEP --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tel = document.getElementById('telefone');
    tel?.addEventListener('input', () => {
        let v = tel.value.replace(/\D/g, '');
        if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        else if (v.length > 5) v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        else v = v.replace(/^(\d*)/, '($1');
        tel.value = v;
    });

    const cpfCnpj = document.getElementById('cpf_cnpj');
    cpfCnpj?.addEventListener('input', () => {
        let v = cpfCnpj.value.replace(/\D/g, '');
        if (v.length <= 11) {
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        } else {
            v = v.replace(/^(\d{2})(\d)/, '$1.$2');
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
            v = v.replace(/(\d{4})(\d)/, '$1-$2');
        }
        cpfCnpj.value = v;
    });

    const cep = document.getElementById('cep');
    const status = document.getElementById('cep-status');
    cep?.addEventListener('input', async () => {
        let v = cep.value.replace(/\D/g, '');
        if (v.length > 5) v = v.replace(/^(\d{5})(\d)/, '$1-$2');
        cep.value = v;

        if (v.length === 9) {
            status.textContent = 'Buscando endereço...';
            try {
                const res = await fetch(`https://viacep.com.br/ws/${v}/json/`);
                const data = await res.json();
                if (!data.erro) {
                    document.getElementById('endereco').value = data.logradouro || '';
                    document.getElementById('cidade').value = data.localidade || '';
                    document.getElementById('estado').value = data.uf || '';
                    status.textContent = 'Endereço encontrado.';
                } else status.textContent = 'CEP não encontrado.';
            } catch { status.textContent = 'Erro ao consultar CEP.'; }
        } else status.textContent = '';
    });
});
</script>
@endsection
