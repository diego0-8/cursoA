<?php
// app/helpers/functions.php
// Funciones de ayuda generales para la aplicación

// Función para sanitizar datos de entrada
function sanitizeInput($data) {
    // Elimina espacios en blanco del principio y final
    $data = trim($data);
    // Elimina barras invertidas
    $data = stripslashes($data);
    // Convierte caracteres especiales a entidades HTML para prevenir XSS
    $data = htmlspecialchars($data);
    return $data;
}

// Función para redirigir a una URL
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Función para verificar si el usuario está autenticado
function isAuthenticated() {
    return Session::exists('user_logged_in') && Session::get('user_logged_in') === true;
}

// Función para verificar el rol del usuario
function hasRole($required_role) {
    if (isAuthenticated()) {
        $user_role = Session::get('user_role');
        return $user_role === $required_role;
    }
    return false;
}

// Función para verificar si el usuario tiene al menos un rol de los requeridos
function hasAnyRole($required_roles = []) {
    if (isAuthenticated()) {
        $user_role = Session::get('user_role');
        return in_array($user_role, $required_roles);
    }
    return false;
}

// Función para proteger una página por rol
function protectPage($required_roles = []) {
    if (!isAuthenticated()) {
        Session::setFlash('error', 'Debes iniciar sesión para acceder a esta página.');
        redirect(BASE_URL . '/login');
    }

    if (!empty($required_roles) && !hasAnyRole($required_roles)) {
        Session::setFlash('error', 'No tienes permisos para acceder a esta página.');
        redirect(BASE_URL . '/dashboard'); // Redirige al dashboard si no tiene el rol, o a una página de acceso denegado
    }
}
?>