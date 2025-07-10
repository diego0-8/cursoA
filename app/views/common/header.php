<?php
// app/views/common/header.php

// Si no se ha definido un título para la página, usamos uno por defecto.
$page_title = $page_title ?? 'Mi Proyecto';
// Obtenemos el nombre de usuario de la sesión para mostrarlo.
$user_name = $_SESSION['user_name'] ?? 'Invitado';

?>
<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- El título se establece dinámicamente desde el controlador -->
    <title><?= htmlspecialchars($page_title) ?></title>
    <!-- Incluimos Tailwind CSS para el diseño -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full">
<div class="min-h-full">
  <nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <!-- Puedes poner tu logo aquí -->
            <img class="h-8 w-8" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" alt="Tu Empresa">
          </div>
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-4">
              <!-- El enlace al dashboard estará activo si la página actual es el dashboard -->
              <a href="<?= BASE_URL ?>/dashboard" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Dashboard</a>
              <!-- Aquí puedes añadir más enlaces de navegación -->
            </div>
          </div>
        </div>
        <div class="hidden md:block">
          <div class="ml-4 flex items-center md:ml-6">
            <span class="text-gray-400 mr-4">Hola, <?= htmlspecialchars($user_name) ?></span>
            <!-- Menú desplegable de usuario -->
            <div class="relative ml-3">
              <div>
                <a href="<?= BASE_URL ?>/logout" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Cerrar Sesión</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?= htmlspecialchars($page_title) ?></h1>
    </div>
  </header>
  <main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
      <!-- El contenido principal de cada página irá aquí -->
