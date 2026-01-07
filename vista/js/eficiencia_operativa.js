document.addEventListener("DOMContentLoaded", function () {
    obtenerCargaTecnico();
    obtenerTiempoRespuesta();
});

// Obtiene la carga de trabajo activa por técnico desde el servidor.
function obtenerCargaTecnico() {
    $.ajax({
        url: "modelo/eficiencia_operativa.php",
        type: "POST",
        data: { accion: "CargaTecnico" },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success") {
                const data = response.data;
                
                const labels = data.map(item => item.nombre_completo);
                const values = data.map(item => item.total_inspecciones_activas);
                
                if (data.length > 0) {
                    dibujarGraficoCargaTecnico(labels, values);
                } else {
                    const ctx = document.getElementById('CargaTecnicoChart');
                    if (ctx) ctx.parentElement.innerHTML = '<p class="text-center mt-5">No hay órdenes de trabajo activas asignadas actualmente.</p>';
                }
            }
        }
    });
}

// Dibuja el gráfico de barras para la Carga de Trabajo por Técnico
function dibujarGraficoCargaTecnico(labels, values) {
    const ctx = document.getElementById('CargaTecnicoChart').getContext('2d');
    
    const baseColors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
        '#858796', '#f39c12', '#9b59b6', '#3498db', '#2ecc71'
    ];
    
    const backgroundColors = values.map((_, index) => baseColors[index % baseColors.length]);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Inspecciones Activas',
                data: values,
                backgroundColor: backgroundColors,
                borderColor: baseColors[0],
                borderWidth: 1,
            }],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    maxBarThickness: 70,
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
                titleFontSize: 14,
                bodyFontSize: 12,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        return 'Asignadas: ' + tooltipItem.yLabel;
                    }
                }
            }
        }
    });
}

