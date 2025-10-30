<?php $__env->startSection('title', 'Nova Compra'); ?>

<?php $__env->startSection('content'); ?>
    <div class="p-6 space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-800">Nova Compra</h1>

            
            <a href="<?php echo e(route('compras.index')); ?>"
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

        
        <form action="<?php echo e(route('compras.store')); ?>" method="POST" id="form-compra">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                
                <div>
                    <label class="block text-sm font-medium mb-1">Descrição</label>
                    <input type="text" name="descricao" placeholder="Ex: Compra de equipamentos de rede"
                        value="<?php echo e(old('descricao')); ?>" class="w-full rounded-lg border border-gray-300 p-2" required>
                </div>

                
                <div>
                    <label class="block text-sm font-medium mb-1">Fornecedor</label>
                    <select name="id_fornecedor" class="w-full rounded-lg border border-gray-300 p-2" required>
                        <option value="">Selecione...</option>
                        <?php $__currentLoopData = $fornecedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($f->id_fornecedor); ?>"><?php echo e($f->nome); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de Compra</label>
                    <select name="tipo" class="w-full rounded-lg border border-gray-300 p-2" required>
                        <option value="">Selecione...</option>
                        <option value="produto" <?php echo e(old('tipo') == 'produto' ? 'selected' : ''); ?>>Produto</option>
                        <option value="serviço" <?php echo e(old('tipo') == 'serviço' ? 'selected' : ''); ?>>Serviço</option>
                    </select>
                </div>

                
                <div>
                    <label class="block text-sm font-medium mb-1">Valor Total</label>
                    <input type="text" name="valor_total" id="valor_total" placeholder="R$ 0,00"
                        value="<?php echo e(old('valor_total')); ?>" class="w-full rounded-lg border border-gray-300 p-2 text-right"
                        required>
                </div>

                
                <div>
                    <label class="block text-sm font-medium mb-1">Nº da Nota Fiscal</label>
                    <input type="text" name="nota_fiscal" id="nota_fiscal" placeholder="Ex: 0000/000000"
                        value="<?php echo e(old('nota_fiscal')); ?>"
                        class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

               
<div>
    <label for="data_compra" class="block text-sm font-medium mb-1 text-gray-700">Data da Compra</label>
    <input type="date" name="data_compra" id="data_compra"
           value="<?php echo e(old('data_compra')); ?>"
           class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
           required>
</div>


                
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 p-2" required>
                        <option value="pendente" <?php echo e(old('status') == 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                        <option value="finalizada" <?php echo e(old('status') == 'finalizada' ? 'selected' : ''); ?>>Finalizada</option>
                        <option value="cancelada" <?php echo e(old('status') == 'cancelada' ? 'selected' : ''); ?>>Cancelada</option>
                    </select>
                </div>

                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Observações</label>
                    <textarea name="observacoes" rows="3" placeholder="Digite observações adicionais sobre a compra..."
                        class="w-full rounded-lg border border-gray-300 p-2"><?php echo e(old('observacoes')); ?></textarea>
                </div>
            </div>

            
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Salvar
                </button>

                <a href="<?php echo e(route('compras.index')); ?>"
                    class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#valor_total').mask('R$ 000.000.000,00', { reverse: true });
            $('#nota_fiscal').mask('0000/000000');
            $('#data_compra').mask('00/00/0000');
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/compras/create.blade.php ENDPATH**/ ?>