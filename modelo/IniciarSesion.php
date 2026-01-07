<?php
require_once("modelo/Conection.php");

// Verificar que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["user"] ?? '');
    $password = trim($_POST["password"] ?? '');

    // Validar que los campos no estén vacíos
    if ($usuario === '' || $password === '') {
        echo json_encode(["status" => "error", "msg" => "Datos incompletos"]);
        exit;
    }

    // Crear instancia de conexión
    $conexion = new Conection();
    $cn = $conexion->getConection();

    if (!$cn) {
        echo json_encode(["status" => "error", "msg" => "Error de conexión a la base de datos"]);
        exit;
    }

    try {
        // Ejecutar el procedimiento almacenado con parámetros preparados
        $stmt = $cn->prepare("CALL P_VALIDAR_USUARIO(?, ?)");
        $stmt->execute([$usuario, $password]);
        
        $fila = $stmt->fetch();

        // Verificar si se encontró un usuario válido
        if ($fila) {
            session_start();
            $_SESSION['usuario'] = $fila['usuario'];
            $_SESSION['rol'] = $fila['rol'];
            $_SESSION['ultimo_acceso'] = time();

            // Responder con éxito
            echo json_encode([
                "status" => "ok",
                "rol" => $fila['rol'],
                "usuario" => $fila['usuario']
            ]);
        } else {
            echo json_encode(["status" => "error", "msg" => "Credenciales inválidas"]);
        }
    
    // Manejar errores de la base de datos
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error", 
            "msg" => "Error al validar usuario: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Método no permitido"]);
}
?>