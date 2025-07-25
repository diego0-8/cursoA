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
        .text-brand-lime { color: #96c11f; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div class="hidden lg:flex w-1/2 bg-brand-dark-green items-center justify-center p-12 text-white flex-col">
            <div class="max-w-md text-center">
                <i class="fas fa-shield-alt fa-5x text-brand-lime mb-6"></i>
                <h1 class="text-3xl font-bold mb-4">Verificación de Certificados</h1>
                <p class="text-gray-300">Este módulo permite verificar la autenticidad de los certificados emitidos por nuestra plataforma. Ingrese el número de documento del estudiante para consultar sus certificaciones válidas.</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-2xl">
                <div class="text-center mb-8">
                     <h2 class="text-3xl font-bold text-gray-800">Consultar Certificado</h2>
                     <p class="text-gray-500 mt-2">Ingrese un número de documento para buscar.</p>
                </div>

                <form action="index.php?c=publico&a=buscarCertificado" method="POST" class="space-y-4 bg-white p-8 rounded-lg shadow-md">
                    <div>
                        <label for="numero_documento" class="block text-sm font-medium text-gray-700">Número de Documento</label>
                        <div class="flex items-center gap-4 mt-1">
                            <input type="text" name="numero_documento" id="numero_documento" value="<?php echo htmlspecialchars($data['numero_buscado'] ?? ''); ?>" required class="flex-grow px-4 py-3 bg-gray-50 border border-gray-300 rounded-md shadow-sm">
                            <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-6 rounded-md hover:bg-blue-700">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>

                <?php if (isset($data['busqueda_realizada'])): ?>
                <div class="mt-8 bg-white p-8 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-4">Resultados de la Búsqueda</h3>
                    <?php if (empty($data['resultados'])): ?>
                        <p class="text-center text-gray-500 py-8"><i class="fas fa-exclamation-circle mr-2"></i>No se encontraron certificados para el documento ingresado.</p>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Estudiante</th>
                                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Curso Certificado</th>
                                        <th class="p-3 text-left text-sm font-semibold text-gray-600">Fecha de Emisión</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <?php foreach ($data['resultados'] as $certificado): ?>
                                    <tr>
                                        <td class="p-3 font-medium"><?php echo htmlspecialchars($certificado['nombre_completo']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($certificado['nombre_curso']); ?></td>
                                        <td class="p-3 text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($certificado['fecha_emision'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="text-center mt-8 text-sm text-gray-600">
                    <a href="index.php?c=login" class="font-bold text-brand-lime hover:underline">
                        <i class="fas fa-arrow-left"></i> Volver a Iniciar Sesión
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
