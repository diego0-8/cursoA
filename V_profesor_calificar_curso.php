<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar Entregas - Profesor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .bg-brand-lime { background-color: #96c11f; }
        .text-brand-lime { color: #96c11f; }
        .hover\:bg-brand-lime-dark:hover { background-color: #82a81b; }
        .sidebar-link { transition: background-color 0.2s, color 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background-color: #96c11f; color: #1a3a3a; }
        .sidebar-link.active i, .sidebar-link:hover i { color: #1a3a3a; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Barra Lateral -->
        <aside class="w-64 bg-brand-dark-green text-white flex flex-col p-4">
            <div class="text-center py-4 mb-5 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-brand-lime">VERTICAL SAFE</h1>
                <p class="text-xs text-gray-400">Panel de Profesor</p>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php?c=profesor" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-tachometer-alt fa-fw mr-3"></i>Dashboard</a>
                <a href="index.php?c=profesor&a=listarCursos" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-book-open fa-fw mr-3"></i>Mis Cursos</a>
                <a href="index.php?c=profesor&a=calificaciones" class="sidebar-link active flex items-center py-2.5 px-4 rounded-lg font-bold"><i class="fas fa-check-double fa-fw mr-3"></i>Calificaciones</a>
            </nav>
            <div class="mt-auto">
                 <a href="index.php?c=login&a=logout" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 p-10 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Calificando: <?php echo htmlspecialchars($data['curso']['nombre']); ?></h1>
                    <p class="text-gray-500">Revisa las entregas de los estudiantes y asigna una calificación.</p>
                </div>
                <a href="index.php?c=profesor&a=calificaciones" class="text-gray-600 hover:text-brand-lime font-semibold"><i class="fas fa-arrow-left mr-2"></i>Volver a Cursos</a>
            </header>
            
            <?php if (empty($data['entregas'])): ?>
                 <div class="text-center p-12 bg-white rounded-lg shadow-md">
                    <i class="fas fa-inbox text-5xl text-gray-300"></i>
                    <h3 class="mt-4 text-xl font-semibold text-gray-700">No hay entregas para calificar.</h3>
                    <p class="text-gray-500 mt-2">Cuando los estudiantes suban sus trabajos, aparecerán aquí.</p>
                </div>
            <?php else: ?>
                <div class="space-y-8">
                <?php foreach ($data['entregas'] as $fase_nombre => $clases): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <h2 class="text-xl font-bold text-brand-dark-green p-4 bg-gray-50 border-b"><?php echo htmlspecialchars($fase_nombre); ?></h2>
                        <?php foreach ($clases as $clase_nombre => $entregas): ?>
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800"><?php echo htmlspecialchars($clase_nombre); ?></h3>
                                <div class="space-y-3">
                                    <?php foreach ($entregas as $entrega): ?>
                                        <div class="flex items-center justify-between border rounded-lg p-3">
                                            <div>
                                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($entrega['nombre_estudiante']); ?></p>
                                                <a href="<?php echo htmlspecialchars($entrega['ruta_archivo']); ?>" target="_blank" class="text-xs text-blue-500 hover:underline"><i class="fas fa-download mr-1"></i><?php echo htmlspecialchars($entrega['nombre_original']); ?></a>
                                            </div>
                                            <form action="index.php?c=profesor&a=guardarCalificacion" method="POST" class="flex items-center gap-2">
                                                <input type="hidden" name="id_entrega" value="<?php echo $entrega['id']; ?>">
                                                <input type="hidden" name="id_curso" value="<?php echo $data['curso']['id']; ?>">
                                                <input type="number" name="nota" class="p-2 border rounded-md w-24 text-sm" placeholder="Nota" value="<?php echo htmlspecialchars($entrega['nota'] ?? ''); ?>" step="0.1" min="0" max="100">
                                                <button type="submit" class="bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-2 px-3 rounded-lg text-sm">Guardar</button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
