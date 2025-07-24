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

    public function getEvaluacionPorFaseId($fase_id) {
        $sql = "SELECT * FROM evaluaciones WHERE fase_id = :fase_id";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':fase_id' => $fase_id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function getEvaluacionCompleta($evaluacion_id) {
        $evaluacion = $this->db->prepare("SELECT * FROM evaluaciones WHERE id = :id");
        $evaluacion->execute([':id' => $evaluacion_id]);
        $data['evaluacion'] = $evaluacion->fetch(PDO::FETCH_ASSOC);

        if (!$data['evaluacion']) return null;

        $preguntas = $this->db->prepare("SELECT * FROM evaluacion_preguntas WHERE evaluacion_id = :evaluacion_id ORDER BY orden ASC");
        $preguntas->execute([':evaluacion_id' => $evaluacion_id]);
        $data['preguntas'] = $preguntas->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data['preguntas'] as $key => $pregunta) {
            $opciones = $this->db->prepare("SELECT * FROM evaluacion_opciones WHERE pregunta_id = :pregunta_id");
            $opciones->execute([':pregunta_id' => $pregunta['id']]);
            $data['preguntas'][$key]['opciones'] = $opciones->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    /**
     * Crea una nueva evaluación y devuelve su ID.
     */
    public function crearEvaluacion($fase_id, $titulo) {
        $sql = "INSERT INTO evaluaciones (fase_id, titulo) VALUES (:fase_id, :titulo)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':fase_id' => $fase_id, ':titulo' => $titulo]);
        return $this->db->lastInsertId();
    }

    /**
     * Crea una nueva pregunta y devuelve su ID.
     */
    public function crearPregunta($evaluacion_id, $texto_pregunta, $orden) {
        $sql = "INSERT INTO evaluacion_preguntas (evaluacion_id, texto_pregunta, orden) VALUES (:evaluacion_id, :texto_pregunta, :orden)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':evaluacion_id' => $evaluacion_id,
            ':texto_pregunta' => $texto_pregunta,
            ':orden' => $orden
        ]);
        return $this->db->lastInsertId();
    }

    public function getEstudiantesConProgresoCompletoPorCurso($curso_id) {
        // Paso 1: Obtener la lista de estudiantes inscritos.
        $sql = "SELECT 
                    u.numero_documento,
                    u.tipo_documento,
                    CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                    u.email
                FROM usuarios u
                JOIN inscripciones i ON u.numero_documento = i.estudiante_numero_documento AND u.tipo_documento = i.estudiante_tipo_documento
                WHERE i.curso_id = :curso_id AND i.estado_inscripcion = 'activa'
                ORDER BY u.apellido, u.nombre";
        
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':curso_id' => $curso_id]);
        $estudiantes = $consulta->fetchAll(PDO::FETCH_ASSOC);

        // Paso 2: Obtener el total de clases del curso una sola vez.
        $sql_total_clases = "SELECT COUNT(cl.id) FROM clases cl JOIN fases f ON cl.fase_id = f.id WHERE f.curso_id = :curso_id";
        $q_total_clases = $this->db->prepare($sql_total_clases);
        $q_total_clases->execute([':curso_id' => $curso_id]);
        $total_clases_curso = $q_total_clases->fetchColumn();

        // Paso 3: Para cada estudiante, obtener sus datos de progreso.
        foreach ($estudiantes as $key => $estudiante) {
            $estudiantes[$key]['total_clases'] = $total_clases_curso;

            // Clases completadas
            $sql_progreso = "SELECT COUNT(pe.id) FROM progreso_estudiante pe JOIN clases cl ON pe.clase_id = cl.id JOIN fases f ON cl.fase_id = f.id WHERE f.curso_id = :curso_id AND pe.estudiante_numero_documento = :num_doc AND pe.completado = 1";
            $q_progreso = $this->db->prepare($sql_progreso);
            $q_progreso->execute([':curso_id' => $curso_id, ':num_doc' => $estudiante['numero_documento']]);
            $estudiantes[$key]['clases_completadas'] = $q_progreso->fetchColumn();

            // Promedio de evaluaciones
            $sql_eval = "SELECT AVG(max_puntaje) FROM (SELECT MAX(er.puntaje) as max_puntaje FROM evaluacion_resultados er JOIN evaluaciones e ON er.evaluacion_id = e.id JOIN fases f ON e.fase_id = f.id WHERE f.curso_id = :curso_id AND er.estudiante_numero_documento = :num_doc GROUP BY er.evaluacion_id) as mejores_puntajes";
            $q_eval = $this->db->prepare($sql_eval);
            $q_eval->execute([':curso_id' => $curso_id, ':num_doc' => $estudiante['numero_documento']]);
            $estudiantes[$key]['promedio_evaluaciones'] = $q_eval->fetchColumn();
            
            // Observaciones del profesor
            $estudiantes[$key]['observaciones'] = $this->getObservacionesPorEstudiante($curso_id, $estudiante['numero_documento']);
        }

        return $estudiantes;
    }

    /**
     * NUEVO: Obtiene las observaciones de un profesor para un estudiante en un curso.
     */
    public function getObservacionesPorEstudiante($curso_id, $estudiante_num_doc) {
        $sql = "SELECT o.*, CONCAT(u.nombre, ' ', u.apellido) as nombre_profesor
                FROM profesor_observaciones o
                JOIN usuarios u ON o.profesor_numero_documento = u.numero_documento AND o.profesor_tipo_documento = u.tipo_documento
                WHERE o.curso_id = :curso_id AND o.estudiante_numero_documento = :est_num_doc
                ORDER BY o.fecha_creacion DESC";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':curso_id' => $curso_id, ':est_num_doc' => $estudiante_num_doc]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * NUEVO: Guarda una nueva observación del profesor en la base de datos.
     */
    public function guardarObservacion($datos) {
        $sql = "INSERT INTO profesor_observaciones (curso_id, estudiante_tipo_documento, estudiante_numero_documento, profesor_tipo_documento, profesor_numero_documento, observacion)
                VALUES (:curso_id, :est_tipo_doc, :est_num_doc, :prof_tipo_doc, :prof_num_doc, :observacion)";
        $consulta = $this->db->prepare($sql);
        return $consulta->execute($datos);
    }

    /**
     * Crea una opción para una pregunta.
     */
    public function crearOpcion($pregunta_id, $texto_opcion, $es_correcta) {
        $sql = "INSERT INTO evaluacion_opciones (pregunta_id, texto_opcion, es_correcta) VALUES (:pregunta_id, :texto_opcion, :es_correcta)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':pregunta_id' => $pregunta_id,
            ':texto_opcion' => $texto_opcion,
            ':es_correcta' => $es_correcta
        ]);
    }

    public function getFasePorId($fase_id) {
    $consulta = $this->db->prepare("SELECT * FROM fases WHERE id = :id");
    $consulta->execute([':id' => $fase_id]);
    return $consulta->fetch(PDO::FETCH_ASSOC);
}

public function getEvaluacionCompletaPorFase($fase_id) {
    $evaluacion = $this->getEvaluacionPorFaseId($fase_id);
    if ($evaluacion) {
        return $this->getEvaluacionCompleta($evaluacion['id']);
    }
    return null;
}

    /**
     * Elimina una evaluación y todas sus preguntas/opciones asociadas.
     */
    public function eliminarEvaluacionExistente($evaluacion_id) {
        $sql = "DELETE FROM evaluaciones WHERE id = :evaluacion_id";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':evaluacion_id' => $evaluacion_id]);
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
