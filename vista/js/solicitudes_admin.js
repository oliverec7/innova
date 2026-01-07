$(document).ready(function () {
    cargarEstadisticas();
    cargarSolicitudesRecibidas();
});

function obtenerTotalSolicitudes() {
    $.ajax({
        url: "modelo/solicitudes_admin.php",
        type: "POST",
        data: { accion: "total_solicitudes" },
        dataType: "json",
        success: function (response) {
            if (response && response.total !== undefined) {
                $("#totalSolicitudes").text(response.total);
            } else {
                console.error("No se pudo obtener la cantidad de solicitudes:", response.msg || "Respuesta inválida");
                $("#totalSolicitudes").text("0");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            console.error("Respuesta del servidor:", xhr.responseText);
            $("#totalSolicitudes").text("0");
        }
    });
}

function obtenerSolicitudesPendientes() {
    $.ajax({
        url: "modelo/solicitudes_admin.php",
        type: "POST",
        data: { accion: "solicitudes_pendientes" },
        dataType: "json",
        success: function (response) {
            if (response && response.total !== undefined) {
                $("#solicitudesPendientes").text(response.total);
            } else {
                console.error("No se pudo obtener la cantidad de solicitudes:", response.msg || "Respuesta inválida");
                $("#solicitudesPendientes").text("0");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            console.error("Respuesta del servidor:", xhr.responseText);
            $("#solicitudesPendientes").text("0");
        }
    });
}

function obtenerSolicitudesAprobadas() {
    $.ajax({
        url: "modelo/solicitudes_admin.php",
        type: "POST",
        data: { accion: "solicitudes_aprobadas" },
        dataType: "json",
        success: function (response) {
            if (response && response.total !== undefined) {
                $("#solicitudesAprobadas").text(response.total);
            } else {
                console.error("No se pudo obtener las solicitudes aprobadas:", response.msg || "Respuesta inválida");
                $("#solicitudesPendientes").text("0");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            console.error("Respuesta del servidor:", xhr.responseText);
            $("#solicitudesAprobadas").text("0");
        }
    });
}

function obtenerSolicitudesRechazadas() {
    $.ajax({
        url: "modelo/solicitudes_admin.php",
        type: "POST",
        data: { accion: "solicitudes_rechazadas" },
        dataType: "json",
        success: function (response) {
            if (response && response.total !== undefined) {
                $("#solicitudesRechazadas").text(response.total);
            } else {
                console.error("No se pudo obtener las solicitudes rechazadas:", response.msg || "Respuesta inválida");
                $("#solicitudesRechazadas").text("0");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            console.error("Respuesta del servidor:", xhr.responseText);
            $("#solicitudesRechazadas").text("0");
        }
    });
}

function cargarEstadisticas() {
    obtenerTotalSolicitudes();
    obtenerSolicitudesPendientes();
    obtenerSolicitudesAprobadas();
    obtenerSolicitudesRechazadas();
}

function cargarSolicitudesRecibidas() {
    $.post('modelo/solicitudes_admin.php', { accion: 'listar_solicitudes' }, function (data) {
        // Destruir DataTable si existe ANTES de modificar el HTML
        if ($.fn.DataTable.isDataTable('#tablaSolicitudes')) {
            $('#tablaSolicitudes').DataTable().clear().destroy();
        }

        let filas = '';
        
        // Verificar si hay error en la respuesta
        if (data.status === 'error') {
            Swal.fire('Error', data.msg, 'error');
            return;
        }
        
        if (Array.isArray(data) && data.length > 0) {
            data.forEach((s, i) => {
                const idSolicitud = s.id || s.idSolicitud || s.Id_Solicitud || s.ID;
                const estado = s.Estado || s.estado || '';
                
                // Determinar si la solicitud está pendiente
                const esPendiente = estado.toLowerCase().includes('pendiente') || 
                                   estado.toLowerCase() === 'pendiente';
                
                // Generar botones según el estado
                let botones = '';
                if (esPendiente) {
                    botones = `
                        <button class="btn btn-sm btn-success" title="Aceptar" onclick="aceptarSolicitud(${idSolicitud})">
                            <i class="fas fa-check-circle"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" title="Rechazar" onclick="rechazarSolicitud(${idSolicitud})">
                            <i class="fas fa-times-circle"></i>
                        </button>`;
                } else {
                    // Solicitud resuelta - botones deshabilitados
                    botones = `
                        <button class="btn btn-sm btn-outline-success" title="Solicitud resuelta" disabled>
                            <i class="fas fa-check-circle"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Solicitud resuelta" disabled>
                            <i class="fas fa-times-circle"></i>
                        </button>`;
                }
                let badgeClass = '';
                
                switch(s.Estado) {
                    case 'Pendiente':
                        badgeClass = 'bg-warning text-dark';
                        break;
                    case 'Aprobada':
                        badgeClass = 'bg-success text-white';
                        break;
                    case 'Rechazada':
                        badgeClass = 'bg-danger text-white';
                        break;
                    default:
                        badgeClass = 'bg-secondary text-white';
                }

                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${s.Equipo}</td>
                    <td>${s.Solicitante}</td>
                    <td>${s.Fecha_Solicitud}</td>
                    <td><span class="badge ${badgeClass}">${s.Estado}</span></td>
                    <td>${s.Razon}</td>
                    <td>
                        ${botones}
                    </td>
                </tr>`;
            });

            $('#tablaSolicitudes tbody').html(filas);

            // Inicializar DataTable SOLO cuando hay datos
            $('#tablaSolicitudes').DataTable({
                scrollX: true,
                scrollCollapse: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                pageLength: 10,
                fixedColumns: {
                    rightColumns: 1
                },
                columnDefs: [
                    { orderable: false, targets: 6 }
                ]
            });
        } else {
            // Si no hay datos, solo mostrar mensaje SIN inicializar DataTable
            filas = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-inbox fa-2x mb-2 text-muted"></i>
                        <p class="text-muted mb-0">No hay solicitudes pendientes</p>
                    </td>
                </tr>`;
            $('#tablaSolicitudes tbody').html(filas);
        }

    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Error en la petición:', textStatus, errorThrown);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo cargar la lista de solicitudes. Intente nuevamente.',
            footer: '<small>Detalles técnicos: ' + textStatus + '</small>'
        });
    });
}

function aceptarSolicitud(id) {
    Swal.fire({
        title: '¿Aprobar solicitud?',
        text: "Esta acción aprobará la solicitud y asignará el equipo al usuario",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check"></i> Sí, aprobar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.post('modelo/solicitudes_admin.php', {
                accion: 'procesar_solicitud',
                idSolicitud: id,
                estado: 'Aprobada'
            }, function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Aprobada!',
                        text: response.msg,
                        timer: 2000,
                        showConfirmButton: false
                    });
                        cargarEstadisticas();
                        cargarSolicitudesRecibidas();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.msg
                    });
                }
            }, 'json').fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo procesar la solicitud. Intente nuevamente.'
                });
            });
        }
    });
}

function rechazarSolicitud(id) {
    Swal.fire({
        title: '¿Rechazar solicitud?',
        text: "Esta acción rechazará la solicitud permanentemente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-times"></i> Sí, rechazar',
        cancelButtonText: '<i class="fas fa-ban"></i> Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.post('modelo/solicitudes_admin.php', {
                accion: 'procesar_solicitud',
                idSolicitud: id,
                estado: 'Rechazada'
            }, function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Rechazada',
                        text: response.msg,
                        timer: 2000,
                        showConfirmButton: false
                    });
                        cargarEstadisticas();
                        cargarSolicitudesRecibidas();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.msg
                    });
                }
            }, 'json').fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo procesar la solicitud. Intente nuevamente.'
                });
            });
        }
    });
}