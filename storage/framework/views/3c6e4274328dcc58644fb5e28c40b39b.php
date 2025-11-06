<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">

    
    <div class="bg-gradient-to-r from-[#4E2A8C] via-[#5A36A8] to-[#3B1F72] rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 relative z-10">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Bem-vindo de volta, <?php echo e(auth()->user()->nome); ?> 👋
                </h1>
                <p class="text-[#D6CFF7] mt-2 text-sm md:text-base">
                    Aqui está o resumo atualizado das finanças e operações da sua empresa.
                </p>
            </div>

            <div class="flex items-center gap-3 bg-white/10 px-4 py-2 rounded-xl backdrop-blur-md border border-white/20">
                <i class="fas fa-calendar-day text-white/90 text-lg"></i>
                <span class="text-sm font-medium text-white/90">
                    <?php echo e(\Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y')); ?>

                </span>
            </div>
        </div>

        
        <div class="absolute right-4 bottom-2 text-white/10 text-[8rem] md:text-[10rem] pointer-events-none select-none">
            <i class="fas fa-chart-pie"></i>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        
        <div class="bg-white border border-[#E5D4FF] rounded-xl shadow-sm p-6 hover:shadow-md hover:border-[#4E2A8C]/50 transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-[#EDE9FE] text-[#4E2A8C] rounded-full shadow-sm">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Entradas</p>
                    <p class="text-2xl font-bold text-[#3B1F72]">
                        R$ <?php echo e(number_format($aReceber, 2, ',', '.')); ?>

                    </p>
                </div>
            </div>
        </div>

        
        <div class="bg-white border border-[#E5D4FF] rounded-xl shadow-sm p-6 hover:shadow-md hover:border-[#4E2A8C]/50 transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-[#F3E8FF] text-[#4E2A8C] rounded-full shadow-sm">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Saídas</p>
                    <p class="text-2xl font-bold text-[#3B1F72]">
                        R$ <?php echo e(number_format($aPagar, 2, ',', '.')); ?>

                    </p>
                </div>
            </div>
        </div>

        
        <div class="bg-white border border-[#E5D4FF] rounded-xl shadow-sm p-6 hover:shadow-md hover:border-[#4E2A8C]/50 transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-[#EDE9FE] text-[#4E2A8C] rounded-full shadow-sm">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Agendamentos Hoje</p>
                    <p class="text-2xl font-bold text-[#3B1F72]"><?php echo e($agendamentosHoje); ?></p>
                </div>
            </div>
        </div>

        
        <div class="bg-gradient-to-br from-[#4E2A8C] to-[#3B1F72] rounded-xl shadow-md p-6 text-white hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white/20 text-white rounded-full">
                    <i class="fas fa-piggy-bank text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-200">Saldo Atual</p>
                    <p class="text-2xl font-bold">
                        R$ <?php echo e(number_format($saldoAtual, 2, ',', '.')); ?>

                    </p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        
        <div class="bg-white border border-[#E5D4FF] rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#3B1F72]">Fluxo Financeiro</h2>
            </div>
            <div class="relative w-full aspect-[16/9]">
                <canvas id="financeChart" class="w-full h-full"></canvas>
            </div>
        </div>

        
        <div class="bg-white border border-[#E5D4FF] rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <h2 class="text-lg font-semibold text-[#3B1F72] mb-4">Resumo de Agendamentos</h2>
            <div class="relative w-full aspect-[16/9]">
                <canvas id="agendamentosChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo e(asset('js/dashboard.js')); ?>"></script>

<script>
    window.dashboardData = {
        aReceber: <?php echo e($aReceber); ?>,
        aPagar: <?php echo e($aPagar); ?>,
        agendamentosHoje: <?php echo e($agendamentosHoje); ?>

    };
</script>

<script>
    // Mantém os gráficos do mesmo tamanho e responsivos
    window.addEventListener('resize', () => {
        Object.values(Chart.instances).forEach(chart => chart.resize());
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/dashboard/index.blade.php ENDPATH**/ ?>