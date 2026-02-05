<?php
namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $file = dirname(__DIR__) . '/views/' . str_replace('.', '/', $view) . '.php';
        if (file_exists($file)) {
            require $file;
        } else {
            http_response_code(500);
            echo "View not found: {$view}";
        }
    }

    protected function redirect(string $url, int $code = 302): void
    {
        header('Location: ' . $url, true, $code);
        exit;
    }

    protected function json($data, int $code = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function baseUrl(string $path = ''): string
    {
        $config = require (defined('APP_PATH') ? APP_PATH : dirname(__DIR__)) . '/config/app.php';
        $url = $config['url'];
        if ($path !== '') {
            $url .= '/' . ltrim($path, '/');
        }
        return $url;
    }
}
