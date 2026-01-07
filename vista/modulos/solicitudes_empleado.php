<?php
    require_once("modelo/conection.php");
    require_once("controlador/verificarSesion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Mis Solicitudes</title>
    <link rel="shortcut icon" href="assets/img/img_logos/general.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
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
                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <h3 class="text-dark mb-1 text-custom-primary">MIS SOLICITUDES</h3>
                            <p class="text-muted">Gestión y seguimiento de mis solicitudes de inspección</p>
                        </div>
                        <div class="col-lg-4 text-end">
                            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalNuevaSolicitud">
                                <i class="fas fa-plus me-2"></i> Nueva Solicitud
                            </button>
                        </div>
                    </div>

                    <!-- Estadísticas rápidas -->
                    <div class="row">
                        <div class="row g-3 justify-content-center">
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="card shadow-sm border-0 text-white bg-primary">
                                    <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center">
                                        <div> 
                                            <h5 class="fw-bold mb-0" id="totalSolicitudes">0</h5>
                                            <span><b>Total</b></span>
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
                                            <span><b>Pendientes</b></span>
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
                                            <span><b>Aprobadas</b></span>
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
                                            <span><b>Rechazadas</b></span>
                                        </div>
                                        <i class="fas fa-times-circle fa-lg opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="card border-0 shadow">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label for="filtro-estado" class="form-label form-label-custom">Estado:</label>
                                            <select id="filtro-estado" class="form-control border-custom">
                                                <option value="">Todos los estados</option>
                                                <option value="Pendiente">Pendiente</option>
                                                <option value="Aprobada">Aprobada</option>
                                                <option value="Rechazada">Rechazada</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="filtro-fecha" class="form-label form-label-custom">Fecha:</label>
                                            <input type="month" id="filtro-fecha" class="form-control border-custom">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de solicitudes -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card border-0 shadow">
                                <div class="card-header py-3 card-header-custom">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fas fa-history me-2"></i>
                                        Historial de Solicitudes
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover nowrap w-100" id="tablaMisSolicitudes">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">#</th>
                                                    <th class="text-center">Equipo</th>
                                                    <th class="text-center">Fecha Solicitud</th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Razón</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="datosTabla" class="text-center">
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <i class="fas fa-tasks fa-2x mb-2 text-custom-primary"></i>
                                                        <p class="text-muted">No hay solicitudes registradas</p>
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

    <!-- Modal para Solicitud de Inspección -->
    <div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalNuevaSolicitudLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header card-header-custom">
                    <h5 class="modal-title text-white" id="modalNuevaSolicitudLabel">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Solicitar Inspección de Equipo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formNuevaSolicitud">
                        <div class="row">
                            <!-- Información del equipo -->
                            <div class="col-md-12 mb-3">
                                <label for="equipo" class="form-label form-label-custom">
                                    Equipo: <span class="text-danger">*</span>
                                </label>
                                <select name="equipo" id="equipo" class="form-control border-custom" required>
                                    <option value="">Seleccionar equipo...</option>
                                </select>
                            </div>
                            
                            <!-- Campo de razón -->
                            <div class="col-12 mb-3">
                                <label for="razon" class="form-label form-label-custom">
                                    <strong>Razón/Motivo de la Inspección:</strong>
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea name="razon" id="razon" class="form-control border-custom" rows="6" 
                                          placeholder="Por favor, describa detalladamente el motivo de su solicitud de inspección. Incluya cualquier problema, anomalía o necesidad de mantenimiento que haya observado en el equipo (mínimo 10 caracteres)." 
                                          required
                                          maxlength="500"></textarea>
                                <small class="text-muted">Caracteres: <span id="contadorCaracteres">0</span>/500</small>
                            </div>
                            
                            <!-- Nota informativa -->
                            <div class="col-12 mb-3">
                                <div class="alert alert-warning d-flex align-items-start" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                                    <div>
                                        <strong>Importante:</strong> Una vez enviada la solicitud, el equipo quedará registrado para revisión. 
                                        No podrá enviar una nueva solicitud si el equipo ya tiene una orden de trabajo pendiente.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones -->
                        <div class="row mt-4">
                            <div class="col text-center">
                                <button type="submit" class="btn px-4 me-3 btn-primary-custom">
                                    <i class="fas fa-paper-plane me-2"></i> Enviar
                                </button>    
                                <button type="button" class="btn px-4 btn-danger-custom" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Solicitud -->
    <div class="modal fade" id="modalEditarSolicitud" tabindex="-1" aria-labelledby="modalEditarSolicitudLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header card-header-custom">
                    <h5 class="modal-title text-white" id="modalEditarSolicitudLabel">
                        <i class="fas fa-edit me-2"></i>
                        Modificar Solicitud
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarSolicitudModal">
                        <input type="hidden" id="id_modal" name="id">
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="equipo_modal" class="form-label form-label-custom">
                                    Equipo: <span class="text-danger">*</span>
                                </label>
                                <select name="equipo_modal" id="equipo_modal" class="form-control border-custom" required>
                                    <option value="">Seleccionar equipo...</option>
                                </select>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="razon_modal" class="form-label form-label-custom">
                                    <strong>Razón/Motivo de la Inspección:</strong>
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea name="razon_modal" id="razon_modal" class="form-control border-custom" rows="6" 
                                          placeholder="Por favor, describa detalladamente el motivo de su solicitud de inspección. Incluya cualquier problema, anomalía o necesidad de mantenimiento que haya observado en el equipo (mínimo 10 caracteres)." 
                                          required
                                          maxlength="500"></textarea>
                                <small class="text-muted">Caracteres: <span id="contadorCaracteres_modal">0</span>/500</small>
                            </div>
                            
                            <!-- Nota informativa -->
                            <div class="col-12 mb-3">
                                <div class="alert alert-info d-flex align-items-start" role="alert">
                                    <i class="fas fa-info-circle me-2 mt-1"></i>
                                    <div>
                                        <strong>Importante:</strong> Una vez enviada la solicitud, el equipo quedará registrado para revisión. 
                                        No podrá enviar una nueva solicitud si el equipo ya tiene una orden de trabajo pendiente.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col text-center">
                                <button type="button" class="btn px-4 me-3 btn-primary-custom" id="btnActualizar">
                                    <i class="fas fa-save me-2"></i> Reenviar
                                </button>    
                                <button type="button" class="btn px-4 btn-danger-custom" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
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
    <script src="vista/js/solicitudes_empleado.js"></script>
</body>
</html>