<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #certificate-container, #certificate-container * {
                visibility: visible;
            }
            #certificate-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none;
            }
        }
        .font-playfair { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-gray-200">

    <div class="no-print container mx-auto p-4 text-center">
        <a href="index.php?c=estudiante&a=misCursos" class="inline-block bg-gray-600 text-white font-bold py-2 px-4 rounded hover:bg-gray-700 transition-colors mr-3">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Mis Cursos
        </a>
        <button onclick="window.print()" class="bg-brand-lime text-white font-bold py-2 px-4 rounded hover:bg-brand-lime-dark transition-colors">
            <i class="fas fa-print mr-2"></i>Imprimir / Guardar como PDF
        </button>
    </div>

    <div id="certificate-container" class="container mx-auto my-10 p-10 bg-white max-w-4xl shadow-2xl">
        <div class="border-4 border-brand-dark-green p-8 relative">
            <div class="text-center">
                <h1 class="font-playfair text-5xl text-brand-dark-green">Certificado de Finalización</h1>
                <p class="text-lg mt-4 text-gray-600">Se otorga a</p>
                <p class="font-playfair text-4xl mt-6 text-brand-lime"><?php echo htmlspecialchars($data['certificado']['nombre_estudiante']); ?></p>
                <div class="w-1/4 h-0.5 bg-gray-300 mx-auto my-6"></div>
                <p class="text-lg text-gray-600">Por haber completado satisfactoriamente el curso</p>
                <h2 class="font-playfair text-3xl mt-4 text-gray-800">"<?php echo htmlspecialchars($data['certificado']['nombre_curso']); ?>"</h2>
            </div>
            
            <div class="flex justify-between items-end mt-16">
                <div class="text-left">
                    <p class="text-sm text-gray-500">Fecha de Emisión</p>
                    <p class="font-bold text-gray-700"><?php echo date("d/m/Y", strtotime($data['certificado']['fecha_emision'])); ?></p>
                </div>
                <div class="text-center">
                    <p class="font-bold text-brand-dark-green border-t-2 border-gray-400 px-8 pt-2">Firma Autorizada</p>
                    <p class="text-sm text-gray-600">Vertical Safe</p>
                </div>
            </div>

            <div class="absolute bottom-4 right-4 text-right text-xs text-gray-400">
                <p>Código de Verificación:</p>
                <p><?php echo htmlspecialchars($data['certificado']['codigo_unico']); ?></p>
            </div>
        </div>
    </div>

</body>
</html>
