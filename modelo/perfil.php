<?php
header('Content-Type: application/json');
require_once('Conection.php');
session_start();

$response = ['Valor' => '0', 'Mensaje' => 'Acción no válida'];

try {
    $conexion = new Conection();
    $mysqli = $conexion->getConection();

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'actualizarUsuario') {
        $idUsuario = intval($_POST['idUsuario'] ?? 0);
        $usuarioNuevo = trim($_POST['usuarioNuevo'] ?? '');
        $claveActual = $_POST['claveActual'] ?? '';
        $claveNueva = $_POST['claveNueva'] ?? '';

        if ($idUsuario === 0 || $usuarioNuevo === '' || $claveActual === '' || $claveNueva === '') {
            throw new Exception('Todos los campos son obligatorios');
        }

        if (strlen($usuarioNuevo) < 3) {
            throw new Exception('El nombre de usuario debe tener al menos 3 caracteres');
        }

        $stmt = $mysqli->prepare("CALL P_UPDATE_USUARIO_ACTUAL(?, ?, ?, ?)");
        $stmt->execute([$idUsuario, $usuarioNuevo, $claveActual, $claveNueva]);

        $_SESSION['usuario'] = $usuarioNuevo;
        $_SESSION['idUsuario'] = $idUsuario;
        
        $response = [
            'Valor' => '1', 
            'Mensaje' => 'Datos actualizados correctamente',
            'usuarioActualizado' => $usuarioNuevo
        ];
    }
} catch (PDOException $e) {
    $response = ['Valor' => '0', 'Mensaje' => $e->getMessage()];
} catch (Exception $e) {
    $response = ['Valor' => '0', 'Mensaje' => $e->getMessage()];
}

echo json_encode($response);