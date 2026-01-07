<?php
// Incluir el archivo de configuración para otener las constantes de conexión
require_once(__DIR__.'/../config.php');

// Clase para manejar la conexión a la base de datos utilizando PDO
class Conection {
    // Atributo privado para almacenar la instancia de PDO
    private $DDBB;

    // Constructor para inicializr la conexión a la base de datos
    public function __construct(){
        try {
            // Configuración de la conexión a la base de datos
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

            // Creación de la instancia de PDO
            $this->DDBB = new PDO($dsn, DB_USER, DB_PASS);

            // Configuración de atributos de PDO
            $this->DDBB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Establecer el modo de obtención de datos por defecto a asociativo
            $this->DDBB->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Captura de excepciones en caso de error de conexión
        } catch (PDOException $e) {
            echo "Error al conectar a la Base de Datos: (" . $e->getMessage() . ")";
        }
    }

    // Método público para obtener la instancia de PDO
    public function getConection(){
        return $this->DDBB;
    }
}
?>