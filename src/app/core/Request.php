<?php

namespace App\Core;

class Request
{
    private static function input(string $key, $default = null)
    {
        return $_REQUEST[$key] ?? $default;
    }

    public static function sanitizedInput(string $key, $default = null)
    {
        $value = self::input($key, $default);

        if ($value === null || $value === $default) {
            return $default;
        }

        if (is_string($value)) {
            return htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
        }

        return $value;
    }

    public static function string(string $key, string $default = ''): string
    {
        $value = self::input($key, $default);
        return htmlspecialchars(strip_tags((string)$value), ENT_QUOTES, 'UTF-8');
    }

    public static function int(string $key, int $default = 0): int
    {
        return (int) self::input($key, $default);
    }

    public static function float(string $key, float $default = 0.0): float
    {
        return (float) self::input($key, $default);
    }

    public static function bool(string $key, bool $default = false): bool
    {
        return (bool) self::input($key, $default);
    }

    public static function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }
}
