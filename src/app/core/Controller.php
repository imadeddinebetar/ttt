<?php

namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . "/../views/pages/{$view}.php";
    }

    protected function redirect(string $url, int $code = 0): void
    {
        if ($code === 301) {
            header("HTTP/1.1 301 Moved Permanently");
        }
        header("Location: /" . env('APP_DIR') . "{$url}");
        exit();
    }
}