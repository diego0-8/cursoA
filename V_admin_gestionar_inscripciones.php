<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Inscripciones - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-dark-green { background-color: #1a3a3a; }
        .bg-brand-lime { background-color: #96c11f; }
        .text-brand-lime { color: #96c11f; }
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
                <a href="index.php?c=admin&a=gestionarUsuarios" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-users fa-fw mr-3"></i>Usuarios</a>
                <a href="index.php?c=admin&a=gestionarCursos" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg"><i class="fas fa-book-open fa-fw mr-3"></i>Cursos</a>
                <a href="index.php?c=admin&a=gestionarInscripciones" class="sidebar-link active flex items-center py-2.5 px-4 rounded-lg font-bold"><i class="fas fa-clipboard-check fa-fw mr-3"></i>Inscripciones</a>
            </nav>
            <div class="mt-auto">
                 <a href="index.php?c=login&a=logout" class="sidebar-link flex items-center py-2.5 px-4 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-sign-out-alt fa-fw mr-3"></i>Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 p-10 overflow-y-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Solicitudes de Inscripción Pendientes</h1>
                <p class="text-gray-500">Aprueba o rechaza las solicitudes de los estudiantes para acceder a los cursos.</p>
            </header>
            
            <!-- Mensajes de feedback -->
            <?php if (isset($_SESSION['mensaje'])): ?><div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-r-lg" role="alert"><?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']);?></div><?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?><div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg" role="alert"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']);?></div><?php endif; ?>

            <!-- Tabla de Solicitudes -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 text-left text-sm font-semibold text-gray-600">Estudiante</th>
                                <th class="p-4 text-left text-sm font-semibold text-gray-600">Curso Solicitado</th>
                                <th class="p-4 text-left text-sm font-semibold text-gray-600">Fecha</th>
                                <th class="p-4 text-center text-sm font-semibold text-gray-600">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($data['solicitudes'])): ?>
                                <tr><td colspan="4" class="text-center p-12 text-gray-500"><i class="fas fa-inbox text-4xl mb-3"></i><p>¡Todo en orden! No hay solicitudes pendientes.</p></td></tr>
                            <?php else: ?>
                                <?php foreach ($data['solicitudes'] as $solicitud): ?>
                                <tr>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-800"><?php echo htmlspecialchars($solicitud['nombre_estudiante']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($solicitud['email_estudiante']); ?></div>
                                    </td>
                                    <td class="p-4 text-sm font-medium text-gray-700"><?php echo htmlspecialchars($solicitud['nombre_curso']); ?></td>
                                    <td class="p-4 text-sm text-gray-600"><?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])); ?></td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-4">
                                            <a href="index.php?c=admin&a=procesarInscripcion&id=<?php echo $solicitud['id']; ?>&accion=aprobar" class="bg-green-100 text-green-600 hover:bg-green-200 px-3 py-1 rounded-full text-sm font-semibold" title="Aprobar">
                                                <i class="fas fa-check mr-1"></i> Aprobar
                                            </a>
                                            <a href="index.php?c=admin&a=procesarInscripcion&id=<?php echo $solicitud['id']; ?>&accion=rechazar" class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-full text-sm font-semibold" title="Rechazar">
                                                <i class="fas fa-times mr-1"></i> Rechazar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
