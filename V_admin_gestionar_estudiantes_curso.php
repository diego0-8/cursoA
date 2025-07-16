<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Estudiantes del Curso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Barra Lateral -->
        <div class="w-64 bg-gray-800 text-white p-5 flex flex-col shadow-lg">
            <div class="text-center mb-10">
                 <h2 class="text-2xl font-bold">AdminPanel</h2>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php?c=admin" class="flex items-center p-2 text-sm hover:bg-gray-700 rounded-md"><i class="fas fa-tachometer-alt fa-fw mr-3"></i>Dashboard</a>
                <a href="index.php?c=admin&a=gestionarUsuarios" class="flex items-center p-2 text-sm hover:bg-gray-700 rounded-md"><i class="fas fa-users fa-fw mr-3"></i>Gestionar Usuarios</a>
                <a href="index.php?c=admin&a=gestionarCursos" class="flex items-center p-2 text-sm bg-gray-700 rounded-md"><i class="fas fa-book fa-fw mr-3"></i>Gestionar Cursos</a>
            </nav>
            <div class="mt-auto">
                <a href="index.php?c=login&a=logout" class="flex items-center p-2 text-sm hover:bg-red-600 rounded-md"><i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="flex-1 p-10 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestionar Estudiantes</h1>
                    <p class="text-gray-600">Curso: <span class="font-semibold"><?php echo htmlspecialchars($data['curso']['nombre']); ?></span></p>
                </div>
                <a href="index.php?c=admin&a=gestionarCursos" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a Cursos
                </a>
            </div>

            <!-- Filtros y Acciones Masivas -->
            <div class="bg-white p-4 rounded-lg shadow-md mb-8">
                <form id="form-masiva" action="index.php?c=admin&a=aplicarAccionMasivaEstudiantes" method="POST">
                    <input type="hidden" name="curso_id" value="<?php echo $data['curso']['id']; ?>">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Búsqueda -->
                        <div>
                            <input type="text" name="busqueda" placeholder="Buscar por nombre o email..." value="<?php echo htmlspecialchars($data['busqueda'] ?? ''); ?>" class="w-full p-2 border rounded-md text-sm">
                        </div>
                        <!-- Filtro por Estado -->
                        <div>
                            <select name="filtro_estado" class="w-full p-2 border rounded-md text-sm">
                                <option value="">-- Filtrar por Estado --</option>
                                <option value="activa" <?php echo (($data['filtro_estado'] ?? '') == 'activa') ? 'selected' : ''; ?>>Activo</option>
                                <option value="inactiva" <?php echo (($data['filtro_estado'] ?? '') == 'inactiva') ? 'selected' : ''; ?>>Inactivo</option>
                                <option value="completado" <?php echo (($data['filtro_estado'] ?? '') == 'completado') ? 'selected' : ''; ?>>Completado</option>
                            </select>
                        </div>
                        <!-- Botón de Filtrar -->
                        <div>
                             <button type="button" onclick="document.getElementById('form-filtros').submit()" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm"><i class="fas fa-filter mr-2"></i>Aplicar Filtros</button>
                        </div>
                    </div>
                </form>
                <!-- Formulario oculto solo para filtros GET -->
                <form id="form-filtros" action="index.php" method="GET">
                    <input type="hidden" name="c" value="admin">
                    <input type="hidden" name="a" value="gestionarEstudiantesCurso">
                    <input type="hidden" name="id" value="<?php echo $data['curso']['id']; ?>">
                </form>

                <!-- Acciones Masivas -->
                <div class="border-t mt-4 pt-4">
                     <div class="flex items-center gap-4">
                        <label class="text-sm font-medium">Acción para seleccionados:</label>
                        <select name="accion_masiva" class="p-2 border rounded-md text-sm">
                            <option value="">-- Seleccionar Acción --</option>
                            <option value="activar">Activar en el curso</option>
                            <option value="suspender">Suspender del curso</option>
                            <option value="eliminar">Eliminar del curso</option>
                        </select>
                        <button type="submit" form="form-masiva" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 text-sm font-semibold"><i class="fas fa-play mr-2"></i>Aplicar</button>
                    </div>
                </div>
            </div>

            <!-- Tabla de Estudiantes -->
            <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="p-3"><input type="checkbox" id="select-all"></th>
                            <th class="p-3 text-left text-sm font-semibold text-gray-600">Estudiante</th>
                            <th class="p-3 text-left text-sm font-semibold text-gray-600">Estado</th>
                            <th class="p-3 text-center text-sm font-semibold text-gray-600">Progreso</th>
                            <th class="p-3 text-left text-sm font-semibold text-gray-600">Inscripción</th>
                            <th class="p-3 text-center text-sm font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['estudiantes'])): ?>
                            <tr><td colspan="6" class="text-center p-8 text-gray-500">No hay estudiantes inscritos en este curso.</td></tr>
                        <?php else: ?>
                            <?php foreach ($data['estudiantes'] as $estudiante): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3"><input type="checkbox" name="estudiantes_seleccionados[]" value="<?php echo $estudiante['numero_documento']; ?>" class="student-checkbox" form="form-masiva"></td>
                                    <td class="p-3">
                                        <p class="font-bold text-gray-800"><?php echo htmlspecialchars($estudiante['nombre_completo']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($estudiante['email']); ?></p>
                                    </td>
                                    <td class="p-3">
                                        <?php 
                                            $estado_clase = 'bg-gray-200 text-gray-800';
                                            if ($estudiante['estado_inscripcion'] == 'activa') $estado_clase = 'bg-green-100 text-green-800';
                                            if ($estudiante['estado_inscripcion'] == 'inactiva' || $estudiante['estado_inscripcion'] == 'suspendida') $estado_clase = 'bg-yellow-100 text-yellow-800';
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $estado_clase; ?>"><?php echo ucfirst($estudiante['estado_inscripcion']); ?></span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="font-semibold">0%</span> <!-- Placeholder -->
                                    </td>
                                    <td class="p-3 text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($estudiante['fecha_inscripcion'])); ?></td>
                                    <td class="p-3 text-center">
                                        <div class="flex items-center justify-center gap-4">
                                            <a href="#" class="text-blue-600" title="Ver Perfil"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="text-purple-600" title="Enviar Mensaje"><i class="fas fa-envelope"></i></a>
                                            <a href="#" class="text-red-600" title="Eliminar del Curso"><i class="fas fa-user-times"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        // Lógica para seleccionar/deseleccionar todos los checkboxes
        document.getElementById('select-all').addEventListener('click', function(event) {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = event.target.checked);
        });

        // Lógica para que el formulario de filtros se envíe con los valores correctos
        const filtroForm = document.getElementById('form-filtros');
        const busquedaInput = document.querySelector('#form-masiva input[name="busqueda"]');
        const estadoSelect = document.querySelector('#form-masiva select[name="filtro_estado"]');
        
        busquedaInput.addEventListener('change', (e) => {
            let hiddenInput = filtroForm.querySelector('input[name="busqueda"]');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'busqueda';
                filtroForm.appendChild(hiddenInput);
            }
            hiddenInput.value = e.target.value;
        });

        estadoSelect.addEventListener('change', (e) => {
             let hiddenInput = filtroForm.querySelector('input[name="filtro_estado"]');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'filtro_estado';
                filtroForm.appendChild(hiddenInput);
            }
            hiddenInput.value = e.target.value;
        });
    </script>
</body>
</html>
