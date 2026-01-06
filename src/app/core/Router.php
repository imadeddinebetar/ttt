<?php

namespace App\Core;

class Router
{
    protected $routes = [];

    public function __construct()
    {
        $this->routes = config('routes');
    }

    public function dispatch(string $uri, string $method)
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Normalize path: remove trailing slash except for root
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }


        // exact match first
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            return $this->callHandler($handler, []);
        }

        // search dynamic routes
        foreach ($this->routes[$method] as $routePattern => $handler) {
            $regex = $this->compileRouteToRegex($routePattern);
            if (preg_match($regex, $path, $matches)) {
                // extract named params
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[$key] = $value;
                    }
                }

                return $this->callHandler($handler, array_values($params));
            }
        }

        http_response_code(404);
    }

    protected function compileRouteToRegex(string $route): string
    {
        // Ensure route starts with slash for consistency
        if ($route !== '/' && substr($route, 0, 1) !== '/') {
            $route = '/' . $route;
        }

        // Convert {param} to named capture groups
        $regex = preg_replace_callback('/\{([^}]+)\}/', function ($m) {
            $name = preg_replace('/[^a-zA-Z0-9_]/', '', $m[1]);
            return '(?P<' . $name . '>[^/]+)';
        }, $route);

        // Allow optional trailing slash when matching
        return '#^' . rtrim($regex, '/') . '/?$#';
    }

    protected function callHandler(string $handler, array $params = [])
    {
        if (!str_contains($handler, '@')) {
            throw new \Exception('Invalid route handler');
        }

        [$controller, $action] = explode('@', $handler);

        $controller = "App\\Controllers\\{$controller}";

        if (!class_exists($controller)) {
            throw new \Exception("Controller not found: {$controller}");
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $action)) {
            throw new \Exception("Method not found: {$action}");
        }

        call_user_func_array([$controllerInstance, $action], $params);
    }
}
