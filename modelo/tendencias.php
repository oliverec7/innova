<?php

header('Content-Type: application/json');
require_once('Conection.php');

$conexion = new Conection();
$conn = $conexion->getConection();
$accion = $_POST['accion'] ?? '';

$data = [];

switch ($accion) {

    case 'FallasMensuales':
        try {
            $sql = "SELECT
                        DATE_FORMAT(M.fecha_fin_mantenimiento, '%Y-%m') AS anio_mes,
                        YEAR(M.fecha_fin_mantenimiento) AS anio,
                        MONTH(M.fecha_fin_mantenimiento) AS mes,
                        COUNT(M.idMantenimiento) AS total_fallas
                    FROM mantenimientos M
                    WHERE M.resultado = 'No Funcional'
                        AND M.fecha_fin_mantenimiento >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY anio_mes, anio, mes
                    ORDER BY anio_mes ASC";

            $stmt = $conn->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $start_date = new DateTime('-11 months first day of this month');
            $end_date = new DateTime('last day of this month');
            $fallas_mensuales = [];
            
            while ($start_date <= $end_date) {
                $key = $start_date->format('Y-m');
                $month_name = $start_date->format('M Y');
                $fallas_mensuales[$key] = [
                    'label' => $month_name,
                    'count' => 0
                ];
                $start_date->modify('+1 month');
            }

            foreach ($results as $row) {
                $key = $row['anio_mes'];
                if (isset($fallas_mensuales[$key])) {
                    $fallas_mensuales[$key]['count'] = (int)$row['total_fallas'];
                }
            }
            
            $labels = array_column($fallas_mensuales, 'label');
            $values = array_column($fallas_mensuales, 'count');

            $data = [
                "status" => "success",
                "labels" => $labels,
                "values" => $values
            ];

        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al obtener tendencias de fallas mensuales: " . $e->getMessage()
            ];
        }
        break;

    case 'SolicitudesArea':
        try {
            $sql = "SELECT
                        A.sigla AS sigla_area,
                        A.nombre_area,
                        COUNT(S.idSolicitud) AS total_solicitudes
                    FROM solicitudes S
                    INNER JOIN usuarios U ON S.empleado_solicitante = U.idUsuario
                    INNER JOIN personas P ON U.persona = P.idPersona
                    INNER JOIN areas A ON P.area_trabajo = A.idArea
                    GROUP BY A.idArea, A.sigla, A.nombre_area
                    ORDER BY total_solicitudes DESC";

            $stmt = $conn->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = [
                "status" => "success",
                "data" => $results
            ];

        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al obtener distribución de solicitudes por área: " . $e->getMessage()
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