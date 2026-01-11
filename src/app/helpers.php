<?php


if (!function_exists('config')) {
    function config($filename)
    {
        $config = require __DIR__ . '/../config/' . $filename . '.php';
        return $config;
    }
}

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = $_ENV[$key] ?? $default;

        // Return default if value is null
        if ($value === null) {
            return $default;
        }

        // Convert to string for processing
        $value = (string) $value;

        // Trim whitespace
        $value = trim($value);

        // Remove surrounding quotes (both single and double)
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        // Handle special values
        if ($value === '') {
            return '';
        }

        $lower = strtolower($value);
        if ($lower === 'null') {
            return null;
        }
        if ($lower === 'true') {
            return true;
        }
        if ($lower === 'false') {
            return false;
        }

        return $value;
    }
}

if (!function_exists('e')) {
    function e($string)
    {
        return $string === null ? '' : htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('now')) {
    function now(string $format = 'Y-m-d H:i:s'): string
    {
        return (new DateTime('now'))->format($format);
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return __DIR__ . '/../storage/' . ($path ? ltrim($path, '/') : '');
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        return  '/' . env('APP_DIR') . 'public/' . ($path ? ltrim($path, '/') : '');
    }
}

if (!function_exists('dd')) {
    function dd(...$data)
    {
        foreach ($data as $element) {
            echo '<pre style="background:#111;color:#0f0;padding:10px;border-radius:6px;">';
            print_r($element);
            echo '</pre>';
        }
        die();
    }
}


if (!function_exists('excelDateToPhpDate')) {
    function excelDateToPhpDate($excelDate)
    {
        return date(
            'Y-m-d',
            ($excelDate - 25569) * 86400
        );
    }
}
