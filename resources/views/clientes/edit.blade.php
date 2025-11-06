@extends('layouts.app')
@section('title', 'Editar Cliente')

@section('content')
<div class="space-y-6">
    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Editar Cliente</h1>
        <a href="{{ route('clientes.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    {{-- Mensagens de erro --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('clientes.update', $cliente->id_cliente) }}" id="clienteForm">
            @csrf
            @method('PUT')

            {{-- Dados principais --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                    <input type="text" name="nome" value="{{ old('nome', $cliente->nome) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Digite o nome completo">
                </div>

                {{-- CPF/CNPJ --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CPF / CNPJ *</label>
                    <input type="text" name="cpf_cnpj" id="cpf_cnpj" value="{{ old('cpf_cnpj', $cliente->cpf_cnpj) }}"
                        maxlength="18" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="000.000.000-00 ou 00.000.000/0000-00">
                    <p id="cpfCnpjMsg" class="text-xs text-gray-500 mt-1">Digite um CPF ou CNPJ válido.</p>
                </div>

                {{-- E-mail --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $cliente->email) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="usuario@email.com">
                </div>

                {{-- Telefone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="telefone" id="telefone" value="{{ old('telefone', $cliente->telefone) }}"
                        maxlength="15"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="(11) 99999-9999">
                </div>
            </div>

            {{-- Endereço --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                    <input type="text" id="cep" name="cep" maxlength="9" value="{{ old('cep', $cliente->cep) }}"
                        placeholder="00000-000"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <small id="cepStatus" class="text-xs text-gray-500 mt-1"></small>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                    <input type="text" id="endereco" name="endereco" value="{{ old('endereco', $cliente->endereco) }}"
                        placeholder="Rua, Avenida, etc."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                    <input type="text" name="numero" value="{{ old('numero', $cliente->numero) }}"
                        placeholder="123"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                    <input type="text" name="complemento" value="{{ old('complemento', $cliente->complemento) }}"
                        placeholder="Apto, sala, etc."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                    <input type="text" name="bairro" value="{{ old('bairro', $cliente->bairro) }}"
                        placeholder="Bairro"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                    <input type="text" id="cidade" name="cidade" value="{{ old('cidade', $cliente->cidade) }}"
                        placeholder="Cidade"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <input type="text" id="estado" name="estado" maxlength="2"
                        value="{{ old('estado', $cliente->estado) }}" placeholder="UF"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 uppercase 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="ativo" {{ old('status', $cliente->status) === 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ old('status', $cliente->status) === 'inativo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            </div>

            {{-- Botões --}}
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('clientes.index') }}" 
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" id="btnSalvar"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Máscaras e validações --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const telefone = document.getElementById('telefone');
    const cpfCnpj = document.getElementById('cpf_cnpj');
    const msg = document.getElementById('cpfCnpjMsg');
    const btn = document.getElementById('btnSalvar');
    const cep = document.getElementById('cep');
    const cepStatus = document.getElementById('cepStatus');

    // --- Máscara telefone ---
    telefone.addEventListener('input', () => {
        let v = telefone.value.replace(/\D/g, '');
        if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        else if (v.length > 5) v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        else v = v.replace(/^(\d*)/, '($1');
        telefone.value = v;
    });

    // --- Máscara e validação CPF/CNPJ ---
    cpfCnpj.addEventListener('input', () => {
        let v = cpfCnpj.value.replace(/\D/g, '');

        if (v.length <= 11) {
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            cpfCnpj.value = v;

            if (v.length === 14 && validarCPF(v)) {
                msg.textContent = '✔ CPF válido.';
                msg.className = 'text-xs text-green-600 mt-1';
                btn.disabled = false;
            } else if (v.length === 14) {
                msg.textContent = '✖ CPF inválido.';
                msg.className = 'text-xs text-red-600 mt-1';
                btn.disabled = true;
            } else {
                msg.textContent = 'Digite um CPF válido.';
                msg.className = 'text-xs text-gray-500 mt-1';
                btn.disabled = true;
            }
        } else {
            v = v.replace(/^(\d{2})(\d)/, '$1.$2');
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
            v = v.replace(/(\d{4})(\d)/, '$1-$2');
            cpfCnpj.value = v;

            if (v.length === 18 && validarCNPJ(v)) {
                msg.textContent = '✔ CNPJ válido.';
                msg.className = 'text-xs text-green-600 mt-1';
                btn.disabled = false;
            } else if (v.length === 18) {
                msg.textContent = '✖ CNPJ inválido.';
                msg.className = 'text-xs text-red-600 mt-1';
                btn.disabled = true;
            } else {
                msg.textContent = 'Digite um CNPJ válido.';
                msg.className = 'text-xs text-gray-500 mt-1';
                btn.disabled = true;
            }
        }
    });

    function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');
        if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
        let soma = 0;
        for (let i = 0; i < 9; i++) soma += parseInt(cpf[i]) * (10 - i);
        let resto = 11 - (soma % 11);
        if (resto >= 10) resto = 0;
        if (resto !== parseInt(cpf[9])) return false;
        soma = 0;
        for (let i = 0; i < 10; i++) soma += parseInt(cpf[i]) * (11 - i);
        resto = 11 - (soma % 11);
        if (resto >= 10) resto = 0;
        return resto === parseInt(cpf[10]);
    }

    function validarCNPJ(cnpj) {
        cnpj = cnpj.replace(/[^\d]+/g, '');
        if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;
        let tamanho = cnpj.length - 2;
        let numeros = cnpj.substring(0, tamanho);
        let digitos = cnpj.substring(tamanho);
        let soma = 0;
        let pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0)) return false;
        tamanho++;
        numeros = cnpj.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        return resultado == digitos.charAt(1);
    }

    // --- CEP automático ---
    cep.addEventListener('input', async () => {
        let v = cep.value.replace(/\D/g, '');
        if (v.length > 5) v = v.replace(/^(\d{5})(\d)/, '$1-$2');
        cep.value = v;

        if (v.length === 9) {
            cepStatus.textContent = 'Buscando endereço...';
            try {
                const res = await fetch(`https://viacep.com.br/ws/${v}/json/`);
                const data = await res.json();
                if (!data.erro) {
                    document.getElementById('endereco').value = data.logradouro || '';
                    document.getElementById('cidade').value = data.localidade || '';
                    document.getElementById('estado').value = data.uf || '';
                    cepStatus.textContent = '✔ Endereço encontrado.';
                    cepStatus.className = 'text-xs text-green-600 mt-1';
                } else {
                    cepStatus.textContent = 'CEP não encontrado.';
                    cepStatus.className = 'text-xs text-red-600 mt-1';
                }
            } catch {
                cepStatus.textContent = 'Erro ao consultar CEP.';
                cepStatus.className = 'text-xs text-red-600 mt-1';
            }
        } else cepStatus.textContent = '';
    });
});
</script>
@endsection
