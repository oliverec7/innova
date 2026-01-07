<?php
    require_once("modelo/conection.php");
    require_once("controlador/verificarSesion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Solicitudes Recibidas</title>

    <link rel="shortcut icon" href="assets/img/img_logos/general.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/material-icons.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" href="vista/css/modulos.css">
</head>

<body id="page-top">
    <div id="wrapper">

        <?php require('vista/includes/cabecera.php'); ?>

        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">

                <?php require('vista/includes/cabeceraPerfil.php'); ?>

                <div class="container-fluid">
                    <h3 class="text-dark mb-4 text-custom-primary">SOLICITUDES
                    </h3>

                    <div class="row mb-4">
                        <div class="row g-3 justify-content-center">

                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="card shadow-sm border-0 text-white bg-primary">
                                    <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="fw-bold mb-0" id="totalSolicitudes">0</h5>
                                            <span><b><i class="fas fa-list me-1"></i> Total</b></span>
                                        </div>
                                        <i class="fas fa-clipboard-list fa-lg opacity-75"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="card shadow-sm border-0 text-dark bg-warning">
                                    <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="fw-bold mb-0" id="solicitudesPendientes">0</h5>
                                            <span><b><i class="fas fa-hourglass-half me-1"></i> Pendientes</b></span>
                                        </div>
                                        <i class="fas fa-clock fa-lg opacity-75"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="card shadow-sm border-0 text-white bg-success">
                                    <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="fw-bold mb-0" id="solicitudesAprobadas">0</h5>
                                            <span><b><i class="fas fa-check me-1"></i> Aprobadas</b></span>
                                        </div>
                                        <i class="fas fa-check-circle fa-lg opacity-75"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="card shadow-sm border-0 text-white bg-danger">
                                    <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="fw-bold mb-0" id="solicitudesRechazadas">0</h5>
                                            <span><b><i class="fas fa-times me-1"></i> Rechazadas</b></span>
                                        </div>
                                        <i class="fas fa-times-circle fa-lg opacity-75"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- TABLA DE SOLICITUDES -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card border-0 shadow">
                                <div class="card-header py-3 card-header-custom">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fas fa-history me-2"></i>
                                        Historial de Solicitudes Recibidas
                                    </h5>
                                </div>

                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover nowrap w-100" id="tablaSolicitudes">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center"><i class="fas fa-hashtag me-1"></i> #</th>
                                                    <th class="text-center"><i class="fas fa-desktop me-1"></i> Equipo</th>
                                                    <th class="text-center"><i class="fas fa-user me-1"></i> Solicitante</th>
                                                    <th class="text-center"><i class="fas fa-calendar-alt me-1"></i> Fecha Solicitud</th>
                                                    <th class="text-center"><i class="fas fa-flag me-1"></i> Estado</th>
                                                    <th class="text-center"><i class="fas fa-info-circle me-1"></i> Razón</th>
                                                    <th class="text-center"><i class="fas fa-cogs me-1"></i> Acciones</th>
                                                </tr>
                                            </thead>

                                            <tbody id="datosTabla" class="text-center">
                                                <tr>
                                                    <td colspan="7" class="text-center py-4">
                                                        <i class="fas fa-tasks fa-2x mb-2 text-custom-primary"></i>
                                                        <p class="text-muted">No hay solicitudes pendientes</p>
                                                    </td>
                                                </tr>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>

                            </div>
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
    <script src="libs/js/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="vista/js/solicitudes_admin.js"></script>

</body>
</html>
