document.addEventListener('DOMContentLoaded', () => {
    const { aReceber, aPagar, agendamentosHoje } = window.dashboardData;

    // === Gráfico Financeiro ===
    const ctx1 = document.getElementById('financeChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['A Receber', 'A Pagar'],
                datasets: [{
                    label: 'Valores (R$)',
                    data: [aReceber, aPagar],
                    backgroundColor: ['#22c55e', '#ef4444'],
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `R$ ${ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#555' } },
                    x: { ticks: { color: '#555' } }
                }
            }
        });
    }

    // === Gráfico de Agendamentos ===
    const ctx2 = document.getElementById('agendamentosChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Hoje', 'Outros Dias'],
                datasets: [{
                    data: [agendamentosHoje, Math.max(10 - agendamentosHoje, 0)],
                    backgroundColor: ['#3b82f6', '#cbd5e1'],
                    hoverOffset: 8
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
