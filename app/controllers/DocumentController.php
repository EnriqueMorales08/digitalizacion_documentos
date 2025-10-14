<?php
require_once __DIR__ . '/../models/Document.php';

class DocumentController {
    private $documentModel;

    public function __construct() {
        $this->documentModel = new Document();
    }

    // Página de bienvenida (después del login)
    public function index() {
        // Obtener el nombre del usuario (puede venir de sesión o parámetro)
        $user = $_SESSION['user_name'] ?? $_GET['user'] ?? 'Asesor';
        
        // Mostrar vista de bienvenida
        require __DIR__ . '/../views/documents/bienvenida.php';
    }

    // Mostrar documento (ejemplo simple)
    public function show() {
        if (!isset($_GET['id'])) {
            header("Location: /digitalizacion-documentos/documents");
            exit;
        }

        $id = $_GET['id'];
        $ordenId = $_SESSION['orden_id'] ?? null;
        $forma_pago = trim($_SESSION['forma_pago'] ?? '');
        $banco_abono = trim($_SESSION['banco_abono'] ?? '');

        // 🔒 Validar acceso a cartas de características según condiciones
        if (in_array($id, ['carta-caracteristicas', 'carta_caracteristicas_banbif'])) {
            // Solo permitir acceso si forma de pago es CRÉDITO
            if ($forma_pago !== 'CRÉDITO') {
                header("Location: /digitalizacion-documentos/documents?error=" . urlencode('Las cartas de características solo están disponibles para compras a CRÉDITO'));
                exit;
            }

            // Validar que se acceda a la carta correcta según el banco
            if ($id === 'carta_caracteristicas_banbif' && $banco_abono !== 'Banco Interamericano de Finanzas') {
                header("Location: /digitalizacion-documentos/documents?error=" . urlencode('Esta carta solo está disponible para Banco Interamericano de Finanzas'));
                exit;
            }

            if ($id === 'carta-caracteristicas' && $banco_abono === 'Banco Interamericano de Finanzas') {
                header("Location: /digitalizacion-documentos/documents?error=" . urlencode('Para Banco Interamericano de Finanzas debe usar la Carta Características Banbif'));
                exit;
            }
        }

        // Cargar datos de la orden de compra
        $ordenCompraData = [];
        if ($ordenId) {
            $ordenCompraData = $this->documentModel->getOrdenCompra($ordenId);
        }

        // Cargar datos del documento específico
        $documentData = [];
        if ($ordenId) {
            $documentData = $this->documentModel->getDocumentData($id, $ordenId);
        }

        // Los datos se pre-llenan desde $ordenCompraData

        // Cargar datos del vehículo para documentos que lo necesiten
        $vehiculoData = [];
        if ($ordenId && in_array($id, ['carta-caracteristicas', 'carta_caracteristicas_banbif'])) {
            $chasis = $ordenCompraData['OC_VEHICULO_CHASIS'] ?? '';
            if ($chasis) {
                $vehiculoData = $this->documentModel->buscarVehiculoPorChasis($chasis);
            }
        }

        // Obtener lista de bancos
        $bancos = $this->documentModel->getBancos();

        // Obtener lista de asesores
        $asesores = $this->documentModel->getAsesores();

        // Hacer disponibles las variables en la vista
        $ordenCompraData = $ordenCompraData;
        $documentData = $documentData;
        $vehiculoData = $vehiculoData;
        $bancos = $bancos;
        $asesores = $asesores;

        require __DIR__ . '/../views/documents/layouts/' . $id . '.php';
    }

    // Procesar orden de compra
    public function procesarOrdenCompra() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->documentModel->guardarOrdenCompra($_POST, $_FILES);

