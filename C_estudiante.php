<?php
require_once 'models/M_estudiante.php'; // Usará un nuevo modelo

class ControladorEstudiante {

    private $modelo;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Solo estudiantes pueden acceder
        if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'estudiante') {
            header('Location: index.php?c=login');
            exit();
        }
        $this->modelo = new ModeloEstudiante();
    }

    // Acción principal: muestra el dashboard del estudiante
    public function index() {
        $data['titulo'] = 'Panel de Estudiante';
        $data['nombre_usuario'] = $_SESSION['usuario_nombre_completo'];

        // Obtener los cursos a los que el estudiante puede inscribirse
        $estudiante_documento = $_SESSION['usuario_numero_documento'];
        $data['cursos_disponibles'] = $this->modelo->getCursosDisponibles($estudiante_documento);
        
        require_once 'views/V_estudiante_dashboard.php';
    }

    // Procesa la solicitud de inscripción a un curso
    public function solicitarInscripcion() {
        $curso_id = $_GET['id_curso'] ?? 0;
        $estudiante_tipo_doc = $_SESSION['usuario_tipo_documento'];
        $estudiante_num_doc = $_SESSION['usuario_numero_documento'];

        if (empty($curso_id)) {
            $_SESSION['error'] = "No se ha especificado un curso.";
            header('Location: index.php?c=estudiante');
            exit();
        }

        $exito = $this->modelo->crearSolicitudInscripcion($curso_id, $estudiante_tipo_doc, $estudiante_num_doc);

        if ($exito) {
            $_SESSION['mensaje'] = "Tu solicitud de inscripción ha sido enviada. Recibirás una notificación cuando sea revisada.";
        } else {
            $_SESSION['error'] = "No se pudo enviar la solicitud. Es posible que ya hayas solicitado la inscripción a este curso.";
        }
        
        header('Location: index.php?c=estudiante');
        exit();
    }

    public function marcarComoCompletada() {
        $clase_id = $_GET['clase_id'] ?? 0;
        $curso_id = $_GET['curso_id'] ?? 0;

        if ($clase_id && $curso_id) {
            $tipo_doc = $_SESSION['usuario_tipo_documento'];
            $num_doc = $_SESSION['usuario_numero_documento'];
            $this->modelo->marcarClaseComoCompleta($clase_id, $tipo_doc, $num_doc);
        }
        
        // Redirigir de vuelta a la página del curso
        header('Location: index.php?c=estudiante&a=verCurso&id=' . $curso_id);
        exit();
    }

    
    public function misCursos() {
        $data['titulo'] = 'Mis Cursos';
        $num_doc = $_SESSION['usuario_numero_documento'];
        $tipo_doc = $_SESSION['usuario_tipo_documento'];

        // 1. Obtiene la lista de cursos
        $cursos = $this->modelo->getMisCursos($num_doc);

        // 2. Itera sobre cada curso para calcular y añadir su progreso
        if (!empty($cursos)) {
            foreach ($cursos as $key => $curso) {
                $progreso = $this->modelo->getProgresoCurso($curso['id'], $tipo_doc, $num_doc);
                $cursos[$key]['progreso'] = $progreso;
            }
        }

        // 3. Pasa los datos (cursos + progreso) a la vista
        $data['cursos'] = $cursos;
        require_once 'views/V_estudiante_mis_cursos.php';
    }

    
    public function verCurso() {
        $curso_id = $_GET['id'] ?? 0;
        $data['curso'] = $this->modelo->getCursoPorId($curso_id);
        
        if (!$data['curso']) {
            die("Curso no encontrado.");
        }

        $tipo_doc = $_SESSION['usuario_tipo_documento'];
        $num_doc = $_SESSION['usuario_numero_documento'];

        $data['titulo'] = $data['curso']['nombre'];
        // Pasamos los datos del estudiante para obtener el progreso individual
        $data['fases'] = $this->modelo->getContenidoCurso($curso_id, $tipo_doc, $num_doc);

        // Verificamos el progreso y lo pasamos a la vista
        $progreso = $this->modelo->getProgresoCurso($curso_id, $tipo_doc, $num_doc);
        $data['progreso'] = $progreso;
        $data['curso_completado'] = ($progreso['total_clases'] > 0 && $progreso['total_clases'] === $progreso['clases_completadas']);
        
        require_once 'views/V_estudiante_ver_curso.php';
    }
    
    
    public function generarCertificado() {
        $curso_id = $_GET['curso_id'] ?? 0;
        $tipo_doc = $_SESSION['usuario_tipo_documento'];
        $num_doc = $_SESSION['usuario_numero_documento'];

        // Doble verificación: ¿El curso está completo?
        $progreso = $this->modelo->getProgresoCurso($curso_id, $tipo_doc, $num_doc);
        $completado = ($progreso['total_clases'] > 0 && $progreso['clases_completadas'] === $progreso['total_clases']);

        if ($curso_id && $completado) {
            // Verificar si ya existe un certificado para no duplicar
            $certificado_existente = $this->modelo->getCertificado($tipo_doc, $num_doc, $curso_id);
            if (!$certificado_existente) {
                $this->modelo->crearCertificado($tipo_doc, $num_doc, $curso_id);
            }
        }
        // Redirigir siempre a la vista del certificado
        header('Location: index.php?c=estudiante&a=verCertificado&curso_id=' . $curso_id);
        exit();
    }
    
    public function verCertificado() {
        $curso_id = $_GET['curso_id'] ?? 0;
        $tipo_doc = $_SESSION['usuario_tipo_documento'];
        $num_doc = $_SESSION['usuario_numero_documento'];
        
        $data['certificado'] = $this->modelo->getCertificado($tipo_doc, $num_doc, $curso_id);

        if (!$data['certificado']) {
            die("Certificado no encontrado o no tienes permiso para verlo.");
        }

        $data['titulo'] = 'Certificado de Finalización';
        require_once 'views/V_estudiante_certificado.php';
    }

}
?>
