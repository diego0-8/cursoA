<?php
// app/views/dashboard/admin.php

// 1. Se incluye el encabezado común para todas las páginas.
// La variable $page_title fue definida en el controlador.
require_once __DIR__ . '/../common/header.php';
?>

<!-- Contenido específico de la página de Admin Dashboard -->
<div class="space-y-6">
    <!-- Sección de bienvenida -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold text-gray-800">¡Bienvenido, Administrador!</h2>
        <p class="mt-2 text-gray-600">Desde este panel tienes control total sobre la plataforma.</p>
    </div>

    <!-- Grid con tarjetas de acceso rápido -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Tarjeta para Gestionar Usuarios -->
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <h3 class="font-bold text-lg text-gray-700">Gestionar Usuarios</h3>
            <p class="mt-2 text-sm text-gray-600">Crea, edita y elimina cuentas de profesores y estudiantes.</p>
            <a href="#" class="mt-4 inline-block bg-indigo-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-indigo-700">Ir a Usuarios</a>
        </div>

        <!-- Tarjeta para Gestionar Cursos -->
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <h3 class="font-bold text-lg text-gray-700">Gestionar Cursos</h3>
            <p class="mt-2 text-sm text-gray-600">Administra los cursos disponibles en la plataforma.</p>
            <a href="#" class="mt-4 inline-block bg-indigo-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-indigo-700">Ir a Cursos</a>
        </div>

        <!-- Tarjeta de Reportes -->
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <h3 class="font-bold text-lg text-gray-700">Ver Reportes</h3>
            <p class="mt-2 text-sm text-gray-600">Consulta estadísticas de uso y progreso de los estudiantes.</p>
            <a href="#" class="mt-4 inline-block bg-indigo-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-indigo-700">Ir a Reportes</a>
        </div>
    </div>
</div>

<?php
// 2. Se incluye el pie de página común.
require_once __DIR__ . '/../common/footer.php';
?>
