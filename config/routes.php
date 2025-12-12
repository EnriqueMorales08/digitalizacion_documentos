<?php
ob_start(); // Iniciar buffer de salida para evitar output antes de JSON
session_start();
require_once __DIR__ . '/../app/controllers/DocumentController.php';
require_once __DIR__ . '/../app/controllers/ExpedienteController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AprobacionController.php';
require_once __DIR__ . '/../app/controllers/AuditController.php';
require_once __DIR__ . '/../app/controllers/ConfirmacionController.php';
require_once __DIR__ . '/../app/controllers/CajeraController.php';

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

// ==========================
// RUTAS DE RECUPERACIÓN DE CONTRASEÑA (SIN PROTECCIÓN)
// ==========================
// Mostrar formulario de recuperación
if ($method === 'GET' && ($uri === '/auth/forgot-password' || $uri === '/digitalizacion-documentos/auth/forgot-password')) {
    $authController = new AuthController();
    $authController->showForgotPassword();
    exit;
}

// Procesar solicitud de recuperación
if ($method === 'POST' && ($uri === '/auth/request-reset' || $uri === '/digitalizacion-documentos/auth/request-reset')) {
    $authController = new AuthController();
    $authController->requestReset();
    exit;
}

// Mostrar formulario de reseteo de contraseña
if ($method === 'GET' && ($uri === '/auth/show-reset' || $uri === '/digitalizacion-documentos/auth/show-reset')) {
    $authController = new AuthController();
    $authController->showResetPassword();
    exit;
}

// Procesar reseteo de contraseña
if ($method === 'POST' && ($uri === '/auth/reset-password' || $uri === '/digitalizacion-documentos/auth/reset-password')) {
    $authController = new AuthController();
    $authController->resetPassword();
    exit;
}

// ==========================
// RUTAS DE APROBACIÓN CON TOKEN (SIN PROTECCIÓN DE LOGIN)
// ==========================
// Panel de aprobación con token - permite acceso sin login
if ($method === 'GET' && ($uri === '/aprobacion/panel' || $uri === '/digitalizacion-documentos/aprobacion/panel') && isset($_GET['token'])) {
    $aprobacionController = new AprobacionController();
    $aprobacionController->panel();
    exit;
}

// Procesar aprobación con token - permite aprobar/rechazar sin login
if ($method === 'POST' && ($uri === '/aprobacion/procesar' || $uri === '/digitalizacion-documentos/aprobacion/procesar') && isset($_POST['token'])) {
    $aprobacionController = new AprobacionController();
    $aprobacionController->procesar();
    exit;
}

// ==========================
// RUTAS DE CONFIRMACIÓN DE CLIENTE (SIN PROTECCIÓN DE LOGIN)
// ==========================
// Ver documentos y confirmar - permite acceso sin login con token
if ($method === 'GET' && ($uri === '/confirmacion/ver' || $uri === '/digitalizacion-documentos/confirmacion/ver') && isset($_GET['token'])) {
    $confirmacionController = new ConfirmacionController();
    $confirmacionController->ver();
    exit;
}

// Ver TODOS los documentos del expediente para el cliente (misma lógica que imprimir-todos, pero por token)
if ($method === 'GET' && ($uri === '/confirmacion/ver-todos' || $uri === '/digitalizacion-documentos/confirmacion/ver-todos') && isset($_GET['token'])) {
    $confirmacionController = new ConfirmacionController();
    $confirmacionController->verTodos();
    exit;
}

// Procesar respuesta del cliente (aceptar/rechazar) - sin login
if ($method === 'POST' && ($uri === '/confirmacion/responder' || $uri === '/digitalizacion-documentos/confirmacion/responder')) {
    $confirmacionController = new ConfirmacionController();
    $confirmacionController->responder();
    exit;
}

// Guardar firma del cliente desde confirmación (canvas) - sin login
if ($method === 'POST' && ($uri === '/confirmacion/guardar-firma-cliente' || $uri === '/digitalizacion-documentos/confirmacion/guardar-firma-cliente')) {
    $confirmacionController = new ConfirmacionController();
    $confirmacionController->guardarFirmaCliente();
    exit;
}

// Ver orden de compra desde confirmación de cliente - sin login
if ($method === 'GET' && ($uri === '/expedientes/imprimir-documento' || $uri === '/digitalizacion-documentos/expedientes/imprimir-documento') && isset($_GET['cliente']) && $_GET['cliente'] === '1') {
    $expedienteController = new ExpedienteController();
    $expedienteController->imprimirDocumento();
    exit;
}

