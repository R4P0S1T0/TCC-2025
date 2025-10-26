<?php $__env->startSection('title', 'Editar Conta a Pagar'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">
            Editar Conta a Pagar #<?php echo e($conta->id_cpagar); ?>

        </h1>

        
        <a href="<?php echo e(route('contas-pagar.index')); ?>" 
           class="px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    
    <?php if($errors->any()): ?>
        <div class="mb-4 rounded-lg bg-red-100 p-3 text-red-800">
            <ul class="list-disc ml-6">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <form action="<?php echo e(route('contas-pagar.update', $conta->id_cpagar)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            
            <div>
                <label class="block text-sm font-medium mb-1">Compra</label>
                <select name="id_compra" class="w-full rounded-lg border border-gray-300 p-2" required>
                    <option value="">Selecione...</option>
                    <?php $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id_compra); ?>" <?php echo e(old('id_compra', $conta->id_compra ?? null) == $c->id_compra ? 'selected' : ''); ?>>
                            #<?php echo e($c->id_compra); ?> — <?php echo e(Str::limit($c->descricao, 40)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div>
                <label class="block text-sm font-medium mb-1">Valor</label>
                <input type="text" name="valor" id="valor"
                       value="<?php echo e(old('valor', number_format($conta->valor, 2, ',', '.'))); ?>"
                       class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>

            
            <div>
                <label class="block text-sm font-medium mb-1">Nº da Nota Fiscal</label>
                <input type="text" name="nota_fiscal" id="nota_fiscal"
                       value="<?php echo e(old('nota_fiscal', $conta->nota_fiscal ?? '')); ?>"
                       placeholder="Ex: 0000/000000"
                       class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            
            <div>
                <label class="block text-sm font-medium mb-1">Data de Vencimento</label>
                <?php
                    $dataFormatada = $conta->data_vencimento;
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $conta->data_vencimento)) {
                        $dataFormatada = \Carbon\Carbon::createFromFormat('Y-m-d', $conta->data_vencimento)->format('d/m/Y');
                    }
                ?>
                <input type="text" name="data_vencimento" id="data_vencimento"
                       value="<?php echo e(old('data_vencimento', $dataFormatada)); ?>"
                       class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>

            
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 p-2">
                    <option value="pendente" <?php echo e(old('status', $conta->status) == 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                    <option value="pago" <?php echo e(old('status', $conta->status) == 'pago' ? 'selected' : ''); ?>>Pago</option>
                </select>
            </div>
        </div>

        
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Atualizar
            </button>
            <a href="<?php echo e(route('contas-pagar.index')); ?>" 
               class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function() {
    $('#valor').mask('000.000.000,00', {reverse: true});
    $('#nota_fiscal').mask('0000/000000');
    $('#data_vencimento').mask('00/00/0000');
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/contas-pagar/edit.blade.php ENDPATH**/ ?>