<?php
// app/models/ProgressModel.php

class ProgressModel {
    /**
     * @var PDO La conexión a la base de datos.
     */
    private $pdo;

    /**
     * El constructor recibe la conexión PDO para poder interactuar con la base de datos.
     *
     * @param PDO $pdo La instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Marca una clase como completada para un estudiante específico.
     *
     * @param string $tipoDocumento El tipo de documento del estudiante.
     * @param string $numeroDocumento El número de documento del estudiante.
     * @param int $claseId El ID de la clase.
     * @return bool Devuelve true si la operación fue exitosa, false en caso contrario.
     */
    public function markClassAsCompleted(string $tipoDocumento, string $numeroDocumento, int $claseId): bool {
        $sql = "
            INSERT INTO progreso_estudiante (estudiante_tipo_documento, estudiante_numero_documento, clase_id, completado, fecha_completado)
            VALUES (:tipo_documento, :numero_documento, :clase_id, 1, NOW())
            ON DUPLICATE KEY UPDATE completado = 1, fecha_completado = NOW()";
            
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            'tipo_documento' => $tipoDocumento,
            'numero_documento' => $numeroDocumento,
            'clase_id' => $claseId
        ]);
    }

    /**
     * Calcula el porcentaje de progreso de un estudiante en un curso específico.
     *
     * @param string $tipoDocumento El tipo de documento del estudiante.
     * @param string $numeroDocumento El número de documento del estudiante.
     * @param int $cursoId El ID del curso.
     * @return array Un array con el total de clases, clases completadas y el porcentaje.
     */
    public function getCourseProgress(string $tipoDocumento, string $numeroDocumento, int $cursoId): array {
        // 1. Contar el número total de clases en el curso.
        $sqlTotal = "
            SELECT COUNT(c.id) 
            FROM clases c
            JOIN fases f ON c.fase_id = f.id
            WHERE f.curso_id = :curso_id
        ";
        $stmtTotal = $this->pdo->prepare($sqlTotal);
        $stmtTotal->execute(['curso_id' => $cursoId]);
        $totalClases = $stmtTotal->fetchColumn();

        if ($totalClases == 0) {
            return ['total' => 0, 'completed' => 0, 'percentage' => 100];
        }

        // 2. Contar cuántas de esas clases ha completado el estudiante.
        $sqlCompleted = "
            SELECT COUNT(pe.id) 
            FROM progreso_estudiante pe
            JOIN clases c ON pe.clase_id = c.id
            JOIN fases f ON c.fase_id = f.id
            WHERE pe.estudiante_tipo_documento = :tipo_documento 
              AND pe.estudiante_numero_documento = :numero_documento
              AND f.curso_id = :curso_id
              AND pe.completado = 1
        ";
        $stmtCompleted = $this->pdo->prepare($sqlCompleted);
        $stmtCompleted->execute([
            'tipo_documento' => $tipoDocumento,
            'numero_documento' => $numeroDocumento,
            'curso_id' => $cursoId
        ]);
        $completedClases = $stmtCompleted->fetchColumn();

        // 3. Calcular el porcentaje.
        $percentage = ($completedClases / $totalClases) * 100;

        return [
            'total' => (int)$totalClases,
            'completed' => (int)$completedClases,
            'percentage' => round($percentage)
        ];
    }
    
    /**
     * Obtiene los IDs de todas las clases que un estudiante ha completado en un curso.
     *
     * @param string $tipoDocumento El tipo de documento del estudiante.
     * @param string $numeroDocumento El número de documento del estudiante.
     * @param int $cursoId El ID del curso.
     * @return array Un array con los IDs de las clases completadas.
     */
    public function getCompletedClassIds(string $tipoDocumento, string $numeroDocumento, int $cursoId): array {
        $sql = "
            SELECT pe.clase_id
            FROM progreso_estudiante pe
            JOIN clases c ON pe.clase_id = c.id
            JOIN fases f ON c.fase_id = f.id
            WHERE pe.estudiante_tipo_documento = :tipo_documento
              AND pe.estudiante_numero_documento = :numero_documento
              AND f.curso_id = :curso_id
              AND pe.completado = 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'tipo_documento' => $tipoDocumento,
            'numero_documento' => $numeroDocumento,
            'curso_id' => $cursoId
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}
