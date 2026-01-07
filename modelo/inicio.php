<?php
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');
require_once('Conection.php');

$conexion = new Conection();
$conn = $conexion->getConection();
$accion = $_POST['accion'] ?? '';

$data = [];

switch ($accion) {

    // Obtener distribución de equipos por área
    case 'EquiposPorArea':
        try {
            // Consulta para obtener la distribución de equipos por área
            $sql = "SELECT nombre_area, sigla, total FROM V_EQUIPOS_X_AREA";
            $stmt = $conn->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            $data = [
                "status" => "error",
                "msg" => "Error al obtener distribución de equipos por área: " . $e->getMessage()
            ];
        }
        break;

    // Accion no válida
    default:
        $data = [
            "status" => "error",
            "msg" => "Acción no válida"
        ];
        break;
}
echo json_encode($data); 
?>