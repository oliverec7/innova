document.addEventListener("DOMContentLoaded", function () {
    cargarMantenimientosAsignados();
    cargarMantenimientosFinalizados();
});

function cargarMantenimientosAsignados() {
    $.post('modelo/mantenimientos_tecnico.php', { accion: 'listar_asignados' }, function (data) {
        // Destruir DataTable existente si lo hay
        if ($.fn.DataTable.isDataTable('#tablaMisMantenimientosAsignados')) {
            $('#tablaMisMantenimientosAsignados').DataTable().destroy();
        }

        let filas = '';
        if (data.length > 0) {
            data.forEach((man, i) => {
                // Determinar qué botones mostrar según el Estado
                let botones = '';
                if (man.Estado === 'Pendiente') {
                    // Botón para INICIAR el mantenimiento (solicita confirmación)
                    botones = `
                        <button class="btn btn-sm btn-outline-primary btn-iniciar-man"
                            title="Iniciar Mantenimiento"
                            data-id="${man.id}">
                            <i class="fas fa-play"></i>
                        </button>
                    `;
                } else if (man.Estado === 'En Proceso') {
                    // Botón para FINALIZAR el mantenimiento (lanza modal de resultados)
                    botones = `
                        <button class="btn btn-sm btn-outline-success btn-finalizar-man"
                            title="Finalizar Mantenimiento"
                            data-id="${man.id}">
                            <i class="fas fa-check-circle"></i>
                        </button>
                    `;
                }
                
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${man.Equipo}</td>
                    <td>${man.Responsable}</td>
                    <td>${man.Fecha_Programada}</td>
                    <td>${botones}</td>
                </tr>`;
            });
        }

        $('#tablaMisMantenimientosAsignados tbody').html(filas);

        // Inicializar DataTable
        $('#tablaMisMantenimientosAsignados').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                emptyTable: `
                    <div class="text-center py-4">
                        <i class="fas fa-tools fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted mb-0">No hay mantenimientos asignados</p>
                    </div>
                `
            },
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            fixedColumns: {
                rightColumns: 1
            },
            columnDefs: [
                {
                    targets: [4], // Columna de acciones
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de mantenimientos asignados', 'error');
    });
}

function cargarMantenimientosFinalizados() {
    $.post('modelo/mantenimientos_tecnico.php', { accion: 'listar_finalizados' }, function (data) {
        // Destruir DataTable existente si lo hay
        if ($.fn.DataTable.isDataTable('#tablaMisMantenimientosFinalizados')) {
            $('#tablaMisMantenimientosFinalizados').DataTable().destroy();
        }

        let filas = '';
        if (data.length > 0) {
            data.forEach((man, i) => {
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${man.Equipo}</td>
                    <td>${man.Responsable}</td>
                    <td>${man.Fecha_Programada || '-'}</td>
                    <td>${man.Hora_Inicio || '-'}</td>
                    <td>${man.Hora_Fin || '-'}</td>
                    <td>${man.Resultado || '-'}</td>
                    <td>${man.Detalle || '-'}</td>
                    <td> 
                        <button class="btn btn-sm btn-outline-warning btn-editar-man"
                            title="Ver / Editar Mantenimiento"
                            data-id="${man.id}">
                            <i class="fas fa-user-edit"></i>
                        </button>
                    </td>
                </tr>`;
            });
        }

        $('#tablaMisMantenimientosFinalizados tbody').html(filas);

        // Inicializar DataTable
        $('#tablaMisMantenimientosFinalizados').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                emptyTable: `
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted mb-0">No hay mantenimientos finalizados</p>
                    </div>
                `
            },
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            fixedColumns: {
                rightColumns: 1
            },
            columnDefs: [
                {
                    targets: [8], // Columna de acciones
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de mantenimientos finalizados', 'error');
    });
}

// Evento delegado para botón de INICIAR mantenimiento
$(document).on('click', '.btn-iniciar-man', function () {
    const idMantenimiento = $(this).data('id');
    modalIniciarMantenimiento(idMantenimiento);
});

// Evento delegado para botón de FINALIZAR mantenimiento (cuando ya está en proceso)
$(document).on('click', '.btn-finalizar-man', function () {
    const idMantenimiento = $(this).data('id');
    mostrarModalFinalizarMantenimiento(idMantenimiento);
});

// Evento delegado para botón de EDITAR/VER mantenimiento
$(document).on('click', '.btn-editar-man', function () {
    const idMantenimiento = $(this).data('id');
    modalEditarMantenimiento(idMantenimiento);
});

// Evento submit del formulario de finalizar mantenimiento (dentro del modalRealizarMantenimiento)
$(document).on('submit', '#formFinalizarMantenimiento', function(e) {
    e.preventDefault();
    
    const idMantenimiento = $('#finalizarIdMantenimiento').val();
    const resultado = $('#finalizarResultado').val();
    const detalle = $('#finalizarDetalle').val();
    
    if (!resultado || detalle.length < 5) {
        Swal.fire('Advertencia', 'Debe seleccionar un resultado e ingresar un detalle de al menos 5 caracteres.', 'warning');
        return;
    }

    finalizarMantenimiento(idMantenimiento, resultado, detalle);
});

// Evento submit del formulario de editar mantenimiento
$(document).on('submit', '#formEditarMantenimiento', function(e) {
    e.preventDefault();
    
    const idMantenimiento = $('#editarIdMantenimiento').val();
    const resultado = $('#editarResultado').val();
    const detalle = $('#editarDetalle').val();
    
    if (!resultado || detalle.length < 5) {
        Swal.fire('Advertencia', 'Debe seleccionar un resultado e ingresar un detalle de al menos 5 caracteres.', 'warning');
        return;
    }

    actualizarMantenimiento(idMantenimiento, resultado, detalle);
});

// Función para abrir modal de confirmación de inicio
function modalIniciarMantenimiento(idMantenimiento) {
    Swal.fire({
        title: '¿Iniciar Mantenimiento?',
        text: 'Se registrará el inicio de este mantenimiento',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, iniciar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            iniciarMantenimiento(idMantenimiento);
        }
    });
}

