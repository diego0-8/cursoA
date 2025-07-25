<?php 

require_once 'models/M_usuarios.php';
require_once 'models/M_auditor.php';

class ControladorAuditor {

    private $modeloUsuarios;
    private $modeloAuditor;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'auditor') {
            header('Location: index.php?c=login');
            exit();
        }
        $this->modeloUsuarios = new ModeloUsuarios();
        $this->modeloAuditor = new ModeloAuditor();
    }

    public function index() {
        $usuarios_por_pagina = 10;
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        if ($pagina_actual < 1) $pagina_actual = 1;

        $total_usuarios = $this->modeloUsuarios->contarUsuarios($busqueda);
        $total_paginas = ceil($total_usuarios / $usuarios_por_pagina);
        $offset = ($pagina_actual - 1) * $usuarios_por_pagina;

        $data['usuarios'] = $this->modeloUsuarios->getUsuariosPaginados($busqueda, $usuarios_por_pagina, $offset);
        
        $data['titulo'] = 'Panel de Auditoría';
        $data['busqueda'] = $busqueda;
        $data['pagina_actual'] = $pagina_actual;
        $data['total_paginas'] = $total_paginas;

        require_once 'views/V_auditor_dashboard.php';
    }

    public function verActividad() {
        $tipo_doc = $_GET['tipo_doc'] ?? '';
        $num_doc = $_GET['num_doc'] ?? '';

        if (empty($tipo_doc) || empty($num_doc)) {
            header('Location: index.php?c=auditor');
            exit();
        }

        $data['usuario'] = $this->modeloUsuarios->buscarUsuarioPorId($tipo_doc, $num_doc);

        if (!$data['usuario']) {
            header('Location: index.php?c=auditor');
            exit();
        }

        if ($data['usuario']['rol'] == 'estudiante') {
            $data['actividad'] = $this->modeloAuditor->getActividadEstudiante($tipo_doc, $num_doc);
        } elseif ($data['usuario']['rol'] == 'profesor') {
            $data['actividad'] = $this->modeloAuditor->getActividadProfesor($tipo_doc, $num_doc);
        } else {
            $data['actividad'] = null;
        }

        $data['titulo'] = 'Detalle de Actividad';
        require_once 'views/V_auditor_detalle_usuario.php';
    }

    public function generarReporte() {
        $fecha_inicio = $_POST['fecha_inicio'] ?? null;
        $fecha_fin = $_POST['fecha_fin'] ?? null;
        $roles = $_POST['roles'] ?? [];

        // VALIDACIÓN 1: Verificar que se haya seleccionado al menos un rol.
        if (empty($roles)) {
            $_SESSION['error'] = "Debes seleccionar al menos un rol para generar el reporte.";
            header('Location: index.php?c=auditor');
            exit();
        }

        // VALIDACIÓN 2: Si se usa una fecha, se debe usar la otra.
        if (!empty($fecha_inicio) && empty($fecha_fin)) {
            $_SESSION['error'] = "Debes seleccionar una fecha de fin para completar el rango.";
            header('Location: index.php?c=auditor');
            exit();
        }
        if (empty($fecha_inicio) && !empty($fecha_fin)) {
            $_SESSION['error'] = "Debes seleccionar una fecha de inicio para completar el rango.";
            header('Location: index.php?c=auditor');
            exit();
        }

        $usuarios_agrupados = $this->modeloUsuarios->getUsuariosParaReporte($fecha_inicio, $fecha_fin, $roles);

        require('utils/fpdf/fpdf.php');

        $pdf = new FPDF('P', 'mm', 'A4');
        // ... (el resto del código para generar el PDF se mantiene igual)
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Reporte de Usuarios Registrados', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'Generado el: ' . date('d/m/Y H:i'), 0, 1, 'C');
        $pdf->Ln(10);

        if (empty($usuarios_agrupados)) {
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->Cell(0, 10, 'No se encontraron usuarios con los filtros seleccionados.', 0, 1, 'C');
        } else {
            foreach ($usuarios_agrupados as $rol => $usuarios) {
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell(0, 10, 'Rol: ' . ucfirst($rol), 0, 1, 'L', true);
                $pdf->Ln(2);

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(60, 7, 'Nombre Completo', 1);
                $pdf->Cell(65, 7, 'Email', 1);
                $pdf->Cell(40, 7, 'Documento', 1);
                $pdf->Cell(25, 7, 'Fecha Registro', 1);
                $pdf->Ln();

                $pdf->SetFont('Arial', '', 9);
                foreach ($usuarios as $usuario) {
                    $pdf->Cell(60, 6, utf8_decode($usuario['nombre'] . ' ' . $usuario['apellido']), 1);
                    $pdf->Cell(65, 6, $usuario['email'], 1);
                    $pdf->Cell(40, 6, $usuario['numero_documento'], 1);
                    $pdf->Cell(25, 6, date('d/m/Y', strtotime($usuario['fecha_creacion'])), 1);
                    $pdf->Ln();
                }
                $pdf->Ln(10);
            }
        }

        $pdf->Output('D', 'Reporte_Usuarios_' . date('Y-m-d') . '.pdf');
    }
}
?>