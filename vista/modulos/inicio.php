<?php
require_once('modelo/Conection.php');
require_once("controlador/verificarSesion.php");

if (session_status() === PHP_SESSION_NONE) session_start();
$rol = $_SESSION['rol'] ?? 0;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Inicio</title>
    <link rel="shortcut icon" href="assets/img/img_logos/general.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/material-icons.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="vista/css/inicio.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php require('vista/includes/cabecera.php'); ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <?php require('vista/includes/cabeceraPerfil.php'); ?>
                <div class="container-fluid mt-4">
                    <div class="welcome-section container text-center">
                        <div class="col-lg-12 text-center">
                            <h1 class="text-dark fw-bold mb-3">BIENVENIDO A INNOVA</h1>
                            <p class="lead text-muted mb-4">
                                <h2>
                                    Plataforma integral orientada a la gestión eficiente de activos informáticos, 
                                    programación de inspecciones técnicas, generación de alertas inteligentes y 
                                    control de mantenimientos preventivos y correctivos. Diseñada para fortalecer 
                                    la seguridad operativa y optimizar los procesos institucionales.
                                </h2>
                            </p>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="row">
                        <h3 class="text-dark text-center fw-bold mb-3">ACCIONES RÁPIDAS</h3>
                        
                        <?php if ($rol == 'Administrador'): ?>
                        <div class="row text-center justify-content-center">
                            <div class="col-md-3 mb-3">
                                <a href="index.php?v=EquiposAdmin" class="btn btn-outline-primary btn-lg w-100 py-3"><i class="fas fa-plus-circle me-2"></i>Nuevo Equipo</a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="index.php?v=OrdenesAdmin" class="btn btn-outline-success btn-lg w-100 py-3"><i class="fas fa-user-md me-2"></i>Asignar Tecnico</a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="index.php?v=MantenimientosAdmin" class="btn btn-outline-warning btn-lg w-100 py-3"><i class="fas fa-calendar-alt me-2"></i>Mantenimientos</a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="index.php?v=Reportes" class="btn btn-outline-info btn-lg w-100 py-3"><i class="fas fa-chart-bar me-2"></i>Reportes</a>
                            </div>
                        </div>

                        <?php elseif ($rol == 'Empleado'): ?>
                        <div class="row text-center justify-content-center">
                            <div class="col-md-3 mb-3">
                                <a href="index.php?v=SolicitudesEmpleado" class="btn btn-outline-success btn-lg w-100 py-3"><i class="fas fa-calendar-check me-2"></i>Solicitar Inspección</a>
                            </div>
                        </div>

                        <?php elseif ($rol == 'Tecnico'): ?>
                        <div class="row text-center justify-content-center">
                            <div class="col-md-3 mb-3">
                                <a href="index.php?v=InspeccionesTecnico" class="btn btn-outline-success btn-lg w-100 py-3"><i class="fas fa-clipboard-list me-2"></i>Realizar Inspección</a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="index.php?v=MantenimientosTecnico" class="btn btn-outline-warning btn-lg w-100 py-3"><i class="fas fa-tools me-2"></i>Realizar Mantenimiento</a>
                            </div>
                        </div>

                        <?php endif; ?>

                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="card shadow mb-4 border-0">
                                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #0f62ac;">
                                    <h6 class="text-white fw-bold m-0">
                                        <i class="fas fa-chart-bar me-2"></i>Dispositivos por áreas
                                    </h6>
                                </div>
                                
                                <div class="chart-container" style="height: 400px;">
                                    <canvas id="EquiposAreaChart"></canvas>
                                </div>
                                
                                <div class="card-footer">
                                    <span>Actualizado el
                                        <script>
                                            document.write(new Date().toLocaleString('es-ES'));
                                        </script> - Fuente: <b>INNOVA</b>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php require('vista/includes/piepagina.php'); ?>
        </div>
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="libs/js/jquery-3.6.3.min.js"></script>
    <script src="libs/js/sweetalert.min.js"></script>
    <script src="libs/js/chart.google.js"></script>
    <script src="libs/js/Chart.js"></script>
    <script src="vista/js/inicio.js"></script>
</body>
</html>