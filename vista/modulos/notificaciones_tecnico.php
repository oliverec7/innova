<?php
    require_once("modelo/Conection.php");
    require_once("controlador/verificarSesion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Notificaciones</title>
    <link rel="shortcut icon" href="assets/img/img_logos/general.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="vista/css/modulos.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php require('vista/includes/cabecera.php'); ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">

                <?php require('vista/includes/cabeceraPerfil.php'); ?>

                <div class="container-fluid">
                    
                    <div class="notif-container mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-dark mb-0 fw-bold text-custom-primary">
                                <i class="fas fa-bell me-2"></i>NOTIFICACIONES
                            </h3>
                            
                            <div class="w-auto">
                                <select class="form-select form-select-sm shadow-sm" id="filtroEstado">
                                    <option value="" selected>Todas</option>
                                    <option value="leido">Leídas</option>
                                    <option value="no_leido">No leídas</option>
                                </select>
                            </div>
                        </div>

                        <div id="contenedorNotificaciones">
                            
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>

                        </div>
                        
                        <div id="mensajeVacio" class="text-center py-5 d-none">
                            <i class="fas fa-bell-slash fa-3x mb-3 text-muted"></i>
                            <p class="text-muted fw-bold">No tienes notificaciones en este momento.</p>
                        </div>

                    </div>
                </div>
            </div>
            <?php require('vista/includes/piePagina.php'); ?>
        </div>
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="vista/js/notificaciones_tecnico.js"></script>
</body>
</html>