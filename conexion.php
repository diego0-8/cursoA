<?php
class Conexion {
    public static function conectar() {
        // --- CONFIGURACIÓN DE LA BASE DE DATOS ---
        // Revisa que estos datos sean correctos para tu XAMPP.
        
        $host = 'localhost';        // Generalmente es 'localhost'
        $dbname = 'cursoaltura';    // El nombre de tu base de datos
        $user = 'root';             // El usuario por defecto de XAMPP es 'root'
        $password = '';             // La contraseña por defecto de XAMPP es vacía ''
        
        // -----------------------------------------

        try {
            // Crear una nueva conexión PDO
            $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
            
            // Configurar PDO para que lance excepciones en caso de error
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $conexion;

        } catch (PDOException $e) {
            // Si la conexión falla, se mostrará el error "Error de conexión o desconocido" en el login.
            // También se registrará el error real en los logs del servidor para depuración.
            error_log("¡Error de conexión!: " . $e->getMessage());
            
            // Terminamos el script para evitar más errores.
            // En un entorno de producción, manejarías esto de forma más elegante.
            die("No se pudo conectar a la base de datos. Revisa la configuración en models/conexion.php");
        }
    }
}
?>
