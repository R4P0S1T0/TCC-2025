<!-- Overlay (escurece o fundo no mobile) -->
<div id="sidebarOverlay" 
     class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden transition-opacity duration-200 ease-in-out">
</div>

<!-- Sidebar -->
<aside id="sidebar"
    class="fixed lg:static inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 
           bg-gradient-to-b from-[#4E2A8C] to-[#3B1F72] text-white w-64 flex-shrink-0 
           z-40 transition-transform duration-300 ease-in-out shadow-lg lg:shadow-none">

    <div class="flex flex-col h-full">
        <!-- Header com botão de fechar (aparece só no mobile) -->
        <div class="flex items-center justify-between bg-[#3B1F72] px-6 py-4 border-b border-[#5E3BAE]">
            <img src="<?php echo e(asset('img/logo/logo.svg')); ?>" alt="Logo" class="w-full max-h-16 object-contain">
            <button id="sidebarClose" class="text-white text-xl lg:hidden ml-3 hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Navegação -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="<?php echo e(route('dashboard')); ?>"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#6A38C1] transition <?php echo e(request()->is('/') ? 'bg-[#6A38C1]' : ''); ?>">
                <i class="fas fa-home w-5"></i>
                <span>Dashboard</span>
            </a>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-[#C7B9F9] uppercase tracking-wider">Gestão Comercial</p>
            </div>

            <a href="<?php echo e(route('fornecedores.index')); ?>"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#6A38C1] transition <?php echo e(request()->is('fornecedores*') ? 'bg-[#6A38C1]' : ''); ?>">
                <i class="fas fa-truck w-5"></i>
                <span>Fornecedores</span>
            </a>

            <a href="<?php echo e(route('compras.index')); ?>"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#6A38C1] transition <?php echo e(request()->is('compras*') ? 'bg-[#6A38C1]' : ''); ?>">
                <i class="fas fa-shopping-cart w-5"></i>
                <span>Compras</span>
            </a>

            <a href="<?php echo e(route('contas-pagar.index')); ?>"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#6A38C1] transition <?php echo e(request()->is('contas-pagar*') ? 'bg-[#6A38C1]' : ''); ?>">
                <i class="fas fa-money-bill-wave w-5"></i>
                <span>Contas a Pagar</span>
            </a>

            <a href="<?php echo e(route('contas-receber.index')); ?>"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#6A38C1] transition <?php echo e(request()->is('contas-receber*') ? 'bg-[#6A38C1]' : ''); ?>">
                <i class="fas fa-hand-holding-usd w-5"></i>
                <span>Contas a Receber</span>
            </a>

            <a href="<?php echo e(route('calendario.index')); ?>"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#6A38C1] transition <?php echo e(request()->is('calendario*') ? 'bg-[#6A38C1]' : ''); ?>">
                <i class="fas fa-calendar-alt w-5"></i>
                <span>Calendário</span>
            </a>

            <a href="<?php echo e(route('fluxo-caixa.index')); ?>"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#6A38C1] transition <?php echo e(request()->is('fluxo-caixa*') ? 'bg-[#6A38C1]' : ''); ?>">
                <i class="fas fa-chart-line w-5"></i>
                <span>Fluxo de Caixa</span>
            </a>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-[#C7B9F9] uppercase tracking-wider">Administração</p>
            </div>

            <a href="<?php echo e(route('usuarios.index')); ?>"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#6A38C1] transition <?php echo e(request()->is('usuarios*') ? 'bg-[#6A38C1]' : ''); ?>">
                <i class="fas fa-user-shield w-5"></i>
                <span>Usuários</span>
            </a>

            <a href="<?php echo e(route('clientes.index')); ?>"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#6A38C1] transition <?php echo e(request()->is('clientes*') ? 'bg-[#6A38C1]' : ''); ?>">
                <i class="fas fa-users w-5"></i>
                <span>Clientes</span>
            </a>
        </nav>

        <!-- Footer -->
        <div class="px-4 py-4 border-t border-[#5E3BAE] bg-[#3B1F72]">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-[#6A38C1] to-[#4E2A8C] rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-medium"><?php echo e(substr(auth()->user()->nome, 0, 1)); ?></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate"><?php echo e(auth()->user()->nome); ?></p>
                    <p class="text-xs text-[#C7B9F9] truncate"><?php echo e(auth()->user()->email); ?></p>
                </div>
            </div>
        </div>
    </div>
</aside>
<?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>