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
        .header-link { transition: color 0.2s; }
        .header-link:hover { color: #96c11f; }
        .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease-in-out; }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header y Navegación COMPLETO -->
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

    <!-- Contenido Principal -->
    <main class="container mx-auto px-6 py-10">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($data['curso']['nombre']); ?></h1>
            <p class="text-gray-500 mt-2"><?php echo htmlspecialchars($data['curso']['descripcion']); ?></p>
            
            <?php if (isset($_SESSION['mensaje'])): ?><div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-4 rounded-r-lg" role="alert"><?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div><?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?><div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mt-4 rounded-r-lg" role="alert"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div><?php endif; ?>
        </header>

        <div id="accordion-container" class="space-y-3">
            <?php foreach ($data['fases'] as $fase): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <button class="accordion-header w-full flex justify-between items-center text-left text-xl font-bold p-6 hover:bg-gray-50 focus:outline-none">
                        <span><?php echo htmlspecialchars($fase['nombre']); ?></span>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="accordion-content">
                        <div class="p-6 pt-0 divide-y">
                            <?php foreach ($fase['clases'] as $clase): ?>
                                <div class="py-4 <?php echo $clase['completado'] ? 'opacity-60' : ''; ?>">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($clase['titulo']); ?></h3>
                                        <?php
                                            $tiene_video = false;
                                            if (!empty($clase['recursos'])) {
                                                foreach ($clase['recursos'] as $recurso) {
                                                    if ($recurso['tipo_recurso'] == 'video') {
                                                        $tiene_video = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        <?php if ($clase['completado']): ?>
                                            <span class="text-green-600 font-bold text-sm"><i class="fas fa-check-circle mr-1"></i>Completado</span>
                                        <?php elseif (!$tiene_video): ?>
                                            <a href="index.php?c=estudiante&a=marcarComoCompletada&clase_id=<?php echo $clase['id']; ?>&curso_id=<?php echo $data['curso']['id']; ?>" class="bg-gray-200 hover:bg-gray-300 text-xs font-bold py-1 px-3 rounded-full">Marcar como completada</a>
                                        <?php endif; ?>
                                    </div>
                                    <?php if(!empty($clase['recursos'])): ?>
                                        <ul class="space-y-6 mt-4 pl-5">
                                            <?php foreach($clase['recursos'] as $recurso): ?>
                                                <?php if ($recurso['tipo_recurso'] == 'video'): ?>
                                                    <!-- CAMBIO: Diseño de dos columnas para video y descripción -->
                                                    <li class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                                                        <!-- Columna de Descripción (4/12) -->
                                                        <div class="md:col-span-4">
                                                            <h4 class="font-medium text-gray-800 mb-2"><i class="fas fa-info-circle text-brand-lime mr-2"></i>Descripción del Video</h4>
                                                            <p class="text-sm text-gray-600 whitespace-pre-wrap"><?php echo htmlspecialchars($recurso['descripcion'] ?: 'No hay descripción disponible.'); ?></p>
                                                        </div>
                                                        <!-- Columna de Video (8/12) -->
                                                        <div class="md:col-span-8">
                                                            <div class="aspect-video bg-black rounded-lg overflow-hidden shadow-lg">
                                                                <video class="video-recurso w-full h-full" 
                                                                       controls 
                                                                       data-clase-id="<?php echo $clase['id']; ?>"
                                                                       data-curso-id="<?php echo $data['curso']['id']; ?>">
                                                                    <source src="<?php echo htmlspecialchars($recurso['ruta_archivo']); ?>" type="<?php echo htmlspecialchars($recurso['tipo_archivo']); ?>">
                                                                    Tu navegador no soporta la etiqueta de video.
                                                                </video>
                                                            </div>
                                                        </div>
                                                    </li>
                                                <?php else: ?>
                                                    <li class="flex items-start">
                                                        <i class="fas fa-file-alt text-brand-lime fa-lg mr-3 mt-1"></i>
                                                        <div>
                                                            <a href="<?php echo htmlspecialchars($recurso['ruta_archivo']); ?>" target="_blank" class="text-blue-600 hover:underline font-medium"><?php echo htmlspecialchars($recurso['nombre_original']); ?></a>
                                                            <?php if($recurso['descripcion']): ?>
                                                                <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($recurso['descripcion']); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($fase['evaluacion_id']): ?>
                            <div class="p-6 border-t bg-gray-50 text-center">
                                <?php $clases_completas = ($fase['progreso_clases']['total'] > 0 && $fase['progreso_clases']['completadas'] >= $fase['progreso_clases']['total']); ?>
                                <?php if ($clases_completas): ?>
                                    <?php $resultado = $fase['resultado_evaluacion']; ?>
                                    <?php if (isset($resultado['mejor_puntaje']) && $resultado['mejor_puntaje'] >= 80): ?>
                                        <div class="text-green-600 font-bold"><i class="fas fa-check-circle mr-2"></i>Evaluación Aprobada (<?php echo $resultado['mejor_puntaje']; ?>%)</div>
                                    <?php else: ?>
                                        <?php if (isset($resultado['mejor_puntaje'])): ?>
                                            <p class="text-sm text-red-600 mb-2">Último intento: <?php echo $resultado['mejor_puntaje']; ?>%. Necesitas 80% para aprobar.</p>
                                        <?php endif; ?>
                                        <a href="index.php?c=estudiante&a=presentarEvaluacion&eval_id=<?php echo $fase['evaluacion_id']; ?>&curso_id=<?php echo $data['curso']['id']; ?>" class="inline-block bg-lime-600 hover:bg-lime-700 text-white font-bold py-2 px-5 rounded-lg">
                                            <i class="fas fa-award mr-2"></i>Realizar Evaluación
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500 italic">Completa todas las clases de este módulo para desbloquear la evaluación.</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($data['curso_aprobado']): ?>
        <div class="mt-12 text-center bg-gray-800 text-white p-8 rounded-lg shadow-2xl">
            <i class="fas fa-certificate fa-3x text-lime-400 mb-4"></i>
            <h2 class="text-3xl font-bold">¡Felicidades, has APROBADO el curso!</h2>
            <p class="mt-2 mb-6">Has completado todas las clases y aprobado todas las evaluaciones. Ya puedes generar tu certificado.</p>
            <a href="index.php?c=estudiante&a=generarCertificado&curso_id=<?php echo $data['curso']['id']; ?>" class="inline-block bg-lime-500 hover:bg-lime-600 font-bold text-lg py-3 px-8 rounded-lg">
                Obtener mi Certificado <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        <?php endif; ?>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            accordionHeaders.forEach(button => {
                button.addEventListener('click', () => {
                    const accordionContent = button.nextElementSibling;
                    const icon = button.querySelector('i.fa-chevron-down');
                    const isExpanded = accordionContent.style.maxHeight && accordionContent.style.maxHeight !== '0px';

                    accordionHeaders.forEach(otherButton => {
                        if (otherButton !== button) {
                            otherButton.nextElementSibling.style.maxHeight = null;
                            otherButton.querySelector('i.fa-chevron-down').classList.remove('rotate-180');
                        }
                    });

                    if (isExpanded) {
                        accordionContent.style.maxHeight = null;
                        icon.classList.remove('rotate-180');
                    } else {
                        accordionContent.style.maxHeight = accordionContent.scrollHeight + "px";
                        icon.classList.add('rotate-180');
                    }
                });
            });

            const videos = document.querySelectorAll('video.video-recurso');
            videos.forEach(video => {
                video.addEventListener('ended', () => {
                    const claseId = video.dataset.claseId;
                    const cursoId = video.dataset.cursoId;
                    const url = `index.php?c=estudiante&a=marcarComoCompletada&clase_id=${claseId}&curso_id=${cursoId}`;
                    window.location.href = url;
                });
            });
        });
    </script>
</body>
</html>
