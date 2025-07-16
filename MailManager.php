<?php
// En un entorno real, aquí usarías una librería como PHPMailer o Symfony Mailer.
// Para este ejemplo, simularemos el envío de correos guardando el mensaje en la sesión.

class MailManager {

    private static function simularEnvio($destinatario, $asunto, $cuerpo) {
        // Guardamos el correo simulado en la sesión para mostrarlo después de la redirección.
        $_SESSION['simulador_correo'] = "<div style='border: 2px dashed #007bff; padding: 15px; margin: 20px auto; max-width: 800px; font-family: sans-serif; background-color: #f8f9fa;'>
            <h3>--- SIMULADOR DE ENVÍO DE CORREO ---</h3>
            <p><strong>Para:</strong> " . htmlspecialchars($destinatario) . "</p>
            <p><strong>Asunto:</strong> " . htmlspecialchars($asunto) . "</p>
            <div><strong>Cuerpo del Mensaje:</strong><hr>" . $cuerpo . "</div>
            <p style='margin-top: 15px; font-size: 12px; color: #6c757d;'>Este mensaje se muestra aquí porque el envío de correos real está deshabilitado en este entorno.</p>
        </div>";
    }

    public static function enviarCorreoVerificacion($email_destinatario, $token) {
        $url_activacion = "http://localhost/mvc_tutorial/index.php?c=usuarios&a=activar&token=" . urlencode($token);
        
        $asunto = "Activa tu cuenta en nuestra plataforma";
        $cuerpo = "<h1>¡Bienvenido!</h1><p>Gracias por registrarte. Por favor, haz clic en el siguiente enlace para activar tu cuenta y establecer tu contraseña:</p><a href='{$url_activacion}' style='padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Activar mi Cuenta</a><p>Si no te registraste, por favor ignora este mensaje.</p>";

        self::simularEnvio($email_destinatario, $asunto, $cuerpo);
    }

    public static function enviarCorreoRecuperacion($email_destinatario, $token) {
        $url_reset = "http://localhost/mvc_tutorial/index.php?c=usuarios&a=reset&token=" . urlencode($token);

        $asunto = "Recuperación de contraseña";
        $cuerpo = "<h1>Solicitud de cambio de contraseña</h1><p>Hemos recibido una solicitud para restablecer tu contraseña. Si has sido tú, haz clic en el siguiente enlace:</p><a href='{$url_reset}' style='padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a><p>Este enlace expirará en 1 hora. Si no solicitaste esto, puedes ignorar este correo de forma segura.</p>";

        self::simularEnvio($email_destinatario, $asunto, $cuerpo);
    }

    /**
     * ¡ESTA ES LA FUNCIÓN QUE FALTABA!
     * Envía las credenciales a un usuario creado por un admin.
     */
    public static function enviarCorreoCredencialesAdmin($email_destinatario, $password_temporal) {
        $url_login = "http://localhost/mvc_tutorial/index.php?c=login";

        $asunto = "Has sido invitado a nuestra plataforma";
        $cuerpo = "<h1>¡Bienvenido a bordo!</h1>";
        $cuerpo .= "<p>Un administrador ha creado una cuenta para ti en nuestra plataforma. Tus credenciales de acceso son:</p>";
        $cuerpo .= "<ul><li><strong>Email:</strong> " . htmlspecialchars($email_destinatario) . "</li><li><strong>Contraseña Temporal:</strong> " . htmlspecialchars($password_temporal) . "</li></ul>";
        $cuerpo .= "<p>Te recomendamos encarecidamente que cambies tu contraseña después de iniciar sesión por primera vez.</p>";
        $cuerpo .= "<a href='{$url_login}' style='padding: 10px 15px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 5px;'>Iniciar Sesión Ahora</a>";

        self::simularEnvio($email_destinatario, $asunto, $cuerpo);
    }
}
?>

