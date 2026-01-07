let tablaSolicitudes;

$(document).ready(function () {
    // Cargar datos iniciales
    cargarCombos();
    cargarEstadisticas();
    cargarSolicitudes();
    
    // Manejador del formulario de solicitud
    $('#formNuevaSolicitud').on('submit', function(e) {
        e.preventDefault();
        console.log('Formulario enviado');
        enviarSolicitud();
    });
    
    // Contador de caracteres para el textarea
    $('#razon').on('input', function() {
        const longitud = $(this).val().length;
        $('#contadorCaracteres').text(longitud);
    });
    
    // Contador de caracteres para el modal de edición
    $('#razon_modal').on('input', function() {
        const longitud = $(this).val().length;
        $('#contadorCaracteres_modal').text(longitud);
    });
    
    // Limpiar modal al cerrarlo
    $('#modalNuevaSolicitud').on('hidden.bs.modal', function () {
        $('#formNuevaSolicitud')[0].reset();
        $('#equipo').val('');
        $('#razon').val('');
        $('#contadorCaracteres').text('0');
    });
    
    // Limpiar modal de edición al cerrarlo
    $('#modalEditarSolicitud').on('hidden.bs.modal', function () {
        $('#formEditarSolicitudModal')[0].reset();
        $('#id_modal').val('');
        $('#equipo_modal').val('');
        $('#razon_modal').val('');
        $('#contadorCaracteres_modal').text('0');
    });
    
    // Filtros
    $('#filtro-estado, #filtro-fecha').on('change', function() {
        aplicarFiltros();
    });
    
    // Manejador del botón actualizar en el modal de edición
    $('#btnActualizar').on('click', function() {
        actualizarSolicitud();
    });
});

function cargarCombos() {
    llenarCombo('listar_equipos', 'equipo', 'idEquipo', 'Equipo', 'Seleccionar equipo...');
    llenarCombo('listar_equipos', 'equipo_modal', 'idEquipo', 'Equipo', 'Seleccionar equipo...');
}

function llenarCombo(accion, idCombo, campoValor, campoTexto, mensaje = 'Seleccione...') {
    $.post('modelo/solicitudes_empleado.php', { accion }, function (data) {
        const $combo = $(`#${idCombo}`);
        $combo.empty().append(`<option value="">${mensaje}</option>`);
        
        if (data && Array.isArray(data)) {
            data.forEach(item => {
                $combo.append(`<option value="${item[campoValor]}">${item[campoTexto]}</option>`);
            });
        }
    }, 'json').fail(function(xhr) {
        console.error(`Error al cargar combo ${idCombo}:`, xhr.responseText);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los equipos disponibles'
        });
    });
}

