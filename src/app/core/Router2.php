<?php
// app/Core/Router.php
namespace App\Core;

class Router2
{
    private array $routes = [];
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get(string $path, string $controller, string $method)
    {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function post(string $path, string $controller, string $method)
    {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch(string $requestMethod, string $uri)
    {
        // Remove query string and clean URI
        $uri = strtok($uri, '?');
        $uri = trim($uri, '/');

        // Check for exact match first
        if (isset($this->routes[$requestMethod]['/' . $uri])) {
            return $this->executeRoute($this->routes[$requestMethod]['/' . $uri], []);
        }

        // Check for dynamic routes
        foreach ($this->routes[$requestMethod] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, '/' . $uri, $matches)) {
                array_shift($matches); // Remove full match
                return $this->executeRoute($handler, $matches);
            }
        }

        http_response_code(404);
        echo "404 - Route not found";
    }

    private function executeRoute(array $handler, array $params)
    {
        $controllerClass = $handler['controller'];
        $method = $handler['method'];

        // Use container to resolve controller with dependencies
        $controller = $this->container->make($controllerClass);

        if (!method_exists($controller, $method)) {
            throw new \Exception("Method $method does not exist in $controllerClass");
        }

        return call_user_func_array([$controller, $method], $params);
    }
}
