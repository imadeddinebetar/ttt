<?php

namespace App\Core;

class Env
{
    public function __construct()
    {
        die('Env class');
    }

    public static function load(string $filePath): void
    {

        if (!file_exists($filePath)) {
            throw new \Exception("Environment file not found: {$filePath}");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Skip comments
            }
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}