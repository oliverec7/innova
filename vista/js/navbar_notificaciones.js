document.addEventListener("DOMContentLoaded", function() {
    cargarNotificacionesNavbar();

    setInterval(cargarNotificacionesNavbar, 60000);
});

function cargarNotificacionesNavbar() {
    $.post('modelo/notificaciones_admin.php', { accion: 'listar' }, function (res) {
        
        const contenedor = $('#contenedor-notificaciones-navbar');
        const badge = $('.badge-counter');
        
        if (res.status === 'success' && Array.isArray(res.data)) {
            
            const noLeidas = res.data.filter(n => n.estado_notificacion === 'No leída').length;
            
            if (noLeidas > 0) {
                badge.text(noLeidas > 9 ? '9+' : noLeidas);
                badge.show();
            } else {
                badge.hide();
            }

            const ultimasNotificaciones = res.data.slice(0, 4);
            
            let html = '';

            if (ultimasNotificaciones.length > 0) {
                ultimasNotificaciones.forEach(row => {
                    
                    let iconClass = 'fa-bell';
                    let bgClass = 'bg-secondary';

                    switch(row.tipo_notificacion) {
                        case 'Solicitud Resuelta':
                            iconClass = 'fa-clipboard-check';
                            bgClass = 'bg-success';
                            break;
                        case 'Solicitud Generada':
                            iconClass = 'fa-file-invoice';
                            bgClass = 'bg-primary'; 
                            break;
                        case 'Asignacion Tecnico':
                            iconClass = 'fa-user-cog';
                            bgClass = 'bg-warning';
                            break;
                    }

                    const fontWeight = row.estado_notificacion === 'No leída' ? 'fw-bold' : 'fw-normal';
                    const bgItem = row.estado_notificacion === 'No leída' ? 'background-color: #f8f9fa;' : '';

                    html += `
                    <a class="dropdown-item d-flex align-items-center p-3 border-bottom" href="#" style="${bgItem}">
                        <div class="me-3">
                            <div class="icon-circle ${bgClass} text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                <i class="fas ${iconClass}"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">${row.fecha}</div>
                            <span class="${fontWeight} text-dark" style="font-size: 0.85rem; display: block; white-space: normal;">
                                ${cortarTexto(row.mensaje, 50)}
                            </span>
                        </div>
                    </a>
                    `;
                });
            } else {
                html = '<div class="text-center py-4 text-muted small">No tienes notificaciones</div>';
            }

            contenedor.html(html);

        } else {
            console.error("Error al cargar notificaciones del navbar");
        }

    }, 'json');
}

function cortarTexto(texto, max) {
    if (texto.length > max) {
        return texto.substring(0, max) + '...';
    }
    return texto;
}