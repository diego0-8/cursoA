<?php
// Sistema de envío de correos electrónicos

class EmailHelper {
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $app_name;
    
    public function __construct() {
        $this->loadEmailConfig();
    }
    
    private function loadEmailConfig() {
        $db = connectDB();
        $stmt = $db->prepare("SELECT clave, valor FROM configuraciones WHERE clave IN ('smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'app_name')");
        $stmt->execute();
        $configs = $stmt->fetchAll();
        
        foreach ($configs as $config) {
            $this->{$config['clave']} = $config['valor'];
        }
    }
    
    public function sendEmail($to, $subject, $body, $isHTML = true) {
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: " . ($isHTML ? "text/html" : "text/plain") . "; charset=UTF-8";
        $headers[] = "From: " . $this->app_name . " <" . $this->smtp_username . ">";
        $headers[] = "Reply-To: " . $this->smtp_username;
        $headers[] = "X-Mailer: PHP/" . phpversion();
        
        return mail($to, $subject, $body, implode("\r\n", $headers));
    }
    
    public function sendWelcomeEmail($email, $nombre, $password_temp, $rol) {
        $subject = "Bienvenido a " . $this->app_name;
        
        $body = "
        <html>
        <head>
            <title>Bienvenido</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { background: #6c757d; color: white; padding: 10px; text-align: center; }
                .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>¡Bienvenido a {$this->app_name}!</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    <p>Te damos la bienvenida a nuestro sistema de cursos virtuales. Has sido registrado como <strong>{$rol}</strong>.</p>
                    <p><strong>Tus credenciales de acceso:</strong></p>
                    <ul>
                        <li><strong>Email:</strong> {$email}</li>
                        <li><strong>Contraseña temporal:</strong> {$password_temp}</li>
                    </ul>
                    <p><strong>Importante:</strong> Por favor, cambia tu contraseña después del primer acceso.</p>
                    <p>
                        <a href='" . BASE_URL . "/login' class='btn'>Iniciar Sesión</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " {$this->app_name}. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    public function sendActivationEmail($email, $nombre, $token) {
        $subject = "Activa tu cuenta - " . $this->app_name;
        
        $activation_url = BASE_URL . "/activate?token=" . $token;
        
        $body = "
        <html>
        <head>
            <title>Activación de Cuenta</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { background: #6c757d; color: white; padding: 10px; text-align: center; }
                .btn { display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Activa tu cuenta</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    <p>Gracias por registrarte en {$this->app_name}. Para completar tu registro, necesitas activar tu cuenta.</p>
                    <p>Haz clic en el siguiente botón para activar tu cuenta:</p>
                    <p>
                        <a href='{$activation_url}' class='btn'>Activar Cuenta</a>
                    </p>
                    <p>Si no puedes hacer clic en el botón, copia y pega este enlace en tu navegador:</p>
                    <p><a href='{$activation_url}'>{$activation_url}</a></p>
                    <p><strong>Nota:</strong> Este enlace expirará en 24 horas.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " {$this->app_name}. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    public function sendGradeNotification($email, $nombre, $curso, $nota, $observaciones) {
        $subject = "Nueva calificación recibida - " . $this->app_name;
        
        $body = "
        <html>
        <head>
            <title>Nueva Calificación</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #17a2b8; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { background: #6c757d; color: white; padding: 10px; text-align: center; }
                .grade { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 10px 0; }
                .btn { display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Nueva Calificación</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    <p>Has recibido una nueva calificación en el curso: <strong>{$curso}</strong></p>
                    <div class='grade'>
                        <p><strong>Calificación:</strong> {$nota}/5.0</p>
                        " . ($observaciones ? "<p><strong>Observaciones:</strong> {$observaciones}</p>" : "") . "
                    </div>
                    <p>
                        <a href='" . BASE_URL . "/dashboard/estudiante' class='btn'>Ver mis Calificaciones</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " {$this->app_name}. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $this->sendEmail($email, $subject, $body);
    }
}