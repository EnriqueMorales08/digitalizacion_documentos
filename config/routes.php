<?php
session_start();
require_once __DIR__ . '/../app/controllers/DocumentController.php';
require_once __DIR__ . '/../app/controllers/ExpedienteController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AprobacionController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// ==========================
// RUTAS DE AUTENTICACIÓN (SIN PROTECCIÓN)
// ==========================
if ($method === 'GET' && ($uri === '/auth/login' || $uri === '/digitalizacion-documentos/auth/login')) {
    $authController = new AuthController();
    $authController->showLogin();
    exit;
} elseif ($method === 'POST' && ($uri === '/auth/login' || $uri === '/digitalizacion-documentos/auth/login')) {
    $authController = new AuthController();
    $authController->login();
    exit;
} elseif ($method === 'GET' && ($uri === '/auth/logout' || $uri === '/digitalizacion-documentos/auth/logout')) {
    $authController = new AuthController();
    $authController->logout();
    exit;
}

// VERIFICAR SESIÓN PARA TODAS LAS DEMÁS RUTAS
AuthController::verificarSesion();

$controller = new DocumentController();
$expedienteController = new ExpedienteController();

// Rutas de navegación
if ($uri === '/' || $uri === '' 
    || $uri === '/documents' || $uri === '/documents/' 
    || $uri === '/digitalizacion-documentos' || $uri === '/digitalizacion-documentos/' 
    || $uri === '/digitalizacion-documentos/documents' || $uri === '/digitalizacion-documentos/documents/') {
    
    $controller->index();

} elseif (($uri === '/documents/show' || $uri === '/digitalizacion-documentos/documents/show') && isset($_GET['id'])) {
    $controller->show();

} elseif ($uri === '/documents/success' || $uri === '/digitalizacion-documentos/documents/success') {
    require __DIR__ . '/../app/views/documents/success.php';

// ==========================
// RUTAS DE PROCESAMIENTO
// ==========================

// ORDEN DE COMPRA
} elseif ($method === 'POST' && ($uri === '/documents/procesar-orden-compra' || $uri === '/digitalizacion-documentos/documents/procesar-orden-compra')) {
    $controller->procesarOrdenCompra();

// BUSCAR VEHÍCULO
} elseif ($method === 'GET' && ($uri === '/documents/buscar-vehiculo' || $uri === '/digitalizacion-documentos/documents/buscar-vehiculo')) {
    $controller->buscarVehiculo();

// BUSCAR DATOS DE MANTENIMIENTO
} elseif ($method === 'GET' && ($uri === '/documents/buscar-datos-mantenimiento' || $uri === '/digitalizacion-documentos/documents/buscar-datos-mantenimiento')) {
    $controller->buscarDatosMantenimiento();

// OBTENER AGENCIAS
} elseif ($method === 'GET' && ($uri === '/documents/get-agencias' || $uri === '/digitalizacion-documentos/documents/get-agencias')) {
    $controller->getAgencias();

// OBTENER NOMBRES POR AGENCIA
} elseif ($method === 'GET' && ($uri === '/documents/get-nombres-por-agencia' || $uri === '/digitalizacion-documentos/documents/get-nombres-por-agencia')) {
    $controller->getNombresPorAgencia();

// OBTENER CENTROS DE COSTO POR NOMBRE
} elseif ($method === 'GET' && ($uri === '/documents/get-centros-costo-por-nombre' || $uri === '/digitalizacion-documentos/documents/get-centros-costo-por-nombre')) {
    $controller->getCentrosCostoPorNombre();

// ==========================
// RUTAS DE APROBACIÓN
// ==========================

// MOSTRAR PANEL DE APROBACIÓN
} elseif ($method === 'GET' && ($uri === '/aprobacion/panel' || $uri === '/digitalizacion-documentos/aprobacion/panel')) {
    $aprobacionController = new AprobacionController();
    $aprobacionController->panel();

// PROCESAR APROBACIÓN O RECHAZO
} elseif ($method === 'POST' && ($uri === '/aprobacion/procesar' || $uri === '/digitalizacion-documentos/aprobacion/procesar')) {
    $aprobacionController = new AprobacionController();
    $aprobacionController->procesar();

// IMPRIMIR DESDE PANEL DE APROBACIÓN (por ID)
} elseif ($method === 'GET' && ($uri === '/documents/imprimir' || $uri === '/digitalizacion-documentos/documents/imprimir')) {
    $controller->imprimir();

// VERIFICAR FIRMA
} elseif ($method === 'POST' && ($uri === '/documents/verificar-firma' || $uri === '/digitalizacion-documentos/documents/verificar-firma')) {
    $controller->verificarFirma();

// LIMPIAR SESIÓN (para nueva orden)
} elseif ($method === 'POST' && ($uri === '/documents/limpiar-sesion' || $uri === '/digitalizacion-documentos/documents/limpiar-sesion')) {
    $controller->limpiarSesion();

// GUARDAR DOCUMENTO INDIVIDUAL
} elseif ($method === 'POST' && ($uri === '/documents/guardar-documento' || $uri === '/digitalizacion-documentos/documents/guardar-documento')) {
    $controller->guardarDocumento();

// ACTA CONOCIMIENTO CONFORMIDAD
} elseif ($method === 'POST' && ($uri === '/documents/procesar-acta-conformidad' || $uri === '/digitalizacion-documentos/documents/procesar-acta-conformidad')) {
    $controller->procesarActaConformidad();

// AUTORIZACIÓN DATOS PERSONALES
} elseif ($method === 'POST' && ($uri === '/documents/procesar-autorizacion-datos-personales' || $uri === '/digitalizacion-documentos/documents/procesar-autorizacion-datos-personales')) {
    $controller->procesarAutorizacionDatosPersonales();

// CARTA CONOCIMIENTO ACEPTACIÓN
} elseif ($method === 'POST' && ($uri === '/documents/procesar-carta-conocimiento-aceptacion' || $uri === '/digitalizacion-documentos/documents/procesar-carta-conocimiento-aceptacion')) {
    $controller->procesarCartaConocimientoAceptacion();

// CARTA RECEPCIÓN
} elseif ($method === 'POST' && ($uri === '/documents/procesar-carta-recepcion' || $uri === '/digitalizacion-documentos/documents/procesar-carta-recepcion')) {
    $controller->procesarCartaRecepcion();

// CARTA CARACTERÍSTICAS
} elseif ($method === 'POST' && ($uri === '/documents/procesar-carta-caracteristicas' || $uri === '/digitalizacion-documentos/documents/procesar-carta-caracteristicas')) {
    $controller->procesarCartaCaracteristicas();

// CARTA FELICITACIONES
} elseif ($method === 'POST' && ($uri === '/documents/procesar-carta-felicitaciones' || $uri === '/digitalizacion-documentos/documents/procesar-carta-felicitaciones')) {
    $controller->procesarCartaFelicitaciones();

// CARTA OBSEQUIOS
} elseif ($method === 'POST' && ($uri === '/documents/procesar-carta-obsequios' || $uri === '/digitalizacion-documentos/documents/procesar-carta-obsequios')) {
    $controller->procesarCartaObsequios();

// POLÍTICA PROTECCIÓN DE DATOS
} elseif ($method === 'POST' && ($uri === '/documents/procesar-politica-proteccion-datos' || $uri === '/digitalizacion-documentos/documents/procesar-politica-proteccion-datos')) {
    $controller->procesarPoliticaProteccionDatos();

// ==========================
// RUTAS DE EXPEDIENTES
// ==========================

// LISTAR EXPEDIENTES
} elseif ($uri === '/expedientes' || $uri === '/expedientes/' || $uri === '/digitalizacion-documentos/expedientes' || $uri === '/digitalizacion-documentos/expedientes/') {
    $expedienteController->index();

// VER EXPEDIENTE
} elseif ($uri === '/expedientes/ver' || $uri === '/digitalizacion-documentos/expedientes/ver') {
    $expedienteController->ver();

// IMPRIMIR TODOS LOS DOCUMENTOS
} elseif ($uri === '/expedientes/imprimir-todos' || $uri === '/digitalizacion-documentos/expedientes/imprimir-todos') {
    $expedienteController->imprimirTodos();

// IMPRIMIR DOCUMENTO INDIVIDUAL
} elseif ($uri === '/expedientes/imprimir-documento' || $uri === '/digitalizacion-documentos/expedientes/imprimir-documento') {
    $expedienteController->imprimirDocumento();

// BUSCAR EXPEDIENTE (API)
} elseif ($uri === '/expedientes/buscar' || $uri === '/digitalizacion-documentos/expedientes/buscar') {
    $expedienteController->buscar();

// Debug API
} elseif ($method === 'GET' && ($uri === '/debug-api' || $uri === '/digitalizacion-documentos/debug-api')) {
    $documentModel = new Document();
    $centros = $documentModel->getCentrosCosto();
    header('Content-Type: application/json');
    echo json_encode($centros);
    exit;
    require_once __DIR__ . '/../debug_correos.php';
    exit;

} elseif ($method === 'GET' && ($uri === '/debug-aprobacion' || $uri === '/digitalizacion-documentos/debug-aprobacion' || $uri === '/debug_aprobacion.php' || $uri === '/digitalizacion-documentos/debug_aprobacion.php')) {
    require_once __DIR__ . '/../debug_aprobacion.php';
    exit;

} elseif ($method === 'GET' && ($uri === '/debug-logs' || $uri === '/digitalizacion-documentos/debug-logs' || $uri === '/debug_logs.php' || $uri === '/digitalizacion-documentos/debug_logs.php')) {
    require_once __DIR__ . '/../debug_logs.php';
    exit;

} elseif ($method === 'GET' && ($uri === '/debug-asesor' || $uri === '/digitalizacion-documentos/debug-asesor' || $uri === '/debug_asesor.php' || $uri === '/digitalizacion-documentos/debug_asesor.php')) {
    require_once __DIR__ . '/../debug_asesor.php';
    exit;

} elseif ($method === 'GET' && ($uri === '/debug-sesion' || $uri === '/digitalizacion-documentos/debug-sesion' || $uri === '/debug_sesion.php' || $uri === '/digitalizacion-documentos/debug_sesion.php')) {
    require_once __DIR__ . '/../debug_sesion.php';
    exit;

} elseif ($method === 'GET' && ($uri === '/debug-centros' || $uri === '/digitalizacion-documentos/debug-centros' || $uri === '/debug_centros.php' || $uri === '/digitalizacion-documentos/debug_centros.php')) {
    require_once __DIR__ . '/../debug_centros.php';
    exit;

// ==========================
// 404
// ==========================
} else {
    http_response_code(404);
    echo "<h1 style='text-align: center; margin-top: 50px;'>Página no encontrada</h1>";
    echo "<p style='text-align: center;'><a href='/digitalizacion-documentos/documents'>← Volver al panel</a></p>";
    exit;
}
