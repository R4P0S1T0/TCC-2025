@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    {{-- ✅ Cabeçalho --}}
    <div class="bg-gradient-to-r from-blue-600 to-purple-700 rounded-2xl p-8 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">Bem-vindo, {{ auth()->user()->nome }} 👋</h1>
                <p class="text-blue-100 mt-2">Resumo financeiro e operacional do seu negócio</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl opacity-25"></i>
            </div>
        </div>
    </div>

    {{-- ✅ Cards de métricas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- A Receber --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 text-green-600 rounded-full">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Entradas</p>
                    <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($aReceber, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- A Pagar --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-100 text-red-600 rounded-full">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Saidas</p>
                    <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($aPagar, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Agendamentos Hoje --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-full">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Agendamentos Hoje</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $agendamentosHoje }}</p>
                </div>
            </div>
        </div>

        {{-- Saldo Atual --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-100 text-purple-600 rounded-full">
                    <i class="fas fa-piggy-bank text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Saldo Atual</p>
                    <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($saldoAtual, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Gráfico Financeiro --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Fluxo Financeiro</h2>
        </div>
        <canvas id="financeChart" height="120"></canvas>
    </div>

    {{-- ✅ Agendamentos --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Resumo de Agendamentos</h2>
        <canvas id="agendamentosChart" height="20"></canvas>
    </div>
</div>

{{-- ✅ Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>

<script>
    window.dashboardData = {
        aReceber: {{ $aReceber }},
        aPagar: {{ $aPagar }},
        agendamentosHoje: {{ $agendamentosHoje }}
    };
</script>
@endsection
