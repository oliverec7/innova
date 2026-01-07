<?php
header('Content-Type: application/json');
require_once('Conection.php');

if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Verificación de sesión
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

// 2. Switch principal para manejar las acciones
switch ($accion) {

    // 2.1. Listar Mantenimientos Asignados (Pendientes / En Proceso - sin fecha fin)
    case 'listar_asignados':
        try {
            // Se asume que un mantenimiento está "asignado" si no tiene fecha de fin.
            $sql = "SELECT
                        m.idMantenimiento AS id,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                        DATE_FORMAT(m.fecha_programada, '%d/%m/%Y') AS Fecha_Programada,
                        -- Indicamos el estado basándonos en si ya tiene fecha de inicio
                        CASE 
                            WHEN m.fecha_inicio_mantenimiento IS NULL THEN 'Pendiente'
                            ELSE 'En Proceso'
                        END AS Estado
                    FROM mantenimientos m
                    INNER JOIN equipos e ON m.equipo_corregir = e.idEquipo
                    INNER JOIN usuarios u ON m.tecnico_corrector = u.idUsuario
                    INNER JOIN personas pr ON m.responsable_equipo = pr.idPersona
                    WHERE u.usuario = :username
                    AND m.fecha_fin_mantenimiento IS NULL
                    ORDER BY m.fecha_programada ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($data);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar mantenimientos asignados: " . $e->getMessage()
            ]);
        }
        break;

    // 2.2. Listar Mantenimientos Finalizados
    case 'listar_finalizados':
        try {
            $sql = "SELECT
                        m.idMantenimiento AS id,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                        DATE_FORMAT(m.fecha_programada, '%d/%m/%Y') AS Fecha_Programada,
                        DATE_FORMAT(m.fecha_inicio_mantenimiento, '%H:%i:%s') AS Hora_Inicio,
                        DATE_FORMAT(m.fecha_fin_mantenimiento, '%H:%i:%s') AS Hora_Fin,
                        m.resultado AS Resultado,
                        m.detalle AS Detalle
                    FROM mantenimientos m
                    INNER JOIN equipos e ON m.equipo_corregir = e.idEquipo
                    INNER JOIN usuarios u ON m.tecnico_corrector = u.idUsuario
                    INNER JOIN personas pr ON m.responsable_equipo = pr.idPersona
                    WHERE u.usuario = :username 
                    AND m.fecha_fin_mantenimiento IS NOT NULL
                    ORDER BY m.fecha_fin_mantenimiento DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($data);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar mantenimientos finalizados: " . $e->getMessage()
            ]);
        }
        break;

    // 2.3. Obtener detalles de un mantenimiento (para editar/ver)
    case 'obtener_mantenimiento':
        try {
            $idMantenimiento = $_POST['idMantenimiento'] ?? null;

            if (!$idMantenimiento) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "El ID del mantenimiento es requerido"
                ]);
                break;
            }

            $sql = "SELECT
                        m.idMantenimiento AS id,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                        m.fecha_programada AS Fecha_Programada,
                        m.fecha_inicio_mantenimiento AS Fecha_Inicio,
                        m.fecha_fin_mantenimiento AS Fecha_Fin,
                        m.resultado AS Resultado,
                        m.detalle AS Detalle
                    FROM mantenimientos m
                    INNER JOIN equipos e ON m.equipo_corregir = e.idEquipo
                    INNER JOIN personas pr ON m.responsable_equipo = pr.idPersona
                    INNER JOIN usuarios u ON m.tecnico_corrector = u.idUsuario
                    WHERE m.idMantenimiento = :id AND u.usuario = :username";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $idMantenimiento, PDO::PARAM_INT);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                echo json_encode($data);
            } else {
                echo json_encode([
                    "status" => "error",
                    "msg" => "No se encontró el mantenimiento o no tiene permisos para verlo"
                ]);
            }

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al obtener mantenimiento: " . $e->getMessage()
            ]);
        }
        break;

    // 2.4. Iniciar Mantenimiento
    case 'iniciar_mantenimiento':
        try {
            $idMantenimiento = $_POST['idMantenimiento'] ?? null;

            if (!$idMantenimiento) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "El ID del mantenimiento es requerido"
                ]);
                break;
            }

            // Llamada al procedimiento almacenado P_INICIAR_MANTENIMIENTO
            $stmt = $pdo->prepare("CALL P_INICIAR_MANTENIMIENTO(:id)");
            $stmt->bindParam(':id', $idMantenimiento, PDO::PARAM_INT);
            $stmt->execute();

            $resp = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "msg" => $resp['mensaje'] ?? "Mantenimiento iniciado correctamente"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al iniciar mantenimiento: " . $e->getMessage()
            ]);
        }
        break;

    // 2.5. Finalizar Mantenimiento
    case 'finalizar_mantenimiento':
        try {
            $idMantenimiento = $_POST['idMantenimiento'] ?? null;
            $resultado = $_POST['resultado'] ?? null;
            $detalle = $_POST['detalle'] ?? null;

            if (!$idMantenimiento || !$resultado || $detalle === null) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "Todos los parámetros son requeridos (idMantenimiento, resultado, detalle)"
                ]);
                break;
            }

            // Llamada al procedimiento almacenado P_FINALIZAR_MANTENIMIENTO
            $stmt = $pdo->prepare("CALL P_FINALIZAR_MANTENIMIENTO(:id, :resultado, :detalle)");
            $stmt->bindParam(':id', $idMantenimiento, PDO::PARAM_INT);
            $stmt->bindParam(':resultado', $resultado, PDO::PARAM_STR);
            $stmt->bindParam(':detalle', $detalle, PDO::PARAM_STR);

            $stmt->execute();
            $resp = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "msg" => $resp['mensaje'] ?? "Mantenimiento finalizado correctamente"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al finalizar mantenimiento: " . $e->getMessage()
            ]);
        }
        break;

    // 2.6. Actualizar Mantenimiento Finalizado (similar a la inspección)
    case 'actualizar_mantenimiento':
        // **NOTA:** No existe un procedimiento P_UPDATE_MANTENIMIENTO_FINALIZADO en tu SQL.
        // Asumo la necesidad de editar los detalles y el resultado de un mantenimiento ya finalizado.
        // Si no tienes este procedimiento, esta función generará un error de SQL.
        try {
            $idMantenimiento = $_POST['idMantenimiento'] ?? null;
            $resultado = $_POST['resultado'] ?? null;
            $detalle = $_POST['detalle'] ?? null;

            if (!$idMantenimiento || !$resultado || $detalle === null) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "Todos los parámetros son requeridos (idMantenimiento, resultado, detalle)"
                ]);
                break;
            }

            // **IMPORTANTE:** Reemplaza 'P_UPDATE_MANTENIMIENTO_FINALIZADO' con el SP que crees
            // O usa una sentencia UPDATE directa si no deseas un SP. 
            // Usaré una sentencia UPDATE directa como ejemplo si no existe el SP:
            
            $sql_update = "UPDATE mantenimientos
                           SET resultado = :resultado, detalle = :detalle
                           WHERE idMantenimiento = :id";
            
            $stmt = $pdo->prepare($sql_update);
            $stmt->bindParam(':id', $idMantenimiento, PDO::PARAM_INT);
            $stmt->bindParam(':resultado', $resultado, PDO::PARAM_STR);
            $stmt->bindParam(':detalle', $detalle, PDO::PARAM_STR);

            $stmt->execute();
            $rowCount = $stmt->rowCount();

            if ($rowCount > 0) {
                echo json_encode([
                    "status" => "success",
                    "msg" => "Mantenimiento actualizado correctamente."
                ]);
            } else {
                 echo json_encode([
                    "status" => "error",
                    "msg" => "No se realizó ninguna actualización. Verifique el ID o si el mantenimiento está finalizado."
                ]);
            }

        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
            
            echo json_encode([
                "status" => "error",
                "msg" => "Error al actualizar mantenimiento: " . $errorMsg
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