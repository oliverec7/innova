<?php
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');
require_once('Conection.php');

$conexion = new Conection();
$pdo = $conexion->getConection();

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    // Listar inspecciones pendientes (Pendiente y En Proceso)
    case 'listar_pendientes':
        try {
            // Consulta SQL para obtener las inspecciones pendientes
            $sql = "SELECT
                        i.idInspeccion AS id,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', pi.nombres, pi.apellido_paterno, pi.apellido_materno) AS Inspector,
                        CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                        DATE_FORMAT(i.fecha_programada, '%d/%m/%Y') AS Fecha_Inspeccion,
                        s.razon AS Razon
                    FROM inspecciones i
                    INNER JOIN equipos e ON i.equipo_inspeccionar = e.idEquipo
                    INNER JOIN ordenes_trabajo ot ON i.orden_trabajar = ot.idOrden
                    INNER JOIN solicitudes s ON ot.solicitud_trabajo = s.idSolicitud
                    INNER JOIN usuarios u ON i.inspector = u.idUsuario
                    INNER JOIN personas pi ON u.persona = pi.idPersona
                    INNER JOIN personas pr ON i.responsable_equipo = pr.idPersona
                    WHERE i.estado IN ('Pendiente', 'En Proceso')
                    ORDER BY i.fecha_programada ASC, i.estado = 'Pendiente' DESC";
            
            // Ejecutar la consulta y obtener los resultados
            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar inspecciones pendientes: " . $e->getMessage()
            ]);
        }
        break;

    // Listar inspecciones finalizadas
    case 'listar_finalizadas':
        try {
            // Consulta SQL para obtener las inspecciones finalizadas
            $sql = "SELECT
                        i.idInspeccion AS id,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', pi.nombres, pi.apellido_paterno, pi.apellido_materno) AS Inspector,
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
                    WHERE i.estado = 'Finalizado'
                    ORDER BY i.fecha_fin_inspeccion DESC";

            // Ejecutar la consulta y obtener los resultados
            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar inspecciones finalizadas: " . $e->getMessage()
            ]);
        }
        break;

    // Visualizar detalles de una inspección específica
    case 'visualizar_inspeccion':
        try {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                throw new Exception("ID de inspección no proporcionado");
            }

            // Consulta para obtener los detalles de la inspección
            $sql = "SELECT
                        i.idInspeccion AS id,
                        e.nombre_equipo AS Equipo,
                        te.tipo_equipo AS Tipo_Equipo,
                        CONCAT_WS(' ', pi.nombres, pi.apellido_paterno, pi.apellido_materno) AS Inspector,
                        CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                        i.estado AS Estado,
                        DATE_FORMAT(i.fecha_programada, '%d/%m/%Y') AS Fecha_Programada,
                        DATE_FORMAT(i.fecha_inicio_inspeccion, '%d/%m/%Y') AS Fecha_Inicio,
                        DATE_FORMAT(i.fecha_inicio_inspeccion, '%H:%i:%s') AS Hora_Inicio,
                        DATE_FORMAT(i.fecha_fin_inspeccion, '%d/%m/%Y') AS Fecha_Fin,
                        DATE_FORMAT(i.fecha_fin_inspeccion, '%H:%i:%s') AS Hora_Fin,
                        i.resultado AS Resultado,
                        i.comentario AS Comentario
                    FROM inspecciones i
                    INNER JOIN equipos e ON i.equipo_inspeccionar = e.idEquipo
                    INNER JOIN tipos_equipos te ON e.tipo_equipo = te.idTipoEquipo
                    INNER JOIN ordenes_trabajo ot ON i.orden_trabajar = ot.idOrden
                    INNER JOIN usuarios u ON i.inspector = u.idUsuario
                    INNER JOIN personas pi ON u.persona = pi.idPersona
                    INNER JOIN personas pr ON i.responsable_equipo = pr.idPersona
                    WHERE i.idInspeccion = :id";

            // Preparar y ejecutar la consulta
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Obtener los datos de la inspección
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($data ? 
                ["status" => "success", "inspeccion" => $data] :
                ["status" => "error", "msg" => "Inspección no encontrada"]
            );
        
        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al visualizar inspección: " . $e->getMessage()
            ]);
        }
        break;

    // Actualizar una inspección como finalizada
    case 'actualizar_inspeccion':
        try {
            $id = $_POST['idInspeccion'] ?? null;
            $resultado = $_POST['resultado'] ?? null;
            $comentario = $_POST['comentario'] ?? null;

            if (!$id) {
                throw new Exception("ID de inspección no proporcionado");
            }

            if (!$resultado || !in_array($resultado, ['Conforme', 'No conforme'])) {
                throw new Exception("Resultado no válido");
            }

            // Llamar al procedimiento almacenado para actualizar la inspección
            $sql = "CALL P_UPDATE_INSPECCION_FINALIZADA(:id, :resultado, :comentario)";
            $stmt = $pdo->prepare($sql);

            // Vincular parámetros
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':resultado', $resultado, PDO::PARAM_STR);

            // Manejar el comentario que puede ser NULL
            if ($comentario === "" || $comentario === null) {
                $stmt->bindValue(':comentario', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
            }

            // Ejecutar el procedimiento almacenado
            $stmt->execute();
            $resp = $stmt->fetch(PDO::FETCH_ASSOC);

            // Responder con éxito
            echo json_encode([
                "status" => "success",
                "msg" => $resp['mensaje'] ?? "Inspección actualizada correctamente"
            ]);

            // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error en la base de datos: " . $e->getMessage()
            ]);

        // Manejar otros errores
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "msg" => $e->getMessage()
            ]);
        }
        break;

    // Acción no válida o no especificada
    default:
        echo json_encode([
            "status" => "error",
            "msg" => "Acción no válida o no especificada"
        ]);
}
?>
