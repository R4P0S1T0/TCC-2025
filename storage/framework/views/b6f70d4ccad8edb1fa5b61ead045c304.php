<?php $__env->startSection('title', 'Editar Conta a Receber'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Editar Conta a Receber</h1>
        <a href="<?php echo e(route('contas-receber.index')); ?>" 
           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('contas-receber.update', $conta->id_creceber)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                    <input type="text" name="cliente_nome" required
                           value="<?php echo e(old('cliente_nome', $conta->cliente_nome)); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Nome do cliente">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                    <input type="text" name="descricao" required
                           value="<?php echo e(old('descricao', $conta->descricao)); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ex: Venda de produtos">
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-600 font-medium">R$</span>
                        <input type="text" id="valor" name="valor" required
                            value="<?php echo e(number_format($conta->valor, 2, ',', '.')); ?>"
                            class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0,00">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Vencimento *</label>
                    <input type="date" name="data_vencimento" required
                           value="<?php echo e(old('data_vencimento', \Carbon\Carbon::parse($conta->data_vencimento)->format('Y-m-d'))); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pendente" <?php echo e($conta->status == 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                        <option value="recebido" <?php echo e($conta->status == 'recebido' ? 'selected' : ''); ?>>Recebido</option>
                        <option value="cancelado" <?php echo e($conta->status == 'cancelado' ? 'selected' : ''); ?>>Cancelado</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="observacoes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Observações adicionais..."><?php echo e(old('observacoes', $conta->observacoes)); ?></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="<?php echo e(route('contas-receber.index')); ?>" 
                   class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('valor');

    input.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2) + '';
        value = value.replace('.', ',');
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        e.target.value = value;
    });

    input.addEventListener('focus', function () {
        if (input.value.trim() === '') input.value = '0,00';
    });

    input.addEventListener('blur', function () {
        if (input.value.trim() === '' || input.value === '0,00') input.value = '';
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/contas-receber/edit.blade.php ENDPATH**/ ?>