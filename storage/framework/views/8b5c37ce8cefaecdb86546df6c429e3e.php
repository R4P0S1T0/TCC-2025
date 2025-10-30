<?php $__env->startSection('title', 'Fornecedores'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Fornecedores</h1>
        <a href="<?php echo e(route('fornecedores.create')); ?>" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center w-full sm:w-auto">
            <i class="fas fa-plus mr-2"></i>Novo Fornecedor
        </a>
    </div>

    <!-- Filtros -->
    <form method="GET" class="bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-1 md:grid-cols-4 gap-4 shadow-sm">
        <div>
            <label class="text-sm block mb-1 text-gray-700">Buscar</label>
            <input type="text" name="busca" value="<?php echo e(request('busca')); ?>"
                   placeholder="Nome, CNPJ, e-mail..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="text-sm block mb-1 text-gray-700">Estado (UF)</label>
            <input type="text" name="estado" maxlength="2" value="<?php echo e(request('estado')); ?>"
                   placeholder="SP"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 uppercase focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="text-sm block mb-1 text-gray-700">Cidade</label>
            <input type="text" name="cidade" value="<?php echo e(request('cidade')); ?>"
                   placeholder="Ex: Americana"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex items-end">
            <button class="w-full md:w-auto px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition flex items-center justify-center">
                <i class="fas fa-filter mr-2"></i>Filtrar
            </button>
        </div>
    </form>

    <!-- Mensagem de sucesso -->
    <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Tabela -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">CNPJ</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Telefone</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">E-mail</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Cidade</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $fornecedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fornecedor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-truck text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-800"><?php echo e($fornecedor->nome); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-800"><?php echo e($fornecedor->cnpj); ?></td>
                            <td class="px-6 py-4 text-gray-800"><?php echo e($fornecedor->telefone); ?></td>
                            <td class="px-6 py-4 text-gray-800"><?php echo e($fornecedor->email); ?></td>
                            <td class="px-6 py-4 text-gray-800"><?php echo e($fornecedor->cidade ?? '—'); ?></td>
                            <td class="px-6 py-4 text-gray-800">
                                <div class="flex space-x-3">
                                    <a href="<?php echo e(route('fornecedores.show', $fornecedor->id_fornecedor)); ?>" class="text-green-600 hover:text-green-800" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('fornecedores.edit', $fornecedor->id_fornecedor)); ?>" class="text-blue-600 hover:text-blue-800" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('fornecedores.destroy', $fornecedor->id_fornecedor)); ?>" method="POST" onsubmit="return confirm('Excluir este fornecedor?')" class="inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">Nenhum fornecedor encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/fornecedores/index.blade.php ENDPATH**/ ?>