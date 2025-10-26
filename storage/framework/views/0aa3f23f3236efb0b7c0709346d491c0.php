<?php $__env->startSection('title', 'Nova Conta a Pagar'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-6">
    
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">Nova Conta a Pagar</h1>

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

    
    <form action="<?php echo e(route('contas-pagar.store')); ?>" method="POST" id="form-conta">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            
            <div>
                <label class="block text-sm font-medium mb-1">Compra</label>
                <select name="id_compra" id="id_compra"
                        class="w-full rounded-lg border border-gray-300 p-2" required>
                    <option value="">Selecione...</option>
                    <?php $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!in_array($c->status, ['finalizada', 'cancelada'])): ?>
                            <option value="<?php echo e($c->id_compra); ?>">
                                #<?php echo e($c->id_compra); ?> — <?php echo e(Str::limit($c->descricao, 40)); ?>

                            </option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div>
                <label class="block text-sm font-medium mb-1">Valor</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-600">R$</span>
                    <input type="text" name="valor" id="valor"
                           placeholder="0,00"
                           value="<?php echo e(old('valor')); ?>"
                           class="pl-8 w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
            </div>

            
            <div>
                <label class="block text-sm font-medium mb-1">Nº da Nota Fiscal</label>
                <input type="text" name="nota_fiscal" id="nota_fiscal"
                       placeholder="Ex: 0000/000000"
                       value="<?php echo e(old('nota_fiscal')); ?>"
                       class="w-full rounded-lg border border-gray-300 p-2">
            </div>

            
            <div>
                <label class="block text-sm font-medium mb-1">Data de Vencimento</label>
                <input type="text" name="data_vencimento" id="data_vencimento"
                       placeholder="dd/mm/aaaa"
                       value="<?php echo e(old('data_vencimento')); ?>"
                       class="w-full rounded-lg border border-gray-300 p-2" required>
            </div>

            
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 p-2" required>
                    <option value="pendente" <?php echo e(old('status') == 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                    <option value="pago" <?php echo e(old('status') == 'pago' ? 'selected' : ''); ?>>Pago</option>
                </select>
            </div>
        </div>

        
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Salvar
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
    // ✅ Máscaras seguras
    $('#valor').mask('000.000.000.000,00', { reverse: true });
    $('#nota_fiscal').mask('0000/000000');
    $('#data_vencimento').mask('00/00/0000');

    // ✅ Busca de compra
    $('#id_compra').on('change', function() {
        const id = $(this).val();
        if (!id) return;

        fetch(`/compras/dados/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Preenche automaticamente os campos
                $('#valor').val(data.valor_total);
                $('#nota_fiscal').val(data.nota_fiscal);

                if (data.data_compra) {
                    $('#data_vencimento').val(data.data_compra);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erro ao buscar dados da compra.');
            });
    });

    // ✅ Evita duplicação de "R$"
    $('#valor').on('focus', function() {
        $(this).val($(this).val().replace(/^R\$\s*/, ''));
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/contas-pagar/create.blade.php ENDPATH**/ ?>