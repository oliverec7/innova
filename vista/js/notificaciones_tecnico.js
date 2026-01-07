let listaNotificaciones = [];

$(document).ready(function () {
    
    cargarNotificaciones();

    $('#filtroEstado').on('change', function() {
        aplicarFiltros();
    });

    $(document).on('click', '.btn-marcar-estado', function () { 
        const idNotificacion = $(this).data('id');
        const nuevoEstado = $(this).data('nuevo-estado');
        cambiarEstadoNotificacion(idNotificacion, nuevoEstado);
    });

    // Nuevo evento: Manejar el clic en el botón de ir a la URL de la orden
    $(document).on('click', '.btn-ir-destino', function () {
        const urlDestino = $(this).attr('href');
        const idNotificacion = $(this).data('id');

        // Opcional: Marcar la notificación como leída al hacer clic en 'Ir a...'
        if ($(this).data('estado') === 'No leída') {
            cambiarEstadoNotificacion(idNotificacion, 'Leída', true, urlDestino);
        } else {
            // Si ya está leída, simplemente navega
            window.location.href = urlDestino;
        }

        return false; // Prevenir la navegación inmediata para manejarla en cambiarEstadoNotificacion
    });
});

function cargarNotificaciones() {
    $('#contenedorNotificaciones').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `);
    $('#mensajeVacio').addClass('d-none');

    $.post('modelo/notificaciones_tecnico.php', { accion: 'listar' }, function (res) {
        
        if (res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {
            listaNotificaciones = res.data;
            aplicarFiltros(); 
        } else {
            listaNotificaciones = [];
            $('#contenedorNotificaciones').empty(); 
            $('#mensajeVacio').removeClass('d-none');
        }

    }, 'json').fail((xhr, status, error) => {
        console.error('Error:', xhr.responseText);
        $('#contenedorNotificaciones').html('<div class="text-center text-danger py-4">Error al cargar notificaciones</div>');
    });
}

function aplicarFiltros() {
    const filtro = $('#filtroEstado').val();
    
    let datosFiltrados = listaNotificaciones;

    if (filtro === 'leido') {
        datosFiltrados = listaNotificaciones.filter(n => n.estado_notificacion === 'Leída');
    } else if (filtro === 'no_leido') {
        datosFiltrados = listaNotificaciones.filter(n => n.estado_notificacion === 'No leída');
    }

    renderizarHTML(datosFiltrados);
}

function renderizarHTML(datos) {
    const contenedor = $('#contenedorNotificaciones');
    contenedor.empty(); 

    if (datos.length === 0) {
        if(listaNotificaciones.length > 0) {
            contenedor.html('<div class="text-center py-4 text-muted">No hay notificaciones con este filtro.</div>');
        } else {
            $('#mensajeVacio').removeClass('d-none');
        }
        return;
    }

    $('#mensajeVacio').addClass('d-none');

    let html = '';

    datos.forEach(row => {
        const esNoLeida = row.estado_notificacion === 'No leída';
        const claseTarjeta = esNoLeida ? 'notif-unread' : 'notif-read';
        
        let claseColor = 'icon-bg-gray'; 
        let iconoClass = 'fa-bell';
        
        let urlDestino = '#'; 
        let mostrarBotonIr = false;
        let tituloBotonIr = 'Ver detalles';

        switch(row.tipo_notificacion) {
            case 'Asignacion Tecnico':
                iconoClass = 'fa-clipboard-check';
                claseColor = 'icon-bg-green'; 
                urlDestino = 'index.php?v=InspeccionesTecnico'; 
                mostrarBotonIr = true;
                tituloBotonIr = 'Ver Asignaciones';
                break;
            default:
                iconoClass = 'fa-bell';
                claseColor = 'icon-bg-gray';
        }

        const nuevoEstado = esNoLeida ? 'Leída' : 'No leída';
        const iconoBoton = esNoLeida ? '<i class="fas fa-check"></i>' : '<i class="fas fa-envelope-open"></i>';
        const tituloBoton = esNoLeida ? 'Marcar como leída' : 'Marcar como no leída';
        const claseBtnAccion = esNoLeida ? 'active-action' : ''; 
        
        let htmlBotonIr = '';
        if (mostrarBotonIr) {
            htmlBotonIr = `
                <a href="${urlDestino}" 
                    class="btn-ir-destino text-primary" 
                    title="${tituloBotonIr}" 
                    data-id="${row.idNotificacion}"
                    data-estado="${row.estado_notificacion}"
                    data-bs-toggle="tooltip">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            `;
        }

        html += `
        <div class="notif-card ${claseTarjeta}">
            <div class="notif-icon-box ${claseColor}">
                <i class="fas ${iconoClass}"></i>
            </div>

            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="notif-badge-type">${row.tipo_notificacion}</span>
                    <span class="notif-time"><i class="far fa-clock"></i> ${row.fecha}</span>
                </div>
                <p class="notif-message">
                    ${row.mensaje}
                </p>
            </div>

            <div class="ms-3 d-flex gap-2"> 
                ${htmlBotonIr} 
                <button class="btn-action-notif btn-marcar-estado ${claseBtnAccion}" 
                    data-id="${row.idNotificacion}" 
                    data-nuevo-estado="${nuevoEstado}" 
                    title="${tituloBoton}"
                    data-bs-toggle="tooltip">
                    ${iconoBoton}
                </button>
            </div>
        </div>
        `;
    });

    contenedor.html(html);

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
}

// Se añade un parámetro opcional 'redirectUrl'
function cambiarEstadoNotificacion(id, nuevoEstado, isRedirect = false, redirectUrl = '') { 
    $.post('modelo/notificaciones_tecnico.php', { 
        accion: 'cambiar_estado', 
        idNotificacion: id,
        nuevo_estado: nuevoEstado
    }, function (res) {
        
        if (res.status === 'success') {

            const notifIndex = listaNotificaciones.findIndex(n => n.idNotificacion == id);
            if(notifIndex !== -1) {
                listaNotificaciones[notifIndex].estado_notificacion = nuevoEstado;
            }
            
            aplicarFiltros();

            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 2000
            });
            Toast.fire({ 
                icon: nuevoEstado === 'Leída' ? 'success' : 'info', 
                title: nuevoEstado === 'Leída' ? 'Marcada como leída' : 'Marcada como no leída' 
            });

            // Si la llamada fue iniciada por el botón de navegación, redirigir AHORA
            if (isRedirect && redirectUrl) {
                window.location.href = redirectUrl;
            }

        } else {
            Swal.fire('Error', res.message, 'error');
        }

    }, 'json');
}