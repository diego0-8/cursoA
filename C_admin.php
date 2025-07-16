<?php
// Se requieren ambos modelos, el de usuarios y el de admin.
require_once 'models/M_usuarios.php';
require_once 'models/M_admin.php';

class ControladorAdmin {

    private $modeloUsuarios;
    private $modeloAdmin;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'administrador') {
            header('Location: index.php?c=login');
            exit();
        }
        $this->modeloUsuarios = new ModeloUsuarios();
        $this->modeloAdmin = new ModeloAdmin();
    }

    public function index() {
        $data['titulo'] = 'Panel de Administrador';
        $data['nombre_usuario'] = $_SESSION['usuario_nombre_completo'];
        $data['estadisticas'] = $this->modeloAdmin->getEstadisticasDashboard();
        require_once 'views/V_admin_dashboard.php';
    }

    // --- Funciones para gestionar Usuarios ---

    public function gestionarUsuarios() {
        $usuarios_por_pagina = 5;
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        if ($pagina_actual < 1) $pagina_actual = 1;

        $total_usuarios = $this->modeloUsuarios->contarUsuarios($busqueda);
        $total_paginas = ceil($total_usuarios / $usuarios_por_pagina);
        $offset = ($pagina_actual - 1) * $usuarios_por_pagina;

        $data['usuarios'] = $this->modeloUsuarios->getUsuariosPaginados($busqueda, $usuarios_por_pagina, $offset);
        $data['titulo'] = 'Gestionar Usuarios';
        $data['nombre_usuario'] = $_SESSION['usuario_nombre_completo'];
        $data['pagina_actual'] = $pagina_actual;
        $data['total_paginas'] = $total_paginas;
        $data['busqueda'] = $busqueda;

        if (isset($_SESSION['mensaje'])) {
            $data['mensaje'] = $_SESSION['mensaje'];
            unset($_SESSION['mensaje']);
        }
        if (isset($_SESSION['error'])) {
            $data['error'] = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        require_once 'views/V_admin_gestionar_usuarios.php';
    }

    public function crearUsuario() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'];
            if (empty($password)) {
                $_SESSION['error'] = "El campo de contraseña no puede estar vacío.";
                header('Location: index.php?c=admin&a=gestionarUsuarios');
                exit();
            }
            $datos = [
                'tipo_documento' => $_POST['tipo_documento'],
                'numero_documento' => $_POST['numero_documento'],
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'] ?? null,
                'rol' => $_POST['rol'],
                'password_hash' => password_hash($password, PASSWORD_DEFAULT)
            ];
            $exito = $this->modeloUsuarios->crearUsuarioDesdeAdmin($datos);
            if ($exito) {
                $_SESSION['mensaje'] = "Usuario creado con éxito.";
            } else {
                $_SESSION['error'] = "No se pudo crear el usuario. El email o documento ya podría existir.";
            }
        }
        header('Location: index.php?c=admin&a=gestionarUsuarios');
        exit();
    }

    /**
     * CORREGIDO: Muestra el formulario para editar un usuario existente con verificación robusta.
     */
    public function editarUsuario() {
        $tipo_doc = $_GET['tipo_doc'] ?? '';
        $num_doc = $_GET['num_doc'] ?? '';

        if (empty($tipo_doc) || empty($num_doc)) {
            $_SESSION['error'] = "No se especificó un usuario para editar.";
            header('Location: index.php?c=admin&a=gestionarUsuarios');
            exit();
        }

        $usuario = $this->modeloUsuarios->buscarUsuarioPorId($tipo_doc, $num_doc);

        // Verificación más robusta para evitar la página en blanco
        if (empty($usuario) || !is_array($usuario)) {
            $_SESSION['error'] = "Usuario no encontrado o datos inválidos.";
            header('Location: index.php?c=admin&a=gestionarUsuarios');
            exit();
        }

        $data['titulo'] = 'Editar Usuario';
        $data['usuario'] = $usuario;

        require_once 'views/V_admin_editar_usuario.php';
    }

    /**
     * Procesa la actualización de los datos de un usuario.
     */
    public function actualizarUsuario() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'tipo_documento' => $_POST['tipo_documento'],
                'numero_documento' => $_POST['numero_documento'],
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'] ?? null,
                'rol' => $_POST['rol'],
                'password_hash' => null
            ];

            if (!empty($_POST['password'])) {
                $datos['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            $exito = $this->modeloUsuarios->actualizarUsuarioDesdeAdmin($datos);

            if ($exito) {
                $_SESSION['mensaje'] = "Usuario actualizado con éxito.";
            } else {
                $_SESSION['error'] = "No se pudo actualizar el usuario. El email podría estar duplicado.";
            }
        }
        header('Location: index.php?c=admin&a=gestionarUsuarios');
        exit();
    }


    public function cambiarEstadoUsuario() {
        $tipo_doc = $_GET['tipo_doc'] ?? '';
        $num_doc = $_GET['num_doc'] ?? '';
        if (empty($tipo_doc) || empty($num_doc)) {
            $_SESSION['error'] = "Faltan datos para realizar la operación.";
            header('Location: index.php?c=admin&a=gestionarUsuarios');
            exit();
        }
        $usuario = $this->modeloUsuarios->buscarUsuarioPorId($tipo_doc, $num_doc);
        if ($usuario) {
            if ($usuario['email'] === $_SESSION['usuario_email']) {
                $_SESSION['error'] = "No puedes deshabilitar tu propia cuenta.";
            } else {
                $nuevo_estado = ($usuario['estado_cuenta'] === 'activo') ? 'inactivo' : 'activo';
                $exito = $this->modeloUsuarios->cambiarEstado($tipo_doc, $num_doc, $nuevo_estado);
                if ($exito) {
                    $_SESSION['mensaje'] = "El estado del usuario ha sido actualizado correctamente.";
                } else {
                    $_SESSION['error'] = "No se pudo actualizar el estado del usuario.";
                }
            }
        } else {
            $_SESSION['error'] = "Usuario no encontrado.";
        }
        header('Location: index.php?c=admin&a=gestionarUsuarios');
        exit();
    }
    
    // --- Funciones para gestionar Cursos e Inscripciones ---

    public function gestionarCursos() {
        $data['titulo'] = 'Gestión de Cursos y Profesores';
        $cursos_por_pagina = 10;
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        if ($pagina_actual < 1) $pagina_actual = 1;

        $total_cursos = $this->modeloAdmin->contarCursos($busqueda);
        $total_paginas = ceil($total_cursos / $cursos_por_pagina);
        $offset = ($pagina_actual - 1) * $cursos_por_pagina;

        $data['cursos'] = $this->modeloAdmin->getCursosPaginados($busqueda, $cursos_por_pagina, $offset);
        $data['profesores'] = $this->modeloAdmin->getAllProfesores();
        
        $data['pagina_actual'] = $pagina_actual;
        $data['total_paginas'] = $total_paginas;
        $data['busqueda'] = $busqueda;
        
        if (isset($_SESSION['mensaje'])) {
            $data['mensaje'] = $_SESSION['mensaje'];
            unset($_SESSION['mensaje']);
        }
        if (isset($_SESSION['error'])) {
            $data['error'] = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        require_once 'views/V_admin_gestionar_cursos.php';
    }

    public function crearCurso() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['nombre'])) {
                $_SESSION['error'] = "El nombre del curso no puede estar vacío.";
            } else {
                $datos_curso = [
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'] ?? ''
                ];
                $nuevo_curso_id = $this->modeloAdmin->crearCurso($datos_curso);

                if ($nuevo_curso_id) {
                    $_SESSION['mensaje'] = "Curso creado con éxito.";
                } else {
                    $_SESSION['error'] = "No se pudo crear el curso.";
                }
            }
        }
        header('Location: index.php?c=admin&a=gestionarCursos');
        exit();
    }

    public function gestionarEstudiantesCurso() {
        $curso_id = $_GET['id'] ?? 0;
        $data['curso'] = $this->modeloAdmin->getCursoPorId($curso_id);

        if (!$data['curso']) {
            $_SESSION['error'] = "Curso no encontrado.";
            header('Location: index.php?c=admin&a=gestionarCursos');
            exit();
        }

        $data['titulo'] = 'Gestionar Estudiantes: ' . htmlspecialchars($data['curso']['nombre']);
        $data['busqueda'] = $_GET['busqueda'] ?? '';
        $data['filtro_estado'] = $_GET['filtro_estado'] ?? '';
        
        $data['estudiantes'] = $this->modeloAdmin->getEstudiantesPorCursoPaginados($curso_id, $data['busqueda'], $data['filtro_estado']);
        
        require_once 'views/V_admin_gestionar_estudiantes_curso.php';
    }

    public function aplicarAccionMasivaEstudiantes() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $curso_id = $_POST['curso_id'];
            $estudiantes_seleccionados = $_POST['estudiantes_seleccionados'] ?? [];
            $accion = $_POST['accion_masiva'] ?? '';

            if (empty($accion) || empty($estudiantes_seleccionados)) {
                $_SESSION['error'] = "Debes seleccionar una acción y al menos un estudiante.";
            } else {
                $filas_afectadas = $this->modeloAdmin->aplicarAccionMasiva($curso_id, $estudiantes_seleccionados, $accion);
                if ($filas_afectadas !== false) {
                    $_SESSION['mensaje'] = "Acción masiva aplicada a {$filas_afectadas} estudiantes.";
                } else {
                    $_SESSION['error'] = "Ocurrió un error al aplicar la acción masiva.";
                }
            }
            header('Location: index.php?c=admin&a=gestionarEstudiantesCurso&id=' . $curso_id);
            exit();
        }
    }

    public function actualizarCurso() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['id']) || empty($_POST['nombre'])) {
                $_SESSION['error'] = "Faltan datos para actualizar el curso.";
            } else {
                $profesor_documento = $_POST['profesor_documento'] ?? null;
                $profesor_tipo_doc = null;
                if (!empty($profesor_documento)) {
                    $profesor_data = $this->modeloAdmin->getProfesorPorDocumento($profesor_documento);
                    if ($profesor_data) {
                        $profesor_tipo_doc = $profesor_data['tipo_documento'];
                    }
                }

                $datos = [
                    'id' => $_POST['id'],
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'] ?? '',
                    'profesor_tipo_documento' => $profesor_tipo_doc,
                    'profesor_numero_documento' => $profesor_documento
                ];

                $exito = $this->modeloAdmin->actualizarCurso($datos);
                if ($exito) {
                    $_SESSION['mensaje'] = "Curso actualizado con éxito.";
                } else {
                    $_SESSION['error'] = "No se pudo actualizar el curso.";
                }
            }
        }
        header('Location: index.php?c=admin&a=gestionarCursos');
        exit();
    }

    public function eliminarCurso() {
        if (isset($_GET['id'])) {
            $curso_id = $_GET['id'];
            $resultado = $this->modeloAdmin->eliminarOInhabilitarCurso($curso_id);

            switch ($resultado) {
                case 'eliminado':
                    $_SESSION['mensaje'] = "El curso ha sido eliminado permanentemente.";
                    break;
                case 'inhabilitado':
                    $_SESSION['mensaje'] = "El curso ha sido inhabilitado porque tiene estudiantes inscritos.";
                    break;
                case 'error':
                    $_SESSION['error'] = "Ocurrió un error al procesar la solicitud.";
                    break;
            }
        } else {
            $_SESSION['error'] = "No se especificó un curso.";
        }

        header('Location: index.php?c=admin&a=gestionarCursos');
        exit();
    }

    public function gestionarInscripciones() {
        $data['titulo'] = 'Gestionar Solicitudes';
        $data['solicitudes'] = $this->modeloAdmin->getSolicitudesPendientes();
        require_once 'views/V_admin_gestionar_inscripciones.php';
    }

    public function procesarInscripcion() {
        $solicitud_id = $_GET['id'] ?? 0;
        $accion = $_GET['accion'] ?? '';

        if (empty($solicitud_id) || !in_array($accion, ['aprobar', 'rechazar'])) {
            $_SESSION['error'] = "Solicitud no válida.";
            header('Location: index.php?c=admin&a=gestionarInscripciones');
            exit();
        }

        $admin_tipo_doc = $_SESSION['usuario_tipo_documento'];
        $admin_num_doc = $_SESSION['usuario_numero_documento'];

        $exito = $this->modeloAdmin->procesarSolicitud($solicitud_id, $accion, $admin_tipo_doc, $admin_num_doc);

        if ($exito) {
            $_SESSION['mensaje'] = "La solicitud ha sido " . ($accion == 'aprobar' ? 'aprobada' : 'rechazada') . " con éxito.";
        } else {
            $_SESSION['error'] = "No se pudo procesar la solicitud.";
        }

        header('Location: index.php?c=admin&a=gestionarInscripciones');
        exit();
    }
}
?>