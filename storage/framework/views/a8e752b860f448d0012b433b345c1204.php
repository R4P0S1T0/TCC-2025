<?php $__env->startSection('title', 'Clientes'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-8">
    
    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-100 text-green-800 border border-green-200 px-4 py-3 rounded-lg text-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">Clientes</h1>
        <a href="<?php echo e(route('clientes.create')); ?>" 
           class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            + Novo Cliente
        </a>
    </div>

    
    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm bg-white">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold border-b">
                <tr>
                    <th class="px-5 py-3 text-left">Nome</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Telefone</th>
                    <th class="px-5 py-3 text-left">Cidade</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">
                            <?php echo e($cliente->nome); ?>

                        </td>
                        <td class="px-5 py-3"><?php echo e($cliente->email ?? '—'); ?></td>
                        <td class="px-5 py-3"><?php echo e($cliente->telefone ?? '—'); ?></td>
                        <td class="px-5 py-3"><?php echo e($cliente->cidade ?? '—'); ?></td>
                        <td class="px-5 py-3">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                <?php echo e($cliente->status === 'ativo' 
                                    ? 'bg-green-100 text-green-700' 
                                    : 'bg-gray-200 text-gray-600'); ?>">
                                <?php echo e(ucfirst($cliente->status)); ?>

                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            
                            <a href="<?php echo e(route('clientes.show', $cliente->id_cliente)); ?>" 
                               class="text-gray-700 hover:text-gray-900 hover:underline">
                                Ver
                            </a>

                            
                            <a href="<?php echo e(route('clientes.edit', $cliente->id_cliente)); ?>" 
                               class="text-blue-600 hover:text-blue-800 hover:underline">
                                Editar
                            </a>

                            
                            <form action="<?php echo e(route('clientes.destroy', $cliente->id_cliente)); ?>" 
                                  method="POST" 
                                  class="inline-block"
                                  onsubmit="return confirm('Deseja realmente excluir este cliente?')">
                                <?php echo csrf_field(); ?> 
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-800 hover:underline">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-5 py-6 text-center text-gray-500">
                            Nenhum cliente cadastrado.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($clientes->hasPages()): ?>
        <div class="mt-6">
            <?php echo e($clientes->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/clientes/index.blade.php ENDPATH**/ ?>