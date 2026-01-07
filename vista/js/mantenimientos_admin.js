document.addEventListener("DOMContentLoaded", function () {
    cargarMantenimientosPendientes();
    cargarMantenimientosFinalizados();

    // Event delegation para los botones de la tabla de mantenimientos finalizados
    $(document).on('click', '.btn-detalles-man', function() {
        const idMantenimiento = $(this).data('id');
        cargarDetallesMantenimiento(idMantenimiento);
    });

    $(document).on('click', '.btn-editar-man', function() {
        const idMantenimiento = $(this).data('id');
        abrirModalEditar(idMantenimiento);
    });

    // Manejar el submit del formulario de edición
    $('#formEditarMantenimiento').on('submit', function(e) {
        e.preventDefault();
        actualizarMantenimiento();
    });
});

function cargarMantenimientosPendientes() {
    $.post('modelo/mantenimientos_admin.php', { accion: 'listar_pendientes' }, function (data) {
        if ($.fn.DataTable.isDataTable('#tablaMantenimientosPendientes')) {
            $('#tablaMantenimientosPendientes').DataTable().destroy();
        }

        let filas = '';
        if (data.length > 0) {
            data.forEach((man, i) => {
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${man.Equipo}</td>
                    <td>${man.Tecnico_Asignado}</td>
                    <td>${man.Responsable}</td>
                    <td>${man.Fecha_Programada}</td>
                    <td>${man.Comentario}</td>
                </tr>`;
            });
        }

        $('#tablaMantenimientosPendientes tbody').html(filas);

        $('#tablaMantenimientosPendientes').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                emptyTable: `
                    <div class="text-center py-4">
                        <i class="fas fa-tools fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted mb-0">No hay mantenimientos pendientes</p>
                    </div>
                `
            },
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            columnDefs: [
                {
                    targets: [5],
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de mantenimientos pendientes', 'error');
    });
}

function cargarMantenimientosFinalizados() {
    $.post('modelo/mantenimientos_admin.php', { accion: 'listar_finalizados' }, function (data) {
        if ($.fn.DataTable.isDataTable('#tablaMantenimientosFinalizados')) {
            $('#tablaMantenimientosFinalizados').DataTable().destroy();
        }

        let filas = '';
        if (data.length > 0) {
            data.forEach((man, i) => {
                const resultadoBadge = man.Resultado === 'Funcional' ? 
                    '<span class="badge bg-success">Funcional</span>' : 
                    '<span class="badge bg-danger">No Funcional</span>';
                
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${man.Equipo}</td>
                    <td>${man.Tecnico}</td>
                    <td>${man.Responsable}</td>
                    <td>${man.Fecha_Fin || '-'}</td>
                    <td>${man.Hora_Inicio || '-'}</td>
                    <td>${man.Hora_Fin || '-'}</td>
                    <td>${resultadoBadge}</td>
                    <td>${man.Detalle}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-info btn-detalles-man"
                            title="Ver Detalles"
                            data-id="${man.id}">
                            <i class="fas fa-eye"></i>
                        </button>     
                        <button class="btn btn-sm btn-outline-warning btn-editar-man"
                            title="Editar Resultado/Detalle"
                            data-id="${man.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>`;
            });
        }

        $('#tablaMantenimientosFinalizados tbody').html(filas);

        $('#tablaMantenimientosFinalizados').DataTable({
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
                    targets: [9],
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de mantenimientos finalizados', 'error');
    });
}

// Funcion para abrir modal de detalles del mantenimiento
function cargarDetallesMantenimiento(idMantenimiento) {
    $.post('modelo/mantenimientos_admin.php', {
        accion: 'visualizar_mantenimiento',
        id: idMantenimiento
    }, function(response) {
        if (response.status === 'success' && response.mantenimiento) {
            const man = response.mantenimiento;
            
            // Llenar los campos del modal de Ver
            $('#verEquipo').val(man.Equipo || '-');
            $('#verTecnico').val(man.Tecnico || '-');
            $('#verResponsable').val(man.Responsable || '-');
            $('#verFechaProgramada').val(man.Fecha_Programada_Formato || '-');
            
            // Llenar campos de tiempo/resultado
            const horaInicio = man.Hora_Inicio_Formato ? man.Hora_Inicio_Formato.substring(0, 5) : '';
            const horaFin = man.Hora_Fin_Formato ? man.Hora_Fin_Formato.substring(0, 5) : '';
            
            $('#verHoraInicio').val(horaInicio || '');
            $('#verHoraFin').val(horaFin || '');
            $('#verResultado').val(man.Resultado || '-');
            $('#verDetalle').val(man.Detalle || '-');
            
            // Mostrar el modal
            $('#modalVerMantenimiento').modal('show');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.msg || 'No se pudieron cargar los detalles del mantenimiento'
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

// Funcion para abrir modal de edición (solo resultado y detalle)
function abrirModalEditar(idMantenimiento) {
    // Primero obtener los datos actuales del mantenimiento
    $.post('modelo/mantenimientos_admin.php', {
        accion: 'visualizar_mantenimiento',
        id: idMantenimiento
    }, function(response) {
        if (response.status === 'success' && response.mantenimiento) {
            const man = response.mantenimiento;
            
            // Llenar el formulario de edición
            $('#editarIdMantenimiento').val(idMantenimiento);
            $('#editarResultado').val(man.Resultado || '');
            $('#editarDetalle').val(man.Detalle || '');
            
            // Mostrar el modal
            $('#modalEditarMantenimiento').modal('show');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.msg || 'No se pudieron cargar los datos del mantenimiento para edición'
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

// Funcion para actualizar (editar) un mantenimiento finalizado
function actualizarMantenimiento() {
    const idMantenimiento = $('#editarIdMantenimiento').val();
    const resultado = $('#editarResultado').val();
    const detalle = $('#editarDetalle').val();

    // Validación básica
    if (!resultado) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Debe seleccionar un resultado'
        });
        return;
    }

    if (!detalle.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Debe ingresar el detalle de la corrección'
        });
        return;
    }

    // Confirmar antes de actualizar
    Swal.fire({
        title: '¿Está seguro?',
        text: "Se actualizarán los datos del mantenimiento finalizado",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar datos al servidor
            $.post('modelo/mantenimientos_admin.php', {
                accion: 'actualizar_mantenimiento',
                idMantenimiento: idMantenimiento,
                resultado: resultado,
                detalle: detalle
            }, function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.msg || 'Mantenimiento actualizado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Cerrar modal y recargar tabla
                        $('#modalEditarMantenimiento').modal('hide');
                        $('#formEditarMantenimiento')[0].reset();
                        cargarMantenimientosFinalizados();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.msg || 'No se pudo actualizar el mantenimiento'
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