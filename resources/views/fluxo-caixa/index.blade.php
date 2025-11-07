@extends('layouts.app')

@section('title', 'Fluxo de Caixa')

@section('content')
<div class="space-y-6">
    {{-- ✅ Cabeçalho --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Fluxo de Caixa</h1>
        <div class="flex space-x-2">
            <a href="{{ route('fluxo-caixa.export.pdf', ['periodo' => request('periodo')]) }}" 
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </a>
            <a href="{{ route('fluxo-caixa.export.excel', ['periodo' => request('periodo')]) }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                <i class="fas fa-file-csv mr-2"></i>CSV
            </a>
        </div>
    </div>

    {{-- ✅ Filtros de Período --}}
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-4">
        <form method="GET" action="{{ route('fluxo-caixa.index') }}" class="flex items-center gap-3">
            <label class="text-gray-700 font-medium">Período:</label>
            <select name="periodo" onchange="this.form.submit()" 
                    class="border border-gray-300 rounded-lg text-gray-700 px-3 py-2 focus:ring focus:ring-blue-200">
                <option value="diario" {{ request('periodo') === 'diario' ? 'selected' : '' }}>Diário</option>
                <option value="semanal" {{ request('periodo') === 'semanal' ? 'selected' : '' }}>Semanal</option>
                <option value="mensal" {{ request('periodo') === 'mensal' ? 'selected' : '' }}>Mensal</option>
                <option value="anual" {{ request('periodo') === 'anual' ? 'selected' : '' }}>Anual</option>
            </select>
        </form>

        <p class="text-sm text-gray-500 italic">
            Atualizado em {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    {{-- ✅ Resumo Geral --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-green-100 border border-green-300 rounded-lg p-4 text-center">
            <p class="text-green-700 font-semibold">Entradas</p>
            <h2 class="text-2xl font-bold text-green-800">
                R$ {{ number_format($entradas, 2, ',', '.') }}
            </h2>
        </div>

        <div class="bg-red-100 border border-red-300 rounded-lg p-4 text-center">
            <p class="text-red-700 font-semibold">Saídas</p>
            <h2 class="text-2xl font-bold text-red-800">
                R$ {{ number_format($saidas, 2, ',', '.') }}
            </h2>
        </div>

        <div class="bg-blue-100 border border-blue-300 rounded-lg p-4 text-center">
            <p class="text-blue-700 font-semibold">
                Saldo {{ ucfirst(request('periodo') ?? 'semanal') }}
            </p>
            <h2 class="text-2xl font-bold {{ $saldo >= 0 ? 'text-blue-800' : 'text-red-800' }}">
                R$ {{ number_format($saldo, 2, ',', '.') }}
            </h2>
        </div>
    </div>

    {{-- ✅ Extrato Detalhado --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            @switch(request('periodo'))
                @case('diario') Extrato Diário @break
                @case('mensal') Extrato Mensal @break
                @case('anual') Extrato Anual @break
                @default Extrato Semanal
            @endswitch
        </h3>

        <table class="w-full border-collapse text-sm md:text-base">
            <thead>
                <tr class="bg-gray-100 text-left text-gray-600">
                    <th class="py-2 px-4">Data</th>
                    <th class="py-2 px-4 text-right text-green-600">Entradas</th>
                    <th class="py-2 px-4 text-right text-red-600">Saídas</th>
                    <th class="py-2 px-4 text-right text-blue-600">Saldo do Dia</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transacoes as $t)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="py-2 px-4">{{ \Carbon\Carbon::parse($t->data)->format('d/m/Y') }}</td>
                        <td class="py-2 px-4 text-right text-green-700 font-medium">
                            R$ {{ number_format($t->entradas, 2, ',', '.') }}
                        </td>
                        <td class="py-2 px-4 text-right text-red-700 font-medium">
                            R$ {{ number_format($t->saidas, 2, ',', '.') }}
                        </td>
                        <td class="py-2 px-4 text-right font-semibold {{ $t->saldo >= 0 ? 'text-blue-800' : 'text-red-800' }}">
                            R$ {{ number_format($t->saldo, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-500">
                            Nenhum movimento registrado neste período.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ Análises Avançadas --}}
    @if(request('periodo') === 'mensal' || request('periodo') === 'anual')
    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 mt-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Análise {{ request('periodo') === 'mensal' ? 'Mensal' : 'Anual' }} Consolidada
        </h3>

        <ul class="list-disc pl-5 text-gray-700 space-y-2">
            @if(request('periodo') === 'mensal')
                <li>Média de entrada diária: <strong>R$ {{ number_format($mediaEntradasDiaria, 2, ',', '.') }}</strong></li>
                <li>Média de saída diária: <strong>R$ {{ number_format($mediaSaidasDiaria, 2, ',', '.') }}</strong></li>
                <li>Total de dias positivos: <strong>{{ $diasPositivos }}</strong></li>
                <li>Total de dias negativos: <strong>{{ $diasNegativos }}</strong></li>
                <li>Saldo acumulado do mês: 
                    <strong class="{{ $saldoMensal >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        R$ {{ number_format($saldoMensal, 2, ',', '.') }}
                    </strong>
                </li>
            @else
                <li>Média de entrada mensal: <strong>R$ {{ number_format($mediaEntradasMensal, 2, ',', '.') }}</strong></li>
                <li>Média de saída mensal: <strong>R$ {{ number_format($mediaSaidasMensal, 2, ',', '.') }}</strong></li>
                <li>Total de meses positivos: <strong>{{ $mesesPositivos }}</strong></li>
                <li>Total de meses negativos: <strong>{{ $mesesNegativos }}</strong></li>
                <li>Saldo acumulado do ano: 
                    <strong class="{{ $saldoAnual >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        R$ {{ number_format($saldoAnual, 2, ',', '.') }}
                    </strong>
                </li>
            @endif
        </ul>
    </div>
    @endif
</div>
@endsection
