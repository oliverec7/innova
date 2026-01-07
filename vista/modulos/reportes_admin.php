<?php
require_once('modelo/Conection.php');
require_once("controlador/verificarSesion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Reportes</title>
    <link rel="shortcut icon" href="assets/img/img_logos/general.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/material-icons.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="vista/css/modulos.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php require('vista/includes/cabecera.php'); ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <?php require('vista/includes/cabeceraPerfil.php'); ?>
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark mb-4 text-custom-primary">REPORTES</h3>
                    </div>
                    <ul class="nav nav-tabs" id="mainTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="kpis-tab" data-bs-toggle="tab" data-bs-target="#kpis" type="button" role="tab" aria-controls="kpis" aria-selected="true">
                                <i class="fas fa-chart-line me-2"></i>KPIs Principales
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="efficiency-tab" data-bs-toggle="tab" data-bs-target="#efficiency" type="button" role="tab" aria-controls="efficiency" aria-selected="false">
                                <i class="fas fa-cogs me-2"></i>Eficiencia Operativa
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="alerts-tab" data-bs-toggle="tab" data-bs-target="#alerts" type="button" role="tab" aria-controls="alerts" aria-selected="false">
                                <i class="fas fa-exclamation-triangle me-2"></i>Alertas y Riesgos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="trends-tab" data-bs-toggle="tab" data-bs-target="#trends" type="button" role="tab" aria-controls="trends" aria-selected="false">
                                <i class="fas fa-chart-bar me-2"></i>Tendencias
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="mainTabsContent">
                        <?php require('vista/modulos/kpis_principales.php'); ?>
                        <?php require('vista/modulos/eficiencia_operativa.php'); ?>
                        <?php require('vista/modulos/alertas_riesgos.php'); ?>
                        <?php require('vista/modulos/tendencias.php'); ?>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
</body>
</html>