// ==========================
// RUTAS DE CONFIRMACIÓN DE CAJERA (SIN PROTECCIÓN DE LOGIN)
// ==========================
// Ver documentos y confirmar - permite acceso sin login con token
if ($method === 'GET' && ($uri === '/cajera/ver' || $uri === '/digitalizacion-documentos/cajera/ver') && isset($_GET['token'])) {
    $cajeraController = new CajeraController();
    $cajeraController->ver();
    exit;
}

// Procesar respuesta de cajera (aprobar/rechazar con firma) - sin login
if ($method === 'POST' && ($uri === '/cajera/responder' || $uri === '/digitalizacion-documentos/cajera/responder')) {
    $cajeraController = new CajeraController();
    $cajeraController->responder();
    exit;
}

// Ver documentos desde confirmación de cajera - sin login
if ($method === 'GET' && ($uri === '/documents/show' || $uri === '/digitalizacion-documentos/documents/show') && isset($_GET['cajera']) && $_GET['cajera'] === '1') {
    $controller = new DocumentController();
    $controller->show();
    exit;
}

// ==========================
// VERIFICAR FIRMA (SIN PROTECCIÓN - usado por cajera y clientes)
// ==========================
if ($method === 'POST' && ($uri === '/documents/verificar-firma' || $uri === '/digitalizacion-documentos/documents/verificar-firma')) {
    $controller = new DocumentController();
    $controller->verificarFirma();
    exit;
}

// ==========================
// RUTAS DE SOLICITUD DE VEHÍCULOS (SIN PROTECCIÓN - usan token)
// ==========================

// ACEPTAR SOLICITUD (SIN LOGIN - usa token)
if ($method === 'GET' && ($uri === '/solicitud-vehiculo/aceptar' || $uri === '/digitalizacion-documentos/solicitud-vehiculo/aceptar')) {
    require_once __DIR__ . '/../app/controllers/SolicitudVehiculoController.php';
    $solicitudController = new SolicitudVehiculoController();
    $solicitudController->aceptar();
    exit;
}

