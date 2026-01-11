<?php

namespace App\Core;

class Request
{
    public static function input(string $key, $default = null)
    {
        return $_REQUEST[$key] ?? $default;
    }

    public static function sanitizeInput(string $key, $default = null)
    {
        return htmlspecialchars(strip_tags($_REQUEST[$key] ?? $default), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeAll(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
        }
        return $sanitized;
    }

    public static function file(string $key)
    {
        return $_FILES[$key] ?? null;
    }
}