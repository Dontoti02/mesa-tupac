<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];
    private Request $request;
    private Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, array|callable $handler, array $middlewares = []): self
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
        return $this;
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): self
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
        return $this;
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares): void
    {
        $normalizedPath = '/' . trim($path, '/');
        if ($normalizedPath !== '/' && str_ends_with($normalizedPath, '/')) {
            $normalizedPath = rtrim($normalizedPath, '/');
        }

        // Convertir {param} en expresiones regulares nombradas
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $normalizedPath);
        $regex = '#^' . $pattern . '$#u';

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $normalizedPath,
            'regex' => $regex,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(): void
    {
        $requestMethod = $this->request->getMethod();
        $requestUri = $this->request->getUri();

        // Normalizar URI
        $normalizedUri = '/' . trim($requestUri, '/');
        if ($normalizedUri !== '/' && str_ends_with($normalizedUri, '/')) {
            $normalizedUri = rtrim($normalizedUri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if (preg_match($route['regex'], $normalizedUri, $matches)) {
                // Extraer parámetros con nombre
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                // Ejecutar Middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $this->executeMiddleware($middleware);
                }

                // Ejecutar el controlador / handler
                $this->executeHandler($route['handler'], $params);
                return;
            }
        }

        // 404 No Encontrado
        $this->response->setStatusCode(404);
        try {
            $this->response->view('errors.404', ['title' => 'Página no encontrada'], 'public');
        } catch (\Throwable $e) {
            echo "<h1>404 - Página no encontrada</h1>";
            echo "<p>La ruta solicitada '{$normalizedUri}' no existe en este servidor.</p>";
            echo "<p><a href='" . ViewHelper::url('/') . "'>Volver al inicio</a></p>";
        }
        exit;
    }

    private function executeMiddleware(string|callable $middleware): void
    {
        if (is_callable($middleware)) {
            $middleware($this->request, $this->response);
            return;
        }

        if (is_string($middleware)) {
            $parts = explode(':', $middleware, 2);
            $class = $parts[0];
            $args = isset($parts[1]) ? explode(',', $parts[1]) : [];

            // Si es nombre corto, buscar en namespace App\Middleware
            if (!class_exists($class)) {
                $fullClass = 'App\\Middleware\\' . $class;
                if (class_exists($fullClass)) {
                    $class = $fullClass;
                }
            }

            if (class_exists($class)) {
                $instance = new $class();
                if (method_exists($instance, 'handle')) {
                    $instance->handle($this->request, $this->response, ...$args);
                    return;
                }
            }
            throw new \Exception("El middleware '{$middleware}' no es válido o no implementa el método handle()");
        }
    }

    private function executeHandler(array|callable $handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func($handler, $this->request, $this->response, ...$params);
            return;
        }

        if (is_array($handler)) {
            [$controllerClass, $method] = $handler;

            if (!class_exists($controllerClass)) {
                throw new \Exception("El controlador '{$controllerClass}' no fue encontrado.");
            }

            $controller = new $controllerClass($this->request, $this->response);

            if (!method_exists($controller, $method)) {
                throw new \Exception("El método '{$method}' no existe en el controlador '{$controllerClass}'.");
            }

            call_user_func_array([$controller, $method], array_merge([$this->request, $this->response], $params));
            return;
        }

        throw new \Exception("Formato de handler de ruta no soportado.");
    }
}
