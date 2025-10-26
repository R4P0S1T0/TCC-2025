<?php $__env->startSection('title', 'Usuários'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Usuários</h1>
        <a href="<?php echo e(route('usuarios.create')); ?>" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Novo Usuário
        </a>
    </div>

    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-white font-semibold">
                                            <?php echo e(strtoupper(substr($usuario->nome, 0, 1))); ?>

                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($usuario->nome); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($usuario->email); ?></div>
                                    </div>
                                </div>
                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($usuario->email); ?>

                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($usuario->telefone ?? '—'); ?>

                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    <?php echo e($usuario->tipo === 'super-admin' 
                                        ? 'bg-red-100 text-red-700' 
                                        : 'bg-purple-100 text-purple-800'); ?>">
                                    <?php echo e(ucfirst($usuario->tipo)); ?>

                                </span>
                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <a href="<?php echo e(route('usuarios.show', $usuario->id_usuario)); ?>" 
                                       class="text-gray-600 hover:text-gray-900" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="<?php echo e(route('usuarios.edit', $usuario->id_usuario)); ?>" 
                                       class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="<?php echo e(route('usuarios.destroy', $usuario->id_usuario)); ?>" method="POST" 
                                          onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')" class="inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-sm">
                                Nenhum usuário cadastrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/usuarios/index.blade.php ENDPATH**/ ?>