@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="space-y-6">
    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Editar Usuário</h1>
        <a href="{{ route('usuarios.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    {{-- Formulário --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('usuarios.update', $usuario->id_usuario) }}" method="POST">
            @csrf
            @method('PUT')

            @php
                $userLogado = Auth::user();
                $isSuperAdmin = $userLogado && $userLogado->tipo === 'super-admin';
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                    <input type="text" name="nome" value="{{ old('nome', $usuario->nome) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- E-mail --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Telefone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="telefone" id="telefone"
                           value="{{ old('telefone', $usuario->telefone) }}"
                           maxlength="15"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="(19) 99999-9999">
                </div>

                {{-- Tipo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Usuário *</label>
                    <select name="tipo" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            {{ !$isSuperAdmin ? 'disabled' : '' }}>
                        <option value="admin" {{ $usuario->tipo === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super-admin" {{ $usuario->tipo === 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    {{-- Para manter o valor ao enviar --}}
                    @unless($isSuperAdmin)
                        <input type="hidden" name="tipo" value="{{ $usuario->tipo }}">
                    @endunless
                </div>

                {{-- CPF --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
                    <input type="text" name="cpf" id="cpf"
                           value="{{ old('cpf', $usuario->cpf) }}"
                           maxlength="14"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="000.000.000-00">
                </div>

                {{-- CEP --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                    <input type="text" name="cep" id="cep"
                           value="{{ old('cep', $usuario->cep) }}"
                           maxlength="9"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="00000-000">
                </div>

                {{-- Logradouro --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                    <input type="text" name="logradouro" value="{{ old('logradouro', $usuario->logradouro) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Número --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                    <input type="text" name="numero" value="{{ old('numero', $usuario->numero) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Complemento --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                    <input type="text" name="complemento" value="{{ old('complemento', $usuario->complemento) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Bairro --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                    <input type="text" name="bairro" value="{{ old('bairro', $usuario->bairro) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Cidade --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                    <input type="text" name="cidade" value="{{ old('cidade', $usuario->cidade) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <input type="text" name="estado" maxlength="2" value="{{ old('estado', $usuario->estado) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 uppercase focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="SP">
                </div>

                {{-- 🔐 Campos de senha — visíveis apenas para Super Admin --}}
                @if($isSuperAdmin)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                        <input type="password" name="senha" id="senha" placeholder="Deixe em branco para manter a atual"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha</label>
                        <input type="password" name="senha_confirmation" id="senha_confirmation"
                               placeholder="Repita a nova senha"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                @endif
            </div>

            {{-- Botões --}}
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('usuarios.index') }}" 
                   class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Máscaras dinâmicas --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cpfInput = document.getElementById('cpf');
    const telInput = document.getElementById('telefone');
    const cepInput = document.getElementById('cep');

    const masks = {
        telefone: value => value
            .replace(/\D/g, '')
            .replace(/^(\d{2})(\d)/g, '($1) $2')
            .replace(/(\d{4,5})(\d{4})$/, '$1-$2'),
        cpf: value => {
            value = value.replace(/\D/g, '');
            if (value.length <= 3) return value;
            if (value.length <= 6) return value.replace(/(\d{3})(\d+)/, '$1.$2');
            if (value.length <= 9) return value.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
            return value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2}).*/, '$1.$2.$3-$4');
        },
        cep: value => value.replace(/\D/g, '').replace(/^(\d{5})(\d)/, '$1-$2')
    };

    if (telInput) telInput.addEventListener('input', e => e.target.value = masks.telefone(e.target.value));
    if (cpfInput) cpfInput.addEventListener('input', e => e.target.value = masks.cpf(e.target.value));
    if (cepInput) cepInput.addEventListener('input', e => e.target.value = masks.cep(e.target.value));
});
</script>
@endsection
