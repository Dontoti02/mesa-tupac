<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Expediente;
use App\Models\ExpedienteMovimiento;

class ConsultaController extends Controller
{
    private Expediente $expedienteModel;
    private ExpedienteMovimiento $movimientoModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->expedienteModel = new Expediente();
        $this->movimientoModel = new ExpedienteMovimiento();
    }

    public function index(): void
    {
        $numero = trim((string)$this->request->input('expediente', ''));
        $codigo = trim((string)$this->request->input('codigo', ''));
        $dni = trim((string)$this->request->input('dni', ''));

        $expediente = null;
        $movimientos = [];
        $busquedaRealizada = false;
        $error = null;

        if (!empty($numero) || !empty($codigo)) {
            $busquedaRealizada = true;
            $expediente = $this->expedienteModel->findByTracking($numero, $codigo, !empty($dni) ? $dni : null);

            if ($expediente) {
                // Obtener historial estrictamente público (es_publico = 1)
                $movimientos = $this->movimientoModel->getHistory((int)$expediente['id'], true);
            } else {
                $error = 'No se encontró ningún expediente con los datos de consulta proporcionados. Verifique el número de expediente o código de seguimiento.';
            }
        }

        $this->view('public.consulta', [
            'title' => 'Seguimiento de Expediente - Mesa de Partes Virtual',
            'expediente' => $expediente,
            'movimientos' => $movimientos,
            'busquedaRealizada' => $busquedaRealizada,
            'error' => $error,
            'oldNumero' => $numero,
            'oldCodigo' => $codigo,
            'oldDni' => $dni
        ], 'public');
    }
}
