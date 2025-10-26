@extends('layouts.app')
@section('title', 'Nova Compra')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-800">Nova Compra</h1>

            {{-- Botão Voltar (cinza) --}}
            <a href="{{ route('compras.index') }}"
                class="px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>
        </div>

        {{-- Exibir erros de validação --}}
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
        <form action="{{ route('compras.store') }}" method="POST" id="form-compra">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                {{-- Descrição --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Descrição</label>
                    <input type="text" name="descricao" placeholder="Ex: Compra de equipamentos de rede"
                        value="{{ old('descricao') }}" class="w-full rounded-lg border border-gray-300 p-2" required>
                </div>

                {{-- Fornecedor --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Fornecedor</label>
                    <select name="id_fornecedor" class="w-full rounded-lg border border-gray-300 p-2" required>
                        <option value="">Selecione...</option>
                        @foreach($fornecedores as $f)
                            <option value="{{ $f->id_fornecedor }}">{{ $f->nome }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo da Compra --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de Compra</label>
                    <select name="tipo" class="w-full rounded-lg border border-gray-300 p-2" required>
                        <option value="">Selecione...</option>
                        <option value="produto" {{ old('tipo') == 'produto' ? 'selected' : '' }}>Produto</option>
                        <option value="serviço" {{ old('tipo') == 'serviço' ? 'selected' : '' }}>Serviço</option>
                    </select>
                </div>

                {{-- Valor Total --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Valor Total</label>
                    <input type="text" name="valor_total" id="valor_total" placeholder="R$ 0,00"
                        value="{{ old('valor_total') }}" class="w-full rounded-lg border border-gray-300 p-2 text-right"
                        required>
                </div>

                {{-- Nota Fiscal --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Nº da Nota Fiscal</label>
                    <input type="text" name="nota_fiscal" id="nota_fiscal" placeholder="Ex: 0000/000000"
                        value="{{ old('nota_fiscal') }}"
                        class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Data da Compra --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Data da Compra</label>
                    <input type="text" name="data_compra" id="data_compra" placeholder="dd/mm/aaaa"
                        value="{{ old('data_compra') }}" class="w-full rounded-lg border border-gray-300 p-2" required>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 p-2" required>
                        <option value="pendente" {{ old('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="finalizada" {{ old('status') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="cancelada" {{ old('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                {{-- Observações --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Observações</label>
                    <textarea name="observacoes" rows="3" placeholder="Digite observações adicionais sobre a compra..."
                        class="w-full rounded-lg border border-gray-300 p-2">{{ old('observacoes') }}</textarea>
                </div>
            </div>

            {{-- Botões --}}
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Salvar
                </button>

                <a href="{{ route('compras.index') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    {{-- Scripts de máscara --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#valor_total').mask('R$ 000.000.000,00', { reverse: true });
            $('#nota_fiscal').mask('0000/000000');
            $('#data_compra').mask('00/00/0000');
        });
    </script>
@endsection