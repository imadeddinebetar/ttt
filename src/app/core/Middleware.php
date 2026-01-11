<?php

namespace App\Core;

class Middleware
{

    private static function init()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function auth()
    {
        self::init();
        if (!isset($_SESSION['login']) || !$_SESSION['login']) {
            header("Location: /" . env('APP_DIR') . 'login');
            exit;
        }
    }

    public static function guest()
    {
        self::init();
        if (isset($_SESSION['login']) && $_SESSION['login']) {
            header("Location: /" . env('APP_DIR'));
            exit;
        }
    }

    public static function rateLimit(string $key, int $limit = 10): void
    {
        self::init();
        $_SESSION['rate'][$key] ??= ['count' => 0, 'timestamp' => time()];

        if (time() - $_SESSION['rate'][$key]['timestamp'] > 60) {
            $_SESSION['rate'][$key] = ['count' => 1, 'timestamp' => time()];
            return;
        }

        if (++$_SESSION['rate'][$key]['count'] > $limit) {
            http_response_code(429);
            exit('Too Many Requests');
        }
    }
}
