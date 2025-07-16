<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Curso - Profesor</title>
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
                <a href="index.php?c=profesor&a=listarCursos" class="sidebar-link active flex items-center py-2.5 px-4 rounded-lg font-bold"><i class="fas fa-book-open fa-fw mr-3"></i>Mis Cursos</a>
                <a href="index.php?c=profesor&a=calificaciones" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-check-double fa-fw mr-3"></i>Calificaciones</a>
            </nav>
            <div class="mt-auto">
                 <a href="index.php?c=login&a=logout" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 p-10 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestionar: <?php echo htmlspecialchars($data['curso']['nombre']); ?></h1>
                    <p class="text-gray-500">Añade módulos, clases y recursos para tu curso.</p>
                </div>
                <a href="index.php?c=profesor&a=listarCursos" class="text-gray-600 hover:text-brand-lime font-semibold"><i class="fas fa-arrow-left mr-2"></i>Volver a Mis Cursos</a>
            </header>
            
            <!-- Mensajes de feedback -->
            <?php if (isset($_SESSION['mensaje'])): ?><div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg" role="alert"><?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div><?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?><div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div><?php endif; ?>

            <!-- Formulario para Crear nuevo módulo -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                <h2 class="text-xl font-bold mb-4">Crear Nuevo Módulo</h2>
                <form action="index.php?c=profesor&a=crearFase" method="POST" class="flex items-center gap-4">
                    <input type="hidden" name="curso_id" value="<?php echo $data['curso']['id']; ?>">
                    <input type="text" name="nombre_fase" placeholder="Ej: Módulo 1 - Introducción a la plataforma" required class="flex-grow p-2 border rounded-md">
                    <button type="submit" class="bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-2 px-4 rounded-lg"><i class="fas fa-plus mr-2"></i>Crear Módulo</button>
                </form>
            </div>

            <!-- Contenedor de Módulos (Acordeón) -->
            <div class="space-y-4">
                <?php foreach ($data['fases'] as $fase): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <h2 class="text-2xl font-bold text-brand-dark-green p-6 border-b"><?php echo htmlspecialchars($fase['nombre']); ?></h2>
                        <div class="p-6 space-y-6">
                            <!-- Formulario para añadir clase -->
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                 <h3 class="text-lg font-semibold text-gray-700 mb-3">Añadir Nueva Clase</h3>
                                 <form action="index.php?c=profesor&a=crearClase" method="POST" class="flex items-center gap-4">
                                    <input type="hidden" name="curso_id" value="<?php echo $data['curso']['id']; ?>"><input type="hidden" name="fase_id" value="<?php echo $fase['id']; ?>">
                                    <input type="text" name="titulo_clase" placeholder="Título de la nueva clase" required class="flex-grow p-2 border rounded-md">
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg"><i class="fas fa-plus mr-2"></i>Añadir</button>
                                 </form>
                            </div>
                            <!-- Lista de clases -->
                            <?php foreach ($fase['clases'] as $clase): ?>
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($clase['titulo']); ?></h3>
                                        <button onclick="openUploadModal(<?php echo $clase['id']; ?>)" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-3 rounded-lg text-sm"><i class="fas fa-upload mr-2"></i>Añadir Recurso</button>
                                    </div>
                                    <?php if (empty($clase['recursos'])): ?>
                                        <p class="text-gray-500 text-sm italic">No hay recursos en esta clase.</p>
                                    <?php else: ?>
                                        <ul class="space-y-2">
                                            <?php foreach ($clase['recursos'] as $recurso): ?>
                                                <li class="flex items-center justify-between p-2 bg-gray-50 rounded-md text-sm">
                                                    <a href="<?php echo htmlspecialchars($recurso['ruta_archivo']); ?>" target="_blank" class="text-blue-600 hover:underline"><i class="fas fa-file-alt mr-2"></i><?php echo htmlspecialchars($recurso['nombre_original']); ?></a>
                                                    <a href="index.php?c=profesor&a=eliminarRecurso&id=<?php echo $recurso['id']; ?>&curso_id=<?php echo $data['curso']['id']; ?>" onclick="return confirm('¿Estás seguro?')" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <!-- Modal para Subir Recurso -->
    <div id="upload-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg">
            <h2 class="text-2xl font-bold mb-6">Subir Nuevo Recurso</h2>
            <form id="upload-form" action="index.php?c=profesor&a=subirRecurso" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="clase_id" id="modal-clase-id">
                <input type="hidden" name="curso_id_redirect" value="<?php echo $data['curso']['id']; ?>">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Archivo</label><input type="file" name="archivo" required class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-lime-100 file:text-lime-800 hover:file:bg-lime-200"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Recurso</label><select name="tipo_recurso" required class="w-full p-2 border rounded-md"><option value="documento">📄 Documento</option><option value="video">🎥 Video</option><option value="audio">🎵 Audio</option><option value="presentacion">📊 Presentación</option><option value="otro">📦 Otro</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Descripción (Opcional)</label><textarea name="descripcion" rows="2" placeholder="Breve descripción del recurso..." class="w-full p-2 border rounded-md"></textarea></div>
                <div class="flex justify-end gap-4 pt-4"><button type="button" onclick="closeUploadModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">Cancelar</button><button type="submit" class="bg-brand-lime hover:bg-brand-lime-dark text-white font-bold px-4 py-2 rounded-md">Subir Recurso</button></div>
            </form>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold mb-4">Configuración del Curso</h2>
        
        <!-- Formulario para el enlace de la evaluación -->
        <form action="index.php?c=profesor&a=actualizarEnlaceEvaluacion" method="POST" class="mb-6 pb-6 border-b">
            <label for="enlace_evaluacion" class="block text-sm font-medium text-gray-700 mb-2">Enlace de la Evaluación Final (Google Forms)</label>
            <div class="flex items-center gap-4">
                <input type="hidden" name="curso_id" value="<?php echo $data['curso']['id']; ?>">
                <input type="url" name="enlace_evaluacion" id="enlace_evaluacion" 
                    value="<?php echo htmlspecialchars($data['curso']['enlace_evaluacion'] ?? ''); ?>" 
                    placeholder="Pega aquí el enlace de la evaluación final" 
                    class="flex-grow p-2 border rounded-md">
                <button type="submit" class="bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-save mr-2"></i>Guardar Enlace
                </button>
            </div>
        </form>

        <!-- Formulario para Crear nuevo módulo -->
        <h2 class="text-xl font-bold mb-4 mt-4">Crear Nuevo Módulo</h2>
        <form action="index.php?c=profesor&a=crearFase" method="POST" class="flex items-center gap-4">
            <input type="hidden" name="curso_id" value="<?php echo $data['curso']['id']; ?>">
            <input type="text" name="nombre_fase" placeholder="Ej: Módulo 1 - Introducción" required class="flex-grow p-2 border rounded-md">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg"><i class="fas fa-plus mr-2"></i>Crear Módulo</button>
        </form>
    </div>

    <script>
        const modal = document.getElementById('upload-modal');
        function openUploadModal(claseId) { document.getElementById('modal-clase-id').value = claseId; modal.classList.remove('hidden'); modal.classList.add('flex'); }
        function closeUploadModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    </script>
</body>
</html>
