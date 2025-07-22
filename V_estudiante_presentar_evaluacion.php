<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-10 max-w-4xl">
        <header class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($data['titulo']); ?></h1>
            <p class="text-gray-500">Selecciona la respuesta correcta para cada pregunta.</p>
        </header>

        <form action="index.php?c=estudiante&a=enviarEvaluacion" method="POST">
            <input type="hidden" name="evaluacion_id" value="<?php echo $data['evaluacion']['id']; ?>">
            <input type="hidden" name="curso_id" value="<?php echo $data['curso_id']; ?>">
            
            <div class="bg-white p-8 rounded-lg shadow-md space-y-8">
                <?php $num_pregunta = 1; foreach ($data['evaluacion']['preguntas'] as $pregunta): ?>
                    <fieldset>
                        <legend class="text-lg font-semibold text-gray-900 mb-4"><?php echo $num_pregunta . ". " . htmlspecialchars($pregunta['texto_pregunta']); ?></legend>
                        <div class="space-y-3">
                            <?php foreach ($pregunta['opciones'] as $opcion): ?>
                                <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="respuestas[<?php echo $pregunta['id']; ?>]" value="<?php echo $opcion['id']; ?>" required class="h-4 w-4 text-lime-600 border-gray-300 focus:ring-lime-500">
                                    <span class="ml-3 text-gray-700"><?php echo htmlspecialchars($opcion['texto']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                <?php $num_pregunta++; endforeach; ?>
            </div>

            <div class="mt-8 text-center">
                <button type="submit" class="bg-lime-600 hover:bg-lime-700 text-white font-bold py-3 px-10 rounded-lg text-lg">
                    <i class="fas fa-paper-plane mr-2"></i>Enviar Respuestas
                </button>
            </div>
        </form>
    </div>
</body>
</html>
