<?php $__env->startSection('title', 'Contas a Receber'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-6 space-y-6">
    
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-800 text-center sm:text-left">Contas a Receber</h1>

        <a href="<?php echo e(route('contas-receber.create')); ?>"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 w-full sm:w-auto">
            <i class="fas fa-plus"></i> Nova Conta
        </a>
    </div>

    
    <form method="GET" action="<?php echo e(route('contas-receber.index')); ?>"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 bg-white p-4 rounded-lg shadow border border-gray-200">
        <!-- Status -->
        <div>
            <label class="text-sm block mb-1 text-gray-700">Status</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="pendente" <?php echo e(request('status') == 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                <option value="recebido" <?php echo e(request('status') == 'recebido' ? 'selected' : ''); ?>>Recebido</option>
                <option value="cancelado" <?php echo e(request('status') == 'cancelado' ? 'selected' : ''); ?>>Cancelado</option>
            </select>
        </div>

        <!-- Período -->
        <div class="flex gap-2">
            <div class="flex-1">
                <label class="text-sm block mb-1 text-gray-700">De</label>
                <input type="date" name="data_inicio" value="<?php echo e(request('data_inicio')); ?>"
                       class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <label class="text-sm block mb-1 text-gray-700">Até</label>
                <input type="date" name="data_fim" value="<?php echo e(request('data_fim')); ?>"
                       class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Busca -->
        <div>
            <label class="text-sm block mb-1 text-gray-700">Buscar</label>
            <input type="text" name="busca" value="<?php echo e(request('busca')); ?>" placeholder="Ex: cliente, descrição, #ID"
                   class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Botões -->
        <div class="flex items-end gap-2">
            <button
                class="flex-1 sm:flex-none px-4 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <?php if(request()->query()): ?>
                <a href="<?php echo e(route('contas-receber.index')); ?>"
                   class="flex-1 sm:flex-none px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition flex items-center justify-center gap-2">
                    <i class="fas fa-rotate-left"></i> Limpar
                </a>
            <?php endif; ?>
        </div>
    </form>

    
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <div class="bg-white shadow rounded-lg border border-gray-200 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Descrição</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Cliente</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Valor</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Vencimento</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-600">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $contas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3"><?php echo e($conta->descricao ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($conta->cliente_nome ?? '—'); ?></td>
                        <td class="px-4 py-3">R$ <?php echo e(number_format($conta->valor, 2, ',', '.')); ?></td>
                        <td class="px-4 py-3"><?php echo e(\Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y')); ?></td>
                        <td class="px-4 py-3">
                            <?php if($conta->status === 'recebido'): ?>
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Recebido</span>
                            <?php elseif($conta->status === 'cancelado'): ?>
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Cancelado</span>
                            <?php else: ?>
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendente</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 flex justify-center gap-3">
                            <a href="<?php echo e(route('contas-receber.show', $conta->id_creceber)); ?>"
                               class="text-blue-600 hover:text-blue-900"><i class="fas fa-eye"></i></a>
                            <a href="<?php echo e(route('contas-receber.edit', $conta->id_creceber)); ?>"
                               class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-edit"></i></a>
                            <form action="<?php echo e(route('contas-receber.destroy', $conta->id_creceber)); ?>" method="POST"
                                  onsubmit="return confirm('Excluir esta conta?')" class="inline-block">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">Nenhuma conta encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/contas-receber/index.blade.php ENDPATH**/ ?>