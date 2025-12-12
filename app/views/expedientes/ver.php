<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente <?= htmlspecialchars($ordenCompra['OC_NUMERO_EXPEDIENTE'] ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .expediente-info {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .documento-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        .documento-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        .documento-disponible {
            border-left: 5px solid #10b981;
        }
        .documento-no-disponible {
            border-left: 5px solid #ef4444;
            opacity: 0.6;
        }
        .galeria-archivos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .archivo-item {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        .archivo-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .archivo-item.no-disponible {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .archivo-item.no-disponible:hover {
            transform: none;
        }
        .archivo-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }
        .archivo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .archivo-info {
            padding: 15px;
            text-align: center;
        }
        .archivo-info h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
        .archivo-info .badge {
            margin-top: 8px;
            font-size: 11px;
        }
        .archivo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .archivo-item:hover .archivo-overlay {
            opacity: 1;
        }
        .archivo-overlay-text {
            color: white;
            font-size: 14px;
            text-align: center;
            padding: 10px;
        }
        .modal-imagen {
            max-width: 90vw;
            max-height: 90vh;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-2"><i class="bi bi-folder-open"></i> Expediente</h1>
                </div>
                <div>
                    <a href="/digitalizacion-documentos/expedientes" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <button onclick="imprimirTodos('<?= urlencode($ordenCompra['OC_NUMERO_EXPEDIENTE']) ?>')" 
                            class="btn btn-success">
                        <i class="bi bi-printer"></i> Imprimir Todos
                    </button>
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="expediente-info">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="bi bi-person-circle"></i> Información del Cliente</h5>
                        <p class="mb-1"><strong>Nombre:</strong> <?= htmlspecialchars($ordenCompra['OC_COMPRADOR_NOMBRE'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong>Documento:</strong> <?= htmlspecialchars($ordenCompra['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? 'N/A') ?></p>
                        <p class="mb-0"><strong>Fecha Orden:</strong> 
                            <?php 
                            if (isset($ordenCompra['OC_FECHA_ORDEN']) && $ordenCompra['OC_FECHA_ORDEN'] instanceof DateTime) {
                                echo $ordenCompra['OC_FECHA_ORDEN']->format('d/m/Y');
                            }
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="bi bi-car-front-fill"></i> Información del Vehículo</h5>
                        <p class="mb-1"><strong>Marca:</strong> <?= htmlspecialchars($ordenCompra['OC_VEHICULO_MARCA'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong>Modelo:</strong> <?= htmlspecialchars($ordenCompra['OC_VEHICULO_MODELO'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong>Chasis:</strong> <?= htmlspecialchars($ordenCompra['OC_VEHICULO_CHASIS'] ?? 'N/A') ?></p>
                        <p class="mb-0">
                            <?php 
                            $estado = $ordenCompra['OC_ESTADO_APROBACION'] ?? 'PENDIENTE';
                            $badgeClass = $estado === 'APROBADO' ? 'bg-success' : ($estado === 'RECHAZADO' ? 'bg-danger' : 'bg-warning text-dark');
                            $icono = $estado === 'APROBADO' ? 'check-circle' : ($estado === 'RECHAZADO' ? 'x-circle' : 'clock');
                            ?>
                            <strong>Estado:</strong> 
                            <span class="badge <?= $badgeClass ?>">
                                <i class="bi bi-<?= $icono ?>"></i> <?= $estado ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Documentos -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="bi bi-files"></i> Documentos del Expediente</h4>
                <div>
                    <button id="btnEnviarCliente" class="btn btn-primary btn-sm me-2" onclick="enviarCorreoCliente()">
                        <i class="bi bi-envelope"></i> Enviar a Cliente
                    </button>
                    <button id="btnEnviarCajera" class="btn btn-success btn-sm" onclick="enviarCorreoCajera()" disabled>
                        <i class="bi bi-cash-coin"></i> Enviar a Cajera
                    </button>
                </div>
            </div>

            <?php 
            // Determinar qué carta de características mostrar según el banco
            $formaPago = trim($ordenCompra['OC_FORMA_PAGO'] ?? '');
            $bancoAbono = trim($ordenCompra['OC_BANCO_ABONO'] ?? '');
            $tipoCombustible = trim($ordenCompra['OC_VEHICULO_TIPO_COMBUSTIBLE'] ?? '');
            
            // ========== GRUPO 1: DOCUMENTOS DE VENTA ==========
            $documentosVenta = [];
            
            // Orden de Compra siempre va primero en documentos de venta
            $documentosVenta['orden-compra'] = 'Orden de Compra';
            
            // Solo agregar carta de características si la forma de pago es CRÉDITO
            if ($formaPago === 'CRÉDITO') {
                if ($bancoAbono === 'Banco Interamericano de Finanzas') {
                    $documentosVenta['carta_caracteristicas_banbif'] = 'Carta Características Banbif';
                } else {
                    $documentosVenta['carta-caracteristicas'] = 'Carta Características';
                }
            }
            
            $documentosVenta['actorizacion-datos-personales'] = 'Autorización de Uso de Imagen';
            $documentosVenta['politica_proteccion_datos'] = 'Política Protección Datos';
            $documentosVenta['carta_conocimiento_aceptacion'] = 'Carta Conocimiento Aceptación';
            
            // Solo agregar Acta de Conocimiento y Conformidad si el vehículo es GLP (tipo combustible = 'DU')
            if ($tipoCombustible === 'DU') {
                $documentosVenta['acta-conocimiento-conformidad'] = 'Acta Conocimiento Conformidad GLP';
            }
            
            // ========== GRUPO 2: DOCUMENTOS DE ENTREGA ==========
            $documentosEntrega = [
                'carta_recepcion' => 'Carta Recepción',
                'carta_felicitaciones' => 'Carta Felicitaciones'
            ];
            
            // ========== GRUPO 3: CARTA DE OBSEQUIOS ==========
            $documentosObsequios = [
                'carta_obsequios' => 'Carta Obsequios'
            ];
            
            $documentosExistentes = [];
            foreach ($documentos as $doc) {
                $documentosExistentes[] = $doc['nombre'];
            }
            ?>

            <!-- GRUPO 1: DOCUMENTOS DE VENTA -->
            <div class="mt-4 mb-3">
                <h5 class="text-primary"><i class="bi bi-cart-check"></i> 📋 DOCUMENTOS DE VENTA</h5>
                <hr>
            </div>

            <?php foreach ($documentosVenta as $docId => $docNombre): ?>
                <?php 
                // La Orden de Compra siempre existe
                if ($docId === 'orden-compra') {
                    $existe = true;
                } else {
                    $existe = false;
                    foreach ($documentos as $doc) {
                        if (stripos($doc['nombre'], str_replace(['_', '-'], ' ', $docNombre)) !== false || 
                            stripos(str_replace(['_', '-'], ' ', $docNombre), $doc['nombre']) !== false) {
                            $existe = true;
                            break;
                        }
                    }
                }
                ?>
                <div class="documento-card <?= $existe ? 'documento-disponible' : 'documento-no-disponible' ?>">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1">
                                <i class="bi bi-file-earmark-<?= $existe ? 'check' : 'x' ?>"></i> 
                                <?= htmlspecialchars($docNombre) ?>
                            </h5>
                            <small class="text-muted">
                                <?php if ($docId === 'orden-compra'): ?>
                                    <span class="badge bg-success">Documento principal</span>
                                <?php else: ?>
                                    <?= $existe ? '<span class="badge bg-success">Disponible</span>' : '<span class="badge bg-danger">No generado</span>' ?>
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php if ($existe): ?>
                                <a href="/digitalizacion-documentos/documents/show?id=<?= $docId ?>&orden_id=<?= $ordenId ?>&modo=ver" 
                                   class="btn btn-primary btn-sm me-2" target="_blank">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <button onclick="imprimirDocumento('<?= urlencode($ordenCompra['OC_NUMERO_EXPEDIENTE']) ?>', '<?= $docId ?>')" 
                                   class="btn btn-success btn-sm">
                                    <i class="bi bi-printer"></i> Imprimir
                                </button>
                            <?php else: ?>
                                <a href="/digitalizacion-documentos/documents/show?id=<?= $docId ?>&orden_id=<?= $ordenId ?>" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Generar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- GRUPO 2: DOCUMENTOS DE ENTREGA -->
            <div class="mt-5 mb-3">
                <h5 class="text-success"><i class="bi bi-truck"></i> 🚚 DOCUMENTOS DE ENTREGA</h5>
                <hr>
            </div>

            <?php foreach ($documentosEntrega as $docId => $docNombre): ?>
                <?php 
                $existe = false;
                foreach ($documentos as $doc) {
                    if (stripos($doc['nombre'], str_replace(['_', '-'], ' ', $docNombre)) !== false || 
                        stripos(str_replace(['_', '-'], ' ', $docNombre), $doc['nombre']) !== false) {
                        $existe = true;
                        break;
                    }
                }
                ?>
                <div class="documento-card <?= $existe ? 'documento-disponible' : 'documento-no-disponible' ?>">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1">
                                <i class="bi bi-file-earmark-<?= $existe ? 'check' : 'x' ?>"></i> 
                                <?= htmlspecialchars($docNombre) ?>
                            </h5>
                            <small class="text-muted">
                                <?= $existe ? '<span class="badge bg-success">Disponible</span>' : '<span class="badge bg-danger">No generado</span>' ?>
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php if ($existe): ?>
                                <a href="/digitalizacion-documentos/documents/show?id=<?= $docId ?>&orden_id=<?= $ordenId ?>&modo=ver" 
                                   class="btn btn-primary btn-sm me-2" target="_blank">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <button onclick="imprimirDocumento('<?= urlencode($ordenCompra['OC_NUMERO_EXPEDIENTE']) ?>', '<?= $docId ?>')" 
                                   class="btn btn-success btn-sm">
                                    <i class="bi bi-printer"></i> Imprimir
                                </button>
                            <?php else: ?>
                                <a href="/digitalizacion-documentos/documents/show?id=<?= $docId ?>&orden_id=<?= $ordenId ?>" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Generar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- GRUPO 3: CARTA DE OBSEQUIOS -->
            <div class="mt-5 mb-3">
                <h5 class="text-warning"><i class="bi bi-gift"></i> 🎁 CARTA DE OBSEQUIOS</h5>
                <hr>
            </div>

            <?php foreach ($documentosObsequios as $docId => $docNombre): ?>
                <?php 
                $existe = false;
                foreach ($documentos as $doc) {
                    if (stripos($doc['nombre'], str_replace(['_', '-'], ' ', $docNombre)) !== false || 
                        stripos(str_replace(['_', '-'], ' ', $docNombre), $doc['nombre']) !== false) {
                        $existe = true;
                        break;
                    }
                }
                ?>
                <div class="documento-card <?= $existe ? 'documento-disponible' : 'documento-no-disponible' ?>">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1">
                                <i class="bi bi-file-earmark-<?= $existe ? 'check' : 'x' ?>"></i> 
                                <?= htmlspecialchars($docNombre) ?>
                            </h5>
                            <small class="text-muted">
                                <?= $existe ? '<span class="badge bg-success">Disponible</span>' : '<span class="badge bg-danger">No generado</span>' ?>
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php if ($existe): ?>
                                <a href="/digitalizacion-documentos/documents/show?id=<?= $docId ?>&orden_id=<?= $ordenId ?>&modo=ver" 
                                   class="btn btn-primary btn-sm me-2" target="_blank">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <button onclick="imprimirDocumento('<?= urlencode($ordenCompra['OC_NUMERO_EXPEDIENTE']) ?>', '<?= $docId ?>')" 
                                   class="btn btn-success btn-sm">
                                    <i class="bi bi-printer"></i> Imprimir
                                </button>
                            <?php else: ?>
                                <a href="/digitalizacion-documentos/documents/show?id=<?= $docId ?>&orden_id=<?= $ordenId ?>" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Generar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- SECCIÓN DE ARCHIVOS ADJUNTOS - GALERÍA VISUAL -->
            <div class="mt-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0"><i class="bi bi-images"></i> Galería de Archivos Adjuntos</h4>
                    <button type="button" class="btn btn-primary" onclick="imprimirArchivosAdjuntos()">
                        <i class="bi bi-printer"></i> Imprimir Archivos Adjuntos
                    </button>
                </div>
                <p class="text-muted">Haz click en una imagen para verla en grande. Click derecho para descargar.</p>
                
                <?php
                // Array de archivos a verificar
                $archivosAdjuntos = [
                    'OC_ARCHIVO_DNI' => ['nombre' => 'DNI del Cliente', 'icono' => 'person-badge', 'color' => '#3b82f6'],
                    'OC_CONFIRMACION_SANTANDER' => ['nombre' => 'Confirmación Santander', 'icono' => 'bank', 'color' => '#ef4444'],
                    'OC_ARCHIVO_PEDIDO_SALESFORCE' => ['nombre' => 'Pedido Salesforce', 'icono' => 'file-earmark-code', 'color' => '#8b5cf6'],
                ];
                
                // Agregar abonos (1-7)
                for ($i = 1; $i <= 7; $i++) {
                    $archivosAdjuntos['OC_ARCHIVO_ABONO' . $i] = [
                        'nombre' => 'Abono ' . $i,
                        'icono' => 'cash-coin',
                        'color' => '#f59e0b'
                    ];
                }
                
                // Agregar otros documentos (1-6)
                for ($i = 1; $i <= 6; $i++) {
                    $archivosAdjuntos['OC_ARCHIVO_OTROS_' . $i] = [
                        'nombre' => 'Otro Documento ' . $i,
                        'icono' => 'file-earmark',
                        'color' => '#6366f1'
                    ];
                }
                
                $hayArchivos = false;
                foreach ($archivosAdjuntos as $campo => $info) {
                    if (!empty($ordenCompra[$campo])) {
                        $hayArchivos = true;
                        break;
                    }
                }
                ?>
                
                <?php if ($hayArchivos): ?>
                    <div class="galeria-archivos">
                        <?php foreach ($archivosAdjuntos as $campo => $info): ?>
                            <?php if (!empty($ordenCompra[$campo])): ?>
                                <?php
                                // Verificar si el archivo existe físicamente
                                $rutaArchivo = $ordenCompra[$campo];
                                $archivoExiste = false;
                                $extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));
                                $esImagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                                
                                // Convertir ruta web a ruta física
                                if (strpos($rutaArchivo, '/digitalizacion-documentos/uploads/') === 0) {
                                    $rutaFisica = __DIR__ . '/../../../uploads/' . basename($rutaArchivo);
                                    $archivoExiste = file_exists($rutaFisica);
                                }
                                ?>
                                <div class="archivo-item <?= $archivoExiste ? '' : 'no-disponible' ?>" 
                                     <?php if ($archivoExiste): ?>
                                     onclick="abrirModal('<?= htmlspecialchars($rutaArchivo, ENT_QUOTES) ?>', '<?= htmlspecialchars($info['nombre'], ENT_QUOTES) ?>')"
                                     oncontextmenu="descargarArchivo(event, '<?= htmlspecialchars($rutaArchivo, ENT_QUOTES) ?>', '<?= htmlspecialchars($info['nombre'], ENT_QUOTES) ?>')"
                                     <?php endif; ?>>
                                     
                                    <div class="archivo-preview" style="background: linear-gradient(135deg, <?= $info['color'] ?> 0%, <?= $info['color'] ?>dd 100%);">
                                        <?php if ($archivoExiste && $esImagen): ?>
                                            <img src="<?= htmlspecialchars($rutaArchivo) ?>" alt="<?= htmlspecialchars($info['nombre']) ?>" loading="lazy">
                                        <?php else: ?>
                                            <i class="bi bi-<?= $info['icono'] ?>"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="archivo-info">
                                        <h6><?= htmlspecialchars($info['nombre']) ?></h6>
                                        <?php if ($archivoExiste): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Disponible</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> No disponible</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($archivoExiste): ?>
                                    <div class="archivo-overlay">
                                        <div class="archivo-overlay-text">
                                            <i class="bi bi-zoom-in" style="font-size: 32px;"></i><br>
                                            <small>Click para ver<br>Click derecho para descargar</small>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No se han adjuntado archivos adicionales en esta orden de compra.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para ver imagen en grande -->
    <div class="modal fade" id="modalImagen" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImagenTitulo"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImagenSrc" src="" class="modal-imagen" alt="">
                </div>
                <div class="modal-footer">
                    <a id="modalImagenDescargar" href="" download class="btn btn-primary">
                        <i class="bi bi-download"></i> Descargar
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para mostrar indicador de carga
        function mostrarCargando(mensaje = 'Preparando documentos para imprimir...') {
            const overlay = document.createElement('div');
            overlay.id = 'loading-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                backdrop-filter: blur(5px);
            `;
            
            overlay.innerHTML = `
                <div style="background: white; padding: 40px; border-radius: 15px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.3); max-width: 400px;">
                    <div style="margin-bottom: 20px;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                    <h5 style="color: #333; margin-bottom: 10px;">
                        <i class="bi bi-printer"></i> Preparando impresión
                    </h5>
                    <p style="color: #666; margin: 0;" id="loading-message">${mensaje}</p>
                </div>
            `;
            
            document.body.appendChild(overlay);
            return overlay;
        }
        
        // Función para ocultar indicador de carga
        function ocultarCargando() {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                overlay.style.opacity = '0';
                overlay.style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    if (overlay.parentNode) {
                        overlay.parentNode.removeChild(overlay);
                    }
                }, 300);
            }
        }
        
        function imprimirTodos(numeroExpediente) {
            // Mostrar indicador de carga
            const loading = mostrarCargando('Cargando todos los documentos del expediente...');
            // Crear iframe oculto para cargar los documentos
            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = 'none';
            
            document.body.appendChild(iframe);
            
            // Cargar la página de imprimir todos en el iframe
            iframe.src = '/digitalizacion-documentos/expedientes/imprimir-todos?numero=' + encodeURIComponent(numeroExpediente);
            
            // Esperar a que cargue y luego imprimir
            iframe.onload = function() {
                setTimeout(() => {
                    try {
                        // Ocultar indicador de carga antes de mostrar diálogo de impresión
                        ocultarCargando();
                        
                        // Pequeña pausa para que se vea la transición
                        setTimeout(() => {
                            iframe.contentWindow.print();
                        }, 300);
                        
                        // Limpiar iframe después de cerrar el diálogo de impresión
                        setTimeout(() => {
                            if (document.body.contains(iframe)) {
                                document.body.removeChild(iframe);
                            }
                        }, 2000);
                    } catch (e) {
                        console.error('Error al imprimir:', e);
                        ocultarCargando();
                        // Si falla, abrir en nueva ventana como fallback
                        window.open('/digitalizacion-documentos/expedientes/imprimir-todos?numero=' + encodeURIComponent(numeroExpediente), '_blank');
                        document.body.removeChild(iframe);
                    }
                }, 5000); // Esperar 6 segundos para que carguen todos los documentos
            };
        }
        
        function imprimirDocumento(numeroExpediente, documentoId) {
            // Mostrar indicador de carga
            const loading = mostrarCargando('Preparando documento para imprimir...');
            
            // Crear iframe oculto para cargar el documento
            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = 'none';
            
            document.body.appendChild(iframe);
            
            const url = `/digitalizacion-documentos/expedientes/imprimir-documento?numero=${numeroExpediente}&documento=${documentoId}`;
            iframe.src = url;
            
            iframe.onload = function() {
                setTimeout(() => {
                    try {
                        // Ocultar indicador de carga antes de mostrar diálogo de impresión
                        ocultarCargando();
                        
                        // Pequeña pausa para que se vea la transición
                        setTimeout(() => {
                            iframe.contentWindow.print();
                        }, 300);
                        
                        // Limpiar iframe después de cerrar el diálogo de impresión
                        setTimeout(() => {
                            if (document.body.contains(iframe)) {
                                document.body.removeChild(iframe);
                            }
                        }, 2000);
                    } catch (e) {
                        console.error('Error al imprimir:', e);
                        ocultarCargando();
                        // Si falla, abrir en nueva ventana como fallback
                        window.open(url, '_blank');
                        document.body.removeChild(iframe);
                    }
                }, 3000); // Esperar 4 segundos para selectores y abonos
            };
        }
        
        // Función para abrir modal con imagen
        function abrirModal(rutaArchivo, nombreArchivo) {
            document.getElementById('modalImagenTitulo').textContent = nombreArchivo;
            document.getElementById('modalImagenSrc').src = rutaArchivo;
            document.getElementById('modalImagenDescargar').href = rutaArchivo;
            document.getElementById('modalImagenDescargar').download = nombreArchivo;
            
            const modal = new bootstrap.Modal(document.getElementById('modalImagen'));
            modal.show();
        }
        
        // Función para descargar archivo con click derecho
        function descargarArchivo(event, rutaArchivo, nombreArchivo) {
            event.preventDefault();
            
            // Crear elemento temporal para descarga
            const link = document.createElement('a');
            link.href = rutaArchivo;
            link.download = nombreArchivo;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            return false;
        }

        // Función para imprimir todos los archivos adjuntos
        function imprimirArchivosAdjuntos() {
            // Obtener todos los archivos disponibles
            const archivos = document.querySelectorAll('.archivo-item:not(.no-disponible)');
            
            if (archivos.length === 0) {
                alert('No hay archivos disponibles para imprimir.');
                return;
            }
            
            // Crear ventana de impresión
            const ventanaImpresion = window.open('', '_blank', 'width=1200,height=800');
            
            // Construir HTML para impresión
            let html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Archivos Adjuntos - Orden #<?= htmlspecialchars($numeroExpediente) ?></title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { 
                            margin: 0;
                            padding: 0;
                            background: #fff;
                        }
                        .archivo-page {
                            width: 100vw;
                            height: 100vh;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            page-break-after: always;
                        }
                        .archivo-page:last-child {
                            page-break-after: auto;
                        }
                        .archivo-page img {
                            max-width: 100%;
                            max-height: 100%;
                            object-fit: contain;
                        }
                        @media print {
                            .archivo-page {
                                width: 100%;
                                height: 100vh;
                                page-break-after: always;
                            }
                            .archivo-page:last-child {
                                page-break-after: auto;
                            }
                        }
                    </style>
                </head>
                <body>
            `;
            
            // Agregar cada archivo en su propia página
            archivos.forEach((archivo, index) => {
                const img = archivo.querySelector('img');
                
                if (img) {
                    html += `
                        <div class="archivo-page">
                            <img src="${img.src}" alt="Archivo ${index + 1}">
                        </div>
                    `;
                }
            });
            
            html += `
                </body>
                </html>
            `;
            
            // Escribir HTML en la ventana
            ventanaImpresion.document.write(html);
            ventanaImpresion.document.close();
            
            // Esperar a que las imágenes carguen y luego imprimir
            ventanaImpresion.onload = function() {
                setTimeout(() => {
                    ventanaImpresion.print();
                }, 500);
            };
        }

        // ========================================
        // FUNCIONES DE CONFIRMACIÓN DE CLIENTE
        // ========================================
        
        const numeroExpediente = '<?= htmlspecialchars($numeroExpediente) ?>';
        const emailCajera = '<?= htmlspecialchars($ordenCompra['OC_EMAIL_CENTRO_COSTO'] ?? '') ?>';
        
        // Verificar estado al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            verificarEstadoConfirmacion();
        });
        
        // Verificar si el cliente ya aceptó
        function verificarEstadoConfirmacion() {
            fetch(`/digitalizacion-documentos/confirmacion/verificar-estado?numero_expediente=${numeroExpediente}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const btnEnviarCajera = document.getElementById('btnEnviarCajera');
                        const btnEnviarCliente = document.getElementById('btnEnviarCliente');
                        
                        if (data.cliente_acepto) {
                            // Cliente aceptó - habilitar botón de cajera
                            btnEnviarCajera.disabled = false;
                            btnEnviarCliente.innerHTML = '<i class="bi bi-check-circle"></i> Cliente Confirmó';
                            btnEnviarCliente.classList.remove('btn-primary');
                            btnEnviarCliente.classList.add('btn-success');
                            btnEnviarCliente.disabled = true;
                        }
                        
                        if (data.ya_enviado_cajera) {
                            // Ya se envió a cajera
                            btnEnviarCajera.innerHTML = '<i class="bi bi-check-circle"></i> Enviado a Cajera';
                            btnEnviarCajera.disabled = true;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error al verificar estado:', error);
                });
        }
        
        // Enviar correo al cliente
        function enviarCorreoCliente() {
            if (!confirm('¿Enviar correo de confirmación al cliente?')) {
                return;
            }
            
            const btnEnviarCliente = document.getElementById('btnEnviarCliente');
            btnEnviarCliente.disabled = true;
            btnEnviarCliente.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
            
            fetch('/digitalizacion-documentos/confirmacion/enviar-cliente', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `numero_expediente=${numeroExpediente}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    btnEnviarCliente.innerHTML = '<i class="bi bi-check-circle"></i> Correo Enviado';
                    btnEnviarCliente.classList.remove('btn-primary');
                    btnEnviarCliente.classList.add('btn-success');
                    
                    // Verificar estado después de 2 segundos
                    setTimeout(verificarEstadoConfirmacion, 2000);
                } else {
                    alert('❌ Error: ' + data.error);
                    btnEnviarCliente.disabled = false;
                    btnEnviarCliente.innerHTML = '<i class="bi bi-envelope"></i> Enviar a Cliente';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al enviar correo');
                btnEnviarCliente.disabled = false;
                btnEnviarCliente.innerHTML = '<i class="bi bi-envelope"></i> Enviar a Cliente';
            });
        }
        
        // Enviar correo a cajera
        function enviarCorreoCajera() {
            // Validar que exista email de cajera
            if (!emailCajera || emailCajera.trim() === '') {
                alert('❌ No hay email de cajera registrado en la orden de compra.\nPor favor, edite la orden y agregue el email del centro de costo.');
                return;
            }
            
            if (!confirm('¿Enviar todos los documentos a la cajera (' + emailCajera + ')?')) {
                return;
            }
            
            const btnEnviarCajera = document.getElementById('btnEnviarCajera');
            btnEnviarCajera.disabled = true;
            btnEnviarCajera.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
            
            fetch('/digitalizacion-documentos/confirmacion/enviar-cajera', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `numero_expediente=${encodeURIComponent(numeroExpediente)}&email_cajera=${encodeURIComponent(emailCajera)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    btnEnviarCajera.innerHTML = '<i class="bi bi-check-circle"></i> Enviado a Cajera';
                    btnEnviarCajera.classList.remove('btn-success');
                    btnEnviarCajera.classList.add('btn-secondary');
                } else {
                    alert('❌ Error: ' + data.error);
                    btnEnviarCajera.disabled = false;
                    btnEnviarCajera.innerHTML = '<i class="bi bi-cash-coin"></i> Enviar a Cajera';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al enviar correo');
                btnEnviarCajera.disabled = false;
                btnEnviarCajera.innerHTML = '<i class="bi bi-cash-coin"></i> Enviar a Cajera';
            });
        }
    </script>
</body>
</html>
