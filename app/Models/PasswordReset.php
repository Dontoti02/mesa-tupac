<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class PasswordReset extends Model
{
    protected string $table = 'password_resets';

    /**
     * Genera un nuevo token de recuperación de contraseña con vigencia de 1 hora.
     * Invalida automáticamente cualquier token previo no utilizado para el mismo correo.
     */
    public function createToken(string $email): string
    {
        // Invalidar tokens previos del mismo usuario
        $stmtInvalidate = $this->db()->prepare(
            "UPDATE `password_resets` SET `utilizado` = 1 WHERE `email` = :email AND `utilizado` = 0"
        );
        $stmtInvalidate->execute(['email' => $email]);

        // Generar nuevo token criptográficamente seguro
        $token = bin2hex(random_bytes(32));
        $expiraAt = date('Y-m-d H:i:s', time() + 3600); // 1 hora de validez

        $stmtInsert = $this->db()->prepare(
            "INSERT INTO `password_resets` (`email`, `token`, `expira_at`, `utilizado`) 
             VALUES (:email, :token, :expira_at, 0)"
        );
        $stmtInsert->execute([
            'email' => $email,
            'token' => $token,
            'expira_at' => $expiraAt
        ]);

        return $token;
    }

    /**
     * Busca un token válido (no utilizado y no expirado).
     */
    public function findValidToken(string $token): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT * FROM `password_resets` 
             WHERE `token` = :token AND `utilizado` = 0 AND `expira_at` > NOW() 
             LIMIT 1"
        );
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Marca un token como utilizado una vez restablecida la contraseña.
     */
    public function markUsed(string $token): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE `password_resets` SET `utilizado` = 1 WHERE `token` = :token"
        );
        return $stmt->execute(['token' => $token]);
    }
}
