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

    case 'listar_asignadas':
        try {
            $sql = "SELECT
                    i.idInspeccion AS id,
                    e.nombre_equipo AS Equipo,
                    CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                    DATE_FORMAT(i.fecha_programada, '%d/%m/%Y') AS Fecha_Inspeccion,
                    s.razon AS Razon,
                    i.estado AS Estado
                FROM inspecciones i
                INNER JOIN equipos e ON i.equipo_inspeccionar = e.idEquipo
                INNER JOIN ordenes_trabajo ot ON i.orden_trabajar = ot.idOrden
                INNER JOIN solicitudes s ON ot.solicitud_trabajo = s.idSolicitud
                INNER JOIN usuarios u ON i.inspector = u.idUsuario
                INNER JOIN personas pi ON u.persona = pi.idPersona
                INNER JOIN personas pr ON i.responsable_equipo = pr.idPersona
                WHERE u.usuario = :username
                AND i.estado IN ('Pendiente', 'En Proceso')
                ORDER BY i.fecha_programada ASC;";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($data);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar inspecciones: " . $e->getMessage()
            ]);
        }
        break;

    case 'listar_finalizadas':
        try {
            $sql = "SELECT
                        i.idInspeccion AS id,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                        DATE_FORMAT(i.fecha_inicio_inspeccion, '%d/%m/%Y') AS Fecha_Inspeccion,
                        DATE_FORMAT(i.fecha_inicio_inspeccion, '%H:%i:%s') AS Hora_Inicio,
                        DATE_FORMAT(i.fecha_fin_inspeccion, '%H:%i:%s') AS Hora_Fin,
                        i.resultado AS Resultado,
                        i.comentario AS Comentario
                    FROM inspecciones i
                    INNER JOIN equipos e ON i.equipo_inspeccionar = e.idEquipo
                    INNER JOIN tipos_equipos te ON e.tipo_equipo = te.idTipoEquipo
                    INNER JOIN usuarios u ON i.inspector = u.idUsuario
                    INNER JOIN personas pi ON u.persona = pi.idPersona
                    INNER JOIN personas pr ON i.responsable_equipo = pr.idPersona
                    WHERE u.usuario = :username AND i.estado = 'Finalizado'
                    ORDER BY i.fecha_inicio_inspeccion DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($data);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar inspecciones: " . $e->getMessage()
            ]);
        }
        break;

    case 'obtener_inspeccion':
        try {
            $idInspeccion = $_POST['idInspeccion'] ?? null;

            if (!$idInspeccion) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "El ID de la inspección es requerido"
                ]);
                break;
            }

            $sql = "SELECT
                        i.idInspeccion AS id,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                        DATE_FORMAT(i.fecha_inicio_inspeccion, '%d/%m/%Y') AS Fecha_Inspeccion,
                        DATE_FORMAT(i.fecha_inicio_inspeccion, '%H:%i:%s') AS Hora_Inicio,
                        DATE_FORMAT(i.fecha_fin_inspeccion, '%H:%i:%s') AS Hora_Fin,
                        i.resultado AS Resultado,
                        i.comentario AS Comentario
                    FROM inspecciones i
                    INNER JOIN equipos e ON i.equipo_inspeccionar = e.idEquipo
                    INNER JOIN personas pr ON i.responsable_equipo = pr.idPersona
                    INNER JOIN usuarios u ON i.inspector = u.idUsuario
                    WHERE i.idInspeccion = :id AND u.usuario = :username";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $idInspeccion, PDO::PARAM_INT);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                echo json_encode($data);
            } else {
                echo json_encode([
                    "status" => "error",
                    "msg" => "No se encontró la inspección o no tiene permisos para verla"
                ]);
            }

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al obtener inspección: " . $e->getMessage()
            ]);
        }
        break;

    case 'iniciar_inspeccion':
        try {
            $idInspeccion = $_POST['idInspeccion'] ?? null;

            if (!$idInspeccion) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "El ID de la inspección es requerido"
                ]);
                break;
            }

            // Llamada al procedimiento almacenado
            $stmt = $pdo->prepare("CALL P_INICIAR_INSPECCION(:id)");
            $stmt->bindParam(':id', $idInspeccion, PDO::PARAM_INT);
            $stmt->execute();

            $resp = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "msg" => $resp['mensaje'] ?? "Inspección iniciada correctamente"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al iniciar inspección: " . $e->getMessage()
            ]);
        }
        break;

    case 'finalizar_inspeccion':
        try {
            $idInspeccion = $_POST['idInspeccion'] ?? null;
            $resultado = $_POST['resultado'] ?? null;
            $comentario = $_POST['comentario'] ?? null;

            if (!$idInspeccion || !$resultado || $comentario === null) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "Todos los parámetros son requeridos (idInspeccion, resultado, comentario)"
                ]);
                break;
            }

            $stmt = $pdo->prepare("CALL P_FINALIZAR_INSPECCION(:id, :resultado, :comentario)");
            $stmt->bindParam(':id', $idInspeccion, PDO::PARAM_INT);
            $stmt->bindParam(':resultado', $resultado, PDO::PARAM_STR);
            $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);

            $stmt->execute();
            $resp = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "msg" => $resp['mensaje'] ?? "Inspección finalizada correctamente"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al finalizar inspección: " . $e->getMessage()
            ]);
        }
        break;

    case 'actualizar_inspeccion':
        try {
            $idInspeccion = $_POST['idInspeccion'] ?? null;
            $resultado = $_POST['resultado'] ?? null;
            $comentario = $_POST['comentario'] ?? null;

            if (!$idInspeccion || !$resultado || $comentario === null) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "Todos los parámetros son requeridos (idInspeccion, resultado, comentario)"
                ]);
                break;
            }

            // Llamada al procedimiento almacenado
            $stmt = $pdo->prepare("CALL P_UPDATE_INSPECCION_FINALIZADA(:id, :resultado, :comentario)");
            $stmt->bindParam(':id', $idInspeccion, PDO::PARAM_INT);
            $stmt->bindParam(':resultado', $resultado, PDO::PARAM_STR);
            $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);

            $stmt->execute();
            $resp = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "msg" => $resp['mensaje'] ?? "Inspección actualizada correctamente"
            ]);

        } catch (PDOException $e) {
            // Capturar errores personalizados del procedimiento almacenado
            $errorMsg = $e->getMessage();
            
            echo json_encode([
                "status" => "error",
                "msg" => "Error al actualizar inspección: " . $errorMsg
            ]);
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