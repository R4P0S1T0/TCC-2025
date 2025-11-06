<?php $__env->startSection('title', 'Novo Cliente'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Novo Cliente</h1>
        <a href="<?php echo e(route('clientes.index')); ?>" 
           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    
    <?php if($errors->any()): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc ml-5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('clientes.store')); ?>" id="clienteForm">
            <?php echo csrf_field(); ?>

            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                    <input type="text" name="nome" value="<?php echo e(old('nome')); ?>" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Digite o nome completo">
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CPF / CNPJ *</label>
                    <input type="text" name="cpf_cnpj" id="cpf_cnpj" value="<?php echo e(old('cpf_cnpj')); ?>"
                        maxlength="18" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="000.000.000-00 ou 00.000.000/0000-00">
                    <p id="cpfCnpjMsg" class="text-xs text-gray-500 mt-1">Digite um CPF ou CNPJ válido.</p>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="usuario@email.com">
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="telefone" id="telefone" value="<?php echo e(old('telefone')); ?>"
                        maxlength="15"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="(11) 99999-9999">
                </div>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                    <input type="text" id="cep" name="cep" maxlength="9" value="<?php echo e(old('cep')); ?>"
                        placeholder="00000-000"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <small id="cepStatus" class="text-xs text-gray-500 mt-1"></small>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                    <input type="text" id="endereco" name="endereco" value="<?php echo e(old('endereco')); ?>"
                        placeholder="Rua, Avenida, etc."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                    <input type="text" name="numero" value="<?php echo e(old('numero')); ?>"
                        placeholder="123"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                    <input type="text" name="complemento" value="<?php echo e(old('complemento')); ?>"
                        placeholder="Apto, sala, etc."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                    <input type="text" name="bairro" value="<?php echo e(old('bairro')); ?>"
                        placeholder="Bairro"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                    <input type="text" id="cidade" name="cidade" value="<?php echo e(old('cidade')); ?>"
                        placeholder="Cidade"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <input type="text" id="estado" name="estado" maxlength="2"
                        value="<?php echo e(old('estado')); ?>" placeholder="UF"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 uppercase 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="ativo" <?php echo e(old('status') === 'ativo' ? 'selected' : ''); ?>>Ativo</option>
                        <option value="inativo" <?php echo e(old('status') === 'inativo' ? 'selected' : ''); ?>>Inativo</option>
                    </select>
                </div>
            </div>

            
            <div class="flex justify-end space-x-3 mt-6">
                <a href="<?php echo e(route('clientes.index')); ?>" 
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" id="btnSalvar"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i>Salvar Cliente
                </button>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const cpfCnpj = document.getElementById('cpf_cnpj');
    const cpfMsg = document.getElementById('cpfCnpjMsg');
    const btn = document.getElementById('btnSalvar');
    const telefone = document.getElementById('telefone');
    const cep = document.getElementById('cep');
    const cepStatus = document.getElementById('cepStatus');

    // --- Máscara telefone ---
    telefone.addEventListener('input', () => {
        let v = telefone.value.replace(/\D/g, '');
        if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        else if (v.length > 5) v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        else v = v.replace(/^(\d*)/, '($1');
        telefone.value = v;
    });

    // --- Máscara dinâmica e validação CPF/CNPJ ---
    cpfCnpj.addEventListener('input', () => {
        let v = cpfCnpj.value.replace(/\D/g, '');

        // Define se é CPF ou CNPJ
        if (v.length <= 11) {
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            cpfCnpj.value = v;

            if (v.length === 14 && validarCPF(v)) {
                cpfMsg.textContent = '✔ CPF válido.';
                cpfMsg.className = 'text-xs text-green-600 mt-1';
                btn.disabled = false;
            } else if (v.length === 14) {
                cpfMsg.textContent = '✖ CPF inválido.';
                cpfMsg.className = 'text-xs text-red-600 mt-1';
                btn.disabled = true;
            } else {
                cpfMsg.textContent = 'Digite um CPF válido.';
                cpfMsg.className = 'text-xs text-gray-500 mt-1';
                btn.disabled = true;
            }
        } else {
            v = v.replace(/^(\d{2})(\d)/, '$1.$2');
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
            v = v.replace(/(\d{4})(\d)/, '$1-$2');
            cpfCnpj.value = v;

            if (v.length === 18 && validarCNPJ(v)) {
                cpfMsg.textContent = '✔ CNPJ válido.';
                cpfMsg.className = 'text-xs text-green-600 mt-1';
                btn.disabled = false;
            } else if (v.length === 18) {
                cpfMsg.textContent = '✖ CNPJ inválido.';
                cpfMsg.className = 'text-xs text-red-600 mt-1';
                btn.disabled = true;
            } else {
                cpfMsg.textContent = 'Digite um CNPJ válido.';
                cpfMsg.className = 'text-xs text-gray-500 mt-1';
                btn.disabled = true;
            }
        }
    });

    // --- Validação CPF ---
    function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');
        if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
        let soma = 0;
        for (let i = 0; i < 9; i++) soma += parseInt(cpf[i]) * (10 - i);
        let resto = 11 - (soma % 11);
        if (resto >= 10) resto = 0;
        if (resto !== parseInt(cpf[9])) return false;
        soma = 0;
        for (let i = 0; i < 10; i++) soma += parseInt(cpf[i]) * (11 - i);
        resto = 11 - (soma % 11);
        if (resto >= 10) resto = 0;
        return resto === parseInt(cpf[10]);
    }

    // --- Validação CNPJ ---
    function validarCNPJ(cnpj) {
        cnpj = cnpj.replace(/[^\d]+/g, '');
        if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;
        let tamanho = cnpj.length - 2;
        let numeros = cnpj.substring(0, tamanho);
        let digitos = cnpj.substring(tamanho);
        let soma = 0;
        let pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0)) return false;
        tamanho++;
        numeros = cnpj.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        return resultado == digitos.charAt(1);
    }

    // --- Busca automática de CEP ---
    cep.addEventListener('input', async () => {
        let v = cep.value.replace(/\D/g, '');
        if (v.length > 5) v = v.replace(/^(\d{5})(\d)/, '$1-$2');
        cep.value = v;

        if (v.length === 9) {
            cepStatus.textContent = 'Buscando endereço...';
            try {
                const res = await fetch(`https://viacep.com.br/ws/${v}/json/`);
                const data = await res.json();
                if (!data.erro) {
                    document.getElementById('endereco').value = data.logradouro || '';
                    document.getElementById('cidade').value = data.localidade || '';
                    document.getElementById('estado').value = data.uf || '';
                    cepStatus.textContent = '✔ Endereço encontrado.';
                    cepStatus.className = 'text-xs text-green-600 mt-1';
                } else {
                    cepStatus.textContent = 'CEP não encontrado.';
                    cepStatus.className = 'text-xs text-red-600 mt-1';
                }
            } catch {
                cepStatus.textContent = 'Erro ao consultar CEP.';
                cepStatus.className = 'text-xs text-red-600 mt-1';
            }
        } else cepStatus.textContent = '';
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/clientes/create.blade.php ENDPATH**/ ?>