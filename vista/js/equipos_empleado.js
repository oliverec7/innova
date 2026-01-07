$(document).ready(function () {
    cargarEquipos();
    
    // Manejador del formulario de solicitud
    $('#formNuevaSolicitud').on('submit', function(e) {
        e.preventDefault();
        console.log('Formulario enviado');
        enviarSolicitud();
    });
    
    // Limpiar modal al cerrarlo
    $('#modalNuevaSolicitud').on('hidden.bs.modal', function () {
        $('#formNuevaSolicitud')[0].reset();
        $('#id').val('');
        $('#nombreEquipoSeleccionado').val('');
        $('#razon').val('');
        $('#contadorCaracteres').text('0');
    });
});

//cargar equipos
function cargarEquipos() {
    $.post('modelo/equipos_empleado.php', { accion: 'listar' }, function (data) {
        let filas = '';
        if (data.length > 0) {
            data.forEach((eq, i) => {
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${eq.Cod_patrimonial}</td>
                    <td>${eq.Equipo}</td>
                    <td>${eq.Tipo}</td>
                    <td>${eq.Marca}</td>
                    <td>${eq.Serie}</td>
                    <td>${eq.Modelo}</td>
                    <td>${eq.Compra}</td>
                    <td>${eq.Instalacion}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="solicitarInspeccion('${eq.id}')">
                            <i class="fas fa-clipboard-check"></i> Solicitar Inspección
                        </button>
                    </td>
                </tr>`;
            });
        } else {
            filas = `
                <tr>
                    <td colspan="11" class="text-center py-4">
                        <i class="fas fa-desktop fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted">No hay equipos registrados</p>
                    </td>
                </tr>`;
        }

        // Reiniciar DataTable
        if ($.fn.DataTable.isDataTable('#tablaEquipos')) {
            $('#tablaEquipos').DataTable().clear().destroy();
        }

        $('#tablaEquipos tbody').html(filas);

        $('#tablaEquipos').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            fixedColumns: {
                rightColumns: 1
            }
        });
    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de equipos', 'error');
    });
}

// Función para abrir el modal de solicitud de inspección
function solicitarInspeccion(idEquipo) {
    // Limpiar el formulario
    $('#formNuevaSolicitud')[0].reset();
    
    // Establecer el ID del equipo en el campo oculto
    $('#id').val(idEquipo);
    
    // Buscar la información del equipo en la tabla
    $.post('modelo/equipos_empleado.php', { 
        accion: 'obtener_equipo', 
        id: idEquipo 
    }, function(data) {
        if (data && data.success) {
            const equipo = data.equipo;
            $('#nombreEquipoSeleccionado').val(equipo.Equipo);
            
            // Abrir el modal
            const modal = new bootstrap.Modal(document.getElementById('modalNuevaSolicitud'));
            modal.show();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo obtener la información del equipo'
            });
        }
    }, 'json').fail(function(xhr, status, error) {
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo conectar con el servidor'
        });
    });
}

// Función para enviar la solicitud
function enviarSolicitud() {
    const idEquipo = $('#id').val();
    const razon = $('#razon').val().trim();
    
    // Debug: Verificar valores
    console.log('ID Equipo:', idEquipo);
    console.log('Razón:', razon);
    
    // Validaciones
    if (!idEquipo) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'No se ha seleccionado un equipo'
        });
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
    btnSubmit.prop('disabled', true);
    
    // Preparar datos
    const datos = {
        accion: 'solicitar_inspeccion',
        id: idEquipo,
        razon: razon
    };
    
    // Debug: Verificar datos a enviar
    console.log('Datos a enviar:', datos);
    
    // Enviar datos al servidor
    $.ajax({
        url: 'modelo/equipos_empleado.php',
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
                    
                    // Recargar equipos
                    cargarEquipos();
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
            console.log('Error AJAX:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor al enviar la solicitud'
            });
        },
        complete: function() {
            // Rehabilitar el botón de envío
            btnSubmit.prop('disabled', false);
        }
    });
}