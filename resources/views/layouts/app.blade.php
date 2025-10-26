<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sistema de Gestão</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-gray-700">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800 ml-4">@yield('title')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        

                        <div class="relative">
                            <button id="userMenuButton"
                                class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span
                                        class="text-white text-sm font-medium">{{ substr(auth()->user()->nome, 0, 1) }}</span>
                                </div>
                                <span class="hidden sm:block">{{ auth()->user()->nome }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="userMenu"
                                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class=" w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Toggle Sidebar on Mobile
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('hidden');
        });

        // Toggle User Menu
        document.getElementById('userMenuButton').addEventListener('click', function () {
            document.getElementById('userMenu').classList.toggle('hidden');
        });

        // Close menus when clicking outside
        document.addEventListener('click', function (event) {
            const userMenu = document.getElementById('userMenu');
            const userMenuButton = document.getElementById('userMenuButton');

            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')

    <!-- Máscaras jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(function () {
            // Máscaras de input
            $('#valor').mask('#.##0,00', { reverse: true });
            $('#nota_fiscal').mask('0000/000000');
            $('#data_vencimento').mask('00/00/0000');

            // Converte formato BR antes de enviar o form
            $('form').on('submit', function () {
                const valorInput = $('#valor');
                const valor = valorInput.val().replace(/\./g, '').replace(',', '.');
                valorInput.val(valor);

                const dataInput = $('#data_vencimento');
                const data = dataInput.val();
                if (data.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
                    const partes = data.split('/');
                    dataInput.val(`${partes[2]}-${partes[1]}-${partes[0]}`);
                }
            });
        });
    </script>

</body>

</html>

<!-- Scripts de Máscaras e CEP -->
<script src="{{ asset('js/masks.js') }}"></script>
</body>