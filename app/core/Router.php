<?php 
//clase para el enrutamiento de URLs a controladores y acciones

class Router {
    protected $routes = []; // Almacenas las rutas definidas
    protected $basePath; // la ruta base
    
    public function __construct($basePath = '/') {
        $this->basePath = rtrim($basePath, '/');  //asegura que no termine en //

    }
    
    // Define una ruta GET
    public function get($uri, $callback) {
        $this->addRoute('GET', $uri, $callback);
    }

    // Define una ruta POST
    public function post($uri, $callback) {
        $this->addRoute('POST', $uri, $callback);
    }

    // Método interno para añadir rutas
    protected function addRoute($method, $uri, $callback) {
        // Limpia la URI para asegurar consistencia (ej. /login en lugar de /login/)
        $uri = rtrim($uri, '/');
        if ($uri === '') { // Para la ruta raíz
            $uri = '/';
        }
        $this->routes[$method][$uri] = $callback;
    }

    // Despacha la solicitud actual al controlador y acción correctos
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD']; // Método de la solicitud (GET, POST, etc.)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // URI solicitada (ej. /tu_proyecto/public/login)

        // Elimina el basePath y /public del inicio de la URI
        $uri = str_replace($this->basePath . '/public', '', $uri);
        $uri = rtrim($uri, '/');
        if ($uri === '') { // Para la ruta raíz
            $uri = '/';
        }

        // Busca la ruta en las rutas definidas
        if (isset($this->routes[$method][$uri])) {
            $callback = $this->routes[$method][$uri];

            // Si el callback es un array (Controlador y método)
            if (is_array($callback)) {
                $controllerName = $callback[0];
                $methodName = $callback[1];

                // Incluye el archivo del controlador
                $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                        if (method_exists($controller, $methodName)) {
                            call_user_func_array([$controller, $methodName], []);
                        } else {
                            $this->notFound("Método de controlador no encontrado: " . $methodName);
                        }
                    } else {
                        $this->notFound("Clase de controlador no encontrada: " . $controllerName);
                    }
                } else {
                    $this->notFound("Archivo de controlador no encontrado: " . $controllerFile);
                }
            }
            // Si el callback es una función anónima (para rutas simples)
            elseif (is_callable($callback)) {
                call_user_func($callback);
            }
        } else {
            // Ruta no encontrada
            $this->notFound();
        }
    }

    // Maneja las rutas no encontradas (Error 404)
    protected function notFound($message = "Página no encontrada") {
        http_response_code(404);
        echo "<h1>Error 404: " . $message . "</h1>";
        // En un proyecto real, podrías cargar una vista de error 404
    }    
    
}


?>