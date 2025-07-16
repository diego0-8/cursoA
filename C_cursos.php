<?php
require_once 'models/M_cursos.php';

class ControladorCursos {

    private $modelo;

    public function __construct() {
        // Proteger todas las acciones de este controlador
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_rol']) || !in_array($_SESSION['usuario_rol'], ['profesor', 'administrador'])) {
            header('Location: index.php?c=login');
            exit();
        }
        $this->modelo = new ModeloCursos();
    }

    /**
     * MÉTODO CORREGIDO: Ahora muestra los módulos (fases) de un curso específico.
     * Se ha modificado para que coincida con la vista V_profesor_cursos.php
     * DEBES LLAMARLO CON UN ID EN LA URL, ej: index.php?c=cursos&id=123
     */
    public function index() {
        $curso_id = $_GET['id'] ?? 0;

        // Si no se proporciona un ID de curso, llamamos a la función que lista todos los cursos.
        if (empty($curso_id)) {
            $this->listarCursos();
            return;
        }

        // Obtenemos los datos del curso específico
        $data['curso'] = $this->modelo->getCursoPorId($curso_id);

        // Verificamos si el curso existe
        if (!$data['curso']) {
            die("Error: El curso no fue encontrado o no tienes permiso para verlo.");
        }

        $data['titulo'] = 'Módulos de: ' . htmlspecialchars($data['curso']['nombre']);
        $data['nombre_usuario'] = $_SESSION['usuario_nombre_completo'];
        
        // --- LA CORRECCIÓN PRINCIPAL ESTÁ AQUÍ ---
        // Se asume que tienes un método en tu modelo para obtener las fases.
        // ¡¡DEBES CREAR EL MÉTODO getFasesPorCurso() EN TU MODELO M_cursos.php!!
        $fases = $this->modelo->getFasesPorCurso($curso_id);

        // Si el modelo devuelve null (sin fases), lo convertimos en un array vacío
        // para evitar el error fatal en la vista.
        $data['fases'] = $fases ? $fases : [];

        // Ahora, los datos ($data['curso'] y $data['fases']) coinciden con lo que la vista espera.
        require_once 'views/V_profesor_cursos.php';
    }

    /**
     * NUEVO MÉTODO: Contiene la lógica original de 'index' para no perderla.
     * Muestra la lista de todos los cursos del profesor.
     */
    public function listarCursos() {
        $data['titulo'] = 'Mis Cursos';
        $data['nombre_usuario'] = $_SESSION['usuario_nombre_completo'];
        $data['cursos'] = $this->modelo->getCursosPorProfesor(
            $_SESSION['usuario_tipo_documento'], 
            $_SESSION['usuario_numero_documento']
        );
        // NOTA: Deberías tener una vista diferente para esto, que sepa cómo mostrar
        // el array $data['cursos']. Por ejemplo: 'V_profesor_listar_cursos.php'
        require_once 'views/V_profesor_listar_cursos.php'; // Cambia esto por tu vista de lista de cursos
    }

    

    /**
     * MÉTODO MEJORADO: Ahora también guarda la descripción del recurso.
     */
    public function subirRecurso() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo'])) {
            $clase_id = $_POST['clase_id'];
            $tipo_recurso = $_POST['tipo_recurso'];
            $descripcion = $_POST['descripcion'] ?? ''; // Captura la descripción (puede estar vacía)
            $archivo = $_FILES['archivo'];

            if ($archivo['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = "Error al subir el archivo. Código: " . $archivo['error'];
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            $nombre_original = basename($archivo['name']);
            // Limpia el nombre del archivo para evitar problemas de ruta
            $nombre_unico = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $nombre_original);
            $ruta_destino = 'uploads/' . $nombre_unico;

            // Asegurarse de que el directorio de subidas exista
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                $datos_recurso = [
                    'clase_id' => $clase_id,
                    'nombre_archivo' => $nombre_unico,
                    'nombre_original' => $nombre_original,
                    'tipo_archivo' => $archivo['type'],
                    'tamaño_archivo' => $archivo['size'],
                    'ruta_archivo' => $ruta_destino,
                    'tipo_recurso' => $tipo_recurso,
                    'descripcion' => $descripcion // Se añade la descripción
                ];
                $this->modelo->insertarRecurso($datos_recurso);
                $_SESSION['mensaje'] = "Archivo subido con éxito.";
            } else {
                $_SESSION['error'] = "No se pudo mover el archivo a su destino. Verifica los permisos de la carpeta 'uploads'.";
            }
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>
