<?php $__env->startSection('title', 'Detalhes do Usuário'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto bg-white shadow-sm rounded-xl border border-gray-200 p-8">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">Detalhes do Usuário</h1>
        <a href="<?php echo e(route('usuarios.index')); ?>" 
           class="inline-flex items-center px-4 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition">
            ← Voltar
        </a>
    </div>

    
    <div class="space-y-4 mb-8">
        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Informações Gerais</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Nome</p>
                <p class="font-medium text-gray-800"><?php echo e($usuario->nome); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">E-mail</p>
                <p class="font-medium text-gray-800"><?php echo e($usuario->email); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Tipo de Usuário</p>
                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    <?php echo e($usuario->tipo === 'admin' 
                        ? 'bg-purple-100 text-purple-700' 
                        : 'bg-blue-100 text-blue-700'); ?>">
                    <?php echo e(ucfirst($usuario->tipo)); ?>

                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Telefone</p>
                <p class="font-medium text-gray-800"><?php echo e($usuario->telefone ?? '—'); ?></p>
            </div>
        </div>
    </div>

    
    <div class="space-y-4 mb-8">
        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Endereço</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">CEP</p>
                <p class="font-medium text-gray-800"><?php echo e($usuario->cep ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Logradouro</p>
                <p class="font-medium text-gray-800"><?php echo e($usuario->logradouro ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Número</p>
                <p class="font-medium text-gray-800"><?php echo e($usuario->numero ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Complemento</p>
                <p class="font-medium text-gray-800"><?php echo e($usuario->complemento ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Bairro</p>
                <p class="font-medium text-gray-800"><?php echo e($usuario->bairro ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Cidade / Estado</p>
                <p class="font-medium text-gray-800">
                    <?php echo e($usuario->cidade ?? '—'); ?> 
                    <?php if($usuario->estado): ?>
                        - <?php echo e(strtoupper($usuario->estado)); ?>

                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    
    <div class="space-y-4 mb-8">
        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Informações Adicionais</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">CPF</p>
                <p class="font-medium text-gray-800"><?php echo e($usuario->cpf ?? '—'); ?></p>
            </div>
        </div>
    </div>

    
    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-10">
        <a href="<?php echo e(route('usuarios.edit', $usuario->id_usuario ?? $usuario->id)); ?>" 
           class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition">
            Editar
        </a>
        <form action="<?php echo e(route('usuarios.destroy', $usuario->id_usuario ?? $usuario->id)); ?>" 
              method="POST" 
              onsubmit="return confirm('Deseja excluir este usuário?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button class="px-4 py-2 rounded-lg border border-red-400 text-red-600 hover:bg-red-50 text-sm transition">
                Excluir
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/usuarios/show.blade.php ENDPATH**/ ?>