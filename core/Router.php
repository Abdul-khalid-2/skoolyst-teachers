<?php

/**
 * Router
 * Minimal, dependency-free router supporting {param} placeholders.
 * All requests pass through public/index.php -> this router.
 */
class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        // Strip the app's subfolder (BASE_PATH) so routes like '/login' match
        // correctly whether the app lives at the domain root or inside a
        // subfolder such as /projects/teacher-portfolio on localhost.
        if (BASE_PATH !== '' && strpos($uri, BASE_PATH) === 0) {
            $uri = substr($uri, strlen(BASE_PATH));
        }

        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->call($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        View::render('errors/404');
    }

    private function call(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            call_user_func_array([$controller, $method], $params);
            return;
        }
        call_user_func_array($handler, $params);
    }
}
