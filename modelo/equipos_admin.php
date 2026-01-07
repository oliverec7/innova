<?php
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');
require_once('Conection.php');

// Crear la instancia de conexión
$conexion = new Conection();
$pdo = $conexion->getConection();

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    // Insertar equipos
    case 'insertar':
        try {
            // Ejecutar la consulta
            $stmt = $pdo->prepare("CALL P_INSERT_EQUIPO(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['codigo_patrimonial'],
                $_POST['nombre_equipo'],
                $_POST['tipo_equipo'],
                $_POST['marca'],
                $_POST['serie'],
                $_POST['modelo'],
                $_POST['responsable'],
                $_POST['fecha_compra'],
                $_POST['fecha_instalacion']
            ]);
            echo json_encode([
                "status" => "ok",
                "msg" => "Equipo guardado correctamente"
            ]);
        
        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al guardar el equipo: " . $e->getMessage()
            ]);
        }
        break;
    
    // Actualizar equipos
    case 'actualizar':
        try {
            $stmt = $pdo->prepare("CALL P_UPDATE_EQUIPO(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['id'],
                $_POST['codigo_patrimonial'],
                $_POST['nombre_equipo'],
                $_POST['tipo_equipo'],
                $_POST['marca'],
                $_POST['serie'],
                $_POST['modelo'],
                $_POST['responsable'],
                $_POST['fecha_compra'],
                $_POST['fecha_instalacion']
            ]);
            echo json_encode([
                "status" => "ok",
                "msg" => "Equipo modificado correctamente"
            ]);

        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al modificar el equipo: " . $e->getMessage()
            ]);
        }
        break;
    
    // Eliminar equipos
    case 'eliminar':
        try {
            $stmt = $pdo->prepare("CALL P_DELETE_EQUIPO(?)");
            $stmt->execute([$_POST['id']]);
            echo json_encode([
                "status" => "ok",
                "msg" => "Equipo eliminado con éxito"
            ]);

        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al eliminar el equipo: " . $e->getMessage()
            ]);
        }
        break;

    // Listar equipos
    case 'listar':
        try {
            $stmt = $pdo->query("SELECT * FROM v_equipos ORDER BY Cod_patrimonial ASC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        
        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar equipos: " . $e->getMessage()
            ]);
        }
        break;

    // Obtener equipo mediante ID
    case 'obtener_por_id':
        if (isset($_POST['id'])) {
            try {
                $id = $_POST['id'];
                $sql = "SELECT * FROM equipos WHERE idEquipo = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id]);
                
                $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Formatear las fechas antes de enviar la respuesta
                if ($equipo) {
                    $equipo['fecha_compra'] = $equipo['fecha_compra'] ? date('Y-m-d', strtotime($equipo['fecha_compra'])) : '';
                    $equipo['fecha_instalacion'] = $equipo['fecha_instalacion'] ? date('Y-m-d', strtotime($equipo['fecha_instalacion'])) : '';
                    echo json_encode(['status' => 'ok', 'data' => $equipo]);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Equipo no encontrado']);
                }

            // Manejar errores de la base de datos    
            } catch (PDOException $e) {
                echo json_encode([
                    "status" => "error",
                    "msg" => "Error al obtener el equipo: " . $e->getMessage()
                ]);
            }
        }
        break;
    
    // Verificar si el código patrimonial ya existe
    case 'verificar_cod_patrimonial':
        $codigo_patrimonial = trim($_POST['codigo_patrimonial'] ?? '');
        $id_excluir = $_POST['id_excluir'] ?? null;
        
        // Realizar la verificación
        if (!empty($codigo_patrimonial)) {
            $codigo_patrimonial = strtoupper($codigo_patrimonial);
            
            // Preparar y ejecutar la consulta
            if ($id_excluir) {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM equipos WHERE codigo_patrimonial = ? AND idEquipo != ?");
                $stmt->execute([$codigo_patrimonial, $id_excluir]);
            } else {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM equipos WHERE codigo_patrimonial = ?");
                $stmt->execute([$codigo_patrimonial]);
            }
            
            // Obtener el resultado
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['existe' => $result['count'] > 0]);
        } else {
            echo json_encode(['existe' => false]);
        }
        break;

    // Listar marcas
    case 'listar_marcas':
        try {
            $stmt = $pdo->query("SELECT idMarca, marca AS Marca FROM marcas");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);

        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar marcas: " . $e->getMessage()
            ]);
        }
        break;
    
    // Listar tipos de equipos
    case 'listar_tipos':
        try {
            $stmt = $pdo->query("SELECT idTipoEquipo, tipo_equipo AS Tipo FROM tipos_equipos");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        
        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar tipos: " . $e->getMessage()
            ]);
        }
        break;

    // Listar personas
    case 'listar_personas':
        try {
            $stmt = $pdo->query("SELECT idPersona, CONCAT_WS(' ',apellido_paterno, apellido_materno, nombres) AS Responsable FROM personas ORDER BY dni ASC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        
        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar personas: " . $e->getMessage()
            ]);
        }
        break;

    // Acción no válida o no especificada
    default:
        echo json_encode([
            "status" => "error",
            "msg" => "Acción no válida o no especificada"
        ]);
        break;
}
?>