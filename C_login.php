<?php
class ControladorLogin {

    /**
     * Muestra la página de login y prepara los mensajes de éxito.
     */
    public function index() {
        $data = []; // Inicializamos el array de datos
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'registered') {
                $data['mensaje_exito'] = "¡Registro completado! Ya puedes iniciar sesión.";
            }
            if ($_GET['status'] === 'success') {
                $data['mensaje_exito'] = "¡Contraseña actualizada con éxito! Ya puedes iniciar sesión.";
            }
        }
        require_once 'views/V_login.php';
    }

    private function redirigirPorRol($rol) {
        switch ($rol) {
            case 'administrador':
                header('Location: index.php?c=admin');
                break;
            case 'profesor':
                header('Location: index.php?c=profesor');
                break;
            case 'estudiante':
                header('Location: index.php?c=estudiante');
                break;
            default:
                header('Location: index.php?c=login');
                break;
        }
        exit();
    }

    public function validar() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            require_once 'models/M_login.php';
            $modeloLogin = new ModeloLogin();
            $resultado = $modeloLogin->verificarUsuario($email, $password);

            if (is_array($resultado)) {
                $_SESSION['usuario_email'] = $resultado['email'];
                $_SESSION['usuario_nombre_completo'] = $resultado['nombre'] . ' ' . $resultado['apellido'];
                $_SESSION['usuario_rol'] = $resultado['rol'];
                $_SESSION['usuario_tipo_documento'] = $resultado['tipo_documento'];
                $_SESSION['usuario_numero_documento'] = $resultado['numero_documento'];
                
                $this->redirigirPorRol($resultado['rol']);

            } else {
                switch ($resultado) {
                    case 'USER_NOT_FOUND':
                        $data['error'] = "El email '{$email}' no fue encontrado.";
                        break;
                    case 'PASSWORD_INCORRECT':
                        $data['error'] = "La contraseña es incorrecta.";
                        break;
                    case 'ACCOUNT_INACTIVE':
                        $data['error'] = "Esta cuenta ha sido deshabilitada. Por favor, contacta a un administrador.";
                        break;
                    case 'ACCOUNT_NOT_ACTIVATED':
                        $data['error'] = "Tu cuenta aún no ha sido activada. Revisa tu correo para el enlace de activación.";
                        break;
                    default:
                        $data['error'] = "Error de conexión o desconocido.";
                        break;
                }
                require_once 'views/V_login.php';
            }
        } else {
            header('Location: index.php?c=login');
            exit();
        }
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: index.php?c=login');
        exit();
    }
}
?>
