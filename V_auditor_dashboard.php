<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']);?></title>
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
    <div class="flex h-screen">
        <aside class="w-64 bg-brand-dark-green text-white flex flex-col p-4">
            <div class="text-center py-4 mb-5 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-brand-lime">VERTICAL SAFE</h1>
                <p class="text-xs text-gray-400">Panel de Auditor</p>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php?c=auditor" class="bg-brand-lime text-brand-dark-green flex items-center py-2.5 px-4 rounded-lg font-bold">
                    <i class="fas fa-users fa-fw mr-3"></i>Usuarios Registrados
                </a>
            </nav>
            <div class="mt-auto">
                <a href="index.php?c=login&a=logout" class="flex items-center py-2.5 px-4 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition-colors">
                    <i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión
                </a>
            </div>
         </aside>

        <main class="flex-1 p-10 overflow-y-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Usuarios Registrados</h1>
                <p class="text-gray-500">Consulta y busca en la lista de todos los usuarios de la plataforma.</p>                
            </header>

            <!-- BLOQUE PARA MOSTRAR MENSAJES DE ERROR/ÉXITO -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
                    <p class="font-bold">Error</p>
                    <p><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']);?></p>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p><?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']);?></p>
                </div>
            <?php endif; ?>

            <!-- Barra de Búsqueda -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-8">
                <form action="index.php" method="GET">
                    <input type="hidden" name="c" value="auditor">
                    <input type="hidden" name="a" value="index">
                    <div class="flex items-center">
                        <i class="fas fa-search text-gray-400 mr-3"></i>
                        <input type="text" name="busqueda" placeholder="Buscar por nombre, documento, email o rol..." value="<?php echo htmlspecialchars($data['busqueda']); ?>" class="w-full p-2 border-0 focus:ring-0">
                        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg ml-4 hover:bg-blue-700">Buscar</button>
                    </div>
                </form>
            </div>

            <!-- Formulario para Generar Reportes -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Generar Reporte PDF</h2>
                <form action="index.php?c=auditor&a=generarReporte" method="POST" target="_blank">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rango de Fechas de Registro</label>
                            <div class="flex items-center gap-2 mt-1">
                                <input type="date" name="fecha_inicio" class="w-full p-2 border rounded-md text-sm">
                                <span class="text-gray-500">-</span>
                                <input type="date" name="fecha_fin" class="w-full p-2 border rounded-md text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Roles</label>
                            <div class="flex flex-wrap gap-x-4 gap-y-2">
                                <label class="flex items-center"><input type="checkbox" name="roles[]" value="administrador" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">Administradores</label>
                                <label class="flex items-center"><input type="checkbox" name="roles[]" value="profesor" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">Profesores</label>
                                <label class="flex items-center"><input type="checkbox" name="roles[]" value="estudiante" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">Estudiantes</label>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-700">
                            <i class="fas fa-file-pdf mr-2"></i>Generar PDF
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabla de Usuarios -->
             <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4 text-left text-sm font-semibold text-gray-600">Nombre</th>
                            <th class="p-4 text-left text-sm font-semibold text-gray-600">Email</th>
                            <th class="p-4 text-left text-sm font-semibold text-gray-600">Rol</th>
                            <th class="p-4 text-left text-sm font-semibold text-gray-600">Estado</th>
                            <th class="p-4 text-center text-sm font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($data['usuarios'])): ?>
                            <tr><td colspan="5" class="p-8 text-center text-gray-500">No se encontraron usuarios.</td></tr>
                        <?php else: ?>
                            <?php foreach ($data['usuarios'] as $usuario): ?>
                                <tr>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-800"><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($usuario['tipo_documento'] . ' ' . $usuario['numero_documento']);?></div>
                                    </td>
                                    <td class="p-4 text-sm text-gray-700"><?php echo htmlspecialchars($usuario['email']);?></td>
                                    <td class="p-4 text-sm text-gray-700"><?php echo ucfirst($usuario['rol']);?></td>
                                    <td class="p-4 text-sm"><span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full <?php echo $usuario['estado_cuenta'] == 'activo' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>"><?php echo ucfirst($usuario['estado_cuenta']); ?></span></td>
                                    <td class="p-4 text-center">
                                        <a href="index.php?c=auditor&a=verActividad&tipo_doc=<?php echo $usuario['tipo_documento']; ?>&num_doc=<?php echo $usuario['numero_documento']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-xs">
                                            <i class="fas fa-eye mr-1"></i>Ver Actividad
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <!-- Paginación -->
                <?php if ($data['total_paginas'] > 1): ?>
                <div class="p-4 border-t flex justify-center items-center gap-2 text-sm">
                    <a href="?c=auditor&a=index&pagina=1&busqueda=<?php echo urlencode($data['busqueda']); ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300"><i class="fas fa-angle-double-left"></i></a>
                    <?php for ($i = 1; $i <= $data['total_paginas']; $i++): ?>
                        <a href="?c=auditor&a=index&pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($data['busqueda']); ?>" class="px-3 py-1 <?php echo $i == $data['pagina_actual'] ? 'bg-blue-600 text-white' : 'bg-gray-200'; ?> rounded hover:bg-gray-300"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <a href="?c=auditor&a=index&pagina=<?php echo $data['total_paginas']; ?>&busqueda=<?php echo urlencode($data['busqueda']); ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300"><i class="fas fa-angle-double-right"></i></a>
                </div>
                <?php endif; ?>
             </div>
        </main>
    </div>
</body>
</html>
