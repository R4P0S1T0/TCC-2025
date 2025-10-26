<?php $__env->startSection('title', 'Detalhes da Conta a Receber'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto bg-white shadow-sm rounded-xl border border-gray-200 p-8">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">Detalhes da Conta a Receber</h1>
    </div>

    <?php if(!$conta): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <strong>Erro:</strong> Nenhuma conta foi encontrada.
        </div>
    <?php else: ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div>
                <p class="text-sm text-gray-500">ID</p>
                <p class="font-medium text-gray-900"><?php echo e($conta->id_creceber); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Cliente</p>
                <p class="font-medium text-gray-900"><?php echo e($conta->cliente_nome ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Descrição</p>
                <p class="font-medium text-gray-900"><?php echo e($conta->descricao ?? '—'); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Valor</p>
                <p class="font-medium text-gray-900">
                    R$ <?php echo e(number_format($conta->valor, 2, ',', '.')); ?>

                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Data de Vencimento</p>
                <?php
                    try {
                        $data = \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y');
                    } catch (\Exception $e) {
                        $data = 'Data inválida';
                    }
                ?>
                <p class="font-medium text-gray-900"><?php echo e($data); ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status</p>
                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    <?php echo e($conta->status === 'recebido' 
                        ? 'bg-green-100 text-green-700' 
                        : 'bg-yellow-100 text-yellow-800'); ?>">
                    <?php echo e(ucfirst($conta->status)); ?>

                </span>
            </div>
        </div>

        
        <div class="space-y-3">
            <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Observações</h2>
            <p class="text-gray-800 leading-relaxed">
                <?php echo $conta->observacoes ? nl2br(e($conta->observacoes)) : '— Nenhuma observação adicionada —'; ?>

            </p>
        </div>

        
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-10">
            <a href="<?php echo e(route('contas-receber.index')); ?>" 
               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>

            <a href="<?php echo e(route('contas-receber.edit', $conta->id_creceber)); ?>"
               class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/contas-receber/show.blade.php ENDPATH**/ ?>