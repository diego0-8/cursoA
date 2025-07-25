<?php
require_once 'models/M_publico.php';

class ControladorPublico {

    private $modelo;

    public function __construct() {
        $this->modelo = new ModeloPublico();
    }

    /**
     * Muestra la página inicial de consulta de certificados.
     */
    public function index() {
        $data['titulo'] = 'Consulta Pública de Certificados';
        $data['resultados'] = null; // Inicialmente no hay resultados
        require_once 'views/V_publico_consulta.php';
    }

    /**
     * Procesa la búsqueda de un certificado por número de documento.
     */
    public function buscarCertificado() {
        $numero_documento = $_POST['numero_documento'] ?? '';
        
        $data['titulo'] = 'Resultado de la Consulta';
        $data['busqueda_realizada'] = true; // Para saber que se hizo una búsqueda
        $data['numero_buscado'] = $numero_documento;

        if (!empty($numero_documento)) {
            $data['resultados'] = $this->modelo->buscarCertificadosPorDocumento($numero_documento);
        } else {
            $data['resultados'] = [];
        }

        require_once 'views/V_publico_consulta.php';
    }
}
?>
