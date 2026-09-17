<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        $db = Database::getConnection();

        // 1. Tarjetas KPI
        // Trámites hoy
        $stmtHoy = $db->query("SELECT COUNT(*) as total FROM `expedientes` WHERE DATE(`fecha_ingreso`) = CURDATE() AND `activo` = 1");
        $tramitesHoy = (int)$stmtHoy->fetch()['total'];

        // Trámites del mes
        $stmtMes = $db->query("SELECT COUNT(*) as total FROM `expedientes` WHERE MONTH(`fecha_ingreso`) = MONTH(CURDATE()) AND YEAR(`fecha_ingreso`) = YEAR(CURDATE()) AND `activo` = 1");
        $tramitesMes = (int)$stmtMes->fetch()['total'];

        // En Mesa de Partes
        $stmtMP = $db->query("SELECT COUNT(*) as total FROM `expedientes` e INNER JOIN `estados_expediente` es ON e.estado_id = es.id WHERE es.codigo IN ('RECIBIDO', 'REGISTRADO') AND e.activo = 1");
        $enMesaPartes = (int)$stmtMP->fetch()['total'];

        // En Dirección General
        $stmtDir = $db->query("SELECT COUNT(*) as total FROM `expedientes` e INNER JOIN `estados_expediente` es ON e.estado_id = es.id WHERE es.codigo IN ('ENVIADO_A_DIRECCION', 'EN_REVISION', 'EN_REVISION_DIRECCION') AND e.activo = 1");
        $enDireccion = (int)$stmtDir->fetch()['total'];

        // En Unidades
        $stmtUnid = $db->query("SELECT COUNT(*) as total FROM `expedientes` e INNER JOIN `estados_expediente` es ON e.estado_id = es.id WHERE es.codigo IN ('DERIVADO', 'RECEPCIONADO', 'EN_TRAMITE', 'RESPONDIDO') AND e.activo = 1");
        $enUnidades = (int)$stmtUnid->fetch()['total'];

        // Pendientes totales
        $stmtPend = $db->query("SELECT COUNT(*) as total FROM `expedientes` e INNER JOIN `estados_expediente` es ON e.estado_id = es.id WHERE es.codigo NOT IN ('FINALIZADO', 'ARCHIVADO', 'ANULADO') AND e.activo = 1");
        $totalPendientes = (int)$stmtPend->fetch()['total'];

        // Observados
        $stmtObs = $db->query("SELECT COUNT(*) as total FROM `expedientes` e INNER JOIN `estados_expediente` es ON e.estado_id = es.id WHERE es.codigo = 'OBSERVADO' AND e.activo = 1");
        $totalObservados = (int)$stmtObs->fetch()['total'];

        // Finalizados
        $stmtFin = $db->query("SELECT COUNT(*) as total FROM `expedientes` e INNER JOIN `estados_expediente` es ON e.estado_id = es.id WHERE es.codigo IN ('FINALIZADO', 'APROBADO') AND e.activo = 1");
        $totalFinalizados = (int)$stmtFin->fetch()['total'];

        // Urgentes
        $stmtUrg = $db->query("SELECT COUNT(*) as total FROM `expedientes` e INNER JOIN `prioridades` p ON e.prioridad_id = p.id WHERE p.codigo IN ('URGENTE', 'MUY_URGENTE') AND e.activo = 1");
        $totalUrgentes = (int)$stmtUrg->fetch()['total'];

        // 2. Gráfico por Estado
        $stmtPorEstado = $db->query("SELECT es.nombre, es.color, COUNT(e.id) as cantidad FROM `estados_expediente` es LEFT JOIN `expedientes` e ON es.id = e.estado_id AND e.activo = 1 WHERE es.activo = 1 GROUP BY es.id ORDER BY cantidad DESC, es.orden ASC LIMIT 7");
        $datosPorEstado = $stmtPorEstado->fetchAll();

        // 3. Gráfico por Unidad
        $stmtPorUnidad = $db->query("SELECT u.nombre, COUNT(e.id) as cantidad FROM `unidades` u LEFT JOIN `expedientes` e ON u.id = e.unidad_actual_id AND e.activo = 1 WHERE u.estado = 1 GROUP BY u.id ORDER BY cantidad DESC LIMIT 6");
        $datosPorUnidad = $stmtPorUnidad->fetchAll();

        // 4. Últimos expedientes registrados
        $stmtUltimos = $db->query(
            "SELECT e.*, es.nombre as estado_nombre, es.color as estado_color, 
                    p.nombre as prioridad_nombre, p.color as prioridad_color,
                    t.nombre as tramite_nombre, u.nombre as unidad_nombre 
             FROM `expedientes` e 
             LEFT JOIN `estados_expediente` es ON e.estado_id = es.id 
             LEFT JOIN `prioridades` p ON e.prioridad_id = p.id 
             LEFT JOIN `tipos_tramite` t ON e.tipo_tramite_id = t.id 
             LEFT JOIN `unidades` u ON e.unidad_actual_id = u.id 
             WHERE e.activo = 1 
             ORDER BY e.fecha_ingreso DESC 
             LIMIT 6"
        );
        $ultimosExpedientes = $stmtUltimos->fetchAll();

        $this->view('dashboard.index', [
            'title' => 'Dashboard Institucional',
            'kpi' => [
                'hoy' => $tramitesHoy,
                'mes' => $tramitesMes,
                'mesa_partes' => $enMesaPartes,
                'direccion' => $enDireccion,
                'unidades' => $enUnidades,
                'pendientes' => $totalPendientes,
                'observados' => $totalObservados,
                'finalizados' => $totalFinalizados,
                'urgentes' => $totalUrgentes,
            ],
            'datosPorEstado' => $datosPorEstado,
            'datosPorUnidad' => $datosPorUnidad,
            'ultimosExpedientes' => $ultimosExpedientes
        ], 'app');
    }
}
