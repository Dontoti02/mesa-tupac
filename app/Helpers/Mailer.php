<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Core\Database;
use PDO;

class Mailer
{
    private static ?array $smtpConfig = null;

    public static function getConfig(): array
    {
        if (self::$smtpConfig !== null) {
            return self::$smtpConfig;
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM `configuracion_smtp` ORDER BY `id` ASC LIMIT 1");
            $config = $stmt->fetch(PDO::FETCH_ASSOC);
            self::$smtpConfig = $config ?: [
                'host' => 'smtp.gmail.com',
                'puerto' => 587,
                'usuario' => '',
                'password_encriptado' => '',
                'seguridad' => 'tls',
                'remitente_email' => 'mesadepartes@tupacamaru.edu.pe',
                'remitente_nombre' => 'Mesa de Partes Virtual',
                'activo' => 0
            ];
        } catch (\Throwable $e) {
            self::$smtpConfig = [
                'host' => 'localhost',
                'puerto' => 25,
                'usuario' => '',
                'password_encriptado' => '',
                'seguridad' => 'ninguna',
                'remitente_email' => 'no-reply@tupacamaru.edu.pe',
                'remitente_nombre' => 'Mesa de Partes',
                'activo' => 0
            ];
        }

        return self::$smtpConfig;
    }

    public static function send(string $to, string $subject, string $htmlContent): bool
    {
        $cfg = self::getConfig();

        if (empty($cfg['activo'])) {
            // Si SMTP está inactivo, registramos en log simulando entrega exitosa (entorno local de desarrollo)
            self::logMail("SMTP Inactivo. Simulación de envío a: {$to} | Asunto: {$subject}");
            return true;
        }

        try {
            return self::sendSmtpSocket(
                (string)$cfg['host'],
                (int)$cfg['puerto'],
                (string)$cfg['seguridad'],
                (string)$cfg['usuario'],
                (string)$cfg['password_encriptado'],
                (string)$cfg['remitente_email'],
                (string)$cfg['remitente_nombre'],
                $to,
                $subject,
                $htmlContent
            );
        } catch (\Throwable $e) {
            self::logMail("Error al enviar correo SMTP a {$to}: " . $e->getMessage());
            // Fallback to PHP native mail() if available
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: {$cfg['remitente_nombre']} <{$cfg['remitente_email']}>\r\n";
            @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $htmlContent, $headers);
            return false;
        }
    }

    public static function testConnection(string $testEmail): array
    {
        $cfg = self::getConfig();
        $subject = "Prueba de Configuración SMTP - IESP Túpac Amaru";
        $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #D1D5DB; border-radius: 8px; overflow: hidden;'>
                <div style='background: #B3261E; color: #FFFFFF; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>Mesa de Partes Virtual</h2>
                    <p style='margin: 5px 0 0 0;'>IESP Túpac Amaru – Cusco</p>
                </div>
                <div style='padding: 24px; background: #FFFFFF;'>
                    <h3 style='color: #1F2937;'>Prueba de Conexión Exitosa</h3>
                    <p style='color: #4B5563;'>Este mensaje confirma que el servidor de correo SMTP institucional se encuentra correctamente configurado y operativo.</p>
                    <p style='color: #6B7280; font-size: 13px;'>Fecha y hora: " . date('d/m/Y H:i:s') . "</p>
                </div>
                <div style='background: #F3F4F6; padding: 12px; text-align: center; color: #6B7280; font-size: 12px;'>
                    Sistema de Trámite Documentario Digital &copy; " . date('Y') . "
                </div>
            </div>
        ";

        try {
            $ok = self::sendSmtpSocket(
                (string)$cfg['host'],
                (int)$cfg['puerto'],
                (string)$cfg['seguridad'],
                (string)$cfg['usuario'],
                (string)$cfg['password_encriptado'],
                (string)$cfg['remitente_email'],
                (string)$cfg['remitente_nombre'],
                $testEmail,
                $subject,
                $body
            );

            return [
                'success' => $ok,
                'message' => $ok ? "Mensaje de prueba enviado exitosamente a {$testEmail}." : "No se pudo completar el envío del correo."
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => "Fallo de conexión SMTP: " . $e->getMessage()
            ];
        }
    }

    private static function sendSmtpSocket(
        string $host,
        int $port,
        string $security,
        string $user,
        string $pass,
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $subject,
        string $htmlBody
    ): bool {
        $protocol = '';
        if (strtolower($security) === 'ssl') {
            $protocol = 'ssl://';
        }

        $timeout = 10;
        $socket = @fsockopen($protocol . $host, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            throw new \Exception("No se pudo conectar a {$host}:{$port} ({$errno}: {$errstr})");
        }

        stream_set_timeout($socket, $timeout);
        self::getResponse($socket);

        self::sendCommand($socket, "EHLO " . gethostname());

        if (strtolower($security) === 'tls') {
            self::sendCommand($socket, "STARTTLS");
            $crypto = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if (!$crypto) {
                fclose($socket);
                throw new \Exception("Fallo al inicializar cifrado TLS");
            }
            self::sendCommand($socket, "EHLO " . gethostname());
        }

        if (!empty($user) && !empty($pass)) {
            self::sendCommand($socket, "AUTH LOGIN");
            self::sendCommand($socket, base64_encode($user));
            self::sendCommand($socket, base64_encode($pass));
        }

        self::sendCommand($socket, "MAIL FROM:<{$fromEmail}>");
        self::sendCommand($socket, "RCPT TO:<{$toEmail}>");
        self::sendCommand($socket, "DATA");

        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $encodedFromName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';

        $data = "Date: " . date('r') . "\r\n";
        $data .= "To: <{$toEmail}>\r\n";
        $data .= "From: {$encodedFromName} <{$fromEmail}>\r\n";
        $data .= "Subject: {$encodedSubject}\r\n";
        $data .= "MIME-Version: 1.0\r\n";
        $data .= "Content-Type: text/html; charset=UTF-8\r\n";
        $data .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $data .= chunk_split(base64_encode($htmlBody)) . "\r\n.";

        self::sendCommand($socket, $data);
        self::sendCommand($socket, "QUIT");
        fclose($socket);

        return true;
    }

    private static function sendCommand($socket, string $cmd): string
    {
        fwrite($socket, $cmd . "\r\n");
        return self::getResponse($socket);
    }

    private static function getResponse($socket): string
    {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        $code = (int)substr($response, 0, 3);
        if ($code >= 400) {
            throw new \Exception("Error SMTP [{$code}]: " . trim($response));
        }
        return $response;
    }

    private static function logMail(string $msg): void
    {
        $dir = __DIR__ . '/../../storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $logFile = $dir . '/mail.log';
        $entry = "[" . date('Y-m-d H:i:s') . "] " . $msg . PHP_EOL;
        @file_put_contents($logFile, $entry, FILE_APPEND);
    }
}
