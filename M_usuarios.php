<?php
class ModeloUsuarios {
    private $db;

    public function __construct() {
        require_once('conexion.php');
        $this->db = Conexion::conectar();
    }

    // --- MÉTODOS PARA LOGIN Y RECUPERACIÓN ---

    public function buscarUsuarioPorEmail($email) {
        $consulta = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
        $consulta->bindParam(':email', $email, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarUsuarioPorId($tipo_doc, $num_doc) {
        $consulta = $this->db->prepare("SELECT * FROM usuarios WHERE tipo_documento = :tipo_doc AND numero_documento = :num_doc");
        $consulta->execute([':tipo_doc' => $tipo_doc, ':num_doc' => $num_doc]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // --- MÉTODOS PARA REGISTRO Y TOKENS ---

    public function crearEstudianteConPassword($datos) {
        try {
            $sql = "INSERT INTO usuarios (tipo_documento, numero_documento, nombre, apellido, email, telefono, password_hash, rol, estado_cuenta, email_verificado, fecha_verificacion_email) 
                    VALUES (:tipo_documento, :numero_documento, :nombre, :apellido, :email, :telefono, :password_hash, 'estudiante', 'activo', 1, NOW())";
            
            $consulta = $this->db->prepare($sql);
            $consulta->execute([
                ':tipo_documento' => $datos['tipo_documento'],
                ':numero_documento' => $datos['numero_documento'],
                ':nombre' => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':email' => $datos['email'],
                ':telefono' => $datos['telefono'],
                ':password_hash' => $datos['password_hash']
            ]);
            return true;
        } catch (PDOException $e) {
            // Este error se registrará si el email o documento ya existen (debido a las claves únicas)
            error_log("Error al crear estudiante con contraseña: " . $e->getMessage());
            return false;
        }
    }
    public function crearToken($tipo_doc, $num_doc, $tipo_token) {
        $token = bin2hex(random_bytes(32));
        $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $sql = "INSERT INTO tokens_activacion (usuario_tipo_documento, usuario_numero_documento, token, tipo_token, fecha_expiracion)
                VALUES (:tipo_doc, :num_doc, :token, :tipo_token, :expiracion)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute(['tipo_doc' => $tipo_doc, 'num_doc' => $num_doc, 'token' => $token, 'tipo_token' => $tipo_token, 'expiracion' => $expiracion]);
        return $token;
    }

    public function validarToken($token, $tipo_token_esperado) {
        $sql = "SELECT * FROM tokens_activacion WHERE token = :token AND usado = 0 AND fecha_expiracion > NOW()";
        if ($tipo_token_esperado) {
            $sql .= " AND tipo_token = :tipo_token";
        }
        $consulta = $this->db->prepare($sql);
        $params = ['token' => $token];
        if ($tipo_token_esperado) {
            $params['tipo_token'] = $tipo_token_esperado;
        }
        $consulta->execute($params);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function marcarTokenComoUsado($token) {
        $sql = "UPDATE tokens_activacion SET usado = 1, fecha_uso = NOW() WHERE token = :token";
        $consulta = $this->db->prepare($sql);
        $consulta->bindParam(':token', $token, PDO::PARAM_STR);
        $consulta->execute();
    }

    public function activarYEstablecerPassword($tipo_doc, $num_doc, $password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password_hash = :password_hash, estado_cuenta = 'activo', email_verificado = 1, fecha_verificacion_email = NOW() 
                WHERE tipo_documento = :tipo_doc AND numero_documento = :num_doc";
        $consulta = $this->db->prepare($sql);
        $consulta->execute(['password_hash' => $password_hash, 'tipo_doc' => $tipo_doc, 'num_doc' => $num_doc]);
    }

    // --- MÉTODOS PARA GESTIÓN DE ADMIN ---

     public function contarUsuarios($busqueda = '') {
        $sql = "SELECT COUNT(*) FROM usuarios";
        $params = [];
        if (!empty($busqueda)) {
            // CAMBIO: Se añade la búsqueda por la columna 'rol'
            $sql .= " WHERE nombre LIKE :busqueda OR apellido LIKE :busqueda OR email LIKE :busqueda OR numero_documento LIKE :busqueda OR rol LIKE :busqueda";
            $params[':busqueda'] = "%{$busqueda}%";
        }
        $consulta = $this->db->prepare($sql);
        $consulta->execute($params);
        return (int)$consulta->fetchColumn();
    }

    public function getUsuariosPaginados($busqueda = '', $limite = 5, $offset = 0) {
        $sql = "SELECT * FROM usuarios";
        $params = [];
        if (!empty($busqueda)) {
            // CAMBIO: Se añade la búsqueda por la columna 'rol'
            $sql .= " WHERE nombre LIKE :busqueda OR apellido LIKE :busqueda OR email LIKE :busqueda OR numero_documento LIKE :busqueda OR rol LIKE :busqueda";
            $params[':busqueda'] = "%{$busqueda}%";
        }
        $sql .= " ORDER BY fecha_creacion DESC LIMIT :limite OFFSET :offset";
        
        $consulta = $this->db->prepare($sql);

        foreach ($params as $key => &$val) {
            $consulta->bindParam($key, $val, PDO::PARAM_STR);
        }
        $consulta->bindParam(':limite', $limite, PDO::PARAM_INT);
        $consulta->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene usuarios para un reporte, filtrando por rango de fechas y roles.
     */
    public function getUsuariosParaReporte($fecha_inicio, $fecha_fin, $roles) {
        // Asegurarse de que el array de roles no esté vacío para la consulta IN
        if (empty($roles)) {
            return [];
        }

        $sql = "SELECT * FROM usuarios WHERE 1=1";
        // CORRECCIÓN: Se usará un array para los parámetros posicionales (?)
        $params = [];

        if (!empty($fecha_inicio)) {
            // CORRECCIÓN: Se cambia el marcador nombrado por uno posicional
            $sql .= " AND DATE(fecha_creacion) >= ?";
            $params[] = $fecha_inicio;
        }

        if (!empty($fecha_fin)) {
            // CORRECCIÓN: Se cambia el marcador nombrado por uno posicional
            $sql .= " AND DATE(fecha_creacion) <= ?";
            $params[] = $fecha_fin;
        }

        // Crear placeholders para la cláusula IN (...) de roles
        $placeholders = implode(',', array_fill(0, count($roles), '?'));
        $sql .= " AND rol IN ($placeholders)";
        
        $sql .= " ORDER BY rol, fecha_creacion DESC";

        $consulta = $this->db->prepare($sql);
        
        // CORRECCIÓN: Se unen los parámetros de fecha con los de roles en un solo array
        $execute_params = array_merge($params, $roles);

        $consulta->execute($execute_params);
        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar los resultados por rol para facilitar la creación de tablas
        $usuarios_agrupados = [];
        foreach ($resultados as $usuario) {
            $usuarios_agrupados[$usuario['rol']][] = $usuario;
        }
        return $usuarios_agrupados;
    }

    public function crearUsuarioDesdeAdmin($datos) {
        try {
            // Se añade la columna 'telefono' a la consulta INSERT
            $sql = "INSERT INTO usuarios (tipo_documento, numero_documento, nombre, apellido, email, telefono, password_hash, rol, estado_cuenta, email_verificado) 
                    VALUES (:tipo_documento, :numero_documento, :nombre, :apellido, :email, :telefono, :password_hash, :rol, 'activo', 1)";
            $consulta = $this->db->prepare($sql);
            // El array $datos ya contiene el teléfono desde el controlador
            $consulta->execute($datos);
            return true;
        } catch (PDOException $e) {
            error_log("Error al crear usuario desde admin: " . $e->getMessage());
            return false;
        }
    }

    public function cambiarEstado($tipo_doc, $num_doc, $nuevo_estado) {
        try {
            $sql = "UPDATE usuarios SET estado_cuenta = :nuevo_estado WHERE tipo_documento = :tipo_doc AND numero_documento = :num_doc";
            $consulta = $this->db->prepare($sql);
            $consulta->execute([':nuevo_estado' => $nuevo_estado, ':tipo_doc' => $tipo_doc, ':num_doc' => $num_doc]);
            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de usuario: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarUsuarioDesdeAdmin($datos) {
        try {
            $sql = "UPDATE usuarios SET 
                        nombre = :nombre, 
                        apellido = :apellido, 
                        email = :email, 
                        telefono = :telefono, 
                        rol = :rol";
            
            // Añadir la actualización de contraseña solo si se proporcionó una nueva
            if (!empty($datos['password_hash'])) {
                $sql .= ", password_hash = :password_hash";
            }
            
            $sql .= " WHERE tipo_documento = :tipo_documento AND numero_documento = :numero_documento";
            
            $consulta = $this->db->prepare($sql);
            
            // Preparar los parámetros para la ejecución
            $params = [
                ':nombre' => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':email' => $datos['email'],
                ':telefono' => $datos['telefono'],
                ':rol' => $datos['rol'],
                ':tipo_documento' => $datos['tipo_documento'],
                ':numero_documento' => $datos['numero_documento']
            ];

            if (!empty($datos['password_hash'])) {
                $params[':password_hash'] = $datos['password_hash'];
            }

            $consulta->execute($params);
            return true;
        } catch (PDOException $e) {
            // Captura errores, como un email duplicado
            error_log("Error al actualizar usuario desde admin: " . $e->getMessage());
            return false;
        }
    }
}
?>