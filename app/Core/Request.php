<?php
declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $get;
    private array $post;
    private array $files;
    private array $server;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;

        // Determinar método HTTP (con soporte para _method override)
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($this->post['_method'])) {
            $override = strtoupper($this->post['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE', 'GET', 'POST'], true)) {
                $method = $override;
            }
        }
        $this->method = $method;

        // Limpiar y normalizar la URI relativa a la aplicación
        $this->uri = $this->parseUri();
    }

    private function parseUri(): string
    {
        $rawUri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($rawUri, PHP_URL_PATH) ?? '/';

        // Detectar base path si la aplicación se aloja en un subdirectorio (ej. /mesa-tupac o /mesa-tupac/public)
        $scriptName = $this->server['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName); // ej. /mesa-tupac/public o /mesa-tupac

        // Normalizar barras invertidas de Windows a barras diagonales
        $baseDir = str_replace('\\', '/', $baseDir);

        if ($baseDir !== '/' && $baseDir !== '' && str_starts_with($path, $baseDir)) {
            $path = substr($path, strlen($baseDir));
        }

        // Si la ruta aún tiene /public al inicio, limpiarla
        if (str_starts_with($path, '/public')) {
            $path = substr($path, 7);
        }

        $path = '/' . trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isAjax(): bool
    {
        return (!empty($this->server['HTTP_X_REQUESTED_WITH']) && 
                strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (str_contains($this->server['HTTP_ACCEPT'] ?? '', 'application/json'));
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function ip(): string
    {
        if (!empty($this->server['HTTP_CLIENT_IP'])) {
            return $this->server['HTTP_CLIENT_IP'];
        }
        if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $this->server['HTTP_X_FORWARDED_FOR'])[0];
        }
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string
    {
        return substr($this->server['HTTP_USER_AGENT'] ?? 'Desconocido', 0, 255);
    }
}
