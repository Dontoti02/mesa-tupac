<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Core\Session;

class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function getToken(): string
    {
        Session::start();
        $token = Session::get(self::SESSION_KEY);
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }
        return $token;
    }

    public static function validate(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        $stored = Session::get(self::SESSION_KEY);
        if (!$stored) {
            return false;
        }
        return hash_equals($stored, $token);
    }

    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
