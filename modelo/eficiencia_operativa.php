<?php
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');
require_once('Conection.php');

// Crear la instancia de conexión
$conexion = new Conection();
$conn = $conexion->getConection();
$accion = $_POST['accion'] ?? '';

$data = [];

switch ($accion) {

    // Obtener la carga de trabajo por técnico
    case 'CargaTecnico':
        try {
            $sql = "SELECT
                        CONCAT_WS(' ',p.nombres, p.apellido_paterno, p.apellido_materno) AS nombre_completo,
                        COUNT(i.idInspeccion) AS total_inspecciones_activas
                    FROM inspecciones i
                    INNER JOIN usuarios u ON I.inspector = U.idUsuario
                    INNER JOIN personas p ON U.persona = P.idPersona
                    WHERE i.estado IN ('Pendiente', 'En Proceso')
                    GROUP BY p.nombres, p.apellido_paterno, p.apellido_materno
                    ORDER BY total_inspecciones_activas DESC";

            // Ejecutar la consulta
            $stmt = $conn->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Preparar la respuesta
            $data = [
                "status" => "success",
                "data" => $results
            ];
        
        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al obtener la carga de trabajo por técnico: " . $e->getMessage()
            ];
        }
        break;
    
    // Obtener el tiempo de respuesta promedio
    case 'TiempoRespuesta':
        try {
            $sql = "SELECT 
                        AVG(TIMESTAMPDIFF(SECOND, s.fecha_generada, ot.fecha_asignacion)) / 3600 AS promedio_horas
                    FROM solicitudes s
                    INNER JOIN ordenes_trabajo ot ON s.idSolicitud = ot.solicitud_trabajo
                    WHERE s.estado = 'Aprobada'
                    AND ot.fecha_asignacion IS NOT NULL";

            // Ejecutar la consulta
            $stmt = $conn->query($sql);
            $promedio = $stmt->fetchColumn();

            $promedio_formateado = round((float)$promedio, 2);

            // Preparar la respuesta
            $data = [
                "status" => "success",
                "promedio_horas" => $promedio_formateado
            ];

        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al obtener el tiempo de respuesta promedio: " . $e->getMessage()
            ];
        }
        break;

    // Acción no válida
    default:
        $data = [
            "status" => "error",
            "msg" => "Acción no válida"
        ];
        break;
}

// Devolver la respuesta en formato JSON
echo json_encode($data); 
?>