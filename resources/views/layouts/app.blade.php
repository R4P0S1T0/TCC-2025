<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo/logo.svg') }}">
    <link rel="shortcut icon" href="{{ asset('img/logo/logo.svg') }}" type="image/x-icon">
    <title>@yield('title') - Sistema de Gestão</title>

    <!-- Tailwind & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-gray-50 h-screen flex overflow-hidden">

    <!-- Overlay (escurece o fundo no mobile) -->
    <div id="sidebarOverlay"
        class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden transition-opacity duration-200 ease-in-out">
    </div>

    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Conteúdo Principal -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <!-- Botão abrir sidebar (mobile) -->
                <button id="sidebarToggle" class="text-gray-700 hover:text-gray-900 lg:hidden">
                    <i class="fas fa-bars text-2xl"></i>
                </button>

                <h1 class="font-semibold text-lg text-gray-800">@yield('title')</h1>
            </div>

            <!-- Usuário -->
            <div class="relative">
                <button id="userMenuButton"
                    class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                    <div
                        class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">
                            {{ substr(auth()->user()->nome, 0, 1) }}
                        </span>
                    </div>
                    <span class="hidden sm:block font-medium">{{ auth()->user()->nome }}</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>

                <!-- Dropdown com animação -->
                <div id="userMenu"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 
                    origin-top-right transform scale-95 opacity-0 pointer-events-none transition-all duration-200 ease-out backdrop-blur-sm">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i> Sair
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Página -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

    <!-- Scripts principais -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('sidebarToggle');
            const close = document.getElementById('sidebarClose');

            const userMenuButton = document.getElementById('userMenuButton');
            const userMenu = document.getElementById('userMenu');

            /* Sidebar (mobile) */
            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            toggle?.addEventListener('click', openSidebar);
            close?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);

            /* Dropdown animado */
            function openUserMenu() {
                userMenu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                userMenu.classList.add('opacity-100', 'scale-100');
            }

            function closeUserMenu() {
                userMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                userMenu.classList.remove('opacity-100', 'scale-100');
            }

            userMenuButton?.addEventListener('click', (e) => {
                e.stopPropagation();
                if (userMenu.classList.contains('opacity-0')) {
                    openUserMenu();
                } else {
                    closeUserMenu();
                }
            });

            document.addEventListener('click', (event) => {
                if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                    closeUserMenu();
                }
            });
        });
    </script>

    @stack('scripts')

    <!-- jQuery e Máscaras -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>
        $(function () {
            // Máscaras de input
            $('#valor').mask('#.##0,00', { reverse: true });
            $('#nota_fiscal').mask('0000/000000');
            $('#data_vencimento').mask('00/00/0000');

            // Converte formato BR antes de enviar
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

    <script src="{{ asset('js/masks.js') }}"></script>
</body>
</html>
