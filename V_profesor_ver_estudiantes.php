<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .bg-brand-lime { background-color: #96c11f; }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-sm sticky top-0 z-10">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($data['titulo']); ?></h1>
                <p class="text-sm text-gray-500">Supervisa el avance de tus estudiantes.</p>
            </div>
            <a href="index.php?c=profesor&a=listarCursos" class="bg-brand-dark-green text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Volver a Mis Cursos
            </a>
        </div>
    </header>

    <main class="container mx-auto px-6 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="p-4 text-left text-sm font-semibold text-gray-600 tracking-wider">Estudiante</th>
                            <th class="p-4 text-left text-sm font-semibold text-gray-600 tracking-wider w-1/3">Progreso del Curso</th>
                            <th class="p-4 text-center text-sm font-semibold text-gray-600 tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($data['estudiantes'])): ?>
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-500">
                                    <i class="fas fa-user-slash fa-3x mb-3"></i>
                                    <p>Aún no hay estudiantes inscritos en este curso.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['estudiantes'] as $estudiante): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-medium text-gray-900"><?php echo htmlspecialchars($estudiante['nombre_completo']); ?></td>
                                    <td class="p-4">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-700 w-12"><?php echo $estudiante['progreso_porcentaje']; ?>%</span>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 ml-2">
                                                <div class="bg-brand-lime h-2.5 rounded-full" style="width: <?php echo $estudiante['progreso_porcentaje']; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver Detalles</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>

