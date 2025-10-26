@extends('layouts.app')
@section('title', 'Editar Conta a Pagar')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">
            Editar Conta a Pagar #{{ $conta->id_cpagar }}
        </h1>

        {{-- Botão Voltar --}}
        <a href="{{ route('contas-pagar.index') }}" 
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
    <form action="{{ route('contas-pagar.update', $conta->id_cpagar) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            {{-- Compra --}}
            <div>
                <label class="block text-sm font-medium mb-1">Compra</label>
                <select name="id_compra" class="w-full rounded-lg border border-gray-300 p-2" required>
                    <option value="">Selecione...</option>
                    @foreach($compras as $c)
                        <option value="{{ $c->id_compra }}" {{ old('id_compra', $conta->id_compra ?? null) == $c->id_compra ? 'selected' : '' }}>
                            #{{ $c->id_compra }} — {{ Str::limit($c->descricao, 40) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Valor --}}
            <div>
                <label class="block text-sm font-medium mb-1">Valor</label>
                <input type="text" name="valor" id="valor"
                       value="{{ old('valor', number_format($conta->valor, 2, ',', '.')) }}"
                       class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>

            {{-- Nota Fiscal --}}
            <div>
                <label class="block text-sm font-medium mb-1">Nº da Nota Fiscal</label>
                <input type="text" name="nota_fiscal" id="nota_fiscal"
                       value="{{ old('nota_fiscal', $conta->nota_fiscal ?? '') }}"
                       placeholder="Ex: 0000/000000"
                       class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Data de Vencimento --}}
            <div>
                <label class="block text-sm font-medium mb-1">Data de Vencimento</label>
                @php
                    $dataFormatada = $conta->data_vencimento;
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $conta->data_vencimento)) {
                        $dataFormatada = \Carbon\Carbon::createFromFormat('Y-m-d', $conta->data_vencimento)->format('d/m/Y');
                    }
                @endphp
                <input type="text" name="data_vencimento" id="data_vencimento"
                       value="{{ old('data_vencimento', $dataFormatada) }}"
                       class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 p-2">
                    <option value="pendente" {{ old('status', $conta->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="pago" {{ old('status', $conta->status) == 'pago' ? 'selected' : '' }}>Pago</option>
                </select>
            </div>
        </div>

        {{-- Botões --}}
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Atualizar
            </button>
            <a href="{{ route('contas-pagar.index') }}" 
               class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>

{{-- ✅ Máscaras --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function() {
    $('#valor').mask('000.000.000,00', {reverse: true});
    $('#nota_fiscal').mask('0000/000000');
    $('#data_vencimento').mask('00/00/0000');
});
</script>
@endsection
