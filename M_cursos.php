<?php
class ModeloCursos {
    private $db;

    public function __construct() {
        // Asegura que la conexión a la base de datos esté disponible
        require_once('conexion.php');
        $this->db = Conexion::conectar();
    }

    /**
     * MÉTODO CORREGIDO Y MEJORADO:
     * Obtiene los cursos de un profesor y, además, cuenta las fases y clases de cada uno.
     * Esto soluciona el error en la vista V_profesor_cursos.php.
     */
    public function getCursosPorProfesor($profesor_num_doc) {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM fases f WHERE f.curso_id = c.id) as total_fases,
                (SELECT COUNT(*) FROM clases cl JOIN fases f ON cl.fase_id = f.id WHERE f.curso_id = c.id) as total_clases,
                (SELECT COUNT(*) FROM inscripciones i WHERE i.curso_id = c.id AND i.estado_inscripcion = 'activa') as total_estudiantes,
                
                -- NUEVO: Subconsulta para contar entregas sin calificar
                (SELECT COUNT(ep.id)
                 FROM entregas_proyecto ep
                 JOIN proyectos p ON ep.proyecto_id = p.id
                 JOIN clases cl ON p.clase_id = cl.id
                 JOIN fases f ON cl.fase_id = f.id
                 WHERE f.curso_id = c.id AND ep.id NOT IN (SELECT cal.entrega_id FROM calificaciones cal)) as total_entregas

