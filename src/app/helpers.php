<?php

if (!function_exists('config')) {
    function config($filename)
    {
        $config = require __DIR__ . '/../config/' . $filename . '.php';
        return $config;
    }
}


if (!function_exists('view')) {
    function view($template, $data = [])
    {
        extract($data);
        $templatePath = __DIR__ . '//view/pages/' . $template . '.php';
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            echo "Template not found: " . htmlspecialchars($template);
        }
    }
}


if (!function_exists('publicPath')) {
    function publicPath($path = '')
    {
        return '/' . ltrim($path, '/');
    }
}
