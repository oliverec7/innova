document.addEventListener("DOMContentLoaded", function () {
    obtenerDistribucionEquiposArea();
});

function obtenerDistribucionEquiposArea() {
    $.ajax({
        url: "modelo/inicio.php",
        type: "POST",
        data: { accion: "EquiposPorArea" },
        dataType: "json",
        success: function (data) {
            if (data && data.status !== "error") {
                const nombresArea = data.map(item => item.nombre_area);
                const siglas = data.map(item => item.sigla);
                const totales = data.map(item => item.total);
                dibujarGraficoEquiposArea(nombresArea, siglas, totales);
            } else {
                console.error("Error al obtener distribución de equipos por área:", data.msg || "Respuesta inválida");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX para gráfico de áreas:", status, error);
        }
    });
}

function dibujarGraficoEquiposArea(nombresArea, siglas, data) {
    const ctx = document.getElementById('EquiposAreaChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: siglas, // Mostrar siglas en el eje X
            datasets: [{
                label: 'Total Equipos',
                data: data,
                backgroundColor: '#4e73df',
                hoverBackgroundColor: '#3c64c7',
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        min: 0
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    title: function(tooltipItem, data) {
                        // Mostrar el nombre completo del área en el tooltip
                        const index = tooltipItem[0].index;
                        return nombresArea[index];
                    },
                    label: function(tooltipItem, data) {
                        return 'Total Equipos: ' + tooltipItem.yLabel;
                    }
                }
            }
        },
    });
}