document.addEventListener("DOMContentLoaded", function () {
    obtenerCumplimientoInspecciones();
    obtenerSolicitudesStatus();
});

function obtenerCumplimientoInspecciones() {
    $.ajax({
        url: "modelo/kpis_principales.php",
        type: "POST",
        data: { accion: "CumplimientoInspecciones" },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success") {
                const finalizadas = response.finalizadas;
                const programadas = response.programadas;
                const no_finalizadas = programadas - finalizadas;
                const porcentaje = response.porcentaje;
                
                if (programadas > 0) {
                    dibujarGraficoInspecciones(finalizadas, no_finalizadas, porcentaje);
                } else {
                    const ctx = document.getElementById('InspeccionesChart');
                    if (ctx) ctx.parentElement.innerHTML = '<p class="text-center mt-5">No hay inspecciones programadas para mostrar.</p>';
                }

            } else {
                console.error("Error al obtener cumplimiento de inspecciones:", response.msg || "Respuesta inválida");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX para CumplimientoInspecciones:", status, error);
        }
    });
}

function dibujarGraficoInspecciones(finalizadas, no_finalizadas, porcentaje) {
    const ctx = document.getElementById('InspeccionesChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Realizadas', 'Pendientes/No Realizadas'],
            datasets: [{
                data: [finalizadas, no_finalizadas],
                backgroundColor: ['#1cc88a', '#e74a3b'],
                hoverBackgroundColor: ['#17a673', '#d94132'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, data) {
                        const dataset = data.datasets[tooltipItem.datasetIndex];
                        const total = dataset.data.reduce((acc, value) => acc + value, 0);
                        const currentValue = dataset.data[tooltipItem.index];
                        const percentage = ((currentValue/total) * 100).toFixed(2) + "%";
                        return data.labels[tooltipItem.index] + ': ' + currentValue + ' (' + percentage + ')';
                    }
                }
            },
            legend: {
                display: true,
                position: 'bottom'
            },
            cutoutPercentage: 80,
            plugins: {
                beforeDraw: function(chart) {
                    const width = chart.chart.width,
                        height = chart.chart.height,
                        ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    const text = porcentaje + "%",
                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                        textY = height / 2;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }
        },
        plugins: [{
            beforeDraw: function(chart) {
                const width = chart.chart.width,
                    height = chart.chart.height,
                    ctx = chart.chart.ctx;
                ctx.restore();
                const fontSize = (height / 114).toFixed(2);
                ctx.font = fontSize + "em sans-serif";
                ctx.textBaseline = "middle";
                const text = porcentaje + "%",
                    textX = Math.round((width - ctx.measureText(text).width) / 2),
                    textY = height / 2;
                ctx.fillStyle = '#4e73df';
                ctx.fillText(text, textX, textY);
                ctx.save();
            }
        }]
    });
}

function obtenerSolicitudesStatus() {
    $.ajax({
        url: "modelo/kpis_principales.php",
        type: "POST",
        data: { accion: "SolicitudesStatus" },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success") {
                const pendientes = response.pendientes;
                const resueltas = response.resueltas;
                
                dibujarGraficoSolicitudes(pendientes, resueltas);
            } else {
                console.error("Error al obtener estado de solicitudes:", response.msg || "Respuesta inválida");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX para SolicitudesStatus:", status, error);
        }
    });
}

function dibujarGraficoSolicitudes(pendientes, resueltas) {
    const ctx = document.getElementById('SolicitudesChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pendientes', 'Resueltas'],
            datasets: [{
                label: 'Total Solicitudes',
                data: [pendientes, resueltas],
                backgroundColor: ['#f6c23e', '#36b9cc'],
                hoverBackgroundColor: ['#f4b619', '#2c9faf'],
                borderColor: '#dddfeb',
                borderWidth: 1,
            }],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                xAxes: [{
                    time: {
                        unit: 'Estado'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    maxBarThickness: 50,
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
                        },
                        beginAtZero: true
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        const datasetLabel = chart.datasets[0].label || '';
                        return datasetLabel + ': ' + tooltipItem.yLabel;
                    }
                }
            },
        }
    });
}