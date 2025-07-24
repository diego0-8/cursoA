<?php

// Es una buena práctica tener los errores visibles durante el desarrollo.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'models/M_cursos.php'; // Se necesita el modelo de cursos

class ControladorProfesor {

    private $modeloCursos;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'profesor') {
            header('Location: index.php?c=login');
            exit();
        }
        $this->modeloCursos = new ModeloCursos();
    }

    /**
     * Muestra el panel principal del profesor.
     */
    public function index() {
        $data['titulo'] = 'Panel de Profesor';
        require_once 'views/V_profesor_dashboard.php';
    }

    /**
     * Muestra la lista de cursos asignados al profesor.
     */
    public function listarCursos() {
        $data['titulo'] = 'Mis Cursos';
        $data['cursos'] = $this->modeloCursos->getCursosPorProfesor($_SESSION['usuario_numero_documento']);
        require_once 'views/V_profesor_listar_cursos.php';
    }

    /**
     * Muestra la página para gestionar un curso específico (módulos, clases, etc.).
     */
    public function gestionarCurso() {
        $curso_id = $_GET['id'] ?? 0;
        $data['curso'] = $this->modeloCursos->getCursoPorId($curso_id);
        
        if (!$data['curso'] || $data['curso']['profesor_numero_documento'] != $_SESSION['usuario_numero_documento']) {
            $_SESSION['error'] = "Acceso denegado o curso no encontrado.";
            header('Location: index.php?c=profesor&a=listarCursos');
            exit();
        }
        
        $data['titulo'] = 'Gestionar: ' . htmlspecialchars($data['curso']['nombre']);
        $data['fases'] = $this->modeloCursos->getFasesPorCurso($curso_id);
        
        require_once 'views/V_profesor_gestionar_curso.php';
    }
    
    /**
     * Muestra la interfaz para crear o editar una evaluación para un módulo (fase).
     */
    public function gestionarEvaluacion() {
        $fase_id = $_GET['fase_id'] ?? 0;
        if (!$fase_id) {
            $_SESSION['error'] = "Error: ID de módulo no especificado.";
            header('Location: index.php?c=profesor&a=listarCursos');
            exit();
        }

        $fase_info = $this->modeloCursos->getFasePorId($fase_id);
        if (!$fase_info) {
            $_SESSION['error'] = "Módulo no encontrado.";
            header('Location: index.php?c=profesor&a=listarCursos');
            exit();
        }
        
        $curso_info = $this->modeloCursos->getCursoPorId($fase_info['curso_id']);

        // Verificar permisos del profesor
        if (!$curso_info || $curso_info['profesor_numero_documento'] != $_SESSION['usuario_numero_documento']) {
            $_SESSION['error'] = "Acceso denegado a este curso.";
            header('Location: index.php?c=profesor&a=listarCursos');
            exit();
        }

        $data['titulo'] = 'Gestionar Evaluación';
        $data['fase_info'] = $fase_info;
        
        // --- CORRECCIÓN ---
        // Se revierte el cambio anterior. Ahora se usa 'curso_info' para que coincida
        // con la vista que tienes en tu servidor.
        $data['curso_info'] = $curso_info; 
        
        $data['evaluacion_existente'] = $this->modeloCursos->getEvaluacionCompletaPorFase($fase_id);
        
        require_once 'views/V_profesor_gestionar_evaluacion.php';
    }

    /**
     * Guarda los datos de una evaluación (preguntas y opciones de selección múltiple).
     */
    public function guardarEvaluacion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fase_id = $_POST['fase_id'];
            $titulo_evaluacion = $_POST['titulo_evaluacion'];
            $preguntas = $_POST['pregunta'] ?? [];
            $opciones = $_POST['opciones'] ?? [];
            $correctas = $_POST['correcta'] ?? [];

            $evaluacion_existente = $this->modeloCursos->getEvaluacionPorFaseId($fase_id);
            if ($evaluacion_existente) {
                $this->modeloCursos->eliminarEvaluacionExistente($evaluacion_existente['id']);
            }

            $evaluacion_id = $this->modeloCursos->crearEvaluacion($fase_id, $titulo_evaluacion);

            if(!empty($preguntas)){
                foreach ($preguntas as $orden => $texto_pregunta) {
                    if (!empty($texto_pregunta) && isset($opciones[$orden])) {
                        $pregunta_id = $this->modeloCursos->crearPregunta($evaluacion_id, $texto_pregunta, $orden + 1);
                        $opciones_pregunta = $opciones[$orden];
                        foreach ($opciones_pregunta as $idx_opcion => $texto_opcion) {
                            if (!empty($texto_opcion)) {
                                $es_correcta = (isset($correctas[$orden]) && $correctas[$orden] == $idx_opcion) ? 1 : 0;
                                $this->modeloCursos->crearOpcion($pregunta_id, $texto_opcion, $es_correcta);
                            }
                        }
                    }
                }
            }

            $_SESSION['mensaje'] = "Evaluación guardada con éxito.";
            $fase_info = $this->modeloCursos->getFasePorId($fase_id);
            header('Location: index.php?c=profesor&a=gestionarCurso&id=' . $fase_info['curso_id']);
            exit();
        }
    }      
    
    /**
     * Procesa la eliminación de un recurso.
     */
    public function eliminarRecurso() {
        $recurso_id = $_GET['id'] ?? 0;
        $curso_id = $_GET['curso_id'] ?? 0;
        
        $recurso = $this->modeloCursos->getRecursoPorId($recurso_id);
        if ($recurso) {
            if (file_exists($recurso['ruta_archivo'])) {
                unlink($recurso['ruta_archivo']);
            }
            $this->modeloCursos->eliminarRecurso($recurso_id);
            $_SESSION['mensaje'] = "Recurso eliminado con éxito.";
        } else {
            $_SESSION['error'] = "No se encontró el recurso a eliminar.";
        }
        header('Location: index.php?c=profesor&a=gestionarCurso&id=' . $curso_id);
        exit();
    }

    /**
     * Procesa la creación de una nueva clase.
     */
    public function crearClase() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $curso_id = $_POST['curso_id'];
            $datos = [
                'fase_id' => $_POST['fase_id'],
                'titulo' => $_POST['titulo_clase'],
                'profesor_tipo_doc' => $_SESSION['usuario_tipo_documento'],
                'profesor_num_doc' => $_SESSION['usuario_numero_documento']
            ];
            
            $exito = $this->modeloCursos->crearClase($datos);

            if ($exito) {
                $_SESSION['mensaje'] = "Clase creada con éxito.";
            } else {
                $_SESSION['error'] = "No se pudo crear la clase.";
            }
            
            header('Location: index.php?c=profesor&a=gestionarCurso&id=' . $curso_id);
            exit();
        }
    }

    /**
     * Procesa la creación de una nueva fase (módulo) para un curso.
     */
    public function crearFase() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $curso_id = $_POST['curso_id'];
            $nombre_fase = $_POST['nombre_fase'];
            
            $exito = $this->modeloCursos->crearFase($curso_id, $nombre_fase);

            if ($exito) {
                $_SESSION['mensaje'] = "Módulo creado con éxito.";
            } else {
                $_SESSION['error'] = "No se pudo crear el módulo.";
            }
            header('Location: index.php?c=profesor&a=gestionarCurso&id=' . $curso_id);
            exit();
        }
    }

    /**
     * Procesa la subida de un nuevo recurso para una clase.
     */
    public function subirRecurso() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo'])) {
            $curso_id = $_POST['curso_id_redirect'];
            
            if ($_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
                $nombre_original = basename($_FILES['archivo']['name']);
                $nombre_unico = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $nombre_original);
                $ruta_destino = 'uploads/' . $nombre_unico;

                if (!file_exists('uploads')) {
                    mkdir('uploads', 0777, true);
                }

                if (move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_destino)) {
                    $datos = [
                        'clase_id' => $_POST['clase_id'],
                        'nombre_archivo' => $nombre_unico,
                        'nombre_original' => $nombre_original,
                        'tipo_archivo' => $_FILES['archivo']['type'],
                        'tamano_archivo' => $_FILES['archivo']['size'],
                        'ruta_archivo' => $ruta_destino,
                        'tipo_recurso' => $_POST['tipo_recurso'],
                        'descripcion' => $_POST['descripcion'] ?? ''
                    ];
                    
                    if ($this->modeloCursos->insertarRecurso($datos)) {
                        $_SESSION['mensaje'] = "Recurso subido con éxito.";
                    } else {
                        $_SESSION['error'] = "Error al guardar el recurso en la base de datos.";
                    }
                } else {
                    $_SESSION['error'] = "Error al mover el archivo.";
                }
            } else {
                $_SESSION['error'] = "Error al subir el archivo. Código: " . $_FILES['archivo']['error'];
            }
            header('Location: index.php?c=profesor&a=gestionarCurso&id=' . $curso_id);
            exit();
        }
    }
    
    // --- FUNCIONES ADICIONALES ---

    public function verEstudiantes() {
        $curso_id = $_GET['id'] ?? 0;
        if (empty($curso_id)) {
            $data['titulo'] = 'Seleccionar Curso para Ver Estudiantes';
            $data['cursos'] = $this->modeloCursos->getCursosPorProfesor($_SESSION['usuario_numero_documento']);
            require_once 'views/V_profesor_listar_cursos.php';
            return;
        }

        $data['curso'] = $this->modeloCursos->getCursoPorId($curso_id);

        if (!$data['curso'] || $data['curso']['profesor_numero_documento'] != $_SESSION['usuario_numero_documento']) {
            $_SESSION['error'] = "Acceso denegado o curso no encontrado.";
            header('Location: index.php?c=profesor&a=listarCursos');
            exit();
        }
        
        $data['titulo'] = 'Estudiantes de: ' . htmlspecialchars($data['curso']['nombre']);
        $data['estudiantes'] = $this->modeloCursos->getEstudiantesConProgresoPorCurso($curso_id);
        
        require_once 'views/V_profesor_ver_estudiantes.php';
    }

    public function calificaciones() {
        $data['titulo'] = 'Calificaciones';
        $data['cursos'] = $this->modeloCursos->getCursosPorProfesor($_SESSION['usuario_numero_documento']);
        require_once 'views/V_profesor_calificaciones_cursos.php';
    }

    public function calificarCurso() {
        $curso_id = $_GET['id_curso'] ?? 0;
        $data['curso'] = $this->modeloCursos->getCursoPorId($curso_id);
        if (!$data['curso'] || $data['curso']['profesor_numero_documento'] != $_SESSION['usuario_numero_documento']) {
            $_SESSION['error'] = "Acceso denegado o curso no encontrado.";
            header('Location: index.php?c=profesor&a=calificaciones');
            exit();
        }
        $data['titulo'] = 'Calificar: ' . htmlspecialchars($data['curso']['nombre']);
        $data['entregas'] = $this->modeloCursos->getEntregasParaCalificar($curso_id);
        require_once 'views/V_profesor_calificar_curso.php';
    }

    public function guardarCalificacion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_entrega = $_POST['id_entrega'];
            $nota = $_POST['nota'];
            $id_curso = $_POST['id_curso'];
            $this->modeloCursos->guardarCalificacion($id_entrega, $nota, $_SESSION['usuario_tipo_documento'], $_SESSION['usuario_numero_documento']);
            header('Location: index.php?c=profesor&a=calificarCurso&id_curso=' . $id_curso);
            exit();
        }
    }
}
?>