<?php
// Asegurar que la cookie se busque en la raíz
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['path' => '/']); 
    session_start();
}

// Si no existe el ROL, mandarlo al login
if (!isset($_SESSION['rol'])) {
    header('Location: index.php?v=Login');
    exit();
}

$tiempoInactividad = 900;

// Control de tiempo
if (isset($_SESSION['ultimo_acceso'])) {
    $vida = time() - $_SESSION['ultimo_acceso'];
    if ($vida > $tiempoInactividad) {
        session_unset();
        session_destroy();
        header('Location: index.php?v=Login&exp=1');
        exit();
    }
}

// Actualizar tiempo
$_SESSION['ultimo_acceso'] = time();