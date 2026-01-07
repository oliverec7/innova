<?php
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');
require_once('Conection.php');

if (session_status() === PHP_SESSION_NONE) session_start();

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

switch ($accion) {

    // Listar equipos asignados al empleado
    case 'listar':
        try {
            // Consulta para obtener los equipos asignados al empleado
            $sql = "SELECT
                        e.idEquipo AS id,
                        e.codigo_patrimonial AS Cod_patrimonial,
                        e.nombre_equipo AS Equipo,
                        te.tipo_equipo AS Tipo,
                        m.marca AS Marca,
                        e.serie AS Serie,
                        e.modelo AS Modelo,
                        CONCAT_WS(' ',p.nombres, p.apellido_paterno, p.apellido_materno) AS Responsable,
                        e.fecha_compra AS Compra,
                        e.fecha_instalacion AS Instalacion
                    FROM equipos e
                    JOIN tipos_equipos te ON e.tipo_equipo = te.idTipoEquipo
                    JOIN marcas m ON e.marca = m.idMarca
                    JOIN personas p ON e.responsable = p.idPersona
                    JOIN usuarios u ON u.persona = p.idPersona
                    WHERE u.usuario = :username
                    ORDER BY e.codigo_patrimonial ASC";

            // Preparar y ejecutar la consulta
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            // Obtener los resultados
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Devolver los datos en formato JSON
            echo json_encode($data);

        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "msg" => "Error al listar equipos: " . $e->getMessage()
            ]);
        }
        break;
    
    // Obtener detalles de un equipo específico
    case 'obtener_equipo':
        try {
            $idEquipo = $_POST['id'] ?? null;
            
            if (empty($idEquipo)) {
                echo json_encode([
                    "success" => false,
                    "message" => "ID de equipo no proporcionado"
                ]);
                exit;
            }
            
            // consulta para obtener los detalles del equipo
            $sql = "SELECT
                        e.idEquipo AS id,
                        e.codigo_patrimonial AS Cod_patrimonial,
                        e.nombre_equipo AS Equipo,
                        te.tipo_equipo AS Tipo,
                        m.marca AS Marca,
                        e.serie AS Serie,
                        e.modelo AS Modelo,
                        e.fecha_compra AS Compra,
                        e.fecha_instalacion AS Instalacion
                    FROM equipos e
                    JOIN tipos_equipos te ON e.tipo_equipo = te.idTipoEquipo
                    JOIN marcas m ON e.marca = m.idMarca
                    JOIN personas p ON e.responsable = p.idPersona
                    JOIN usuarios u ON u.persona = p.idPersona
                    WHERE e.idEquipo = :idEquipo
                    AND u.usuario = :username";
            
            // Preparar y ejecutar la consulta
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idEquipo', $idEquipo, PDO::PARAM_INT);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            // Obtener el resultado
            $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Devolver los datos en formato JSON
            if ($equipo) {
                echo json_encode([
                    "success" => true,
                    "equipo" => $equipo
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Equipo no encontrado o no autorizado"
                ]);
            }

        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            echo json_encode([
                "success" => false,
                "message" => "Error al obtener equipo: " . $e->getMessage()
            ]);
        }
        break;
    
    // Solicitar inspección para un equipo
    case 'solicitar_inspeccion':
        try {
            $idEquipo = isset($_POST['id']) ? trim($_POST['id']) : '';
            $razon = isset($_POST['razon']) ? trim($_POST['razon']) : '';
            
            // Validar datos de entrada
            if ($idEquipo === '' || $razon === '') {
                echo json_encode([
                    "success" => false,
                    "message" => "Datos incompletos. Se requiere ID de equipo y razón."
                ]);
                exit;
            }
            
            // Validar que el ID del equipo sea numérico
            if (!is_numeric($idEquipo)) {
                echo json_encode([
                    "success" => false,
                    "message" => "El ID del equipo no es válido"
                ]);
                exit;
            }
            
            // Validar longitud mínima de la razón
            if (strlen($razon) < 10) {
                echo json_encode([
                    "success" => false,
                    "message" => "La razón debe tener al menos 10 caracteres"
                ]);
                exit;
            }
            
            // consultar el ID del empleado basado en el nombre de usuario
            $sqlEmpleado = "SELECT u.idUsuario
                            FROM usuarios u
                            WHERE u.usuario = :username";
            
            // Preparar y ejecutar la consulta
            $stmtEmpleado = $pdo->prepare($sqlEmpleado);
            $stmtEmpleado->bindParam(':username', $username, PDO::PARAM_STR);
            $stmtEmpleado->execute();
            
            // Obtener el resultado
            $empleado = $stmtEmpleado->fetch(PDO::FETCH_ASSOC);
            
            // Verificar si se encontró el empleado
            if (!$empleado) {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo identificar al empleado solicitante"
                ]);
                exit;
            }
            
            // Verificar si ya existe una solicitud pendiente para el mismo equipo
            $idEmpleado = $empleado['idUsuario'];

            // Consulta para verificar solicitudes pendientes
            $sqlVerificar = "SELECT COUNT(*) AS tiene_pendiente
                            FROM solicitudes
                            WHERE equipo_solicitado = :equipo
                            AND estado = 'Pendiente'";
            $stmtVerificar = $pdo->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':equipo', $idEquipo, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

            // Si ya hay una solicitud pendiente, devolver un mensaje de error
            if ($resultado && $resultado['tiene_pendiente'] > 0) {
                echo json_encode([
                    "success" => false,
                    "message" => "Este equipo ya tiene una solicitud pendiente. No se pueden crear solicitudes duplicadas."
                ]);
                exit;
            }

            // Llamar al procedimiento almacenado para insertar la solicitud
            $sql = "CALL P_INSERT_SOLICITUD(:equipo, :empleado, :razon)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':equipo', $idEquipo, PDO::PARAM_INT);
            $stmt->bindParam(':empleado', $idEmpleado, PDO::PARAM_INT);
            $stmt->bindParam(':razon', $razon, PDO::PARAM_STR);
            $stmt->execute();
            
            // Devolver una respuesta exitosa
            echo json_encode([
                "success" => true,
                "message" => "Su solicitud de inspección fue enviada correctamente y está pendiente de revisión."
            ]);

        // Manejar errores de la base de datos
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                echo json_encode([
                    "success" => false,
                    "message" => $e->getMessage()
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al registrar solicitud: " . $e->getMessage()
                ]);
            }
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