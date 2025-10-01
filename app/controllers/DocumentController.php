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
        $ordenId = $_SESSION['orden_id'] ?? null;

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

        // Hacer disponibles las variables en la vista
        $ordenCompraData = $ordenCompraData;
        $documentData = $documentData;

        require __DIR__ . '/../views/documents/layouts/' . $id . '.php';
    }

    // Procesar orden de compra
    public function procesarOrdenCompra() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->documentModel->guardarOrdenCompra($_POST);

            if ($resultado['success']) {
                // Guardamos en sesión que la orden está registrada
                $_SESSION['orden_guardada'] = true;
                $_SESSION['forma_pago'] = $_POST['OC_FORMA_PAGO'] ?? null;
                $_SESSION['orden_id'] = $resultado['id'];

                header("Location: /digitalizacion-documentos/documents?success=orden_compra&documento_id=" . $resultado['id']);
                exit;
            } else {
                header("Location: /digitalizacion-documentos/documents?error=" . urlencode($resultado['error']));
                exit;
            }
        }
    }
}
