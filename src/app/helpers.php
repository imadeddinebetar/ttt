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
