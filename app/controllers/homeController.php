<?php
// app/controllers/HomeController.php

class HomeController {

    /**
     * Muestra la página de inicio pública (landing page).
     * Este método no necesita lógica, solo cargar la vista.
     */
    public function index() {
        // Carga el archivo de la vista que creamos anteriormente.
        require_once __DIR__ . '/../views/home/index.php';
    }
}