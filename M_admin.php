<?php
class ModeloAdmin {
    private $db;

    public function __construct() {
        require_once('conexion.php');
        $this->db = Conexion::conectar();
    }

    public function contarCursos($busqueda = '') {
        $sql = "SELECT COUNT(*) FROM cursos WHERE activo = 1";
        $params = [];
        if (!empty($busqueda)) {
            $sql .= " AND nombre LIKE :busqueda";
            $params[':busqueda'] = "%{$busqueda}%";
        }
        $consulta = $this->db->prepare($sql);
        $consulta->execute($params);
        return (int)$consulta->fetchColumn();
    }

    /**
     * CORRECCIÓN FINAL: La consulta ahora es más simple y correcta.
     * Une 'cursos' directamente con 'usuarios' para obtener el profesor.
     */
    public function getCursosPaginados($busqueda = '', $limite = 10, $offset = 0) {
        $sql = "SELECT
                    c.*,
                    CONCAT(u.nombre, ' ', u.apellido) as profesor_nombre_completo,
                    (SELECT COUNT(DISTINCT i.estudiante_numero_documento) FROM inscripciones i WHERE i.curso_id = c.id AND i.estado_inscripcion = 'activa') as total_estudiantes
                FROM cursos c
                LEFT JOIN usuarios u ON c.profesor_numero_documento = u.numero_documento
                WHERE c.activo = 1";

        $params = [];
        if (!empty($busqueda)) {
            $sql .= " AND c.nombre LIKE :busqueda";
            $params[':busqueda'] = "%{$busqueda}%";
        }

        $sql .= " ORDER BY c.nombre ASC LIMIT :limite OFFSET :offset";

        $consulta = $this->db->prepare($sql);

        foreach ($params as $key => &$val) {
            $consulta->bindParam($key, $val, PDO::PARAM_STR);
        }
        $consulta->bindParam(':limite', $limite, PDO::PARAM_INT);
        $consulta->bindParam(':offset', $offset, PDO::PARAM_INT);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getEstadisticasDashboard() {
        $estadisticas = [];

        // 1. Contar total de usuarios
        $consulta_usuarios = $this->db->query("SELECT COUNT(*) FROM usuarios");
        $estadisticas['total_usuarios'] = $consulta_usuarios->fetchColumn();

        // 2. Contar cursos activos
        $consulta_cursos = $this->db->query("SELECT COUNT(*) FROM cursos WHERE activo = 1");
        $estadisticas['cursos_activos'] = $consulta_cursos->fetchColumn();

        // 3. Contar inscripciones pendientes
        $consulta_pendientes = $this->db->query("SELECT COUNT(*) FROM solicitudes_inscripcion WHERE estado_solicitud = 'pendiente'");
        $estadisticas['inscripciones_pendientes'] = $consulta_pendientes->fetchColumn();

        return $estadisticas;
    }

    public function crearCurso($datos) {
        try {
            // Asigna el profesor directamente al crear el curso
            $sql = "INSERT INTO cursos (nombre, descripcion, profesor_tipo_documento, profesor_numero_documento) VALUES (:nombre, :descripcion, :tipo_doc, :num_doc)";
            $consulta = $this->db->prepare($sql);
            $consulta->execute([
                ':nombre' => $datos['nombre'],
                ':descripcion' => $datos['descripcion'],
                ':tipo_doc' => $datos['profesor_tipo_documento'],
                ':num_doc' => $datos['profesor_numero_documento']
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al crear curso: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * CORREGIDO: Ahora actualiza el nombre, la descripción y el profesor del curso.
     */
    public function actualizarCurso($datos) {
        try {
            $sql = "UPDATE cursos SET 
                        nombre = :nombre, 
                        descripcion = :descripcion, 
                        profesor_tipo_documento = :tipo_doc, 
                        profesor_numero_documento = :num_doc 
                    WHERE id = :id";
            $consulta = $this->db->prepare($sql);
            $consulta->execute([
                ':nombre' => $datos['nombre'],
                ':descripcion' => $datos['descripcion'],
                ':tipo_doc' => $datos['profesor_tipo_documento'],
                ':num_doc' => $datos['profesor_numero_documento'],
                ':id' => $datos['id']
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar curso: " . $e->getMessage());
            return false;
        }
    }

    public function getAllProfesores() {
        $consulta = $this->db->prepare("SELECT tipo_documento, numero_documento, nombre, apellido FROM usuarios WHERE rol = 'profesor' AND estado_cuenta = 'activo'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProfesorPorDocumento($numero_documento) {
        $consulta = $this->db->prepare("SELECT tipo_documento FROM usuarios WHERE numero_documento = :num_doc AND rol = 'profesor'");
        $consulta->execute([':num_doc' => $numero_documento]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function asignarProfesorACurso($curso_id, $profesor_tipo_doc, $profesor_num_doc) {
        try {
            $sql = "UPDATE cursos 
                    SET profesor_tipo_documento = :tipo_doc, profesor_numero_documento = :num_doc
                    WHERE id = :curso_id";
            
            $consulta = $this->db->prepare($sql);
            $consulta->execute([
                ':tipo_doc' => $profesor_tipo_doc,
                ':num_doc' => $profesor_num_doc,
                ':curso_id' => $curso_id
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en asignarProfesorACurso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si un curso tiene estudiantes inscritos.
     * Es un paso previo para saber si se puede eliminar o solo inactivar.
     */
    public function verificarDependenciasCurso($curso_id) {
        $sql = "SELECT COUNT(*) FROM inscripciones WHERE curso_id = :curso_id";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':curso_id' => $curso_id]);
        return (int)$consulta->fetchColumn();
    }

    /**
     * Elimina un curso si no tiene dependencias (estudiantes), 
     * de lo contrario, lo inhabilita (cambia su estado a inactivo).
     */
    public function eliminarOInhabilitarCurso($curso_id) {
        $dependencias = $this->verificarDependenciasCurso($curso_id);

        if ($dependencias > 0) {
            // Si hay estudiantes, solo se inhabilita (soft delete)
            $sql = "UPDATE cursos SET activo = 0 WHERE id = :curso_id";
            $mensaje = 'inhabilitado';
        } else {
            // Si no hay estudiantes, se elimina permanentemente (hard delete)
            $sql = "DELETE FROM cursos WHERE id = :curso_id";
            $mensaje = 'eliminado';
        }

        try {
            $consulta = $this->db->prepare($sql);
            $consulta->execute([':curso_id' => $curso_id]);
            return $mensaje; // Devuelve la acción que se realizó
        } catch (PDOException $e) {
            error_log("Error en eliminarOInhabilitarCurso: " . $e->getMessage());
            return 'error'; // Devuelve 'error' si algo falla
        }
    }

     /**
     * NUEVO: Obtiene todas las solicitudes de inscripción pendientes.
     */
    public function getSolicitudesPendientes() {
        $sql = "SELECT 
                    s.*,
                    CONCAT(u.nombre, ' ', u.apellido) as nombre_estudiante,
                    u.email as email_estudiante,
                    c.nombre as nombre_curso
                FROM solicitudes_inscripcion s
                JOIN usuarios u ON s.estudiante_numero_documento = u.numero_documento
                JOIN cursos c ON s.curso_id = c.id
                WHERE s.estado_solicitud = 'pendiente'
                ORDER BY s.fecha_solicitud ASC";
        $consulta = $this->db->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * NUEVO: Procesa una solicitud de inscripción (aprobar o rechazar).
     */
    public function procesarSolicitud($solicitud_id, $accion, $admin_tipo_doc, $admin_num_doc) {
        $this->db->beginTransaction();
        try {
            // 1. Obtener datos de la solicitud
            $sql_solicitud = "SELECT * FROM solicitudes_inscripcion WHERE id = :id AND estado_solicitud = 'pendiente'";
            $consulta_solicitud = $this->db->prepare($sql_solicitud);
            $consulta_solicitud->execute([':id' => $solicitud_id]);
            $solicitud = $consulta_solicitud->fetch(PDO::FETCH_ASSOC);

            if (!$solicitud) {
                $this->db->rollBack();
                return false; // La solicitud no existe o ya fue procesada
            }

            // 2. Actualizar el estado de la solicitud
            $nuevo_estado = ($accion == 'aprobar') ? 'aprobada' : 'rechazada';
            $sql_update = "UPDATE solicitudes_inscripcion 
                           SET estado_solicitud = :estado, 
                               revisado_por_tipo_documento = :admin_tipo, 
                               revisado_por_numero_documento = :admin_num, 
                               fecha_revision = NOW() 
                           WHERE id = :id";
            $consulta_update = $this->db->prepare($sql_update);
            $consulta_update->execute([
                ':estado' => $nuevo_estado,
                ':admin_tipo' => $admin_tipo_doc,
                ':admin_num' => $admin_num_doc,
                ':id' => $solicitud_id
            ]);

            // 3. Si se aprueba, insertar en la tabla de inscripciones
            if ($accion == 'aprobar') {
                $sql_insert = "INSERT INTO inscripciones (estudiante_tipo_documento, estudiante_numero_documento, curso_id, aprobado_por_tipo_documento, aprobado_por_numero_documento, fecha_aprobacion)
                               VALUES (:est_tipo, :est_num, :curso_id, :admin_tipo, :admin_num, NOW())";
                $consulta_insert = $this->db->prepare($sql_insert);
                $consulta_insert->execute([
                    ':est_tipo' => $solicitud['estudiante_tipo_documento'],
                    ':est_num' => $solicitud['estudiante_numero_documento'],
                    ':curso_id' => $solicitud['curso_id'],
                    ':admin_tipo' => $admin_tipo_doc,
                    ':admin_num' => $admin_num_doc
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en procesarSolicitud: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los datos de un único curso por su ID.
     * Esta función es necesaria para la ventana modal de edición.
     */
    public function getCursoPorId($curso_id) {
        $consulta = $this->db->prepare("SELECT * FROM cursos WHERE id = :id");
        $consulta->execute([':id' => $curso_id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // --- Funciones para Gestión de Estudiantes en Cursos ---

    public function contarEstudiantesPorCurso($curso_id, $busqueda = '', $filtro_estado = '') {
        $sql = "SELECT COUNT(*) 
                FROM inscripciones i
                JOIN usuarios u ON i.estudiante_numero_documento = u.numero_documento
                WHERE i.curso_id = :curso_id";
        
        $params = [':curso_id' => $curso_id];

        if (!empty($busqueda)) {
            $sql .= " AND (u.nombre LIKE :busqueda OR u.apellido LIKE :busqueda OR u.email LIKE :busqueda)";
            $params[':busqueda'] = "%{$busqueda}%";
        }
        if (!empty($filtro_estado)) {
            $sql .= " AND i.estado_inscripcion = :estado";
            $params[':estado'] = $filtro_estado;
        }

        $consulta = $this->db->prepare($sql);
        $consulta->execute($params);
        return (int)$consulta->fetchColumn();
    }

    public function getEstudiantesPorCursoPaginados($curso_id, $busqueda = '', $filtro_estado = '', $limite = 10, $offset = 0) {
        $sql = "SELECT 
                    u.numero_documento,
                    u.tipo_documento,
                    CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                    u.email,
                    i.fecha_inscripcion,
                    i.estado_inscripcion
                FROM inscripciones i
                JOIN usuarios u ON i.estudiante_numero_documento = u.numero_documento
                WHERE i.curso_id = :curso_id";

        $params = [':curso_id' => $curso_id];

        if (!empty($busqueda)) {
            $sql .= " AND (u.nombre LIKE :busqueda OR u.apellido LIKE :busqueda OR u.email LIKE :busqueda)";
            $params[':busqueda'] = "%{$busqueda}%";
        }
        if (!empty($filtro_estado)) {
            $sql .= " AND i.estado_inscripcion = :estado";
            $params[':estado'] = $filtro_estado;
        }

        $sql .= " ORDER BY u.apellido, u.nombre ASC LIMIT :limite OFFSET :offset";
        
        $consulta = $this->db->prepare($sql);
        
        foreach ($params as $key => &$val) {
            $consulta->bindParam($key, $val);
        }
        $consulta->bindParam(':limite', $limite, PDO::PARAM_INT);
        $consulta->bindParam(':offset', $offset, PDO::PARAM_INT);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aplicarAccionMasiva($curso_id, $estudiantes, $accion) {
        if (empty($estudiantes)) {
            return 0; // No se seleccionaron estudiantes
        }

        // Prepara los placeholders para la cláusula IN (...)
        $placeholders = implode(',', array_fill(0, count($estudiantes), '?'));

        switch ($accion) {
            case 'activar':
                // Cambia el estado a 'activa' en la tabla de inscripciones
                $sql = "UPDATE inscripciones SET estado_inscripcion = 'activa' WHERE curso_id = ? AND estudiante_numero_documento IN ($placeholders)";
                break;
            case 'suspender':
                // Cambia el estado a 'suspendida'
                $sql = "UPDATE inscripciones SET estado_inscripcion = 'suspendida' WHERE curso_id = ? AND estudiante_numero_documento IN ($placeholders)";
                break;
            case 'eliminar':
                // Elimina la inscripción del estudiante en el curso
                $sql = "DELETE FROM inscripciones WHERE curso_id = ? AND estudiante_numero_documento IN ($placeholders)";
                break;
            default:
                return 0; // Acción no válida
        }

        try {
            $params = array_merge([$curso_id], $estudiantes);
            $consulta = $this->db->prepare($sql);
            $consulta->execute($params);
            return $consulta->rowCount(); // Devuelve el número de filas afectadas
        } catch (PDOException $e) {
            error_log("Error en aplicarAccionMasiva: " . $e->getMessage());
            return false;
        }
    }
}
?>