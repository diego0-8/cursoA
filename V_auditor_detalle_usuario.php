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
    </style>
</head>
<body class="bg-gray-100 p-10">
    <div class="container mx-auto">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detalle de Actividad</h1>
                <p class="text-gray-500">Mostrando información para el usuario: <span class="font-bold"><?php echo htmlspecialchars($data['usuario']['nombre'] . ' ' . $data['usuario']['apellido']); ?></span></p>
            </div>
            <a href="index.php?c=auditor" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Volver al listado
            </a>
        </header>

        <!-- SECCIÓN PARA ESTUDIANTES -->
        <?php if ($data['usuario']['rol'] == 'estudiante'): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <h2 class="text-xl font-bold p-4 bg-gray-50 border-b">Actividad como Estudiante</h2>
                <div class="p-6">
                    <?php if (empty($data['actividad'])): ?>
                        <p class="text-gray-500">Este estudiante no está inscrito en ningún curso.</p>
                    <?php else: ?>
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Curso Inscrito</th>
                                    <th class="p-3 text-center text-sm font-semibold text-gray-600">Progreso</th>
                                    <th class="p-3 text-center text-sm font-semibold text-gray-600">Promedio Evaluaciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <?php foreach ($data['actividad'] as $curso): ?>
                                    <tr>
                                        <td class="p-3 font-medium"><?php echo htmlspecialchars($curso['nombre_curso']); ?></td>
                                        <td class="p-3 text-center">
                                            <?php
                                                $progreso = ($curso['total_clases'] > 0) ? round(($curso['clases_completadas'] / $curso['total_clases']) * 100) : 0;
                                                echo $progreso . '%';
                                            ?>
                                        </td>
                                        <td class="p-3 text-center font-semibold <?php echo ($curso['promedio_evaluaciones'] >= 80) ? 'text-green-600' : 'text-red-600'; ?>">
                                            <?php echo isset($curso['promedio_evaluaciones']) ? number_format($curso['promedio_evaluaciones'], 1) . '%' : 'N/A'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <!-- SECCIÓN PARA PROFESORES -->
        <?php elseif ($data['usuario']['rol'] == 'profesor'): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <h2 class="text-xl font-bold p-4 bg-gray-50 border-b">Actividad como Profesor</h2>
                <div class="p-6">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 text-left text-sm font-semibold text-gray-600">Métrica</th>
                                <th class="p-3 text-center text-sm font-semibold text-gray-600">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr>
                                <td class="p-3 font-medium">Cursos Asignados</td>
                                <td class="p-3 text-center font-bold text-lg"><?php echo $data['actividad']['total_cursos']; ?></td>
                            </tr>
                            <tr>
                                <td class="p-3 font-medium">Estudiantes a Cargo</td>
                                <td class="p-3 text-center font-bold text-lg"><?php echo $data['actividad']['total_estudiantes']; ?></td>
                            </tr>
                            <tr>
                                <td class="p-3 font-medium">Módulos Creados</td>
                                <td class="p-3 text-center font-bold text-lg"><?php echo $data['actividad']['total_modulos']; ?></td>
                            </tr>
                            <tr>
                                <td class="p-3 font-medium">Clases Creadas</td>
                                <td class="p-3 text-center font-bold text-lg"><?php echo $data['actividad']['total_clases']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <!-- OTROS ROLES -->
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-600">No hay un reporte de actividad detallado para el rol de '<?php echo htmlspecialchars($data['usuario']['rol']); ?>'.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
