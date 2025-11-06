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
        <form method="POST" action="<?php echo e(route('fornecedores.update', $fornecedor->id_fornecedor)); ?>" id="fornecedorForm">
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
                    <input type="text" id="cnpj" name="cnpj" value="<?php echo e(old('cnpj', $fornecedor->cnpj)); ?>" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="00.000.000/0000-00" maxlength="18">
                    <p id="cnpjMsg" class="text-xs text-gray-500 mt-1">Digite um CNPJ válido.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                    <input type="text" id="telefone" name="telefone" value="<?php echo e(old('telefone', $fornecedor->telefone)); ?>" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="(11) 99999-9999" maxlength="15">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $fornecedor->email)); ?>" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="fornecedor@email.com">
                </div>
            </div>

            
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Endereço</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                        <input type="text" id="cep" name="cep" value="<?php echo e(old('cep', $fornecedor->cep)); ?>" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="00000-000" maxlength="9">
                        <p id="cepStatus" class="text-xs text-gray-500 mt-1">Digite o CEP para buscar o endereço.</p>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro *</label>
                        <input type="text" id="logradouro" name="logradouro" value="<?php echo e(old('logradouro', $fornecedor->logradouro)); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Rua, Avenida, etc.">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bairro *</label>
                        <input type="text" name="bairro" value="<?php echo e(old('bairro', $fornecedor->bairro)); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Bairro">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cidade *</label>
                        <input type="text" id="cidade" name="cidade" value="<?php echo e(old('cidade', $fornecedor->cidade)); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Cidade">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <input type="text" id="estado" name="estado" maxlength="2" value="<?php echo e(old('estado', $fornecedor->estado)); ?>" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 uppercase focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="UF">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="<?php echo e(route('fornecedores.index')); ?>" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" id="btnSalvar" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i>Atualizar Fornecedor
                </button>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const cnpjInput = document.getElementById('cnpj');
    const msg = document.getElementById('cnpjMsg');
    const btn = document.getElementById('btnSalvar');
    const tel = document.getElementById('telefone');
    const cep = document.getElementById('cep');
    const cepStatus = document.getElementById('cepStatus');

    // --- Máscara telefone ---
    tel.addEventListener('input', () => {
        let v = tel.value.replace(/\D/g, '');
        if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        else if (v.length > 5) v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        else v = v.replace(/^(\d*)/, '($1');
        tel.value = v;
    });

    // --- Máscara e validação CNPJ ---
    cnpjInput.addEventListener('input', () => {
        let v = cnpjInput.value.replace(/\D/g, '');
        v = v.replace(/^(\d{2})(\d)/, '$1.$2');
        v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
        v = v.replace(/(\d{4})(\d)/, '$1-$2');
        cnpjInput.value = v;

        if (v.length === 18) {
            if (validarCNPJ(v)) {
                msg.textContent = '✔ CNPJ válido.';
                msg.className = 'text-xs text-green-600 mt-1';
                btn.disabled = false;
            } else {
                msg.textContent = '✖ CNPJ inválido.';
                msg.className = 'text-xs text-red-600 mt-1';
                btn.disabled = true;
            }
        } else {
            msg.textContent = 'Digite um CNPJ válido.';
            msg.className = 'text-xs text-gray-500 mt-1';
            btn.disabled = true;
        }
    });

    // --- Função validar CNPJ ---
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
                    document.getElementById('logradouro').value = data.logradouro || '';
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/fornecedores/edit.blade.php ENDPATH**/ ?>