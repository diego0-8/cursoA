<?php
class ControladorVerUsuarios {

    public function __construct() {
        // --- PROTECCIÓN DE RUTA ---
        // Al construir el controlador, verificamos si el usuario ha iniciado sesión.
        // session_start() ya fue llamado en index.php
        if (!isset($_SESSION['usuario_id'])) {
            // Si no hay sesión, lo redirigimos a la página de login
            header('Location: index.php?c=login');
            exit(); // Es importante usar exit() después de una redirección
        }
    }

    public function index() {
        // Como la verificación ya se hizo en el constructor,
        // si el código llega aquí, el usuario está autenticado.
        
        // Aquí iría tu lógica actual para obtener y mostrar los usuarios.
        // Por ejemplo:
        // require_once 'models/M_verUsuarios.php';
        // $modelo = new ModeloVerUsuarios();
        // $data['usuarios'] = $modelo->getUsuarios();

        // Simulación de datos para el ejemplo
        $data['titulo'] = "Lista de Usuarios";
        $data['nombre_usuario'] = $_SESSION['usuario_nombre']; // Obtenemos el nombre de la sesión
        $data['usuarios'] = [
            ['id' => 1, 'nombre' => 'Juan Perez', 'usuario' => 'jperez'],
            ['id' => 2, 'nombre' => 'Ana Gomez', 'usuario' => 'agomez'],
        ];

        // Cargar la vista para mostrar los usuarios
        require_once 'views/V_verUsuarios.php';
    }
}
?>