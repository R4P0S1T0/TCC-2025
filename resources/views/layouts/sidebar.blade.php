<aside id="sidebar" class="bg-gradient-to-b from-gray-800 to-gray-900 text-white w-64 flex-shrink-0 hidden lg:block lg:static absolute inset-y-0 left-0 z-40 transform lg:translate-x-0 transition duration-200 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
                <span class="text-xl font-bold">Gestão Pro</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700 transition {{ request()->is('/') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-home w-5"></i>
                <span>Dashboard</span>
            </a>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gestão Comercial</p>
            </div>

            <a href="{{ route('fornecedores.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700 transition {{ request()->is('fornecedores*') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-truck w-5"></i>
                <span>Fornecedores</span>
            </a>

            <a href="{{ route('compras.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700 transition {{ request()->is('compras*') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-shopping-cart w-5"></i>
                <span>Compras</span>
            </a>

            <a href="{{ route('contas-pagar.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700 transition {{ request()->is('contas-pagar*') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-money-bill-wave w-5"></i>
                <span>Contas a Pagar</span>
            </a>

            <a href="{{ route('contas-receber.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700 transition {{ request()->is('contas-receber*') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-hand-holding-usd w-5"></i>
                <span>Contas a Receber</span>
            </a>

            <a href="{{ route('calendario.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700 transition {{ request()->is('calendario*') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-calendar-alt w-5"></i>
                <span>Calendário</span>
            </a>

            <a href="{{ route('fluxo-caixa.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700 transition {{ request()->is('fluxo-caixa*') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-chart-line w-5"></i>
                <span>Fluxo de Caixa</span>
            </a>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administração</p>
            </div>

            <a href="{{ route('usuarios.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700 transition {{ request()->is('usuarios*') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-users w-5"></i>
                <span>Usuários</span>
            </a>

            <a href="{{ route('clientes.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700 transition {{ request()->is('clientes*') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-users w-5"></i>
                <span>Clientes</span>
            </a>

        </nav>

        <!-- Footer -->
        <div class="px-4 py-4 border-t border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-medium">{{ substr(auth()->user()->nome, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ auth()->user()->nome }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>
