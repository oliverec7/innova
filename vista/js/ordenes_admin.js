document.addEventListener("DOMContentLoaded", function () {
    obtenerCantidadOrdenes();
    obtenerCantidadOrdenesPendientes();
    obtenerCantidadOrdenesAsignadas();
    cargarOrdenesPendientes();
    cargarOrdenesAsignadas();
    cargarTecnicosSelect();
    inicializarEventosReasignacion();
});

function obtenerCantidadOrdenes() {
    $.ajax({
        url: "modelo/ordenes_admin.php",
        type: "POST",
        data: { accion: "total_ordenes" },
        dataType: "json",
        success: function (response) {
            if (response && response.total !== undefined) {
                $("#totalOrdenes").text(response.total);
            } else {
                console.error("No se pudo obtener la cantidad de órdenes:", response.msg || "Respuesta inválida");
                $("#totalOrdenes").text("0");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            $("#totalOrdenes").text("0");
        }
    });
}

function obtenerCantidadOrdenesPendientes() {
    $.ajax({
        url: "modelo/ordenes_admin.php",
        type: "POST",
        data: { accion: "ordenes_pendientes" },
        dataType: "json",
        success: function (response) {
            if (response && response.total !== undefined) {
                $("#ordenesPendientes").text(response.total);
            } else {
                console.error("No se pudo obtener la cantidad de órdenes pendientes:", response.msg || "Respuesta inválida");
                $("#ordenesPendientes").text("0");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            $("#ordenesPendientes").text("0");
        }
    });
}

function obtenerCantidadOrdenesAsignadas() {
    $.ajax({
        url: "modelo/ordenes_admin.php",
        type: "POST",
        data: { accion: "ordenes_asignadas" },
        dataType: "json",
        success: function (response) {
            if (response && response.total !== undefined) {
                $("#ordenesAsignadas").text(response.total);
            } else {
                console.error("No se pudo obtener la cantidad de órdenes asignadas:", response.msg || "Respuesta inválida");
                $("#ordenesAsignadas").text("0");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            $("#ordenesAsignadas").text("0");
        }
    });
}

function cargarOrdenesPendientes() {
    $.post('modelo/ordenes_admin.php', { accion: 'listar_pendientes' }, function (data) {
        if ($.fn.DataTable.isDataTable('#tablaOrdenesPendientes')) {
            $('#tablaOrdenesPendientes').DataTable().destroy();
        }

        let filas = '';
        if (data.length > 0) {
            data.forEach((o, i) => {
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${o.Tipo_Orden}</td>
                    <td>${o.Prioridad}</td>
                    <td>${o.Fecha_Orden}</td>
                    <td>${o.Equipo}</td>
                    <td>${o.Solicitante}</td>
                    <td>${o.Razon}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary btn-asignar"
                            title="Asignar Técnico"
                            data-id="${o.id}">
                            <i class="fas fa-user-plus"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info btn-detalles"
                            title="Ver Detalles"
                            data-id="${o.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>`;
            });
        }

        $('#tablaOrdenesPendientes tbody').html(filas);

        $('#tablaOrdenesPendientes').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                emptyTable: `
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted mb-0">No hay órdenes pendientes</p>
                    </div>
                `
            },
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            fixedColumns: {
                rightColumns: 1
            },
            columnDefs: [
                {
                    targets: [7],
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de órdenes pendientes', 'error');
    });
}

function cargarOrdenesAsignadas() {
    $.post('modelo/ordenes_admin.php', { accion: 'listar_asignadas' }, function (data) {
        if ($.fn.DataTable.isDataTable('#tablaOrdenesAsignadas')) {
            $('#tablaOrdenesAsignadas').DataTable().destroy();
        }

        let filas = '';
        if (data.length > 0) {
            data.forEach((o, i) => {
                filas += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${o.Tipo_Orden}</td>
                    <td>${o.Prioridad}</td>
                    <td>${o.Fecha_Orden}</td>
                    <td>${o.Fecha_Programada || '-'}</td>
                    <td>${o.Equipo}</td>
                    <td>${o.Solicitante}</td>
                    <td>${o.Tecnico_Asignado}</td>
                    <td>${o.Razon}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-warning btn-reasignar"
                            title="Reasignar Técnico"
                            data-id="${o.id}">
                            <i class="fas fa-user-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info btn-detalles"
                            title="Ver Detalles"
                            data-id="${o.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>`;
            });
        }

        $('#tablaOrdenesAsignadas tbody').html(filas);

        $('#tablaOrdenesAsignadas').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                emptyTable: `
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted mb-0">No hay órdenes asignadas</p>
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
        Swal.fire('Error', 'No se pudo cargar la lista de órdenes asignadas', 'error');
    });
}

function cargarTecnicosSelect() {
    $.ajax({
        url: "modelo/ordenes_admin.php",
        type: "POST",
        data: { accion: "listar_tecnicos" },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success" && response.tecnicos) {
                let options = '<option value="">Seleccionar técnico</option>';
                response.tecnicos.forEach(tecnico => {
                    options += `<option value="${tecnico.id}">${tecnico.Tecnico}</option>`;
                });
                $("#tecnico_asignado").html(options);
                $("#tecnico_reasignado").html(options);
            } else {
                console.error("No se pudieron cargar los técnicos:", response.msg || "Respuesta inválida");
                Swal.fire('Error', 'No se pudieron cargar los técnicos', 'error');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX al cargar técnicos:", status, error);
            Swal.fire('Error', 'Error al cargar la lista de técnicos', 'error');
        }
    });
}

// Evento para abrir modal de asignar técnico
$(document).on('click', '.btn-asignar', function () {
    const idOrden = $(this).data('id');
    
    $.ajax({
        url: "modelo/ordenes_admin.php",
        type: "POST",
        data: { 
            accion: "obtener_orden",
            id: idOrden 
        },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success" && response.orden) {
                const orden = response.orden;
                
                $("#id_orden_asignar").val(idOrden);
                $("#info_equipo_asignar").text(orden.Equipo || '-');
                $("#info_prioridad_asignar").text(orden.Prioridad || '-');
                
                const modal = new bootstrap.Modal(document.getElementById('modalAsignarTecnico'));
                modal.show();
            } else {
                Swal.fire('Error', response.msg || 'No se pudo obtener la información de la orden', 'error');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            Swal.fire('Error', 'Error al cargar los datos de la orden', 'error');
        }
    });
});

// Evento para mostrar disponibilidad del técnico al seleccionarlo
$(document).on('change', '#tecnico_asignado', function () {
    const idTecnico = $(this).val();
    
    if (idTecnico) {
        $.ajax({
            url: "modelo/ordenes_admin.php",
            type: "POST",
            data: { 
                accion: "disponibilidad_tecnico",
                id_tecnico: idTecnico 
            },
            dataType: "json",
            success: function (response) {
                if (response && response.status === "success") {
                    // CORRECCIÓN: Usar 'ordenes_activas' según el modelo PHP actualizado
                    const ordenes = response.ordenes_activas || 0; 
                    const maxOrdenes = 3;
                    let clase = 'text-success';
                    let mensaje = 'Disponible';
                    let icono = 'fa-check-circle';
                    
                    if (ordenes >= maxOrdenes) {
                        clase = 'text-danger';
                        mensaje = 'No disponible (límite alcanzado)';
                        icono = 'fa-exclamation-circle';
                    } else if (ordenes === maxOrdenes - 1) {
                        clase = 'text-warning';
                        mensaje = 'Última orden disponible';
                        icono = 'fa-exclamation-triangle';
                    }
                    
                    $("#disponibilidad-tecnico-asignar").html(`
                        <small class="${clase}">
                            <i class="fas ${icono}"></i>
                            ${mensaje} (${ordenes}/${maxOrdenes} órdenes activas)
                        </small>
                    `);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al verificar disponibilidad:", status, error);
                $("#disponibilidad-tecnico-asignar").html('');
            }
        });
    } else {
        $("#disponibilidad-tecnico-asignar").html('');
    }
});

// Evento para enviar el formulario de asignación
$(document).on('submit', '#formAsignarTecnico', function (e) {
    e.preventDefault();
    
    const idOrden = $("#id_orden_asignar").val();
    const idTecnico = $("#tecnico_asignado").val();
    
    if (!idTecnico) {
        Swal.fire('Advertencia', 'Debe seleccionar un técnico', 'warning');
        return;
    }
    
    $.ajax({
        url: "modelo/ordenes_admin.php",
        type: "POST",
        data: {
            accion: "asignar_tecnico",
            id_orden: idOrden,
            id_tecnico: idTecnico
        },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success") {
                Swal.fire('Éxito', response.msg || 'Técnico asignado correctamente', 'success');
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAsignarTecnico'));
                modal.hide();
                
                cargarOrdenesPendientes();
                cargarOrdenesAsignadas();
                obtenerCantidadOrdenesPendientes();
                obtenerCantidadOrdenesAsignadas();
            } else {
                Swal.fire('Error', response.msg || 'No se pudo asignar el técnico', 'error');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            Swal.fire('Error', 'Error al asignar el técnico', 'error');
        }
    });
});

// Inicializar eventos para reasignación
function inicializarEventosReasignacion() {
    // Evento para abrir modal de reasignar técnico
    $(document).on('click', '.btn-reasignar', function () {
        const idOrden = $(this).data('id');
        
        $.ajax({
            url: "modelo/ordenes_admin.php",
            type: "POST",
            data: { 
                accion: "obtener_orden",
                id: idOrden 
            },
            dataType: "json",
            success: function (response) {
                if (response && response.status === "success" && response.orden) {
                    const orden = response.orden;
                    
                    $("#id_orden_reasignar").val(idOrden);
                    $("#info_equipo_reasignar").text(orden.Equipo || '-');
                    $("#info_prioridad_reasignar").text(orden.Prioridad || '-');
                    
                    $("#disponibilidad-tecnico-reasignar").html('');
                    
                    const modal = new bootstrap.Modal(document.getElementById('modalReasignarTecnico'));
                    modal.show();
                } else {
                    Swal.fire('Error', response.msg || 'No se pudo obtener la información de la orden', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error AJAX:", status, error);
                Swal.fire('Error', 'Error al cargar los datos de la orden', 'error');
            }
        });
    });
    
    // Evento para mostrar disponibilidad del técnico al seleccionarlo (reasignación)
    $(document).on('change', '#tecnico_reasignado', function () {
        const idTecnico = $(this).val();
        
        if (idTecnico) {
            $.ajax({
                url: "modelo/ordenes_admin.php",
                type: "POST",
                data: { 
                    accion: "disponibilidad_tecnico",
                    id_tecnico: idTecnico 
                },
                dataType: "json",
                success: function (response) {
                    if (response && response.status === "success") {
                        // CORRECCIÓN: Usar 'ordenes_activas' según el modelo PHP actualizado
                        const ordenes = response.ordenes_activas || 0; 
                        const maxOrdenes = 3;
                        let clase = 'text-success';
                        let mensaje = 'Disponible';
                        let icono = 'fa-check-circle';
                        
                        if (ordenes >= maxOrdenes) {
                            clase = 'text-danger';
                            mensaje = 'No disponible (límite alcanzado)';
                            icono = 'fa-exclamation-circle';
                        } else if (ordenes === maxOrdenes - 1) {
                            clase = 'text-warning';
                            mensaje = 'Última orden disponible';
                            icono = 'fa-exclamation-triangle';
                        }
                        
                        $("#disponibilidad-tecnico-reasignar").html(`
                            <small class="${clase}">
                                <i class="fas ${icono}"></i>
                                ${mensaje} (${ordenes}/${maxOrdenes} órdenes activas)
                            </small>
                        `);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al verificar disponibilidad:", status, error);
                    $("#disponibilidad-tecnico-reasignar").html('');
                }
            });
        } else {
            $("#disponibilidad-tecnico-reasignar").html('');
        }
    });
    
    // Evento para enviar el formulario de reasignación
    $(document).on('submit', '#formReasignarTecnico', function (e) {
        e.preventDefault();
        
        const idOrden = $("#id_orden_reasignar").val();
        const idNuevoTecnico = $("#tecnico_reasignado").val();
        
        if (!idNuevoTecnico) {
            Swal.fire('Advertencia', 'Debe seleccionar un técnico', 'warning');
            return;
        }
        
        Swal.fire({
            title: '¿Reasignar técnico?',
            text: 'Esta acción reasignará la orden al nuevo técnico seleccionado.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, reasignar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                realizarReasignacion(idOrden, idNuevoTecnico);
            }
        });
    });
    
    // Limpiar formulario al cerrar modal de reasignación
    document.getElementById('modalReasignarTecnico').addEventListener('hidden.bs.modal', function () {
        $("#formReasignarTecnico")[0].reset();
        $("#disponibilidad-tecnico-reasignar").html('');
        $("#info_equipo_reasignar").text('-');
        $("#info_prioridad_reasignar").text('-');
    });
}

// Función para realizar la reasignación
function realizarReasignacion(idOrden, idNuevoTecnico) {
    $.ajax({
        url: "modelo/ordenes_admin.php",
        type: "POST",
        data: {
            accion: "reasignar_tecnico",
            id_orden: idOrden,
            id_nuevo_tecnico: idNuevoTecnico
        },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success") {
                Swal.fire({
                    title: 'Éxito',
                    text: response.msg || 'Técnico reasignado correctamente',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalReasignarTecnico'));
                modal.hide();
                
                cargarOrdenesAsignadas();
                obtenerCantidadOrdenesAsignadas();
            } else {
                Swal.fire('Error', response.msg || 'No se pudo reasignar el técnico', 'error');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            let errorMessage = 'Error al reasignar el técnico';
            
            // Intento de manejar errores del SP o JSON
            if (xhr.responseText) {
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.msg) {
                        errorMessage = errorResponse.msg;
                    }
                } catch (e) {
                    if (xhr.responseText.includes('SQLSTATE')) {
                        const match = xhr.responseText.match(/MESSAGE_TEXT = '([^']+)'/);
                        if (match && match[1]) {
                            errorMessage = match[1];
                        }
                    }
                }
            }
            
            Swal.fire('Error', errorMessage, 'error');
        }
    });
}

// Evento para abrir modal de detalles de orden
$(document).on('click', '.btn-detalles', function (e) {
    e.stopPropagation();
    
    const idOrden = $(this).data('id');
    
    $.ajax({
        url: "modelo/ordenes_admin.php",
        type: "POST",
        data: { 
            accion: "obtener_orden",
            id: idOrden 
        },
        dataType: "json",
        success: function (response) {
            if (response && response.status === "success" && response.orden) {
                const orden = response.orden;
                
                $("#prioridad").val(orden.Prioridad || '');
                $("#fecha_orden").val(orden.Fecha_Orden || '');
                $("#equipo").val(orden.Equipo || '');
                $("#solicitante").val(orden.Solicitante || '');
                $("#razon").val(orden.Razon || '');
                
                const modal = new bootstrap.Modal(document.getElementById('modalDetallesOrden'));
                modal.show();
            } else {
                Swal.fire('Error', response.msg || 'No se pudo obtener la información de la orden', 'error');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            Swal.fire('Error', 'Error al cargar los detalles de la orden', 'error');
        }
    });
});

// Limpiar formulario al cerrar modal de asignación
document.getElementById('modalAsignarTecnico').addEventListener('hidden.bs.modal', function () {
    $("#formAsignarTecnico")[0].reset();
    $("#disponibilidad-tecnico-asignar").html('');
    $("#info_equipo_asignar").text('-');
    $("#info_prioridad_asignar").text('-');
});