<?php

header('Content-Type: application/json');
require_once('Conection.php'); 

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    echo json_encode([
        "status" => "error_session",
        "msg" => "Sesión no válida o expirada. Por favor, inicie sesión de nuevo."
    ]);
    exit;
}

$username = $_SESSION['usuario'];

$conexion = new Conection();
$pdo = $conexion->getConection();
$accion = $_POST['accion'] ?? '';

$stmtUser = $pdo->prepare("SELECT idUsuario FROM usuarios WHERE usuario = :u LIMIT 1");
$stmtUser->execute([':u' => $username]);
$filaUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

$idUsuarioLogueado = $filaUser['idUsuario'] ?? 0;

$response = [];

switch ($accion) {
    case 'listar':
        try {
            $sql = "SELECT 
                        idNotificacion, 
                        mensaje, 
                        fecha, 
                        tipo_notificacion, 
                        estado_notificacion 
                    FROM notificaciones 
                    WHERE usuario_destino = :idUsuario 
                    ORDER BY fecha DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuarioLogueado, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($data as &$row) {
                $row['fecha'] = date('d/m/Y H:i', strtotime($row['fecha']));
            }

            $response = ["status" => "success", "data" => $data];

        } catch (Exception $e) {
            $response = ['status' => 'error', 'message' => 'Error al listar: ' . $e->getMessage()];
        }
        break;

    case 'cambiar_estado':
        try {
            $idNotificacion = $_POST['idNotificacion'];
            $nuevoEstado = $_POST['nuevo_estado']; 
            
            // Validación de seguridad
            if($nuevoEstado !== 'Leída' && $nuevoEstado !== 'No leída') {
                throw new Exception("Estado no válido");
            }

            $sql = "UPDATE notificaciones SET estado_notificacion = :estado WHERE idNotificacion = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':estado', $nuevoEstado, PDO::PARAM_STR);
            $stmt->bindParam(':id', $idNotificacion, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Estado actualizado.'];
            } else {
                $response = ['status' => 'error', 'message' => 'No se pudo actualizar.'];
            }
        } catch (Exception $e) {
            $response = ['status' => 'error', 'message' => 'Error de BD: ' . $e->getMessage()];
        }
        break;

    default:
        $response['status'] = 'error';
        $response['message'] = 'Acción no válida.';
        break;
}

echo json_encode($response);
?>