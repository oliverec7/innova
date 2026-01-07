<?php
    require_once("modelo/conection.php");
    require_once("controlador/verificarSesion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Mantenimientos</title>
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
                    <h3 class="text-dark mb-4 text-custom-primary">MANTENIMIENTOS</h3>

                    <div class="card border-0 shadow mt-4">
                        <div class="card-header py-3 card-header-custom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fas fa-tools me-2"></i>
                                        Listado de Mantenimientos Asignados
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered nowrap w-100" id="tablaMisMantenimientosAsignados">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Equipo</th>
                                            <th class="text-center">Responsable Equipo</th>
                                            <th class="text-center">Fecha Programada</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="datosTablaAsignados" class="text-center">
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fas fa-hammer fa-2x mb-2 text-custom-primary"></i>
                                                <p class="text-muted">No hay mantenimientos pendientes</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow mt-4">
                        <div class="card-header py-3 card-header-custom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fas fa-check-double me-2"></i>
                                        Listado de Mantenimientos Finalizados
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered nowrap w-100" id="tablaMisMantenimientosFinalizados">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Equipo</th>
                                            <th class="text-center">Responsable Equipo</th>
                                            <th class="text-center">Fecha Programada</th>
                                            <th class="text-center">Hora Inicio</th>
                                            <th class="text-center">Hora Fin</th>
                                            <th class="text-center">Resultado</th>
                                            <th class="text-center">Detalle</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="datosTablaFinalizados" class="text-center">
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <i class="fas fa-check-circle fa-2x mb-2 text-custom-primary"></i>
                                                <p class="text-muted">No hay mantenimientos finalizados</p>
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
    </div>

    <div class="modal fade" id="modalRealizarMantenimiento" tabindex="-1" aria-labelledby="modalRealizarMantenimientoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header card-header-custom">
                    <h5 class="modal-title text-white" id="modalRealizarMantenimientoLabel">
                        <i class="fas fa-info-circle me-2"></i>
                        Realizar Mantenimiento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formFinalizarMantenimiento">
                        <input type="hidden" id="finalizarIdMantenimiento" name="idMantenimiento">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="finalizarResultado" class="form-label form-label-custom">
                                    <i class="fas fa-check-square me-1"></i>Resultado:
                                </label>
                                <select name="resultado" id="finalizarResultado" class="form-control border-custom" required>
                                    <option value="" disabled selected>Seleccione un resultado</option>
                                    <option value="Funcional">Funcional</option>
                                    <option value="No Funcional">No Funcional</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="finalizarDetalle" class="form-label form-label-custom">
                                    <i class="fas fa-pencil-alt me-1"></i>Detalle de la Corrección:
                                </label>
                                <textarea id="finalizarDetalle" name="detalle" class="form-control border-custom" rows="3" required minlength="5"></textarea>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col text-center">
                                <button type="submit" class="btn px-4 btn-primary-custom">
                                    <i class="fas fa-save me-2"></i> Finalizar Mantenimiento
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVerMantenimiento" tabindex="-1" aria-labelledby="modalVerMantenimientoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header card-header-custom">
                    <h5 class="modal-title text-white" id="modalVerMantenimientoLabel">
                        <i class="fas fa-eye me-2"></i>
                        Detalles del Mantenimiento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarMantenimiento">
                        <input type="hidden" id="editarIdMantenimiento" name="idMantenimiento">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editarResultado" class="form-label form-label-custom">Resultado:</label>
                                <select name="resultado" id="editarResultado" class="form-control border-custom" required>
                                    <option value="" disabled selected>Seleccione un resultado</option>
                                    <option value="Funcional">Funcional</option>
                                    <option value="No Funcional">No Funcional</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editarDetalle" class="form-label form-label-custom">Detalle de la Corrección</label>
                                <textarea class="form-control border-custom" id="editarDetalle" name="detalle" rows="3" required minlength="5"></textarea>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn px-4 me-3 btn-primary-custom">
                                        <i class="fas fa-save me-2"></i> Actualizar
                                </button> 
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
    <script src="vista/js/mantenimientos_tecnico.js"></script>
</body>
</html>