<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?> - Reporte</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .text-brand-lime { color: #96c11f; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto my-10 p-8">
        <div class="bg-white p-8 rounded-xl shadow-lg">
            <header class="flex justify-between items-start mb-8 pb-4 border-b">
                <div>
                    <h1 class="text-2xl font-bold text-brand-dark-green"><?php echo htmlspecialchars($data['titulo']); ?></h1>
                    <p class="text-gray-500 text-sm">Generado el <?php echo date('d/m/Y H:i'); ?> por <?php echo htmlspecialchars($data['nombre_usuario']); ?></p>
                </div>
                <div class="text-right">
                     <h2 class="text-2xl font-bold text-brand-dark-green"><span class="text-brand-lime">VERTICAL</span> SAFE</h2>
                     <p class="text-xs text-gray-400">Plataforma de Cursos</p>
                </div>
            </header>

            <!-- Tabla de Usuarios -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm text-gray-600">ID</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm text-gray-600">Nombre</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm text-gray-600">Usuario/Email</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y divide-gray-200">
                        <?php foreach ($data['usuarios'] as $usuario): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4"><?php echo htmlspecialchars($usuario['id']); ?></td>
                                <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
             <footer class="text-center mt-8 pt-4 border-t text-xs text-gray-400">
                Reporte generado automáticamente por la plataforma Vertical Safe.
            </footer>
        </div>
    </div>
</body>
</html>