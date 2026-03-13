<?php

declare(strict_types=1);

final class Router
{
    private App $app;

    /**
     * @var array<string, array<string, array{0:string,1:string}>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$this->normalizePath($path)] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = $this->normalizePath((string) parse_url($uri, PHP_URL_PATH));
        $method = strtoupper($method);

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 - Sidan kunde inte hittas.';
            return;
        }

        [$controllerName, $action] = $handler;

        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo 'Controller saknas: ' . htmlspecialchars($controllerName, ENT_QUOTES, 'UTF-8');
            return;
        }

        $controller = new $controllerName($this->app);

        if (!method_exists($controller, $action)) {
            http_response_code(500);
            echo 'Action saknas: ' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8');
            return;
        }

        $controller->{$action}();
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            return '/';
        }

        $path = '/' . trim($path, '/');

        return $path === '' ? '/' : $path;
    }
}
