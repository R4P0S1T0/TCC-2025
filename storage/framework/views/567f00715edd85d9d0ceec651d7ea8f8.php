<?php $__env->startSection('title', 'Detalhes do Cliente'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto bg-white shadow-sm rounded-xl border border-gray-200 p-8">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">Detalhes do Cliente</h1>
        <a href="<?php echo e(route('clientes.index')); ?>" 
           class="inline-flex items-center px-4 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition">
            ← Voltar
        </a>
    </div>

    
    <div class="space-y-4 mb-8">
        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Dados Pessoais</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Nome</p>
                <p class="font-medium text-gray-800"><?php echo e($cliente->nome); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">CPF / CNPJ</p>
                <p class="font-medium text-gray-800"><?php echo e($cliente->cpf_cnpj ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">E-mail</p>
                <p class="font-medium text-gray-800"><?php echo e($cliente->email ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Telefone</p>
                <p class="font-medium text-gray-800"><?php echo e($cliente->telefone ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status</p>
                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    <?php echo e($cliente->status === 'ativo' 
                        ? 'bg-green-100 text-green-700' 
                        : 'bg-gray-200 text-gray-600'); ?>">
                    <?php echo e(ucfirst($cliente->status)); ?>

                </span>
            </div>
        </div>
    </div>

    
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Endereço</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">CEP</p>
                <p class="font-medium text-gray-800"><?php echo e($cliente->cep ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Logradouro</p>
                <p class="font-medium text-gray-800"><?php echo e($cliente->endereco ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Número</p>
                <p class="font-medium text-gray-800"><?php echo e($cliente->numero ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Complemento</p>
                <p class="font-medium text-gray-800"><?php echo e($cliente->complemento ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Bairro</p>
                <p class="font-medium text-gray-800"><?php echo e($cliente->bairro ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Cidade / Estado</p>
                <p class="font-medium text-gray-800">
                    <?php echo e($cliente->cidade ?? '—'); ?> 
                    <?php if($cliente->estado): ?>
                        - <?php echo e(strtoupper($cliente->estado)); ?>

                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    
    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-10">
        <a href="<?php echo e(route('clientes.edit', $cliente->id_cliente)); ?>" 
           class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition">
            Editar
        </a>
        <form action="<?php echo e(route('clientes.destroy', $cliente->id_cliente)); ?>" 
              method="POST" 
              onsubmit="return confirm('Deseja excluir este cliente?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button class="px-4 py-2 rounded-lg border border-red-400 text-red-600 hover:bg-red-50 text-sm transition">
                Excluir
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/clientes/show.blade.php ENDPATH**/ ?>