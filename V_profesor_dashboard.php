<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Profesor - Vertical Safe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .bg-brand-lime { background-color: #96c11f; }
        .text-brand-lime { color: #96c11f; }
        .sidebar-link { transition: background-color 0.2s, color 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background-color: #96c11f; color: #1a3a3a; }
        .sidebar-link.active i, .sidebar-link:hover i { color: #1a3a3a; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Barra Lateral de Navegación -->
        <aside class="w-64 bg-brand-dark-green text-white flex flex-col p-4">
            <div class="text-center py-4 mb-5 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-brand-lime">VERTICAL SAFE</h1>
                <p class="text-xs text-gray-400">Panel de Profesor</p>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php?c=profesor" class="sidebar-link active flex items-center py-2.5 px-4 rounded-lg font-bold"><i class="fas fa-tachometer-alt fa-fw mr-3"></i>Dashboard</a>
                <a href="index.php?c=profesor&a=listarCursos" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-book-open fa-fw mr-3"></i>Mis Cursos</a>
                <a href="index.php?c=profesor&a=calificaciones" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-check-double fa-fw mr-3"></i>Calificaciones</a>
            </nav>
            <div class="mt-auto">
                 <a href="index.php?c=login&a=logout" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 p-10 overflow-y-auto">
            <header class="mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Bienvenido, <?php echo htmlspecialchars(explode(' ', $_SESSION['usuario_nombre_completo'])[0]); ?></h1>
                <p class="text-gray-500">Aquí tienes un resumen de tu actividad en la plataforma.</p>
            </header>

            <!-- Tarjetas de Acceso Rápido -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <a href="index.php?c=profesor&a=listarCursos" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-4 rounded-full mr-4">
                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Mis Cursos</h3>
                        <p class="text-gray-600 text-sm">Gestiona contenido y estudiantes.</p>
                    </div>
                </a>
                <a href="index.php?c=profesor&a=calificaciones" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow flex items-center">
                    <div class="bg-green-100 text-green-600 p-4 rounded-full mr-4">
                        <i class="fas fa-tasks fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Calificaciones</h3>
                        <p class="text-gray-600 text-sm">Revisa y califica las entregas.</p>
                    </div>
                </a>
                
                <a href="index.php?c=profesor&a=verEstudiantes" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow flex items-center">
                    <div class="bg-purple-100 text-purple-600 p-4 rounded-full mr-4">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Ver Estudiantes</h3>
                        <p class="text-gray-600 text-sm">Consulta el progreso general.</p>
                    </div>
                </a>
            </div>
        </main>
    </div>
</body>
</html>