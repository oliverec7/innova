<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$rol = $_SESSION['rol'] ?? null;
?>

<nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
    <div class="container-fluid">
        <ul class="navbar-nav flex-nowrap ms-auto">
            <!-- Botón de Notificaciones -->
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" 
                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw"></i>
                    <!-- Contador de notificaciones -->
                    <span class="badge bg-danger badge-counter">0</span>
                </a>
                <!-- Dropdown de Notificaciones -->
                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" 
                     aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">
                        Centro de Notificaciones
                    </h6>
                    <?php if ($rol == 'Administrador'): ?>
                        <a class="dropdown-item text-center small text-gray-500" href="index.php?v=NotificacionesAdmin">Ver todas las notificaciones</a>
                    <?php elseif ($rol == 'Empleado'): ?>
                        <a class="dropdown-item text-center small text-gray-500" href="index.php?v=NotificacionesEmpleado">Ver todas las notificaciones</a>
                    <?php elseif ($rol == 'Tecnico'): ?>
                        <a class="dropdown-item text-center small text-gray-500" href="index.php?v=NotificacionesTecnico">Ver todas las notificaciones</a>
                    <?php endif; ?>
                </div>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Perfil de Usuario -->
            <li class="nav-item dropdown no-arrow">
                <div class="nav-item dropdown no-arrow">
                    <a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#">
                        <span class="d-none d-lg-inline me-2 text-gray-600 small">
                            <i class="fa fa-address-card" aria-hidden="true"></i>
                            <?php echo $_SESSION['usuario']; ?> |
                            <?php echo $_SESSION['rol']; ?>
                        </span>
                        <img class="border rounded-circle img-profile" src="assets/img/avatars/avatar1.jpeg">
                    </a>
                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                        <a class="dropdown-item" href="index.php?v=Perfil">
                            <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                            &nbsp;Perfil
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="index.php?v=CerrarSesion">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                            &nbsp;Cerrar Sesión
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>

<script src="vista/js/navbar_notificaciones.js"></script>