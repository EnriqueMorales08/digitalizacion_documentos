<?php
require_once __DIR__ . '/../models/Document.php';

class ExpedienteController {
    private $documentModel;

    public function __construct() {
        $this->documentModel = new Document();
    }

    /**
     * Vista principal: Listar todos los expedientes
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        $resultado = $this->documentModel->listarOrdenesCompra($page, 20, $search);
        
        require __DIR__ . '/../views/expedientes/index.php';
    }

    /**
     * Ver todos los documentos de un expediente específico
     */
    public function ver() {
        // Aceptar búsqueda por ID o por número de expediente
        if (isset($_GET['id'])) {
            // Buscar por ID directo
            $ordenId = (int)$_GET['id'];
            $ordenCompra = $this->documentModel->getOrdenCompra($ordenId);
        } elseif (isset($_GET['numero'])) {
            // Buscar por número de expediente
            $numeroExpediente = trim($_GET['numero']);
            $ordenCompra = $this->documentModel->buscarPorNumeroExpediente($numeroExpediente);
        } else {
            header("Location: /digitalizacion-documentos/expedientes");
            exit;
        }
        
        if (!$ordenCompra) {
            header("Location: /digitalizacion-documentos/expedientes?error=" . urlencode('Expediente no encontrado'));
            exit;
        }

        $ordenId = $ordenCompra['OC_ID'];
        
        // Guardar en sesión para uso posterior
        $_SESSION['orden_id'] = $ordenId;
        
        // Obtener todos los documentos asociados
        $documentos = $this->documentModel->getDocumentosPorOrden($ordenId);
        
        require __DIR__ . '/../views/expedientes/ver.php';
    }

    /**
     * Imprimir todos los documentos de un expediente
     */
    public function imprimirTodos() {
        if (!isset($_GET['numero'])) {
            header("Location: /digitalizacion-documentos/expedientes");
            exit;
        }

        $numeroExpediente = trim($_GET['numero']);
        
        // Buscar la orden de compra
        $ordenCompra = $this->documentModel->buscarPorNumeroExpediente($numeroExpediente);
        
        if (!$ordenCompra) {
            header("Location: /digitalizacion-documentos/expedientes?error=" . urlencode('Expediente no encontrado'));
            exit;
        }

        $ordenId = $ordenCompra['OC_ID'];
        
        // Obtener todos los documentos asociados
        $documentos = $this->documentModel->getDocumentosPorOrden($ordenId);
        
        // Cargar datos del vehículo si es necesario
        $vehiculoData = [];
        $chasis = $ordenCompra['OC_VEHICULO_CHASIS'] ?? '';
        if ($chasis) {
            $vehiculoData = $this->documentModel->buscarVehiculoPorChasis($chasis);
        }
        
        require __DIR__ . '/../views/expedientes/imprimir_todos.php';
    }

    /**
     * Imprimir un documento individual de un expediente
     */
    public function imprimirDocumento() {
        if (!isset($_GET['numero']) || !isset($_GET['documento'])) {
            header("Location: /digitalizacion-documentos/expedientes");
            exit;
        }

        $numeroExpediente = trim($_GET['numero']);
        $documentoId = trim($_GET['documento']);
        
        // Buscar la orden de compra
        $ordenCompra = $this->documentModel->buscarPorNumeroExpediente($numeroExpediente);
        
        if (!$ordenCompra) {
            header("Location: /digitalizacion-documentos/expedientes?error=" . urlencode('Expediente no encontrado'));
            exit;
        }

        $ordenId = $ordenCompra['OC_ID'];
        
        // Preparar datos para la vista
        $ordenCompraData = $ordenCompra;
        $id = $ordenId;
        
        // Cargar datos del documento específico
        $documentData = $this->documentModel->getDocumentData($documentoId, $ordenId);
        
        // Cargar datos del vehículo si es necesario
        $vehiculoData = [];
        if (in_array($documentoId, ['carta-caracteristicas', 'carta_caracteristicas_banbif'])) {
            $chasis = $ordenCompra['OC_VEHICULO_CHASIS'] ?? '';
            if ($chasis) {
                $vehiculoData = $this->documentModel->buscarVehiculoPorChasis($chasis);
            }
        }
        
        // Redirigir a la vista de impresión del documento específico
        require __DIR__ . "/../views/documents/layouts/{$documentoId}.php";
    }

    /**
     * API: Buscar expediente por número (AJAX)
     */
    public function buscar() {
        header('Content-Type: application/json');
        
        if (!isset($_GET['numero'])) {
            echo json_encode(['success' => false, 'message' => 'Número de expediente requerido']);
            exit;
        }

        $numeroExpediente = trim($_GET['numero']);
        $ordenCompra = $this->documentModel->buscarPorNumeroExpediente($numeroExpediente);
        
        if ($ordenCompra) {
            $documentos = $this->documentModel->getDocumentosPorOrden($ordenCompra['OC_ID']);
            echo json_encode([
                'success' => true,
                'orden' => $ordenCompra,
                'documentos' => $documentos
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Expediente no encontrado']);
        }
        exit;
    }
}
