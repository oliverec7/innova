<?php
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');
require_once('Conection.php');

// Crear instancia de conexión
$conexion = new Conection();
$conn = $conexion->getConection();
$accion = $_POST['accion'] ?? '';

$data = [];

switch ($accion) {
    
    // Obtener lista de equipos críticos con más de 3 fallas en los últimos 90 días
    case 'EquiposCriticos':
        try {
            $sql = "SELECT
                        e.codigo_patrimonial,
                        e.nombre_equipo,
                        COUNT(m.idMantenimiento) AS total_fallas
                    FROM mantenimientos m
                    INNER JOIN equipos e ON m.equipo_corregir = e.idEquipo
                    WHERE m.resultado = 'No Funcional'
                        AND m.fecha_fin_mantenimiento >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
                    GROUP BY e.idEquipo, e.codigo_patrimonial, e.nombre_equipo
                    HAVING total_fallas >= 3
                    ORDER BY total_fallas DESC";

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
                "msg" => "Error al obtener equipos críticos: " . $e->getMessage()
            ];
        }
        break;

    // Obtener lista de inspecciones atrasadas por área
    case 'InspeccionesAtrasadas':
        try {
            $sql = "SELECT
                        a.nombre_area,
                        COUNT(i.idInspeccion) AS inspecciones_atrasadas
                    FROM inspecciones i
                    INNER JOIN personas p ON i.responsable_equipo = p.idPersona
                    INNER JOIN areas a ON p.area_trabajo = a.idArea
                    WHERE i.estado = 'Pendiente' AND i.fecha_programada < CURDATE()
                    GROUP BY a.nombre_area
                    ORDER BY inspecciones_atrasadas DESC";

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
                "msg" => "Error al obtener inspecciones atrasadas: " . $e->getMessage()
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