<?php $__env->startSection('title', 'Novo Usuário'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Novo Usuário</h1>
        <a href="<?php echo e(route('usuarios.index')); ?>" 
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
        <form method="POST" action="<?php echo e(route('usuarios.store')); ?>" id="usuarioForm">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
                    <input type="text" name="cpf" id="cpf" value="<?php echo e(old('cpf')); ?>"
                        maxlength="14"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="000.000.000-00">
                    <p id="cpfMsg" class="text-xs text-gray-500 mt-1">Digite um CPF válido.</p>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
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

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Senha *</label>
                    <input type="password" name="senha" id="senha" required minlength="8"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Digite uma senha segura">
                    <p id="senhaRequisitos" class="text-xs text-gray-500 mt-1">
                        A senha deve conter pelo menos 8 caracteres, incluindo letra maiúscula, minúscula, número e símbolo.
                    </p>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha *</label>
                    <input type="password" name="senha_confirmation" id="senha_confirmation" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Confirme a senha">
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Usuário *</label>
                    <select name="tipo" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione o tipo</option>
                        <option value="admin" <?php echo e(old('tipo') == 'admin' ? 'selected' : ''); ?>>Administrador</option>
                        <option value="super-admin" <?php echo e(old('tipo') == 'super-admin' ? 'selected' : ''); ?>>Super Administrador</option>
                    </select>
                </div>
            </div>

            
            <div class="flex justify-end space-x-3 mt-6">
                <a href="<?php echo e(route('usuarios.index')); ?>" 
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" id="btnCadastrar"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-user-plus mr-2"></i>Cadastrar Usuário
                </button>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const senha = document.getElementById('senha');
    const confirm = document.getElementById('senha_confirmation');
    const btn = document.getElementById('btnCadastrar');
    const requisitos = document.getElementById('senhaRequisitos');
    const cpfInput = document.getElementById('cpf');
    const cpfMsg = document.getElementById('cpfMsg');

    const regexSenha = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\-_])[A-Za-z\d@$!%*?&\-_]{8,}$/;

    // --- Validação da senha ---
    function validarSenha() {
        if (regexSenha.test(senha.value) && senha.value === confirm.value) {
            requisitos.textContent = '✔ Senha forte e confirmada.';
            requisitos.className = 'text-xs text-green-600 mt-1';
            btn.disabled = false;
        } else {
            requisitos.textContent = 'A senha deve conter pelo menos 8 caracteres, incluindo letra maiúscula, minúscula, número e símbolo.';
            requisitos.className = 'text-xs text-red-600 mt-1';
            btn.disabled = true;
        }
    }
    senha.addEventListener('input', validarSenha);
    confirm.addEventListener('input', validarSenha);

    // --- Validação de CPF ---
    function validarCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');
        if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

        let soma = 0;
        for (let i = 0; i < 9; i++) soma += parseInt(cpf.charAt(i)) * (10 - i);
        let resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(9))) return false;

        soma = 0;
        for (let i = 0; i < 10; i++) soma += parseInt(cpf.charAt(i)) * (11 - i);
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;

        return resto === parseInt(cpf.charAt(10));
    }

    cpfInput.addEventListener('input', () => {
        let v = cpfInput.value.replace(/\D/g, '');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        cpfInput.value = v;

        if (v.length === 14) {
            if (validarCPF(v)) {
                cpfMsg.textContent = '✔ CPF válido.';
                cpfMsg.className = 'text-xs text-green-600 mt-1';
            } else {
                cpfMsg.textContent = '✖ CPF inválido.';
                cpfMsg.className = 'text-xs text-red-600 mt-1';
            }
        } else {
            cpfMsg.textContent = 'Digite um CPF válido.';
            cpfMsg.className = 'text-xs text-gray-500 mt-1';
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/usuarios/create.blade.php ENDPATH**/ ?>