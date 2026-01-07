$(document).ready(function(){
    $("#txtUsuario").focus();

    var veces = 0;

    // Función para manejar el inicio de sesión
    function iniciarSesion() {
        let user = $("#txtUsuario").val();
        let pass = $("#txtPassword").val();

        if (user.trim().length === 0) {
            swal("Faltan datos", "Falta ingresar el Usuario", "info");
            $("#txtUsuario").focus();
            return;
        }
        if (pass.trim().length === 0) {
            swal("Faltan datos", "Falta ingresar la contraseña", "info");
            $("#txtPassword").focus();
            return;
        }

        $.post("index.php?v=IniciarSesion", {user: user, password: pass})
        .done(function(response) {
            console.log("Respuesta del servidor:", response);
            
            try {
                const data = JSON.parse(response);

                if (data.status === "ok") {
                    // Redirigir con cualquier rol
                    if (data.rol === "Administrador" || data.rol === "Tecnico" || data.rol === "Empleado") {
                        window.location.href = "index.php?v=Inicio";
                    } else {
                        swal("Error", "Rol no reconocido: " + data.rol, "error");
                    }
                } else {
                    veces++;
                    swal("Credenciales incorrectos", "Intento " + veces, "error");
                }
            } catch (err) {
                console.error("Error parseando la respuesta:", err);
                swal("Error", "Respuesta inesperada del servidor", "error");
            }
        })
        .fail(function() {
            swal("Error", "No se pudo conectar con el servidor", "error");
        });
    }

    // Click en el botón
    $("#btnIngresar").click(function (e) { 
        e.preventDefault();
        iniciarSesion();
    });

    // Presionar Enter en los campos de texto
    $("#txtUsuario, #txtPassword").keypress(function(e) {
        if (e.which === 13) { // 13 es el código de la tecla Enter
            e.preventDefault();
            iniciarSesion();
        }
    });
});