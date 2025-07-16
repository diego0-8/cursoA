<?php
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

    public function gestionarCurso() {
        $curso_id = $_GET['id'] ?? 0;
        $data['curso'] = $this->modeloCursos->getCursoPorId($curso_id);
        if (!$data['curso'] || $data['curso']['profesor_numero_documento'] != $_SESSION['usuario_numero_documento']) {
            die("Acceso denegado o curso no encontrado.");
        }
        $data['titulo'] = 'Gestionar: ' . htmlspecialchars($data['curso']['nombre']);
        $data['fases'] = $this->modeloCursos->getFasesPorCurso($curso_id);
        require_once 'views/V_profesor_cursos.php';
    }

    public function actualizarEnlaceEvaluacion() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $curso_id = $_POST['curso_id'];
        $enlace = $_POST['enlace_evaluacion'];
        
        // Llama a la nueva función del modelo
        $this->modeloCursos->actualizarEnlaceEvaluacion($curso_id, $enlace);
        
        $_SESSION['mensaje'] = "Enlace de evaluación actualizado con éxito.";
        // Redirige de vuelta a la página de gestión del curso
        header('Location: index.php?c=profesor&a=gestionarCurso&id=' . $curso_id);
        exit();
    }
}



    public function verEstudiantes() {
        $curso_id = $_GET['id'] ?? 0;

        // Si no se ha proporcionado un ID de curso desde la URL...
        if (empty($curso_id)) {
            // ...le mostramos al profesor su lista de cursos para que elija uno.
            $data['titulo'] = 'Seleccionar Curso para Ver Estudiantes';
            $data['cursos'] = $this->modeloCursos->getCursosPorProfesor($_SESSION['usuario_numero_documento']);
            // Reutilizamos la vista que ya lista los cursos.
            require_once 'views/V_profesor_listar_cursos.php';
            return; // Importante: detenemos la ejecución aquí.
        }

        // Si sí se proporcionó un ID, continuamos como antes.
        $data['curso'] = $this->modeloCursos->getCursoPorId($curso_id);

        if (!$data['curso'] || $data['curso']['profesor_numero_documento'] != $_SESSION['usuario_numero_documento']) {
            die("Acceso denegado o curso no encontrado.");
        }
        
        $data['titulo'] = 'Estudiantes de: ' . htmlspecialchars($data['curso']['nombre']);
        $data['estudiantes'] = $this->modeloCursos->getEstudiantesConProgresoPorCurso($curso_id);
        
        require_once 'views/V_profesor_ver_estudiantes.php';
    }

    public function crearModulo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $curso_id = $_POST['curso_id'];
            $nombre_fase = $_POST['nombre_modulo'];
            $this->modeloCursos->crearFase($curso_id, $nombre_fase);
            $_SESSION['mensaje'] = "Módulo creado con éxito.";
            header('Location: index.php?c=profesor&a=gestionarCurso&id=' . $curso_id);
            exit();
        }
    }

    /**
     * NUEVO: Muestra la lista de cursos para que el profesor elija cuál calificar.
     */
    public function calificaciones() {
        $data['titulo'] = 'Calificaciones';
        $data['cursos'] = $this->modeloCursos->getCursosPorProfesor($_SESSION['usuario_numero_documento']);
        require_once 'views/V_profesor_calificaciones_cursos.php';
    }

     /**
     * NUEVO: Muestra la interfaz para calificar las entregas de un curso.
     */
    public function calificarCurso() {
        $curso_id = $_GET['id_curso'] ?? 0;
        $data['curso'] = $this->modeloCursos->getCursoPorId($curso_id);
        if (!$data['curso'] || $data['curso']['profesor_numero_documento'] != $_SESSION['usuario_numero_documento']) {
            die("Acceso denegado o curso no encontrado.");
        }
        $data['titulo'] = 'Calificar: ' . htmlspecialchars($data['curso']['nombre']);
        $data['entregas'] = $this->modeloCursos->getEntregasParaCalificar($curso_id);
        require_once 'views/V_profesor_calificar_curso.php';
    }

     /**
     * NUEVO: Guarda la calificación de una entrega.
     */
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
    
    /**
     * NUEVO: Guarda una observación para un estudiante en un curso.
     */
    public function guardarObservacion() {
        // Esta función requeriría una nueva tabla o campo en la tabla 'inscripciones'
        // Por ahora, solo redirige.
        $id_curso = $_POST['id_curso'];
        $_SESSION['mensaje'] = "Observación guardada (simulación).";
        header('Location: index.php?c=profesor&a=verEstudiantes&id=' . $id_curso);
        exit();
    }

    

    /**
     * NUEVO: Procesa la eliminación de un recurso.
     */
    public function eliminarRecurso() {
        $recurso_id = $_GET['id'] ?? 0;
        $curso_id = $_GET['curso_id'] ?? 0;
        
        $recurso = $this->modeloCursos->getRecursoPorId($recurso_id);
        if ($recurso) {
            // Eliminar archivo físico del servidor
            if (file_exists($recurso['ruta_archivo'])) {
                unlink($recurso['ruta_archivo']);
            }
            // Eliminar registro de la base de datos
            $this->modeloCursos->eliminarRecurso($recurso_id);
            $_SESSION['mensaje'] = "Recurso eliminado con éxito.";
        } else {
            $_SESSION['error'] = "No se encontró el recurso a eliminar.";
        }
        header('Location: index.php?c=profesor&a=gestionarCurso&id=' . $curso_id);
        exit();
    }


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
                // Array de datos completo y explícito
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
}
?>

