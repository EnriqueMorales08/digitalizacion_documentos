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
        
        // Priorizar orden_id de la URL, luego de la sesión
        if (isset($_GET['orden_id']) && !empty($_GET['orden_id'])) {
            $ordenId = (int)$_GET['orden_id'];
            // Actualizar sesión con el ID correcto
            $_SESSION['orden_id'] = $ordenId;
        } else {
            $ordenId = $_SESSION['orden_id'] ?? null;
        }

        // Cargar datos de la orden de compra PRIMERO (antes de validaciones)
        $ordenCompraData = [];
        if ($ordenId) {
            $ordenCompraData = $this->documentModel->getOrdenCompra($ordenId);
        } else {
            // Si es una nueva orden de compra, generar el número de expediente
            if ($id === 'orden-compra') {
                // Verificar si ya existe un número en sesión para esta nueva orden
                if (!isset($_SESSION['numero_expediente_temporal'])) {
                    $_SESSION['numero_expediente_temporal'] = $this->documentModel->generarNumeroExpediente();
                }
                $ordenCompraData['OC_NUMERO_EXPEDIENTE'] = $_SESSION['numero_expediente_temporal'];
            }
        }
        
        // Obtener forma_pago y banco_abono para validaciones de acceso.
        // Prioridad:
        // 1) Valores enviados por GET (preview en tiempo real desde la OC)
        // 2) Valores guardados en BD (ordenCompraData)
        // 3) Valores en sesión (última OC guardada)

        $forma_pago = isset($_GET['forma_pago']) && $_GET['forma_pago'] !== ''
            ? trim($_GET['forma_pago'])
            : (!empty($ordenCompraData['OC_FORMA_PAGO'])
                ? trim($ordenCompraData['OC_FORMA_PAGO'])
                : trim($_SESSION['forma_pago'] ?? ''));

        $banco_abono = isset($_GET['banco_abono']) && $_GET['banco_abono'] !== ''
            ? trim($_GET['banco_abono'])
            : (!empty($ordenCompraData['OC_BANCO_ABONO'])
                ? trim($ordenCompraData['OC_BANCO_ABONO'])
                : trim($_SESSION['banco_abono'] ?? ''));

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
        
        // Convertir fechas DateTime a string para que funcionen en inputs type="date"
        if (isset($ordenCompraData['OC_FECHA_ORDEN']) && $ordenCompraData['OC_FECHA_ORDEN'] instanceof DateTime) {
            $ordenCompraData['OC_FECHA_ORDEN'] = $ordenCompraData['OC_FECHA_ORDEN']->format('Y-m-d');
        }
        if (isset($ordenCompraData['OC_FECHA_NACIMIENTO']) && $ordenCompraData['OC_FECHA_NACIMIENTO'] instanceof DateTime) {
            $ordenCompraData['OC_FECHA_NACIMIENTO'] = $ordenCompraData['OC_FECHA_NACIMIENTO']->format('Y-m-d');
        }

        // Cargar datos del documento específico
        $documentData = [];
        if ($ordenId) {
            $documentData = $this->documentModel->getDocumentData($id, $ordenId);
            // Debug: verificar si se cargaron datos
            error_log("📄 Documento: $id | Orden ID: $ordenId");
            error_log("📦 documentData cargado: " . (empty($documentData) ? 'VACÍO' : 'CON DATOS'));
            if (!empty($documentData)) {
                error_log("🔍 Claves en documentData: " . implode(', ', array_keys($documentData)));
            }
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
        $documentModel = $this->documentModel; // Pasar modelo a la vista
        // Solo activar modo visualización cuando modo=ver en la URL
        $modoImpresion = isset($_GET['modo']) && $_GET['modo'] === 'ver';
        // Modo edición: cuando hay datos guardados pero no está en modo visualización
        $modoEdicion = !empty($documentData) && !$modoImpresion;

        require __DIR__ . '/../views/documents/layouts/' . $id . '.php';
    }

    // Procesar orden de compra
    public function procesarOrdenCompra() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("🔵 === INICIO procesarOrdenCompra() ===");
            error_log("📋 POST keys: " . print_r(array_keys($_POST), true));
            error_log("✅ Documentos marcados: " . print_r($_POST['generar_documento'] ?? [], true));
            
            // 🔒 Verificar permisos de edición
            if (!Document::puedeEditar()) {
                header("Location: /digitalizacion-documentos/documents?error=" . urlencode('No tiene permisos para editar documentos'));
                exit;
            }
            
            error_log("💾 Guardando orden de compra...");
            $resultado = $this->documentModel->guardarOrdenCompra($_POST, $_FILES);
            error_log("📊 Resultado: " . ($resultado['success'] ? 'SUCCESS' : 'FAIL') . " | ID: " . ($resultado['id'] ?? 'N/A'));

            if ($resultado['success']) {
                $ordenId = $resultado['id'];
                
                // Guardamos en sesión que la orden está registrada
                $_SESSION['orden_guardada'] = true;
                $_SESSION['forma_pago'] = $_POST['OC_FORMA_PAGO'] ?? null;
                $_SESSION['banco_abono'] = $_POST['OC_BANCO_ABONO'] ?? null;
                $_SESSION['orden_id'] = $ordenId;
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

                // 🎯 PROCESAR DOCUMENTOS RELACIONADOS MARCADOS
                error_log("🎯 Procesando documentos relacionados para orden ID: $ordenId");
                $this->procesarDocumentosRelacionados($ordenId, $_POST);

                // Comentado: no usar cookie para evitar persistencia de firmas entre sesiones
                // setcookie('orden_id', $resultado['id'], time() + 3600, '/'); // 1 hora

                // Obtener el número de expediente para redirigir a ver documentos
                $numeroExpediente = $_POST['OC_NUMERO_EXPEDIENTE'] ?? '';
                // Usar el ID de la orden para evitar problemas de búsqueda inmediata
                error_log("✅ Orden guardada exitosamente, redirigiendo a expediente...");
                header("Location: /digitalizacion-documentos/expedientes/ver?id=" . $ordenId . "&success=orden_guardada");
                exit;
            } else {
                error_log("❌ Error al guardar orden: " . ($resultado['error'] ?? 'desconocido'));
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

    // Validar asignación de vehículo al asesor
    public function validarAsignacionVehiculo() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['chasis'])) {
            $chasis = trim($_GET['chasis']);
            $resultado = $this->documentModel->validarAsignacionVehiculo($chasis);
            header('Content-Type: application/json');
            echo json_encode($resultado);
            exit;
        }
    }
    
    // Notificar al asesor cuando otro usuario intenta usar su vehículo asignado
    public function notificarIntentoUsoVehiculo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $chasis = $data['chasis'] ?? '';
            $vendedorAsignado = $data['vendedor_asignado'] ?? '';
            
            $resultado = $this->documentModel->enviarNotificacionIntentoUso($chasis, $vendedorAsignado);
            header('Content-Type: application/json');
            echo json_encode($resultado);
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
        // Limpiar cualquier output previo
        if (ob_get_level()) {
            ob_clean();
        }
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        if (!isset($_POST['usuario']) || !isset($_POST['password'])) {
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros']);
            exit;
        }
        
        try {
            $usuario = trim($_POST['usuario']);
            $password = trim($_POST['password']);
            
            error_log("Verificando firma para usuario: " . $usuario);
            
            $firma = $this->documentModel->verificarFirma($usuario, $password);
            
            error_log("Resultado de verificación: " . ($firma ? "OK" : "NULL"));
            
            if ($firma !== null) {
                echo json_encode(['success' => true, 'firma' => $firma]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
            }
        } catch (Exception $e) {
            error_log("Error en verificarFirma: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    // Guardar documento individual
    public function guardarDocumento() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("🔵 === INICIO guardarDocumento() ===");
            error_log("📋 POST recibido: " . print_r(array_keys($_POST), true));
            
            // 🔒 Verificar permisos de edición
            if (!Document::puedeEditar()) {
                header("Location: /digitalizacion-documentos/documents?error=" . urlencode('No tiene permisos para editar documentos'));
                exit;
            }
            
            $documentType = $_POST['document_type'] ?? '';
            $ordenId = $_SESSION['orden_id'] ?? null;
            
            error_log("📄 Document Type: '$documentType'");
            error_log("🆔 Orden ID: " . ($ordenId ?? 'NULL'));
            error_log("✅ Documentos marcados: " . print_r($_POST['generar_documento'] ?? [], true));

            if (!$ordenId) {
                error_log("❌ No hay orden_id en sesión");
                header("Location: /digitalizacion-documentos/documents?error=no_orden");
                exit;
            }

            error_log("💾 Guardando documento principal: $documentType");
            $resultado = $this->documentModel->guardarDocumentoIndividual($documentType, $_POST, $ordenId);
            error_log("📊 Resultado guardado: " . ($resultado['success'] ? 'SUCCESS' : 'FAIL'));

            // Si es orden de compra y se guardó exitosamente, procesar documentos relacionados
            if ($resultado['success'] && $documentType === 'orden-compra') {
                error_log("🎯 Es orden-compra, procesando documentos relacionados...");
                $this->procesarDocumentosRelacionados($ordenId, $_POST);
            } else {
                error_log("⚠️ NO se procesan documentos relacionados. Success: " . ($resultado['success'] ? 'true' : 'false') . " | Type: $documentType");
            }

            if ($resultado['success']) {
                error_log("✅ Guardado exitoso, redirigiendo...");
                // Agregar orden_id en la URL para forzar recarga de datos actualizados
                header("Location: /digitalizacion-documentos/documents/show?id=$documentType&orden_id=$ordenId&success=documento_guardado");
                exit;
            } else {
                error_log("❌ Error al guardar: " . ($resultado['error'] ?? 'desconocido'));
                header("Location: /digitalizacion-documentos/documents/show?id=$documentType&orden_id=$ordenId&error=" . urlencode($resultado['error']));
                exit;
            }
        }
    }

    /**
     * Procesar documentos relacionados seleccionados
     */
    private function procesarDocumentosRelacionados($ordenId, $postData) {
        // Obtener documentos seleccionados
        $documentosSeleccionados = $postData['generar_documento'] ?? [];
        
        error_log("📄 Documentos seleccionados: " . print_r($documentosSeleccionados, true));
        
        if (empty($documentosSeleccionados)) {
            error_log("⚠️ No hay documentos seleccionados");
            return; // No hay documentos seleccionados
        }

        // Obtener datos de la orden para copiar a los documentos
        $ordenCompra = $this->documentModel->getOrdenCompra($ordenId);
        
        if (!$ordenCompra) {
            error_log("❌ No se pudo obtener la orden de compra ID: $ordenId");
            return;
        }

        error_log("✅ Orden de compra obtenida. Procesando " . count($documentosSeleccionados) . " documentos");

        // Procesar cada documento seleccionado
        foreach ($documentosSeleccionados as $documentoId) {
            error_log("🔍 Verificando documento: $documentoId");
            
            // Verificar si ya existe
            $existe = $this->documentModel->getDocumentData($documentoId, $ordenId);
            
            if (!$existe) {
                error_log("➕ Generando documento nuevo: $documentoId");
                // Generar documento con datos de la orden
                $resultado = $this->generarDocumentoAutomatico($documentoId, $ordenId, $ordenCompra);
                error_log($resultado ? "✅ Documento $documentoId generado" : "❌ Error al generar $documentoId");
            } else {
                error_log("✓ Documento $documentoId ya existe, no se genera");
            }
        }
    }

    /**
     * Generar documento automáticamente con datos de la orden
     */
    private function generarDocumentoAutomatico($documentoId, $ordenId, $ordenCompra) {
        error_log("🔧 Generando documento: $documentoId para orden: $ordenId");
        
        // Preparar datos según el tipo de documento (campos EXACTOS del schema)
        $datosDocumento = ['document_type' => $documentoId];
        
        // Obtener nombre completo del cliente
        $nombreCompleto = trim(($ordenCompra['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompra['OC_COMPRADOR_APELLIDO'] ?? ''));
        if (empty($nombreCompleto)) {
            $nombreCompleto = $ordenCompra['OC_CLIENTE_NOMBRE'] ?? '';
        }
        
        // 🖊️ FIRMA DEL CLIENTE - Reutilizar de la orden de compra
        $firmaCliente = $ordenCompra['OC_CLIENTE_FIRMA'] ?? '';
        if (!empty($firmaCliente)) {
            error_log("✍️ Reutilizando firma del cliente: $firmaCliente");
        }
        
        switch ($documentoId) {
            case 'carta_conocimiento_aceptacion':
                $datosDocumento['CCA_CLIENTE_NOMBRE_COMPLETO'] = $nombreCompleto;
                $datosDocumento['CCA_CLIENTE_DOCUMENTO'] = $ordenCompra['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? $ordenCompra['OC_CLIENTE_DNI'] ?? '';
                $datosDocumento['CCA_VEHICULO_MARCA'] = $ordenCompra['OC_VEHICULO_MARCA'] ?? '';
                $datosDocumento['CCA_VEHICULO_MODELO'] = $ordenCompra['OC_VEHICULO_MODELO'] ?? '';
                $datosDocumento['CCA_VEHICULO_ANIO'] = $ordenCompra['OC_VEHICULO_ANIO_MODELO'] ?? '';
                $datosDocumento['CCA_VEHICULO_VIN'] = $ordenCompra['OC_VEHICULO_CHASIS'] ?? '';
                $datosDocumento['CCA_NOMBRE_FIRMA'] = $nombreCompleto;
                $datosDocumento['CCA_DOCUMENTO_FIRMA'] = $ordenCompra['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? '';
                $datosDocumento['CCA_FECHA_FIRMA'] = date('Y-m-d');
                $datosDocumento['CCA_FIRMA_CLIENTE'] = $firmaCliente; // 🖊️ Reutilizar firma
                break;
                
            case 'carta_recepcion':
                $datosDocumento['CR_FECHA_DIA'] = date('d');
                $datosDocumento['CR_FECHA_MES'] = date('m');
                $datosDocumento['CR_FECHA_ANIO'] = date('Y');
                $datosDocumento['CR_CLIENTE_NOMBRE'] = $nombreCompleto;
                $datosDocumento['CR_CLIENTE_DNI'] = $ordenCompra['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? '';
                $datosDocumento['CR_VEHICULO_MARCA'] = $ordenCompra['OC_VEHICULO_MARCA'] ?? '';
                $datosDocumento['CR_VEHICULO_MODELO'] = $ordenCompra['OC_VEHICULO_MODELO'] ?? '';
                $datosDocumento['CR_FIRMA_CLIENTE'] = $firmaCliente; // 🖊️ Reutilizar firma
                break;
                
            case 'carta-caracteristicas':
                $datosDocumento['CC_FECHA_CARTA'] = date('d/m/Y');
                $datosDocumento['CC_EMPRESA_DESTINO'] = $ordenCompra['OC_BANCO_ABONO'] ?? '';
                $datosDocumento['CC_CLIENTE_NOMBRE'] = $nombreCompleto;
                $datosDocumento['CC_CLIENTE_DNI'] = $ordenCompra['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? '';
                $datosDocumento['CC_VEHICULO_MARCA'] = $ordenCompra['OC_VEHICULO_MARCA'] ?? '';
                $datosDocumento['CC_VEHICULO_MODELO'] = $ordenCompra['OC_VEHICULO_MODELO'] ?? '';
                $datosDocumento['CC_VEHICULO_ANIO_MODELO'] = $ordenCompra['OC_VEHICULO_ANIO_MODELO'] ?? '';
                $datosDocumento['CC_VEHICULO_CHASIS'] = $ordenCompra['OC_VEHICULO_CHASIS'] ?? '';
                break;
                
            case 'carta_caracteristicas_banbif':
                $datosDocumento['CCB_FECHA_CARTA'] = date('Y-m-d');
                $datosDocumento['CCB_CLIENTE_NOMBRE'] = $nombreCompleto;
                $datosDocumento['CCB_VEHICULO_MARCA'] = $ordenCompra['OC_VEHICULO_MARCA'] ?? '';
                $datosDocumento['CCB_VEHICULO_MODELO'] = $ordenCompra['OC_VEHICULO_MODELO'] ?? '';
                $datosDocumento['CCB_VEHICULO_ANIO_MODELO'] = $ordenCompra['OC_VEHICULO_ANIO_MODELO'] ?? '';
                $datosDocumento['CCB_VEHICULO_CHASIS'] = $ordenCompra['OC_VEHICULO_CHASIS'] ?? '';
                break;
                
            case 'carta_felicitaciones':
                $datosDocumento['CF_CLIENTE_NOMBRE'] = $nombreCompleto;
                $datosDocumento['CF_VEHICULO_MARCA'] = $ordenCompra['OC_VEHICULO_MARCA'] ?? '';
                $datosDocumento['CF_VEHICULO_MODELO'] = $ordenCompra['OC_VEHICULO_MODELO'] ?? '';
                $datosDocumento['CF_VEHICULO_VERSION'] = $ordenCompra['OC_VEHICULO_VERSION'] ?? '';
                $datosDocumento['CF_ASESOR_NOMBRE'] = $ordenCompra['OC_ASESOR_VENTA'] ?? '';
                $datosDocumento['CF_ASESOR_CELULAR'] = $ordenCompra['OC_ASESOR_CELULAR'] ?? '';
                $datosDocumento['CF_APLICACION_NOMBRE'] = 'NOMBRE DE APLICACIÓN - SI APLICA';
                break;
                
            case 'carta_obsequios':
                $datosDocumento['CO_CLIENTE_NOMBRE'] = $nombreCompleto;
                $datosDocumento['CO_VEHICULO_MARCA'] = $ordenCompra['OC_VEHICULO_MARCA'] ?? '';
                break;
                
            case 'politica_proteccion_datos':
                $datosDocumento['PPD_CLIENTE_NOMBRE'] = $nombreCompleto;
                $datosDocumento['PPD_CLIENTE_DNI'] = $ordenCompra['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? '';
                $datosDocumento['PPD_FECHA_AUTORIZACION'] = date('Y-m-d');
                $datosDocumento['PPD_FIRMA_CLIENTE'] = $firmaCliente; // 🖊️ Reutilizar firma
                break;
                
            case 'acta-conocimiento-conformidad':
                // Convertir fecha si es DateTime
                $fechaVenta = $ordenCompra['OC_FECHA_ORDEN'] ?? date('Y-m-d');
                if ($fechaVenta instanceof DateTime) {
                    $fechaVenta = $fechaVenta->format('Y-m-d');
                }
                
                $datosDocumento['ACC_FECHA_ACTA'] = date('Y-m-d');
                $datosDocumento['ACC_NOMBRE_CLIENTE'] = $nombreCompleto;
                $datosDocumento['ACC_DNI_CLIENTE'] = $ordenCompra['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? '';
                $datosDocumento['ACC_EMPRESA_INSTALADORA'] = 'Interamericana Norte SAC';
                $datosDocumento['ACC_BOLETA_FACTURA_NUMERO'] = $ordenCompra['OC_ID'] ?? '';
                $datosDocumento['ACC_CLIENTE_VEHICULO'] = $nombreCompleto;
                $datosDocumento['ACC_FECHA_VENTA'] = $fechaVenta;
                $datosDocumento['ACC_MARCA_VEHICULO'] = $ordenCompra['OC_VEHICULO_MARCA'] ?? '';
                $datosDocumento['ACC_MODELO_VEHICULO'] = $ordenCompra['OC_VEHICULO_MODELO'] ?? '';
                $datosDocumento['ACC_ANIO_VEHICULO'] = $ordenCompra['OC_VEHICULO_ANIO_MODELO'] ?? '';
                $datosDocumento['ACC_VIN_VEHICULO'] = $ordenCompra['OC_VEHICULO_CHASIS'] ?? '';
                $datosDocumento['ACC_COLOR_VEHICULO'] = $ordenCompra['OC_VEHICULO_COLOR'] ?? '';
                $datosDocumento['ACC_FIRMA_CLIENTE'] = $firmaCliente; // 🖊️ Reutilizar firma
                $datosDocumento['ACC_NOMBRE_FIRMA'] = $nombreCompleto;
                $datosDocumento['ACC_DNI_FIRMA'] = $ordenCompra['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? '';
                break;
                
            case 'actorizacion-datos-personales':
                $datosDocumento['ADP_NOMBRE_AUTORIZACION'] = $nombreCompleto;
                $datosDocumento['ADP_DNI_AUTORIZACION'] = $ordenCompra['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? '';
                $datosDocumento['ADP_FECHA_AUTORIZACION'] = date('Y-m-d');
                $datosDocumento['ADP_FIRMA_CLIENTE'] = $firmaCliente; // 🖊️ Reutilizar firma
                break;
                
            default:
                error_log("❌ Documento desconocido: $documentoId");
                return false;
        }
        
        error_log("📦 Datos preparados para $documentoId: " . json_encode(array_keys($datosDocumento)));

        // Guardar el documento
        try {
            $resultado = $this->documentModel->guardarDocumentoIndividual($documentoId, $datosDocumento, $ordenId);
            return $resultado['success'] ?? false;
        } catch (Exception $e) {
            error_log("❌ Error al generar documento automático $documentoId: " . $e->getMessage());
            return false;
        }
    }

    // Imprimir documentos desde panel de aprobación (redirige a ExpedienteController)
    public function imprimir() {
        if (!isset($_GET['id'])) {
            header("Location: /digitalizacion-documentos/aprobacion/panel");
            exit;
        }

        $ordenId = (int)$_GET['id'];

        // Obtener la orden de compra
        $ordenCompra = $this->documentModel->getOrdenCompra($ordenId);

        if (!$ordenCompra) {
            header("Location: /digitalizacion-documentos/aprobacion/panel?error=" . urlencode('Orden no encontrada'));
            exit;
        }

        // Verificar que esté aprobada
        $estadoAprobacion = $ordenCompra['OC_ESTADO_APROBACION'] ?? 'PENDIENTE';
        if ($estadoAprobacion !== 'APROBADO') {
            $mensaje = $estadoAprobacion === 'RECHAZADO'
                ? 'No se puede imprimir. La orden fue RECHAZADA.'
                : 'No se puede imprimir. La orden está PENDIENTE de aprobación.';
            header("Location: /digitalizacion-documentos/aprobacion/panel?id={$ordenId}&error=" . urlencode($mensaje));
            exit;
        }

        // Redirigir a la funcionalidad de impresión usando el número de expediente
        $numeroExpediente = $ordenCompra['OC_NUMERO_EXPEDIENTE'];
        header("Location: /digitalizacion-documentos/expedientes/imprimir-todos?numero=" . urlencode($numeroExpediente));
        exit;
    }

    // Limpiar sesión para generar nueva orden
    public function limpiarSesion() {
        // Limpiar solo las variables relacionadas con la orden
        unset($_SESSION['orden_id']);
        unset($_SESSION['orden_data']);
        unset($_SESSION['firmas']);
        unset($_SESSION['orden_guardada']);
        unset($_SESSION['forma_pago']);
        unset($_SESSION['banco_abono']);
        unset($_SESSION['numero_expediente_temporal']); // Limpiar número temporal
        
        // Retornar respuesta JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    // Guardar firma del cliente como archivo
    public function guardarFirmaCliente() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_POST['firma_base64'])) {
                throw new Exception('No se recibió la firma');
            }
            
            $firmaBase64 = $_POST['firma_base64'];
            
            // Extraer solo los datos de la imagen (quitar el prefijo data:image/png;base64,)
            $firmaData = explode(',', $firmaBase64);
            if (count($firmaData) < 2) {
                throw new Exception('Formato de firma inválido');
            }
            
            $imagenBase64 = $firmaData[1];
            $imagenBinaria = base64_decode($imagenBase64);
            
            if ($imagenBinaria === false) {
                throw new Exception('Error al decodificar la firma');
            }
            
            // Crear carpeta si no existe
            $carpetaFirmas = __DIR__ . '/../../uploads/firmas_cliente';
            if (!file_exists($carpetaFirmas)) {
                mkdir($carpetaFirmas, 0777, true);
            }
            
            // Generar nombre único para el archivo
            $nombreArchivo = 'firma_cliente_' . uniqid() . '_' . time() . '.png';
            $rutaCompleta = $carpetaFirmas . '/' . $nombreArchivo;
            
            // Guardar archivo
            if (file_put_contents($rutaCompleta, $imagenBinaria) === false) {
                throw new Exception('Error al guardar el archivo de firma');
            }
            
            // Retornar la ruta relativa para guardar en BD
            $rutaRelativa = '/digitalizacion-documentos/uploads/firmas_cliente/' . $nombreArchivo;
            
            // 🔄 Sincronizar firma en todos los documentos existentes
            $ordenId = $_SESSION['orden_id'] ?? null;
            if ($ordenId) {
                error_log("🔄 Sincronizando firma del cliente en todos los documentos...");
                $this->sincronizarFirmaCliente($ordenId, $rutaRelativa);
            } else {
                error_log("⚠️ No se encontró orden_id en sesión para sincronizar firma");
            }
            
            echo json_encode([
                'success' => true,
                'ruta' => $rutaRelativa,
                'mensaje' => 'Firma guardada y sincronizada correctamente'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    // Obtener vehículos asignados al asesor logueado
    public function obtenerVehiculosAsesor() {
        // Limpiar cualquier output previo
        if (ob_get_level()) {
            ob_clean();
        }
        
        header('Content-Type: application/json');
        
        try {
            $vehiculos = $this->documentModel->obtenerVehiculosAsesor();
            echo json_encode([
                'success' => true,
                'vehiculos' => $vehiculos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    // Obtener vehículos asignados a un asesor específico por nombre
    public function obtenerVehiculosPorAsesor() {
        // Limpiar cualquier output previo
        if (ob_get_level()) {
            ob_clean();
        }
        
        header('Content-Type: application/json');
        
        try {
            // Obtener el nombre del asesor desde la SESIÓN (usuario logueado)
            $nombreAsesor = $_SESSION['usuario_nombre_completo'] ?? '';
            
            if (empty($nombreAsesor)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Usuario no autenticado o sin nombre en sesión'
                ]);
                exit;
            }
            
            error_log("🔍 DEBUG - Nombre del asesor logueado: " . $nombreAsesor);
            
            $vehiculos = $this->documentModel->obtenerVehiculosPorNombreAsesor($nombreAsesor);
            echo json_encode([
                'success' => true,
                'vehiculos' => $vehiculos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    // Obtener datos del asesor para prellenar orden de compra
    public function obtenerDatosAsesor() {
        header('Content-Type: application/json');
        
        try {
            $datos = $this->documentModel->obtenerDatosAsesorParaOrden();
            echo json_encode($datos);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Sincronizar firma del cliente en todos los documentos existentes
     */
    public function sincronizarFirmaCliente($ordenId, $firmaUrl) {
        try {
            error_log("🔄 Sincronizando firma del cliente en todos los documentos para orden ID: $ordenId");
            
            // Lista de tablas y campos de firma a actualizar
            $tablasFirma = [
                'SIST_CARTA_CONOCIMIENTO_ACEPTACION' => 'CCA_FIRMA_CLIENTE',
                'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD' => 'ACC_FIRMA_CLIENTE', 
                'SIST_CARTA_RECEPCION' => 'CR_FIRMA_CLIENTE',
                'SIST_AUTORIZACION_DATOS_PERSONALES' => 'ADP_FIRMA_CLIENTE',
                'SIST_CARTA_CARACTERISTICAS' => 'CC_FIRMA_CLIENTE',
                'SIST_POLITICA_PROTECCION_DATOS' => 'PPD_FIRMA_CLIENTE'
            ];
            
            $actualizaciones = 0;
            
            foreach ($tablasFirma as $tabla => $campoFirma) {
                // Construir consulta dinámica
                $sql = "UPDATE $tabla SET $campoFirma = ? WHERE ";
                
                // Determinar el campo de ID según la tabla
                switch ($tabla) {
                    case 'SIST_CARTA_CONOCIMIENTO_ACEPTACION':
                        $sql .= "CCA_ORDEN_ID = ?";
                        break;
                    case 'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD':
                        $sql .= "ACC_ORDEN_ID = ?";
                        break;
                    case 'SIST_CARTA_RECEPCION':
                        $sql .= "CR_ORDEN_ID = ?";
                        break;
                    case 'SIST_AUTORIZACION_DATOS_PERSONALES':
                        $sql .= "ADP_ORDEN_ID = ?";
                        break;
                    case 'SIST_CARTA_CARACTERISTICAS':
                        $sql .= "CC_ORDEN_ID = ?";
                        break;
                    case 'SIST_POLITICA_PROTECCION_DATOS':
                        $sql .= "PPD_ORDEN_ID = ?";
                        break;
                }
                
                $params = [$firmaUrl, $ordenId];
                $result = sqlsrv_query($this->conn, $sql, $params);
                
                if ($result) {
                    $filasAfectadas = sqlsrv_rows_affected($result);
                    if ($filasAfectadas > 0) {
                        $actualizaciones++;
                        error_log("✅ Firma sincronizada en $tabla ($filasAfectadas filas)");
                    }
                } else {
                    error_log("❌ Error al sincronizar firma en $tabla: " . print_r(sqlsrv_errors(), true));
                }
            }
            
            error_log("🎯 Sincronización completada: $actualizaciones tablas actualizadas");
            return $actualizaciones > 0;
            
        } catch (Exception $e) {
            error_log("❌ Excepción en sincronizarFirmaCliente: " . $e->getMessage());
            return false;
        }
    }
}
