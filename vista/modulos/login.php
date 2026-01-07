<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login | INNOVA</title>
    <link rel="shortcut icon" href="assets/img/img_logos/general.png">

    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="vista/css/login.css">
</head>

<body>
    <div class="container h-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card login-card animate__animated animate__fadeIn">
                    <div class="card-body p-0">
                        <div class="row no-gutters">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <div class="text-center">
                                        <h2 class="login-title">Bienvenido a INNOVA</h2>
                                        <p class="text-muted mb-4">Ingresa tus credenciales para acceder al sistema</p>
                                    </div>
                                    <form class="user">
                                        <div class="mb-3">
                                            <input class="form-control form-control-user" type="text" id="txtUsuario"
                                                aria-describedby="" placeholder="Usuario" required autocomplete="username">
                                        </div>
                                        <div class="mb-3">
                                            <input class="form-control form-control-user" type="password"
                                                id="txtPassword" placeholder="Contraseña" required autocomplete="current-password">
                                        </div>
                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <div class="form-check remember-me">
                                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                                <label class="form-check-label" for="rememberMe">
                                                    Recordarme
                                                </label>
                                            </div>
                                            <a href="#" class="forgot-password" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                                                ¿Olvidaste tu contraseña?
                                            </a>
                                        </div>
                                        <button id="btnIngresar" class="btn btn-login d-block btn-user w-100 mb-3" type="button">
                                            <i class="fas fa-lock-open me-2"></i> Iniciar Sesión
                                        </button>
                                        <div class="text-center">
                                            <br><b><span>Copyright © INNOVA <span id="year"></span>. Todos los derechos reservados.</span></b><br/>
                                            <script>
                                                document.getElementById("year").textContent = new Date().getFullYear();
                                            </script>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para recuperación de contraseña -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Recuperar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Ingresa tu correo electrónico o número de teléfono registrado para recuperar tu contraseña.</p>
                    <form id="forgotPasswordForm">
                        <div class="mb-3">
                            <label for="recoveryContact" class="form-label">Correo Electrónico o Teléfono</label>
                            <input type="text" class="form-control form-control-user" id="recoveryContact" 
                                placeholder="tu@email.com o 999999999" required>
                            <small class="form-text text-muted">Ingresa el correo o teléfono registrado en el sistema</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Método de envío</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodoEnvio" id="metodoEmail" value="email" checked>
                                <label class="form-check-label" for="metodoEmail">
                                    Enviar por correo electrónico
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodoEnvio" id="metodoSMS" value="sms">
                                <label class="form-check-label" for="metodoSMS">
                                    Enviar por SMS
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="sendRecoveryEmail">Enviar Enlace</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="libs/js/jquery-3.6.3.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="libs/js/sweetalert.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="vista/js/login.js"></script>
</body>
</html>