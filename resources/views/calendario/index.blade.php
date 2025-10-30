@extends('layouts.app')

@section('title', 'Calendário de Agendamentos')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Calendário de Agendamentos</h1>
        <a href="{{ route('calendario.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Novo Agendamento
        </a>
    </div>

    <div id="calendar" class="bg-white rounded-lg shadow border border-gray-200 p-4"></div>
</div>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- Context Menu -->
<div id="context-menu" class="hidden absolute bg-white shadow-lg rounded-lg border border-gray-200 z-50 w-40">
    <ul class="text-sm text-gray-700">
        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer" id="verDetalhes">🔍 Ver detalhes</li>
        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer" id="editar">✏️ Editar</li>
        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-red-600" id="excluir">🗑️ Excluir</li>
    </ul>
</div>

<!-- Modal -->
<div id="modal-agendamento" class=" fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4" id="modal-titulo"></h2>
        <div class="space-y-2 text-gray-700">
            <p><strong>Cliente:</strong> <span id="modal-cliente"></span></p>
            <p><strong>Data e Hora:</strong> <span id="modal-data"></span></p>
            <p><strong>Status:</strong> <span id="modal-status"></span></p>
            <p><strong>Tipo:</strong> <span id="modal-tipo"></span></p>
            <p><strong>Observações:</strong> <span id="modal-observacoes"></span></p>
        </div>
        <div class="mt-6 flex justify-end">
            <button id="modal-fechar"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Fechar</button>
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
            const title = arg.event.title.length > 22 
                ? arg.event.title.slice(0, 22) + '...' 
                : arg.event.title;

            return {
                html: `
                    <div class="text-xs font-medium truncate" 
                         style="background-color: ${arg.event.backgroundColor || '#2563eb'};
                                color: white;
                                border-radius: 4px;
                                padding: 2px 4px;
                                line-height: 1.3;">
                        ${title}
                    </div>
                `
            };
        },

        eventDidMount: function(info) {
            info.el.addEventListener('contextmenu', function (e) {
                e.preventDefault();
                selectedEvent = info.event;
                contextMenu.classList.remove('hidden');
                contextMenu.style.left = e.pageX + 'px';
                contextMenu.style.top = e.pageY + 'px';
            });
        }
    });

    calendar.render();

    // Fecha menu ao clicar fora
    window.addEventListener('click', (e) => {
        if (!contextMenu.contains(e.target)) contextMenu.classList.add('hidden');
    });

    // Ver detalhes do evento
    document.getElementById('verDetalhes').addEventListener('click', () => {
        contextMenu.classList.add('hidden');
        if (!selectedEvent) return;

        const dataFormatada = new Date(selectedEvent.start).toLocaleString('pt-BR', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        document.getElementById('modal-titulo').textContent = selectedEvent.title;
        document.getElementById('modal-cliente').textContent = selectedEvent.extendedProps.cliente ?? 'Não informado';
        document.getElementById('modal-data').textContent = dataFormatada;
        document.getElementById('modal-status').textContent = selectedEvent.extendedProps.status ?? 'Pendente';
        document.getElementById('modal-tipo').textContent = selectedEvent.extendedProps.tipo ?? selectedEvent.title;
        document.getElementById('modal-observacoes').textContent =
            selectedEvent.extendedProps.observacoes && selectedEvent.extendedProps.observacoes.trim() !== ''
                ? selectedEvent.extendedProps.observacoes
                : 'Nenhuma observação.';
        
        modal.classList.remove('hidden');
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
@endsection
