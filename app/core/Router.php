<?php
namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, string $handler, array $middlewares = []): self
    {
        $this->add('GET', $path, $handler, $middlewares);
        return $this;
    }

    public function post(string $path, string $handler, array $middlewares = []): self
    {
        $this->add('POST', $path, $handler, $middlewares);
        return $this;
    }

    private function add(string $method, string $path, string $handler, array $middlewares): void
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'handler'    => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function middleware(string $name): self
    {
        $this->middlewares[] = $name;
        return $this;
    }

    public function group(callable $callback): void
    {
        $router = new self();
        $callback($router);
        foreach ($router->routes as $route) {
            $route['middlewares'] = array_merge($this->middlewares, $route['middlewares']);
            $this->routes[] = $route;
        }
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = $this->getBasePath();
        if ($base !== '' && strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base)) ?: '/';
        }
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $pattern = $this->pathToRegex($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                foreach ($route['middlewares'] as $mw) {
                    $mwClass = "App\\Middlewares\\" . $mw;
                    if (class_exists($mwClass)) {
                        $m = new $mwClass();
                        if (!$m->handle()) {
                            return;
                        }
                    }
                }
                [$class, $methodName] = explode('@', $route['handler']);
                $controller = "App\\Controllers\\" . $class;
                if (class_exists($controller)) {
                    $obj = new $controller();
                    if (method_exists($obj, $methodName)) {
                        call_user_func_array([$obj, $methodName], array_values($matches));
                        return;
                    }
                }
            }
        }

        http_response_code(404);
        $config = require dirname(__DIR__) . '/config/app.php';
        if ($config['debug']) {
            echo "404 Not Found: {$uri}";
        } else {
            header('Location: ' . $this->baseUrl());
            exit;
        }
    }

    private function pathToRegex(string $path): string
    {
        $path = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $path . '$#';
    }

    private function getBasePath(): string
    {
        $script = $_SERVER['SCRIPT_NAME'];
        $dir = dirname($script);
        if ($dir === '/' || $dir === '\\') {
            return '';
        }
        return $dir;
    }

    private function baseUrl(): string
    {
        $config = require dirname(__DIR__) . '/config/app.php';
        return $config['url'];
    }
}
