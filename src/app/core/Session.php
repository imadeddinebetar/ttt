<?php

namespace App\Core;

class Session
{
    private static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value){
        self::init();
        $_SESSION[$key] = $value;
    }

    public static function get($key){
        self::init();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function remove($key){
        self::init();
        unset($_SESSION[$key]);
    }

    public static function clear(){
        self::init();
        session_destroy();
    }
}