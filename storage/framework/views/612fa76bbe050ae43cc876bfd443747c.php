<?php $__env->startSection('title', 'Editar Fornecedor'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Editar Fornecedor</h1>
        <a href="<?php echo e(route('fornecedores.index')); ?>" 
           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('fornecedores.update', $fornecedor->id_fornecedor)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Fornecedor *</label>
                    <input type="text" name="nome" value="<?php echo e(old('nome', $fornecedor->nome)); ?>" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Digite o nome do fornecedor">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ *</label>
                    <input type="text" name="cnpj" value="<?php echo e(old('cnpj', $fornecedor->cnpj)); ?>" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="00.000.000/0000-00"
                           maxlength="18">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                    <input type="text" name="telefone" value="<?php echo e(old('telefone', $fornecedor->telefone)); ?>" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="(11) 99999-9999"
                           maxlength="15">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $fornecedor->email)); ?>" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="fornecedor@email.com">
                </div>
            </div>

            <!-- Seção de Endereço com CEP -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Endereço</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                        <input type="text" name="cep" value="<?php echo e(old('cep', $fornecedor->cep)); ?>" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="00000-000"
                               maxlength="9">
                        <p class="text-xs text-gray-500 mt-1">Digite o CEP para buscar o endereço</p>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro *</label>
                        <input type="text" name="logradouro" value="<?php echo e(old('logradouro', $fornecedor->logradouro)); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Rua, Avenida, etc.">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número *</label>
                        <input type="text" name="numero" value="<?php echo e(old('numero', $fornecedor->numero)); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="123">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input type="text" name="complemento" value="<?php echo e(old('complemento', $fornecedor->complemento)); ?>" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Apto, Sala, etc.">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bairro *</label>
                        <input type="text" name="bairro" value="<?php echo e(old('bairro', $fornecedor->bairro)); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Bairro">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cidade *</label>
                        <input type="text" name="cidade" value="<?php echo e(old('cidade', $fornecedor->cidade)); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Cidade">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select name="estado" required 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecione</option>
                            <option value="AC" <?php echo e(old('estado', $fornecedor->estado) == 'AC' ? 'selected' : ''); ?>>Acre</option>
                            <option value="AL" <?php echo e(old('estado', $fornecedor->estado) == 'AL' ? 'selected' : ''); ?>>Alagoas</option>
                            <option value="AP" <?php echo e(old('estado', $fornecedor->estado) == 'AP' ? 'selected' : ''); ?>>Amapá</option>
                            <option value="AM" <?php echo e(old('estado', $fornecedor->estado) == 'AM' ? 'selected' : ''); ?>>Amazonas</option>
                            <option value="BA" <?php echo e(old('estado', $fornecedor->estado) == 'BA' ? 'selected' : ''); ?>>Bahia</option>
                            <option value="CE" <?php echo e(old('estado', $fornecedor->estado) == 'CE' ? 'selected' : ''); ?>>Ceará</option>
                            <option value="DF" <?php echo e(old('estado', $fornecedor->estado) == 'DF' ? 'selected' : ''); ?>>Distrito Federal</option>
                            <option value="ES" <?php echo e(old('estado', $fornecedor->estado) == 'ES' ? 'selected' : ''); ?>>Espírito Santo</option>
                            <option value="GO" <?php echo e(old('estado', $fornecedor->estado) == 'GO' ? 'selected' : ''); ?>>Goiás</option>
                            <option value="MA" <?php echo e(old('estado', $fornecedor->estado) == 'MA' ? 'selected' : ''); ?>>Maranhão</option>
                            <option value="MT" <?php echo e(old('estado', $fornecedor->estado) == 'MT' ? 'selected' : ''); ?>>Mato Grosso</option>
                            <option value="MS" <?php echo e(old('estado', $fornecedor->estado) == 'MS' ? 'selected' : ''); ?>>Mato Grosso do Sul</option>
                            <option value="MG" <?php echo e(old('estado', $fornecedor->estado) == 'MG' ? 'selected' : ''); ?>>Minas Gerais</option>
                            <option value="PA" <?php echo e(old('estado', $fornecedor->estado) == 'PA' ? 'selected' : ''); ?>>Pará</option>
                            <option value="PB" <?php echo e(old('estado', $fornecedor->estado) == 'PB' ? 'selected' : ''); ?>>Paraíba</option>
                            <option value="PR" <?php echo e(old('estado', $fornecedor->estado) == 'PR' ? 'selected' : ''); ?>>Paraná</option>
                            <option value="PE" <?php echo e(old('estado', $fornecedor->estado) == 'PE' ? 'selected' : ''); ?>>Pernambuco</option>
                            <option value="PI" <?php echo e(old('estado', $fornecedor->estado) == 'PI' ? 'selected' : ''); ?>>Piauí</option>
                            <option value="RJ" <?php echo e(old('estado', $fornecedor->estado) == 'RJ' ? 'selected' : ''); ?>>Rio de Janeiro</option>
                            <option value="RN" <?php echo e(old('estado', $fornecedor->estado) == 'RN' ? 'selected' : ''); ?>>Rio Grande do Norte</option>
                            <option value="RS" <?php echo e(old('estado', $fornecedor->estado) == 'RS' ? 'selected' : ''); ?>>Rio Grande do Sul</option>
                            <option value="RO" <?php echo e(old('estado', $fornecedor->estado) == 'RO' ? 'selected' : ''); ?>>Rondônia</option>
                            <option value="RR" <?php echo e(old('estado', $fornecedor->estado) == 'RR' ? 'selected' : ''); ?>>Roraima</option>
                            <option value="SC" <?php echo e(old('estado', $fornecedor->estado) == 'SC' ? 'selected' : ''); ?>>Santa Catarina</option>
                            <option value="SP" <?php echo e(old('estado', $fornecedor->estado) == 'SP' ? 'selected' : ''); ?>>São Paulo</option>
                            <option value="SE" <?php echo e(old('estado', $fornecedor->estado) == 'SE' ? 'selected' : ''); ?>>Sergipe</option>
                            <option value="TO" <?php echo e(old('estado', $fornecedor->estado) == 'TO' ? 'selected' : ''); ?>>Tocantins</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="<?php echo e(route('fornecedores.index')); ?>" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Atualizar Fornecedor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Buscar CEP quando o campo perder o foco
    const cepInput = document.querySelector('input[name="cep"]');
    if (cepInput) {
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                buscarCEP(cep);
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/fornecedores/edit.blade.php ENDPATH**/ ?>