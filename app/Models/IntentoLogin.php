<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class IntentoLogin extends Model
{
    protected string $table = 'intentos_login';

    public function record(string $ip, string $username, bool $success): void
    {
        $this->insert([
            'ip' => $ip,
            'username' => $username,
            'exito' => $success ? 1 : 0
        ]);
    }

    public function isBlocked(string $ip, string $username, int $maxAttempts = 5, int $decayMinutes = 15): bool
    {
        $stmt = $this->db()->prepare(
            "SELECT COUNT(*) as failed_attempts 
             FROM `intentos_login` 
             WHERE (`ip` = :ip OR `username` = :user) 
               AND `exito` = 0 
               AND `fecha_hora` >= (NOW() - INTERVAL :minutes MINUTE)"
        );
        $stmt->bindValue(':ip', $ip);
        $stmt->bindValue(':user', $username);
        $stmt->bindValue(':minutes', $decayMinutes, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        return ((int)($row['failed_attempts'] ?? 0)) >= $maxAttempts;
    }

    public function clear(string $ip, string $username): void
    {
        $stmt = $this->db()->prepare(
            "DELETE FROM `intentos_login` WHERE `ip` = :ip OR `username` = :user"
        );
        $stmt->execute(['ip' => $ip, 'user' => $username]);
    }
}
