<?php
require_once('modelo/Conection.php');
require_once("controlador/verificarSesion.php");

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$DB = new Conection();
$conn = $DB->getConection();
$usuario = $_SESSION['usuario'];

try {
    $sql = "SELECT * FROM v_perfil WHERE Usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$usuario]);
    $datosUsuario = $stmt->fetch();
} catch (PDOException $e) {
    $datosUsuario = null;
}

$idUsuario = $_SESSION['idUsuario'] ?? ($datosUsuario['id'] ?? null);
$datosUser = null;
if ($idUsuario) {
    try {
        $sql = "SELECT idUsuario, ultimo_acceso, fecha_creacion FROM usuarios WHERE idUsuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idUsuario]);
        $datosUser = $stmt->fetch();
    } catch (PDOException $e) {
        $datosUser = null;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Perfil</title>
    <link rel="shortcut icon" href="assets/img/img_logos/general.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/material-icons.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" type="text/css" href="libs/css/datatables.min.css"/>
    <link rel="stylesheet" href="vista/css/modulos.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php require('vista/includes/cabecera.php'); ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <?php require('vista/includes/cabeceraPerfil.php'); ?>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">PERFIL DE USUARIO</h3>
                    
                    <div id="seccionPerfil" class="container-fluid p-4 bg-perfil-light">
                        <div class="row justify-content-center">
                            <div class="col-xl-8 col-lg-10">
                                <div class="card border-0 shadow-lg overflow-hidden card-perfil-principal">
                                    <div class="row g-0">
                                        
                                        <div class="col-md-4 d-flex align-items-stretch bg-perfil-image-col">
                                            <div class="card-body d-flex flex-column justify-content-center align-items-center p-4">
                                                <div class="position-relative mb-3">
                                                    <img src="vista/img/perfil.png" alt="Perfil Usuario" class="img-fluid rounded-circle shadow img-perfil-main">
                                                    <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-2 shadow-sm">
                                                        <button class="btn btn-sm btn-perfil-primary">
                                                            <i class="fas fa-camera"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <h4 class="mt-3 mb-0 text-center text-perfil-primary"><?= htmlspecialchars($datosUsuario['Usuario'] ?? 'Usuario') ?></h4>
                                                <p class="text-muted text-center"><?= htmlspecialchars($datosUsuario['Rol'] ?? 'Rol no definido') ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-8">
                                            <div class="card-header py-3 bg-perfil-header">
                                                <h5 class="card-title mb-0 text-white">
                                                    <i class="fas fa-user-circle me-2"></i>
                                                    <span class="fw-bold">Datos Personales</span>
                                                </h5>
                                            </div>
                                            <div class="card-body p-4">
                                                <table class="table table-hover align-middle table-perfil-data">
                                                    <tbody>
                                                        <tr>
                                                            <th class="text-end text-perfil-primary w-data-label">DNI:</th>
                                                            <td class="border-start-custom"><?= htmlspecialchars($datosUsuario['DNI'] ?? 'No disponible') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-end text-perfil-primary">Nombres:</th>
                                                            <td class="border-start-custom"><?= htmlspecialchars($datosUsuario['Nombres'] ?? 'No disponible') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-end text-perfil-primary">Apellidos:</th>
                                                            <td class="border-start-custom"><?= htmlspecialchars($datosUsuario['Apellidos'] ?? 'No disponible') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-end text-perfil-primary">Telefono:</th>
                                                            <td class="border-start-custom"><?= htmlspecialchars($datosUsuario['Telefono'] ?? 'No disponible') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-end text-perfil-primary">Área de trabajo:</th>
                                                            <td class="border-start-custom"><?= htmlspecialchars($datosUsuario['Area'] ?? 'No disponible') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-end text-perfil-primary">Miembro desde:</th>
                                                            <td class="border-start-custom"><?= htmlspecialchars(substr($datosUser['fecha_creacion'] ?? 'N/A', 0, 10)) ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="mt-4 text-center">
                                                    <button id="btnEditarPerfil" class="btn btn-lg btn-perfil-primary btn-lg-custom">
                                                        <i class="fas fa-user-edit me-2"></i> Editar Credenciales
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="seccionEditarPerfil" class="container-fluid p-4 bg-perfil-light" style="display: none;">
                        <div class="row justify-content-center">
                            <div class="col-xl-8 col-lg-10">
                                <div class="card shadow">
                                    <div class="card-header py-3 bg-perfil-header">
                                        <h5 class="card-title mb-0 text-white">
                                            <i class="fas fa-lock me-2"></i>
                                            <span class="fw-bold">Actualizar Credenciales</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="formEditarPerfil" method="post">
                                            <input type="hidden" id="idUsuario" name="idUsuario" value="<?php echo $idUsuario; ?>">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="usuario" class="form-label">Nuevo Nombre de Usuario</label>
                                                    <input type="text" class="form-control border-custom" name="usuario" id="usuario" value="<?= htmlspecialchars($datosUsuario['Usuario'] ?? '') ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="actual" class="form-label">Contraseña Actual</label>
                                                    <input type="password" class="form-control border-custom" name="actual" id="actual" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nuevo" class="form-label">Contraseña Nueva</label>
                                                    <input type="password" class="form-control border-custom" name="nuevo" id="nuevo" placeholder="Dejar vacío para no cambiar" autocomplete="new-password">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="confirmar" class="form-label">Confirmar Contraseña Nueva</label>
                                                    <input type="password" class="form-control border-custom" name="confirmar" id="confirmar" placeholder="Repetir nueva contraseña" autocomplete="new-password">
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col text-center">
                                                    <button type="submit" class="btn px-4 me-3 btn-primary-custom">
                                                        <i class="fas fa-save me-2"></i> Guardar
                                                    </button>
                                                    <button type="button" id="btnCancelarEdicion" class="btn px-4 btn-danger-custom">
                                                        <i class="fas fa-times me-2"></i> Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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
    <script src="libs/js/datatables.min.js"></script>
    <script src="libs/js/sweetalert.min.js"></script>
    <script src="vista/js/perfil.js"></script>
</body>
</html>