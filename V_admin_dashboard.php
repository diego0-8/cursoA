<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
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
                <p class="text-xs text-gray-400">Admin Panel</p>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php?c=admin" class="sidebar-link active flex items-center py-2.5 px-4 rounded-lg font-bold"><i class="fas fa-tachometer-alt fa-fw mr-3"></i>Dashboard</a>
                <a href="index.php?c=admin&a=gestionarUsuarios" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-users fa-fw mr-3"></i>Usuarios</a>
                <a href="index.php?c=admin&a=gestionarCursos" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-book-open fa-fw mr-3"></i>Cursos</a>
                <a href="index.php?c=admin&a=gestionarInscripciones" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-clipboard-check fa-fw mr-3"></i>Inscripciones</a>
                <button class="">rojito</button>
            </nav>
            <div class="mt-auto">
                 <a href="index.php?c=login&a=logout" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 p-10 overflow-y-auto">
            <header class="mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Bienvenido, <?php echo htmlspecialchars(explode(' ', $_SESSION['usuario_nombre_completo'])[0]); ?></h1>
                <p class="text-gray-500">Resumen general de la plataforma.</p>
            </header>

            <!-- Tarjetas de Resumen con Datos Reales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total de Usuarios</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $data['estadisticas']['total_usuarios'] ?? '0'; ?></p>
                    </div>
                    <div class="bg-blue-100 text-blue-600 p-4 rounded-full">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Cursos Activos</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $data['estadisticas']['cursos_activos'] ?? '0'; ?></p>
                    </div>
                    <div class="bg-green-100 text-green-600 p-4 rounded-full">
                        <i class="fas fa-book-open fa-2x"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Inscripciones Pendientes</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $data['estadisticas']['inscripciones_pendientes'] ?? '0'; ?></p>
                    </div>
                    <div class="bg-yellow-100 text-yellow-600 p-4 rounded-full">
                        <i class="fas fa-user-clock fa-2x"></i>
                    </div>
                </div>
            </div>

            <!-- Accesos Directos -->
             <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Accesos Directos</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <a href="index.php?c=admin&a=gestionarUsuarios" class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow flex items-center">
                        <i class="fas fa-user-plus text-2xl text-brand-lime mr-4"></i>
                        <div>
                            <h3 class="text-lg font-bold">Gestionar Usuarios</h3>
                            <p class="text-sm text-gray-600">Añadir, editar o cambiar estado de usuarios.</p>
                        </div>
                    </a>
                    <a href="index.php?c=admin&a=gestionarCursos" class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow flex items-center">
                        <i class="fas fa-plus-circle text-2xl text-brand-lime mr-4"></i>
                        <div>
                            <h3 class="text-lg font-bold">Crear Nuevo Curso</h3>
                            <p class="text-sm text-gray-600">Diseña y publica un nuevo curso.</p>
                        </div>
                    </a>
                     <a href="index.php?c=admin&a=gestionarInscripciones" class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow flex items-center">
                        <i class="fas fa-check-double text-2xl text-brand-lime mr-4"></i>
                        <div>
                            <h3 class="text-lg font-bold">Aprobar Inscripciones</h3>
                            <p class="text-sm text-gray-600">Revisa las solicitudes pendientes.</p>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
