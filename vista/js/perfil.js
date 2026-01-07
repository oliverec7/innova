$(document).ready(function () {
    const $seccionPerfil = $('#seccionPerfil');
    const $seccionEditarPerfil = $('#seccionEditarPerfil');
    const $formEditarPerfil = $('#formEditarPerfil');
    const $btnEditarPerfil = $('#btnEditarPerfil');
    const $btnCancelarEdicion = $('#btnCancelarEdicion');
    const $nombreUsuarioDisplay = $('h4.mt-3');
    
    $btnEditarPerfil.click(function () {
        $seccionPerfil.hide();
        $seccionEditarPerfil.show();
        
        const usuarioActual = $nombreUsuarioDisplay.text().trim();
        $('#usuario').val(usuarioActual).focus();
    });

    $btnCancelarEdicion.click(function () {
        $seccionEditarPerfil.hide();
        $seccionPerfil.show();
        $formEditarPerfil[0].reset();
        $('.is-invalid').removeClass('is-invalid');
    });

    $('#actual, #nuevo').on('input', function() {
        if ($(this).val().length > 0) {
            $(this).removeClass('is-invalid');
        }
    });

    $formEditarPerfil.submit(function (e) {
        e.preventDefault();
        
        const $usuario = $('#usuario');
        const $claveActual = $('#actual');
        const $claveNueva = $('#nuevo');
        
        let isValid = true;
        
        if ($usuario.val().trim().length < 3) {
            $usuario.addClass('is-invalid');
            isValid = false;
        } else {
            $usuario.removeClass('is-invalid');
        }
        
        if ($claveActual.val().length === 0) {
            $claveActual.addClass('is-invalid');
            isValid = false;
        } else {
            $claveActual.removeClass('is-invalid');
        }
        
        if ($claveNueva.val().length === 0) {
            $claveNueva.addClass('is-invalid');
            isValid = false;
        } else {
            $claveNueva.removeClass('is-invalid');
        }
        
        if (!isValid) {
            swal("Advertencia", "Por favor complete todos los campos correctamente", "warning");
            return;
        }

        swal({
            title: "Actualizando perfil",
            text: "Por favor espere...",
            icon: "info",
            buttons: false,
            closeOnClickOutside: false,
            closeOnEsc: false
        });

        const datos = {
            accion: 'actualizarUsuario',
            idUsuario: $('#idUsuario').val(),
            usuarioNuevo: $usuario.val().trim(),
            claveActual: $claveActual.val(),
            claveNueva: $claveNueva.val()
        };

        $.ajax({
            type: 'POST',
            url: 'modelo/perfil.php',
            data: datos,
            dataType: 'json',
            success: function (respuesta) {
                swal.close();
                if (respuesta.Valor === '1') {
                    if (respuesta.usuarioActualizado) {
                        $nombreUsuarioDisplay.text(respuesta.usuarioActualizado);
                    }
                    
                    swal({
                        title: "¡Éxito!",
                        text: respuesta.Mensaje,
                        icon: "success",
                        buttons: false,
                        timer: 2000
                    }).then(() => {
                        $seccionEditarPerfil.hide();
                        $seccionPerfil.show();
                        $formEditarPerfil[0].reset();
                    });
                } else {
                    swal("Advertencia", respuesta.Mensaje, "warning");
                    
                    if (respuesta.Mensaje.includes('contraseña actual')) {
                        $claveActual.addClass('is-invalid').focus();
                    } else if (respuesta.Mensaje.includes('nombre de usuario')) {
                        $usuario.addClass('is-invalid').focus();
                    }
                }
            },
            error: function (xhr) {
                swal.close();
                let errorMsg = "No se pudo actualizar el perfil";
                
                if (xhr.responseJSON && xhr.responseJSON.Mensaje) {
                    errorMsg = xhr.responseJSON.Mensaje;
                } else if (xhr.statusText) {
                    errorMsg += ` (${xhr.statusText})`;
                }
                swal("Error", errorMsg, "error");
            }
        });
    });
});