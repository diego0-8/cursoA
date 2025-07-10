<?php
// app/views/home/index.php
// Esta es la página de inicio pública (landing page).
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a la Plataforma de Cursos</title>
    <!-- Incluimos Tailwind CSS para el diseño -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Barra de Navegación Pública -->
    <header class="bg-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-gray-800">
                <a href="<?= BASE_URL ?>/">TuLogo</a>
            </div>
            <div class="hidden md:flex items-center space-x-6">
                <a href="#" class="text-gray-600 hover:text-indigo-600">Cursos</a>
                <a href="#" class="text-gray-600 hover:text-indigo-600">Sobre Nosotros</a>
                <a href="#" class="text-gray-600 hover:text-indigo-600">Contacto</a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/login" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                    Iniciar Sesión
                </a>
            </div>
        </nav>
    </header>

    <!-- Sección Principal (Hero) -->
    <main>
        <section class="bg-white">
            <div class="container mx-auto px-6 py-20 text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-800 leading-tight">
                    Aprende sin límites.
                </h1>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Únete a nuestra comunidad y adquiere nuevas habilidades con cursos impartidos por expertos de la industria.
                </p>
                <div class="mt-8">
                    <a href="#" class="bg-indigo-600 text-white font-bold px-8 py-4 rounded-md hover:bg-indigo-700 transition-transform transform hover:scale-105">
                        Explorar Cursos
                    </a>
                </div>
            </div>
        </section>

        <!-- Sección de Características -->
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">¿Por qué elegirnos?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <!-- Característica 1 -->
                    <div class="bg-white p-8 rounded-lg shadow">
                        <h3 class="text-xl font-bold text-gray-800">Instructores Expertos</h3>
                        <p class="mt-2 text-gray-600">Aprende de profesionales con años de experiencia en su campo.</p>
                    </div>
                    <!-- Característica 2 -->
                    <div class="bg-white p-8 rounded-lg shadow">
                        <h3 class="text-xl font-bold text-gray-800">Aprendizaje Flexible</h3>
                        <p class="mt-2 text-gray-600">Accede a tus cursos en cualquier momento y desde cualquier dispositivo.</p>
                    </div>
                    <!-- Característica 3 -->
                    <div class="bg-white p-8 rounded-lg shadow">
                        <h3 class="text-xl font-bold text-gray-800">Certificados Oficiales</h3>
                        <p class="mt-2 text-gray-600">Obtén un certificado al completar cada curso para validar tus habilidades.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Pie de Página Público -->
    <footer class="bg-gray-800 text-white">
        <div class="container mx-auto px-6 py-8 text-center">
            <p>&copy; <?= date('Y') ?> Tu Proyecto. Todos los derechos reservados.</p>
            <p class="mt-2 text-sm text-gray-400">Construido con ❤️ para el aprendizaje.</p>
        </div>
    </footer>

</body>
</html>
