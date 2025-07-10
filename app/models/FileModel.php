<?php
// app/models/FileModel.php
// Modelo para la gestión de archivos (materiales y entregas)

class FileModel {
    private $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    // Subir material (profesor)
    public function uploadMaterial($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO materiales (curso_id, profesor_id, nombre_archivo, nombre_original, 
                                       tipo_archivo, ruta_archivo, tamaño_archivo, descripcion)
                VALUES (:curso_id, :profesor_id, :nombre_archivo, :nombre_original, 
                        :tipo_archivo, :ruta_archivo, :tamaño_archivo, :descripcion)
            ");
            
            $stmt->execute([
                ':curso_id' => $data['curso_id'],
                ':profesor_id' => $data['profesor_id'],
                ':nombre_archivo' => $data['nombre_archivo'],
                ':nombre_original' => $data['nombre_original'],
                ':tipo_archivo' => $data['tipo_archivo'],
                ':ruta_archivo' => $data['ruta_archivo'],
                ':tamaño_archivo' => $data['tamaño_archivo'],
                ':descripcion' => $data['descripcion'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al subir material: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener materiales de un curso
    public function getMaterialsByCourse($curso_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, u.nombre as profesor_nombre, u.apellido as profesor_apellido
                FROM materiales m
                INNER JOIN usuarios u ON m.profesor_id = u.id
                WHERE m.curso_id = :curso_id
                ORDER BY m.fecha_subida DESC
            ");
            $stmt->execute([':curso_id' => $curso_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener materiales: " . $e->getMessage());
            return false;
        }
    }

    // Obtener un material por su ID
    public function getMaterialById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM materiales WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener material por ID: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar un material
    public function deleteMaterial($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM materiales WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al eliminar material: " . $e->getMessage());
            return false;
        }
    }
    
    // Subir entrega (estudiante)
    public function uploadSubmission($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO entregas (estudiante_id, curso_id, nombre_archivo, nombre_original, 
                                     tipo_archivo, ruta_archivo, tamaño_archivo, descripcion)
                VALUES (:estudiante_id, :curso_id, :nombre_archivo, :nombre_original, 
                        :tipo_archivo, :ruta_archivo, :tamaño_archivo, :descripcion)
            ");
            
            $stmt->execute([
                ':estudiante_id' => $data['estudiante_id'],
                ':curso_id' => $data['curso_id'],
                ':nombre_archivo' => $data['nombre_archivo'],
                ':nombre_original' => $data['nombre_original'],
                ':tipo_archivo' => $data['tipo_archivo'],
                ':ruta_archivo' => $data['ruta_archivo'],
                ':tamaño_archivo' => $data['tamaño_archivo'],
                ':descripcion' => $data['descripcion'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al subir entrega: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener entregas de un curso
    public function getSubmissionsByCourse($curso_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido,
                       cal.nota, cal.observaciones, cal.fecha_calificacion
                FROM entregas e
                INNER JOIN usuarios u ON e.estudiante_id = u.id
                LEFT JOIN calificaciones cal ON e.id = cal.entrega_id
                WHERE e.curso_id = :curso_id
                ORDER BY e.fecha_entrega DESC
            ");
            $stmt->execute([':curso_id' => $curso_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener entregas del curso: " . $e->getMessage());
            return false;
        }
    }

    // Obtener una entrega por su ID
    public function getSubmissionById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido,
                       cal.nota, cal.observaciones, cal.fecha_calificacion
                FROM entregas e
                INNER JOIN usuarios u ON e.estudiante_id = u.id
                LEFT JOIN calificaciones cal ON e.id = cal.entrega_id
                WHERE e.id = :id
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener entrega por ID: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar una entrega (por ejemplo, su descripción)
    public function updateSubmission($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE entregas 
                SET nombre_archivo = :nombre_archivo, descripcion = :descripcion
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':id' => $id,
                ':nombre_archivo' => $data['nombre_archivo'],
                ':descripcion' => $data['descripcion'] ?? null
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al actualizar entrega: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar una entrega
    public function deleteSubmission($id) {
        try {
            // Primero, eliminar cualquier calificación asociada a esta entrega
            $stmt = $this->db->prepare("DELETE FROM calificaciones WHERE entrega_id = :id");
            $stmt->execute([':id' => $id]);

            // Luego, eliminar la entrega
            $stmt = $this->db->prepare("DELETE FROM entregas WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al eliminar entrega: " . $e->getMessage());
            return false;
        }
    }
}
?>