$(document).ready(function () {
    
    cargarEquipos();
    cargarCombos();

    // Validación de Código Patrimonial en tiempo real
    $('#codigo_patrimonial').on('input', function() {
        filtrarCodPatrimonial();
        validarCodPatrimonial();
    });
    
    $('#codigo_patrimonial_modal').on('input', function() {
        filtrarCodPatrimonialModal();
        // Solo validar si hay un ID cargado (estamos editando)
        if ($('#id_modal').val()) {
            validarCodPatrimonialModal();
        }
    });

    // Enviar formulario principal (solo insertar)
    $('#formEquipo').on('submit', async function (e) {
        e.preventDefault();

        // Validar Código Patrimonial antes de enviar
        const codigoValido = await validarCodPatrimonial();
        if (!codigoValido) {
            Swal.fire('Error', 'Por favor, ingrese un código patrimonial válido y único', 'error');
            return;
        }

        const datos = {
            codigo_patrimonial: $('#codigo_patrimonial').val(),
            nombre_equipo: $('#nombre_equipo').val(),
            tipo_equipo: $('#tipo_equipo').val(),
            marca: $('#marca').val(),
            serie: $('#serie').val(),
            modelo: $('#modelo').val(),
            responsable: $('#responsable').val(),
            fecha_compra: $('#fecha_compra').val(),
            fecha_instalacion: $('#fecha_instalacion').val(),
            accion: 'insertar'
        };

        console.log('Datos a insertar:', datos);

        $.post('modelo/equipos_admin.php', datos, function (res) {
            Swal.fire({
                title: res.status === 'ok' ? 'Éxito' : 'Error',
                text: res.msg,
                icon: res.status === 'ok' ? 'success' : 'error'
            });

            if (res.status === 'ok') {
                $('#formEquipo')[0].reset();
                $('#codigo_patrimonial-feedback').html('').removeClass('text-success text-danger');
                cargarEquipos();
            }
        }, 'json').fail((xhr, status, error) => {
            console.error('Error:', xhr.responseText);
            Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
        });
    });

    // Cancelar edición del formulario principal
    $('#btnCancelar').click(function () {
        $('#formEquipo')[0].reset();
        $('#codigo_patrimonial-feedback').html('').removeClass('text-success text-danger');
    });

    // Variable para almacenar datos originales del equipo
    let datosOriginales = {};

    // Editar equipo - ABRE EL MODAL
    $(document).on('click', '.btn-editar', function () {
        const idEquipo = $(this).data('id');
        
        console.log('ID del equipo a editar:', idEquipo);
        
        // Obtener los datos completos del equipo
        $.post('modelo/equipos_admin.php', { 
            accion: 'obtener_por_id', 
            id: idEquipo 
        }, function(res) {
            console.log('Respuesta del servidor:', res);
            
            if (res.status === 'ok') {
                const eq = res.data;
                
                // Guardar datos originales para comparación posterior
                datosOriginales = {
                    codigo_patrimonial: eq.codigo_patrimonial,
                    nombre_equipo: eq.nombre_equipo,
                    tipo_equipo: eq.tipo_equipo,
                    marca: eq.marca,
                    serie: eq.serie,
                    modelo: eq.modelo,
                    responsable: eq.responsable,
                    fecha_compra: eq.fecha_compra,
                    fecha_instalacion: eq.fecha_instalacion
                };
                
                // Llenar el formulario del modal
                $('#id_modal').val(eq.idEquipo);
                $('#codigo_patrimonial_modal').val(eq.codigo_patrimonial);
                $('#nombre_equipo_modal').val(eq.nombre_equipo);
                $('#tipo_equipo_modal').val(eq.tipo_equipo);
                $('#marca_modal').val(eq.marca);
                $('#serie_modal').val(eq.serie);
                $('#modelo_modal').val(eq.modelo);
                $('#responsable_modal').val(eq.responsable);
                $('#fecha_compra_modal').val(eq.fecha_compra);
                $('#fecha_instalacion_modal').val(eq.fecha_instalacion);

                // Limpiar feedback del modal
                $('#codigo_patrimonial_modal-feedback').html('').removeClass('text-success text-danger');

                console.log('ID cargado en modal:', $('#id_modal').val());

                // Mostrar el modal
                $('#modalEquipo').modal('show');
            } else {
                Swal.fire('Error', 'No se pudieron cargar los datos para editar', 'error');
            }
        }, 'json').fail((xhr, status, error) => {
            console.error('Error al cargar datos:', xhr.responseText);
            Swal.fire('Error', 'Error al cargar datos para editar', 'error');
        });
    });

    // Enviar formulario del modal (actualizar)
    $('#btnActualizar').click(async function () {
        const idEquipo = $('#id_modal').val();
        
        console.log('ID al actualizar:', idEquipo);
        
        // Verificar que tengamos un ID válido
        if (!idEquipo) {
            Swal.fire('Error', 'No se puede actualizar: ID de equipo no encontrado', 'error');
            return;
        }

        // Validar Código Patrimonial antes de enviar
        const codigoValido = await validarCodPatrimonialModal();
        if (!codigoValido) {
            Swal.fire('Error', 'Por favor, ingrese un código patrimonial válido y único', 'error');
            return;
        }

        const datosActuales = {
            codigo_patrimonial: $('#codigo_patrimonial_modal').val(),
            nombre_equipo: $('#nombre_equipo_modal').val(),
            tipo_equipo: $('#tipo_equipo_modal').val(),
            marca: $('#marca_modal').val(),
            serie: $('#serie_modal').val(),
            modelo: $('#modelo_modal').val(),
            responsable: $('#responsable_modal').val(),
            fecha_compra: $('#fecha_compra_modal').val(),
            fecha_instalacion: $('#fecha_instalacion_modal').val()
        };

        // Comparar datos originales con actuales
        const huboModificaciones = Object.keys(datosActuales).some(key => {
            return datosActuales[key] != datosOriginales[key];
        });

        console.log('Datos originales:', datosOriginales);
        console.log('Datos actuales:', datosActuales);
        console.log('¿Hubo modificaciones?:', huboModificaciones);

        // Si no hay cambios, mostrar mensaje y salir
        if (!huboModificaciones) {
            Swal.fire({
                title: 'Sin cambios',
                text: 'No se realizaron modificaciones en el equipo',
                icon: 'info'
            });
            return;
        }

        const datos = {
            id: idEquipo,
            ...datosActuales,
            accion: 'actualizar'
        };

        console.log('Datos a actualizar:', datos);

        $.post('modelo/equipos_admin.php', datos, function (res) {
            console.log('Respuesta actualización:', res);
            
            Swal.fire({
                title: res.status === 'ok' ? 'Éxito' : 'Error',
                text: res.msg,
                icon: res.status === 'ok' ? 'success' : 'error'
            });

            if (res.status === 'ok') {
                $('#modalEquipo').modal('hide');
                cargarEquipos();
            }
        }, 'json').fail((xhr, status, error) => {
            console.error('Error completo:', xhr.responseText);
            Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
        });
    });

    // Limpiar formulario modal cuando se cierra
    $('#modalEquipo').on('hidden.bs.modal', function () {
        $('#formEquipoModal')[0].reset();
        $('#codigo_patrimonial_modal-feedback').html('').removeClass('text-success text-danger');
        // Limpiar datos originales
        datosOriginales = {};
    });
});

