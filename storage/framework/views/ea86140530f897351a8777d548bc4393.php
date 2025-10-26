<?php $__env->startSection('title', 'Nova Conta a Receber'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Nova Conta a Receber</h1>
        <a href="<?php echo e(route('contas-receber.index')); ?>" 
           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('contas-receber.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                    <select name="cliente_nome" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione um cliente</option>
                        <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cliente->nome); ?>"><?php echo e($cliente->nome); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                    <input type="text" name="descricao" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ex: Venda de produtos">
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor *</label>
                    <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2">
                        <span class="text-gray-600 mr-2">R$</span>
                        <input type="text" name="valor" id="valor" required
                               class="flex-1 border-none outline-none text-right"
                               placeholder="0,00">
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Vencimento *</label>
                    <input type="text" name="data_vencimento" id="data_vencimento" required
                           value="<?php echo e(now()->format('d/m/Y')); ?>"
                           maxlength="10"
                           placeholder="dd/mm/aaaa"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="observacoes" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Observações adicionais..."></textarea>
                </div>
            </div>

            
            <div class="flex justify-end space-x-3 mt-6">
                <a href="<?php echo e(route('contas-receber.index')); ?>" 
                   class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-hand-holding-usd mr-2"></i>Cadastrar Conta
                </button>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    // Máscara de moeda
    const inputValor = document.getElementById('valor');
    inputValor.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2) + '';
        value = value.replace('.', ',');
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        this.value = value;
    });

    // Máscara de data (dd/mm/yyyy)
    const inputData = document.getElementById('data_vencimento');
    inputData.addEventListener('input', function(e) {
        let v = this.value.replace(/\D/g, '');
        if (v.length > 2 && v.length <= 4) {
            v = v.replace(/(\d{2})(\d+)/, '$1/$2');
        } else if (v.length > 4) {
            v = v.replace(/(\d{2})(\d{2})(\d{1,4})/, '$1/$2/$3');
        }
        this.value = v.slice(0, 10);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/contas-receber/create.blade.php ENDPATH**/ ?>