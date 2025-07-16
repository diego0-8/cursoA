<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Estudiante - Vertical Safe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .bg-brand-lime { background-color: #96c11f; }
        .text-brand-lime { color: #96c11f; }
        .hover\:bg-brand-lime-dark:hover { background-color: #82a81b; }
        .border-brand-lime:focus { border-color: #96c11f; box-shadow: 0 0 0 2px rgba(150, 193, 31, 0.5); }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Columna Izquierda: Información y Branding -->
        <div class="hidden lg:flex w-1/2 bg-brand-dark-green items-center justify-center p-12 text-white flex-col">
            <div class="max-w-md text-center">
                <img src="img/vertical-safe.jpg" alt="Logo Vertical Safe" class="w-full h-auto object-cover rounded-lg shadow-2xl mb-8">
                <h1 class="text-3xl font-bold mb-4">Únete a Nuestra Comunidad de Aprendizaje</h1>
                <p class="text-gray-300">Regístrate para acceder a cursos de alta calidad y obtener tu certificación en seguridad en alturas.</p>
            </div>
        </div>

        <!-- Columna Derecha: Formulario de Registro -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-lg">
                <div class="text-center mb-8">
                     <h2 class="text-3xl font-bold text-gray-800">Crear Cuenta de Estudiante</h2>
                     <p class="text-gray-500 mt-2">Completa tus datos para comenzar.</p>
                </div>

                <?php if (isset($data['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Error</p>
                        <p><?php echo htmlspecialchars($data['error']); ?></p>
                    </div>
                <?php endif; ?>

                <form action="index.php?c=usuarios&a=registrarEstudiante" method="POST" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="nombre" id="nombre" required class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                        </div>
                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido</label>
                            <input type="text" name="apellido" id="apellido" required class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                            <select name="tipo_documento" id="tipo_documento" required class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="TI">Tarjeta de Identidad</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PP">Pasaporte</option>
                            </select>
                        </div>
                        <div>
                            <label for="numero_documento" class="block text-sm font-medium text-gray-700">Número de Documento</label>
                            <input type="text" name="numero_documento" id="numero_documento" required class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input type="password" name="password" id="password" required class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                        </div>
                        <div>
                            <label for="confirmar_password" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                            <input type="password" name="confirmar_password" id="confirmar_password" required class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-bold text-white bg-brand-lime hover:bg-brand-lime-dark focus:outline-none">
                            Registrarme
                        </button>
                    </div>
                </form>

                <div class="text-center mt-6 text-sm text-gray-600">
                    <p>¿Ya tienes una cuenta? 
                        <a href="index.php?c=login" class="font-bold text-brand-lime hover:underline">Inicia sesión aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
