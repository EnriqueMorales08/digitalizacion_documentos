<?php
require_once __DIR__ . '/../models/Document.php';

class DocumentController {
    private $documentModel;

    public function __construct() {
        $this->documentModel = new Document();
    }

    // Panel principal
    public function index() {
        // 🚨 Aquí definimos las variables para que no estén undefined
        $orden_guardada = isset($_SESSION['orden_guardada']) && $_SESSION['orden_guardada'] === true;
        $forma_pago = $_SESSION['forma_pago'] ?? null;

        // Lista de documentos que se mostrarán en el panel
        $documents = [
            ['id' => 'orden-compra', 'title' => 'Orden de Compra'],
            ['id' => 'acta-conocimiento-conformidad', 'title' => 'Acta Conocimiento Conformidad'],
            ['id' => 'actorizacion-datos-personales', 'title' => 'Autorización Datos Personales'],
            ['id' => 'carta_conocimiento_aceptacion', 'title' => 'Carta Conocimiento Aceptación'],
            ['id' => 'carta_felicitaciones', 'title' => 'Carta Felicitaciones'],
            ['id' => 'carta_recepcion', 'title' => 'Carta Recepción'],
            ['id' => 'carta-caracteristicas', 'title' => 'Carta Características'],
            ['id' => 'carta_caracteristicas_banbif', 'title' => 'Carta Características Banbif'],
            ['id' => 'carta_obsequios', 'title' => 'Carta Obsequios'],
            ['id' => 'politica_proteccion_datos', 'title' => 'Política de Protección de Datos'],
        ];

        // Hacemos disponibles las variables en la vista
        require __DIR__ . '/../views/documents/index.php';
    }

    // Mostrar documento (ejemplo simple)
    public function show() {
        if (!isset($_GET['id'])) {
            header("Location: /digitalizacion-documentos/documents");
            exit;
        }

        $id = $_GET['id'];
        $ordenId = $_SESSION['orden_id'] ?? $_COOKIE['orden_id'] ?? null;

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

        // Hacer disponibles las variables en la vista
        $ordenCompraData = $ordenCompraData;
        $documentData = $documentData;
        $vehiculoData = $vehiculoData;
        $bancos = $bancos;

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
                $_SESSION['orden_id'] = $resultado['id'];

                // También en cookie por si la sesión se pierde
                setcookie('orden_id', $resultado['id'], time() + 3600, '/'); // 1 hora

                header("Location: /digitalizacion-documentos/documents?success=orden_compra");
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
                header("Location: /digitalizacion-documentos/documents/show?id=$documentType&success=guardado");
                exit;
            } else {
                header("Location: /digitalizacion-documentos/documents/show?id=$documentType&error=" . urlencode($resultado['error']));
                exit;
            }
        }
    }
}
