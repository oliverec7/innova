<?php
require_once("config.php");
require_once("controlador/index.php");

if(isset($_GET['v'])):    
    if(method_exists("modeloController", $_GET['v'])):
        modeloController::{$_GET['v']}();
    endif;
else:
    modeloController::Login();
endif;
?>