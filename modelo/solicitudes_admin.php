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

switch ($accion) {

    case 'total_solicitudes':
        try {
            $sql = "SELECT COUNT(*) AS total FROM solicitudes";
            $stmt = $pdo->query($sql);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(["total" => $data['total']]);
        } catch (PDOException $e) {
            echo json_encode(["total" => 0]);
        }
        break;

    case 'solicitudes_pendientes':
        try {
            $sql = "SELECT COUNT(*) AS total FROM solicitudes WHERE estado = 'Pendiente'";
            $stmt = $pdo->query($sql);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(["total" => $data['total']]);
        } catch (PDOException $e) {
            echo json_encode(["total" => 0]);
        }
        break;

    case 'solicitudes_aprobadas':
        try {
            $sql = "SELECT COUNT(*) AS total FROM solicitudes WHERE estado = 'Aprobada'";
            $stmt = $pdo->query($sql);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(["total" => $data['total']]);
        } catch (PDOException $e) {
            echo json_encode(["total" => 0]);
        }
        break;

    case 'solicitudes_rechazadas':
        try {
            $sql = "SELECT COUNT(*) AS total FROM solicitudes WHERE estado = 'Rechazada'";
            $stmt = $pdo->query($sql);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(["total" => $data['total']]);
        } catch (PDOException $e) {
            echo json_encode(["total" => 0]);
        }
        break;
    
    case 'listar_solicitudes':
        try {
            $stmt = $pdo->query("SELECT
                                            s.idSolicitud AS id,
                                            e.nombre_equipo AS Equipo,
                                            CONCAT_WS(' ', p.apellido_paterno, p.apellido_materno, p.nombres) AS Solicitante,
                                            DATE_FORMAT(s.fecha_generada, '%d/%m/%Y %H:%i:%s') AS Fecha_Solicitud,
                                            s.estado AS Estado,
                                            s.razon AS Razon
                                        FROM solicitudes s
                                        JOIN equipos e ON s.equipo_solicitado = e.idEquipo
                                        JOIN personas p ON e.responsable = p.idPersona
                                        ORDER BY (s.estado = 'Pendiente') DESC, s.fecha_generada DESC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar solicitudes recibidas: " . $e->getMessage()
            ]);
        }
        break;
    
    case 'procesar_solicitud':
        try {
            $idSolicitud = $_POST['idSolicitud'] ?? null;
            $estado = $_POST['estado'] ?? '';
            
            if (!$idSolicitud || !$estado) {
                throw new Exception("Parámetros incompletos");
            }
            
            // Esta llamada activará el trigger si es 'Aprobada'
            $stmt = $pdo->prepare("CALL P_PROCESAR_SOLICITUD(?, ?)");
            $stmt->execute([$idSolicitud, $estado]);
            
            echo json_encode([
                "status" => "success",
                "msg" => "Solicitud procesada correctamente"
            ]);
            
        } catch (PDOException $e) {
            // Captura errores del procedimiento Y del trigger
            echo json_encode([
                "status" => "error", 
                "msg" => "Error al procesar: " . $e->getMessage()
            ]);
        }
        break;

    case 'obtener_por_id':
        if (isset($_POST['id'])) {
            try {
                $id = $_POST['id'];
                $sql = "SELECT * FROM solicitudes WHERE idSolicitud = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id]);
                
                $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($solicitud) {
                    echo json_encode(['status' => 'ok', 'data' => $solicitud]);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Solicitud no encontrada']);
                }
            } catch (PDOException $e) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "Error al obtener la solicitud: " . $e->getMessage()
                ]);
            }
        }
        break;

    default:
        echo json_encode([
            "status" => "error",
            "msg" => "Acción no válida o no especificada"
        ]);
        break;
}
?>