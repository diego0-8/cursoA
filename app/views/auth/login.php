<?php
// app/views/auth/login.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <!-- Ruta corregida usando BASE_URL -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css"> 
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<p class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
            unset($_SESSION['error_message']);
        }
        ?>

        <!-- ¡ACCIÓN CORREGIDA! Ahora usa la ruta del router. -->
        <form action="<?= BASE_URL ?>/login" method="post">
            <div class="input-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Ingresar</button>
        </form>
        <div class="links">
            <!-- También corregimos este enlace -->
            <a href="<?= BASE_URL ?>/forgot-password">¿Olvidaste tu contraseña?</a>
        </div>
    </div>
</body>
</html>