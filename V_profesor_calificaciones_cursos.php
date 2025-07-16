<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Curso para Calificar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Calificaciones</h1>
            <a href="index.php?c=profesor" class="text-blue-600 hover:text-blue-700"><i class="fas fa-arrow-left mr-2"></i>Volver al Panel</a>
        </div>
    </header>
    <main class="container mx-auto px-6 py-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-6">Selecciona un curso para ver las entregas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($data['cursos'] as $curso): ?>
                <a href="index.php?c=profesor&a=calificarCurso&id_curso=<?php echo $curso['id']; ?>" class="block bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                    <h3 class="font-bold text-lg text-blue-700"><?php echo htmlspecialchars($curso['nombre']); ?></h3>
                    <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($curso['total_entregas'] ?? 0); ?> entregas pendientes</p>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>