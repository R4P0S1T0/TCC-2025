<?php echo csrf_field(); ?>
<?php if(isset($cliente)): ?>
    <?php echo method_field('PUT'); ?>
<?php endif; ?>

<div class="space-y-6">
    
    <div>
        <label class="block text-sm text-gray-600 mb-1 font-medium">Nome *</label>
        <input type="text" name="nome" value="<?php echo e(old('nome', $cliente->nome ?? '')); ?>"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-gray-500 focus:outline-none"
            required>
    </div>

    
    <div>
        <label class="block text-sm text-gray-600 mb-1 font-medium">Email</label>
        <input type="email" name="email" value="<?php echo e(old('email', $cliente->email ?? '')); ?>"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-gray-500 focus:outline-none">
    </div>

    
    <div>
        <label class="block text-sm text-gray-600 mb-1 font-medium">Telefone</label>
        <input type="text" id="telefone" name="telefone" value="<?php echo e(old('telefone', $cliente->telefone ?? '')); ?>"
            placeholder="(11) 99999-9999"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-gray-500 focus:outline-none">
    </div>

    
    <div>
        <label class="block text-sm text-gray-600 mb-1 font-medium">CPF / CNPJ</label>
        <input type="text" id="cpf_cnpj" name="cpf_cnpj" value="<?php echo e(old('cpf_cnpj', $cliente->cpf_cnpj ?? '')); ?>"
            placeholder="Digite o CPF ou CNPJ"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-gray-500 focus:outline-none">
    </div>

    
    <div>
        <label class="block text-sm text-gray-600 mb-1 font-medium">CEP</label>
        <input type="text" id="cep" name="cep" maxlength="9"
            value="<?php echo e(old('cep', $cliente->cep ?? '')); ?>"
            placeholder="00000-000"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-gray-500 focus:outline-none">
        <small id="cep-status" class="text-gray-500 text-xs mt-1"></small>
    </div>

    
    <div>
        <label class="block text-sm text-gray-600 mb-1 font-medium">Endereço</label>
        <input type="text" id="endereco" name="endereco" value="<?php echo e(old('endereco', $cliente->endereco ?? '')); ?>"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-gray-500 focus:outline-none">
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm text-gray-600 mb-1 font-medium">Cidade</label>
            <input type="text" id="cidade" name="cidade" value="<?php echo e(old('cidade', $cliente->cidade ?? '')); ?>"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-gray-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1 font-medium">Estado</label>
            <input type="text" id="estado" name="estado" maxlength="2"
                value="<?php echo e(old('estado', $cliente->estado ?? '')); ?>"
                placeholder="UF"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 uppercase text-gray-800 focus:ring-2 focus:ring-gray-500 focus:outline-none">
        </div>
    </div>

    
    <div>
        <label class="block text-sm text-gray-600 mb-1 font-medium">Status</label>
        <select name="status"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-gray-500 focus:outline-none">
            <option value="ativo" <?php echo e(old('status', $cliente->status ?? '') == 'ativo' ? 'selected' : ''); ?>>Ativo</option>
            <option value="inativo" <?php echo e(old('status', $cliente->status ?? '') == 'inativo' ? 'selected' : ''); ?>>Inativo</option>
        </select>
    </div>

    
    <div class="flex justify-end gap-3 pt-4">
        <a href="<?php echo e(route('clientes.index')); ?>"
           class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition text-sm">
            Cancelar
        </a>
        <button type="submit"
            class="px-5 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition text-sm">
            <?php echo e(isset($cliente) ? 'Salvar Alterações' : 'Cadastrar Cliente'); ?>

        </button>
    </div>
</div>


<?php echo $__env->make('clientes.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/clientes/form.blade.php ENDPATH**/ ?>