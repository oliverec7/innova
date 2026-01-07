<?php
    require_once("modelo/conection.php");
    require_once("controlador/verificarSesion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Inspecciones</title>
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
                    <h3 class="text-dark mb-4 text-custom-primary">INSPECCIONES
                    </h3>

                    <!-- Tabla inspecciones pendientes -->
                    <div class="card border-0 shadow mt-4">
                        <div class="card-header py-3 card-header-custom">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-hourglass-half me-2"></i>
                                Listado de Inspecciones Pendientes
                            </h5>
                        </div>

                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered nowrap w-100" id="tablaInspeccionesPendientes">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><i class="fas fa-hashtag"></i></th>
                                            <th class="text-center"><i class="fas fa-desktop me-1"></i>Equipo</th>
                                            <th class="text-center"><i class="fas fa-user-check me-1"></i>Inspector</th>
                                            <th class="text-center"><i class="fas fa-user-tie me-1"></i>Responsable</th>
                                            <th class="text-center"><i class="fas fa-calendar-alt me-1"></i>Fecha</th>
                                            <th class="text-center"><i class="fas fa-comment-dots me-1"></i>Razón</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="fas fa-clipboard-check fa-2x mb-2 text-custom-primary"></i>
                                                <p class="text-muted">No hay inspecciones pendientes</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla inspecciones finalizadas -->
                    <div class="card border-0 shadow mt-4">
                        <div class="card-header py-3 card-header-custom">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-check-circle me-2"></i>
                                Listado de Inspecciones Finalizadas
                            </h5>
                        </div>

                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered nowrap w-100" id="tablaInspeccionesFinalizadas">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><i class="fas fa-hashtag"></i></th>
                                            <th class="text-center"><i class="fas fa-desktop me-1"></i>Equipo</th>
                                            <th class="text-center"><i class="fas fa-user-check me-1"></i>Inspector</th>
                                            <th class="text-center"><i class="fas fa-user-tie me-1"></i>Responsable</th>
                                            <th class="text-center"><i class="fas fa-calendar-alt me-1"></i>Fecha</th>
                                            <th class="text-center"><i class="fas fa-clock me-1"></i>Hora Inicio</th>
                                            <th class="text-center"><i class="fas fa-clock me-1"></i>Hora Fin</th>
                                            <th class="text-center"><i class="fas fa-clipboard-check me-1"></i>Resultado</th>
                                            <th class="text-center"><i class="fas fa-comment-alt me-1"></i>Comentario</th>
                                            <th class="text-center"><i class="fas fa-tools me-1"></i>Acciones</th>
                                        </tr>
                                    </thead>

                                    <tbody class="text-center">
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <i class="fas fa-clipboard-check fa-2x mb-2 text-custom-primary"></i>
                                                <p class="text-muted">No hay inspecciones finalizadas</p>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php require('vista/includes/piePagina.php'); ?>
        </div>
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="modalEditarInspeccion" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header card-header-custom">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-edit me-2"></i> Editar Inspección
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <form id="formEditarInspeccion">
                        <input type="hidden" id="editarIdInspeccion" name="idInspeccion">

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fas fa-clipboard-check me-1"></i>Resultado:
                            </label>
                            <select name="resultado" id="editarResultado" class="form-control border-custom" required>
                                <option value="" disabled selected>Seleccione un resultado</option>
                                <option value="Conforme">Conforme</option>
                                <option value="No conforme">No conforme</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-comment-alt me-1"></i>Comentario
                            </label>
                            <textarea class="form-control border-custom" id="editarComentario" name="comentario" rows="3" required></textarea>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn px-4 btn-primary-custom">
                                <i class="fas fa-save me-2"></i> Actualizar
                            </button>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>

    <!-- Modal Ver -->
    <div class="modal fade" id="modalVerInspeccion" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header card-header-custom">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-info-circle me-2"></i> Detalles de la Inspección
                    </h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fas fa-desktop me-1"></i>Equipo:
                            </label>
                            <input type="text" id="equipo" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fas fa-user-check me-1"></i>Inspector:
                            </label>
                            <input type="text" id="inspector" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fas fa-user-tie me-1"></i>Responsable:
                            </label>
                            <input type="text" id="responsable" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fas fa-calendar-alt me-1"></i>Fecha Inspección:
                            </label>
                            <input type="date" id="fecha_inspeccion" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fas fa-clock me-1"></i>Hora Inicio:
                            </label>
                            <input type="time" id="hora_inicio" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fas fa-clock me-1"></i>Hora Fin:
                            </label>
                            <input type="time" id="hora_fin" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">
                                <i class="fas fa-clipboard-check me-1"></i>Resultado:
                            </label>
                            <input type="text" id="resultado" class="form-control border-custom" readonly>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <i class="fas fa-comment-alt me-1"></i>Comentario:
                            </label>
                            <textarea id="comentario" class="form-control border-custom" rows="3" readonly></textarea>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button class="btn px-4 btn-danger-custom" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Cerrar
                        </button>
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
    <script src="vista/js/inspecciones_admin.js"></script>

</body>
</html>
