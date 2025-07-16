<?php
class ModeloLogin {
    private $db;

    public function __construct() {
        require_once('conexion.php');
        $this->db = Conexion::conectar();
    }

    public function verificarUsuario($email, $password) {
        try {
            $consulta = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
            $consulta->bindParam(':email', $email, PDO::PARAM_STR);
            $consulta->execute();

            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            if (!$resultado) {
                return 'USER_NOT_FOUND'; // Email no encontrado
            }

            // --- ¡NUEVA VERIFICACIÓN DE ESTADO! ---
            // Si la cuenta está inactiva, no se permite el login.
            if ($resultado['estado_cuenta'] === 'inactivo') {
                return 'ACCOUNT_INACTIVE';
            }
            // Si la cuenta aún no ha sido activada por el estudiante.
            if ($resultado['estado_cuenta'] === 'pendiente_activacion') {
                return 'ACCOUNT_NOT_ACTIVATED';
            }
            // --- FIN DE LA VERIFICACIÓN ---

            if (password_verify($password, $resultado['password_hash'])) {
                return $resultado; // ¡Éxito!
            } else {
                return 'PASSWORD_INCORRECT'; // Contraseña incorrecta
            }

        } catch (PDOException $e) {
            error_log("Error en M_login->verificarUsuario: " . $e->getMessage());
            return 'DB_ERROR';
        }
    }
}
?>