function obtenerTotalSolicitudes() {
    $.ajax({
        url: "modelo/solicitudes_empleado.php",
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
        url: "modelo/solicitudes_empleado.php",
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
        url: "modelo/solicitudes_empleado.php",
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
        url: "modelo/solicitudes_empleado.php",
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

function cargarSolicitudes() {
    $.post('modelo/solicitudes_empleado.php', { accion: 'listar_mis_solicitudes' }, function (data) {
        let filas = '';
        if (data.length > 0) {
            data.forEach((s, i) => {
                // Determinar clase del badge según el estado
                let badgeClass = '';
                let puedeEditar = false;
                
                switch(s.Estado) {
                    case 'Pendiente':
                        badgeClass = 'bg-warning text-dark';
                        puedeEditar = true;
                        break;
                    case 'Aprobada':
                        badgeClass = 'bg-success text-white';
                        puedeEditar = false;
                        break;
                    case 'Rechazada':
                        badgeClass = 'bg-danger text-white';
                        puedeEditar = false;
                        break;
                    default:
                        badgeClass = 'bg-secondary text-white';
                        puedeEditar = false;
                }
                
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${s.Equipo}</td>
                    <td>${s.Fecha_Solicitud}</td>
                    <td><span class="badge ${badgeClass}">${s.Estado}</span></td>
                    <td>${s.Razon}</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary btn-editar"
                                data-id="${s.id}"
                                ${puedeEditar ? '' : 'disabled'}>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-eliminar"
                                data-id="${s.id}"
                                ${puedeEditar ? '' : 'disabled'}>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
        } else {
            filas = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="fas fa-tasks fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted">No hay solicitudes registradas</p>
                    </td>
                </tr>`;
        }

        // Reiniciar DataTable
        if ($.fn.DataTable.isDataTable('#tablaMisSolicitudes')) {
            $('#tablaMisSolicitudes').DataTable().clear().destroy();
        }

        $('#tablaMisSolicitudes tbody').html(filas);

        // Inicializar DataTable
        tablaSolicitudes = $('#tablaMisSolicitudes').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            fixedColumns: {
                rightColumns: 1
            },
            columnDefs: [
                {
                    targets: [5], // Columna de acciones
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de solicitudes', 'error');
    });
}

// Editar solicitud - ABRE EL MODAL
$(document).on('click', '.btn-editar', function () {
    const idSolicitud = $(this).data('id');
    
    console.log('ID de la solicitud a editar:', idSolicitud);
    
    // Obtener los datos completos de la solicitud
    $.post('modelo/solicitudes_empleado.php', { 
        accion: 'obtener_por_id', 
        id: idSolicitud 
    }, function(res) {
        console.log('Respuesta del servidor:', res);
        
        if (res.status === 'ok') {
            const solicitud = res.data;
            
            // Llenar el formulario del modal
            $('#id_modal').val(solicitud.idSolicitud);
            $('#equipo_modal').val(solicitud.equipo_solicitado);
            $('#razon_modal').val(solicitud.razon);
            $('#contadorCaracteres_modal').text(solicitud.razon.length);

            console.log('ID cargado en modal:', $('#id_modal').val());
            console.log('Equipo cargado en modal:', $('#equipo_modal').val());

            // Mostrar el modal
            $('#modalEditarSolicitud').modal('show');
        } else {
            Swal.fire('Error', 'No se pudieron cargar los datos para editar', 'error');
        }
    }, 'json').fail((xhr, status, error) => {
        console.error('Error al cargar datos:', xhr.responseText);
        Swal.fire('Error', 'Error al cargar datos para editar', 'error');
    });
});

// Función para actualizar la solicitud
function actualizarSolicitud() {
    const idSolicitud = $('#id_modal').val();
    const equipo = $('#equipo_modal').val();
    const razon = $('#razon_modal').val().trim();
    
    console.log('Actualizando solicitud:', {idSolicitud, equipo, razon});
    
    // Verificar que tengamos un ID válido
    if (!idSolicitud) {
        Swal.fire('Error', 'No se puede actualizar: ID de solicitud no encontrado', 'error');
        return;
    }

    // Validaciones
    if (!equipo) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Por favor seleccione un equipo'
        });
        $('#equipo_modal').focus();
        return;
    }
    
    if (!razon) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Por favor describa el motivo de su solicitud'
        });
        $('#razon_modal').focus();
        return;
    }
    
    if (razon.length < 10) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'La descripción debe tener al menos 10 caracteres'
        });
        $('#razon_modal').focus();
        return;
    }
    
    // Deshabilitar el botón de actualizar
    const btnActualizar = $('#btnActualizar');
    btnActualizar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Actualizando...');
    
    // Preparar datos
    const datos = {
        id: idSolicitud,
        equipo: equipo,
        razon: razon,
        accion: 'actualizar_solicitud'
    };
    
    console.log('Datos a actualizar:', datos);

    // Enviar datos al servidor
    $.post('modelo/solicitudes_empleado.php', datos, function (res) {
        console.log('Respuesta actualización:', res);
        
        Swal.fire({
            title: res.status === 'ok' ? 'Éxito' : 'Error',
            text: res.msg,
            icon: res.status === 'ok' ? 'success' : 'error'
        });

        if (res.status === 'ok') {
            $('#modalEditarSolicitud').modal('hide');
            cargarSolicitudes();
            cargarEstadisticas();
        }
    }, 'json').fail((xhr, status, error) => {
        console.error('Error completo:', xhr.responseText);
        Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
    }).always(function() {
        // Rehabilitar el botón de actualizar
        btnActualizar.prop('disabled', false).html('<i class="fas fa-save me-2"></i> Actualizar');
    });
}

// Eliminar solicitud
$(document).on('click', '.btn-eliminar', function () {
    const idSolicitud = $(this).data('id');
    
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará la solicitud seleccionada",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Llamar al procedimiento para eliminar la solicitud
            $.post('modelo/solicitudes_empleado.php', {
                accion: 'eliminar_solicitud',
                id: idSolicitud
            }, function(res) {
                if (res.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado!',
                        text: 'La solicitud ha sido eliminada correctamente.',
                        confirmButtonText: 'Aceptar'
                    });
                    cargarSolicitudes();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: res.msg,
                        confirmButtonText: 'Aceptar'
                    });
                }
            }, 'json').fail((xhr, status, error) => {
                console.error('Error al eliminar:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo eliminar la solicitud. Por favor, intente nuevamente.',
                    confirmButtonText: 'Aceptar'
                });
            });
        }
    });
});

function aplicarFiltros() {
    if (!tablaSolicitudes) {
        console.log('DataTable no inicializada');
        return;
    }
    
    const estadoFiltro = $('#filtro-estado').val().toLowerCase();
    const fechaFiltro = $('#filtro-fecha').val();
    
    tablaSolicitudes.rows().every(function() {
        const data = this.data();
        let mostrar = true;
        
        // Filtro por estado
        if (estadoFiltro && data[3]) {
            const estadoTexto = $(data[3]).text().toLowerCase();
            mostrar = mostrar && estadoTexto.includes(estadoFiltro);
        }
        
        // Filtro por fecha
        if (fechaFiltro && data[2]) {
            const fechaSolicitud = data[2];
            const [dia, mes, año] = fechaSolicitud.split('/');
            const fechaFormateada = `${año}-${mes}`;
            mostrar = mostrar && fechaFormateada === fechaFiltro;
        }
        
        if (mostrar) {
            $(this.node()).show();
        } else {
            $(this.node()).hide();
        }
    });
    
    tablaSolicitudes.draw();
}

// Función para enviar la solicitud
function enviarSolicitud() {
    const idEquipo = $('#equipo').val();
    const razon = $('#razon').val().trim();
    
    // Debug: Verificar valores
    console.log('ID Equipo:', idEquipo);
    console.log('Razón:', razon);
    
    // Validaciones
    if (!idEquipo) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Por favor seleccione un equipo'
        });
        $('#equipo').focus();
        return;
    }
    
    if (!razon) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Por favor describa el motivo de su solicitud'
        });
        $('#razon').focus();
        return;
    }
    
    if (razon.length < 10) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'La descripción debe tener al menos 10 caracteres'
        });
        $('#razon').focus();
        return;
    }
    
    // Deshabilitar el botón de envío para evitar duplicados
    const btnSubmit = $('#formNuevaSolicitud button[type="submit"]');
    btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Enviando...');
    
    // Preparar datos
    const datos = {
        accion: 'solicitar_inspeccion',
        equipo: idEquipo,
        razon: razon
    };
    
    // Debug: Verificar datos a enviar
    console.log('Datos a enviar:', datos);
    
    // Enviar datos al servidor
    $.ajax({
        url: 'modelo/solicitudes_empleado.php',
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            
            if (response && response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Solicitud Enviada',
                    text: response.message || 'Su solicitud de inspección ha sido enviada correctamente',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Cerrar el modal
                    const modalElement = document.getElementById('modalNuevaSolicitud');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Limpiar formulario
                    $('#formNuevaSolicitud')[0].reset();
                    $('#contadorCaracteres').text('0');
                    
                    // Recargar datos
                    cargarSolicitudes();
                    cargarEstadisticas();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo enviar la solicitud'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor. Por favor, intente nuevamente.'
            });
        },
        complete: function() {
            // Rehabilitar el botón de envío
            btnSubmit.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> Enviar Solicitud');
        }
    });
}