$(document).ready(function () {
    mostrarDatos();

    $('#formMarca').submit(function (e) {
        e.preventDefault();

        const marca = $('#marca').val().trim();
        const estado = $('#estado').val();

        if (!marca || !estado) {
            Swal.fire('Advertencia', 'Debe completar todos los campos.', 'warning');
            return;
        }

        const datos = {
            id: $('#id').val(),
            marca: marca,
            estado: estado,
            accion: $('#id').val() ? 'actualizar' : 'insertar'
        };

        $.post('modelo/marcas.php', datos, function (res) {
            Swal.fire({
                title: res.status === 'ok' ? 'Éxito' : 'Error',
                text: res.msg,
                icon: res.status === 'ok' ? 'success' : 'error'
            });

            if (res.status === 'ok') {
                $('#formMarca')[0].reset();
                $('#id').val('');
                mostrarDatos();
                $('#btnGuardar').html('<i class="fas fa-plus me-2"></i> Guardar');
            }
        }, 'json');
    });

    $(document).on('click', '.btn-editar', function () {
        $('#id').val($(this).data('id'));
        $('#marca').val($(this).data('marca'));
        $('#estado').val($(this).data('estado'));
        $('#btnGuardar').html('<i class="fas fa-edit me-2"></i> Actualizar');
    });

    $('#btnCancelar').click(function () {
        $('#formMarca')[0].reset();
        $('#id').val('');
        $('#btnGuardar').html('<i class="fas fa-plus me-2"></i> Guardar');
    });
});

function mostrarDatos() {
    $.post('modelo/marcas.php', { accion: 'listar' }, function (data) {
        let filas = '';
        data.forEach((row, index) => {
            filas += `<tr>
                <td>${index + 1}</td>
                <td>${row.Marca}</td>
                <td>${row.Estado}</td>
                <td>
                    <button title="Editar" class="btn btn-sm btn-outline-primary me-1 btn-editar"
                        data-id="${row.id}"
                        data-marca="${row.Marca}"
                        data-estado="${row.Estado}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button title="Eliminar" class="btn btn-sm btn-outline-danger" onclick="eliminar(${row.id})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;
        });
        // Limpiar DataTable si ya existe
        if ($.fn.DataTable.isDataTable('#tablaMarcas')) {
            $('#tablaMarcas').DataTable().clear().destroy();
        }

        // Insertar las filas nuevas
        $('#tablaDatos').html(filas);

        // Inicializar DataTable
        $('#tablaMarcas').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            lengthMenu: [ [5, 10, 25, 50], [5, 10, 25, 50] ]
        });
    }, 'json');
}

function eliminar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará la marca permanentemente',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('modelo/marcas.php', { accion: 'eliminar', id: id }, function (res) {
                Swal.fire({
                    title: res.status === 'ok' ? 'Eliminado' : 'Error',
                    text: res.msg,
                    icon: res.status === 'ok' ? 'success' : 'error'
                });
                if (res.status === 'ok') {
                    mostrarDatos();
                }
            }, 'json');
        }
    });
}