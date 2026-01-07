document.addEventListener("DOMContentLoaded", function () {
    obtenerEquiposCriticos();
    obtenerInspeccionesAtrasadas();
});

function obtenerEquiposCriticos() {
    $.ajax({
        url: "modelo/alertas_riesgos.php",
        type: "POST",
        data: { accion: "EquiposCriticos" },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success") {
                const data = response.data;
                
                const labels = data.map(item => item.nombre_equipo + ' (' + item.codigo_patrimonial + ')');
                const values = data.map(item => item.total_fallas);
                
                if (data.length > 0) {
                    dibujarGraficoCriticos(labels, values);
                } else {
                    const ctx = document.getElementById('CriticosChart');
                    if (ctx) ctx.parentElement.innerHTML = '<p class="text-center mt-5 text-success"> ¡Ningún equipo supera el umbral de criticidad!</p>';
                }

            } else {
                console.error("Error al obtener equipos críticos:", response.msg || "Respuesta inválida");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX para EquiposCriticos:", status, error);
        }
    });
}

function dibujarGraficoCriticos(labels, values) {
    const ctx = document.getElementById('CriticosChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'horizontalBar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Fallas Reportadas (90 días)',
                data: values,
                backgroundColor: '#e74a3b',
                hoverBackgroundColor: '#d94132',
                borderColor: '#e74a3b',
                borderWidth: 1,
            }],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                xAxes: [{
                    ticks: {
                        min: 3,
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
                        },
                        beginAtZero: true
                    },
                    gridLines: {
                        display: true,
                        drawBorder: true
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Número de Mantenimientos "No Funcional"'
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    maxBarThickness: 50,
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                titleFontSize: 14,
                bodyFontSize: 12,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        return 'Fallas: ' + tooltipItem.xLabel;
                    }
                }
            }
        }
    });
}

function obtenerInspeccionesAtrasadas() {
    $.ajax({
        url: "modelo/alertas_riesgos.php",
        type: "POST",
        data: { accion: "InspeccionesAtrasadas" },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success") {
                const data = response.data;
                
                const labels = data.map(item => item.nombre_area);
                const values = data.map(item => item.inspecciones_atrasadas);
                
                if (data.length > 0) {
                    dibujarGraficoAtrasadas(labels, values);
                } else {
                    const ctx = document.getElementById('AtrasadasChart');
                    if (ctx) ctx.parentElement.innerHTML = '<p class="text-center mt-5 text-success"> ¡Todas las inspecciones están al día o en proceso!</p>';
                }

            } else {
                console.error("Error al obtener inspecciones atrasadas:", response.msg || "Respuesta inválida");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX para InspeccionesAtrasadas:", status, error);
        }
    });
}

function dibujarGraficoAtrasadas(labels, values) {
    const ctx = document.getElementById('AtrasadasChart').getContext('2d');
    
    const baseColors = [
        '#f6c23e', '#fd7e14', '#e74a3b', '#4e73df', '#36b9cc', 
        '#1cc88a', '#858796', '#5a5c69', '#3b5998', '#ff6384'
    ];
    
    const backgroundColors = values.map((_, index) => baseColors[index % baseColors.length]);

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: backgroundColors, 
                hoverBackgroundColor: backgroundColors.map(c => c + 'AA'),
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
                displayColors: true,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, data) {
                        const dataset = data.datasets[tooltipItem.datasetIndex];
                        const total = dataset.data.reduce((acc, value) => acc + value, 0);
                        const currentValue = dataset.data[tooltipItem.index];
                        const percentage = ((currentValue/total) * 100).toFixed(1) + "%";
                        return data.labels[tooltipItem.index] + ': ' + currentValue + ' (' + percentage + ')';
                    }
                }
            },
            legend: {
                display: true,
                position: 'bottom'
            },
            cutoutPercentage: 0,
        }
    });
}