<?php
// app/models/CourseModel.php
// Modelo para la gestión de cursos

class CourseModel {
    private $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    // Crear un nuevo curso
    public function createCourse($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO cursos (nombre, descripcion, profesor_id, fecha_inicio, fecha_fin, estado)
                VALUES (:nombre, :descripcion, :profesor_id, :fecha_inicio, :fecha_fin, :estado)
            ");
            
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':descripcion' => $data['descripcion'],
                ':profesor_id' => $data['profesor_id'],
                ':fecha_inicio' => $data['fecha_inicio'],
                ':fecha_fin' => $data['fecha_fin'],
                ':estado' => $data['estado'] ?? 'activo'
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al crear curso: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener todos los cursos
    public function getAllCourses() {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, u.nombre as profesor_nombre, u.apellido as profesor_apellido,
                       COUNT(i.id) as total_estudiantes
                FROM cursos c
                LEFT JOIN usuarios u ON c.profesor_id = u.id
                LEFT JOIN inscripciones i ON c.id = i.curso_id AND i.estado = 'activo'
                GROUP BY c.id
                ORDER BY c.fecha_creacion DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener cursos: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener cursos por profesor
    public function getCoursesByProfessor($profesor_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, COUNT(i.id) as total_estudiantes
                FROM cursos c
                LEFT JOIN inscripciones i ON c.id = i.curso_id AND i.estado = 'activo'
                WHERE c.profesor_id = :profesor_id
                GROUP BY c.id
                ORDER BY c.fecha_creacion DESC
            ");
            $stmt->execute([':profesor_id' => $profesor_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener cursos del profesor: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener cursos de un estudiante
    public function getCoursesByStudent($estudiante_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, u.nombre as profesor_nombre, u.apellido as profesor_apellido,
                       i.fecha_inscripcion, i.estado as estado_inscripcion
                FROM cursos c
                INNER JOIN inscripciones i ON c.id = i.curso_id
                LEFT JOIN usuarios u ON c.profesor_id = u.id
                WHERE i.estudiante_id = :estudiante_id
                ORDER BY i.fecha_inscripcion DESC
            ");
            $stmt->execute([':estudiante_id' => $estudiante_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener cursos del estudiante: " . $e->getMessage());
            return false;
        }
    }
    
    // Inscribir estudiante en curso
    public function enrollStudent($estudiante_id, $curso_id) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO inscripciones (estudiante_id, curso_id, estado)
                VALUES (:estudiante_id, :curso_id, 'activo')
            ");
            
            $stmt->execute([
                ':estudiante_id' => $estudiante_id,
                ':curso_id' => $curso_id
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al inscribir estudiante: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener estudiantes de un curso
    public function getStudentsByCourse($curso_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT u.*, i.fecha_inscripcion, i.estado as estado_inscripcion,
                       AVG(cal.nota) as promedio_notas
                FROM usuarios u
                INNER JOIN inscripciones i ON u.id = i.estudiante_id
                LEFT JOIN calificaciones cal ON u.id = cal.estudiante_id AND cal.curso_id = :curso_id
                WHERE i.curso_id = :curso_id AND u.rol = 'estudiante'
                GROUP BY u.id
                ORDER BY u.apellido, u.nombre
            ");
            $stmt->execute([':curso_id' => $curso_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener estudiantes del curso: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener curso por ID
    public function getCourseById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, u.nombre as profesor_nombre, u.apellido as profesor_apellido,
                       u.email as profesor_email
                FROM cursos c
                LEFT JOIN usuarios u ON c.profesor_id = u.id
                WHERE c.id = :id
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener curso: " . $e->getMessage());
            return false;
        }
    }
    
    // Actualizar curso
    public function updateCourse($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE cursos 
                SET nombre = :nombre, descripcion = :descripcion, 
                    profesor_id = :profesor_id, fecha_inicio = :fecha_inicio, 
                    fecha_fin = :fecha_fin, estado = :estado
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':id' => $id,
                ':nombre' => $data['nombre'],
                ':descripcion' => $data['descripcion'],
                ':profesor_id' => $data['profesor_id'],
                ':fecha_inicio' => $data['fecha_inicio'],
                ':fecha_fin' => $data['fecha_fin'],
                ':estado' => $data['estado']
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al actualizar curso: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar curso
    public function deleteCourse($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM cursos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al eliminar curso: " . $e->getMessage());
            return false;
        }
    }
    
    // Verificar si un estudiante está inscrito en un curso
    public function isStudentEnrolled($estudiante_id, $curso_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM inscripciones 
                WHERE estudiante_id = :estudiante_id AND curso_id = :curso_id AND estado = 'activo'
            ");
            $stmt->execute([
                ':estudiante_id' => $estudiante_id,
                ':curso_id' => $curso_id
            ]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar inscripción: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener estadísticas generales
    public function getStatistics() {
        try {
            $stats = [];
            
            // Total de cursos
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM cursos");
            $stmt->execute();
            $stats['total_cursos'] = $stmt->fetchColumn();
            
            // Total de inscripciones activas
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM inscripciones WHERE estado = 'activo'");
            $stmt->execute();
            $stats['total_inscripciones'] = $stmt->fetchColumn();
            
            // Cursos más populares
            $stmt = $this->db->prepare("
                SELECT c.nombre, COUNT(i.id) as total_estudiantes
                FROM cursos c
                LEFT JOIN inscripciones i ON c.id = i.curso_id AND i.estado = 'activo'
                GROUP BY c.id
                ORDER BY total_estudiantes DESC
                LIMIT 5
            ");
            $stmt->execute();
            $stats['cursos_populares'] = $stmt->fetchAll();
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return false;
        }
    }
}