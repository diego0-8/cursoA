<?php
// app/controllers/AuthController.php

class AuthController {
    private $userModel;

    public function __construct() {
        $pdo = connectDB();
        // Asumiendo que UserModel.php ya fue incluido en index.php
        $this->userModel = new UserModel($pdo);
    }

    public function showLoginForm() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/login');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['numero_documento']; // Usando la clave primaria correcta
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_role'] = $user['rol'];
            unset($_SESSION['error_message']);
            redirect(BASE_URL . '/dashboard');
        } else {
            $_SESSION['error_message'] = "Correo o contraseña incorrectos.";
            redirect(BASE_URL . '/login');
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        redirect(BASE_URL . '/login');
    }

    // --- MÉTODOS AÑADIDOS ---

    public function showForgotPasswordForm() {
        require_once __DIR__ . '/../views/auth/forgot_password.php';
    }

    public function handleForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            // Aquí iría tu lógica para generar un token, guardarlo en la BD
            // y enviar un correo al usuario.
            // Por ahora, simularemos un mensaje de éxito.
            $_SESSION['info_message'] = "Si tu correo está en nuestro sistema, recibirás un enlace para restablecer tu contraseña.";
            redirect(BASE_URL . '/forgot-password');
        }
    }

    public function showResetPasswordForm() {
        // Aquí verificarías que el token de la URL es válido.
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            // Manejar token inválido
            redirect(BASE_URL . '/login');
        }
        require_once __DIR__ . '/../views/auth/reset_password.php';
    }

    public function handleResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            // Aquí iría tu lógica para validar el token, actualizar la contraseña
            // y luego redirigir al login con un mensaje de éxito.
            $_SESSION['info_message'] = "Tu contraseña ha sido actualizada con éxito.";
            redirect(BASE_URL . '/login');
        }
    }
}
