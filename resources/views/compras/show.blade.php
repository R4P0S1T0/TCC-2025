@extends('layouts.app')

@section('title', 'Detalhes da Compra')

@section('content')
<div class="p-6 space-y-6">
    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">
            Detalhes da Compra #{{ $compra->id_compra }}
        </h1>

        <a href="{{ route('compras.index') }}"
           class="px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    {{-- Card de informações --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
        {{-- Descrição --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Descrição</h2>
            <p class="text-gray-800 text-lg font-medium">
                {{ $compra->descricao ?? '—' }}
            </p>
        </div>

        {{-- Fornecedor --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Fornecedor</h2>
            <p class="text-gray-800">
                {{ $compra->fornecedor->nome ?? 'Não informado' }}
            </p>
        </div>

        {{-- Tipo (produto/serviço) --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Tipo</h2>
            @if(!empty($compra->tipo))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $compra->tipo === 'produto' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                    {{ ucfirst($compra->tipo) }}
                </span>
            @else
                <span class="text-gray-700">—</span>
            @endif
        </div>

        {{-- Valor total --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Valor Total</h2>
            <p class="text-gray-800 font-semibold text-lg">
                R$ {{ number_format($compra->valor_total ?? 0, 2, ',', '.') }}
            </p>
        </div>

        {{-- Nota fiscal --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Nº da Nota Fiscal</h2>
            <p class="text-gray-800">
                {{ $compra->nota_fiscal ?? '—' }}
            </p>
        </div>

        {{-- Data da compra --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Data da Compra</h2>
            <p class="text-gray-800">
                @if(!empty($compra->data_compra))
                    {{ \Carbon\Carbon::parse($compra->data_compra)->format('d/m/Y') }}
                @else
                    —
                @endif
            </p>
        </div>

        {{-- Status --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Status</h2>
            @php
                $cores = [
                    'pendente'   => 'bg-yellow-100 text-yellow-800',
                    'finalizada' => 'bg-green-100 text-green-800',
                    'cancelada'  => 'bg-gray-200 text-gray-700',
                ];
            @endphp
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $cores[$compra->status] ?? 'bg-gray-100 text-gray-700' }}">
                {{ ucfirst($compra->status ?? 'Desconhecido') }}
            </span>
        </div>

        {{-- Observações (opcional) --}}
        @if(!empty($compra->observacoes))
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Observações</h2>
            <p class="text-gray-800">{{ $compra->observacoes }}</p>
        </div>
        @endif
    </div>

    {{-- Botões de ação --}}
    <div class="flex gap-3">
        <a href="{{ route('compras.edit', $compra->id_compra) }}"
           class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
           <i class="fas fa-edit mr-2"></i>Editar
        </a>

        <form action="{{ route('compras.destroy', $compra->id_compra) }}" method="POST"
              onsubmit="return confirm('Tem certeza que deseja excluir esta compra?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                <i class="fas fa-trash mr-2"></i>Excluir
            </button>
        </form>
    </div>
</div>
@endsection
