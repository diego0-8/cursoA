<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .bg-brand-lime { background-color: #96c11f; }
        .text-brand-lime { color: #96c11f; }
        .hover\:bg-brand-lime-dark:hover { background-color: #82a81b; }
        .sidebar-link { transition: background-color 0.2s, color 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background-color: #96c11f; color: #1a3a3a; }
        .sidebar-link.active i, .sidebar-link:hover i { color: #1a3a3a; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Barra Lateral de Navegación -->
        <aside class="w-64 bg-brand-dark-green text-white flex flex-col p-4">
            <div class="text-center py-4 mb-5 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-brand-lime">VERTICAL SAFE</h1>
                <p class="text-xs text-gray-400">Admin Panel</p>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php?c=admin" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-tachometer-alt fa-fw mr-3"></i>Dashboard</a>
                <a href="index.php?c=admin&a=gestionarUsuarios" class="sidebar-link active flex items-center py-2.5 px-4 rounded-lg font-bold"><i class="fas fa-users fa-fw mr-3"></i>Usuarios</a>
                <a href="index.php?c=admin&a=gestionarCursos" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-book-open fa-fw mr-3"></i>Cursos</a>
                <a href="index.php?c=admin&a=gestionarInscripciones" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-clipboard-check fa-fw mr-3"></i>Inscripciones</a>
            </nav>
            <div class="mt-auto">
                 <a href="index.php?c=login&a=logout" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 p-10 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestionar Usuarios</h1>
                    <p class="text-gray-500">Crea, busca, edita y gestiona todos los usuarios de la plataforma.</p>
                </div>
                <button onclick="toggleCreateForm()" class="bg-brand-lime hover:bg-brand-lime-dark text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors">
                    <i class="fas fa-plus mr-2"></i>Crear Usuario
                </button>
            </header>

            <!-- Formulario para Crear Nuevo Usuario (Oculto por defecto) -->
            <div id="create-user-form" class="hidden bg-white p-6 rounded-lg shadow-lg mb-8">
                <h2 class="text-xl font-bold mb-4 text-gray-700">Formulario de Creación</h2>
                <form action="index.php?c=admin&a=crearUsuario" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                    <input type="text" name="nombre" placeholder="Nombre" required class="p-2 border rounded-md">
                    <input type="text" name="apellido" placeholder="Apellido" required class="p-2 border rounded-md">
                    <input type="email" name="email" placeholder="Email" required class="p-2 border rounded-md">
                    <input type="text" name="numero_documento" placeholder="N° Documento" required class="p-2 border rounded-md">
                    <select name="tipo_documento" required class="p-2 border rounded-md"><option value="CC">Cédula de Ciudadanía</option><option value="CE">Cédula de Extranjería</option></select>
                    <select name="rol" required class="p-2 border rounded-md"><option value="estudiante">Estudiante</option><option value="profesor">Profesor</option><option value="administrador">Administrador</option></select>
                    <input type="password" name="password" placeholder="Contraseña" required class="p-2 border rounded-md">
                    <input type="tel" name="telefono" placeholder="Teléfono (Opcional)" class="p-2 border rounded-md">
                    <button type="submit" class="lg:col-start-3 bg-brand-lime hover:bg-brand-lime-dark text-white p-2 rounded-lg font-semibold">Guardar Usuario</button>
                </form>
            </div>
            
            <!-- Mensajes de feedback -->
            <?php if (isset($_SESSION['mensaje'])): ?><div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-r-lg" role="alert"><?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']);?></div><?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?><div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg" role="alert"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']);?></div><?php endif; ?>

            <!-- Tabla de Usuarios -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 border-b">
                    <form action="index.php" method="GET" class="flex items-center gap-4">
                        <input type="hidden" name="c" value="admin"><input type="hidden" name="a" value="gestionarUsuarios">
                        <i class="fas fa-search text-gray-400"></i>
                        <input type="text" name="busqueda" placeholder="Buscar por nombre, documento, email o rol..." value="<?php echo htmlspecialchars($data['busqueda']); ?>" class="flex-grow p-2 border-0 focus:ring-0">
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 text-left text-sm font-semibold text-gray-600">Nombre</th>
                                <th class="p-4 text-left text-sm font-semibold text-gray-600">Documento</th>
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
                                    <td class="p-4"><div class="font-bold text-gray-800"><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></div><div class="text-xs text-gray-500"><?php echo htmlspecialchars($usuario['email']); ?></div></td>
                                    <td class="p-4 text-sm text-gray-700"><?php echo htmlspecialchars($usuario['tipo_documento'] . ' ' . $usuario['numero_documento']); ?></td>
                                    <td class="p-4"><span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full <?php echo $usuario['rol'] == 'administrador' ? 'bg-red-100 text-red-700' : ($usuario['rol'] == 'profesor' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-800'); ?>"><?php echo ucfirst($usuario['rol']); ?></span></td>
                                    <td class="p-4"><span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full <?php echo $usuario['estado_cuenta'] == 'activo' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>"><?php echo ucfirst($usuario['estado_cuenta']); ?></span></td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="index.php?c=admin&a=editarUsuario&tipo_doc=<?php echo $usuario['tipo_documento']; ?>&num_doc=<?php echo $usuario['numero_documento']; ?>" class="text-yellow-500 hover:text-yellow-700" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                                            <?php if ($usuario['email'] !== $_SESSION['usuario_email']): ?>
                                                <a href="index.php?c=admin&a=cambiarEstadoUsuario&tipo_doc=<?php echo $usuario['tipo_documento']; ?>&num_doc=<?php echo $usuario['numero_documento']; ?>" class="<?php echo $usuario['estado_cuenta'] === 'activo' ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700'; ?>" title="<?php echo $usuario['estado_cuenta'] === 'activo' ? 'Deshabilitar' : 'Habilitar'; ?>"><i class="fas fa-toggle-<?php echo $usuario['estado_cuenta'] === 'activo' ? 'on' : 'off'; ?>"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Paginación -->
                <?php if ($data['total_paginas'] > 1): ?>
                <div class="p-4 border-t flex justify-center items-center gap-2 text-sm">
                    <a href="?c=admin&a=gestionarUsuarios&pagina=1&busqueda=<?php echo urlencode($data['busqueda']); ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300"><i class="fas fa-angle-double-left"></i></a>
                    <?php for ($i = 1; $i <= $data['total_paginas']; $i++): ?>
                        <a href="?c=admin&a=gestionarUsuarios&pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($data['busqueda']); ?>" class="px-3 py-1 <?php echo $i == $data['pagina_actual'] ? 'bg-brand-lime text-white' : 'bg-gray-200'; ?> rounded hover:bg-gray-300"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <a href="?c=admin&a=gestionarUsuarios&pagina=<?php echo $data['total_paginas']; ?>&busqueda=<?php echo urlencode($data['busqueda']); ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300"><i class="fas fa-angle-double-right"></i></a>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script>
        function toggleCreateForm() {
            const form = document.getElementById('create-user-form');
            form.classList.toggle('hidden');
        }
    </script>
</body>
</html>