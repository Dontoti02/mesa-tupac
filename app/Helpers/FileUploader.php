<?php
declare(strict_types=1);

namespace App\Helpers;

use Exception;

class FileUploader
{
    private const MAX_SIZE_BYTES = 26214400; // 25 MB

    private const ALLOWED_MIMES = [
        'pdf'  => ['application/pdf'],
        'doc'  => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls'  => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png']
    ];

    public static function upload(array $file, string $subfolder = 'documents'): array
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception('Parámetros de archivo no válidos.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No se envió ningún archivo.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('El archivo excede el tamaño máximo permitido por el servidor.');
            default:
                throw new Exception('Error desconocido al subir el archivo.');
        }

        if ($file['size'] > self::MAX_SIZE_BYTES) {
            throw new Exception('El archivo excede el tamaño límite permitido de 25MB.');
        }

        // Obtener extensión original
        $originalName = basename($file['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!array_key_exists($extension, self::ALLOWED_MIMES)) {
            throw new Exception("El tipo de archivo .{$extension} no está permitido. Formatos admitidos: PDF, Word, Excel, JPG, PNG.");
        }

        // Validar tipo MIME real mediante finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($realMime, self::ALLOWED_MIMES[$extension], true)) {
            // Caso especial para DOCX/XLSX en algunos entornos que reportan zip genérico
            if (in_array($extension, ['docx', 'xlsx']) && $realMime === 'application/zip') {
                // permitido
            } else {
                throw new Exception("El contenido del archivo no coincide con su extensión declarada (MIME: {$realMime}).");
            }
        }

        // Calcular Hash SHA-256 para integridad documental
        $sha256 = hash_file('sha256', $file['tmp_name']);

        // Directorio destino estructurado por Año/Mes fuera del webroot
        $yearMonth = date('Y/m');
        $targetDir = dirname(__DIR__, 2) . '/storage/' . $subfolder . '/' . $yearMonth;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Generar nombre de archivo único no predecible
        $safeFileName = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = $targetDir . '/' . $safeFileName;
        $relativePath = 'storage/' . $subfolder . '/' . $yearMonth . '/' . $safeFileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Fallo al mover el archivo al almacenamiento seguro.');
        }

        return [
            'original_name' => $originalName,
            'file_name' => $safeFileName,
            'relative_path' => $relativePath,
            'absolute_path' => $targetPath,
            'mime_type' => $realMime,
            'size_bytes' => $file['size'],
            'hash_sha256' => $sha256
        ];
    }
}
