<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Expediente <?= htmlspecialchars($ordenCompra['OC_NUMERO_EXPEDIENTE'] ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-header {
                display: none !important;
            }
            .documento-section {
                margin: 0 !important;
                padding: 0 !important;
            }
            .documento-section h2 {
                display: none !important;
            }
            
            /* Estilos específicos para cada documento */
            
            /* Orden de Compra */
            input, select, textarea {
                border: none !important;
                background: transparent !important;
                font-size: 8.5px !important;
                padding: 1px !important;
            }
            
            div[style*="margin"] {
                margin: 2px auto !important;
            }
            
            div[style*="padding"] {
                padding: 2px !important;
            }
            
            div[style*="height:70px"] {
                height: 50px !important;
            }
            
            div, span, p, li {
                font-size: 7.5px !important;
                line-height: 1.2 !important;
            }
            
            textarea {
                height: 30px !important;
            }
            
            .header {
                padding: 4px !important;
            }
            
            .header-left img {
                width: 150px !important;
            }
            
            /* Cartas y documentos */
            .page {
                box-shadow: none !important;
                padding: 15px !important;
            }
            
            .header img {
                height: 50px !important;
            }
            
            .title, h2 {
                font-size: 11pt !important;
                margin: 10px 0 !important;
            }
            
            .date-section {
                margin-bottom: 15px !important;
            }
            
            .content {
                margin: 10px 0 !important;
            }
            
            .signature-section, .firma-section {
                margin-top: 15px !important;
            }
            
            .firma-box {
                height: 50px !important;
            }
            
            ul, ol {
                margin: 5px 0 !important;
                padding-left: 15px !important;
            }
            
            @page {
                size: A4;
                margin: 10mm;
            }
        }
        
        body {
            background: white;
            padding: 20px;
        }
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            border-bottom: 3px solid #667eea;
        }
        .documento-section {
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg">
            <i class="bi bi-printer"></i> Imprimir Todos los Documentos
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-lg">
            Cerrar
        </button>
    </div>

    <div class="print-header">
        <h1>Expediente: <?= htmlspecialchars($ordenCompra['OC_NUMERO_EXPEDIENTE'] ?? 'N/A') ?></h1>
        <p class="mb-0">Cliente: <?= htmlspecialchars($ordenCompra['OC_COMPRADOR_NOMBRE'] ?? 'N/A') ?></p>
        <p class="mb-0">Vehículo: <?= htmlspecialchars($ordenCompra['OC_VEHICULO_MARCA'] ?? '') ?> <?= htmlspecialchars($ordenCompra['OC_VEHICULO_MODELO'] ?? '') ?></p>
    </div>

    <?php
    require_once __DIR__ . '/../../models/Document.php';
    $docModel = new Document();
    
    // Mapeo de documentos a sus vistas y tablas
    $documentosConfig = [
        'acta-conocimiento-conformidad' => ['vista' => 'acta-conocimiento-conformidad', 'tabla' => 'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD', 'fk' => 'ACC_DOCUMENTO_VENTA_ID'],
        'actorizacion-datos-personales' => ['vista' => 'actorizacion-datos-personales', 'tabla' => 'SIST_AUTORIZACION_DATOS_PERSONALES', 'fk' => 'ADP_DOCUMENTO_VENTA_ID'],
        'carta_conocimiento_aceptacion' => ['vista' => 'carta_conocimiento_aceptacion', 'tabla' => 'SIST_CARTA_CONOCIMIENTO_ACEPTACION', 'fk' => 'CCA_DOCUMENTO_VENTA_ID'],
        'carta_recepcion' => ['vista' => 'carta_recepcion', 'tabla' => 'SIST_CARTA_RECEPCION', 'fk' => 'CR_DOCUMENTO_VENTA_ID'],
        'carta-caracteristicas' => ['vista' => 'carta-caracteristicas', 'tabla' => 'SIST_CARTA_CARACTERISTICAS', 'fk' => 'CC_DOCUMENTO_VENTA_ID'],
        'carta_caracteristicas_banbif' => ['vista' => 'carta_caracteristicas_banbif', 'tabla' => 'SIST_CARTA_CARACTERISTICAS_BANBIF', 'fk' => 'CCB_DOCUMENTO_VENTA_ID'],
        'carta_felicitaciones' => ['vista' => 'carta_felicitaciones', 'tabla' => 'SIST_CARTA_FELICITACIONES', 'fk' => 'CF_DOCUMENTO_VENTA_ID'],
        'carta_obsequios' => ['vista' => 'carta_obsequios', 'tabla' => 'SIST_CARTA_OBSEQUIOS', 'fk' => 'CO_DOCUMENTO_VENTA_ID'],
        'politica_proteccion_datos' => ['vista' => 'politica_proteccion_datos', 'tabla' => 'SIST_POLITICA_PROTECCION_DATOS', 'fk' => 'PPD_DOCUMENTO_VENTA_ID']
    ];

    $ordenCompraData = $ordenCompra;
    $id = $ordenCompra['OC_ID'];

    // 1. Siempre incluir la orden de compra primero
    echo '<div class="documento-section page-break">';
    echo '<h2 class="text-center mb-4">Orden de Compra</h2>';
    if (file_exists(__DIR__ . '/../documents/layouts/orden-compra.php')) {
        include __DIR__ . '/../documents/layouts/orden-compra.php';
    }
    echo '</div>';

    // 2. Incluir todos los demás documentos que existan en la BD
    foreach ($documentosConfig as $documentoId => $config) {
        // Verificar si el documento existe en la BD
        $documentData = $docModel->getDocumentData($documentoId, $ordenCompra['OC_ID']);
        
        if (!empty($documentData)) {
            $vistaPath = __DIR__ . "/../documents/layouts/{$config['vista']}.php";
            
            if (file_exists($vistaPath)) {
                echo '<div class="documento-section page-break">';
                echo '<h2 class="text-center mb-4">' . htmlspecialchars(ucwords(str_replace(['_', '-'], ' ', $documentoId))) . '</h2>';
                
                // Cargar datos del vehículo si es necesario
                if (in_array($documentoId, ['carta-caracteristicas', 'carta_caracteristicas_banbif'])) {
                    $chasis = $ordenCompra['OC_VEHICULO_CHASIS'] ?? '';
                    if ($chasis) {
                        $vehiculoData = $docModel->buscarVehiculoPorChasis($chasis);
                    }
                }
                
                include $vistaPath;
                echo '</div>';
            }
        }
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-imprimir al cargar
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