// Función para verificar si el código patrimonial ya existe
async function verificarCodPatrimonialUnico(codigo, idExcluir = null) {
    if (!codigo) return true;
    
    try {
        const respuesta = await $.post('modelo/equipos_admin.php', { 
            accion: 'verificar_cod_patrimonial',
            codigo_patrimonial: codigo,
            id_excluir: idExcluir
        });
        
        return respuesta.existe === false;
    } catch (error) {
        console.error('Error al verificar código patrimonial:', error);
        return true;
    }
}

// Función para validar Código Patrimonial en formulario principal
async function validarCodPatrimonial() {
    const codigo = $('#codigo_patrimonial').val().trim();
    const feedback = $('#codigo_patrimonial-feedback');
    
    if (codigo === '') {
        feedback.html('').removeClass('text-success text-danger');
        return true;
    }

    const codigoRegex = /^[A-Z0-9]*$/;
    
    if (!codigoRegex.test(codigo)) {
        feedback.html('<i class="fas fa-times-circle"></i> Solo se permiten letras y números').removeClass('text-success').addClass('text-danger');
        return false;
    }

    feedback.html('<i class="fas fa-spinner fa-spin"></i> Verificando...').removeClass('text-success text-danger').addClass('text-warning');
    
    const esUnico = await verificarCodPatrimonialUnico(codigo);
    
    if (esUnico) {
        feedback.html('<i class="fas fa-check-circle"></i> Código disponible').removeClass('text-danger text-warning').addClass('text-success');
        return true;
    } else {
        feedback.html('<i class="fas fa-times-circle"></i> Este código ya está en uso').removeClass('text-success text-warning').addClass('text-danger');
        return false;
    }
}

