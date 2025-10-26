@extends('layouts.app')
@section('title', 'Detalhes - Conta a Pagar')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-semibold mb-6">
            Detalhes da Conta a Pagar #{{ $conta->id_cpagar }}
        </h1>

        {{-- Informações da conta --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Compra --}}
            <div class="rounded-lg border border-gray-200 p-4 bg-white shadow-sm">
                <p class="text-sm text-gray-500">Compra</p>
                <p class="font-medium text-gray-800 mt-1">
                    @if($conta->compra)
                        Compra #{{ $conta->compra->id_compra }}
                    @else
                        —
                    @endif
                </p>
            </div>

            {{-- Valor --}}
            <div class="rounded-lg border border-gray-200 p-4 bg-white shadow-sm">
                <p class="text-sm text-gray-500">Valor</p>
                <p class="font-medium text-gray-800 mt-1">
                    R$ {{ number_format($conta->valor, 2, ',', '.') }}
                </p>
            </div>

            {{-- Vencimento --}}
            <div class="rounded-lg border border-gray-200 p-4 bg-white shadow-sm">
                <p class="text-sm text-gray-500">Data de Vencimento</p>
                <p class="font-medium text-gray-800 mt-1">
                    @php
                        $data = $conta->data_vencimento;

                        try {
                            // Se vier no formato americano (YYYY-MM-DD)
                            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
                                $data = \Carbon\Carbon::createFromFormat('Y-m-d', $data)->format('d/m/Y');
                            }
                            // Se vier em BR, apenas confirma formato válido
                            elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data)) {
                                // Já está em formato correto
                            } else {
                                $data = 'Data inválida';
                            }
                        } catch (\Exception $e) {
                            $data = 'Erro ao converter';
                        }
                    @endphp

                    {{ $data }}
                </p>
            </div>


            {{-- Status --}}
            <div class="rounded-lg border border-gray-200 p-4 bg-white shadow-sm">
                <p class="text-sm text-gray-500">Status</p>
                <p class="mt-1">
                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                        {{ $conta->status === 'pago'
        ? 'bg-green-100 text-green-700'
        : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($conta->status) }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Botões de ação --}}
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('contas-pagar.edit', $conta->id_cpagar) }}"
                class="px-4 py-2 rounded-lg border border-blue-300 text-blue-700 hover:bg-blue-50 transition">
                Editar
            </a>

            <a href="{{ route('contas-pagar.index') }}"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                Voltar
            </a>
        </div>
    </div>
@endsection