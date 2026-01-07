<?php

header('Content-Type: application/json');
require_once('Conection.php');

$conexion = new Conection();
$conn = $conexion->getConection();
$accion = $_POST['accion'] ?? '';

$data = [];

switch ($accion) {
    
    case 'CumplimientoInspecciones':
        try {
            $sql_finalizadas = "SELECT COUNT(*) AS total_finalizadas
                                FROM inspecciones
                                WHERE estado = 'Finalizado'";

            $stmt_finalizadas = $conn->query($sql_finalizadas);
            $finalizadas = (int)$stmt_finalizadas->fetchColumn();

            $sql_programadas = "SELECT COUNT(*) AS total_programadas
                                FROM ordenes_trabajo
                                WHERE tipo_orden = 'Inspección' AND estado != 'Anulada'";

            $stmt_programadas = $conn->query($sql_programadas);
            $programadas = (int)$stmt_programadas->fetchColumn();

            $porcentaje = ($programadas > 0) ? round(($finalizadas / $programadas) * 100, 2) : 0;

            $data = [
                "status" => "success",
                "finalizadas" => $finalizadas,
                "programadas" => $programadas,
                "porcentaje" => $porcentaje
            ];

        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al obtener cumplimiento de inspecciones: " . $e->getMessage()
            ];
        }
        break;

    case 'SolicitudesStatus':
        try {
            $sql = "SELECT 
                        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                        SUM(CASE WHEN estado IN ('Aprobada', 'Rechazada') THEN 1 ELSE 0 END) AS resueltas
                    FROM solicitudes";

            $stmt = $conn->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $data = [
                "status" => "success",
                "pendientes" => (int)$result['pendientes'],
                "resueltas" => (int)$result['resueltas']
            ];

        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al obtener estado de solicitudes: " . $e->getMessage()
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