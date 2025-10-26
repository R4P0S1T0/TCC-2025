@extends('layouts.app')
@section('title', 'Editar Compra')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">Editar Compra</h1>

        <a href="{{ route('compras.index') }}" 
           class="px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    {{-- Exibir erros --}}
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 p-3 text-red-800">
            <ul class="list-disc ml-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário --}}
    <form action="{{ route('compras.update', $compra->id_compra) }}" method="POST" id="form-compra">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            {{-- Descrição --}}
            <div>
                <label class="block text-sm font-medium mb-1">Descrição</label>
                <input type="text" name="descricao" 
                       value="{{ old('descricao', $compra->descricao) }}"
                       class="w-full rounded-lg border border-gray-300 p-2"
                       placeholder="Ex: Compra de equipamentos" required>
            </div>

            {{-- Fornecedor --}}
            <div>
                <label class="block text-sm font-medium mb-1">Fornecedor</label>
                <select name="id_fornecedor" class="w-full rounded-lg border border-gray-300 p-2" required>
                    @foreach($fornecedores as $f)
                        <option value="{{ $f->id_fornecedor }}" 
                            {{ $compra->id_fornecedor == $f->id_fornecedor ? 'selected' : '' }}>
                            {{ $f->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tipo --}}
            <div>
                <label class="block text-sm font-medium mb-1">Tipo</label>
                <select name="tipo" class="w-full rounded-lg border border-gray-300 p-2" required>
                    <option value="produto" {{ $compra->tipo == 'produto' ? 'selected' : '' }}>Produto</option>
                    <option value="servico" {{ $compra->tipo == 'servico' ? 'selected' : '' }}>Serviço</option>
                </select>
            </div>

            {{-- Valor --}}
            <div>
                <label class="block text-sm font-medium mb-1">Valor Total</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-600">R$</span>
                    <input type="text" name="valor_total" id="valor_total"
                           value="{{ number_format($compra->valor_total, 2, ',', '.') }}"
                           class="pl-8 w-full rounded-lg border border-gray-300 p-2"
                           placeholder="0,00" required>
                </div>
            </div>

            {{-- Nota Fiscal --}}
            <div>
                <label class="block text-sm font-medium mb-1">Nº da Nota Fiscal</label>
                <input type="text" name="nota_fiscal" id="nota_fiscal"
                       value="{{ old('nota_fiscal', $compra->nota_fiscal) }}"
                       class="w-full rounded-lg border border-gray-300 p-2"
                       placeholder="Ex: 0000/000000">
            </div>

            {{-- Data --}}
            <div>
                <label class="block text-sm font-medium mb-1">Data da Compra</label>
                <input type="text" name="data_compra" id="data_compra"
                       value="{{ old('data_compra', \Carbon\Carbon::parse($compra->data_compra)->format('d/m/Y')) }}"
                       class="w-full rounded-lg border border-gray-300 p-2" required>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 p-2" required>
                    <option value="pendente" {{ $compra->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="finalizada" {{ $compra->status == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                    <option value="cancelada" {{ $compra->status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>

            {{-- Observações --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Observações</label>
                <textarea name="observacoes" rows="3"
                          class="w-full rounded-lg border border-gray-300 p-2"
                          placeholder="Observações adicionais...">{{ old('observacoes', $compra->observacoes) }}</textarea>
            </div>
        </div>

        {{-- Botões --}}
        <div class="flex items-center gap-3">
            <button type="submit" 
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Salvar Alterações
            </button>

            <a href="{{ route('compras.index') }}" 
               class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>

{{-- Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('#valor_total').on('input', function() {
        let val = $(this).val().replace(/\D/g, '');
        if (val === '') val = '0';
        val = (parseInt(val) / 100).toFixed(2) + '';
        val = val.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        $(this).val(val);
    });

    $('#nota_fiscal').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9/]/g, '').slice(0, 10));
    });

    $('#data_compra').on('input', function() {
        let val = $(this).val().replace(/\D/g, '').slice(0,8);
        if (val.length >= 5)
            val = val.replace(/(\d{2})(\d{2})(\d{0,4})/, '$1/$2/$3');
        else if (val.length >= 3)
            val = val.replace(/(\d{2})(\d{0,2})/, '$1/$2');
        $(this).val(val);
    });
});
</script>
@endsection
