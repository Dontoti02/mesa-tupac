<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Configuracion extends Model
{
    protected string $table = 'configuraciones';
    protected string $primaryKey = 'clave';

    private static ?array $cachedConfig = null;

    public static function getAll(): array
    {
        if (self::$cachedConfig !== null) {
            return self::$cachedConfig;
        }

        $instance = new self();
        $stmt = $instance->db()->query("SELECT `clave`, `valor` FROM `configuraciones`");
        $rows = $stmt->fetchAll();

        $config = [];
        foreach ($rows as $row) {
            $config[$row['clave']] = $row['valor'];
        }

        self::$cachedConfig = $config;
        return $config;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = self::getAll();
        return $all[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        $instance = new self();
        $stmt = $instance->db()->prepare(
            "INSERT INTO `configuraciones` (`clave`, `valor`) VALUES (:clave, :valor) 
             ON DUPLICATE KEY UPDATE `valor` = :valor_up, `updated_at` = NOW()"
        );
        $stmt->execute([
            'clave' => $key,
            'valor' => $value,
            'valor_up' => $value
        ]);

        self::$cachedConfig = null; // Invalidate cache
    }

    public static function setMultiple(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            self::set((string)$key, $value !== null ? (string)$value : null);
        }
    }
}
