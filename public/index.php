<?php
// public/index.php
// Todas las solicitudes pasan por este único punto de entrada.

session_start();

// --- Inclusión de archivos esenciales ---
require_once __DIR__ . '/../app/config/db_config.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Session.php';
require_once __DIR__ . '/../app/helpers/functions.php';

// --- Inclusión de Modelos y Controladores ---
require_once __DIR__ . '/../app/models/UserModel.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
// ¡CORREGIDO! El nombre del archivo ahora es HomeController.php (singular)
require_once __DIR__ . '/../app/controllers/HomeController.php';


// --- Configuración de la URL Base ---
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path_for_links = rtrim(str_replace('/public/index.php', '', $script_name), '/');
define('BASE_URL', $protocol . '://' . $host . $base_path_for_links);


// --- Inicialización del Enrutador ---
$router = new Router(); 

// --- DEFINICIÓN DE RUTAS ---

// Rutas de Autenticación
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// ¡AÑADIDO! Rutas para recuperación de contraseña
$router->get('/forgot-password', 'AuthController@showForgotPasswordForm');
$router->post('/forgot-password', 'AuthController@handleForgotPassword');
$router->get('/reset-password', 'AuthController@showResetPasswordForm');
$router->post('/reset-password', 'AuthController@handleResetPassword');

// Rutas del Panel de Control (Dashboard)
$router->get('/dashboard', 'DashboardController@index');
$router->get('/dashboard/admin', 'DashboardController@admin');
$router->get('/dashboard/profesor', 'DashboardController@profesor');
$router->get('/dashboard/estudiante', 'DashboardController@estudiante');

// Ruta principal
$router->get('/', 'HomeController@index');


// --- Despachar la solicitud ---
$router->dispatch();
?>
