<?php
require_once __DIR__ . '/../app/controllers/DocumentController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$controller = new DocumentController();

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

// ACTA CONOCIMIENTO CONFORMIDAD
} elseif ($method === 'POST' && ($uri === '/documents/procesar-acta-conformidad' || $uri === '/digitalizacion-documentos/documents/procesar-acta-conformidad')) {
    $controller->procesarActaConformidad();

// AUTORIZACIÓN DATOS PERSONALES
} elseif ($method === 'POST' && ($uri === '/documents/procesar-actorizacion-datos-personales' || $uri === '/digitalizacion-documentos/documents/procesar-actorizacion-datos-personales')) {
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
// 404
// ==========================
} else {
    http_response_code(404);
    echo "<h1 style='text-align: center; margin-top: 50px;'>Página no encontrada</h1>";
    echo "<p style='text-align: center;'><a href='/digitalizacion-documentos/documents'>← Volver al panel</a></p>";
    exit;
}
