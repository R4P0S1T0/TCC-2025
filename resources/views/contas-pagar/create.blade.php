@extends('layouts.app')
@section('title', 'Nova Conta a Pagar')

@section('content')
<div class="p-6 space-y-6">
    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">Nova Conta a Pagar</h1>

        <a href="{{ route('contas-pagar.index') }}" 
           class="px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    {{-- Erros --}}
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
    <form action="{{ route('contas-pagar.store') }}" method="POST" id="form-conta">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            {{-- Compra --}}
            <div>
                <label class="block text-sm font-medium mb-1">Compra</label>
                <select name="id_compra" id="id_compra"
                        class="w-full rounded-lg border border-gray-300 p-2" required>
                    <option value="">Selecione...</option>
                    @foreach($compras as $c)
                        @if(!in_array($c->status, ['finalizada', 'cancelada']))
                            <option value="{{ $c->id_compra }}">
                                #{{ $c->id_compra }} — {{ Str::limit($c->descricao, 40) }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- Valor --}}
            <div>
                <label class="block text-sm font-medium mb-1">Valor</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-600">R$</span>
                    <input type="text" name="valor" id="valor"
                           placeholder="0,00"
                           value="{{ old('valor') }}"
                           class="pl-8 w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
            </div>

            {{-- Nota Fiscal --}}
            <div>
                <label class="block text-sm font-medium mb-1">Nº da Nota Fiscal</label>
                <input type="text" name="nota_fiscal" id="nota_fiscal"
                       placeholder="Ex: 0000/000000"
                       value="{{ old('nota_fiscal') }}"
                       class="w-full rounded-lg border border-gray-300 p-2">
            </div>

            {{-- Data de Vencimento --}}
            <div>
                <label class="block text-sm font-medium mb-1">Data de Vencimento</label>
                <input type="text" name="data_vencimento" id="data_vencimento"
                       placeholder="dd/mm/aaaa"
                       value="{{ old('data_vencimento') }}"
                       class="w-full rounded-lg border border-gray-300 p-2" required>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 p-2" required>
                    <option value="pendente" {{ old('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="pago" {{ old('status') == 'pago' ? 'selected' : '' }}>Pago</option>
                </select>
            </div>
        </div>

        {{-- Botões --}}
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Salvar
            </button>
            <a href="{{ route('contas-pagar.index') }}" 
               class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>

{{-- Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function() {
    // ✅ Máscaras seguras
    $('#valor').mask('000.000.000.000,00', { reverse: true });
    $('#nota_fiscal').mask('0000/000000');
    $('#data_vencimento').mask('00/00/0000');

    // ✅ Busca de compra
    $('#id_compra').on('change', function() {
        const id = $(this).val();
        if (!id) return;

        fetch(`/compras/dados/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Preenche automaticamente os campos
                $('#valor').val(data.valor_total);
                $('#nota_fiscal').val(data.nota_fiscal);

                if (data.data_compra) {
                    $('#data_vencimento').val(data.data_compra);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erro ao buscar dados da compra.');
            });
    });

    // ✅ Evita duplicação de "R$"
    $('#valor').on('focus', function() {
        $(this).val($(this).val().replace(/^R\$\s*/, ''));
    });
});
</script>
@endsection
