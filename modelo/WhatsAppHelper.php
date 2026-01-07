<?php
// ejecuar C:\xampp\php\php.exe "C:\xampp\htdocs\ProyectoWeb\modelo\WhatsAppHelper.php"

require_once('Conection.php');

// Crear la instancia de conexión
$conexion = new Conection();
$pdo = $conexion->getConection();

if (!$pdo) {
    die("Error Fatal: No se pudo establecer la conexión a la base de datos.");
}

// Configuración de Telegram
$telegram_token = '8558312822:AAHYVwRGN8edLMuxxAxNUNocbqTxELVGNzs'; 

$chat_id_mapping = [
    '942733499' => '1402009803',
    '921015108' => '1402009803'];

$numeros_validos = ['942733499','921015108'];

$placeholders = implode(',', array_fill(0, count($numeros_validos), '?'));

// Consultar notificaciones no leídas
$sql_select ="SELECT 
                n.idNotificacion, 
                n.mensaje, 
                p.telefono, 
                p.nombres
            FROM notificaciones n
            JOIN usuarios u ON n.usuario_destino = u.idUsuario
            JOIN personas p ON u.persona = p.idPersona
            LEFT JOIN notificaciones_whatsapp nw ON n.idNotificacion = nw.idNotificacion
            WHERE n.estado_notificacion = 'No leída' 
            AND p.telefono IN ({$placeholders}) 
            AND nw.idLog IS NULL 
            LIMIT 10";

$stmt = $pdo->prepare($sql_select);
$stmt->execute($numeros_validos);
$notificaciones = $stmt->fetchAll();

if (empty($notificaciones)) {
    exit;
}

// Enviar notificaciones a través de Telegram
foreach ($notificaciones as $notificacion) {
    $idNotificacion = $notificacion['idNotificacion'];
    $telefono_local = $notificacion['telefono'];
    $mensaje_db = $notificacion['mensaje'];
    $nombre = $notificacion['nombres'];

    // Obtener el chat ID de Telegram
    if (!isset($chat_id_mapping[$telefono_local])) {
        error_log("Advertencia: No se encontró ID de Chat de Telegram para el teléfono: {$telefono_local}");
        continue;
    }

    $chat_id = $chat_id_mapping[$telefono_local];
    
    $mensaje_telegram = "¡Hola {$nombre}! Tienes una notificación: \n\n" . $mensaje_db;

    $api_url = "https://api.telegram.org/bot{$telegram_token}/sendMessage";

    // Estructura del Payload para Telegram
    $payload = [
        'chat_id' => $chat_id,
        'text' => $mensaje_telegram, 
        'parse_mode' => 'HTML',
    ];

    // Llamada cURL
    $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded' 
        ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $data = json_decode($response, true);

    $resultado_log = 'Error';
    $detalle_error = '';

    if ($http_code == 200 && isset($data['ok']) && $data['ok'] === true) {
        $resultado_log = 'Enviado';
    } else {
        // La lógica de diagnóstico sigue activa, aunque el envío ya funciona
        echo "\n--- FALLO DE ENVÍO DE TELEGRAM ---\n";
        echo "ID Notificación: " . $idNotificacion . "\n";
        echo "Chat ID: " . $chat_id . "\n";
        echo "HTTP Code: " . $http_code . "\n";
        echo "API Response (JSON): " . $response . "\n";
        echo "---------------------------------\n";
        
        if (isset($data['description'])) {
            $detalle_error = 'API: ' . substr($data['description'], 0, 50);
        } else {
            $detalle_error = 'HTTP:' . $http_code;
        }
        $resultado_log = 'Error: ' . $detalle_error;
    }

    $resultado_simple = ($resultado_log === 'Enviado') ? 'Enviado' : 'Error';
    
    $sql_insert_log = "
    INSERT INTO notificaciones_whatsapp (idNotificacion, resultado) 
    VALUES (?, ?)
    ";

    $stmt_insert = $pdo->prepare($sql_insert_log);
    $stmt_insert->execute([$idNotificacion, $resultado_simple]);
}
?>