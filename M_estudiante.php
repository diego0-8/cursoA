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
        $sql_fases = "SELECT f.*, e.id as evaluacion_id, e.titulo as evaluacion_titulo
                      FROM fases f
                      LEFT JOIN evaluaciones e ON f.id = e.fase_id
                      WHERE f.curso_id = :curso_id ORDER BY f.orden ASC";
        $consulta_fases = $this->db->prepare($sql_fases);
        $consulta_fases->execute([':curso_id' => $curso_id]);
        $fases = $consulta_fases->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fases as $key_fase => $fase) {
            // Clases del módulo
            $sql_clases = "SELECT c.*, p.completado 
                           FROM clases c
                           LEFT JOIN progreso_estudiante p ON c.id = p.clase_id AND p.estudiante_numero_documento = :num_doc
                           WHERE c.fase_id = :fase_id ORDER BY c.orden ASC";
            $consulta_clases = $this->db->prepare($sql_clases);
            $consulta_clases->execute([':fase_id' => $fase['id'], ':num_doc' => $num_doc]);
            $clases = $consulta_clases->fetchAll(PDO::FETCH_ASSOC);
            
            // Recursos de cada clase
            foreach ($clases as $key_clase => $clase) {
                $clases[$key_clase]['recursos'] = $this->getRecursosPorClase($clase['id']);
            }
            $fases[$key_fase]['clases'] = $clases;

            // Progreso y estado de la evaluación del módulo
            $fases[$key_fase]['progreso_clases'] = $this->getProgresoFase($fase['id'], $tipo_doc, $num_doc);
            if ($fase['evaluacion_id']) {
                $fases[$key_fase]['resultado_evaluacion'] = $this->getMejorResultado($fase['evaluacion_id'], $tipo_doc, $num_doc);
            } else {
                $fases[$key_fase]['resultado_evaluacion'] = null;
            }
        }
        return $fases;
    }

    public function getProgresoFase($fase_id, $tipo_doc, $num_doc) {
        $sql_total = "SELECT COUNT(id) FROM clases WHERE fase_id = :fase_id";
        $q_total = $this->db->prepare($sql_total);
        $q_total->execute([':fase_id' => $fase_id]);
        $total = $q_total->fetchColumn();

        $sql_completadas = "SELECT COUNT(p.id) FROM progreso_estudiante p JOIN clases c ON p.clase_id = c.id WHERE c.fase_id = :fase_id AND p.estudiante_numero_documento = :num_doc AND p.completado = 1";
        $q_completadas = $this->db->prepare($sql_completadas);
        $q_completadas->execute([':fase_id' => $fase_id, ':num_doc' => $num_doc]);
        $completadas = $q_completadas->fetchColumn();

        return ['total' => (int)$total, 'completadas' => (int)$completadas];
    }

    public function getMejorResultado($evaluacion_id, $tipo_doc, $num_doc) {
        $sql = "SELECT MAX(puntaje) as mejor_puntaje FROM evaluacion_resultados WHERE evaluacion_id = :eval_id AND estudiante_numero_documento = :num_doc";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':eval_id' => $evaluacion_id, ':num_doc' => $num_doc]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * NUEVO: Obtiene los datos de una evaluación para que el estudiante la realice.
     */
    public function getEvaluacionParaPresentar($evaluacion_id) {
        $sql = "SELECT e.id, e.titulo, p.id as pregunta_id, p.texto_pregunta, o.id as opcion_id, o.texto_opcion
                FROM evaluaciones e
                JOIN evaluacion_preguntas p ON e.id = p.evaluacion_id
                JOIN evaluacion_opciones o ON p.id = o.pregunta_id
                WHERE e.id = :eval_id ORDER BY p.orden, o.id";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':eval_id' => $evaluacion_id]);
        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar resultados en un formato útil
        $evaluacion = [];
        if (!empty($resultados)) {
            $evaluacion['id'] = $resultados[0]['id'];
            $evaluacion['titulo'] = $resultados[0]['titulo'];
            $evaluacion['preguntas'] = [];
            foreach ($resultados as $row) {
                $evaluacion['preguntas'][$row['pregunta_id']]['id'] = $row['pregunta_id'];
                $evaluacion['preguntas'][$row['pregunta_id']]['texto_pregunta'] = $row['texto_pregunta'];
                $evaluacion['preguntas'][$row['pregunta_id']]['opciones'][] = ['id' => $row['opcion_id'], 'texto' => $row['texto_opcion']];
            }
        }
        return $evaluacion;
    }

    /**
     * NUEVO: Calcula el puntaje de una evaluación enviada.
     */
    public function calcularPuntaje($evaluacion_id, $respuestas) {
        $sql = "SELECT pregunta_id, id FROM evaluacion_opciones WHERE es_correcta = 1 AND pregunta_id IN (SELECT id FROM evaluacion_preguntas WHERE evaluacion_id = :eval_id)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':eval_id' => $evaluacion_id]);
        $correctas = $consulta->fetchAll(PDO::FETCH_KEY_PAIR); // [pregunta_id => opcion_id_correcta]

        $puntaje = 0;
        $total_preguntas = count($correctas);
        $respuestas_correctas = 0;

        foreach ($respuestas as $pregunta_id => $opcion_id) {
            if (isset($correctas[$pregunta_id]) && $correctas[$pregunta_id] == $opcion_id) {
                $respuestas_correctas++;
            }
        }

        if ($total_preguntas > 0) {
            $puntaje = ($respuestas_correctas / $total_preguntas) * 100;
        }

        return [
            'puntaje' => round($puntaje, 2),
            'total_preguntas' => $total_preguntas,
            'respuestas_correctas' => $respuestas_correctas
        ];
    }

    /**
     * NUEVO: Guarda el resultado de un intento de evaluación.
     */
    public function guardarResultadoEvaluacion($evaluacion_id, $tipo_doc, $num_doc, $resultado, $respuestas_json) {
        $sql = "INSERT INTO evaluacion_resultados (evaluacion_id, estudiante_tipo_documento, estudiante_numero_documento, puntaje, total_preguntas, respuestas_correctas, respuestas_json)
                VALUES (:eval_id, :tipo_doc, :num_doc, :puntaje, :total, :correctas, :json)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':eval_id' => $evaluacion_id,
            ':tipo_doc' => $tipo_doc,
            ':num_doc' => $num_doc,
            ':puntaje' => $resultado['puntaje'],
            ':total' => $resultado['total_preguntas'],
            ':correctas' => $resultado['respuestas_correctas'],
            ':json' => $respuestas_json
        ]);
    }
    
    /**
     * NUEVO: Verifica si un estudiante ha aprobado todos los módulos de un curso.
     */
    public function verificarAprobacionCurso($curso_id, $tipo_doc, $num_doc) {
        $contenido = $this->getContenidoCurso($curso_id, $tipo_doc, $num_doc);
        if (empty($contenido)) return false;

        foreach ($contenido as $fase) {
            // Condición 1: Todas las clases del módulo deben estar completas.
            if ($fase['progreso_clases']['total'] > 0 && $fase['progreso_clases']['completadas'] < $fase['progreso_clases']['total']) {
                return false; // Faltan clases por completar
            }
            // Condición 2: Si el módulo tiene evaluación, debe estar aprobada con 80 o más.
            if ($fase['evaluacion_id'] && (!isset($fase['resultado_evaluacion']['mejor_puntaje']) || $fase['resultado_evaluacion']['mejor_puntaje'] < 80)) {
                return false; // La evaluación no ha sido aprobada
            }
        }
        return true; // Si pasa todas las validaciones, el curso está aprobado.
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