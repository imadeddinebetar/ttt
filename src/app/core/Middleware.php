<?php

namespace App\Core;

class Middleware
{
    public static function rateLimit(string $key, int $limit = 10): void
    {
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
