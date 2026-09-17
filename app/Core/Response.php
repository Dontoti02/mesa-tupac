<?php
declare(strict_types=1);

namespace App\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function sendHeaders(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }
    }

    public function json(mixed $data, int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        $this->setHeader('Content-Type', 'application/json; charset=UTF-8');
        $this->sendHeaders();
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function redirect(string $path, array $flash = []): void
    {
        foreach ($flash as $key => $message) {
            Session::flash($key, $message);
        }

        // Si la ruta no comienza con http:// ni https://, concatenar la base institucional
        if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
            $config = require __DIR__ . '/../../config/app.php';
            $baseUrl = rtrim($config['url'], '/');
            $path = $baseUrl . '/' . ltrim($path, '/');
        }

        $this->statusCode = 302;
        $this->setHeader('Location', $path);
        $this->sendHeaders();
        exit;
    }

    public function view(string $viewPath, array $data = [], string|bool|null $layout = 'app'): void
    {
        $layout = ($layout === false ? null : $layout);
        $this->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $this->sendHeaders();

        // Extraer variables a ámbito local
        extract($data);

        // Capturar contenido de la vista específica
        $viewFile = __DIR__ . '/../Views/' . str_replace('.', '/', $viewPath) . '.php';
        if (!file_exists($viewFile)) {
            throw new \Exception("La vista '{$viewPath}' no existe en {$viewFile}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Si se especificó un layout, renderizar el layout envolviendo el contenido
        if ($layout !== null) {
            $layoutFile = __DIR__ . '/../Views/layouts/' . $layout . '.php';
            if (!file_exists($layoutFile)) {
                throw new \Exception("El layout '{$layout}' no existe en {$layoutFile}");
            }
            require $layoutFile;
        } else {
            echo $content;
        }
        exit;
    }

    public function download(string $filePath, string $downloadName, string $mime = 'application/octet-stream'): void
    {
        if (!file_exists($filePath)) {
            $this->setStatusCode(404);
            $this->sendHeaders();
            echo "Archivo no encontrado.";
            exit;
        }

        $this->setHeader('Content-Description', 'File Transfer');
        $this->setHeader('Content-Type', $mime);
        $this->setHeader('Content-Disposition', 'attachment; filename="' . addslashes($downloadName) . '"');
        $this->setHeader('Expires', '0');
        $this->setHeader('Cache-Control', 'must-revalidate');
        $this->setHeader('Pragma', 'public');
        $this->setHeader('Content-Length', (string)filesize($filePath));
        $this->sendHeaders();

        readfile($filePath);
        exit;
    }
}
