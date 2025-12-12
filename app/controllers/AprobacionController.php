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

        // Validar token si se accede sin login
        if (isset($_GET['token'])) {
            $tokenProporcionado = $_GET['token'];
            $tokenOrden = $orden['OC_TOKEN_APROBACION'] ?? '';
            
            if (empty($tokenOrden) || $tokenProporcionado !== $tokenOrden) {
                die('Token de acceso inválido o expirado');
            }
            
            // Token válido - permitir acceso sin login
            $accesoConToken = true;
        } else {
            // Sin token - requiere estar logueado (ya validado por routes.php)
            $accesoConToken = false;
        }

        // Ahora permitimos mostrar el panel aunque esté aprobado o rechazado
        // La lógica de qué mostrar se maneja en la vista

        require_once __DIR__ . '/../views/aprobacion/panel.php';
    }

    // Procesar aprobación o rechazo
    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        // Validar acceso: con token O con permisos de usuario logueado
        $accesoConToken = false;
        if (isset($_POST['token'])) {
            // Acceso con token - validar
            $ordenId = intval($_POST['orden_id'] ?? 0);
            if ($ordenId) {
                $orden = $this->documentModel->getOrdenCompra($ordenId);
                if ($orden) {
                    $tokenProporcionado = $_POST['token'];
                    $tokenOrden = $orden['OC_TOKEN_APROBACION'] ?? '';
                    
                    if (!empty($tokenOrden) && $tokenProporcionado === $tokenOrden) {
                        $accesoConToken = true;
                    }
                }
            }
            
            if (!$accesoConToken) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Token de acceso inválido o expirado']);
                exit;
            }
        } else {
            // Sin token - verificar permisos de usuario logueado
            if (!Document::puedeEditar()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'No tiene permisos para aprobar o rechazar documentos']);
                exit;
            }
        }

        $ordenId = intval($_POST['orden_id'] ?? 0);
        $accion = $_POST['accion'] ?? ''; // 'aprobar' o 'rechazar'
        $observaciones = trim($_POST['observaciones'] ?? '');

        if (!$ordenId || !in_array($accion, ['aprobar', 'rechazar'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            exit;
        }

        // Obtener la orden actual para verificar su estado
        $orden = $this->documentModel->getOrdenCompra($ordenId);
        if (!$orden) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Orden no encontrada']);
            exit;
        }

        $estadoActual = $orden['OC_ESTADO_APROBACION'] ?? 'PENDIENTE';

        // Validar acciones permitidas según estado
        if ($accion === 'aprobar' && $estadoActual !== 'PENDIENTE') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Solo se pueden aprobar órdenes pendientes']);
            exit;
        }

        $resultado = $this->documentModel->procesarAprobacion($ordenId, $accion, $observaciones);

        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit;
    }

    // Verificar estado de una orden (API para polling)
    public function verificarEstado() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        $ordenId = intval($_GET['id'] ?? 0);
        
        if (!$ordenId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'ID de orden no especificado']);
            exit;
        }

        $orden = $this->documentModel->getOrdenCompra($ordenId);
        
        if (!$orden) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Orden no encontrada']);
            exit;
        }

        $estado = $orden['OC_ESTADO_APROBACION'] ?? 'PENDIENTE';
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'estado' => $estado,
            'orden_id' => $ordenId
        ]);
        exit;
    }
}
