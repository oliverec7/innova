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
                    <h3 class="text-dark mb-4 text-custom-primary">GESTIÓN DE EQUIPOS</h3>

                    <div class="row mb-3">
                        <!-- Formulario -->
                        <div class="col-lg-12">
                            <div class="card border-0 shadow">
                                <div class="card-header py-3 card-header-custom">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fas fa-desktop me-2"></i>
                                        Registrar datos del Equipo
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <form id="formEquipo">
                                        <input type="hidden" id="id" name="id">
                                        
                                        <div class="row">

                                            <div class="col-md-4 mb-3">
                                                <label for="codigo_patrimonial" class="form-label form-label-custom">
                                                    <i class="fas fa-barcode me-1"></i> Código Patrimonial:
                                                </label>
                                                <input type="text" name="codigo_patrimonial" id="codigo_patrimonial" class="form-control border-custom" required placeholder="Código patrimonial" maxlength="12">
                                                <div id="codigo_patrimonial-feedback" class="mt-1"></div>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <label for="nombre_equipo" class="form-label form-label-custom">
                                                    <i class="fas fa-tag me-1"></i> Nombre del Equipo:
                                                </label>
                                                <input type="text" name="nombre_equipo" id="nombre_equipo" class="form-control border-custom" required placeholder="Nombre descriptivo">
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="tipo_equipo" class="form-label form-label-custom">
                                                    <i class="fas fa-layer-group me-1"></i> Tipo de Equipo:
                                                </label>
                                                <select name="tipo_equipo" id="tipo_equipo" class="form-control border-custom" required>
                                                    <option value="">Seleccionar tipo</option>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <label for="marca" class="form-label form-label-custom">
                                                    <i class="fas fa-bookmark me-1"></i> Marca:
                                                </label>
                                                <select name="marca" id="marca" class="form-control border-custom" required>
                                                    <option value="">Seleccionar marca</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="serie" class="form-label form-label-custom">
                                                    <i class="fas fa-hashtag me-1"></i> Serie:
                                                </label>
                                                <input type="text" name="serie" id="serie" class="form-control border-custom" required placeholder="Número de serie">
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <label for="modelo" class="form-label form-label-custom">
                                                    <i class="fas fa-laptop-code me-1"></i> Modelo:
                                                </label>
                                                <input type="text" name="modelo" id="modelo" class="form-control border-custom" required placeholder="Modelo del equipo">
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <label for="responsable" class="form-label form-label-custom">
                                                    <i class="fas fa-user-tie me-1"></i> Responsable:
                                                </label>
                                                <select name="responsable" id="responsable" class="form-control border-custom" required>
                                                    <option value="">Seleccionar responsable</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="fecha_compra" class="form-label form-label-custom">
                                                    <i class="fas fa-calendar-plus me-1"></i> Fecha de Compra:
                                                </label>
                                                <input type="date" name="fecha_compra" id="fecha_compra" class="form-control border-custom" required>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="fecha_instalacion" class="form-label form-label-custom">
                                                    <i class="fas fa-calendar-check me-1"></i> Fecha de Instalación:
                                                </label>
                                                <input type="date" name="fecha_instalacion" id="fecha_instalacion" class="form-control border-custom" required>
                                            </div>

                                        </div>

                                        <!-- Botones -->
                                        <div class="row mt-4">
                                            <div class="col text-center">
                                                <button type="submit" id="btnGuardar" class="btn px-4 me-3 btn-primary-custom">
                                                    <i class="fas fa-save me-2"></i> Guardar
                                                </button>
                                                <button type="reset" id="btnCancelar" class="btn px-4 btn-danger-custom">
                                                    <i class="fas fa-times me-2"></i> Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="card border-0 shadow mt-4">
                        <div class="card-header py-3 card-header-custom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fas fa-list-alt me-2"></i>
                                        Listado de Equipos
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered nowrap" id="tablaEquipos">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><i class="fas fa-hashtag me-1"></i> #</th>
                                            <th class="text-center"><i class="fas fa-barcode me-1"></i> Cód. Patrimonial</th>
                                            <th class="text-center"><i class="fas fa-desktop me-1"></i> Equipo</th>
                                            <th class="text-center"><i class="fas fa-layer-group me-1"></i> Tipo</th>
                                            <th class="text-center"><i class="fas fa-bookmark me-1"></i> Marca</th>
                                            <th class="text-center"><i class="fas fa-hashtag me-1"></i> Serie</th>
                                            <th class="text-center"><i class="fas fa-laptop-code me-1"></i> Modelo</th>
                                            <th class="text-center"><i class="fas fa-user me-1"></i> Responsable</th>
                                            <th class="text-center"><i class="fas fa-calendar-plus me-1"></i> Fecha Compra</th>
                                            <th class="text-center"><i class="fas fa-calendar-check me-1"></i> Fecha Instalación</th>
                                            <th class="text-center"><i class="fas fa-info-circle me-1"></i> Estado</th>
                                            <th class="text-center"><i class="fas fa-cogs me-1"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="datosTabla" class="text-center">
                                        <tr>
                                            <td colspan="12" class="text-center py-4">
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

    <!-- Modal para editar-->
    <div class="modal fade" id="modalEquipo" tabindex="-1" aria-labelledby="modalEquipoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header card-header-custom">
                    <h5 class="modal-title text-white" id="modalEquipoLabel">
                        <i class="fas fa-edit me-2"></i>
                        Editar Equipo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEquipoModal">
                        <input type="hidden" id="id_modal" name="id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="codigo_patrimonial_modal" class="form-label form-label-custom">
                                    <i class="fas fa-barcode me-1"></i> Código Patrimonial:
                                </label>
                                <input type="text" name="codigo_patrimonial" id="codigo_patrimonial_modal" class="form-control border-custom" required placeholder="Código Patrimonial" maxlength="12">
                                <div id="codigo_patrimonial_modal-feedback" class="mt-1"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nombre_equipo_modal" class="form-label form-label-custom">
                                    <i class="fas fa-tag me-1"></i> Nombre del Equipo:
                                </label>
                                <input type="text" name="nombre_equipo" id="nombre_equipo_modal" class="form-control border-custom" required placeholder="Nombre descriptivo">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipo_equipo_modal" class="form-label form-label-custom">
                                    <i class="fas fa-layer-group me-1"></i> Tipo de Equipo:
                                </label>
                                <select name="tipo_equipo" id="tipo_equipo_modal" class="form-control border-custom" required>
                                    <option value="">Seleccionar tipo</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="marca_modal" class="form-label form-label-custom">
                                    <i class="fas fa-bookmark me-1"></i> Marca:
                                </label>
                                <select name="marca" id="marca_modal" class="form-control border-custom" required>
                                    <option value="">Seleccionar marca</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="serie_modal" class="form-label form-label-custom">
                                    <i class="fas fa-hashtag me-1"></i> Serie:
                                </label>
                                <input type="text" name="serie" id="serie_modal" class="form-control border-custom" required placeholder="Número de serie">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modelo_modal" class="form-label form-label-custom">
                                    <i class="fas fa-laptop-code me-1"></i> Modelo:
                                </label>
                                <input type="text" name="modelo" id="modelo_modal" class="form-control border-custom" required placeholder="Modelo del equipo">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="responsable_modal" class="form-label form-label-custom">
                                    <i class="fas fa-user-tie me-1"></i> Responsable:
                                </label>
                                <select name="responsable" id="responsable_modal" class="form-control border-custom" required>
                                    <option value="">Seleccionar responsable</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_compra_modal" class="form-label form-label-custom">
                                    <i class="fas fa-calendar-plus me-1"></i> Fecha de Compra:
                                </label>
                                <input type="date" name="fecha_compra" id="fecha_compra_modal" class="form-control border-custom" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_instalacion_modal" class="form-label form-label-custom">
                                    <i class="fas fa-calendar-check me-1"></i> Fecha de Instalación:
                                </label>
                                <input type="date" name="fecha_instalacion" id="fecha_instalacion_modal" class="form-control border-custom" required>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col text-center">
                                <button type="button" class="btn px-4 me-3 btn-primary-custom" id="btnActualizar">
                                    <i class="fas fa-save me-2"></i> Actualizar
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
    <script src="vista/js/equipos_admin.js"></script>
</body>
</html>
