<?php
// app/config/db_config.php
// Configuración de la conexión a la base de datos

// Define las constantes para la conexión a la base de datos
define('DB_HOST', 'localhost'); // O la IP de tu servidor de base de datos
define('DB_USER', 'root');     // Tu usuario de base de datos
define('DB_PASS', '');         // Tu contraseña de base de datos
define('DB_NAME', 'cursoaltura'); // El nombre de tu base de datos

// Función para establecer la conexión a la base de datos usando PDO
function connectDB() {
    try {
        // Crea una nueva instancia de PDO (PHP Data Objects) para la conexión
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        // Configura PDO para lanzar excepciones en caso de errores, lo cual es útil para la depuración
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Configura PDO para devolver los resultados como arrays asociativos por defecto
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        // En caso de error en la conexión, muestra un mensaje y termina la ejecución
        // En un entorno de producción, deberías registrar el error y mostrar un mensaje genérico.
        die("Error de conexión a la base de datos: " . $e->getMessage());
    }
}
?>