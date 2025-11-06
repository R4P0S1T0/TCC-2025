@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">

    {{-- ✅ Cabeçalho Moderno --}}
    <div class="bg-gradient-to-r from-[#4E2A8C] via-[#5A36A8] to-[#3B1F72] rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 relative z-10">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Bem-vindo de volta, {{ auth()->user()->nome }} 👋
                </h1>
                <p class="text-[#D6CFF7] mt-2 text-sm md:text-base">
                    Aqui está o resumo atualizado das finanças e operações da sua empresa.
                </p>
            </div>

            <div class="flex items-center gap-3 bg-white/10 px-4 py-2 rounded-xl backdrop-blur-md border border-white/20">
                <i class="fas fa-calendar-day text-white/90 text-lg"></i>
                <span class="text-sm font-medium text-white/90">
                    {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}
                </span>
            </div>
        </div>

        {{-- Ícone decorativo --}}
        <div class="absolute right-4 bottom-2 text-white/10 text-[8rem] md:text-[10rem] pointer-events-none select-none">
            <i class="fas fa-chart-pie"></i>
        </div>
    </div>

    {{-- ✅ Cards de métricas - Lart Digital Style --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Entradas --}}
        <div class="bg-white border border-[#E5D4FF] rounded-xl shadow-sm p-6 hover:shadow-md hover:border-[#4E2A8C]/50 transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-[#EDE9FE] text-[#4E2A8C] rounded-full shadow-sm">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Entradas</p>
                    <p class="text-2xl font-bold text-[#3B1F72]">
                        R$ {{ number_format($aReceber, 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Saídas --}}
        <div class="bg-white border border-[#E5D4FF] rounded-xl shadow-sm p-6 hover:shadow-md hover:border-[#4E2A8C]/50 transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-[#F3E8FF] text-[#4E2A8C] rounded-full shadow-sm">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Saídas</p>
                    <p class="text-2xl font-bold text-[#3B1F72]">
                        R$ {{ number_format($aPagar, 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Agendamentos Hoje --}}
        <div class="bg-white border border-[#E5D4FF] rounded-xl shadow-sm p-6 hover:shadow-md hover:border-[#4E2A8C]/50 transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-[#EDE9FE] text-[#4E2A8C] rounded-full shadow-sm">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Agendamentos Hoje</p>
                    <p class="text-2xl font-bold text-[#3B1F72]">{{ $agendamentosHoje }}</p>
                </div>
            </div>
        </div>

        {{-- Saldo Atual --}}
        <div class="bg-gradient-to-br from-[#4E2A8C] to-[#3B1F72] rounded-xl shadow-md p-6 text-white hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white/20 text-white rounded-full">
                    <i class="fas fa-piggy-bank text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-200">Saldo Atual</p>
                    <p class="text-2xl font-bold">
                        R$ {{ number_format($saldoAtual, 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Gráficos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Fluxo Financeiro --}}
        <div class="bg-white border border-[#E5D4FF] rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#3B1F72]">Fluxo Financeiro</h2>
            </div>
            <div class="relative w-full aspect-[16/9]">
                <canvas id="financeChart" class="w-full h-full"></canvas>
            </div>
        </div>

        {{-- Agendamentos --}}
        <div class="bg-white border border-[#E5D4FF] rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <h2 class="text-lg font-semibold text-[#3B1F72] mb-4">Resumo de Agendamentos</h2>
            <div class="relative w-full aspect-[16/9]">
                <canvas id="agendamentosChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>

<script>
    window.dashboardData = {
        aReceber: {{ $aReceber }},
        aPagar: {{ $aPagar }},
        agendamentosHoje: {{ $agendamentosHoje }}
    };
</script>

<script>
    // Mantém os gráficos do mesmo tamanho e responsivos
    window.addEventListener('resize', () => {
        Object.values(Chart.instances).forEach(chart => chart.resize());
    });
</script>
@endsection
