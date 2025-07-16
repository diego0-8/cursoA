<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($data['titulo']); ?></h1>
                <p class="text-gray-600">Gestiona las clases y sube material de apoyo.</p>
            </div>
            <a href="index.php?c=cursos" class="text-blue-500 hover:text-blue-700">← Volver a Mis Cursos</a>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert"><?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="space-y-8">
            <?php foreach ($data['clases'] as $clase): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($clase['titulo']); ?></h2>
                    <p class="text-sm text-gray-500 mb-4">Fase: <?php echo htmlspecialchars($clase['nombre_fase']); ?></p>
                    
                    <!-- Formulario de subida -->
                    <form action="index.php?c=cursos&a=subirRecurso" method="POST" enctype="multipart/form-data" class="mb-6 p-4 border-dashed border-2 rounded-lg">
                        <input type="hidden" name="clase_id" value="<?php echo $clase['id']; ?>">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                            <input type="file" name="archivo" required class="col-span-1 md:col-span-1">
                            <select name="tipo_recurso" required class="p-2 border rounded col-span-1 md:col-span-1">
                                <option value="documento">Documento (PDF, Word, Excel)</option>
                                <option value="video">Video</option>
                                <option value="audio">Audio</option>
                                <option value="presentacion">Presentación</option>
                                <option value="otro">Otro</option>
                            </select>
                            <button type="submit" class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 col-span-1 md:col-span-1"><i class="fas fa-upload mr-2"></i>Subir Recurso</button>
                        </div>
                    </form>

                    <!-- Lista de recursos existentes -->
                    <h3 class="font-semibold mb-2">Recursos Subidos:</h3>
                    <?php if (empty($clase['recursos'])): ?>
                        <p class="text-gray-500 text-sm">Aún no hay recursos para esta clase.</p>
                    <?php else: ?>
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach ($clase['recursos'] as $recurso): ?>
                                <li class="text-sm"><a href="<?php echo htmlspecialchars($recurso['ruta_archivo']); ?>" target="_blank" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($recurso['nombre_original']); ?></a> (<?php echo htmlspecialchars($recurso['tipo_recurso']); ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>