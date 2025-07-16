<?php
class ModeloEstudiante {
    private $db;

    public function __construct() {
        require_once('conexion.php');
        $this->db = Conexion::conectar();
    }

    /**
     * Obtiene todos los cursos a los que un estudiante aún no está inscrito
     * ni ha enviado una solicitud pendiente.
     */
    public function getCursosDisponibles($estudiante_documento) {
        $sql = "SELECT 
                    c.*,
                    CONCAT(u.nombre, ' ', u.apellido) as profesor_nombre_completo
                FROM cursos c
                LEFT JOIN usuarios u ON c.profesor_numero_documento = u.numero_documento
                WHERE c.activo = 1 
                AND c.id NOT IN (
                    SELECT curso_id FROM inscripciones WHERE estudiante_numero_documento = :doc
                ) 
                AND c.id NOT IN (
                    SELECT curso_id FROM solicitudes_inscripcion WHERE estudiante_numero_documento = :doc AND estado_solicitud = 'pendiente'
                )";
        
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':doc' => $estudiante_documento]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva solicitud de inscripción para un estudiante en un curso.
     */
    public function crearSolicitudInscripcion($curso_id, $tipo_doc, $num_doc) {
        try {
            $sql = "INSERT INTO solicitudes_inscripcion (curso_id, estudiante_tipo_documento, estudiante_numero_documento)
                    VALUES (:curso_id, :tipo_doc, :num_doc)";
            $consulta = $this->db->prepare($sql);
            $consulta->execute([
                ':curso_id' => $curso_id,
                ':tipo_doc' => $tipo_doc,
                ':num_doc' => $num_doc
            ]);
            return true;
        } catch (PDOException $e) {
            // Este error ocurrirá si ya existe una solicitud (por la clave única)
            error_log("Error al crear solicitud: " . $e->getMessage());
            return false;
        }
    }

