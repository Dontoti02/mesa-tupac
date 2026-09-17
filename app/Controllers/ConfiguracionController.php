<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Configuracion;
use App\Models\Auditoria;
use App\Helpers\Validator;
use App\Helpers\Mailer;

class ConfiguracionController extends Controller
{
    public function configuracion(): void
    {
        $config = Configuracion::getAll();

        $this->view('administracion.configuracion', [
            'title' => 'Configuración Institucional General',
            'config' => $config
        ], 'app');
    }

    public function updateConfiguracion(): void
    {
        $data = $this->request->all();
        $fields = [
            'institucion_nombre',
            'institucion_nombre_corto',
            'institucion_dependencia',
            'institucion_resolucion',
            'institucion_ruc',
            'institucion_direccion',
            'institucion_telefono',
            'institucion_correo',
            'institucion_web',
            'institucion_ciudad',
            'institucion_region',
            'institucion_anio',
            'institucion_pie_pagina'
        ];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                Configuracion::set($field, trim((string)$data[$field]));
            }
        }

        // Procesar subida de imágenes (logos, favicon)
        $uploadDir = __DIR__ . '/../../public/assets/img';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $files = $this->request->file('logo_principal') ? ['logo_principal' => $_FILES['logo_principal'] ?? null] : [];
        if (!empty($_FILES['logo_login']['name'])) $files['logo_login'] = $_FILES['logo_login'];
        if (!empty($_FILES['logo_documentos']['name'])) $files['logo_documentos'] = $_FILES['logo_documentos'];
        if (!empty($_FILES['favicon']['name'])) $files['favicon'] = $_FILES['favicon'];

        foreach ($files as $key => $file) {
            if (!empty($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['png', 'jpg', 'jpeg', 'svg', 'ico', 'webp'], true)) {
                    $fileName = $key . '_' . time() . '.' . $ext;
                    $targetPath = $uploadDir . '/' . $fileName;
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        Configuracion::set($key, 'assets/img/' . $fileName);
                    }
                }
            }
        }

        Auditoria::log((int)$this->userId(), 'ACTUALIZAR_CONFIGURACION_GENERAL', 'configuraciones');
        $this->redirect('/administracion/configuracion', ['success' => 'Configuración institucional actualizada correctamente.']);
    }

    public function apariencia(): void
    {
        $config = Configuracion::getAll();

        $this->view('administracion.apariencia', [
            'title' => 'Personalización de Apariencia y Colores Institucionales',
            'config' => $config
        ], 'app');
    }

    public function updateApariencia(): void
    {
        $data = $this->request->all();
        $colors = [
            'color_primario' => $data['color_primario'] ?? '#B3261E',
            'color_primario_oscuro' => $data['color_primario_oscuro'] ?? '#7F1D1D',
            'color_primario_suave' => $data['color_primario_suave'] ?? '#FEE2E2',
            'color_secundario' => $data['color_secundario'] ?? '#F97316',
            'color_secundario_oscuro' => $data['color_secundario_oscuro'] ?? '#C2410C',
            'color_secundario_suave' => $data['color_secundario_suave'] ?? '#FFEDD5',
            'color_sidebar' => $data['color_sidebar'] ?? '#374151',
            'color_sidebar_texto' => $data['color_sidebar_texto'] ?? '#F3F4F6',
            'color_header' => $data['color_header'] ?? '#FFFFFF',
            'color_fondo' => $data['color_fondo'] ?? '#F3F4F6',
            'color_texto_principal' => $data['color_texto_principal'] ?? '#374151',
            'color_texto_secundario' => $data['color_texto_secundario'] ?? '#6B7280',
            'color_borde' => $data['color_borde'] ?? '#D1D5DB'
        ];

        foreach ($colors as $key => $val) {
            Configuracion::set($key, trim((string)$val));
        }

        // Sincronizar archivo físico variables.css
        $cssContent = ":root {\n";
        $cssContent .= "  --color-primary: {$colors['color_primario']};\n";
        $cssContent .= "  --color-primary-hover: {$colors['color_primario_oscuro']};\n";
        $cssContent .= "  --color-primary-light: {$colors['color_primario_suave']};\n";
        $cssContent .= "  --color-secondary: {$colors['color_secundario']};\n";
        $cssContent .= "  --color-secondary-hover: {$colors['color_secundario_oscuro']};\n";
        $cssContent .= "  --color-secondary-light: {$colors['color_secundario_suave']};\n";
        $cssContent .= "  --color-sidebar: {$colors['color_sidebar']};\n";
        $cssContent .= "  --color-sidebar-text: {$colors['color_sidebar_texto']};\n";
        $cssContent .= "  --color-header: {$colors['color_header']};\n";
        $cssContent .= "  --color-bg: {$colors['color_fondo']};\n";
        $cssContent .= "  --color-dark: {$colors['color_texto_principal']};\n";
        $cssContent .= "  --color-muted: {$colors['color_texto_secundario']};\n";
        $cssContent .= "  --color-border: {$colors['color_borde']};\n";
        $cssContent .= "}\n";

        @file_put_contents(__DIR__ . '/../../public/assets/css/variables.css', $cssContent);

        Auditoria::log((int)$this->userId(), 'ACTUALIZAR_APARIENCIA_COLORES', 'configuraciones');
        $this->redirect('/administracion/apariencia', ['success' => 'Paleta de colores y apariencia institucional actualizada con éxito.']);
    }

    public function smtp(): void
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM `configuracion_smtp` ORDER BY `id` ASC LIMIT 1");
        $smtp = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->view('administracion.smtp', [
            'title' => 'Configuración de Servidor de Correo SMTP',
            'smtp' => $smtp ?: []
        ], 'app');
    }

    public function updateSmtp(): void
    {
        $data = $this->request->all();
        $db = Database::getConnection();

        $host = trim((string)$data['host']);
        $puerto = (int)$data['puerto'];
        $seguridad = trim((string)$data['seguridad']);
        $usuario = trim((string)$data['usuario']);
        $remitenteEmail = trim((string)$data['remitente_email']);
        $remitenteNombre = trim((string)$data['remitente_nombre']);
        $activo = isset($data['activo']) ? 1 : 0;

        // Comprobar si existe registro
        $stmt = $db->query("SELECT `id`, `password_encriptado` FROM `configuracion_smtp` LIMIT 1");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $password = !empty($data['password']) ? trim((string)$data['password']) : ($row['password_encriptado'] ?? '');

        if ($row) {
            $stmtUp = $db->prepare(
                "UPDATE `configuracion_smtp` SET 
                    `host` = :host, 
                    `puerto` = :puerto, 
                    `usuario` = :usuario, 
                    `password_encriptado` = :pass, 
                    `seguridad` = :seg, 
                    `remitente_email` = :remail, 
                    `remitente_nombre` = :rname, 
                    `activo` = :act, 
                    `updated_at` = NOW() 
                 WHERE `id` = :id"
            );
            $stmtUp->execute([
                'host' => $host,
                'puerto' => $puerto,
                'usuario' => $usuario,
                'pass' => $password,
                'seg' => $seguridad,
                'remail' => $remitenteEmail,
                'rname' => $remitenteNombre,
                'act' => $activo,
                'id' => $row['id']
            ]);
        } else {
            $stmtIns = $db->prepare(
                "INSERT INTO `configuracion_smtp` 
                    (`host`, `puerto`, `usuario`, `password_encriptado`, `seguridad`, `remitente_email`, `remitente_nombre`, `activo`) 
                 VALUES (:host, :puerto, :usuario, :pass, :seg, :remail, :rname, :act)"
            );
            $stmtIns->execute([
                'host' => $host,
                'puerto' => $puerto,
                'usuario' => $usuario,
                'pass' => $password,
                'seg' => $seguridad,
                'remail' => $remitenteEmail,
                'rname' => $remitenteNombre,
                'act' => $activo
            ]);
        }

        Auditoria::log((int)$this->userId(), 'ACTUALIZAR_CONFIGURACION_SMTP', 'configuracion_smtp');
        $this->redirect('/administracion/smtp', ['success' => 'Parámetros del servidor SMTP actualizados.']);
    }

    public function probarSmtp(): void
    {
        $testEmail = trim((string)$this->request->post('email_prueba'));
        if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/administracion/smtp', ['error' => 'Por favor ingrese una dirección de correo válida para la prueba.']);
        }

        $res = Mailer::testConnection($testEmail);
        if ($res['success']) {
            $this->redirect('/administracion/smtp', ['success' => $res['message']]);
        } else {
            $this->redirect('/administracion/smtp', ['error' => $res['message']]);
        }
    }
}
