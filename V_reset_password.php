<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Establecer Nueva Contraseña - Vertical Safe</title>
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
                <h1 class="text-3xl font-bold mb-4">Casi listo...</h1>
                <p class="text-gray-300">Establece una nueva contraseña segura para proteger tu cuenta y continuar con tu formación.</p>
            </div>
        </div>

        <!-- Columna Derecha: Formulario -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                     <h2 class="text-3xl font-bold text-gray-800">Establecer Nueva Contraseña</h2>
                     <p class="text-gray-500 mt-2">Elige una contraseña segura y fácil de recordar.</p>
                </div>

                <?php if (isset($data['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Error</p>
                        <p><?php echo htmlspecialchars($data['error']); ?></p>
                    </div>
                <?php endif; ?>

                <form action="index.php?c=usuarios&a=establecerPassword" method="POST" class="space-y-6">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($data['token']); ?>">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                        <input type="password" name="password" id="password" required class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                    </div>
                    <div>
                        <label for="confirmar_password" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                        <input type="password" name="confirmar_password" id="confirmar_password" required class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none border-brand-lime">
                    </div>
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-bold text-white bg-brand-lime hover:bg-brand-lime-dark focus:outline-none">
                            Guardar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