    public function marcarClaseComoCompleta($clase_id, $tipo_doc, $num_doc) {
        // Usamos INSERT ... ON DUPLICATE KEY UPDATE para crear o actualizar el registro.
        // Esto evita errores si el estudiante marca la misma clase dos veces.
        // Requiere una clave UNIQUE en (estudiante_tipo_documento, estudiante_numero_documento, clase_id)
        // que ya tienes en tu tabla `progreso_estudiante`.
        $sql = "INSERT INTO progreso_estudiante (estudiante_tipo_documento, estudiante_numero_documento, clase_id, completado, fecha_completado)
                VALUES (:tipo_doc, :num_doc, :clase_id, 1, NOW())
                ON DUPLICATE KEY UPDATE completado = 1, fecha_completado = NOW()";
        
        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':tipo_doc' => $tipo_doc,
            ':num_doc' => $num_doc,
            ':clase_id' => $clase_id
        ]);
        return $consulta->rowCount() > 0;
    }
    
    public function crearCertificado($tipo_doc, $num_doc, $curso_id) {
        // Genera un código único para el certificado
        $codigo_unico = "VS-" . $curso_id . "-" . $num_doc . "-" . time();

        $sql = "INSERT INTO certificados (estudiante_tipo_documento, estudiante_numero_documento, curso_id, codigo_unico)
                VALUES (:tipo_doc, :num_doc, :curso_id, :codigo)";
        
        $consulta = $this->db->prepare($sql);
        $exito = $consulta->execute([
            ':tipo_doc' => $tipo_doc,
            ':num_doc'  => $num_doc,
            ':curso_id' => $curso_id,
            ':codigo'   => $codigo_unico
        ]);
        
        return $exito ? $codigo_unico : false;
    }
    
    public function getCertificado($tipo_doc, $num_doc, $curso_id) {
        $sql = "SELECT 
                    cert.*,
                    c.nombre as nombre_curso,
                    CONCAT(u.nombre, ' ', u.apellido) as nombre_estudiante
                FROM certificados cert
                JOIN cursos c ON cert.curso_id = c.id
                JOIN usuarios u ON cert.estudiante_numero_documento = u.numero_documento AND cert.estudiante_tipo_documento = u.tipo_documento
                WHERE cert.estudiante_tipo_documento = :tipo_doc 
                  AND cert.estudiante_numero_documento = :num_doc 
                  AND cert.curso_id = :curso_id";

        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':tipo_doc' => $tipo_doc,
            ':num_doc'  => $num_doc,
            ':curso_id' => $curso_id
        ]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function getProgresoCurso($curso_id, $tipo_doc, $num_doc) {
        // Contar todas las clases del curso
        $sql_total = "SELECT COUNT(cl.id) 
                      FROM clases cl
                      JOIN fases f ON cl.fase_id = f.id
                      WHERE f.curso_id = :curso_id";
        $consulta_total = $this->db->prepare($sql_total);
        $consulta_total->execute([':curso_id' => $curso_id]);
        $total_clases = $consulta_total->fetchColumn();

        // Contar las clases completadas por el estudiante en ese curso
        $sql_completadas = "SELECT COUNT(p.id)
                            FROM progreso_estudiante p
                            JOIN clases cl ON p.clase_id = cl.id
                            JOIN fases f ON cl.fase_id = f.id
                            WHERE f.curso_id = :curso_id 
                              AND p.estudiante_tipo_documento = :tipo_doc 
                              AND p.estudiante_numero_documento = :num_doc
                              AND p.completado = 1";
        $consulta_completadas = $this->db->prepare($sql_completadas);
        $consulta_completadas->execute([
            ':curso_id' => $curso_id,
            ':tipo_doc' => $tipo_doc,
            ':num_doc' => $num_doc
        ]);
        $clases_completadas = $consulta_completadas->fetchColumn();

        return [
            'total_clases' => (int)$total_clases,
            'clases_completadas' => (int)$clases_completadas
        ];
    }
    
      /**
     * Obtiene los cursos en los que un estudiante está inscrito.
     */
    public function getMisCursos($estudiante_documento) {
        $sql = "SELECT 
                    c.*, -- Se restaura c.* para asegurar que todas las columnas del curso se carguen
                    CONCAT(u.nombre, ' ', u.apellido) as profesor_nombre_completo
                FROM cursos c
                JOIN inscripciones i ON c.id = i.curso_id
                LEFT JOIN usuarios u ON c.profesor_numero_documento = u.numero_documento
                WHERE i.estudiante_numero_documento = :doc AND i.estado_inscripcion = 'activa'";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':doc' => $estudiante_documento]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getContenidoCurso($curso_id, $tipo_doc, $num_doc) {
    $sql_fases = "SELECT * FROM fases WHERE curso_id = :curso_id ORDER BY orden ASC";
    $consulta_fases = $this->db->prepare($sql_fases);
    $consulta_fases->execute([':curso_id' => $curso_id]);
    $fases = $consulta_fases->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fases as $key_fase => $fase) {
        // Unimos la tabla de clases con la de progreso para saber qué está completado
        $sql_clases = "SELECT 
                            c.*, 
                            p.completado 
                       FROM clases c
                       LEFT JOIN progreso_estudiante p ON c.id = p.clase_id 
                            AND p.estudiante_tipo_documento = :tipo_doc 
                            AND p.estudiante_numero_documento = :num_doc
                       WHERE c.fase_id = :fase_id 
                       ORDER BY c.orden ASC";
        
        $consulta_clases = $this->db->prepare($sql_clases);
        $consulta_clases->execute([
            ':fase_id' => $fase['id'],
            ':tipo_doc' => $tipo_doc,
            ':num_doc' => $num_doc
        ]);
        $clases = $consulta_clases->fetchAll(PDO::FETCH_ASSOC);
        
        // Bucle para obtener los recursos de cada clase
        foreach ($clases as $key_clase => $clase) {
            $clases[$key_clase]['recursos'] = $this->getRecursosPorClase($clase['id']);
        }
        $fases[$key_fase]['clases'] = $clases;
    }
    return $fases;
}

    
    public function getRecursosPorClase($clase_id) {
        $consulta = $this->db->prepare("SELECT * FROM recursos_clase WHERE clase_id = :clase_id ORDER BY fecha_subida ASC");
        $consulta->execute([':clase_id' => $clase_id]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Reutilizado: Obtiene los datos de un único curso por su ID.
     */
    public function getCursoPorId($curso_id) {
        $consulta = $this->db->prepare("SELECT * FROM cursos WHERE id = :id");
        $consulta->execute([':id' => $curso_id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
}
?>