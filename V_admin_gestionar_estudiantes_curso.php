<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Estudiantes del Curso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .bg-brand-lime { background-color: #96c11f; }
        .text-brand-lime { color: #96c11f; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Barra Lateral -->
        <aside class="w-64 bg-brand-dark-green text-white p-4 flex flex-col">
            <div class="text-center py-4 mb-5 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-brand-lime">VERTICAL SAFE</h1>
                <p class="text-xs text-gray-400">Panel de Administrador</p>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php?c=admin" class="p-3 hover:bg-brand-lime hover:text-brand-dark-green rounded-lg text-sm"><i class="fas fa-tachometer-alt fa-fw mr-3"></i>Dashboard</a>
                <a href="index.php?c=admin&a=gestionarUsuarios" class="p-3 hover:bg-brand-lime hover:text-brand-dark-green rounded-lg text-sm"><i class="fas fa-users fa-fw mr-3"></i>Usuarios</a>
                <a href="index.php?c=admin&a=gestionarCursos" class="p-3 bg-brand-lime text-brand-dark-green rounded-lg text-sm font-bold"><i class="fas fa-book fa-fw mr-3"></i>Cursos</a>
                <a href="index.php?c=admin&a=gestionarInscripciones" class="p-3 hover:bg-brand-lime hover:text-brand-dark-green rounded-lg text-sm"><i class="fas fa-user-check fa-fw mr-3"></i>Inscripciones</a>
            </nav>
            <div class="mt-auto">
                <a href="index.php?c=login&a=logout" class="p-3 text-red-400 hover:bg-red-500 hover:text-white rounded-lg text-sm"><i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <div class="flex-1 p-10 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestionar Estudiantes</h1>
                    <p class="text-gray-600">Curso: <span class="font-semibold"><?php echo htmlspecialchars($data['curso']['nombre']); ?></span></p>
                </div>
                <a href="index.php?c=admin&a=gestionarCursos" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a Cursos
                </a>
            </div>

            <!-- Formulario de Acciones y Filtros -->
            <form id="form-gestion" action="index.php" method="GET">
                <input type="hidden" name="c" value="admin">
                <input type="hidden" name="a" value="gestionarEstudiantesCurso">
                <input type="hidden" name="id" value="<?php echo $data['curso']['id']; ?>">
                
                <div class="bg-white p-4 rounded-lg shadow-md mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="text" name="busqueda" placeholder="Buscar por nombre o email..." value="<?php echo htmlspecialchars($data['busqueda'] ?? ''); ?>" class="w-full p-2 border rounded-md text-sm">
                        <select name="filtro_estado" class="w-full p-2 border rounded-md text-sm">
                            <option value="">-- Filtrar por Estado --</option>
                            <option value="activa" <?php echo (($data['filtro_estado'] ?? '') == 'activa') ? 'selected' : ''; ?>>Activa</option>
                            <option value="suspendida" <?php echo (($data['filtro_estado'] ?? '') == 'suspendida') ? 'selected' : ''; ?>>Suspendida</option>
                        </select>
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm"><i class="fas fa-filter mr-2"></i>Aplicar Filtros</button>
                    </div>
                </div>
            </form>

            <!-- Tabla de Estudiantes -->
            <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="p-3 text-left text-sm font-semibold text-gray-600">Estudiante</th>
                            <th class="p-3 text-left text-sm font-semibold text-gray-600">Estado</th>
                            <th class="p-3 text-center text-sm font-semibold text-gray-600">Progreso Clases</th>
                            <th class="p-3 text-center text-sm font-semibold text-gray-600">Promedio Evaluaciones</th>
                            <th class="p-3 text-left text-sm font-semibold text-gray-600">Inscripción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php if (empty($data['estudiantes'])): ?>
                            <tr><td colspan="5" class="text-center p-8 text-gray-500">No se encontraron estudiantes con los filtros aplicados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($data['estudiantes'] as $estudiante): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3">
                                        <p class="font-bold text-gray-800"><?php echo htmlspecialchars($estudiante['nombre_completo']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($estudiante['email']); ?></p>
                                    </td>
                                    <td class="p-3">
                                        <?php 
                                            $estado_clase = 'bg-gray-200 text-gray-800';
                                            if ($estudiante['estado_inscripcion'] == 'activa') $estado_clase = 'bg-green-100 text-green-800';
                                            if ($estudiante['estado_inscripcion'] == 'suspendida') $estado_clase = 'bg-yellow-100 text-yellow-800';
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $estado_clase; ?>"><?php echo ucfirst($estudiante['estado_inscripcion']); ?></span>
                                    </td>
                                    <td class="p-3">
                                        <?php
                                            $porcentaje = 0;
                                            if ($estudiante['total_clases'] > 0) {
                                                $porcentaje = ($estudiante['clases_completadas'] / $estudiante['total_clases']) * 100;
                                            }
                                        ?>
                                        <div class="flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700 w-12 text-right mr-2"><?php echo round($porcentaje); ?>%</span>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-brand-lime h-2.5 rounded-full" style="width: <?php echo $porcentaje; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <?php if (isset($estudiante['promedio_evaluaciones'])): ?>
                                            <span class="font-semibold text-lg <?php echo $estudiante['promedio_evaluaciones'] >= 80 ? 'text-green-600' : 'text-red-600'; ?>">
                                                <?php echo number_format($estudiante['promedio_evaluaciones'], 1); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-400">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($estudiante['fecha_inscripcion'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
