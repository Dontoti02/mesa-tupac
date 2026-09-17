<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                error_log('Database Connection Error: ' . $e->getMessage());
                throw new Exception('No se pudo conectar a la base de datos institucional: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function beginTransaction(): bool
    {
        $pdo = self::getConnection();
        if (!$pdo->inTransaction()) {
            return $pdo->beginTransaction();
        }
        return false;
    }

    public static function commit(): bool
    {
        $pdo = self::getConnection();
        if ($pdo->inTransaction()) {
            return $pdo->commit();
        }
        return false;
    }

    public static function rollBack(): bool
    {
        $pdo = self::getConnection();
        if ($pdo->inTransaction()) {
            return $pdo->rollBack();
        }
        return false;
    }

    /**
     * Ejecuta una operación dentro de una transacción automática.
     * Si ocurre cualquier excepción, realiza rollback y relanza la excepción.
     */
    public static function transaction(callable $callback)
    {
        self::beginTransaction();
        try {
            $result = $callback(self::getConnection());
            self::commit();
            return $result;
        } catch (\Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }
}
