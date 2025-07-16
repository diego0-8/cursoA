<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Cursos - Admin</title>
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
        <!-- Barra Lateral de Navegación -->
        <aside class="w-64 bg-brand-dark-green text-white flex flex-col p-4">
            <div class="text-center py-4 mb-5 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-brand-lime">VERTICAL SAFE</h1>
                <p class="text-xs text-gray-400">Admin Panel</p>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php?c=admin" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-tachometer-alt fa-fw mr-3"></i>Dashboard</a>
                <a href="index.php?c=admin&a=gestionarUsuarios" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-users fa-fw mr-3"></i>Usuarios</a>
                <a href="index.php?c=admin&a=gestionarCursos" class="sidebar-link active flex items-center py-2.5 px-4 rounded-lg font-bold"><i class="fas fa-book-open fa-fw mr-3"></i>Cursos</a>
                <a href="index.php?c=admin&a=gestionarInscripciones" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-clipboard-check fa-fw mr-3"></i>Inscripciones</a>
            </nav>
            <div class="mt-auto">
                 <a href="index.php?c=login&a=logout" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 p-10 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestionar Cursos</h1>
                    <p class="text-gray-500">Crea, asigna profesores y gestiona los cursos de la plataforma.</p>
                </div>
                 <button onclick="openCreateModal()" class="bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors">
                    <i class="fas fa-plus mr-2"></i>Crear Curso
                </button>
            </header>

            <!-- Tabla de Cursos -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 text-left text-sm font-semibold text-gray-600">Nombre del Curso</th>
                                <th class="p-4 text-left text-sm font-semibold text-gray-600">Profesor Asignado</th>
                                <th class="p-4 text-center text-sm font-semibold text-gray-600">Estudiantes</th>
                                <th class="p-4 text-center text-sm font-semibold text-gray-600">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($data['cursos'] as $curso): ?>
                            <tr>
                                <td class="p-4">
                                    <div class="font-bold text-gray-800"><?php echo htmlspecialchars($curso['nombre']); ?></div>
                                    <p class="text-xs text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                                </td>
                                <td class="p-4 text-sm text-gray-700"><?php echo htmlspecialchars($curso['profesor_nombre_completo'] ?? 'Sin Asignar'); ?></td>
                                <td class="p-4 text-center text-sm font-semibold text-gray-700"><?php echo $curso['total_estudiantes']; ?></td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <button onclick='openEditModal(<?php echo json_encode($curso); ?>)' class="text-yellow-500 hover:text-yellow-700" title="Editar Curso"><i class="fas fa-edit"></i></button>
                                        <a href="index.php?c=admin&a=gestionarEstudiantesCurso&id=<?php echo $curso['id']; ?>" class="text-green-500 hover:text-green-700" title="Gestionar Estudiantes"><i class="fas fa-users-cog"></i></a>
                                        <a href="index.php?c=admin&a=eliminarCurso&id=<?php echo $curso['id']; ?>" class="text-red-500 hover:text-red-700" title="Eliminar Curso" onclick="return confirm('¿Estás seguro? Esta acción puede ser irreversible.')"><i class="fas fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Crear/Editar Curso -->
    <div id="course-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg">
            <h2 id="modal-title" class="text-2xl font-bold mb-6 text-gray-800"></h2>
            <form id="course-form" method="POST">
                <input type="hidden" name="id" id="curso-id">
                <div class="space-y-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Curso</label>
                        <input type="text" name="nombre" id="nombre" required class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="mt-1 w-full p-2 border border-gray-300 rounded-md"></textarea>
                    </div>
                    <div>
                        <label for="profesor" class="block text-sm font-medium text-gray-700">Asignar Profesor</label>
                        <select name="profesor_documento" id="profesor" class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                            <option value="">-- Sin Asignar --</option>
                            <?php foreach ($data['profesores'] as $profesor): ?>
                                <option value="<?php echo $profesor['numero_documento']; ?>"><?php echo htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-4 mt-8">
                    <button type="button" onclick="closeModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">Cancelar</button>
                    <button type="submit" class="bg-brand-lime hover:bg-brand-lime-dark text-white font-bold px-4 py-2 rounded-md">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('course-modal');
        const form = document.getElementById('course-form');
        const modalTitle = document.getElementById('modal-title');

        function openCreateModal() {
            form.reset();
            form.action = 'index.php?c=admin&a=crearCurso';
            modalTitle.innerText = 'Crear Nuevo Curso';
            document.getElementById('curso-id').value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openEditModal(curso) {
            form.reset();
            form.action = 'index.php?c=admin&a=actualizarCurso';
            modalTitle.innerText = 'Editar Curso';
            document.getElementById('curso-id').value = curso.id;
            document.getElementById('nombre').value = curso.nombre;
            document.getElementById('descripcion').value = curso.descripcion;
            document.getElementById('profesor').value = curso.profesor_numero_documento || "";
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>
