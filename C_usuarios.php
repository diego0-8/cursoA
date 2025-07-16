<?php
require_once 'models/M_usuarios.php';
// En un proyecto real, usarías una librería como PHPMailer. Aquí simularemos el envío.
require_once 'utils/MailManager.php'; 

class ControladorUsuarios {

    // Muestra el formulario de registro para estudiantes
    public function registro() {
        require_once 'views/V_registro.php';
    }

    // Procesa el alta de un nuevo estudiante
     public function registrarEstudiante() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Validar que las contraseñas coincidan
            $password = $_POST['password'];
            $confirmar_password = $_POST['confirmar_password'];

            if ($password !== $confirmar_password) {
                $data['error'] = "Las contraseñas no coinciden. Por favor, inténtalo de nuevo.";
                require_once 'views/V_registro.php';
                return;
            }

            // 2. Recopilar los datos del formulario
            $datos = [
                'tipo_documento' => $_POST['tipo_documento'],
                'numero_documento' => trim($_POST['numero_documento']),
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'email' => trim($_POST['email']),
                'telefono' => !empty($_POST['telefono']) ? trim($_POST['telefono']) : null,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT)
            ];

            // 3. Llamar al modelo para crear el usuario
            $modelo = new ModeloUsuarios();
            $exito = $modelo->crearEstudianteConPassword($datos);

            // 4. Redirigir según el resultado
            if ($exito) {
                // Si el registro es exitoso, redirigir al login con un mensaje de éxito
                header('Location: index.php?c=login&status=registered');
                exit();
            } else {
                // Si falla (ej: email duplicado), mostrar error en la vista de registro
                $data['error'] = "No se pudo crear el usuario. El email o número de documento ya podría existir en el sistema.";
                require_once 'views/V_registro.php';
            }
        }
    }
    // Muestra el formulario para solicitar recuperación de contraseña
    public function recuperar() {
        require_once 'views/V_recuperar.php';
    }
    
    // Procesa la solicitud de recuperación
    public function solicitarRecuperacion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $modelo = new ModeloUsuarios();
            $usuario = $modelo->buscarUsuarioPorEmail($email);

            if ($usuario) {
                // Generar token y enviar correo
                $token = $modelo->crearToken($usuario['tipo_documento'], $usuario['numero_documento'], 'recuperacion_password');
                MailManager::enviarCorreoRecuperacion($email, $token);
            }

            // Siempre mostramos el mismo mensaje para no revelar si un email existe o no
            $data['mensaje'] = "Si tu email está en nuestro sistema, recibirás un enlace para recuperar tu contraseña.";
            require_once 'views/V_recuperar.php';
        }
    }

    // Muestra el formulario para establecer una nueva contraseña
    public function reset() {
        $token = $_GET['token'] ?? '';
        $modelo = new ModeloUsuarios();
        $tokenValido = $modelo->validarToken($token, 'recuperacion_password');

        if ($tokenValido) {
            $data['token'] = $token;
            require_once 'views/V_reset_password.php';
        } else {
            die("Token no válido o expirado.");
        }
    }

    // Activa la cuenta del estudiante y le permite poner su contraseña
    public function activar() {
        $token = $_GET['token'] ?? '';
        $modelo = new ModeloUsuarios();
        $tokenValido = $modelo->validarToken($token, 'activacion_cuenta');

        if ($tokenValido) {
            $data['token'] = $token;
            // Usamos la misma vista de reset, pero el controlador sabe que es para activación
            require_once 'views/V_reset_password.php';
        } else {
            die("Token de activación no válido o expirado.");
        }
    }

    // Guarda la nueva contraseña (para reset o activación)
    public function establecerPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $token = $_POST['token'];
            $password = $_POST['password'];
            $confirmar_password = $_POST['confirmar_password'];

            if ($password !== $confirmar_password) {
                $data['error'] = "Las contraseñas no coinciden.";
                $data['token'] = $token;
                require_once 'views/V_reset_password.php';
                return;
            }

            $modelo = new ModeloUsuarios();
            $tokenData = $modelo->validarToken($token, null); // Validar cualquier tipo de token

            if ($tokenData) {
                $tipo_documento = $tokenData['usuario_tipo_documento'];
                $numero_documento = $tokenData['usuario_numero_documento'];
                
                // Actualizar la contraseña y activar la cuenta si es necesario
                $modelo->activarYEstablecerPassword($tipo_documento, $numero_documento, $password);
                
                // Marcar el token como usado
                $modelo->marcarTokenComoUsado($token);

                // Redirigir al login con mensaje de éxito
                header('Location: index.php?c=login&a=index&status=success');
                exit();
            } else {
                die("Token no válido o expirado.");
            }
        }
    }

    
}
?>
