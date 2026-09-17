<?php
use App\Helpers\ViewHelper;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ViewHelper::escape($title ?? 'Reporte Estadístico Oficial') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1f2937;
            background: #fff;
            font-size: 12px;
        }
        .header-box {
            border-bottom: 2px solid #B3261E;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .table-report th {
            background-color: #f3f4f6 !important;
            color: #111827;
            font-weight: 700;
            font-size: 11px;
            border-color: #d1d5db;
        }
        .table-report td {
            border-color: #e5e7eb;
            font-size: 11px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body class="p-4">

    <!-- Barra Superior de Control de Impresión -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light border rounded">
        <div>
            <strong>Reporte Ejecutivo Oficial Listo para Imprimir</strong>
            <div class="text-muted small">Puede imprimir en papel o guardar directamente en formato PDF desde su navegador.</div>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary fw-bold">
                <i class="bi bi-printer-fill me-1"></i> Imprimir / Guardar en PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary ms-2">Cerrar</button>
        </div>
    </div>

    <!-- Encabezado Institucional -->
    <div class="header-box">
        <div class="row align-items-center">
            <div class="col-8">
                <h5 class="fw-bold text-uppercase mb-1" style="color: #B3261E;">
                    <?= ViewHelper::escape($config['institucion_nombre'] ?? 'INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO') ?>
                </h5>
                <div class="small fw-semibold text-secondary"><?= ViewHelper::escape($config['institucion_dependencia'] ?? 'GERENCIA REGIONAL DE EDUCACIÓN CUSCO') ?></div>
                <div class="small text-muted">
                    RUC: <?= ViewHelper::escape($config['institucion_ruc'] ?? '20165849301') ?> | 
                    <?= ViewHelper::escape($config['institucion_direccion'] ?? '') ?>
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="fw-bold font-monospace text-dark">SISTEMA MESA DE PARTES</div>
                <div class="text-muted small">Fecha emisión: <?= date('d/m/Y H:i:s') ?></div>
                <div class="text-muted small">Periodo: <?= ViewHelper::escape($filters['desde']) ?> al <?= ViewHelper::escape($filters['hasta']) ?></div>
            </div>
        </div>
    </div>

    <div class="text-center my-3">
        <h4 class="fw-bold text-uppercase" style="letter-spacing: 0.5px;">INFORME ESTADÍSTICO DE GESTIÓN DOCUMENTARIA</h4>
        <p class="text-muted small mb-0">Balance de atención ciudadana y derivación interna a unidades orgánicas</p>
    </div>

    <!-- Resumen de Indicadores Clave -->
    <div class="row g-2 mb-4">
        <div class="col-2">
            <div class="border p-2 rounded text-center">
                <div class="small text-muted fw-bold">TOTAL</div>
                <div class="fs-5 fw-bold"><?= $metrics['total'] ?></div>
            </div>
        </div>
        <div class="col-2">
            <div class="border p-2 rounded text-center text-success">
                <div class="small fw-bold">ATENDIDOS</div>
                <div class="fs-5 fw-bold"><?= $metrics['atendidos'] ?></div>
            </div>
        </div>
        <div class="col-2">
            <div class="border p-2 rounded text-center text-primary">
                <div class="small fw-bold">EN TRÁMITE</div>
                <div class="fs-5 fw-bold"><?= $metrics['en_tramite'] ?></div>
            </div>
        </div>
        <div class="col-2">
            <div class="border p-2 rounded text-center text-warning">
                <div class="small fw-bold">OBSERVADOS</div>
                <div class="fs-5 fw-bold"><?= $metrics['observados'] ?></div>
            </div>
        </div>
        <div class="col-2">
            <div class="border p-2 rounded text-center text-info">
                <div class="small fw-bold">A TIEMPO</div>
                <div class="fs-5 fw-bold"><?= $metrics['dentro_plazo'] ?></div>
            </div>
        </div>
        <div class="col-2">
            <div class="border p-2 rounded text-center text-danger">
                <div class="small fw-bold">VENCIDOS</div>
                <div class="fs-5 fw-bold"><?= $metrics['vencidos'] ?></div>
            </div>
        </div>
    </div>

    <!-- Distribución por Unidad -->
    <h6 class="fw-bold mb-2 text-secondary">1. CARGA DOCUMENTARIA POR UNIDAD ORGÁNICA</h6>
    <table class="table table-bordered table-sm table-report mb-4">
        <thead>
            <tr>
                <th>Unidad Orgánica</th>
                <th class="text-center" style="width: 100px;">Expedientes</th>
                <th class="text-center" style="width: 100px;">Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($porUnidad)): ?>
                <tr><td colspan="3" class="text-center text-muted">Sin datos registrados</td></tr>
            <?php else: ?>
                <?php foreach ($porUnidad as $unidad => $c): 
                    $pct = $metrics['total'] > 0 ? round(($c / $metrics['total']) * 100, 1) : 0;
                ?>
                    <tr>
                        <td><?= ViewHelper::escape($unidad) ?></td>
                        <td class="text-center fw-bold"><?= $c ?></td>
                        <td class="text-center"><?= $pct ?>%</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Detalle de Expedientes -->
    <h6 class="fw-bold mb-2 text-secondary">2. REGISTRO DETALLADO DE EXPEDIENTES</h6>
    <table class="table table-bordered table-sm table-report mb-4">
        <thead>
            <tr>
                <th>N° Expediente</th>
                <th>Fecha</th>
                <th>Solicitante</th>
                <th>Trámite FUT</th>
                <th>Unidad Actual</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($expedientes)): ?>
                <tr><td colspan="6" class="text-center text-muted">No hay expedientes para este reporte</td></tr>
            <?php else: ?>
                <?php foreach ($expedientes as $exp): ?>
                    <tr>
                        <td class="fw-bold font-monospace"><?= ViewHelper::escape($exp['numero_expediente']) ?></td>
                        <td><?= ViewHelper::formatDate($exp['created_at']) ?></td>
                        <td><?= ViewHelper::escape($exp['tipo_solicitante'] === 'juridica' ? $exp['razon_social'] : ($exp['nombres'] . ' ' . $exp['apellidos'])) ?></td>
                        <td><?= ViewHelper::escape($exp['tramite_nombre'] ?? '-') ?></td>
                        <td><?= ViewHelper::escape($exp['unidad_actual_nombre'] ?? 'Mesa de Partes') ?></td>
                        <td><span class="badge bg-light text-dark border"><?= ViewHelper::escape($exp['estado_nombre'] ?? '-') ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Bloque de Firmas Institucionales -->
    <div class="row mt-5 pt-4 text-center">
        <div class="col-6">
            <div class="border-top border-dark mx-auto" style="width: 240px; padding-top: 5px;">
                <div class="fw-bold small">RESPONSABLE DE MESA DE PARTES</div>
                <div class="text-muted" style="font-size: 10px;">Recepción y Verificación Documentaria</div>
            </div>
        </div>
        <div class="col-6">
            <div class="border-top border-dark mx-auto" style="width: 240px; padding-top: 5px;">
                <div class="fw-bold small">DIRECCIÓN GENERAL</div>
                <div class="text-muted" style="font-size: 10px;">IESP Túpac Amaru – Cusco</div>
            </div>
        </div>
    </div>

</body>
</html>