// Función para iniciar el mantenimiento (llamada a SP)
function iniciarMantenimiento(idMantenimiento) {
    $.post('modelo/mantenimientos_tecnico.php', {
        accion: 'iniciar_mantenimiento',
        idMantenimiento: idMantenimiento
    }, function (response) {
        if (response.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Mantenimiento Iniciado',
                text: response.msg,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Actualizar tablas y mostrar modal para finalizar
                cargarMantenimientosAsignados();
                mostrarModalFinalizarMantenimiento(idMantenimiento);
            });
        } else {
            Swal.fire('Error', response.msg || 'No se pudo iniciar el mantenimiento', 'error');
        }
    }, 'json').fail(() => {
        Swal.fire('Error', 'Error de conexión al iniciar el mantenimiento', 'error');
    });
}

// Función para mostrar el modal de Finalizar Mantenimiento
function mostrarModalFinalizarMantenimiento(idMantenimiento) {
    // Asignar el ID al campo oculto del formulario de finalización
    $('#finalizarIdMantenimiento').val(idMantenimiento);

    // Limpiar campos por si acaso
    $('#finalizarResultado').val('');
    $('#finalizarDetalle').val('');
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalRealizarMantenimiento'));
    modal.show();
}

// Función para finalizar el mantenimiento (llamada a SP)
function finalizarMantenimiento(idMantenimiento, resultado, detalle) {
    $.post('modelo/mantenimientos_tecnico.php', {
        accion: 'finalizar_mantenimiento',
        idMantenimiento: idMantenimiento,
        resultado: resultado,
        detalle: detalle
    }, function (response) {
        if (response.status === 'success') {
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalRealizarMantenimiento'));
            modal.hide();
            
            Swal.fire({
                icon: 'success',
                title: 'Mantenimiento Finalizado',
                text: response.msg,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                cargarMantenimientosAsignados();
                cargarMantenimientosFinalizados();
            });
        } else {
            Swal.fire('Error', response.msg || 'No se pudo finalizar el mantenimiento', 'error');
        }
    }, 'json').fail(() => {
        Swal.fire('Error', 'Error de conexión al finalizar el mantenimiento', 'error');
    });
}

// Función para abrir modal de edición de mantenimiento
function modalEditarMantenimiento(idMantenimiento) {
    // Obtener datos del mantenimiento
    $.post('modelo/mantenimientos_tecnico.php', {
        accion: 'obtener_mantenimiento',
        idMantenimiento: idMantenimiento
    }, function (data) {
        if (data && data.status !== 'error') {
            // Llenar el formulario con los datos
            $('#editarIdMantenimiento').val(data.id);
            $('#editarResultado').val(data.Resultado);
            $('#editarDetalle').val(data.Detalle);
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalVerMantenimiento'));
            modal.show();
        } else {
            Swal.fire('Error', data.msg || 'No se pudo obtener los datos del mantenimiento', 'error');
        }
    }, 'json').fail(() => {
        Swal.fire('Error', 'Error de conexión al obtener el mantenimiento', 'error');
    });
}

// Función para actualizar (editar) un mantenimiento finalizado
function actualizarMantenimiento(idMantenimiento, resultado, detalle) {
    $.post('modelo/mantenimientos_tecnico.php', {
        accion: 'actualizar_mantenimiento',
        idMantenimiento: idMantenimiento,
        resultado: resultado,
        detalle: detalle
    }, function (response) {
        if (response.status === 'success') {
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalVerMantenimiento'));
            modal.hide();
            
            Swal.fire({
                icon: 'success',
                title: 'Mantenimiento Actualizado',
                text: response.msg,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                cargarMantenimientosFinalizados();
            });
        } else {
            Swal.fire('Error', response.msg || 'No se pudo actualizar el mantenimiento', 'error');
        }
    }, 'json').fail(() => {
        Swal.fire('Error', 'Error de conexión al actualizar el mantenimiento', 'error');
    });
}