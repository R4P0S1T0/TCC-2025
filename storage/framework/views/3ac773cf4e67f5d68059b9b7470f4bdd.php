<?php $__env->startSection('title', 'Fluxo de Caixa'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Fluxo de Caixa</h1>
        <div class="flex space-x-2">
            <a href="<?php echo e(route('fluxo-caixa.export.pdf')); ?>" 
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </a>
            <a href="<?php echo e(route('fluxo-caixa.export.excel')); ?>" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                <i class="fas fa-file-csv mr-2"></i>CSV
            </a>
        </div>
    </div>

    <!-- Resumo da Semana -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-green-100 border border-green-300 rounded-lg p-4 text-center">
            <p class="text-green-700 font-semibold">Entradas</p>
            <h2 class="text-2xl font-bold text-green-800">R$ <?php echo e(number_format($entradas, 2, ',', '.')); ?></h2>
        </div>
        <div class="bg-red-100 border border-red-300 rounded-lg p-4 text-center">
            <p class="text-red-700 font-semibold">Saídas</p>
            <h2 class="text-2xl font-bold text-red-800">R$ <?php echo e(number_format($saidas, 2, ',', '.')); ?></h2>
        </div>
        <div class="bg-blue-100 border border-blue-300 rounded-lg p-4 text-center">
            <p class="text-blue-700 font-semibold">Saldo da Semana</p>
            <h2 class="text-2xl font-bold text-blue-800">R$ <?php echo e(number_format($saldo, 2, ',', '.')); ?></h2>
        </div>
    </div>

    <!-- Extrato Diário -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Extrato Diário da Semana</h3>
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
                <?php $__empty_1 = true; $__currentLoopData = $transacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="py-2 px-4"><?php echo e(\Carbon\Carbon::parse($t->data)->format('d/m/Y')); ?></td>
                        <td class="py-2 px-4 text-right text-green-700 font-medium">
                            R$ <?php echo e(number_format($t->entradas, 2, ',', '.')); ?>

                        </td>
                        <td class="py-2 px-4 text-right text-red-700 font-medium">
                            R$ <?php echo e(number_format($t->saidas, 2, ',', '.')); ?>

                        </td>
                        <td class="py-2 px-4 text-right font-semibold <?php echo e($t->saldo >= 0 ? 'text-blue-800' : 'text-red-800'); ?>">
                            R$ <?php echo e(number_format($t->saldo, 2, ',', '.')); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-500">
                            Nenhum movimento registrado nesta semana.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/fluxo-caixa/index.blade.php ENDPATH**/ ?>