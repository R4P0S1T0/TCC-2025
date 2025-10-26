@extends('layouts.app')

@section('title', 'Nova Conta a Receber')

@section('content')
<div class="space-y-6">
    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Nova Conta a Receber</h1>
        <a href="{{ route('contas-receber.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    {{-- Formulário --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('contas-receber.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Cliente --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                    <select name="cliente_nome" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione um cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->nome }}">{{ $cliente->nome }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Descrição --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                    <input type="text" name="descricao" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ex: Venda de produtos">
                </div>

                {{-- Valor com formatação automática --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor *</label>
                    <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2">
                        <span class="text-gray-600 mr-2">R$</span>
                        <input type="text" name="valor" id="valor" required
                               class="flex-1 border-none outline-none text-right"
                               placeholder="0,00">
                    </div>
                </div>

                {{-- Data de Vencimento (pt-BR) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Vencimento *</label>
                    <input type="text" name="data_vencimento" id="data_vencimento" required
                           value="{{ now()->format('d/m/Y') }}"
                           maxlength="10"
                           placeholder="dd/mm/aaaa"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Observações --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="observacoes" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Observações adicionais..."></textarea>
                </div>
            </div>

            {{-- Botões --}}
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('contas-receber.index') }}" 
                   class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-hand-holding-usd mr-2"></i>Cadastrar Conta
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts de máscara --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Máscara de moeda
    const inputValor = document.getElementById('valor');
    inputValor.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2) + '';
        value = value.replace('.', ',');
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        this.value = value;
    });

    // Máscara de data (dd/mm/yyyy)
    const inputData = document.getElementById('data_vencimento');
    inputData.addEventListener('input', function(e) {
        let v = this.value.replace(/\D/g, '');
        if (v.length > 2 && v.length <= 4) {
            v = v.replace(/(\d{2})(\d+)/, '$1/$2');
        } else if (v.length > 4) {
            v = v.replace(/(\d{2})(\d{2})(\d{1,4})/, '$1/$2/$3');
        }
        this.value = v.slice(0, 10);
    });
});
</script>
@endsection
