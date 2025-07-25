<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Plataforma de Cursos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Color personalizado extraído de la imagen */
        .bg-brand-dark-green {
            background-color: #1a3a3a; /* Un verde oscuro y profesional */
        }
        .bg-brand-lime {
            background-color: #96c11f; /* El verde lima del logo */
        }
        .hover\:bg-brand-lime-dark:hover {
            background-color: #82a81b; /* Un tono más oscuro para el hover */
        }
        .text-brand-lime {
            color: #96c11f;
        }
        .border-brand-lime:focus {
            border-color: #96c11f;
            box-shadow: 0 0 0 2px rgba(150, 193, 31, 0.5);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Columna Izquierda: Información y Branding -->
        <div class="hidden lg:flex w-1/2 bg-brand-dark-green items-center justify-center p-12 text-white flex-col">
            <div class="max-w-md text-center">
                <img src="img/vertical-safe.jpg" alt="Logo Vertical Safe" class="w-full h-auto object-cover rounded-lg shadow-2xl mb-8">
                <h1 class="text-3xl font-bold mb-4">¡Bienvenido al Curso de Trabajo Seguro en Alturas! Vertical Safe🧗‍♂️</h1>
                <p class="text-gray-300">Te damos la bienvenida a este espacio de aprendizaje diseñado para fortalecer tus conocimientos, habilidades y conciencia frente a uno de los trabajos más exigentes y riesgosos: el trabajo en alturas. <br>

Aquí aprenderás a: <br>
✅ Identificar equipos certificados y su correcto uso  <br>
✅ Reconocer los riesgos y factores de caída <br>
✅ Realizar inspecciones y mantenimiento del equipo <br>
✅ Aplicar normas y prácticas seguras en cada tarea <br>

👷‍♀️ La seguridad es responsabilidad de todos. Este curso no solo busca que cumplas con una normativa, sino que protejas tu vida y la de tus compañeros.</p>
            </div>
        </div>

        <!-- Columna Derecha: Formulario de Login -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                     <h2 class="text-3xl font-bold text-gray-800">Iniciar Sesión</h2>
                     <p class="text-gray-500 mt-2">Bienvenido de nuevo. Por favor, ingresa tus credenciales.</p>
                </div>

                <!-- Mensajes de Éxito o Error -->
                <?php if (isset($data['mensaje_exito'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Éxito</p>
                        <p><?php echo htmlspecialchars($data['mensaje_exito']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($data['error'])): ?>
                     <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Error</p>
                        <p><?php echo htmlspecialchars($data['error']); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form action="index.php?c=login&a=validar" method="POST" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <input type="password" name="password" id="password" required class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                    </div>
                    
                    
                    <!-- CAMBIO: Se añade el botón de consulta -->
                    <div class="flex justify-between items-center text-sm">
                        <a href="index.php?c=publico" class="font-medium text-gray-600 hover:text-brand-lime">
                            <i class="fas fa-search"></i> Consultar Certificado
                        </a>
                        <a href="index.php?c=usuarios&a=recuperar" class="font-medium text-brand-lime hover:underline">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-bold text-white bg-brand-lime hover:bg-brand-lime-dark focus:outline-none">
                            Ingresar
                        </button>
                    </div>
                </form>

                <div class="text-center mt-8 text-sm text-gray-600">
                    <p>¿No tienes una cuenta de estudiante? 
                        <a href="index.php?c=usuarios&a=registro" class="font-bold text-brand-lime hover:underline">Regístrate aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
