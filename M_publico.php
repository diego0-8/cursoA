<?php
class ModeloPublico {
    private $db;

    public function __construct() {
        require_once('conexion.php');
        $this->db = Conexion::conectar();
    }

    /**
     * Busca todos los certificados emitidos para un número de documento específico.
     */
    public function buscarCertificadosPorDocumento($num_doc) {
        $sql = "SELECT 
                    CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                    c.nombre as nombre_curso,
                    cert.fecha_emision,
                    cert.codigo_unico
                FROM certificados cert
                JOIN usuarios u ON cert.estudiante_numero_documento = u.numero_documento AND cert.estudiante_tipo_documento = u.tipo_documento
                JOIN cursos c ON cert.curso_id = c.id
                WHERE cert.estudiante_numero_documento = :num_doc
                ORDER BY cert.fecha_emision DESC";
        
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':num_doc' => $num_doc]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>