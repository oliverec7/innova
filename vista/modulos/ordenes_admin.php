<?php
    require_once("modelo/conection.php");
    require_once("controlador/verificarSesion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Órdenes de Trabajo</title>
    <link rel="shortcut icon" href="assets/img/img_logos/general.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="assets/fonts/material-icons.min.css">
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
                    <h3 class="text-dark mb-4 text-custom-primary">ÓRDENES DE TRABAJO</h3>

                    <!-- Panel de Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card stats-card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Órdenes
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalOrdenes">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa-solid fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card stats-card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Sin Asignar
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="ordenesPendientes">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa-solid fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card stats-card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Asignadas
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="ordenesAsignadas">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa-solid fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Órdenes Pendientes -->
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="card border-0 shadow">
                                <div class="card-header py-3 card-header-custom d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fa-solid fa-tasks me-2"></i>
                                        Órdenes Pendientes
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered nowrap w-100" id="tablaOrdenesPendientes">
                                            <thead>
                                                <tr>
                                                    <th class="text-center"><i class="fas fa-hashtag me-1"></i>#</th>
                                                    <th class="text-center"><i class="fas fa-cogs me-1"></i>Tipo</th>
                                                    <th class="text-center"><i class="fas fa-exclamation-circle me-1"></i>Prioridad</th>
                                                    <th class="text-center"><i class="fas fa-calendar-day me-1"></i>Fecha Orden</th>
                                                    <th class="text-center"><i class="fas fa-tools me-1"></i>Equipo</th>
                                                    <th class="text-center"><i class="fas fa-user me-1"></i>Solicitante</th>
                                                    <th class="text-center"><i class="fas fa-info-circle me-1"></i>Razon</th>
                                                    <th class="text-center"><i class="fas fa-cogs me-1"></i>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="datosTablaPendientes" class="text-center">
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <i class="fa-solid fa-tasks fa-2x mb-2 text-custom-primary"></i>
                                                        <p class="text-muted">No hay órdenes pendientes</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Órdenes Asignadas -->
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="card border-0 shadow">
                                <div class="card-header py-3 card-header-custom d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fa-solid fa-clipboard-list me-2"></i>
                                        Órdenes Asignadas
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered nowrap w-100" id="tablaOrdenesAsignadas">
                                            <thead>
                                                <tr>
                                                    <th class="text-center"><i class="fas fa-hashtag me-1"></i>#</th>
                                                    <th class="text-center"><i class="fas fa-cogs me-1"></i>Tipo</th>
                                                    <th class="text-center"><i class="fas fa-exclamation-circle me-1"></i>Prioridad</th>
                                                    <th class="text-center"><i class="fas fa-calendar-day me-1"></i>Fecha Orden</th>
                                                    <th class="text-center"><i class="fas fa-calendar-check me-1"></i>Fecha Programada</th>
                                                    <th class="text-center"><i class="fas fa-tools me-1"></i>Equipo</th>
                                                    <th class="text-center"><i class="fas fa-user me-1"></i>Solicitante</th>
                                                    <th class="text-center"><i class="fas fa-user-cog me-1"></i>Técnico Asignado</th>
                                                    <th class="text-center"><i class="fas fa-comment-dots me-1"></i>Razón</th>
                                                    <th class="text-center"><i class="fas fa-cog me-1"></i>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="datosTablaAsignadas" class="text-center">
                                                <tr>
                                                    <td colspan="10" class="text-center py-4">
                                                        <i class="fa-solid fa-clipboard-list fa-2x mb-2 text-custom-primary"></i>
                                                        <p class="text-muted">No hay órdenes asignadas</p>
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

    <!-- Modal Asignar Técnico -->
    <div class="modal fade" id="modalAsignarTecnico" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header card-header-custom">
                    <h5 class="modal-title text-white">
                        <i class="fa-solid fa-user-gear me-2"></i>
                        Asignar Técnico
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-info mb-4">
                        <h6 class="mb-2"><strong>Información de la Orden</strong></h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>Equipo:</strong> <span id="info_equipo_asignar">-</span></small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Prioridad:</strong> <span id="info_prioridad_asignar">-</span></small>
                            </div>
                        </div>
                    </div>

                    <form id="formAsignarTecnico">
                        <input type="hidden" id="id_orden_asignar" name="id_orden">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-custom">
                                    <i class="fa-solid fa-user me-1"></i> Técnico: <span class="text-danger">*</span>
                                </label>
                                <select name="tecnico_asignado" id="tecnico_asignado" class="form-control border-custom" required>
                                    <option value="">Seleccionar técnico</option>
                                </select>
                                <div id="disponibilidad-tecnico-asignar" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col text-center">
                                <button type="submit" class="btn px-4 me-3 btn-primary-custom">
                                    <i class="fa-solid fa-user-plus me-2"></i> Asignar
                                </button>
                                <button type="button" class="btn px-4 btn-danger-custom" data-bs-dismiss="modal">
                                    <i class="fa-solid fa-xmark me-2"></i> Cancelar
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reasignar Técnico -->
    <div class="modal fade" id="modalReasignarTecnico" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header card-header-custom">
                    <h5 class="modal-title text-white">
                        <i class="fa-solid fa-user-gear me-2"></i>
                        Reasignar Técnico
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-info mb-4">
                        <h6 class="mb-2"><strong>Información de la Orden</strong></h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>Equipo:</strong> <span id="info_equipo_reasignar">-</span></small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Prioridad:</strong> <span id="info_prioridad_reasignar">-</span></small>
                            </div>
                        </div>
                    </div>

                    <form id="formReasignarTecnico">
                        <input type="hidden" id="id_orden_reasignar" name="id_orden">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-custom">
                                    <i class="fa-solid fa-user me-1"></i> Técnico: <span class="text-danger">*</span>
                                </label>
                                <select name="tecnico_reasignado" id="tecnico_reasignado" class="form-control border-custom" required>
                                    <option value="">Seleccionar técnico</option>
                                </select>
                                <div id="disponibilidad-tecnico-reasignar" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col text-center">
                                <button type="submit" class="btn px-4 me-3 btn-primary-custom">
                                    <i class="fa-solid fa-user-plus me-2"></i> Reasignar
                                </button>
                                <button type="button" class="btn px-4 btn-danger-custom" data-bs-dismiss="modal">
                                    <i class="fa-solid fa-xmark me-2"></i> Cancelar
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div class="modal fade" id="modalDetallesOrden" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header card-header-custom">
                    <h5 class="modal-title text-white">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Detalles de la Orden
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fa-solid fa-exclamation-circle me-1"></i> Prioridad:
                            </label>
                            <input type="text" id="prioridad" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fa-solid fa-calendar me-1"></i> Fecha de Orden:
                            </label>
                            <input type="text" id="fecha_orden" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fa-solid fa-screwdriver-wrench me-1"></i> Equipo:
                            </label>
                            <input type="text" id="equipo" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fa-solid fa-user me-1"></i> Solicitante:
                            </label>
                            <input type="text" id="solicitante" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fa-solid fa-comment me-1"></i> Razón:
                            </label>
                            <textarea id="razon" class="form-control border-custom" rows="3" readonly></textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col text-center">
                            <button type="button" class="btn px-4 btn-danger-custom" data-bs-dismiss="modal">
                                <i class="fa-solid fa-xmark me-2"></i> Cerrar
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="libs/js/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="vista/js/ordenes_admin.js"></script>
</body>
</html>