                FROM cursos c
                WHERE c.profesor_numero_documento = :num_doc AND c.activo = 1";
        
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':num_doc' => $profesor_num_doc]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getEstudiantesConProgresoPorCurso($curso_id) {
        // Primero, obtenemos el número total de clases en el curso
        $sql_total = "SELECT COUNT(id) FROM clases WHERE fase_id IN (SELECT id FROM fases WHERE curso_id = :curso_id)";
        $consulta_total = $this->db->prepare($sql_total);
        $consulta_total->execute([':curso_id' => $curso_id]);
        $total_clases = $consulta_total->fetchColumn();

        if ($total_clases == 0) {
            return []; // Si no hay clases, no hay progreso que calcular
        }

        // Obtenemos los estudiantes inscritos y contamos sus clases completadas
        $sql = "SELECT 
                    u.tipo_documento,
                    u.numero_documento,
                    CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                    (
                        SELECT COUNT(pe.id)
                        FROM progreso_estudiante pe
                        JOIN clases cl ON pe.clase_id = cl.id
                        JOIN fases f ON cl.fase_id = f.id
                        WHERE f.curso_id = i.curso_id 
                          AND pe.estudiante_numero_documento = i.estudiante_numero_documento
                          AND pe.completado = 1
                    ) as clases_completadas
                FROM inscripciones i
                JOIN usuarios u ON i.estudiante_numero_documento = u.numero_documento AND i.estudiante_tipo_documento = u.tipo_documento
                WHERE i.curso_id = :curso_id AND i.estado_inscripcion = 'activa'
                ORDER BY u.apellido, u.nombre";
        
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':curso_id' => $curso_id]);
        $estudiantes = $consulta->fetchAll(PDO::FETCH_ASSOC);

        // Calculamos el porcentaje para cada estudiante
        foreach ($estudiantes as $key => $estudiante) {
            $porcentaje = ($estudiante['clases_completadas'] / $total_clases) * 100;
            $estudiantes[$key]['progreso_porcentaje'] = round($porcentaje);
        }

        return $estudiantes;
    }

    public function getEstudiantesInscritos($curso_id) {
        $sql = "SELECT 
                    CONCAT(u.nombre, ' ', u.apellido) as nombre_completo, 
                    u.numero_documento, 
                    u.email, 
                    i.fecha_inscripcion, 
                    i.estado_inscripcion 
                FROM inscripciones i
                JOIN usuarios u ON i.estudiante_numero_documento = u.numero_documento
                WHERE i.curso_id = :curso_id AND i.estado_inscripcion = 'activa'
                ORDER BY u.apellido, u.nombre";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':curso_id' => $curso_id]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearModulo($curso_id, $nombre_modulo) {
        $sql_orden = "SELECT MAX(orden) as max_orden FROM fases WHERE curso_id = :curso_id";
        $consulta_orden = $this->db->prepare($sql_orden);
        $consulta_orden->execute([':curso_id' => $curso_id]);
        $nuevo_orden = ($consulta_orden->fetchColumn() ?? 0) + 1;

        $sql = "INSERT INTO fases (curso_id, nombre, orden) VALUES (:curso_id, :nombre, :orden)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':curso_id' => $curso_id, ':nombre' => $nombre_modulo, ':orden' => $nuevo_orden]);
    }

    public function actualizarEnlaceEvaluacion($curso_id, $enlace) {
    try {
        $sql = "UPDATE cursos SET enlace_evaluacion = :enlace WHERE id = :curso_id";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':enlace' => $enlace, ':curso_id' => $curso_id]);
        return true;
    } catch (PDOException $e) {
        error_log("Error al actualizar enlace: " . $e->getMessage());
        return false;
    }
}

     /**
     * NUEVO: Obtiene las entregas de un curso, agrupadas por fase y clase.
     */
    public function getEntregasParaCalificar($curso_id) {
        $sql = "SELECT 
                    e.id, e.ruta_archivo, e.nombre_original,
                    CONCAT(u.nombre, ' ', u.apellido) as nombre_estudiante,
                    f.nombre as nombre_fase,
                    cl.titulo as nombre_clase,
                    cal.nota
                FROM entregas_proyecto e
                JOIN usuarios u ON e.estudiante_numero_documento = u.numero_documento
                JOIN proyectos p ON e.proyecto_id = p.id
                JOIN clases cl ON p.clase_id = cl.id
                JOIN fases f ON cl.fase_id = f.id
                LEFT JOIN calificaciones cal ON e.id = cal.entrega_id
                WHERE f.curso_id = :curso_id
                ORDER BY f.orden, cl.orden, u.apellido";
        
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':curso_id' => $curso_id]);
        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar resultados
        $entregas_agrupadas = [];
        foreach ($resultados as $row) {
            $entregas_agrupadas[$row['nombre_fase']][$row['nombre_clase']][] = $row;
        }
        return $entregas_agrupadas;
    }

     /**
     * MODIFICADO: Ahora también obtiene los recursos de cada clase.
     */
     public function getFasesPorCurso($curso_id) {
        $sql_fases = "SELECT * FROM fases WHERE curso_id = :curso_id ORDER BY orden ASC";
        $consulta_fases = $this->db->prepare($sql_fases);
        $consulta_fases->execute([':curso_id' => $curso_id]);
        $fases = $consulta_fases->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fases as $key_fase => $fase) {
            $sql_clases = "SELECT * FROM clases WHERE fase_id = :fase_id ORDER BY orden ASC";
            $consulta_clases = $this->db->prepare($sql_clases);
            $consulta_clases->execute([':fase_id' => $fase['id']]);
            $clases = $consulta_clases->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($clases as $key_clase => $clase) {
                $clases[$key_clase]['recursos'] = $this->getRecursosPorClase($clase['id']);
            }
            $fases[$key_fase]['clases'] = $clases;
        }
        return $fases;
    }
    /**
     * NUEVO: Inserta un nuevo recurso en la base de datos.
     */
    public function insertarRecurso($datos) {
        $sql = "INSERT INTO recursos_clase (clase_id, nombre_archivo, nombre_original, tipo_archivo, tamano_archivo, ruta_archivo, tipo_recurso, descripcion)
                VALUES (:clase_id, :nombre_archivo, :nombre_original, :tipo_archivo, :tamano_archivo, :ruta_archivo, :tipo_recurso, :descripcion)";
        
        try {
            $consulta = $this->db->prepare($sql);
            // PDO emparejará automáticamente las claves del array con los placeholders de la consulta.
            $consulta->execute($datos);
            return true;
        } catch (PDOException $e) {
            // Si algo falla, el error se registrará para una mejor depuración.
            error_log("Error en insertarRecurso: " . $e->getMessage());
            // Opcional: relanzar la excepción para que el controlador la maneje si es necesario.
            throw $e;
        }
    }
    /**
     * NUEVO: Obtiene un recurso por su ID (para poder eliminarlo).
     */
    public function getRecursoPorId($id) {
        $consulta = $this->db->prepare("SELECT * FROM recursos_clase WHERE id = :id");
        $consulta->execute([':id' => $id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * NUEVO: Elimina un recurso de la base de datos.
     */
    public function eliminarRecurso($id) {
        $sql = "DELETE FROM recursos_clase WHERE id = :id";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':id' => $id]);
    }

    /**
     * NUEVO: Guarda o actualiza la calificación de una entrega.
     */
    public function guardarCalificacion($entrega_id, $nota, $profesor_tipo_doc, $profesor_num_doc) {
        // Verificar si ya existe una calificación
        $sql_check = "SELECT id FROM calificaciones WHERE entrega_id = :entrega_id";
        $check = $this->db->prepare($sql_check);
        $check->execute([':entrega_id' => $entrega_id]);
        $existente = $check->fetch();

        if ($existente) {
            // Actualizar
            $sql = "UPDATE calificaciones SET nota = :nota, profesor_tipo_documento = :tipo_doc, profesor_numero_documento = :num_doc, fecha_actualizacion = NOW() WHERE id = :id";
            $params = [':nota' => $nota, ':tipo_doc' => $profesor_tipo_doc, ':num_doc' => $profesor_num_doc, ':id' => $existente['id']];
        } else {
            // Insertar
            $sql = "INSERT INTO calificaciones (entrega_id, nota, profesor_tipo_documento, profesor_numero_documento) VALUES (:entrega_id, :nota, :tipo_doc, :num_doc)";
            $params = [':entrega_id' => $entrega_id, ':nota' => $nota, ':tipo_doc' => $profesor_tipo_doc, ':num_doc' => $profesor_num_doc];
        }
        $consulta = $this->db->prepare($sql);
        $consulta->execute($params);
    }


    public function crearClase($datos) {
        try {
            $sql_orden = "SELECT MAX(orden) as max_orden FROM clases WHERE fase_id = :fase_id";
            $consulta_orden = $this->db->prepare($sql_orden);
            $consulta_orden->execute([':fase_id' => $datos['fase_id']]);
            $nuevo_orden = ($consulta_orden->fetchColumn() ?? 0) + 1;

            $sql = "INSERT INTO clases (fase_id, titulo, orden, profesor_tipo_documento, profesor_numero_documento) 
                    VALUES (:fase_id, :titulo, :orden, :profesor_tipo_doc, :profesor_num_doc)";
            $consulta = $this->db->prepare($sql);
            $consulta->execute([
                ':fase_id' => $datos['fase_id'],
                ':titulo' => $datos['titulo'],
                ':orden' => $nuevo_orden,
                ':profesor_tipo_doc' => $datos['profesor_tipo_doc'],
                ':profesor_num_doc' => $datos['profesor_num_doc']
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en crearClase: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene la información de un curso específico por su ID.
     */
     public function getCursoPorId($id) {
        $consulta = $this->db->prepare("SELECT * FROM cursos WHERE id = :id AND activo = 1");
        $consulta->execute([':id' => $id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }



    /**
     * Obtiene las clases de un curso con sus respectivos recursos.
     * Este método puede seguir siendo útil para otras vistas.
     */
    public function getRecursosPorClase($clase_id) {
        $consulta = $this->db->prepare("SELECT * FROM recursos_clase WHERE clase_id = :clase_id ORDER BY fecha_subida DESC");
        $consulta->execute([':clase_id' => $clase_id]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public function crearFase($curso_id, $nombre_fase) {
        try {
            $sql_orden = "SELECT MAX(orden) as max_orden FROM fases WHERE curso_id = :curso_id";
            $consulta_orden = $this->db->prepare($sql_orden);
            $consulta_orden->execute([':curso_id' => $curso_id]);
            $nuevo_orden = ($consulta_orden->fetchColumn() ?? 0) + 1;
            
            $sql = "INSERT INTO fases (curso_id, nombre, orden) VALUES (:curso_id, :nombre, :orden)";
            $consulta = $this->db->prepare($sql);
            $consulta->execute([':curso_id' => $curso_id, ':nombre' => $nombre_fase, ':orden' => $nuevo_orden]);
            return true;
        } catch(PDOException $e) {
            error_log("Error en crearFase: " . $e->getMessage());
            return false;
        }
    }
}
?>