// Función para validar código patrimonial en modal
async function validarCodPatrimonialModal() {
    const codigo = $('#codigo_patrimonial_modal').val().trim();
    const idEquipo = $('#id_modal').val();
    const feedback = $('#codigo_patrimonial_modal-feedback');
    
    console.log('Validando código modal:', codigo, 'ID:', idEquipo);
    
    if (codigo === '') {
        feedback.html('').removeClass('text-success text-danger');
        return true;
    }

    const codigoRegex = /^[A-Z0-9]*$/;
    
    if (!codigoRegex.test(codigo)) {
        feedback.html('<i class="fas fa-times-circle"></i> Solo se permiten letras y números').removeClass('text-success').addClass('text-danger');
        return false;
    }

    feedback.html('<i class="fas fa-spinner fa-spin"></i> Verificando...').removeClass('text-success text-danger').addClass('text-warning');
    
    const esUnico = await verificarCodPatrimonialUnico(codigo, idEquipo);
    
    if (esUnico) {
        feedback.html('<i class="fas fa-check-circle"></i> Código disponible').removeClass('text-danger text-warning').addClass('text-success');
        return true;
    } else {
        feedback.html('<i class="fas fa-times-circle"></i> Este código ya está en uso').removeClass('text-success text-warning').addClass('text-danger');
        return false;
    }
}

// Función para convertir a mayúsculas y filtrar caracteres no permitidos en formulario principal
function filtrarCodPatrimonial() {
    const $input = $('#codigo_patrimonial');
    let valor = $input.val();
    valor = valor.toUpperCase().replace(/[^A-Z0-9]/g, '');
    $input.val(valor);
}

// Función para convertir a mayúsculas y filtrar caracteres no permitidos en modal
function filtrarCodPatrimonialModal() {
    const $input = $('#codigo_patrimonial_modal');
    let valor = $input.val();
    valor = valor.toUpperCase().replace(/[^A-Z0-9]/g, '');
    $input.val(valor);
}

// Cargar equipos
function cargarEquipos() {
    $.post('modelo/equipos_admin.php', { accion: 'listar' }, function (data) {
        if ($.fn.DataTable.isDataTable('#tablaEquipos')) {
            $('#tablaEquipos').DataTable().clear().destroy();
        }

        let filas = '';
        
        if (Array.isArray(data) && data.length > 0) {
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
                    <td>${eq.Responsable}</td>
                    <td>${eq.Compra}</td>
                    <td>${eq.Instalacion}</td>
                    <td>${eq.Estado}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary btn-editar"
                            title="Editar"
                            data-id="${eq.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="eliminarEquipo(${eq.id})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>`;
            });

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
                },
            });
        } else {
            filas = `
                <tr>
                    <td colspan="11" class="text-center py-4">
                        <i class="fas fa-desktop fa-2x mb-2 text-custom-primary"></i>
                        <p class="text-muted">No hay equipos registrados</p>
                    </td>
                </tr>`;
            $('#tablaEquipos tbody').html(filas);
        }

    }, 'json').fail(() => {
        Swal.fire('Error', 'No se pudo cargar la lista de equipos', 'error');
    });
}

function cargarCombos() {
    llenarCombo('listar_marcas', 'marca', 'idMarca', 'Marca', 'Seleccionar marca...');
    llenarCombo('listar_tipos', 'tipo_equipo', 'idTipoEquipo', 'Tipo', 'Seleccionar tipo...');
    llenarCombo('listar_personas', 'responsable', 'idPersona', 'Responsable', 'Seleccionar responsable...');
    
    // También cargar combos del modal
    llenarCombo('listar_marcas', 'marca_modal', 'idMarca', 'Marca', 'Seleccionar marca...');
    llenarCombo('listar_tipos', 'tipo_equipo_modal', 'idTipoEquipo', 'Tipo', 'Seleccionar tipo...');
    llenarCombo('listar_personas', 'responsable_modal', 'idPersona', 'Responsable', 'Seleccionar responsable...');
}

function llenarCombo(accion, idCombo, campoValor, campoTexto, mensaje = 'Seleccione...') {
    $.post('modelo/equipos_admin.php', { accion }, function (data) {
        const $combo = $(`#${idCombo}`);
        $combo.empty().append(`<option value="">${mensaje}</option>`);
        data.forEach(item => {
            $combo.append(`<option value="${item[campoValor]}">${item[campoTexto]}</option>`);
        });
    }, 'json').fail(() => {
        console.error(`Error al cargar combo ${idCombo}`);
    });
}

function eliminarEquipo(id) {
    Swal.fire({
        title: '¿Eliminar equipo?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            $.post('modelo/equipos_admin.php', { accion: 'eliminar', id }, function (res) {
                Swal.fire({
                    title: res.status === 'ok' ? 'Eliminado' : 'Error',
                    text: res.msg,
                    icon: res.status === 'ok' ? 'success' : 'error'
                });
                if (res.status === 'ok') cargarEquipos();
            }, 'json').fail(() => {
                Swal.fire('Error', 'No se pudo eliminar el equipo', 'error');
            });
        }
    });
}