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

            
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Endereço</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                        <input type="text" name="cep" id="cep" value="<?php echo e(old('cep')); ?>"
                            maxlength="9"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="00000-000">
                    </div>

                    
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                        <input type="text" name="logradouro" id="logradouro" value="<?php echo e(old('logradouro')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Rua, Avenida, etc.">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                        <input type="text" name="numero" id="numero" value="<?php echo e(old('numero')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="123">
                    </div>

                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input type="text" name="complemento" id="complemento" value="<?php echo e(old('complemento')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Apto, sala, etc.">
                    </div>

                    
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                        <input type="text" name="bairro" id="bairro" value="<?php echo e(old('bairro')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Bairro">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="cidade" id="cidade" value="<?php echo e(old('cidade')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Cidade">
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="estado" id="estado"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecione</option>
                            <?php $__currentLoopData = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($uf); ?>" <?php echo e(old('estado') == $uf ? 'selected' : ''); ?>><?php echo e($uf); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
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

    const regexSenha = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\-_])[A-Za-z\d@$!%*?&\-_]{8,}$/;

    function validar() {
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

    senha.addEventListener('input', validar);
    confirm.addEventListener('input', validar);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/usuarios/create.blade.php ENDPATH**/ ?>