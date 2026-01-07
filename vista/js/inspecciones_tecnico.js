document.addEventListener("DOMContentLoaded", function () {
    cargarInspeccionesAsignadas();
    cargarInspeccionesFinalizadas();
});

function cargarInspeccionesAsignadas() {
    $.post('modelo/inspecciones_tecnico.php', { accion: 'listar_asignadas' }, function (data) {
        if ($.fn.DataTable.isDataTable('#tablaMisInspeccionesAsignadas')) {
            $('#tablaMisInspeccionesAsignadas').DataTable().destroy();
        }

        let filas = '';
        if (data.length > 0) {
            data.forEach((ins, i) => {
                // Determinar qué botones mostrar según el estado
                let botones = '';
                if (ins.Estado === 'Pendiente') {
                    botones = `
                        <button class="btn btn-sm btn-outline-primary btn-iniciar"
                            title="Iniciar Inspección"
                            data-id="${ins.id}">
                            <i class="fas fa-play"></i>
                        </button>
                    `;
                } else if (ins.Estado === 'En Proceso') {
                    botones = `
                        <button class="btn btn-sm btn-outline-success btn-continuar"
                            title="Continuar Inspección"
                            data-id="${ins.id}">
                            <i class="fas fa-play-circle"></i>
                        </button>
                    `;
                }
                
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${ins.Equipo}</td>
                    <td>${ins.Responsable}</td>
                    <td>${ins.Fecha_Inspeccion}</td>
                    <td>${ins.Razon}</td>
                    <td>${botones}</td>
                </tr>`;
            });
        }

        $('#tablaMisInspeccionesAsignadas tbody').html(filas);

        $('#tablaMisInspeccionesAsignadas').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                emptyTable: `
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted mb-0">No hay inspecciones asignadas</p>
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
        Swal.fire('Error', 'No se pudo cargar la lista de inspecciones asignadas', 'error');
    });
}

function cargarInspeccionesFinalizadas() {
    $.post('modelo/inspecciones_tecnico.php', { accion: 'listar_finalizadas' }, function (data) {
        if ($.fn.DataTable.isDataTable('#tablaMisInspeccionesFinalizadas')) {
            $('#tablaMisInspeccionesFinalizadas').DataTable().destroy();
        }

        let filas = '';
        if (data.length > 0) {
            data.forEach((ins, i) => {
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${ins.Equipo}</td>
                    <td>${ins.Responsable}</td>
                    <td>${ins.Fecha_Inspeccion || '-'}</td>
                    <td>${ins.Hora_Inicio}</td>
                    <td>${ins.Hora_Fin}</td>
                    <td>${ins.Resultado}</td>
                    <td>${ins.Comentario}</td>
                    <td>   
                        <button class="btn btn-sm btn-outline-warning btn-editar"
                            title="Editar Inspección"
                            data-id="${ins.id}">
                            <i class="fas fa-user-edit"></i>
                        </button>
                    </td>
                </tr>`;
            });
        }

        $('#tablaMisInspeccionesFinalizadas tbody').html(filas);

        $('#tablaMisInspeccionesFinalizadas').DataTable({
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
                    targets: [8],
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de inspecciones finalizadas', 'error');
    });
}

// Evento delegado para botón de iniciar inspección
$(document).on('click', '.btn-iniciar', function () {
    const idInspeccion = $(this).data('id');
    modalRealizarInspeccion(idInspeccion);
});

// Evento delegado para botón de continuar inspección (cuando ya está en proceso)
$(document).on('click', '.btn-continuar', function () {
    const idInspeccion = $(this).data('id');
    mostrarModalFinalizarInspeccion(idInspeccion);
});

// Evento delegado para botón de editar inspección
$(document).on('click', '.btn-editar', function () {
    const idInspeccion = $(this).data('id');
    modalEditarInspeccion(idInspeccion);
});

// Función para abrir modal de realizar inspección
function modalRealizarInspeccion(idInspeccion) {
    Swal.fire({
        title: '¿Iniciar Inspección?',
        text: 'Se registrará el inicio de esta inspección',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, iniciar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            realizarInspeccion(idInspeccion);
        }
    });
}

// Función para realizar (iniciar) una inspección
function realizarInspeccion(idInspeccion) {
    $.post('modelo/inspecciones_tecnico.php', {
        accion: 'iniciar_inspeccion',
        idInspeccion: idInspeccion
    }, function (response) {
        if (response.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Inspección Iniciada',
                text: response.msg,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Mostrar modal para finalizar inspección
                mostrarModalFinalizarInspeccion(idInspeccion);
            });
        } else {
            Swal.fire('Error', response.msg || 'No se pudo iniciar la inspección', 'error');
        }
    }, 'json').fail(() => {
        Swal.fire('Error', 'Error de conexión al iniciar la inspección', 'error');
    });
}

// Función para mostrar modal de finalizar inspección (usando modal HTML existente)
function mostrarModalFinalizarInspeccion(idInspeccion) {
    // Limpiar campos del modal
    $('#resultado').val('');
    $('#comentario').val('');
    
    // Cambiar campos de readonly a editables
    $('#resultado').prop('readonly', false);
    $('#comentario').prop('readonly', false);
    
    // Convertir input de resultado a select
    const selectResultado = `
        <select id="resultado" class="form-control border-custom" required>
            <option value="">Seleccione...</option>
            <option value="Conforme">Conforme</option>
            <option value="No conforme">No conforme</option>
        </select>
    `;
    $('#resultado').parent().html(`
        <label class="form-label form-label-custom">
            <i class="fas fa-clipboard-check me-1"></i>Resultado:
        </label>
        ${selectResultado}
    `);
    
    // Cambiar el botón Finalizar para que ejecute la acción
    $('.btn-danger-custom').off('click').on('click', function() {
        const resultado = $('#resultado').val();
        const comentario = $('#comentario').val();
        
        if (!resultado) {
            Swal.fire('Advertencia', 'Debe seleccionar un resultado', 'warning');
            return;
        }
        if (!comentario.trim()) {
            Swal.fire('Advertencia', 'Debe ingresar un comentario', 'warning');
            return;
        }
        
        finalizarInspeccion(idInspeccion, resultado, comentario);
    });
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalRealizarInspeccion'));
    modal.show();
}

// Función para finalizar inspección
function finalizarInspeccion(idInspeccion, resultado, comentario) {
    $.post('modelo/inspecciones_tecnico.php', {
        accion: 'finalizar_inspeccion',
        idInspeccion: idInspeccion,
        resultado: resultado,
        comentario: comentario
    }, function (response) {
        if (response.status === 'success') {
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalRealizarInspeccion'));
            modal.hide();
            
            Swal.fire({
                icon: 'success',
                title: 'Inspección Finalizada',
                text: response.msg,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                cargarInspeccionesAsignadas();
                cargarInspeccionesFinalizadas();
            });
        } else {
            Swal.fire('Error', response.msg || 'No se pudo finalizar la inspección', 'error');
        }
    }, 'json').fail(() => {
        Swal.fire('Error', 'Error de conexión al finalizar la inspección', 'error');
    });
}

// Función para abrir modal de edición de inspección (usando modal HTML existente)
function modalEditarInspeccion(idInspeccion) {
    // Obtener datos de la inspección
    $.post('modelo/inspecciones_tecnico.php', {
        accion: 'obtener_inspeccion',
        idInspeccion: idInspeccion
    }, function (data) {
        if (data && data.status !== 'error') {
            // Llenar el formulario con los datos
            $('#editarIdInspeccion').val(data.id);
            $('#editarResultado').val(data.Resultado);
            $('#editarComentario').val(data.Comentario);
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalEditarInspeccion'));
            modal.show();
        } else {
            Swal.fire('Error', data.msg || 'No se pudo obtener los datos de la inspección', 'error');
        }
    }, 'json').fail(() => {
        Swal.fire('Error', 'Error de conexión al obtener la inspección', 'error');
    });
}

// Evento submit del formulario de editar inspección
$(document).on('submit', '#formEditarInspeccion', function(e) {
    e.preventDefault();
    
    const idInspeccion = $('#editarIdInspeccion').val();
    const resultado = $('#editarResultado').val();
    const comentario = $('#editarComentario').val();
    
    actualizarInspeccion(idInspeccion, resultado, comentario);
});

// Función para actualizar (editar) una inspección finalizada
function actualizarInspeccion(idInspeccion, resultado, comentario) {
    $.post('modelo/inspecciones_tecnico.php', {
        accion: 'actualizar_inspeccion',
        idInspeccion: idInspeccion,
        resultado: resultado,
        comentario: comentario
    }, function (response) {
        if (response.status === 'success') {
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarInspeccion'));
            modal.hide();
            
            Swal.fire({
                icon: 'success',
                title: 'Inspección Actualizada',
                text: response.msg,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                cargarInspeccionesFinalizadas();
            });
        } else {
            Swal.fire('Error', response.msg || 'No se pudo actualizar la inspección', 'error');
        }
    }, 'json').fail(() => {
        Swal.fire('Error', 'Error de conexión al actualizar la inspección', 'error');
    });
}