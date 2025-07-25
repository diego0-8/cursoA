<?php
class ModeloAuditor {
    private $db;

    public function __construct() {
        require_once('conexion.php');
        $this->db = Conexion::conectar();
    }

    /**
     * Obtiene toda la actividad de un estudiante: cursos, progreso y notas.
     */
    public function getActividadEstudiante($tipo_doc, $num_doc) {
        // CORRECCIÓN DEFINITIVA: Se reestructura la consulta usando LEFT JOINs con subconsultas
        // para evitar el error de "columna no encontrada" (scope issue) en algunas versiones de MySQL/MariaDB.
        // Este método es más robusto y compatible.
        $sql = "SELECT
                    c.nombre AS nombre_curso,
                    i.fecha_inscripcion,
                    COALESCE(clases_totales.total, 0) AS total_clases,
                    COALESCE(clases_completadas.total, 0) AS clases_completadas,
                    promedios.promedio_evaluaciones
                FROM
                    inscripciones i
                JOIN
                    cursos c ON i.curso_id = c.id
                LEFT JOIN
                    (SELECT f.curso_id, COUNT(cl.id) AS total
                     FROM clases cl
                     JOIN fases f ON cl.fase_id = f.id
                     GROUP BY f.curso_id) AS clases_totales ON c.id = clases_totales.curso_id
                LEFT JOIN
                    (SELECT f.curso_id, COUNT(pe.id) AS total
                     FROM progreso_estudiante pe
                     JOIN clases cl ON pe.clase_id = cl.id
                     JOIN fases f ON cl.fase_id = f.id
                     WHERE pe.estudiante_numero_documento = :num_doc AND pe.completado = 1
                     GROUP BY f.curso_id) AS clases_completadas ON c.id = clases_completadas.curso_id
                LEFT JOIN
                    (SELECT
                         f.curso_id,
                         AVG(mejores_puntajes.max_puntaje) AS promedio_evaluaciones
                     FROM
                         (SELECT
                              er.evaluacion_id,
                              MAX(er.puntaje) AS max_puntaje
                          FROM
                              evaluacion_resultados er
                          WHERE er.estudiante_numero_documento = :num_doc
                          GROUP BY er.evaluacion_id) AS mejores_puntajes
                     JOIN evaluaciones e ON mejores_puntajes.evaluacion_id = e.id
                     JOIN fases f ON e.fase_id = f.id
                     GROUP BY f.curso_id) AS promedios ON c.id = promedios.curso_id
                WHERE
                    i.estudiante_tipo_documento = :tipo_doc AND i.estudiante_numero_documento = :num_doc
                ORDER BY
                    c.nombre";
        
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':tipo_doc' => $tipo_doc, ':num_doc' => $num_doc]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene las estadísticas de un profesor: conteo de cursos, estudiantes, etc.
     */
    public function getActividadProfesor($tipo_doc, $num_doc) {
        $sql = "SELECT 
                    (SELECT COUNT(id) FROM cursos WHERE profesor_numero_documento = :num_doc) as total_cursos,
                    (SELECT COUNT(DISTINCT i.estudiante_numero_documento) FROM inscripciones i JOIN cursos c ON i.curso_id = c.id WHERE c.profesor_numero_documento = :num_doc) as total_estudiantes,
                    (SELECT COUNT(f.id) FROM fases f JOIN cursos c ON f.curso_id = c.id WHERE c.profesor_numero_documento = :num_doc) as total_modulos,
                    (SELECT COUNT(cl.id) FROM clases cl JOIN fases f ON cl.fase_id = f.id JOIN cursos c ON f.curso_id = c.id WHERE c.profesor_numero_documento = :num_doc) as total_clases";

        $consulta = $this->db->prepare($sql);
        $consulta->execute([':num_doc' => $num_doc]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
}
?>