            if ($resultado['success']) {
                // Guardamos en sesión que la orden está registrada
                $_SESSION['orden_guardada'] = true;
                $_SESSION['forma_pago'] = $_POST['OC_FORMA_PAGO'] ?? null;
                $_SESSION['banco_abono'] = $_POST['OC_BANCO_ABONO'] ?? null;
                $_SESSION['orden_id'] = $resultado['id'];
                $_SESSION['orden_data'] = $_POST; // Guardar todos los datos de la orden
                
                // Guardar firmas en sesión
                $_SESSION['firmas'] = [
                    'OC_ASESOR_FIRMA' => $_POST['OC_ASESOR_FIRMA'] ?? null,
                    'OC_CLIENTE_FIRMA' => $_POST['OC_CLIENTE_FIRMA'] ?? null,
                    'OC_CLIENTE_HUELLA' => $_POST['OC_CLIENTE_HUELLA'] ?? null,
                    'OC_JEFE_FIRMA' => $_POST['OC_JEFE_FIRMA'] ?? null,
                    'OC_JEFE_HUELLA' => $_POST['OC_JEFE_HUELLA'] ?? null,
                    'OC_VISTO_ADV' => $_POST['OC_VISTO_ADV'] ?? null
                ];

                // Comentado: no usar cookie para evitar persistencia de firmas entre sesiones
                // setcookie('orden_id', $resultado['id'], time() + 3600, '/'); // 1 hora

                // Obtener el número de expediente para redirigir a ver documentos
                $numeroExpediente = $_POST['OC_NUMERO_EXPEDIENTE'] ?? '';
                // Usar el ID de la orden para evitar problemas de búsqueda inmediata
                header("Location: /digitalizacion-documentos/expedientes/ver?id=" . $resultado['id'] . "&success=orden_guardada");
                exit;
            } else {
                header("Location: /digitalizacion-documentos/documents?error=" . urlencode($resultado['error']));
                exit;
            }
        }
    }

    // Buscar vehículo por chasis
    public function buscarVehiculo() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['chasis'])) {
            $chasis = trim($_GET['chasis']);
            $vehiculo = $this->documentModel->buscarVehiculoPorChasis($chasis);
            header('Content-Type: application/json');
            echo json_encode($vehiculo ?: []);
            exit;
        }
    }

    // Buscar datos de mantenimiento por marca y modelo
    public function buscarDatosMantenimiento() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['marca']) && isset($_GET['modelo'])) {
            $marca = trim($_GET['marca']);
            $modelo = trim($_GET['modelo']);
            $datos = $this->documentModel->getDatosMantenimiento($marca, $modelo);
            header('Content-Type: application/json');
            echo json_encode($datos ?: []);
            exit;
        }
    }
    
    // Obtener agencias
    public function getAgencias() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $agencias = $this->documentModel->getAgencias();
            header('Content-Type: application/json');
            echo json_encode($agencias);
            exit;
        }
    }
    
    // Obtener nombres por agencia
    public function getNombresPorAgencia() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['agencia'])) {
            $agencia = trim($_GET['agencia']);
            $nombres = $this->documentModel->getNombresPorAgencia($agencia);
            header('Content-Type: application/json');
            echo json_encode($nombres);
            exit;
        }
    }
    
    // Obtener centros de costo por nombre
    public function getCentrosCostoPorNombre() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['agencia']) && isset($_GET['nombre'])) {
            $agencia = trim($_GET['agencia']);
            $nombre = trim($_GET['nombre']);
            $centros = $this->documentModel->getCentrosCostoPorNombre($agencia, $nombre);
            header('Content-Type: application/json');
            echo json_encode($centros);
            exit;
        }
    }

    // Verificar firma
    public function verificarFirma() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario']) && isset($_POST['password'])) {
            $usuario = trim($_POST['usuario']);
            $password = trim($_POST['password']);
            $firma = $this->documentModel->verificarFirma($usuario, $password);
            header('Content-Type: application/json');
            echo json_encode(['success' => $firma !== null, 'firma' => $firma]);
            exit;
        }
    }

    // Guardar documento individual
    public function guardarDocumento() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $documentType = $_POST['document_type'] ?? '';
            $ordenId = $_SESSION['orden_id'] ?? null;

            if (!$ordenId) {
                header("Location: /digitalizacion-documentos/documents?error=no_orden");
                exit;
            }

            $resultado = $this->documentModel->guardarDocumentoIndividual($documentType, $_POST, $ordenId);

            if ($resultado['success']) {
                header("Location: /digitalizacion-documentos/documents/show?id=$documentType&success=documento_guardado");
                exit;
            } else {
                header("Location: /digitalizacion-documentos/documents/show?id=$documentType&error=" . urlencode($resultado['error']));
                exit;
            }
        }
    }
}
