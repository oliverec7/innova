document.addEventListener("DOMContentLoaded", function () {
    obtenerFallasMensuales();
    obtenerSolicitudesArea();
});

const nombresMeses = [
    'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 
    'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
];

function obtenerFallasMensuales() {
    $.ajax({
        url: "modelo/tendencias.php",
        type: "POST",
        data: { accion: "FallasMensuales" },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success") {
                const labels = response.labels;
                const values = response.values;
                
                if (values.some(v => v > 0)) {
                    dibujarGraficoFallasMensuales(labels, values);
                } else {
                    const ctx = document.getElementById('FallasMensualesChart');
                    if (ctx) ctx.parentElement.innerHTML = '<p class="text-center mt-5 text-success">No hay fallas de equipo registradas como "No Funcional" en el último año.</p>';
                }

            } else {
                console.error("Error al obtener tendencias de fallas:", response.msg || "Respuesta inválida");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX para FallasMensuales:", status, error);
        }
    });
}

function dibujarGraficoFallasMensuales(labels, values) {
    const ctx = document.getElementById('FallasMensualesChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Fallas (No Funcional)',
                data: values,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: '#4e73df',
                pointRadius: 3,
                pointBackgroundColor: '#4e73df',
                pointHoverRadius: 3,
                pointHitRadius: 10,
                pointBorderWidth: 2,
                tension: 0.4,
            }],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            layout: {
                padding: { left: 10, right: 25, top: 25, bottom: 0 }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 12
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
                        }
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
                        return 'Fallas: ' + tooltipItem.yLabel;
                    }
                }
            }
        }
    });
}

function obtenerSolicitudesArea() {
    $.ajax({
        url: "modelo/tendencias.php",
        type: "POST",
        data: { accion: "SolicitudesArea" },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success") {
                const data = response.data;
                
                const labels = data.map(item =>  item.nombre_area);
                const values = data.map(item => item.total_solicitudes);
                
                if (data.length > 0) {
                    dibujarGraficoSolicitudesArea(labels, values);
                } else {
                    const ctx = document.getElementById('SolicitudesAreaChart');
                    if (ctx) ctx.parentElement.innerHTML = '<p class="text-center mt-5 text-warning">No hay solicitudes registradas aún.</p>';
                }

            } else {
                console.error("Error al obtener distribución de solicitudes por área:", response.msg || "Respuesta inválida");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX para SolicitudesArea:", status, error);
        }
    });
}

function dibujarGraficoSolicitudesArea(labels, values) {
    const ctx = document.getElementById('SolicitudesAreaChart').getContext('2d');
    
    const baseColors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
        '#858796', '#5a5c69', '#fd7e14', '#6f42c1', '#20c997'
    ];
    
    const backgroundColors = values.map((_, index) => baseColors[index % baseColors.length]);

    new Chart(ctx, {
        type: 'doughnut',
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
            cutoutPercentage: 80,
        }
    });
}