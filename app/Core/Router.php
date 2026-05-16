<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, string $action, array $middleware = []): void
    {
        $this->add('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, string $action, array $middleware = []): void
    {
        $this->add('POST', $uri, $action, $middleware);
    }

    private function add(string $method, string $uri, string $action, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'uri'        => $uri,
            'pattern'    => $this->buildPattern($uri),
            'action'     => $action,
            'middleware' => $middleware,
        ];
    }

    /**
     * Convert route URI into a regex pattern.
     * Supports {param} named placeholders.
     */
    private function buildPattern(string $uri): string
    {
        $uri     = rtrim($uri, '/') ?: '/';
        $pattern = preg_quote($uri, '#');
        $pattern = preg_replace('/\\\{([a-zA-Z_]+)\\\}/', '([^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $uri    = parse_url($requestUri, PHP_URL_PATH);
        $uri    = rawurldecode($uri);
        $uri    = rtrim($uri, '/') ?: '/';

        // Strip BASE_URI prefix if app is in a subdirectory
        if (BASE_URI !== '' && str_starts_with($uri, BASE_URI)) {
            $uri = substr($uri, strlen(BASE_URI)) ?: '/';
        }

        $method = strtoupper($requestMethod);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (!preg_match($route['pattern'], $uri, $matches)) {
                continue;
            }

            array_shift($matches); // Remove full match, keep capture groups

            // Run middleware stack
            foreach ($route['middleware'] as $middlewareClass) {
                $mw = new $middlewareClass();
                $mw->handle();
            }

            // Resolve and call controller action
            [$controllerClass, $method] = explode('@', $route['action']);

            // Support both namespaced and non-namespaced controller names
            if (!str_contains($controllerClass, '\\')) {
                $controllerClass = 'Controllers\\' . $controllerClass;
            }

            if (!class_exists($controllerClass)) {
                $this->abort(500, "Controller not found: {$controllerClass}");
                return;
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $method)) {
                $this->abort(500, "Method not found: {$method}");
                return;
            }

            $controller->$method(...$matches);
            return;
        }

        // No route matched
        http_response_code(404);
        include BASE_PATH . '/views/errors/404.php';
    }

    private function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        if (APP_DEBUG && $message) {
            echo "<h1>Error {$code}</h1><p>" . htmlspecialchars($message) . '</p>';
        } else {
            $file = BASE_PATH . '/views/errors/' . $code . '.php';
            if (file_exists($file)) {
                include $file;
            }
        }
        exit;
    }
}
