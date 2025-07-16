<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos - Vertical Safe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .bg-brand-lime { background-color: #96c11f; }
        .text-brand-lime { color: #96c11f; }
        .hover\:bg-brand-lime-dark:hover { background-color: #82a81b; }
        .header-link { transition: color 0.2s; }
        .header-link:hover { color: #96c11f; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header y Navegación -->
    <header class="bg-white shadow-md sticky top-0 z-20">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php?c=estudiante" class="text-2xl font-bold text-brand-dark-green">
                    <span class="text-brand-lime">VERTICAL</span> SAFE
                </a>
                <nav class="flex items-center space-x-6">
                    <a href="index.php?c=estudiante" class="header-link text-gray-600 font-semibold">Cursos Disponibles</a>
                    <a href="index.php?c=estudiante&a=misCursos" class="header-link text-brand-lime font-bold">Mis Cursos</a>
                    <span class="text-gray-300">|</span>
                    <span class="font-medium text-gray-800">Hola, <?php echo htmlspecialchars(explode(' ', $_SESSION['usuario_nombre_completo'])[0]); ?></span>
                    <a href="index.php?c=login&a=logout" class="text-red-500 hover:text-red-700" title="Cerrar Sesión">
                        <i class="fas fa-sign-out-alt fa-lg"></i>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-6 py-10">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Mis Cursos Inscritos</h1>
            <p class="text-gray-500">Continúa tu aprendizaje y completa tus certificaciones.</p>
        </header>

        <?php if (empty($data['cursos'])): ?>
            <div class="text-center p-12 bg-white rounded-lg shadow-md">
                <i class="fas fa-search-minus text-5xl text-gray-400"></i>
                <h3 class="mt-4 text-xl font-semibold text-gray-700">Aún no estás inscrito en ningún curso.</h3>
                <p class="text-gray-500 mt-2">Explora los cursos disponibles en el panel principal y solicita tu inscripción.</p>
                <a href="index.php?c=estudiante" class="mt-6 inline-block bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-2 px-4 rounded-lg transition-colors">Explorar Cursos</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($data['cursos'] as $curso): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col transition-transform hover:-translate-y-2">
                        <div class="p-6 flex-grow flex flex-col">
                            <h2 class="text-xl font-bold text-brand-dark-green mb-2"><?php echo htmlspecialchars($curso['nombre']); ?></h2>
                            <p class="text-sm text-gray-500 mb-4">
                                <i class="fas fa-user-tie mr-2 text-gray-400"></i>Profesor: <?php echo htmlspecialchars($curso['profesor_nombre_completo'] ?? 'N/A'); ?>
                            </p>
                            
                            <!-- Barra de progreso dinámica -->
                            <?php
                                $porcentaje = 0;
                                if (isset($curso['progreso']) && $curso['progreso']['total_clases'] > 0) {
                                    $porcentaje = ($curso['progreso']['clases_completadas'] / $curso['progreso']['total_clases']) * 100;
                                }
                                $porcentaje_redondeado = round($porcentaje);
                            ?>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                                <div class="bg-brand-lime h-2.5 rounded-full" style="width: <?php echo $porcentaje; ?>%"></div>
                            </div>
                            <?php if ($porcentaje_redondeado == 100): ?>
                                <p class="text-xs text-green-600 font-bold mb-6"><i class="fas fa-check-circle mr-1"></i>Curso Completado</p>
                            <?php else: ?>
                                <p class="text-xs text-gray-500 mb-6">Progreso: <?php echo $porcentaje_redondeado; ?>%</p>
                            <?php endif; ?>
                            
                            <!-- INICIO: Lógica para mostrar botón de examen o de acceso -->
                            <div class="mt-auto">
                                <?php if ($porcentaje_redondeado == 100 && !empty($curso['enlace_evaluacion'])): ?>
                                    <a href="<?php echo htmlspecialchars($curso['enlace_evaluacion']); ?>" target="_blank" class="block w-full text-center bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-3 px-4 rounded-lg transition-colors">
                                        <i class="fas fa-award mr-2"></i>Realizar Evaluación
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?c=estudiante&a=verCurso&id=<?php echo $curso['id']; ?>" class="block w-full text-center bg-brand-dark-green hover:bg-gray-800 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                                        <i class="fas fa-arrow-right mr-2"></i>Acceder al Curso
                                    </a>
                                <?php endif; ?>
                                <?php if ($porcentaje_redondeado == 100 && !empty($curso['certificado'])): ?>
                                    <a href="index.php?c=estudiante&a=verCertificado&curso_id=<?php echo $curso['id']; ?>" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                                        <i class="fas fa-certificate mr-2"></i>Ver Certificado
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?c=estudiante&a=generarCertificado&curso_id=<?php echo $curso['id']; ?>" class="block w-full text-center bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-3 px-4 rounded-lg transition-colors">
                                        <i class="fas fa-award mr-2"></i>Generar Certificado
                                    </a>
                                <?php endif; ?>
                            </div>
                            <!-- FIN: Lógica de botones -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>