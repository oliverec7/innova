document.addEventListener("DOMContentLoaded", function () {
    cargarInspeccionesPendientes();
    cargarInspeccionesFinalizadas();

    // Event delegation para los botones de la tabla de inspecciones finalizadas
    $(document).on('click', '.btn-detalles', function() {
        const idInspeccion = $(this).data('id');
        cargarDetallesInspeccion(idInspeccion);
    });

    $(document).on('click', '.btn-reasignar', function() {
        const idInspeccion = $(this).data('id');
        abrirModalEditar(idInspeccion);
    });

    // Manejar el submit del formulario de edición
    $('#formEditarInspeccion').on('submit', function(e) {
        e.preventDefault();
        actualizarInspeccion();
    });
});

function cargarInspeccionesPendientes() {
    $.post('modelo/inspecciones_admin.php', { accion: 'listar_pendientes' }, function (data) {
        if ($.fn.DataTable.isDataTable('#tablaInspeccionesPendientes')) {
            $('#tablaInspeccionesPendientes').DataTable().destroy();
        }

        let filas = '';
        if (data.length > 0) {
            data.forEach((ins, i) => {
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${ins.Equipo}</td>
                    <td>${ins.Inspector}</td>
                    <td>${ins.Responsable}</td>
                    <td>${ins.Fecha_Inspeccion}</td>
                    <td>${ins.Razon}</td>
                </tr>`;
            });
        }

        $('#tablaInspeccionesPendientes tbody').html(filas);

        $('#tablaInspeccionesPendientes').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                emptyTable: `
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted mb-0">No hay inspecciones pendientes</p>
                    </div>
                `
            },
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            fixedColumns: {
                rightColumns: 1
            },
            columnDefs: [
                {
                    targets: [5],
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de inspecciones pendientes', 'error');
    });
}

function cargarInspeccionesFinalizadas() {
    $.post('modelo/inspecciones_admin.php', { accion: 'listar_finalizadas' }, function (data) {
        if ($.fn.DataTable.isDataTable('#tablaInspeccionesFinalizadas')) {
            $('#tablaInspeccionesFinalizadas').DataTable().destroy();
        }

        let filas = '';
        if (data.length > 0) {
            data.forEach((ins, i) => {
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${ins.Equipo}</td>
                    <td>${ins.Inspector}</td>
                    <td>${ins.Responsable}</td>
                    <td>${ins.Fecha_Inspeccion || '-'}</td>
                    <td>${ins.Hora_Inicio}</td>
                    <td>${ins.Hora_Fin}</td>
                    <td>${ins.Resultado}</td>
                    <td>${ins.Comentario}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-info btn-detalles"
                            title="Ver Detalles"
                            data-id="${ins.id}">
                            <i class="fas fa-eye"></i>
                        </button>    
                        <button class="btn btn-sm btn-outline-warning btn-reasignar"
                            title="Editar Inspeccion"
                            data-id="${ins.id}">
                            <i class="fas fa-user-edit"></i>
                        </button>
                    </td>
                </tr>`;
            });
        }

        $('#tablaInspeccionesFinalizadas tbody').html(filas);

        $('#tablaInspeccionesFinalizadas').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                emptyTable: `
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted mb-0">No hay inspecciones finalizadas</p>
                    </div>
                `
            },
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            fixedColumns: {
                rightColumns: 1
            },
            columnDefs: [
                {
                    targets: [9],
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de inspecciones finalizadas', 'error');
    });
}

// Funcion para abrir modal de detalles de la inspeccion
function cargarDetallesInspeccion(idInspeccion) {
    $.post('modelo/inspecciones_admin.php', {
        accion: 'visualizar_inspeccion',
        id: idInspeccion
    }, function(response) {
        if (response.status === 'success' && response.inspeccion) {
            const insp = response.inspeccion;
            
            // Llenar los campos del modal
            $('#equipo').val(insp.Equipo || '-');
            $('#inspector').val(insp.Inspector || '-');
            $('#responsable').val(insp.Responsable || '-');
            
            // Convertir fecha de dd/mm/yyyy a yyyy-mm-dd para el input date
            if (insp.Fecha_Inicio) {
                const partes = insp.Fecha_Inicio.split('/');
                if (partes.length === 3) {
                    $('#fecha_inspeccion').val(`${partes[2]}-${partes[1]}-${partes[0]}`);
                }
            } else {
                $('#fecha_inspeccion').val('');
            }
            
            $('#hora_inicio').val(insp.Hora_Inicio || '-');
            $('#hora_fin').val(insp.Hora_Fin || '-');
            $('#resultado').val(insp.Resultado || '-');
            $('#comentario').val(insp.Comentario || '-');
            
            // Mostrar el modal
            $('#modalVerInspeccion').modal('show');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.msg || 'No se pudieron cargar los detalles de la inspección'
            });
        }
    }, 'json').fail(function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al comunicarse con el servidor'
        });
    });
}

// Funcion para abrir modal de edición
function abrirModalEditar(idInspeccion) {
    // Primero obtener los datos actuales de la inspección
    $.post('modelo/inspecciones_admin.php', {
        accion: 'visualizar_inspeccion',
        id: idInspeccion
    }, function(response) {
        if (response.status === 'success' && response.inspeccion) {
            const insp = response.inspeccion;
            
            // Llenar el formulario de edición
            $('#editarIdInspeccion').val(idInspeccion);
            $('#editarResultado').val(insp.Resultado || '');
            $('#editarComentario').val(insp.Comentario || '');
            
            // Mostrar el modal
            $('#modalEditarInspeccion').modal('show');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.msg || 'No se pudieron cargar los datos de la inspección'
            });
        }
    }, 'json').fail(function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al comunicarse con el servidor'
        });
    });
}

// Funcion para actualizar (editar) una inspección finalizada
function actualizarInspeccion() {
    const idInspeccion = $('#editarIdInspeccion').val();
    const resultado = $('#editarResultado').val();
    const comentario = $('#editarComentario').val();

    // Validación básica
    if (!resultado) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Debe seleccionar un resultado'
        });
        return;
    }

    if (!comentario.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Debe ingresar un comentario'
        });
        return;
    }

    // Confirmar antes de actualizar
    Swal.fire({
        title: '¿Está seguro?',
        text: "Se actualizarán los datos de la inspección",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar datos al servidor
            $.post('modelo/inspecciones_admin.php', {
                accion: 'actualizar_inspeccion',
                idInspeccion: idInspeccion,
                resultado: resultado,
                comentario: comentario
            }, function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.msg || 'Inspección actualizada correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Cerrar modal y recargar tabla
                        $('#modalEditarInspeccion').modal('hide');
                        $('#formEditarInspeccion')[0].reset();
                        cargarInspeccionesFinalizadas();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.msg || 'No se pudo actualizar la inspección'
                    });
                }
            }, 'json').fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al comunicarse con el servidor'
                });
            });
        }
    });
}