document.addEventListener("DOMContentLoaded", function () {
    const { aReceber, aPagar, agendamentosHoje } = window.dashboardData;

    // 🔹 Gráfico 1 — Fluxo Financeiro (barras)
    const ctxFinance = document.getElementById("financeChart");
    if (ctxFinance) {
        new Chart(ctxFinance, {
            type: "bar",
            data: {
                labels: ["A Receber", "A Pagar"],
                datasets: [{
                    label: "Valores (R$)",
                    data: [aReceber, aPagar],
                    backgroundColor: [
                        "rgba(78, 42, 140, 0.9)", // Roxo Lart
                        "rgba(59, 31, 114, 0.9)"  // Azul escuro Lart
                    ],
                    borderRadius: 10,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: 10 },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: "#3B1F72", font: { size: 13, weight: "500" } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: "rgba(78,42,140,0.08)" },
                        ticks: { color: "#3B1F72", font: { size: 12 } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "rgba(59,31,114,0.9)",
                        titleColor: "#fff",
                        bodyColor: "#fff",
                        cornerRadius: 8,
                        padding: 10
                    }
                }
            }
        });
    }

    // 🔹 Gráfico 2 — Resumo de Agendamentos (donut)
    const ctxAg = document.getElementById("agendamentosChart");
    if (ctxAg) {
        new Chart(ctxAg, {
            type: "doughnut",
            data: {
                labels: ["Hoje", "Outros Dias"],
                datasets: [{
                    data: [agendamentosHoje, Math.max(1, 10 - agendamentosHoje)],
                    backgroundColor: [
                        "rgba(78, 42, 140, 0.9)",  // Roxo Lart
                        "rgba(213, 197, 255, 0.4)" // Lilás claro
                    ],
                    borderWidth: 0,
                    cutout: "70%"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: 20 },
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            color: "#3B1F72",
                            font: { size: 13, weight: "500" },
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: "rgba(59,31,114,0.9)",
                        titleColor: "#fff",
                        bodyColor: "#fff",
                        cornerRadius: 8,
                        padding: 10
                    }
                }
            }
        });
    }
});
