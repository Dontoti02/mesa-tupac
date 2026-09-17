<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\Configuracion;

class CargoGenerator
{
    public static function generateHtml(array $expediente, array $documentos = []): string
    {
        $config = Configuracion::getAll();
        $instNombre = $config['institucion_nombre'] ?? 'INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO';
        $instDependencia = $config['institucion_dependencia'] ?? 'GERENCIA REGIONAL DE EDUCACIÓN CUSCO';
        $instResolucion = $config['institucion_resolucion'] ?? 'R.M 195-2005-ED';
        $instRuc = $config['institucion_ruc'] ?? '20165849301';
        $instDireccion = $config['institucion_direccion'] ?? 'Av. Cusco N° 456, San Sebastián, Cusco';

        $trackingUrl = ViewHelper::url('/consulta?expediente=' . urlencode($expediente['numero_expediente']) . '&codigo=' . urlencode($expediente['codigo_seguimiento']));
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=' . urlencode($trackingUrl);

        $solicitanteNombre = $expediente['nombres'] . ' ' . $expediente['apellido_paterno'] . ' ' . $expediente['apellido_materno'];

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Cargo de Recepción - <?= ViewHelper::escape($expediente['numero_expediente']) ?></title>
            <style>
                @page {
                    size: A4 portrait;
                    margin: 15mm;
                }
                body {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 11pt;
                    color: #1f2937;
                    margin: 0;
                    padding: 20px;
                    background: #ffffff;
                }
                .cargo-container {
                    border: 2px solid #374151;
                    padding: 25px;
                    border-radius: 4px;
                    max-width: 800px;
                    margin: 0 auto;
                }
                .header-table {
                    width: 100%;
                    border-bottom: 2px solid #b3261e;
                    padding-bottom: 12px;
                    margin-bottom: 20px;
                }
                .title-main {
                    font-size: 13pt;
                    font-weight: bold;
                    color: #b3261e;
                    text-transform: uppercase;
                }
                .title-sub {
                    font-size: 9pt;
                    color: #4b5563;
                }
                .cargo-title {
                    text-align: center;
                    background-color: #f3f4f6;
                    border: 1px solid #d1d5db;
                    padding: 10px;
                    margin-bottom: 20px;
                    border-radius: 4px;
                }
                .cargo-title h2 {
                    margin: 0;
                    color: #b3261e;
                    font-size: 14pt;
                    letter-spacing: 1px;
                }
                .cargo-title div {
                    font-size: 10pt;
                    color: #374151;
                    margin-top: 4px;
                }
                .data-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                .data-table th, .data-table td {
                    border: 1px solid #e5e7eb;
                    padding: 8px 12px;
                    font-size: 10pt;
                }
                .data-table th {
                    background-color: #f9fafb;
                    color: #374151;
                    text-align: left;
                    width: 28%;
                }
                .highlight-box {
                    background-color: #fef2f2;
                    border: 1px dashed #b3261e;
                    padding: 12px;
                    border-radius: 4px;
                    margin-bottom: 20px;
                }
                .footer-box {
                    margin-top: 30px;
                    border-top: 1px solid #e5e7eb;
                    padding-top: 15px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .print-button-bar {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .btn-print {
                    background-color: #b3261e;
                    color: #ffffff;
                    padding: 10px 24px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-weight: bold;
                    font-size: 11pt;
                }
                @media print {
                    .print-button-bar { display: none !important; }
                    body { padding: 0; }
                    .cargo-container { border: 1px solid #000; }
                }
            </style>
        </head>
        <body>
            <div class="print-button-bar">
                <button class="btn-print" onclick="window.print();">🖨️ Imprimir o Guardar en PDF</button>
                <a href="<?= ViewHelper::url('/') ?>" style="margin-left: 15px; color: #4b5563; text-decoration: none;">Volver al Portal</a>
            </div>

            <div class="cargo-container">
                <table class="header-table">
                    <tr>
                        <td style="width: 75%;">
                            <div class="title-sub"><?= ViewHelper::escape($instDependencia) ?></div>
                            <div class="title-main"><?= ViewHelper::escape($instNombre) ?></div>
                            <div class="title-sub">RUC: <?= ViewHelper::escape($instRuc) ?> | <?= ViewHelper::escape($instResolucion) ?></div>
                            <div class="title-sub"><?= ViewHelper::escape($instDireccion) ?></div>
                        </td>
                        <td style="width: 25%; text-align: right;">
                            <img src="<?= $qrApiUrl ?>" alt="QR Seguimiento" style="width: 100px; height: 100px; border: 1px solid #ddd; padding: 2px;">
                        </td>
                    </tr>
                </table>

                <div class="cargo-title">
                    <h2>CARGO DE RECEPCIÓN DIGITAL — FUT</h2>
                    <div>FORMULARIO ÚNICO DE TRÁMITE DOCUMENTARIO</div>
                </div>

                <div class="highlight-box">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 50%;">
                                <div style="font-size: 9pt; color: #6b7280; text-transform: uppercase;">Número de Expediente:</div>
                                <div style="font-size: 15pt; font-weight: bold; color: #b3261e;"><?= ViewHelper::escape($expediente['numero_expediente']) ?></div>
                            </td>
                            <td style="width: 50%;">
                                <div style="font-size: 9pt; color: #6b7280; text-transform: uppercase;">Código de Seguimiento Ciudadano:</div>
                                <div style="font-size: 15pt; font-weight: bold; color: #f97316; font-family: monospace;"><?= ViewHelper::escape($expediente['codigo_seguimiento']) ?></div>
                            </td>
                        </tr>
                    </table>
                </div>

                <table class="data-table">
                    <tr>
                        <th>Fecha y Hora de Ingreso:</th>
                        <td><?= ViewHelper::formatDateTime($expediente['fecha_ingreso']) ?> hrs.</td>
                    </tr>
                    <tr>
                        <th>Solicitante / Peticionante:</th>
                        <td><strong><?= ViewHelper::escape($solicitanteNombre) ?></strong></td>
                    </tr>
                    <tr>
                        <th>DNI / Identificación:</th>
                        <td><?= ViewHelper::escape($expediente['dni']) ?></td>
                    </tr>
                    <tr>
                        <th>Correo Electrónico:</th>
                        <td><?= ViewHelper::escape($expediente['correo']) ?></td>
                    </tr>
                    <tr>
                        <th>Teléfono Celular:</th>
                        <td><?= ViewHelper::escape($expediente['celular']) ?></td>
                    </tr>
                    <?php if (!empty($expediente['es_estudiante_egresado'])): ?>
                        <tr>
                            <th>Condición Académica:</th>
                            <td>Estudiante / Egresado — Programa: <?= ViewHelper::escape($expediente['programa_nombre'] ?? 'No especificado') ?> (Cód: <?= ViewHelper::escape($expediente['codigo_estudiante'] ?? '-') ?>)</td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Procedimiento / Trámite:</th>
                        <td><strong><?= ViewHelper::escape($expediente['tramite_nombre'] ?? 'Trámite General') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Solicito (Sumilla):</th>
                        <td><?= ViewHelper::escape($expediente['sumilla'] ?: $expediente['solicito']) ?></td>
                    </tr>
                    <tr>
                        <th>Documentos Adjuntos:</th>
                        <td><?= count($documentos) ?> archivo(s) registrado(s) con firma digital SHA-256</td>
                    </tr>
                    <tr>
                        <th>Estado Actual:</th>
                        <td><span style="font-weight: bold; color: #0d9488;"><?= ViewHelper::escape($expediente['estado_nombre'] ?? 'REGISTRADO') ?></span></td>
                    </tr>
                </table>

                <div style="font-size: 9pt; color: #6b7280; line-height: 1.4; background-color: #f9fafb; padding: 10px; border-radius: 4px;">
                    <strong>AVISO IMPORTANTE AL ADMINISTRADO:</strong> Conserve este documento. Para consultar el avance, estado o resolución de su expediente, ingrese al portal <strong><?= ViewHelper::url('/consulta') ?></strong> e ingrese su Código de Seguimiento. La notificación oficial se remitirá al correo registrado.
                </div>

                <div class="footer-box">
                    <div style="font-size: 8.5pt; color: #9ca3af;">
                        Generado electrónicamente por el Sistema de Mesa de Partes Virtual<br>
                        Fecha de emisión: <?= date('d/m/Y H:i:s') ?> &bull; Sello Institucional
                    </div>
                    <div style="text-align: center; border-top: 1px solid #374151; width: 220px; padding-top: 4px; font-size: 9pt;">
                        Firma Digital / Sello de Recepción<br>
                        <strong>Mesa de Partes Virtual</strong>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
