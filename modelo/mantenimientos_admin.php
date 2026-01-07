<?php
header('Content-Type: application/json');
require_once('Conection.php');

$conexion = new Conection();
$pdo = $conexion->getConection();

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'listar_pendientes':
        try {
            $sql = "SELECT
                        m.idMantenimiento AS id,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', pu.nombres, pu.apellido_paterno, pu.apellido_materno) AS Tecnico_Asignado,
                        CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                        DATE_FORMAT(m.fecha_programada, '%d/%m/%Y') AS Fecha_Programada,
                        i.comentario AS Comentario
                    FROM mantenimientos m
                    INNER JOIN equipos e ON m.equipo_corregir = e.idEquipo
                    INNER JOIN inspecciones i ON m.inspeccion_relacionada = i.idInspeccion
                    INNER JOIN usuarios u ON m.tecnico_corrector = u.idUsuario
                    INNER JOIN personas pu ON u.persona = pu.idPersona
                    INNER JOIN personas pr ON m.responsable_equipo = pr.idPersona
                    ORDER BY m.fecha_programada ASC";
            
            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar mantenimientos pendientes: " . $e->getMessage()
            ]);
        }
        break;

    case 'listar_finalizados':
        try {
            // Mantenimientos finalizados (con fecha de fin)
            $sql = "SELECT
                        m.idMantenimiento AS id,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', pu.nombres, pu.apellido_paterno, pu.apellido_materno) AS Tecnico,
                        CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                        DATE_FORMAT(m.fecha_fin_mantenimiento, '%d/%m/%Y') AS Fecha_Fin,
                        DATE_FORMAT(m.fecha_inicio_mantenimiento, '%H:%i:%s') AS Hora_Inicio,
                        DATE_FORMAT(m.fecha_fin_mantenimiento, '%H:%i:%s') AS Hora_Fin,
                        m.resultado AS Resultado,
                        m.detalle AS Detalle
                    FROM mantenimientos m
                    INNER JOIN equipos e ON m.equipo_corregir = e.idEquipo
                    INNER JOIN usuarios u ON m.tecnico_corrector = u.idUsuario
                    INNER JOIN personas pu ON u.persona = pu.idPersona
                    INNER JOIN personas pr ON m.responsable_equipo = pr.idPersona
                    WHERE m.fecha_fin_mantenimiento IS NOT NULL
                    ORDER BY m.fecha_fin_mantenimiento DESC";

            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar mantenimientos finalizados: " . $e->getMessage()
            ]);
        }
        break;

    case 'visualizar_mantenimiento':
        try {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                throw new Exception("ID de mantenimiento no proporcionado");
            }

            $sql = "SELECT
                        m.idMantenimiento AS id,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', pu.nombres, pu.apellido_paterno, pu.apellido_materno) AS Tecnico,
                        CONCAT_WS(' ', pr.nombres, pr.apellido_paterno, pr.apellido_materno) AS Responsable,
                        m.fecha_programada AS Fecha_Programada,
                        m.fecha_inicio_mantenimiento AS Fecha_Inicio,
                        m.fecha_fin_mantenimiento AS Fecha_Fin,
                        m.resultado AS Resultado,
                        m.detalle AS Detalle
                    FROM mantenimientos m
                    INNER JOIN equipos e ON m.equipo_corregir = e.idEquipo
                    INNER JOIN usuarios u ON m.tecnico_corrector = u.idUsuario
                    INNER JOIN personas pu ON u.persona = pu.idPersona
                    INNER JOIN personas pr ON m.responsable_equipo = pr.idPersona
                    WHERE m.idMantenimiento = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Formateo de fechas para que JavaScript las pueda usar fácilmente
            if ($data) {
                $data['Fecha_Programada_Formato'] = (new DateTime($data['Fecha_Programada']))->format('d/m/Y');
                $data['Fecha_Inicio_Formato'] = $data['Fecha_Inicio'] ? (new DateTime($data['Fecha_Inicio']))->format('d/m/Y') : null;
                $data['Hora_Inicio_Formato'] = $data['Fecha_Inicio'] ? (new DateTime($data['Fecha_Inicio']))->format('H:i:s') : null;
                $data['Fecha_Fin_Formato'] = $data['Fecha_Fin'] ? (new DateTime($data['Fecha_Fin']))->format('d/m/Y') : null;
                $data['Hora_Fin_Formato'] = $data['Fecha_Fin'] ? (new DateTime($data['Fecha_Fin']))->format('H:i:s') : null;
                
                unset($data['Fecha_Inicio']);
                unset($data['Fecha_Fin']);
            }


            echo json_encode($data ? 
                ["status" => "success", "mantenimiento" => $data] :
                ["status" => "error", "msg" => "Mantenimiento no encontrado"]
            );

        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "msg" => $e->getMessage()
            ]);
        }
        break;

    case 'actualizar_mantenimiento':
        try {
            $id = $_POST['idMantenimiento'] ?? null;
            $resultado = $_POST['resultado'] ?? null;
            $detalle = $_POST['detalle'] ?? null;

            if (!$id) {
                throw new Exception("ID de mantenimiento no proporcionado");
            }

            if (!$resultado || !in_array($resultado, ['Funcional', 'No Funcional'])) {
                throw new Exception("Resultado no válido");
            }

            // Usamos la lógica de UPDATE directo que definimos en el modelo técnico para actualizar el resultado/detalle
            $sql = "UPDATE mantenimientos
                    SET resultado = :resultado, detalle = :detalle
                    WHERE idMantenimiento = :id AND fecha_fin_mantenimiento IS NOT NULL";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':resultado', $resultado, PDO::PARAM_STR);
            $stmt->bindParam(':detalle', $detalle, PDO::PARAM_STR);

            $stmt->execute();

            $rowCount = $stmt->rowCount();

            if ($rowCount > 0) {
                 echo json_encode([
                    "status" => "success",
                    "msg" => "Mantenimiento actualizado correctamente"
                ]);
            } else {
                 echo json_encode([
                    "status" => "error",
                    "msg" => "No se pudo actualizar el mantenimiento. Verifique que el ID existe y que esté finalizado."
                ]);
            }


        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error en la base de datos: " . $e->getMessage()
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "msg" => $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode([
            "status" => "error",
            "msg" => "Acción no válida o no especificada"
        ]);
}
?>