// RECHAZAR SOLICITUD (SIN LOGIN - usa token)
if ($method === 'GET' && ($uri === '/solicitud-vehiculo/rechazar' || $uri === '/digitalizacion-documentos/solicitud-vehiculo/rechazar')) {
    require_once __DIR__ . '/../app/controllers/SolicitudVehiculoController.php';
    $solicitudController = new SolicitudVehiculoController();
    $solicitudController->rechazar();
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

// VALIDAR ASIGNACIÓN DE VEHÍCULO
} elseif ($method === 'GET' && ($uri === '/documents/validar-asignacion-vehiculo' || $uri === '/digitalizacion-documentos/documents/validar-asignacion-vehiculo')) {
    $controller->validarAsignacionVehiculo();

// NOTIFICAR INTENTO DE USO DE VEHÍCULO
} elseif ($method === 'POST' && ($uri === '/documents/notificar-intento-uso-vehiculo' || $uri === '/digitalizacion-documentos/documents/notificar-intento-uso-vehiculo')) {
    $controller->notificarIntentoUsoVehiculo();

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

// OBTENER VEHÍCULOS DEL ASESOR
} elseif ($method === 'GET' && ($uri === '/documents/obtenerVehiculosAsesor' || $uri === '/digitalizacion-documentos/documents/obtenerVehiculosAsesor')) {
    $controller->obtenerVehiculosAsesor();

// OBTENER VEHÍCULOS POR NOMBRE DE ASESOR
} elseif ($method === 'POST' && ($uri === '/documents/obtenerVehiculosPorAsesor' || $uri === '/digitalizacion-documentos/documents/obtenerVehiculosPorAsesor')) {
    $controller->obtenerVehiculosPorAsesor();

// OBTENER DATOS DEL ASESOR (AGENCIA, CAJERA, CENTRO DE COSTO)
} elseif ($method === 'GET' && ($uri === '/documents/obtenerDatosAsesor' || $uri === '/digitalizacion-documentos/documents/obtenerDatosAsesor')) {
    $controller->obtenerDatosAsesor();

// ==========================
// RUTAS DE SOLICITUD DE VEHÍCULOS (CON AUTENTICACIÓN)
// ==========================

// SOLICITAR VEHÍCULO LIBRE (requiere login)
} elseif ($method === 'POST' && ($uri === '/solicitud-vehiculo/solicitar-libre' || $uri === '/digitalizacion-documentos/solicitud-vehiculo/solicitar-libre')) {
    require_once __DIR__ . '/../app/controllers/SolicitudVehiculoController.php';
    $solicitudController = new SolicitudVehiculoController();
    $solicitudController->solicitarLibre();

// SOLICITAR REASIGNACIÓN (requiere login)
} elseif ($method === 'POST' && ($uri === '/solicitud-vehiculo/solicitar-reasignacion' || $uri === '/digitalizacion-documentos/solicitud-vehiculo/solicitar-reasignacion')) {
    require_once __DIR__ . '/../app/controllers/SolicitudVehiculoController.php';
    $solicitudController = new SolicitudVehiculoController();
    $solicitudController->solicitarReasignacion();

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

// VERIFICAR ESTADO DE ORDEN (API para polling)
} elseif ($method === 'GET' && ($uri === '/aprobacion/verificar-estado' || $uri === '/digitalizacion-documentos/aprobacion/verificar-estado')) {
    $aprobacionController = new AprobacionController();
    $aprobacionController->verificarEstado();

// IMPRIMIR DESDE PANEL DE APROBACIÓN (por ID)
} elseif ($method === 'GET' && ($uri === '/documents/imprimir' || $uri === '/digitalizacion-documentos/documents/imprimir')) {
    $controller->imprimir();

// LIMPIAR SESIÓN (para nueva orden)
} elseif ($method === 'POST' && ($uri === '/documents/limpiar-sesion' || $uri === '/digitalizacion-documentos/documents/limpiar-sesion')) {
    $controller->limpiarSesion();

// GUARDAR FIRMA DEL CLIENTE
} elseif ($method === 'POST' && ($uri === '/documents/guardar-firma-cliente' || $uri === '/digitalizacion-documentos/documents/guardar-firma-cliente')) {
    $controller->guardarFirmaCliente();

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
// RUTAS DE AUDITORÍA (SOLO ADMIN)
// ==========================

// REPORTE DE AUDITORÍA
} elseif ($method === 'GET' && ($uri === '/audit' || $uri === '/digitalizacion-documentos/audit' || $uri === '/audit/' || $uri === '/digitalizacion-documentos/audit/')) {
    $auditController = new AuditController();
    $auditController->index();

// EXPORTAR AUDITORÍA A CSV
} elseif ($method === 'GET' && ($uri === '/audit/exportar-csv' || $uri === '/digitalizacion-documentos/audit/exportar-csv')) {
    $auditController = new AuditController();
    $auditController->exportarCSV();

// VER DETALLE DE DOCUMENTO (AJAX)
} elseif ($method === 'GET' && ($uri === '/audit/detalle-documento' || $uri === '/digitalizacion-documentos/audit/detalle-documento')) {
    $auditController = new AuditController();
    $auditController->verDetalleDocumento();

// ESTADÍSTICAS DE AUDITORÍA (AJAX)
} elseif ($method === 'GET' && ($uri === '/audit/estadisticas' || $uri === '/digitalizacion-documentos/audit/estadisticas')) {
    $auditController = new AuditController();
    $auditController->estadisticas();

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
} elseif ($uri === '/expedientes/imprimir-todos' || $uri === '/digitalizacion-documentos/expedientes/imprimir-todos' 
           || $uri === '/expedientes/imprimir' || $uri === '/digitalizacion-documentos/expedientes/imprimir') {
    $expedienteController->imprimirTodos();

// IMPRIMIR DOCUMENTO INDIVIDUAL
} elseif ($uri === '/expedientes/imprimir-documento' || $uri === '/digitalizacion-documentos/expedientes/imprimir-documento') {
    $expedienteController->imprimirDocumento();

// BUSCAR EXPEDIENTE (API)
} elseif ($uri === '/expedientes/buscar' || $uri === '/digitalizacion-documentos/expedientes/buscar') {
    $expedienteController->buscar();

// ==========================
// RUTAS DE CONFIRMACIÓN (PROTEGIDAS)
// ==========================

// ENVIAR CORREO AL CLIENTE
} elseif ($method === 'POST' && ($uri === '/confirmacion/enviar-cliente' || $uri === '/digitalizacion-documentos/confirmacion/enviar-cliente')) {
    $confirmacionController = new ConfirmacionController();
    $confirmacionController->enviarCliente();

// ENVIAR CORREO A CAJERA
} elseif ($method === 'POST' && ($uri === '/confirmacion/enviar-cajera' || $uri === '/digitalizacion-documentos/confirmacion/enviar-cajera')) {
    $confirmacionController = new ConfirmacionController();
    $confirmacionController->enviarCajera();

// VERIFICAR ESTADO DE CONFIRMACIÓN
} elseif ($method === 'GET' && ($uri === '/confirmacion/verificar-estado' || $uri === '/digitalizacion-documentos/confirmacion/verificar-estado')) {
    $confirmacionController = new ConfirmacionController();
    $confirmacionController->verificarEstado();

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
