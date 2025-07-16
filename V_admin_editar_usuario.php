<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Barra Lateral -->
        <div class="w-64 bg-gray-800 text-white p-5 flex flex-col">
            <h2 class="text-2xl font-bold mb-10">AdminPanel</h2>
            <nav class="flex flex-col space-y-2">
                <a href="index.php?c=admin" class="flex items-center p-2 hover:bg-gray-700 rounded-md"><i class="fas fa-tachometer-alt mr-3"></i>Dashboard</a>
                <a href="index.php?c=admin&a=gestionarUsuarios" class="flex items-center p-2 bg-gray-700 rounded-md"><i class="fas fa-users mr-3"></i>Gestionar Usuarios</a>
                <a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded-md"><i class="fas fa-book mr-3"></i>Gestionar Cursos</a>
            </nav>
            <div class="mt-auto">
                <a href="index.php?c=login&a=logout" class="flex items-center p-2 hover:bg-red-600 rounded-md"><i class="fas fa-sign-out-alt mr-3"></i>Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Editar Usuario</h1>

            <div class="bg-white p-8 rounded-lg shadow-md max-w-4xl mx-auto">
                <form action="index.php?c=admin&a=actualizarUsuario" method="POST">
                    <!-- Campos ocultos para identificar al usuario -->
                    <input type="hidden" name="tipo_documento" value="<?php echo htmlspecialchars($data['usuario']['tipo_documento']); ?>">
                    <input type="hidden" name="numero_documento" value="<?php echo htmlspecialchars($data['usuario']['numero_documento']); ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($data['usuario']['nombre']); ?>" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido</label>
                            <input type="text" name="apellido" id="apellido" value="<?php echo htmlspecialchars($data['usuario']['apellido']); ?>" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($data['usuario']['email']); ?>" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="tel" name="telefono" id="telefono" value="<?php echo htmlspecialchars($data['usuario']['telefono'] ?? ''); ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="rol" class="block text-sm font-medium text-gray-700">Rol</label>
                            <select name="rol" id="rol" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                                <option value="estudiante" <?php echo $data['usuario']['rol'] == 'estudiante' ? 'selected' : ''; ?>>Estudiante</option>
                                <option value="profesor" <?php echo $data['usuario']['rol'] == 'profesor' ? 'selected' : ''; ?>>Profesor</option>
                                <option value="administrador" <?php echo $data['usuario']['rol'] == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                            <input type="password" name="password" id="password" placeholder="Dejar en blanco para no cambiar" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <a href="index.php?c=admin&a=gestionarUsuarios" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">Cancelar</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Actualizar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
