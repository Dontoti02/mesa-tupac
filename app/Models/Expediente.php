<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Expediente extends Model
{
    protected string $table = 'expedientes';

    private const SIGLA_POR_DEFECTO = 'EXP';
    private const DIGITOS_POR_DEFECTO = 6;
    private const INICIO_POR_DEFECTO = 1;

    /**
     * Parámetros de numeración vigentes (Superadmin > Configuración General).
     * Se normalizan aquí para que una configuración incompleta o corrupta
     * nunca deje al sistema sin un formato de numeración válido.
     */
    public static function getNumeracionSettings(): array
    {
        $config = Configuracion::getAll();

        $sigla = mb_strtoupper(trim((string)($config['expediente_num_sigla'] ?? '')), 'UTF-8');
        if ($sigla === '' || !preg_match('/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u', $sigla)) {
            $sigla = self::SIGLA_POR_DEFECTO;
        }

        $digitos = (int)($config['expediente_num_digitos'] ?? self::DIGITOS_POR_DEFECTO);
        if ($digitos < 1 || $digitos > 12) {
            $digitos = self::DIGITOS_POR_DEFECTO;
        }

        $inicio = (int)($config['expediente_num_inicio'] ?? self::INICIO_POR_DEFECTO);
        if ($inicio < 1) {
            $inicio = self::INICIO_POR_DEFECTO;
        }

        $incluirAnio = !isset($config['expediente_num_incluir_anio'])
            || (int)$config['expediente_num_incluir_anio'] === 1;
        $anio = date('Y');

        return [
            'sigla' => $sigla,
            'incluir_anio' => $incluirAnio,
            'digitos' => $digitos,
            'inicio' => $inicio,
            'anio' => $anio,
            // El correlativo se reinicia por año cuando el año forma parte del
            // número; si se omite el año debe ser único de forma global.
            'clave_correlativo' => 'EXPEDIENTE' . ($incluirAnio ? '-' . $anio : '-GLOBAL'),
            'patron' => $sigla . ($incluirAnio ? '-' . $anio . '-' : '-')
        ];
    }

    /**
     * Compone el número de expediente con el formato configurado.
     */
    public static function formatearNumero(array $numeracion, int $correlativo): string
    {
        $partes = [$numeracion['sigla']];
        if ($numeracion['incluir_anio']) {
            $partes[] = $numeracion['anio'];
        }
        $partes[] = str_pad((string)$correlativo, $numeracion['digitos'], '0', STR_PAD_LEFT);

        return implode('-', $partes);
    }

    /**
     * Número que se asignará al siguiente expediente (solo vista previa,
     * no consume correlativo).
     */
    public static function previewNumeroExpediente(): string
    {
        return self::formatearNumero(self::getNumeracionSettings(), self::proximoCorrelativo());
    }

    /**
     * Correlativo que corresponde al siguiente expediente.
     */
    public static function proximoCorrelativo(?array $numeracion = null): int
    {
        return (new self())->resolverProximoCorrelativo($numeracion ?? self::getNumeracionSettings());
    }

    public function generateNumeroExpediente(): string
    {
        $numeracion = self::getNumeracionSettings();

        return self::formatearNumero($numeracion, $this->siguienteCorrelativo($numeracion));
    }

    /**
     * Reserva el siguiente correlativo de forma atómica. Debe invocarse dentro
     * de la transacción que inserta el expediente: el bloqueo de la fila
     * correlativa serializa los registros simultáneos y la reversión de la
     * transacción devuelve el número si el expediente no llega a crearse.
     */
    private function siguienteCorrelativo(array $numeracion): int
    {
        $clave = $numeracion['clave_correlativo'];

        try {
            $stmt = $this->db()->prepare(
                "INSERT INTO `correlativos` (`clave`, `ultimo_numero`) VALUES (:clave, :inicial)
                 ON DUPLICATE KEY UPDATE `ultimo_numero` = `ultimo_numero` + 1"
            );
            $stmt->execute([
                'clave' => $clave,
                // Solo aplica al crear la secuencia por primera vez: arranca
                // por encima del mayor correlativo ya emitido para no repetir.
                'inicial' => max($numeracion['inicio'], $this->ultimoCorrelativoEmitido($numeracion) + 1)
            ]);

            $stmt = $this->db()->prepare(
                "SELECT `ultimo_numero` FROM `correlativos` WHERE `clave` = :clave LIMIT 1"
            );
            $stmt->execute(['clave' => $clave]);

            return max(1, (int)$stmt->fetchColumn());
        } catch (PDOException $e) {
            // Instalaciones previas a la tabla `correlativos`: se conserva el
            // cálculo por lectura para no interrumpir el registro de expedientes.
            if (!$this->faltaTablaCorrelativos($e)) {
                throw $e;
            }
            error_log('Numeracion: tabla correlativos no disponible, se usa cálculo por lectura.');

            return max($numeracion['inicio'], $this->ultimoCorrelativoEmitido($numeracion) + 1);
        }
    }

    private function resolverProximoCorrelativo(array $numeracion): int
    {
        try {
            $stmt = $this->db()->prepare(
                "SELECT `ultimo_numero` FROM `correlativos` WHERE `clave` = :clave LIMIT 1"
            );
            $stmt->execute(['clave' => $numeracion['clave_correlativo']]);
            $actual = (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            if (!$this->faltaTablaCorrelativos($e)) {
                throw $e;
            }
            $actual = 0;
        }

        return max(
            $numeracion['inicio'],
            $actual + 1,
            $this->ultimoCorrelativoEmitido($numeracion) + 1
        );
    }

    /**
     * Distingue "la tabla aún no existe" de un fallo real de base de datos,
     * para no degradar la numeración ante errores ajenos a la instalación.
     */
    private function faltaTablaCorrelativos(PDOException $e): bool
    {
        return $e->getCode() === '42S02'
            || $e->getCode() === 1146
            || str_contains($e->getMessage(), "correlativos'");
    }

    /**
     * Mayor correlativo ya registrado con el formato vigente.
     */
    private function ultimoCorrelativoEmitido(array $numeracion): int
    {
        $stmt = $this->db()->prepare(
            "SELECT COALESCE(MAX(CAST(RIGHT(`numero_expediente`, :largo) AS UNSIGNED)), 0) AS `ultimo`
             FROM `expedientes`
             WHERE `numero_expediente` LIKE :patron"
        );
        $stmt->execute([
            'largo' => $numeracion['digitos'],
            'patron' => $numeracion['patron'] . '%'
        ]);

        return (int)$stmt->fetch()['ultimo'];
    }

    public function generateCodigoSeguimiento(): string
    {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $max = strlen($chars) - 1;

        do {
            $code = 'TA-';
            for ($i = 0; $i < 6; $i++) {
                $code .= $chars[random_int(0, $max)];
            }

            $stmt = $this->db()->prepare(
                "SELECT COUNT(*) as total FROM `expedientes` WHERE `codigo_seguimiento` = :code"
            );
            $stmt->execute(['code' => $code]);
            $exists = (int)$stmt->fetch()['total'] > 0;
        } while ($exists);

        return $code;
    }

    public function findWithRelations(int|string $id): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT e.*, 
                    es.nombre as estado_nombre, es.color as estado_color, es.icono as estado_icono, es.codigo as estado_codigo,
                    p.nombre as prioridad_nombre, p.color as prioridad_color,
                    t.nombre as tramite_nombre, t.codigo as tramite_codigo, t.plazo_dias, t.costo as tramite_costo,
                    c.nombre as categoria_nombre,
                    u_act.nombre as unidad_actual_nombre, u_act.codigo as unidad_actual_codigo,
                    u_resp.nombre as unidad_responsable_nombre,
                    prog.nombre as programa_nombre
             FROM `expedientes` e
             LEFT JOIN `estados_expediente` es ON e.estado_id = es.id
             LEFT JOIN `prioridades` p ON e.prioridad_id = p.id
             LEFT JOIN `tipos_tramite` t ON e.tipo_tramite_id = t.id
             LEFT JOIN `categorias_tramite` c ON t.categoria_id = c.id
             LEFT JOIN `unidades` u_act ON e.unidad_actual_id = u_act.id
             LEFT JOIN `unidades` u_resp ON e.unidad_responsable_id = u_resp.id
             LEFT JOIN `programas_estudio` prog ON e.programa_id = prog.id
             WHERE e.id = :id AND e.activo = 1
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Resumen de los expedientes activos indicados, usado para dejar
     * constancia en auditoría antes de eliminarlos.
     */
    public function resumenPorIds(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids), fn(int $id): bool => $id > 0));
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db()->prepare(
            "SELECT `id`, `numero_expediente`, `codigo_seguimiento`
             FROM `expedientes`
             WHERE `id` IN ({$placeholders}) AND `activo` = 1"
        );
        $stmt->execute($ids);

        return $stmt->fetchAll();
    }

    /**
     * Elimina lógicamente (activo = 0) los expedientes indicados. Se conserva
     * la trazabilidad y el número de expediente no vuelve a emitirse.
     */
    public function desactivarMultiples(array $ids): int
    {
        $ids = array_values(array_filter(array_map('intval', $ids), fn(int $id): bool => $id > 0));
        if (empty($ids)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db()->prepare(
            "UPDATE `expedientes` SET `activo` = 0
             WHERE `id` IN ({$placeholders}) AND `activo` = 1"
        );
        $stmt->execute($ids);

        return $stmt->rowCount();
    }

    public function findByTracking(string $numeroExpediente, string $codigoSeguimiento, ?string $dni = null): ?array
    {
        $sql = "SELECT e.*, 
                       es.nombre as estado_nombre, es.color as estado_color, es.icono as estado_icono, es.codigo as estado_codigo,
                       t.nombre as tramite_nombre,
                       u_act.nombre as unidad_actual_nombre
                FROM `expedientes` e
                LEFT JOIN `estados_expediente` es ON e.estado_id = es.id
                LEFT JOIN `tipos_tramite` t ON e.tipo_tramite_id = t.id
                LEFT JOIN `unidades` u_act ON e.unidad_actual_id = u_act.id
                WHERE (e.numero_expediente = :num OR e.codigo_seguimiento = :code) 
                  AND e.activo = 1";
        
        $params = [
            'num' => trim($numeroExpediente),
            'code' => trim($codigoSeguimiento)
        ];

        if (!empty($dni)) {
            $sql .= " AND e.dni = :dni";
            $params['dni'] = trim($dni);
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
