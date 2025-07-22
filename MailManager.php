<?php
/**
 * MailManager - Gestor Centralizado para el Envío de Correos
 * * Simula el envío de correos electrónicos para las diferentes notificaciones de la plataforma.
 * Para un entorno de producción, la función simularEnvio() debe ser reemplazada
 * por una librería real como PHPMailer o Symfony Mailer.
 */
class MailManager {

    /**
     * URL base del sitio. ¡IMPORTANTE! Cámbiala por la URL de tu sitio web en producción.
     */
    private static $baseUrl = "https://royalblue-clam-573362.hostingsite.com/"; // Reemplaza esto con tu dominio real

    /**
     * Simula el envío de un correo mostrando una caja de alerta en la sesión.
     * @param string $destinatario Email del receptor.
     * @param string $asunto Asunto del correo.
     * @param string $cuerpo Contenido HTML del correo.
     */
    private static function simularEnvio($destinatario, $asunto, $cuerpo) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['simulador_correo'] = "<div style='border: 2px dashed #007bff; padding: 15px; margin: 20px auto; max-width: 800px; font-family: sans-serif; background-color: #f8f9fa;'>
            <h3>--- SIMULADOR DE ENVÍO DE CORREO ---</h3>
            <p><strong>Para:</strong> " . htmlspecialchars($destinatario) . "</p>
            <p><strong>Asunto:</strong> " . htmlspecialchars($asunto) . "</p>
            <div><strong>Cuerpo del Mensaje:</strong><hr>" . $cuerpo . "</div>
            <p style='margin-top: 15px; font-size: 12px; color: #6c757d;'>Este mensaje se muestra aquí porque el envío de correos real está deshabilitado en este entorno.</p>
        </div>";
    }

    // --- 1. Notificaciones de Cuentas de Usuario ---

    /**
     * Envía credenciales a un nuevo usuario creado por un administrador.
     * @param string $email Email del nuevo usuario.
     * @param string $nombreCompleto Nombre completo del nuevo usuario.
     * @param string $rol Rol asignado (Estudiante, Profesor, etc.).
     * @param string $passwordTemporal La contraseña generada para el primer inicio de sesión.
     */
    public static function enviarCredencialesNuevoUsuario($email, $nombreCompleto, $rol, $passwordTemporal) {
        $url_login = self::$baseUrl . "index.php?c=login";
        $asunto = "¡Bienvenido/a a Vertical Safe!";
        $cuerpo = "<h1>¡Hola, " . htmlspecialchars($nombreCompleto) . "!</h1>
                   <p>Un administrador ha creado una cuenta para ti en nuestra plataforma con el rol de <strong>" . htmlspecialchars($rol) . "</strong>.</p>
                   <p>Tus credenciales de acceso son:</p>
                   <ul>
                       <li><strong>Usuario:</strong> " . htmlspecialchars($email) . "</li>
                       <li><strong>Contraseña Temporal:</strong> " . htmlspecialchars($passwordTemporal) . "</li>
                   </ul>
                   <p>Te recomendamos cambiar tu contraseña después de iniciar sesión por primera vez.</p>
                   <a href='{$url_login}' style='padding: 10px 15px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 5px;'>Iniciar Sesión Ahora</a>";
        self::simularEnvio($email, $asunto, $cuerpo);
    }

    /**
     * Envía un enlace para restablecer la contraseña.
     * @param string $email Email del usuario.
     * @param string $nombreCompleto Nombre del usuario.
     * @param string $token Token único para el reseteo.
     */
    public static function enviarCorreoRecuperacion($email, $nombreCompleto, $token) {
        $url_reset = self::$baseUrl . "index.php?c=usuarios&a=reset&token=" . urlencode($token);
        $asunto = "Restablecimiento de tu contraseña en Vertical Safe";
        $cuerpo = "<h1>Hola, " . htmlspecialchars($nombreCompleto) . "</h1>
                   <p>Hemos recibido una solicitud para restablecer tu contraseña. Si has sido tú, haz clic en el siguiente enlace:</p>
                   <a href='{$url_reset}' style='padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a>
                   <p>Este enlace expirará en 1 hora. Si no solicitaste esto, puedes ignorar este correo.</p>";
        self::simularEnvio($email, $asunto, $cuerpo);
    }
    
    /**
     * Envía un correo de verificación a un estudiante que se auto-registra.
     * @param string $email Email del estudiante.
     * @param string $nombreCompleto Nombre del estudiante.
     * @param string $token Token de activación.
     */
    public static function enviarConfirmacionRegistroEstudiante($email, $nombreCompleto, $token) {
        $url_activacion = self::$baseUrl . "index.php?c=usuarios&a=activar&token=" . urlencode($token);
        $asunto = "Activa tu cuenta en Vertical Safe";
        $cuerpo = "<h1>¡Bienvenido, " . htmlspecialchars($nombreCompleto) . "!</h1>
                   <p>Gracias por registrarte. Por favor, haz clic en el siguiente enlace para activar tu cuenta:</p>
                   <a href='{$url_activacion}' style='padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Activar mi Cuenta</a>
                   <p>Si no te registraste, por favor ignora este mensaje.</p>";
        self::simularEnvio($email, $asunto, $cuerpo);
    }

    /**
     * Notifica a un usuario sobre un cambio en el estado de su cuenta (activada/desactivada).
     * @param string $email Email del usuario.
     * @param string $nombreCompleto Nombre del usuario.
     * @param string $nuevoEstado 'activo' o 'inactivo'.
     */
    public static function notificarCambioEstadoCuenta($email, $nombreCompleto, $nuevoEstado) {
        if ($nuevoEstado === 'activo') {
            $asunto = "Tu cuenta en Vertical Safe ha sido reactivada";
            $cuerpo = "<h1>¡Hola, " . htmlspecialchars($nombreCompleto) . "!</h1>
                       <p>Te informamos que tu cuenta en nuestra plataforma ha sido <strong>reactivada</strong>. Ya puedes iniciar sesión con normalidad.</p>";
        } else {
            $asunto = "Notificación sobre tu cuenta en Vertical Safe";
            $cuerpo = "<h1>Hola, " . htmlspecialchars($nombreCompleto) . "</h1>
                       <p>Te informamos que tu cuenta en nuestra plataforma ha sido <strong>deshabilitada</strong> por un administrador. Si crees que es un error, por favor contacta con soporte.</p>";
        }
        self::simularEnvio($email, $asunto, $cuerpo);
    }

    // --- 2. Notificaciones de Cursos ---

    /**
     * Notifica a un profesor que se le ha asignado un nuevo curso.
     * @param string $email Email del profesor.
     * @param string $nombreProfesor Nombre del profesor.
     * @param string $nombreCurso Nombre del curso asignado.
     */
    public static function notificarAsignacionCursoProfesor($email, $nombreProfesor, $nombreCurso) {
        $url_mis_cursos = self::$baseUrl . "index.php?c=profesor&a=listarCursos";
        $asunto = "Se te ha asignado un nuevo curso: " . $nombreCurso;
        $cuerpo = "<h1>¡Hola, Prof. " . htmlspecialchars($nombreProfesor) . "!</h1>
                   <p>Te informamos que has sido asignado como instructor del curso <strong>\"" . htmlspecialchars($nombreCurso) . "\"</strong>.</p>
                   <p>Ya puedes acceder al panel de gestión para añadir módulos, clases y contenido.</p>
                   <a href='{$url_mis_cursos}' style='padding: 10px 15px; background-color: #96c11f; color: white; text-decoration: none; border-radius: 5px;'>Gestionar Mis Cursos</a>";
        self::simularEnvio($email, $asunto, $cuerpo);
    }
    
    /**
     * Notifica a un estudiante que su inscripción a un curso fue aprobada.
     * @param string $email Email del estudiante.
     * @param string $nombreEstudiante Nombre del estudiante.
     * @param string $nombreCurso Nombre del curso.
     */
    public static function notificarInscripcionAprobadaEstudiante($email, $nombreEstudiante, $nombreCurso) {
        $url_mis_cursos = self::$baseUrl . "index.php?c=estudiante&a=misCursos";
        $asunto = "¡Inscripción Aprobada! Ya puedes empezar el curso " . $nombreCurso;
        $cuerpo = "<h1>¡Felicidades, " . htmlspecialchars($nombreEstudiante) . "!</h1>
                   <p>Tu solicitud de inscripción para el curso <strong>\"" . htmlspecialchars($nombreCurso) . "\"</strong> ha sido aprobada.</p>
                   <p>Ya puedes acceder a todo el contenido y empezar tu aprendizaje.</p>
                   <a href='{$url_mis_cursos}' style='padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Ir a Mis Cursos</a>";
        self::simularEnvio($email, $asunto, $cuerpo);
    }

    /**
     * Notifica a los estudiantes inscritos sobre nuevo contenido o un nuevo curso.
     * @param string $email Email del estudiante.
     * @param string $nombreEstudiante Nombre del estudiante.
     * @param string $nombreCurso Nombre del curso.
     * @param string $mensajeAdicional Mensaje específico sobre la novedad.
     */
    public static function notificarNovedadCurso($email, $nombreEstudiante, $nombreCurso, $mensajeAdicional) {
        $url_curso = self::$baseUrl . "index.php?c=estudiante&a=misCursos"; // URL genérica a sus cursos
        $asunto = "Novedades en tu curso: " . $nombreCurso;
        $cuerpo = "<h1>¡Hola, " . htmlspecialchars($nombreEstudiante) . "!</h1>
                   <p>Hay novedades en tu curso <strong>\"" . htmlspecialchars($nombreCurso) . "\"</strong>.</p>
                   <p><strong>Detalle:</strong> " . htmlspecialchars($mensajeAdicional) . "</p>
                   <p>¡No te lo pierdas y sigue aprendiendo!</p>
                   <a href='{$url_curso}' style='padding: 10px 15px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 5px;'>Ver el Curso</a>";
        self::simularEnvio($email, $asunto, $cuerpo);
    }
}
?>

