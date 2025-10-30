@extends('layouts.app')

@section('title', 'Calendário de Agendamentos')

@section('content')
<div class="p-4 sm:p-6 space-y-6">
    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-800 text-center sm:text-left">Calendário de Agendamentos</h1>

        <a href="{{ route('calendario.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 w-full sm:w-auto">
            <i class="fas fa-plus"></i> Novo Agendamento
        </a>
    </div>

    <!-- Calendário -->
    <div id="calendar" class="bg-white rounded-lg shadow border border-gray-200 p-3 sm:p-4 overflow-hidden"></div>
</div>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- Context Menu -->
<div id="context-menu"
     class="hidden fixed bg-white shadow-lg rounded-lg border border-gray-200 z-50 w-44 sm:w-48">
    <ul class="text-sm text-gray-700">
        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center gap-2" id="verDetalhes">
            🔍 <span>Ver detalhes</span>
        </li>
        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center gap-2" id="editar">
            ✏️ <span>Editar</span>
        </li>
        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-red-600 flex items-center gap-2" id="excluir">
            🗑️ <span>Excluir</span>
        </li>
    </ul>
</div>

<!-- Modal Detalhes -->
<div id="modal-agendamento"
     class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 p-4 sm:p-0">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative animate-fadeIn">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center sm:text-left" id="modal-titulo"></h2>

        <div class="space-y-2 text-gray-700 text-sm sm:text-base">
            <p><strong>Cliente:</strong> <span id="modal-cliente"></span></p>
            <p><strong>Data e Hora:</strong> <span id="modal-data"></span></p>
            <p><strong>Status:</strong> <span id="modal-status"></span></p>
            <p><strong>Tipo:</strong> <span id="modal-tipo"></span></p>
            <p><strong>Observações:</strong> <span id="modal-observacoes"></span></p>
        </div>

        <div class="mt-6 flex justify-end">
            <button id="modal-fechar"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">Fechar</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const modal = document.getElementById('modal-agendamento');
    const contextMenu = document.getElementById('context-menu');
    let selectedEvent = null;

    modal.classList.add('hidden');
    contextMenu.classList.add('hidden');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        height: 'auto',
        expandRows: true,
        aspectRatio: 1.5,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            day: 'Dia',
            list: 'Lista'
        },
        events: @json($agendamentos),

        eventContent: function(arg) {
            const title = arg.event.title.length > 20 
                ? arg.event.title.slice(0, 20) + '...' 
                : arg.event.title;

            return {
                html: `
                    <div class="text-xs sm:text-sm font-medium truncate"
                         style="background-color: ${arg.event.backgroundColor || '#2563eb'};
                                color: white;
                                border-radius: 4px;
                                padding: 2px 4px;
                                line-height: 1.3;">
                        ${title}
                    </div>`
            };
        },

        eventDidMount: function(info) {
            info.el.addEventListener('contextmenu', function (e) {
                e.preventDefault();
                selectedEvent = info.event;
                const { clientX, clientY } = e;

                // Posiciona menu com limite de tela
                const offsetX = Math.min(clientX, window.innerWidth - 200);
                const offsetY = Math.min(clientY, window.innerHeight - 150);

                contextMenu.style.left = offsetX + 'px';
                contextMenu.style.top = offsetY + 'px';
                contextMenu.classList.remove('hidden');
            });

            // Clique direto em mobile abre detalhes
            info.el.addEventListener('click', function () {
                if (window.innerWidth < 768) {
                    selectedEvent = info.event;
                    abrirModal(selectedEvent);
                }
            });
        }
    });

    calendar.render();

    // Ajuste de responsividade
    function ajustarCalendario() {
        if (window.innerWidth < 640) {
            calendar.changeView('listWeek');
            calendar.setOption('aspectRatio', 0.8);
        } else {
            calendar.changeView('dayGridMonth');
            calendar.setOption('aspectRatio', 1.5);
        }
    }
    ajustarCalendario();
    window.addEventListener('resize', ajustarCalendario);

    // Fecha menu ao clicar fora
    window.addEventListener('click', (e) => {
        if (!contextMenu.contains(e.target)) contextMenu.classList.add('hidden');
    });

    // Função para abrir modal
    function abrirModal(evento) {
        if (!evento) return;

        const dataFormatada = new Date(evento.start).toLocaleString('pt-BR', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        document.getElementById('modal-titulo').textContent = evento.title;
        document.getElementById('modal-cliente').textContent = evento.extendedProps.cliente ?? 'Não informado';
        document.getElementById('modal-data').textContent = dataFormatada;
        document.getElementById('modal-status').textContent = evento.extendedProps.status ?? 'Pendente';
        document.getElementById('modal-tipo').textContent = evento.extendedProps.tipo ?? '-';
        document.getElementById('modal-observacoes').textContent =
            evento.extendedProps.observacoes?.trim() || 'Nenhuma observação.';

        modal.classList.remove('hidden');
    }

    // Ver detalhes
    document.getElementById('verDetalhes').addEventListener('click', () => {
        contextMenu.classList.add('hidden');
        abrirModal(selectedEvent);
    });

    // Editar
    document.getElementById('editar').addEventListener('click', () => {
        contextMenu.classList.add('hidden');
        if (selectedEvent) {
            window.location.href = `/calendario/${selectedEvent.id}/edit`;
        }
    });

    // Excluir
    document.getElementById('excluir').addEventListener('click', () => {
        contextMenu.classList.add('hidden');
        if (selectedEvent && confirm('Tem certeza que deseja excluir este agendamento?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/calendario/${selectedEvent.id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Fechar modal
    document.getElementById('modal-fechar').addEventListener('click', () => modal.classList.add('hidden'));
    window.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });
});
</script>

<style>
/* === Estilos de Responsividade e Aparência === */
#calendar {
    width: 100%;
    min-width: 100%;
    overflow-x: auto;
}

/* Toolbar */
.fc-toolbar {
    flex-wrap: wrap !important;
    justify-content: center !important;
    gap: 0.5rem;
}

/* Título */
.fc-toolbar-title {
    font-size: 1rem;
    font-weight: 600;
    text-align: center;
}

/* Botões menores no mobile */
@media (max-width: 640px) {
    .fc-toolbar-chunk button {
        padding: 0.3rem 0.5rem !important;
        font-size: 0.75rem !important;
    }

    .fc-toolbar-title {
        font-size: 0.9rem !important;
    }

    #calendar {
        padding: 0.5rem;
    }
}

/* Animação modal */
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.97); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
    animation: fadeIn 0.2s ease-in-out;
}
</style>
@endsection
