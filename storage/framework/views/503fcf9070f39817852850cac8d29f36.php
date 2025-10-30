<?php $__env->startSection('title', 'Calendário de Agendamentos'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Calendário de Agendamentos</h1>
        <a href="<?php echo e(route('calendario.create')); ?>" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center w-full sm:w-auto">
            <i class="fas fa-plus mr-2"></i>Novo Agendamento
        </a>
    </div>

    <!-- Calendário -->
    <div id="calendar-container" class="bg-white rounded-lg shadow border border-gray-200 p-4 overflow-hidden">
        <div id="calendar"></div>
    </div>
</div>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<style>
/* 🔹 Corrige proporções no mobile */
.fc .fc-toolbar-title {
    font-size: 1rem !important;
    text-align: center;
}

.fc .fc-toolbar.fc-header-toolbar {
    flex-wrap: wrap !important;
    gap: 0.5rem;
    justify-content: center !important;
}

.fc .fc-button {
    padding: 0.35rem 0.6rem !important;
    font-size: 0.8rem !important;
}

.fc .fc-daygrid-day-number {
    font-size: 0.75rem !important;
}

@media (max-width: 640px) {
    #calendar-container {
        padding: 0.5rem !important;
    }
    .fc .fc-toolbar-title {
        font-size: 0.9rem !important;
    }
}
</style>

<!-- Context Menu -->
<div id="context-menu" class=" fixed bg-white shadow-lg rounded-lg border border-gray-200 z-50 w-44">
    <ul class="text-sm text-gray-700">
        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center gap-2" id="verDetalhes">🔍 Ver detalhes</li>
        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center gap-2" id="editar">✏️ Editar</li>
        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-red-600 flex items-center gap-2" id="excluir">🗑️ Excluir</li>
    </ul>
</div>

<!-- Modal -->
<div id="modal-agendamento" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 hidden px-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4" id="modal-titulo"></h2>
        <div class="space-y-2 text-gray-700 text-sm sm:text-base">
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
        initialView: window.innerWidth < 640 ? 'listWeek' : 'dayGridMonth',
        locale: 'pt-br',
        height: 'auto',
        aspectRatio: 1.5,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: window.innerWidth < 640 ? '' : 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            day: 'Dia',
            list: 'Lista'
        },
        events: <?php echo json_encode($agendamentos, 15, 512) ?>,

        eventContent(arg) {
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

        eventDidMount(info) {
            info.el.addEventListener('contextmenu', function (e) {
                e.preventDefault();
                selectedEvent = info.event;

                const x = e.clientX;
                const y = e.clientY;
                const menuWidth = contextMenu.offsetWidth;
                const menuHeight = contextMenu.offsetHeight;
                const screenWidth = window.innerWidth;
                const screenHeight = window.innerHeight;

                let left = x;
                let top = y;

                if (x + menuWidth > screenWidth) left = screenWidth - menuWidth - 10;
                if (y + menuHeight > screenHeight) top = screenHeight - menuHeight - 10;

                contextMenu.style.left = left + 'px';
                contextMenu.style.top = top + 'px';
                contextMenu.classList.remove('hidden');
            });
        },
    });

    calendar.render();

    // Fechar context menu
    window.addEventListener('click', (e) => {
        if (!contextMenu.contains(e.target)) contextMenu.classList.add('hidden');
    });

    // Detalhes
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
            form.innerHTML = `<?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>`;
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Fechar modal
    document.getElementById('modal-fechar').addEventListener('click', () => modal.classList.add('hidden'));
    window.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });

    // Responsividade dinâmica
    window.addEventListener('resize', () => {
        if (window.innerWidth < 640) calendar.changeView('listWeek');
        else calendar.changeView('dayGridMonth');
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/calendario/index.blade.php ENDPATH**/ ?>