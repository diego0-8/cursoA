<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Estudiante - Vertical Safe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .bg-brand-lime { background-color: #96c11f; }
        .text-brand-lime { color: #96c11f; }
        .hover\:bg-brand-lime-dark:hover { background-color: #82a81b; }
        .header-link { transition: color 0.2s; }
        .header-link:hover { color: #96c11f; }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header y Navegación -->
    <header class="bg-white shadow-md sticky top-0 z-20">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php?c=estudiante" class="text-2xl font-bold text-brand-dark-green">
                    <span class="text-brand-lime">VERTICAL</span> SAFE
                </a>
                <nav class="flex items-center space-x-6">
                    <a href="index.php?c=estudiante" class="header-link text-brand-lime font-bold">Cursos Disponibles</a>
                    <a href="index.php?c=estudiante&a=misCursos" class="header-link text-gray-600 font-semibold">Mis Cursos</a>
                    <span class="text-gray-300">|</span>
                    <span class="font-medium text-gray-800">Hola, <?php echo htmlspecialchars(explode(' ', $_SESSION['usuario_nombre_completo'])[0]); ?></span>
                    <a href="index.php?c=login&a=logout" class="text-red-500 hover:text-red-700" title="Cerrar Sesión">
                        <i class="fas fa-sign-out-alt fa-lg"></i>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="container mx-auto px-6 py-10">
        <!-- Mensajes de feedback -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg" role="alert"><?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Cursos Disponibles para Inscripción</h1>
            <p class="text-gray-500">Explora nuestra oferta académica y únete a un nuevo curso.</p>
        </header>

        <?php if (empty($data['cursos_disponibles'])): ?>
            <div class="text-center p-12 bg-white rounded-lg shadow-md">
                <i class="fas fa-check-circle text-5xl text-brand-lime"></i>
                <h3 class="mt-4 text-xl font-semibold text-gray-700">¡Estás al día!</h3>
                <p class="text-gray-500 mt-2">No hay nuevos cursos disponibles o ya has solicitado la inscripción a todos. ¡Bien hecho!</p>
                <a href="index.php?c=estudiante&a=misCursos" class="mt-6 inline-block bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-2 px-4 rounded-lg transition-colors">Ir a Mis Cursos</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($data['cursos_disponibles'] as $curso): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col transition-transform hover:-translate-y-2">
                        <div class="p-6 flex-grow">
                            <h2 class="text-xl font-bold text-brand-dark-green mb-2"><?php echo htmlspecialchars($curso['nombre']); ?></h2>
                            <p class="text-sm text-gray-500 mb-4">
                                <i class="fas fa-user-tie mr-2 text-gray-400"></i>Profesor: <?php echo htmlspecialchars($curso['profesor_nombre_completo'] ?? 'No asignado'); ?>
                            </p>
                            <p class="text-gray-600 text-sm flex-grow"><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                        </div>
                        <div class="p-4 bg-gray-50 border-t">
                             <a href="index.php?c=estudiante&a=solicitarInscripcion&id_curso=<?php echo $curso['id']; ?>" class="block w-full text-center bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                                <i class="fas fa-plus-circle mr-2"></i>Solicitar Inscripción
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
