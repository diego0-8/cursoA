<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['curso']['nombre']); ?> - Vertical Safe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .bg-brand-lime { background-color: #96c11f; }
        .text-brand-lime { color: #96c11f; }
        .hover\:bg-brand-lime-dark:hover { background-color: #82a81b; }
        .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease-in-out; }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md sticky top-0 z-20">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php?c=estudiante" class="text-2xl font-bold text-brand-dark-green">
                    <span class="text-brand-lime">VERTICAL</span> SAFE
                </a>
                <nav class="flex items-center space-x-6">
                    <a href="index.php?c=estudiante&a=misCursos" class="text-gray-600 hover:text-brand-lime font-semibold"><i class="fas fa-arrow-left mr-2"></i>Volver a Mis Cursos</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-6 py-10">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($data['curso']['nombre']); ?></h1>
            <!-- INICIO: Descripción del Curso -->
            <?php if (!empty($data['curso']['descripcion'])): ?>
                <div class="mt-4 bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-700 text-base">
                        <?php echo nl2br(htmlspecialchars($data['curso']['descripcion'])); ?>
                    </p>
                </div>
            <?php endif; ?>
            <!-- FIN: Descripción del Curso -->
            
            <!-- Barra de Progreso -->
            <div class="mt-4">
                <?php 
                    $porcentaje = 0;
                    if ($data['progreso']['total_clases'] > 0) {
                        $porcentaje = ($data['progreso']['clases_completadas'] / $data['progreso']['total_clases']) * 100;
                    }
                ?>
                <div class="flex justify-between mb-1">
                    <span class="text-base font-medium text-brand-dark-green">Progreso del Curso</span>
                    <span class="text-sm font-medium text-brand-dark-green"><?php echo round($porcentaje); ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-brand-lime h-2.5 rounded-full" style="width: <?php echo $porcentaje; ?>%"></div>
                </div>
            </div>
        </header>

        <!-- Acordeón de Clases -->
        <div id="accordion-container" class="space-y-3">
            <?php foreach ($data['fases'] as $fase): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <button class="accordion-header w-full flex justify-between items-center text-left text-xl font-bold p-6 hover:bg-gray-50 focus:outline-none">
                        <span class="text-brand-dark-green"><?php echo htmlspecialchars($fase['nombre']); ?></span>
                        <i class="fas fa-chevron-down transition-transform text-brand-lime"></i>
                    </button>
                    <div class="accordion-content">
                        <div class="p-6 pt-0 divide-y">
                            <?php foreach ($fase['clases'] as $clase): ?>
                                <div class="py-4">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-semibold text-lg text-gray-800"><?php echo htmlspecialchars($clase['titulo']); ?></h3>
                                        <!-- Botón para marcar como completada -->
                                        <?php if ($clase['completado']): ?>
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full"><i class="fas fa-check-circle mr-1"></i>Completado</span>
                                        <?php else: ?>
                                            <a href="index.php?c=estudiante&a=marcarComoCompletada&clase_id=<?php echo $clase['id']; ?>&curso_id=<?php echo $data['curso']['id']; ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold py-1 px-3 rounded-full">
                                                Marcar como completada
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <!-- INICIO: Material de Estudio con Descripción -->
                                    <?php if(!empty($clase['recursos'])): ?>
                                        <ul class="space-y-3 mt-4">
                                            <?php foreach($clase['recursos'] as $recurso): ?>
                                            <li class="flex items-start">
                                                <i class="fas fa-file-alt text-brand-lime fa-lg mr-3 mt-1"></i>
                                                <div class="flex-grow">
                                                    <a href="<?php echo htmlspecialchars($recurso['ruta_archivo']); ?>" target="_blank" class="text-blue-600 hover:underline font-medium"><?php echo htmlspecialchars($recurso['nombre_original']); ?></a>
                                                    <?php if (!empty($recurso['descripcion'])): ?>
                                                        <p class="text-gray-600 text-sm mt-1">
                                                            <?php echo nl2br(htmlspecialchars($recurso['descripcion'])); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Sección de Evaluación Final -->
        <?php if ($data['curso_completado'] && !empty($data['curso']['enlace_evaluacion'])): ?>
        <div class="mt-12 text-center bg-brand-dark-green text-white p-8 rounded-lg shadow-2xl">
            <i class="fas fa-award fa-3x text-brand-lime mb-4"></i>
            <h2 class="text-3xl font-bold">¡Felicidades, has completado el curso!</h2>
            <p class="mt-2 mb-6">Estás a un solo paso de obtener tu certificación. Realiza la evaluación final para completar el proceso.</p>
            <a href="<?php echo htmlspecialchars($data['curso']['enlace_evaluacion']); ?>" target="_blank" class="inline-block bg-brand-lime hover:bg-brand-lime-dark text-white font-bold text-lg py-3 px-8 rounded-lg transition-transform hover:scale-105">
                Realizar Evaluación Final <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        <?php endif; ?>
    </main>

    <script>
        document.querySelectorAll('.accordion-header').forEach(button => {
            button.addEventListener('click', () => {
                const accordionContent = button.nextElementSibling;
                const icon = button.querySelector('i');
                
                document.querySelectorAll('.accordion-content').forEach(content => {
                    if (content !== accordionContent) {
                        content.style.maxHeight = null;
                        content.previousElementSibling.querySelector('i').classList.remove('rotate-180');
                    }
                });

                icon.classList.toggle('rotate-180');

                if (accordionContent.style.maxHeight) {
                    accordionContent.style.maxHeight = null;
                } else {
                    accordionContent.style.maxHeight = accordionContent.scrollHeight + "px";
                } 
            });
        });
    </script>
</body>
</html>
