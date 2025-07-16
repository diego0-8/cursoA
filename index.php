<?php
/**
 * Controlador Frontal / Enrutador Principal
 * * Este es el único punto de entrada a la aplicación.
 * Su responsabilidad es recibir todas las peticiones, analizar la URL
 * para determinar el controlador y la acción a ejecutar, y luego
 * delegar la tarea a dicho controlador.
 */

// 1. Iniciar la sesión
// Es crucial iniciar la sesión al principio para que esté disponible en toda la aplicación.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Definir la estructura de directorios
// Se definen constantes para tener rutas claras y evitar errores.
define('CONTROLLERS_DIR', 'controller/');
define('MODELS_DIR', 'models/');
define('VIEWS_DIR', 'views/');

// 3. Configuración por defecto
// Si no se especifica nada en la URL, se cargará el controlador de login.
$controller_name = 'login';
$action_name = 'index';

// 4. Analizar la petición (URL)
// Se verifica si los parámetros 'c' (controlador) y 'a' (acción) vienen en la URL.
// strtolower() se usa para estandarizar y evitar problemas con mayúsculas/minúsculas.
if (isset($_REQUEST['c'])) {
    $controller_name = strtolower($_REQUEST['c']);
}

if (isset($_REQUEST['a'])) {
    $action_name = strtolower($_REQUEST['a']);
}

// 5. Construir y cargar el archivo del controlador
$controller_file = CONTROLLERS_DIR . 'C_' . $controller_name . '.php';

if (file_exists($controller_file)) {
    require_once $controller_file;

    // 6. Crear la instancia del controlador
    // Se formatea el nombre de la clase (ej: 'login' -> 'ControladorLogin')
    $class_name = 'Controlador' . ucfirst($controller_name);
    
    if (class_exists($class_name)) {
        $controller = new $class_name;

        // 7. Verificar y llamar a la acción (método)
        if (method_exists($controller, $action_name)) {
            // Se llama al método del controlador que corresponde a la acción.
            // Esta es la línea que ejecuta la lógica principal de la página.
            $controller->$action_name();
        } else {
            // Error si la acción no existe en la clase del controlador.
            die("Error 404: La acción '$action_name' no fue encontrada en el controlador '$class_name'.");
        }
    } else {
        // Error si el archivo del controlador existe, pero la clase no está definida dentro.
        die("Error 404: La clase '$class_name' no fue encontrada en el archivo '$controller_file'.");
    }
} else {
    // Error si el archivo del controlador no se encuentra.
    die("Error 404: El controlador '$controller_name' no fue encontrado en la ruta '$controller_file'.");
}
?>
