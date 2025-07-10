<?php
// app/views/auth/forgot_password.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <!-- La ruta al CSS ahora usa la URL base para ser consistente -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Recuperar Contraseña</h2>
        
        <?php
        // Muestra mensajes de error o información si existen en la sesión.
        if (isset($_SESSION['error_message'])) {
            echo '<p class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
            unset($_SESSION['error_message']);
        }
        if (isset($_SESSION['info_message'])) {
            echo '<p class="info-message">' . htmlspecialchars($_SESSION['info_message']) . '</p>';
            unset($_SESSION['info_message']);
        }
        ?>

        <!-- ¡ACCIÓN CORREGIDA! El formulario ahora envía los datos a la ruta /forgot-password -->
        <form action="<?= BASE_URL ?>/forgot-password" method="post">
            <div class="input-group">
                <label for="email">Ingresa tu correo electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn">Enviar Enlace de Recuperación</button>
        </form>
        <div class="links">
            <!-- ¡ENLACE CORREGIDO! El enlace ahora apunta a la ruta /login -->
            <a href="<?= BASE_URL ?>/login">Volver a Iniciar Sesión</a>
        </div>
    </div>
</body>
</html>
