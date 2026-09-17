<?php
declare(strict_types=1);

namespace App\Core;

class App
{
    private static bool $booted = false;

    public static function boot(string $basePath): void
    {
        if (self::$booted) {
            return;
        }

        // 1. Cargar archivo de variables de entorno (.env) si existe
        self::loadEnv($basePath . '/.env');

        // 2. Configuración de Zona Horaria Institucional
        $timezone = getenv('APP_TIMEZONE') ?: 'America/Lima';
        date_default_timezone_set($timezone);

        // 3. Manejo de Errores según APP_DEBUG
        $debug = filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN);
        if ($debug) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', '0');
            error_reporting(0);
        }

        // 4. Registrar Autoloader PSR-4 para el namespace App\
        spl_autoload_register(function ($class) use ($basePath) {
            $prefix = 'App\\';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }
            $relativeClass = substr($class, $len);
            $file = $basePath . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        });

        // 5. Iniciar Sesión Segura
        Session::start();

        self::$booted = true;
    }

    private static function loadEnv(string $envFile): void
    {
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Quitar comillas si las tuviese
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                    putenv("{$key}={$value}");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }

    public static function run(string $basePath): void
    {
        self::boot($basePath);

        $request = new Request();
        $response = new Response();
        $router = new Router($request, $response);

        // Cargar rutas declaradas
        $routesFile = $basePath . '/config/routes.php';
        if (file_exists($routesFile)) {
            require $routesFile;
        }

        $router->dispatch();
    }
}
