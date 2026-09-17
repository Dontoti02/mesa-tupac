<?php
declare(strict_types=1);

namespace App\Helpers;

use DateTime;
use DateTimeZone;

class ViewHelper
{
    private static ?string $baseUrl = null;

    public static function url(string $path = ''): string
    {
        if (self::$baseUrl === null) {
            $config = require __DIR__ . '/../../config/app.php';
            self::$baseUrl = rtrim($config['url'], '/');
        }

        if ($path === '' || $path === '/') {
            return self::$baseUrl;
        }

        return self::$baseUrl . '/' . ltrim($path, '/');
    }

    public static function asset(string $path = ''): string
    {
        return self::url('assets/' . ltrim($path, '/'));
    }

    public static function escape(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public static function formatDate(?string $datetime, string $format = 'd/m/Y'): string
    {
        if (empty($datetime)) {
            return '-';
        }
        try {
            $dt = new DateTime($datetime, new DateTimeZone('America/Lima'));
            return $dt->format($format);
        } catch (\Exception $e) {
            return $datetime;
        }
    }

    public static function formatDateTime(?string $datetime, string $format = 'd/m/Y H:i'): string
    {
        return self::formatDate($datetime, $format);
    }

    public static function badgeEstado(string $nombre, string $color, string $icono = ''): string
    {
        $nombreEscaped = self::escape($nombre);
        $colorEscaped = self::escape($color);
        $iconHtml = !empty($icono) ? '<i class="bi ' . self::escape($icono) . ' me-1"></i>' : '';

        return sprintf(
            '<span class="badge" style="background-color: %s; color: #ffffff; font-weight: 500; padding: 0.4em 0.65em;">%s%s</span>',
            $colorEscaped,
            $iconHtml,
            $nombreEscaped
        );
    }

    public static function badgePrioridad(string $nombre, string $color): string
    {
        $nombreEscaped = self::escape($nombre);
        $colorEscaped = self::escape($color);

        return sprintf(
            '<span class="badge" style="border: 1px solid %s; color: %s; background-color: transparent; font-weight: 600;">%s</span>',
            $colorEscaped,
            $colorEscaped,
            $nombreEscaped
        );
    }

    public static function truncate(string $text, int $limit = 50, string $ellipsis = '...'): string
    {
        if (mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }
        return mb_substr($text, 0, $limit, 'UTF-8') . $ellipsis;
    }

    public static function isActive(string $route): string
    {
        $currentUri = '/' . trim($_SERVER['REQUEST_URI'] ?? '', '/');
        $normalizedRoute = '/' . trim($route, '/');

        if ($normalizedRoute === '/' && ($currentUri === '/' || $currentUri === '')) {
            return 'active';
        }

        if ($normalizedRoute !== '/' && str_contains($currentUri, $normalizedRoute)) {
            return 'active';
        }

        return '';
    }
}
