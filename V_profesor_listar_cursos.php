<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos - Profesor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Vinculando la hoja de estilos del profesor -->
    <link rel="stylesheet" href="views/css/profesor.css">
</head>
<body>
    <div class="flex h-screen">
        <!-- Barra Lateral -->
        <aside class="w-64 bg-brand-dark-green text-white flex flex-col p-4">
            <div class="text-center py-4 mb-5 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-brand-lime">VERTICAL SAFE</h1>
                <p class="text-xs text-gray-400">Panel de Profesor</p>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php?c=profesor" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-tachometer-alt fa-fw mr-3"></i>Dashboard</a>
                <a href="index.php?c=profesor&a=listarCursos" class="sidebar-link active flex items-center py-2.5 px-4 rounded-lg font-bold"><i class="fas fa-book-open fa-fw mr-3"></i>Mis Cursos</a>
                <a href="index.php?c=profesor&a=calificaciones" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-check-double fa-fw mr-3"></i>Calificaciones</a>
            </nav>
            <div class="mt-auto">
                 <a href="index.php?c=login&a=logout" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 p-10 overflow-y-auto">
            <header class="mb-8 bg-white/80 backdrop-blur-sm p-6 rounded-lg">
                <h1 class="text-3xl font-bold text-gray-800">Mis Cursos</h1>
                <p class="text-gray-500">Gestiona el contenido, los estudiantes y las calificaciones de tus cursos asignados.</p>
            </header>

            <?php if (empty($data['cursos'])): ?>
                <div class="text-center p-12 bg-white rounded-lg shadow-md">
                    <i class="fas fa-folder-open text-5xl text-gray-300"></i>
                    <h3 class="mt-4 text-xl font-semibold text-gray-700">No tienes cursos asignados.</h3>
                    <p class="text-gray-500 mt-2">Contacta a un administrador para que te asigne a los cursos correspondientes.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($data['cursos'] as $curso): ?>
                        <div class="course-card bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                            <div class="p-6 flex-grow">
                                <h2 class="text-xl font-bold text-brand-dark-green mb-2"><?php echo htmlspecialchars($curso['nombre']); ?></h2>
                                <p class="text-gray-600 text-sm mb-4 flex-grow"><?php echo htmlspecialchars($curso['descripcion'] ?? 'Sin descripción.'); ?></p>
                                <div class="flex justify-around text-center border-t border-b py-3 my-4 text-sm">
                                    <div><p class="font-bold text-lg text-blue-600"><?php echo $curso['total_fases'] ?? 0; ?></p><p class="text-gray-500">Módulos</p></div>
                                    <div><p class="font-bold text-lg text-green-600"><?php echo $curso['total_clases'] ?? 0; ?></p><p class="text-gray-500">Clases</p></div>
                                    <div><p class="font-bold text-lg text-purple-600"><?php echo $curso['total_estudiantes'] ?? 0; ?></p><p class="text-gray-500">Estudiantes</p></div>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 border-t flex justify-around">
                                <a href="index.php?c=profesor&a=gestionarCurso&id=<?php echo $curso['id']; ?>" title="Gestionar Contenido" class="text-gray-600 hover:text-brand-lime"><i class="fas fa-edit fa-lg"></i></a>
                                <a href="index.php?c=profesor&a=verEstudiantes&id=<?php echo $curso['id']; ?>" title="Ver Estudiantes" class="text-gray-600 hover:text-brand-lime"><i class="fas fa-users fa-lg"></i></a>
                                <a href="index.php?c=profesor&a=calificarCurso&id_curso=<?php echo $curso['id']; ?>" title="Calificar Entregas" class="text-gray-600 hover:text-brand-lime"><i class="fas fa-check-double fa-lg"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
