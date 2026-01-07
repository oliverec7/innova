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
        $sql = "SELECT COUNT(*) AS total
                FROM solicitudes s
                INNER JOIN usuarios u ON s.empleado_solicitante = u.idUsuario
                WHERE u.usuario = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(["total" => (int)$data['total']]);
        
    } catch (PDOException $e) {
        error_log("Error en Total Solicitudes: " . $e->getMessage());
        echo json_encode(["total" => 0, "error" => $e->getMessage()]);
    }
    break;

    case 'solicitudes_pendientes':
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM solicitudes s
                    INNER JOIN usuarios u ON s.empleado_solicitante = u.idUsuario
                    WHERE u.usuario = :username
                    AND estado = 'Pendiente'";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(["total" => (int)$data['total']]);
        
        } catch (PDOException $e) {
            error_log("Error en Solicitudes Pendientes: " . $e->getMessage());
            echo json_encode(["total" => 0, "error" => $e->getMessage()]);
        }
        break;

    case 'solicitudes_aprobadas':
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM solicitudes s
                    INNER JOIN usuarios u ON s.empleado_solicitante = u.idUsuario
                    WHERE u.usuario = :username
                    AND estado = 'Aprobada'";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(["total" => (int)$data['total']]);
        } catch (PDOException $e) {
            error_log("Error en Total Aprobadas: " . $e->getMessage());
            echo json_encode(["total" => 0, "error" => $e->getMessage()]);
        }
        break;

    case 'solicitudes_rechazadas':
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM solicitudes s
                    INNER JOIN usuarios u ON s.empleado_solicitante = u.idUsuario
                    WHERE u.usuario = :username
                    AND estado = 'Rechazada'";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(["total" => (int)$data['total']]);
        } catch (PDOException $e) {
            error_log("Error en Total Rechazadas: " . $e->getMessage());
            echo json_encode(["total" => 0, "error" => $e->getMessage()]);
        }
        break;

    case 'listar_equipos':
        try {
            $sqlUsuario = "SELECT idUsuario FROM usuarios WHERE usuario = :username";
            $stmtUsuario = $pdo->prepare($sqlUsuario);
            $stmtUsuario->bindParam(':username', $username, PDO::PARAM_STR);
            $stmtUsuario->execute();
            $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "Usuario no encontrado"
                ]);
                exit;
            }
            $idUsuario = $usuario['idUsuario'];
            $stmt = $pdo->prepare("SELECT
                                                e.idEquipo AS idEquipo,
                                                e.nombre_equipo AS Equipo
                                            FROM equipos e
                                            INNER JOIN personas p ON e.responsable = p.idPersona
                                            INNER JOIN usuarios u ON u.persona = p.idPersona
                                            WHERE u.usuario = :username
                                            ORDER BY e.nombre_equipo ASC
                                        ");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        } catch (PDOException $e) {

            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar equipos: " . $e->getMessage()
            ]);
        }
        break;

    case 'listar_mis_solicitudes':
        try {
            $stmt = $pdo->prepare(
                "SELECT
                            s.idSolicitud AS id,
                            e.nombre_equipo AS Equipo,
                            s.fecha_generada AS Fecha_Solicitud,
                            s.estado AS Estado,
                            s.razon AS Razon
                        FROM solicitudes s
                        INNER JOIN equipos e ON s.equipo_solicitado = e.idEquipo
                        INNER JOIN usuarios u ON s.empleado_solicitante = u.idUsuario
                        WHERE u.usuario = :username
                        ORDER BY s.fecha_generada DESC");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar solicitudes: " . $e->getMessage()
            ]);
        }
        break;

    case 'solicitar_inspeccion':
        try {
            $idEquipo = isset($_POST['equipo']) ? trim($_POST['equipo']) : '';
            $razon = isset($_POST['razon']) ? trim($_POST['razon']) : '';

            if ($idEquipo === '' || $razon === '') {
                echo json_encode([
                    "success" => false,
                    "message" => "Datos incompletos. Se requiere seleccionar un equipo y describir la razón."
                ]);
                exit;
            }

            if (!is_numeric($idEquipo)) {
                echo json_encode([
                    "success" => false,
                    "message" => "El ID del equipo no es válido"
                ]);
                exit;
            }

            if (strlen($razon) < 10) {
                echo json_encode([
                    "success" => false,
                    "message" => "La razón debe tener al menos 10 caracteres"
                ]);
                exit;
            }

            $sqlEmpleado = "
                SELECT u.idUsuario
                FROM usuarios u
                WHERE u.usuario = :username
            ";

            $stmtEmpleado = $pdo->prepare($sqlEmpleado);
            $stmtEmpleado->bindParam(':username', $username, PDO::PARAM_STR);
            $stmtEmpleado->execute();
            $empleado = $stmtEmpleado->fetch(PDO::FETCH_ASSOC);

            if (!$empleado) {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo identificar al empleado solicitante"
                ]);
                exit;
            }

            $idEmpleado = $empleado['idUsuario'];
            $sqlVerificar = "SELECT COUNT(*) as tiene_pendiente
                            FROM solicitudes
                            WHERE equipo_solicitado = :equipo
                            AND estado = 'Pendiente'
                        ";

            $stmtVerificar = $pdo->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':equipo', $idEquipo, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

            if ($resultado['tiene_pendiente'] > 0) {
                echo json_encode([
                    "success" => false,
                    "message" => "Este equipo ya tiene una solicitud pendiente. No se pueden crear solicitudes duplicadas."
                ]);
                exit;
            }

            $sql = "CALL P_INSERT_SOLICITUD(:equipo, :empleado, :razon)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':equipo', $idEquipo, PDO::PARAM_INT);
            $stmt->bindParam(':empleado', $idEmpleado, PDO::PARAM_INT);
            $stmt->bindParam(':razon', $razon, PDO::PARAM_STR);
            $stmt->execute();

            echo json_encode([
                "success" => true,
                "message" => "Su solicitud de inspección fue enviada correctamente y está pendiente de revisión."
            ]);

        } catch (PDOException $e) {

            if ($e->getCode() == '45000') {
                echo json_encode([
                    "success" => false,
                    "message" => $e->getMessage()
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al registrar solicitud: " . $e->getMessage()
                ]);
            }
        }
        break;

    case 'actualizar_solicitud':
        try {
            $id = $_POST['id'] ?? '';
            $equipo = $_POST['equipo'] ?? '';
            $razon = $_POST['razon'] ?? '';

            if (empty($id) || empty($equipo) || empty($razon)) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "Datos incompletos"
                ]);
                exit;
            }

            if (strlen($razon) < 10) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "La razón debe tener al menos 10 caracteres"
                ]);
                exit;
            }

            $sql = "CALL P_UPDATE_SOLICITUD(?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id, $equipo, $razon]);

            echo json_encode([
                "status" => "ok",
                "msg" => "Solicitud actualizada correctamente"
            ]);

        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                echo json_encode([
                    "status" => "error",
                    "msg" => $e->getMessage()
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "msg" => "Error al actualizar la solicitud: " . $e->getMessage()
                ]);
            }
        }
        break;

    case 'eliminar_solicitud':
        try {
            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "ID de solicitud no válido"
                ]);
                exit;

            }

            $sql = "CALL P_DELETE_SOLICITUD(?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            echo json_encode([
                "status" => "ok",
                "msg" => "Solicitud eliminada correctamente"
            ]);

           

        } catch (PDOException $e) {

            if ($e->getCode() == '45000') {
                echo json_encode([
                    "status" => "error",
                    "msg" => $e->getMessage()
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "msg" => "Error al eliminar la solicitud: " . $e->getMessage()
                ]);
            }
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