<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['titulo']; ?></title>
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
    <div class="container mx-auto p-10">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Gestionar Evaluación</h1>
                <p class="text-gray-500">Módulo: <?php echo htmlspecialchars($data['fase_info']['nombre']); ?> (Curso: <?php echo htmlspecialchars($data['curso_info']['nombre']); ?>)</p>
            </div>
            <a href="index.php?c=profesor&a=gestionarCurso&id=<?php echo $data['curso_info']['id']; ?>" class="text-gray-600 hover:text-brand-lime font-semibold"><i class="fas fa-arrow-left mr-2"></i>Volver a la Gestión del Curso</a>
        </header>

        <form action="index.php?c=profesor&a=guardarEvaluacion" method="POST">
            <input type="hidden" name="fase_id" value="<?php echo $data['fase_info']['id']; ?>">

            <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                <label for="titulo_evaluacion" class="block text-lg font-bold text-gray-700 mb-2">Título de la Evaluación</label>
                <input type="text" name="titulo_evaluacion" id="titulo_evaluacion" value="<?php echo htmlspecialchars($data['evaluacion_existente']['evaluacion']['titulo'] ?? 'Evaluación del Módulo'); ?>" required class="w-full p-2 border rounded-md">
            </div>

            <div id="preguntas-container" class="space-y-6">
                <!-- Las preguntas se añadirán aquí dinámicamente -->
            </div>

            <div class="flex justify-between items-center mt-8">
                <button type="button" id="add-pregunta-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-plus mr-2"></i>Añadir Pregunta
                </button>
                <button type="submit" class="bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-3 px-6 rounded-lg text-lg">
                    <i class="fas fa-save mr-2"></i>Guardar Evaluación
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('preguntas-container');
            const addPreguntaBtn = document.getElementById('add-pregunta-btn');
            let preguntaIndex = 0;

            function addPregunta(preguntaData = null) {
                const preguntaDiv = document.createElement('div');
                preguntaDiv.className = 'bg-white p-6 rounded-lg shadow-md border border-gray-200';
                preguntaDiv.innerHTML = `
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-md font-bold text-gray-800">Pregunta ${preguntaIndex + 1}</label>
                        <button type="button" class="remove-pregunta-btn text-red-500 hover:text-red-700"><i class="fas fa-trash"></i> Eliminar</button>
                    </div>
                    <textarea name="pregunta[${preguntaIndex}]" placeholder="Escribe aquí el enunciado de la pregunta..." required class="w-full p-2 border rounded-md mb-4">${preguntaData ? preguntaData.texto_pregunta : ''}</textarea>
                    <div class="pl-4 border-l-4 border-lime-200 space-y-2">
                        ${[0, 1, 2, 3].map(opcionIndex => `
                        <div class="flex items-center">
                            <input type="radio" name="correcta[${preguntaIndex}]" value="${opcionIndex}" required class="mr-3 h-5 w-5 text-lime-600 focus:ring-lime-500" ${preguntaData && preguntaData.opciones[opcionIndex] && preguntaData.opciones[opcionIndex].es_correcta == 1 ? 'checked' : ''}>
                            <input type="text" name="opciones[${preguntaIndex}][${opcionIndex}]" placeholder="Opción ${opcionIndex + 1}" required class="w-full p-2 border rounded-md" value="${preguntaData && preguntaData.opciones[opcionIndex] ? preguntaData.opciones[opcionIndex].texto_opcion : ''}">
                        </div>
                        `).join('')}
                    </div>
                `;
                container.appendChild(preguntaDiv);

                preguntaDiv.querySelector('.remove-pregunta-btn').addEventListener('click', () => {
                    preguntaDiv.remove();
                });

                preguntaIndex++;
            }

            addPreguntaBtn.addEventListener('click', () => addPregunta());

            // Si hay una evaluación existente, la cargamos
            const evaluacionExistente = <?php echo json_encode($data['evaluacion_existente']['preguntas'] ?? null); ?>;
            if (evaluacionExistente && evaluacionExistente.length > 0) {
                evaluacionExistente.forEach(pregunta => {
                    addPregunta(pregunta);
                });
            } else {
                // Iniciar con una pregunta por defecto si no hay ninguna
                addPregunta();
            }
        });
    </script>
</body>
</html>
