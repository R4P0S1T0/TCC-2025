<?php $__env->startSection('title', 'Compras'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Compras</h1>
        <a href="<?php echo e(route('compras.create')); ?>" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Nova Compra
        </a>
    </div>

    
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <strong class="font-bold">Sucesso!</strong>
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <strong class="font-bold">Erro!</strong>
            <span class="block sm:inline"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fornecedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800 whitespace-nowrap">
                                #<?php echo e($compra->id_compra); ?>

                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($compra->descricao); ?></div>
                                <?php if($compra->observacoes): ?>
                                    <div class="text-sm text-gray-500"><?php echo e(Str::limit($compra->observacoes, 50)); ?></div>
                                <?php endif; ?>
                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($compra->tipo == 'produto'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Produto
                                    </span>
                                <?php elseif($compra->tipo == 'serviço'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Serviço
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                        -
                                    </span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($compra->fornecedor_nome ?? 'N/A'); ?>

                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                R$ <?php echo e(number_format($compra->valor_total, 2, ',', '.')); ?>

                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e(\Carbon\Carbon::parse($compra->data_compra)->format('d/m/Y')); ?>

                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($compra->status == 'finalizada'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Finalizada
                                    </span>
                                <?php elseif($compra->status == 'cancelada'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Cancelada
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pendente
                                    </span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <?php if($compra->status == 'pendente'): ?>
                                        <form action="<?php echo e(route('compras.finalizar', $compra->id_compra)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-green-600 hover:text-green-900" 
                                                    onclick="return confirm('Finalizar esta compra?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>

                                        <form action="<?php echo e(route('compras.cancelar', $compra->id_compra)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-gray-600 hover:text-gray-900"
                                                    onclick="return confirm('Cancelar esta compra?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <a href="<?php echo e(route('compras.show', $compra->id_compra)); ?>" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="<?php echo e(route('compras.edit', $compra->id_compra)); ?>" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="<?php echo e(route('compras.destroy', $compra->id_compra)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Tem certeza que deseja excluir esta compra?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma compra encontrada.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/compras/index.blade.php ENDPATH**/ ?>