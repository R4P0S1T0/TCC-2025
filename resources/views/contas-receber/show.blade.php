@extends('layouts.app')

@section('title', 'Detalhes da Conta a Receber')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-sm rounded-xl border border-gray-200 p-8">
    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">Detalhes da Conta a Receber</h1>
    </div>

    @if(!$conta)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <strong>Erro:</strong> Nenhuma conta foi encontrada.
        </div>
    @else
        {{-- Seção Detalhes --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div>
                <p class="text-sm text-gray-500">ID</p>
                <p class="font-medium text-gray-900">{{ $conta->id_creceber }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Cliente</p>
                <p class="font-medium text-gray-900">{{ $conta->cliente_nome ?? '—' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Descrição</p>
                <p class="font-medium text-gray-900">{{ $conta->descricao ?? '—' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Valor</p>
                <p class="font-medium text-gray-900">
                    R$ {{ number_format($conta->valor, 2, ',', '.') }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Data de Vencimento</p>
                @php
                    try {
                        $data = \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y');
                    } catch (\Exception $e) {
                        $data = 'Data inválida';
                    }
                @endphp
                <p class="font-medium text-gray-900">{{ $data }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status</p>
                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    {{ $conta->status === 'recebido' 
                        ? 'bg-green-100 text-green-700' 
                        : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($conta->status) }}
                </span>
            </div>
        </div>

        {{-- Seção Observações --}}
        <div class="space-y-3">
            <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Observações</h2>
            <p class="text-gray-800 leading-relaxed">
                {!! $conta->observacoes ? nl2br(e($conta->observacoes)) : '— Nenhuma observação adicionada —' !!}
            </p>
        </div>

        {{-- Botões --}}
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-10">
            <a href="{{ route('contas-receber.index') }}" 
               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>

            <a href="{{ route('contas-receber.edit', $conta->id_creceber) }}"
               class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
        </div>
    @endif
</div>
@endsection
