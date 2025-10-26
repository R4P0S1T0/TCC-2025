<?php $__env->startSection('title', 'Contas a Pagar'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    
    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg bg-green-100 p-3 text-green-800">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Contas a Pagar</h1>
        <a href="<?php echo e(route('contas-pagar.create')); ?>"
           class="px-4 py-2 rounded-lg bg-blue-700 text-white hover:bg-gray-600 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Nova Conta
        </a>
    </div>

    
    <form method="GET" class="mb-6 grid grid-cols-1 gap-3 md:grid-cols-3">
        <div>
            <label class="text-sm block mb-1 text-gray-700">Status</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 p-2">
                <option value="">Todos</option>
                <option value="pendente" <?php echo e(request('status') == 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                <option value="pago" <?php echo e(request('status') == 'pago' ? 'selected' : ''); ?>>Pago</option>
            </select>
        </div>

        <div>
            <label for="busca_compra" class="block text-sm font-medium mb-1 text-gray-700">
                Buscar Compra
            </label>
            <input type="text" id="busca_compra" name="busca_compra"
                   value="<?php echo e(request('busca_compra')); ?>"
                   placeholder="Ex: #12 ou 'equipamentos'"
                   class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
        </div>

        <div class="flex items-end">
            <button
                class="h-10 w-full md:w-auto px-4 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition">
                Filtrar
            </button>
        </div>
    </form>

    
    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-left font-semibold text-gray-700">
                <tr>
                    <th class="px-4 py-3">Compra</th>
                    <th class="px-4 py-3">Valor</th>
                    <th class="px-4 py-3">Nota Fiscal</th> 
                    <th class="px-4 py-3">Vencimento</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $contas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition">
                        
                        <td class="px-4 py-3">
                            <?php if($conta->compra): ?>
                                <span class="font-semibold text-gray-900">Compra #<?php echo e($conta->compra->id_compra); ?></span>
                                <div class="text-sm text-gray-500"><?php echo e($conta->compra->descricao ?? ''); ?></div>
                            <?php else: ?>
                                <span class="text-gray-400 italic">Sem compra vinculada</span>
                            <?php endif; ?>
                        </td>

                        
                        <td class="px-4 py-3 font-medium">
                            R$ <?php echo e(number_format($conta->valor, 2, ',', '.')); ?>

                        </td>

                        
                        <td class="px-4 py-3 text-gray-700">
                            <?php if($conta->nota_fiscal): ?>
                                <?php echo e($conta->nota_fiscal); ?>

                            <?php else: ?>
                                <span class="text-gray-400 italic">—</span>
                            <?php endif; ?>
                        </td>

                        
                        <td class="px-4 py-3">
                            <?php
                                try {
                                    $data = \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y');
                                } catch (\Exception $e) {
                                    $data = $conta->data_vencimento;
                                }
                            ?>
                            <?php echo e($data); ?>

                        </td>

                        
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                <?php echo e($conta->status === 'pago'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-yellow-100 text-yellow-800'); ?>">
                                <?php echo e(ucfirst($conta->status)); ?>

                            </span>
                        </td>

                        
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a href="<?php echo e(route('contas-pagar.show', $conta->id_cpagar)); ?>"
                                   class="px-3 py-1 rounded-md border border-gray-300 hover:bg-gray-100 transition">
                                    Ver
                                </a>

                                <a href="<?php echo e(route('contas-pagar.edit', $conta->id_cpagar)); ?>"
                                   class="px-3 py-1 rounded-md border border-blue-300 text-blue-700 hover:bg-blue-50 transition">
                                    Editar
                                </a>

                                <form method="POST" action="<?php echo e(route('contas-pagar.toggle-status', $conta->id_cpagar)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button class="px-3 py-1 rounded-md border transition
                                        <?php echo e($conta->status === 'pago'
                                            ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50'
                                            : 'border-green-300 text-green-700 hover:bg-green-50'); ?>">
                                        <?php echo e($conta->status === 'pago' ? 'Marcar Pendente' : 'Marcar Pago'); ?>

                                    </button>
                                </form>

                                <form method="POST" action="<?php echo e(route('contas-pagar.destroy', $conta->id_cpagar)); ?>"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta conta?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="px-3 py-1 rounded-md border border-red-300 text-red-700 hover:bg-red-50 transition">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            Nenhuma conta encontrada.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div class="mt-6 flex justify-center">
        <?php echo e($contas->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/contas-pagar/index.blade.php ENDPATH**/ ?>