// Dibuja un gráfico gauge con canvas puro
function dibujarGaugeCanvas(promedioHoras) {
    const canvas = document.getElementById('TiempoRespuestaChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext("2d");
    const W = canvas.width;
    const H = canvas.height;
    
    if (W === 0 || H === 0) {
        canvas.width = 400;
        canvas.height = 300;
        return dibujarGaugeCanvas(promedioHoras);
    }
    
    const maxHoras = 72;
    const porcentaje = Math.min((promedioHoras / maxHoras) * 100, 100);
    const grados = (porcentaje / 100) * 180;
    
    let colorGauge, clasificacion;
    
    if (promedioHoras <= 24) {
        colorGauge = "#1cc88a";
        clasificacion = "EXCELENTE";
    } else if (promedioHoras <= 48) {
        colorGauge = "#f6c23e";
        clasificacion = "BUENO";
    } else {
        colorGauge = "#e74a3b";
        clasificacion = "REGULAR";
    }
    
    const bgColor = "#e9ecef";
    const textColor = "#2e3a59";
    const centerX = W / 2;
    const centerY = H * 0.45;
    const radius = Math.min(W, H) * 0.3;
    const lineWidth = radius * 0.15;
    
    ctx.clearRect(0, 0, W, H);
    
    // Fondo del gauge
    ctx.beginPath();
    ctx.strokeStyle = bgColor;
    ctx.lineWidth = lineWidth;
    ctx.lineCap = "round";
    ctx.arc(centerX, centerY, radius, Math.PI, 0, false);
    ctx.stroke();
    
    // Zonas de color
    const gradosVerde = Math.min((24 / maxHoras) * 180, grados);
    const gradosAmarillo = Math.min((48 / maxHoras) * 180, grados) - (24 / maxHoras) * 180;
    const gradosRojo = Math.min((72 / maxHoras) * 180, grados) - (48 / maxHoras) * 180;
    
    if (gradosVerde > 0) {
        ctx.beginPath();
        ctx.strokeStyle = "#1cc88a";
        ctx.lineWidth = lineWidth;
        ctx.lineCap = "round";
        ctx.arc(centerX, centerY, radius, Math.PI, Math.PI + (gradosVerde * Math.PI / 180), false);
        ctx.stroke();
    }
    
    if (gradosAmarillo > 0) {
        const inicioAmarillo = Math.PI + ((24 / maxHoras) * 180 * Math.PI / 180);
        ctx.beginPath();
        ctx.strokeStyle = "#f6c23e";
        ctx.lineWidth = lineWidth;
        ctx.lineCap = "round";
        ctx.arc(centerX, centerY, radius, inicioAmarillo, 
                inicioAmarillo + (gradosAmarillo * Math.PI / 180), false);
        ctx.stroke();
    }
    
    if (gradosRojo > 0) {
        const inicioRojo = Math.PI + ((48 / maxHoras) * 180 * Math.PI / 180);
        ctx.beginPath();
        ctx.strokeStyle = "#e74a3b";
        ctx.lineWidth = lineWidth;
        ctx.lineCap = "round";
        ctx.arc(centerX, centerY, radius, inicioRojo, 
                inicioRojo + (gradosRojo * Math.PI / 180), false);
        ctx.stroke();
    }
    
    // Aguja
    const anguloAguja = Math.PI + (grados * Math.PI / 180);
    const longitudAguja = radius * 0.85;
    const agujaX = centerX + Math.cos(anguloAguja) * longitudAguja;
    const agujaY = centerY + Math.sin(anguloAguja) * longitudAguja;
    
    ctx.beginPath();
    ctx.moveTo(centerX, centerY);
    ctx.lineTo(agujaX, agujaY);
    ctx.strokeStyle = "#2e3a59";
    ctx.lineWidth = 4;
    ctx.lineCap = "round";
    ctx.stroke();
    
    ctx.beginPath();
    ctx.arc(centerX, centerY, 8, 0, Math.PI * 2, false);
    ctx.fillStyle = "#2e3a59";
    ctx.fill();
    
    // Texto - sin fuentes personalizadas
    ctx.font = "bold 24px Arial";
    ctx.fillStyle = textColor;
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText(promedioHoras.toFixed(1) + " h", centerX, centerY + radius * 0.9);
    
    ctx.font = "bold 16px Arial";
    ctx.fillStyle = colorGauge;
    ctx.fillText(clasificacion, centerX, centerY + radius * 1.3);
    
    ctx.font = "12px Arial";
    ctx.fillStyle = "#6c757d";
    ctx.fillText("Tiempo promedio de respuesta", centerX, centerY + radius * 1.6);
    
    // Leyenda
    const leyendaY = centerY + radius * 1.9;
    const anchoTotal = W * 0.8;
    const inicioX = centerX - anchoTotal / 2;
    const espaciado = anchoTotal / 3;
    
    const zonas = [
        { color: "#1cc88a", texto: "Excelente", rango: "0-24h" },
        { color: "#f6c23e", texto: "Bueno", rango: "24-48h" },
        { color: "#e74a3b", texto: "Regular", rango: "48-72h" }
    ];
    
    zonas.forEach((zona, index) => {
        const x = inicioX + (index * espaciado) + (espaciado / 2);
        
        ctx.beginPath();
        ctx.arc(x, leyendaY, 6, 0, Math.PI * 2, false);
        ctx.fillStyle = zona.color;
        ctx.fill();
        
        ctx.font = "12px Arial";
        ctx.fillStyle = "#5a5c69";
        ctx.textAlign = "center";
        ctx.fillText(zona.texto, x, leyendaY + 20);
        
        ctx.font = "10px Arial";
        ctx.fillStyle = "#858796";
        ctx.fillText(zona.rango, x, leyendaY + 35);
    });
}

// Configura el canvas y dibuja el gauge
function configurarGaugeCanvas(promedioHoras) {
    setTimeout(() => {
        const canvas = document.getElementById('TiempoRespuestaChart');
        if (!canvas) return;
        
        const container = canvas.parentElement;
        if (!container) return;
        
        const width = container.clientWidth;
        const height = container.clientHeight;
        
        canvas.width = width;
        canvas.height = height;
        
        dibujarGaugeCanvas(promedioHoras);
        
        // Redibujar al cambiar tamaño
        window.addEventListener('resize', function() {
            canvas.width = container.clientWidth;
            canvas.height = container.clientHeight;
            dibujarGaugeCanvas(promedioHoras);
        });
        
    }, 50);
}

//Obtiene el tiempo de respuesta promedio y dibuja el gauge
function obtenerTiempoRespuesta() {
    $.ajax({
        url: "modelo/eficiencia_operativa.php",
        type: "POST",
        data: { accion: "TiempoRespuesta" },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success") {
                const promedio = response.promedio_horas;
                
                const canvas = document.getElementById('TiempoRespuestaChart');
                if (canvas && canvas.getContext) {
                    configurarGaugeCanvas(promedio);
                }
            }
        }
    });
}