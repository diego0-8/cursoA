<?php 
// clase para el manejo de sesiones de usuario 

class Session {

    //Inicia la sesion si todavia no esta iniciada 
    public static function init(){
        if (session_status() == PHP_SESSION_NONE){
            session_start();
        }
    }

    //Establace un valor en la sesion
    public static function set($key, $value){
        $_SESSION[$key] = $value;
    }

    //obtine un valor de la sesion
    public static function get($key){
        if (isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
        return null;
    }

    //Verifica si una clave existe en la sesion
    public static function exists($key){
        return isset($_SESSION[$key]);
    }

    //Elimina un valor de la sesión
    public static function delete ($key){
        if (isset($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
    }

    //Destruye toda la sesión 
    public static function destroy(){
        session_unset();  //Elimina todas las variables de una sesion
        session_destroy(); //Destruye la sesión 
    }

    //Establece un mensaje flash
    public static function setFlash($name, $message){
        self::set('flash_' . $name, $message);
    }

    //obtiene y elimina el mensaje flash
    public static function getFlash ($name){
        $message = self::get('flash_' . $name);
        self::delete('flash_' . $name);
        return $message;
    }
}

?>