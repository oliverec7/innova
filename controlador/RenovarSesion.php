<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['path' => '/']); 
    session_start();
}

// Solo renovamos si ya hay una sesión activa
if (isset($_SESSION['rol'])) {
    $_SESSION['ultimo_acceso'] = time();
    echo "OK";
} else {
    echo "ERROR";
}
?>