<?php
// app/controllers/DashboardController.php

// NO se necesitan 'require_once' ni 'Session::init()' aquí.
// public/index.php ya se encarga de cargar todos los helpers y de iniciar la sesión.

class DashboardController {

    // El constructor fue eliminado porque Session::init() es redundante.
    // session_start() en index.php es suficiente.

    /**
     * Método principal del dashboard.
     * Redirige al usuario a su dashboard específico según su rol.
     */
    public function index() {
        // La función protectPage() viene de helpers/functions.php
        protectPage(['administrador', 'profesor', 'estudiante']);

        $user_role = $_SESSION['user_role'] ?? null;

        switch ($user_role) {
            case 'administrador':
                // La función redirect() viene de helpers/functions.php
                redirect(BASE_URL . '/dashboard/admin');
                break;
            case 'profesor':
                redirect(BASE_URL . '/dashboard/profesor');
                break;
            case 'estudiante':
                redirect(BASE_URL . '/dashboard/estudiante');
                break;
            default:
                // Si por alguna razón el rol no existe, lo mandamos al login.
                redirect(BASE_URL . '/logout');
                break;
        }
    }

    /**
     * Muestra el dashboard para Administradores.
     */
    public function admin() {
        protectPage(['administrador']);

        // Se define el título que usará el header.php
        $page_title = "Panel de Administrador";
        
        // Se carga la vista, la cual internamente usa el header y el footer.
        require_once __DIR__ . '/../views/dashboard/admin.php';
    }

    /**
     * Muestra el dashboard para Profesores.
     */
    public function profesor() {
        protectPage(['profesor']);

        $page_title = "Panel de Profesor";
        require_once __DIR__ . '/../views/dashboard/profesor.php';
    }

    /**
     * Muestra el dashboard para Estudiantes.
     */
    public function estudiante() {
        protectPage(['estudiante']);

        $page_title = "Panel de Estudiante";
        require_once __DIR__ . '/../views/dashboard/estudiante.php';
    }
}