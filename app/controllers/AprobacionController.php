<?php
require_once __DIR__ . '/../models/Document.php';

class AprobacionController {
    private $documentModel;

    public function __construct() {
        $this->documentModel = new Document();
    }

    // Mostrar panel de aprobación
    public function panel() {
        if (!isset($_GET['id'])) {
            die('ID de orden no especificado');
        }

        $ordenId = intval($_GET['id']);
        $orden = $this->documentModel->getOrdenCompra($ordenId);

        if (!$orden) {
            die('Orden de compra no encontrada');
        }

        // Verificar que esté pendiente
        if ($orden['OC_ESTADO_APROBACION'] !== 'PENDIENTE') {
            $mensaje = $orden['OC_ESTADO_APROBACION'] === 'APROBADO' 
                ? 'Esta orden ya fue aprobada' 
                : 'Esta orden ya fue rechazada';
            die($mensaje);
        }

        require_once __DIR__ . '/../views/aprobacion/panel.php';
    }

    // Procesar aprobación o rechazo
    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        $ordenId = intval($_POST['orden_id'] ?? 0);
        $accion = $_POST['accion'] ?? ''; // 'aprobar' o 'rechazar'
        $observaciones = trim($_POST['observaciones'] ?? '');

        if (!$ordenId || !in_array($accion, ['aprobar', 'rechazar'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            exit;
        }

        $resultado = $this->documentModel->procesarAprobacion($ordenId, $accion, $observaciones);

        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit;
    }
}
