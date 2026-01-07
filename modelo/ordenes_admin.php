<?php
header('Content-Type: application/json');
require_once('Conection.php');

$conexion = new Conection();
$conn = $conexion->getConection();
$accion = $_POST['accion'] ?? '';

$data = [];

switch ($accion) {

    case 'total_ordenes':
        try {
            $sql = "SELECT COUNT(*) AS total FROM ordenes_trabajo";
            $stmt = $conn->query($sql);
            $data = $stmt->fetch();
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al ejecutar la consulta: " . $e->getMessage()
            ];
        }
        break;

    case 'ordenes_pendientes':
        try {
            $sql = "SELECT COUNT(*) AS total FROM ordenes_trabajo WHERE estado = 'Pendiente'";
            $stmt = $conn->query($sql);
            $data = $stmt->fetch();
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al ejecutar la consulta: " . $e->getMessage()
            ];
        }
        break;

    case 'ordenes_asignadas':
        try {
            $sql = "SELECT COUNT(*) AS total FROM ordenes_trabajo WHERE estado = 'Asignada'";
            $stmt = $conn->query($sql);
            $data = $stmt->fetch();
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al ejecutar la consulta: " . $e->getMessage()
            ];
        }
        break;

    case 'listar_pendientes':
        try {
            $sql = "SELECT 
                        ot.idOrden AS id,
                        ot.tipo_orden AS Tipo_Orden,
                        ot.prioridad AS Prioridad,
                        ot.fecha_creacion AS Fecha_Orden,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', p.nombres, p.apellido_paterno, p.apellido_materno) AS Solicitante,
                        s.razon as Razon
                    FROM ordenes_trabajo ot
                    INNER JOIN equipos e ON ot.equipo_trabajar = e.idEquipo
                    LEFT JOIN solicitudes s ON ot.solicitud_trabajo = s.idSolicitud
                    LEFT JOIN usuarios u ON s.empleado_solicitante = u.idUsuario
                    LEFT JOIN personas p ON u.persona = p.idPersona
                    WHERE ot.estado = 'Pendiente'
                    ORDER BY 
                        CASE ot.prioridad 
                            WHEN 'Alta' THEN 1 
                            WHEN 'Media' THEN 2 
                            WHEN 'Baja' THEN 3 
                        END,
                        ot.fecha_creacion DESC";
            
            $stmt = $conn->query($sql);
            $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $data = $ordenes;
            
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al ejecutar la consulta: " . $e->getMessage()
            ];
        }
        break;

    case 'listar_asignadas':
        try {
            $sql = "SELECT 
                        ot.idOrden AS id,
                        ot.tipo_orden AS Tipo_Orden,
                        ot.prioridad AS Prioridad,
                        DATE_FORMAT(ot.fecha_creacion, '%d/%m/%Y %H:%i:%s') AS Fecha_Orden,
                        DATE_FORMAT(i.fecha_programada, '%d/%m/%Y') AS Fecha_Programada,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', ps.nombres, ps.apellido_paterno, ps.apellido_materno) AS Solicitante,
                        CONCAT_WS(' ', pt.nombres, pt.apellido_paterno, pt.apellido_materno) AS Tecnico_Asignado,
                        s.razon as Razon
                    FROM ordenes_trabajo ot
                    INNER JOIN equipos e ON ot.equipo_trabajar = e.idEquipo
                    LEFT JOIN solicitudes s ON ot.solicitud_trabajo = s.idSolicitud
                    LEFT JOIN inspecciones i ON ot.idOrden = i.orden_trabajar
                    LEFT JOIN usuarios us ON s.empleado_solicitante = us.idUsuario
                    LEFT JOIN personas ps ON us.persona = ps.idPersona
                    LEFT JOIN usuarios ut ON ot.tecnico_asignado = ut.idUsuario
                    LEFT JOIN personas pt ON ut.persona = pt.idPersona
                    WHERE ot.estado = 'Asignada'
                    ORDER BY ot.fecha_asignacion DESC";
            
            $stmt = $conn->query($sql);
            $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $data = $ordenes;
            
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al ejecutar la consulta: " . $e->getMessage()
            ];
        }
        break;

    case 'listar_tecnicos':
        try {
            $sql = "SELECT 
                        u.idUsuario AS id,
                        CONCAT_WS(' ', p.nombres, p.apellido_paterno, p.apellido_materno) AS Tecnico
                    FROM usuarios u
                    INNER JOIN personas p ON u.persona = p.idPersona
                    INNER JOIN roles r ON u.rol = r.idRol
                    WHERE r.rol = 'Tecnico' -- CORRECCIÓN: Usar 'Tecnico' (sin tilde) para coincidir con el INSERT de la BD
                    ORDER BY p.apellido_paterno, p.apellido_materno, p.nombres";
            
            $stmt = $conn->query($sql);
            $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $data = [
                "status" => "success",
                "tecnicos" => $tecnicos
            ];
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al obtener técnicos: " . $e->getMessage()
            ];
        }
        break;

    case 'obtener_orden':
        try {
            $idOrden = $_POST['id'] ?? null;
            
            if (!$idOrden) {
                throw new Exception("ID de orden no proporcionado");
            }
            
            $sql = "SELECT
                        ot.idOrden AS id,
                        ot.tipo_orden AS Tipo_Orden,
                        ot.prioridad AS Prioridad,
                        ot.fecha_creacion AS Fecha_Orden,
                        e.nombre_equipo AS Equipo,
                        CONCAT_WS(' ', p.nombres, p.apellido_paterno, p.apellido_materno) AS Solicitante,
                        s.razon AS Razon
                    FROM ordenes_trabajo ot
                    INNER JOIN equipos e ON ot.equipo_trabajar = e.idEquipo
                    LEFT JOIN solicitudes s ON ot.solicitud_trabajo = s.idSolicitud
                    LEFT JOIN usuarios u ON s.empleado_solicitante = u.idUsuario
                    LEFT JOIN personas p ON u.persona = p.idPersona
                    WHERE ot.idOrden = :idOrden";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idOrden', $idOrden, PDO::PARAM_INT);
            $stmt->execute();
            $orden = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($orden) {
                $data = [
                    "status" => "success",
                    "orden" => $orden
                ];
            } else {
                $data = [
                    "status" => "error",
                    "msg" => "Orden no encontrada"
                ];
            }
            
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al obtener detalles: " . $e->getMessage()
            ];
        } catch (Exception $e) {
            $data = [
                "status" => "error",
                "msg" => $e->getMessage()
            ];
        }
        break;

    case 'disponibilidad_tecnico':
        try {
            $idTecnico = $_POST['id_tecnico'] ?? null;
            
            if (!$idTecnico) {
                throw new Exception("ID de técnico no proporcionado");
            }
            
            // CORRECCIÓN: Contar solo las inspecciones en estado 'Pendiente' o 'En Proceso'
            $sql = "SELECT COUNT(*) as ordenes_activas
                    FROM inspecciones
                    WHERE inspector = :idTecnico
                    AND estado IN ('Pendiente', 'En Proceso')"; // La lógica del límite de 3 se basa en la tabla inspecciones
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idTecnico', $idTecnico, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $data = [
                "status" => "success",
                "ordenes_activas" => $result['ordenes_activas'] ?? 0 // Cambiado el nombre de la clave para reflejar la corrección
            ];
            
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al verificar disponibilidad: " . $e->getMessage()
            ];
        } catch (Exception $e) {
            $data = [
                "status" => "error",
                "msg" => $e->getMessage()
            ];
        }
        break;

    case 'asignar_tecnico':
        try {
            $idOrden = $_POST['id_orden'] ?? null;
            $idTecnico = $_POST['id_tecnico'] ?? null;
            
            // Validar datos requeridos
            if (!$idOrden || !$idTecnico) {
                throw new Exception("Faltan datos requeridos: ID de orden y técnico");
            }

            // Validar que sean números
            if (!is_numeric($idOrden) || !is_numeric($idTecnico)) {
                throw new Exception("Los IDs deben ser valores numéricos");
            }

            // Llamar al stored procedure actualizado
            $sql = "CALL P_ASIGNAR_TECNICO(:idOrden, :idTecnico)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idOrden', $idOrden, PDO::PARAM_INT);
            $stmt->bindParam(':idTecnico', $idTecnico, PDO::PARAM_INT);
            $stmt->execute();
            
            // Obtener el mensaje del procedimiento
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $data = [
                "status" => "success",
                "msg" => $result['mensaje'] ?? "Técnico asignado exitosamente",
                "orden_id" => $idOrden,
                "tecnico_id" => $idTecnico
            ];
            
        } catch (PDOException $e) {
            // Manejar errores específicos del stored procedure
            $errorMsg = $e->getMessage();
            
            // Mejorar mensajes de error específicos (Actualizados para coincidir con SP)
            if (strpos($errorMsg, 'La orden de trabajo no existe') !== false) {
                $msg = "La orden de trabajo especificada no existe";
            } elseif (strpos($errorMsg, 'La orden de trabajo ya fue asignada') !== false) {
                $msg = "La orden de trabajo ya fue asignada a otro técnico";
            } elseif (strpos($errorMsg, 'El usuario no existe o no tiene rol de técnico') !== false) {
                $msg = "El usuario seleccionado no existe o no tiene permiso de técnico";
            } elseif (strpos($errorMsg, 'El técnico tiene el máximo de inspecciones pendientes/en proceso') !== false) {
                // Mensaje mejorado para incluir el límite (3) que ya estaba en el código original
                $msg = "El técnico seleccionado tiene el máximo permitido de inspecciones (3) pendientes o en proceso"; 
            } else {
                $msg = "Error al asignar técnico: " . $errorMsg;
            }
            
            $data = [
                "status" => "error",
                "msg" => $msg,
                "error_code" => $e->getCode()
            ];
            
        } catch (Exception $e) {
            $data = [
                "status" => "error",
                "msg" => $e->getMessage()
            ];
        }
        break;

    case 'reasignar_tecnico':
        try {
            $idOrden = $_POST['id_orden'] ?? null;
            $idNuevoTecnico = $_POST['id_nuevo_tecnico'] ?? null;
            
            // Validar datos requeridos
            if (!$idOrden || !$idNuevoTecnico) {
                throw new Exception("Faltan datos requeridos: ID de orden y nuevo técnico");
            }

            // Validar que sean números
            if (!is_numeric($idOrden) || !is_numeric($idNuevoTecnico)) {
                throw new Exception("Los IDs deben ser valores numéricos");
            }

            // Llamar al stored procedure de reasignación actualizado
            $sql = "CALL P_REASIGNAR_TECNICO(:idOrden, :idNuevoTecnico)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idOrden', $idOrden, PDO::PARAM_INT);
            $stmt->bindParam(':idNuevoTecnico', $idNuevoTecnico, PDO::PARAM_INT);
            $stmt->execute();
            
            // Obtener el mensaje del procedimiento
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $data = [
                "status" => "success",
                "msg" => $result['mensaje'] ?? "Técnico reasignado exitosamente",
                "orden_id" => $idOrden,
                "nuevo_tecnico_id" => $idNuevoTecnico
            ];
            
        } catch (PDOException $e) {
            // Capturar errores específicos del stored procedure
            $errorMsg = $e->getMessage();
            
            // Mejorar mensajes de error específicos (Actualizados para coincidir con SP)
            if (strpos($errorMsg, 'La orden de trabajo no existe') !== false) {
                $msg = "La orden de trabajo especificada no existe";
            } elseif (strpos($errorMsg, 'Solo se pueden reasignar órdenes en estado Asignada') !== false) {
                $msg = "Solo se pueden reasignar órdenes que ya están asignadas";
            } elseif (strpos($errorMsg, 'El técnico especificado ya está asignado a esta orden') !== false) {
                $msg = "El técnico seleccionado ya está asignado a esta orden";
            } elseif (strpos($errorMsg, 'No se puede reasignar: la inspección ya fue finalizada') !== false) {
                $msg = "No se puede reasignar porque la inspección ya fue finalizada";
            } elseif (strpos($errorMsg, 'El usuario no existe o no tiene rol de técnico') !== false) {
                $msg = "El nuevo técnico no existe o no tiene permiso de técnico";
            } elseif (strpos($errorMsg, 'El nuevo técnico tiene el máximo de inspecciones pendientes/en proceso') !== false) {
                 // Mensaje mejorado para incluir el límite (3) que ya estaba en el código original
                $msg = "El nuevo técnico tiene el máximo permitido de inspecciones (3) pendientes o en proceso";
            } else {
                $msg = "Error al reasignar técnico: " . $errorMsg;
            }
            
            $data = [
                "status" => "error",
                "msg" => $msg,
                "error_code" => $e->getCode()
            ];
            
        } catch (Exception $e) {
            $data = [
                "status" => "error",
                "msg" => $e->getMessage()
            ];
        }
        break;

    default:
        $data = [
            "status" => "error",
            "msg" => "Acción no válida"
        ];
        break;
}

echo json_encode($data); 
?>