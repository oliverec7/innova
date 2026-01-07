<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$rol = $_SESSION['rol'] ?? null;
?>
<head>
    <link rel="stylesheet" href="vista/css/menu.css">
</head>
<nav id="sidebar" class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion p-0 menu-moderno">
    <div class="container-fluid d-flex flex-column p-0">
        <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="index.php?v=Inicio">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fa fa-shekel" aria-hidden="true"></i>
            </div>
            <div class="sidebar-brand-text mx-3">
                <span>INNOVA</span>
            </div>
        </a>
        <hr class="sidebar-divider my-0">
        <ul class="navbar-nav text-light" id="accordionSidebar">

            <?php if ($rol == 'Administrador'): ?>
                <li class="nav-item"><a class="nav-link" href="index.php?v=Inicio"><i class="fa fa-bar-chart" aria-hidden="true"></i><span>INICIO</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=EquiposAdmin"><i class="fa fa-desktop" aria-hidden="true"></i><span>EQUIPOS</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=SolicitudesAdmin"><i class="fa fa-tasks" aria-hidden="true"></i><span>SOLICITUDES</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=OrdenesAdmin"><i class="fa fa-file-alt" aria-hidden="true"></i><span>ORDENES</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=InspeccionesAdmin"><i class="fa fa-search" aria-hidden="true"></i><span>INSPECCIONES</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=MantenimientosAdmin"><i class="fa fa-wrench" aria-hidden="true"></i><span>MANTENIMIENTOS</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=ReportesAdmin"><i class="fa fa-line-chart" aria-hidden="true"></i><span>REPORTES</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=Perfil"><i class="fa fa-user-o" aria-hidden="true"></i><span>PERFIL</span></a></li>

            <?php elseif ($rol == 'Tecnico'): ?>
                <li class="nav-item"><a class="nav-link" href="index.php?v=Inicio"><i class="fa fa-bar-chart" aria-hidden="true"></i><span>INICIO</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=InspeccionesTecnico"><i class="fa fa-search" aria-hidden="true"></i><span>INSPECCIONES</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=MantenimientosTecnico"><i class="fa fa-wrench" aria-hidden="true"></i><span>MANTENIMIENTOS</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=Perfil"><i class="fa fa-user-o" aria-hidden="true"></i><span>PERFIL</span></a></li>

            <?php elseif ($rol == 'Empleado'): ?>
                <li class="nav-item"><a class="nav-link" href="index.php?v=Inicio"><i class="fa fa-bar-chart" aria-hidden="true"></i><span>INICIO</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=EquiposEmpleado"><i class="fa fa-desktop" aria-hidden="true"></i><span>EQUIPOS</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=SolicitudesEmpleado"><i class="fa fa-tasks" aria-hidden="true"></i><span>SOLICITUDES</span></a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?v=Perfil"><i class="fa fa-user-o" aria-hidden="true"></i><span>PERFIL</span></a></li>
            <?php endif; ?>
        </ul>
        <div class="text-center d-none d-md-inline mt-auto p-3">
            <button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="vista/js/cabecera.js"></script>
</nav>

<!-- Script para gestión de sesión por inactividad -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Configuraciones
    const TIEMPO_INACTIVIDAD = 900;
    const TIEMPO_AVISO = 120;
    const FRECUENCIA_RENOVACION = 60000;

    let temporizadorAlerta;
    let ultimaRenovacionServidor = Date.now();

    function iniciarMonitoreo() {
        clearTimeout(temporizadorAlerta);

        // Calculamos el tiempo para mostrar el aviso
        let tiempoParaAviso = (TIEMPO_INACTIVIDAD - TIEMPO_AVISO) * 1000;

        temporizadorAlerta = setTimeout(() => {
            mostrarAlerta();
        }, tiempoParaAviso);
    }

    function mostrarAlerta() {
        Swal.fire({
            title: "Sesión por expirar",
            text: "Tu sesión se cerrará en 2 minutos por inactividad.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Mantener sesión activa",
            cancelButtonText: "Cerrar sesión ahora",
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                renovacionManual();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = "index.php?v=Login";
            }
        });
    }

    // Renovación silenciosa (Por clics o teclado)
    function renovacionSilenciosa() {
        const ahora = Date.now();
        if (ahora - ultimaRenovacionServidor > FRECUENCIA_RENOVACION) {
            ultimaRenovacionServidor = ahora;
            fetch("index.php?v=RenovarSesion")
                .then(() => {
                    console.log("Sesión extendida automáticamente.");
                    iniciarMonitoreo();
                });
        }
    }

    // Renovación manual (Desde el botón de la alerta)
    function renovacionManual() {
        ultimaRenovacionServidor = Date.now();
        fetch("index.php?v=RenovarSesion")
            .then(() => {
                Swal.fire({ title: "Renovada", icon: "success", timer: 1000, showConfirmButton: false });
                iniciarMonitoreo();
            });
    }

    // Si el usuario hace algo, intentamos renovar silenciosamente
    ['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(evento => {
        document.addEventListener(evento, () => {

            // Solo intentamos renovar si la alerta de Swal NO está visible
            if (!Swal.isVisible()) {
                renovacionSilenciosa();
            }
        }, { passive: true });
    });

    // Iniciar al cargar
    iniciarMonitoreo();
});
</script>
