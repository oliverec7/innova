<?php
    require_once("modelo/conection.php");
    require_once("controlador/verificarSesion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Equipos</title>
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
                    <h3 class="text-dark mb-4 text-custom-primary">EQUIPOS A MI DISPOSICIÓN</h3>

                    <!-- Tabla -->
                    <div class="card border-0 shadow mt-4">
                        <div class="card-header py-3 card-header-custom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fas fa-list-alt me-2"></i>
                                        Listado de mis Equipos
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover nowrap w-100" id="tablaEquipos">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Cód. Patrimonial</th>
                                            <th class="text-center">Equipo</th>
                                            <th class="text-center">Tipo</th>
                                            <th class="text-center">Marca</th>
                                            <th class="text-center">Serie</th>
                                            <th class="text-center">Modelo</th>
                                            <th class="text-center">Fecha Compra</th>
                                            <th class="text-center">Fecha Instalación</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="datosTabla" class="text-center">
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <i class="fas fa-desktop fa-2x mb-2 text-custom-primary"></i>
                                                <p class="text-muted">No hay equipos registrados</p>
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
                        <!-- Campo oculto para ID del equipo -->
                        <input type="hidden" id="id" name="id">
                        
                        <div class="row">
                            <!-- Información del equipo -->
                            <div class="col-12 mb-4">
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>
                                        <strong>Equipo seleccionado:</strong>
                                        <input type="text" id="nombreEquipoSeleccionado" class="form-control mt-2 border-custom bg-white" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Campo de razón -->
                            <div class="col-12 mb-3">
                                <label for="razon" class="form-label form-label-custom">
                                    <i class="fas fa-comment-alt me-1"></i>
                                    <strong>Razón/Motivo de la Inspección:</strong>
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea name="razon" id="razon" class="form-control border-custom" rows="6" 
                                          placeholder="Por favor, describa detalladamente el motivo de su solicitud de inspección. Incluya cualquier problema, anomalía o necesidad de mantenimiento que haya observado en el equipo (mínimo 10 caracteres)." 
                                          required
                                          maxlength="500"></textarea>
                                <div class="form-text">
                                    <span id="contadorCaracteres">0</span> / 500 caracteres
                                </div>
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
                                    <i class="fas fa-paper-plane me-2"></i> Enviar Solicitud
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
    <script src="vista/js/equipos_empleado.js"></script>
    
    <!-- Script para contador de caracteres -->
    <script>
        $(document).ready(function() {
            $('#razon').on('input', function() {
                const longitud = $(this).val().length;
                $('#contadorCaracteres').text(longitud);
            });
        });
    </script>
</body>
</html>