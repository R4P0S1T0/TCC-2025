<?php $__env->startSection('title', 'Detalhes da Compra'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-6">
    
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">
            Detalhes da Compra #<?php echo e($compra->id_compra); ?>

        </h1>

        <a href="<?php echo e(route('compras.index')); ?>"
           class="px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
        
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Descrição</h2>
            <p class="text-gray-800 text-lg font-medium">
                <?php echo e($compra->descricao ?? '—'); ?>

            </p>
        </div>

        
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Fornecedor</h2>
            <p class="text-gray-800">
                <?php echo e($compra->fornecedor->nome ?? 'Não informado'); ?>

            </p>
        </div>

        
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Tipo</h2>
            <?php if(!empty($compra->tipo)): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    <?php echo e($compra->tipo === 'produto' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                    <?php echo e(ucfirst($compra->tipo)); ?>

                </span>
            <?php else: ?>
                <span class="text-gray-700">—</span>
            <?php endif; ?>
        </div>

        
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Valor Total</h2>
            <p class="text-gray-800 font-semibold text-lg">
                R$ <?php echo e(number_format($compra->valor_total ?? 0, 2, ',', '.')); ?>

            </p>
        </div>

        
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Nº da Nota Fiscal</h2>
            <p class="text-gray-800">
                <?php echo e($compra->nota_fiscal ?? '—'); ?>

            </p>
        </div>

        
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Data da Compra</h2>
            <p class="text-gray-800">
                <?php if(!empty($compra->data_compra)): ?>
                    <?php echo e(\Carbon\Carbon::parse($compra->data_compra)->format('d/m/Y')); ?>

                <?php else: ?>
                    —
                <?php endif; ?>
            </p>
        </div>

        
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Status</h2>
            <?php
                $cores = [
                    'pendente'   => 'bg-yellow-100 text-yellow-800',
                    'finalizada' => 'bg-green-100 text-green-800',
                    'cancelada'  => 'bg-gray-200 text-gray-700',
                ];
            ?>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium <?php echo e($cores[$compra->status] ?? 'bg-gray-100 text-gray-700'); ?>">
                <?php echo e(ucfirst($compra->status ?? 'Desconhecido')); ?>

            </span>
        </div>

        
        <?php if(!empty($compra->observacoes)): ?>
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase">Observações</h2>
            <p class="text-gray-800"><?php echo e($compra->observacoes); ?></p>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="flex gap-3">
        <a href="<?php echo e(route('compras.edit', $compra->id_compra)); ?>"
           class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
           <i class="fas fa-edit mr-2"></i>Editar
        </a>

        <form action="<?php echo e(route('compras.destroy', $compra->id_compra)); ?>" method="POST"
              onsubmit="return confirm('Tem certeza que deseja excluir esta compra?')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                <i class="fas fa-trash mr-2"></i>Excluir
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/compras/show.blade.php ENDPATH**/ ?>