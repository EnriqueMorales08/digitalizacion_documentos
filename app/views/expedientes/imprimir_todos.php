<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Expediente <?= htmlspecialchars($ordenCompra['OC_NUMERO_EXPEDIENTE'] ?? '') ?></title>
    <style>
        body {
            background: white;
            margin: 0;
            padding: 0;
        }
        
        /* Contenedor de carga (se oculta al imprimir) */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            margin-top: 20px;
            font-size: 18px;
            color: #667eea;
            font-family: Arial, sans-serif;
        }
        
        /* Contenedor de documentos */
        #documentos-container {
            display: none; /* Oculto hasta que cargue */
        }
        
        #documentos-container.loaded {
            display: block;
        }
        
        /* Iframe para cada documento */
        .documento-iframe {
            width: 100%;
            border: none;
            margin: 0;
            padding: 0;
            display: block;
            min-height: 1000px;
            overflow: hidden;
        }
        
        /* Separador entre documentos - salto de página */
        .document-separator {
            page-break-after: always;
            height: 0;
            margin: 0;
            padding: 0;
            background: transparent;
            display: block;
        }
        
        /* Estilos para impresión */
        @media print {
            @page {
                size: A4;
                margin: 8mm 8mm 8mm 3mm;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .loading-overlay {
                display: none !important;
            }
            
            #documentos-container {
                display: block !important;
                margin: 0;
                padding: 0;
            }
            
            .documento-iframe {
                page-break-before: always;
                page-break-after: avoid;
                page-break-inside: avoid;
                display: block;
                border: none;
                margin: 0;
                padding: 0;
                width: 100% !important;
            }
            
            /* El primer iframe no necesita salto antes */
            .documento-iframe:first-child {
                page-break-before: avoid !important;
            }
            
            /* Asegurar que el último iframe no tenga salto de página después */
            .documento-iframe:last-child {
                page-break-after: avoid !important;
            }
        }
        
        @media screen {
            #documentos-container {
                max-width: 950px;
                margin: 0 auto;
                padding: 20px;
            }
            
            .documento-iframe {
                margin: 0 auto 20px auto;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay de carga -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="spinner"></div>
        <div class="loading-text" id="loading-text">Cargando documentos...</div>
    </div>
    
    <!-- Contenedor donde se cargarán todos los documentos en iframes -->
    <div id="documentos-container"></div>

    <script>
        <?php
        require_once __DIR__ . '/../../models/Document.php';
        $docModel = new Document();
        
        // Obtener tipo de combustible del vehículo
        $tipoCombustible = trim($ordenCompra['OC_VEHICULO_TIPO_COMBUSTIBLE'] ?? '');
        
        // Mapeo de documentos a sus vistas y tablas
        $documentosConfig = [];
        
        // Solo agregar Acta de Conocimiento y Conformidad si el vehículo es GLP (tipo combustible = 'DU')
        if ($tipoCombustible === 'DU') {
            $documentosConfig['acta-conocimiento-conformidad'] = ['vista' => 'acta-conocimiento-conformidad', 'tabla' => 'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD', 'fk' => 'ACC_DOCUMENTO_VENTA_ID'];
        }
        
        // Agregar el resto de documentos
        $documentosConfig['actorizacion-datos-personales'] = ['vista' => 'actorizacion-datos-personales', 'tabla' => 'SIST_AUTORIZACION_DATOS_PERSONALES', 'fk' => 'ADP_DOCUMENTO_VENTA_ID'];
        $documentosConfig['carta_conocimiento_aceptacion'] = ['vista' => 'carta_conocimiento_aceptacion', 'tabla' => 'SIST_CARTA_CONOCIMIENTO_ACEPTACION', 'fk' => 'CCA_DOCUMENTO_VENTA_ID'];
        $documentosConfig['carta_recepcion'] = ['vista' => 'carta_recepcion', 'tabla' => 'SIST_CARTA_RECEPCION', 'fk' => 'CR_DOCUMENTO_VENTA_ID'];
        $documentosConfig['carta-caracteristicas'] = ['vista' => 'carta-caracteristicas', 'tabla' => 'SIST_CARTA_CARACTERISTICAS', 'fk' => 'CC_DOCUMENTO_VENTA_ID'];
        $documentosConfig['carta_caracteristicas_banbif'] = ['vista' => 'carta_caracteristicas_banbif', 'tabla' => 'SIST_CARTA_CARACTERISTICAS_BANBIF', 'fk' => 'CCB_DOCUMENTO_VENTA_ID'];
        $documentosConfig['carta_felicitaciones'] = ['vista' => 'carta_felicitaciones', 'tabla' => 'SIST_CARTA_FELICITACIONES', 'fk' => 'CF_DOCUMENTO_VENTA_ID'];
        $documentosConfig['carta_obsequios'] = ['vista' => 'carta_obsequios', 'tabla' => 'SIST_CARTA_OBSEQUIOS', 'fk' => 'CO_DOCUMENTO_VENTA_ID'];
        $documentosConfig['politica_proteccion_datos'] = ['vista' => 'politica_proteccion_datos', 'tabla' => 'SIST_POLITICA_PROTECCION_DATOS', 'fk' => 'PPD_DOCUMENTO_VENTA_ID'];
        
        $numeroExpediente = $ordenCompra['OC_NUMERO_EXPEDIENTE'] ?? '';
        
        // Construir array de documentos a imprimir
        $documentosAImprimir = ['orden-compra'];
        
        foreach ($documentosConfig as $documentoId => $config) {
            $documentData = $docModel->getDocumentData($documentoId, $id);
            if (!empty($documentData)) {
                $documentosAImprimir[] = $documentoId;
            }
        }
        ?>
        const numeroExpediente = '<?= htmlspecialchars($numeroExpediente) ?>';
        const documentos = <?= json_encode($documentosAImprimir) ?>;
        const clienteFlag = <?= (isset($esVistaCliente) && $esVistaCliente) ? "'&cliente=1'" : "''" ?>;
        const container = document.getElementById('documentos-container');
        const loadingOverlay = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');
        
        let documentosCargados = 0;
        let iframes = [];
        
        // Cargar todos los documentos mediante iframes
        function cargarDocumentos() {
            console.log('📄 Cargando', documentos.length, 'documentos en iframes...');
            
            documentos.forEach((doc, index) => {
                const url = `/digitalizacion-documentos/expedientes/imprimir-documento?numero=${encodeURIComponent(numeroExpediente)}&documento=${encodeURIComponent(doc)}${clienteFlag}`;
                
                loadingText.textContent = `Cargando documento ${index + 1}/${documentos.length}: ${doc}...`;
                console.log(`📄 Creando iframe ${index + 1}/${documentos.length}: ${doc}`);
                
                // Crear iframe para este documento
                const iframe = document.createElement('iframe');
                iframe.className = 'documento-iframe';
                iframe.setAttribute('data-documento', doc);
                iframe.setAttribute('scrolling', 'no');
                iframe.src = url;
                
                // Evento cuando el iframe carga completamente
                iframe.onload = function() {
                    console.log(`📄 Iframe cargado para: ${doc}`);
                    
                    // Ajustar altura del iframe e inyectar CSS para impresión
                    setTimeout(() => {
                        try {
                            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                            
                            // Inyectar CSS para reducir y centrar el contenido en impresión
                            const style = iframeDoc.createElement('style');
                            style.textContent = `
                                @media print {
                                    html, body {
                                        width: 100% !important;
                                        margin: 0 !important;
                                        padding: 0 !important;
                                        overflow: visible !important;
                                    }
                                    
                                    body {
                                        transform: scale(0.85);
                                        transform-origin: top left;
                                        /* Compensar el escalado para centrar: (100% - 80%) / 2 = 10% */
                                        margin-left: 7.5% !important;
                                        margin-right: 7.5% !important;
                                    }
                                    
                                    .page {
                                        margin: 0 auto !important;
                                    }
                                }
                            `;
                            iframeDoc.head.appendChild(style);
                            console.log(`🎨 CSS de impresión inyectado en ${doc}`);
                            
                            const height = Math.max(
                                iframeDoc.body.scrollHeight,
                                iframeDoc.body.offsetHeight,
                                iframeDoc.documentElement.scrollHeight,
                                iframeDoc.documentElement.offsetHeight
                            );
                            iframe.style.height = (height + 150) + 'px';
                            console.log(`📏 Altura ajustada para ${doc}: ${height + 150}px`);
                        } catch (e) {
                            console.warn('No se pudo ajustar altura:', e);
                        }
                        
                        documentosCargados++;
                        console.log(`✅ Documento ${documentosCargados}/${documentos.length} completado: ${doc}`);
                        
                        if (documentosCargados === documentos.length) {
                            todosDocumentosCargados();
                        }
                    }, 1500);
                };
                
                // Manejo de errores
                iframe.onerror = function() {
                    console.error('❌ Error al cargar documento:', doc);
                    documentosCargados++;
                    
                    if (documentosCargados === documentos.length) {
                        todosDocumentosCargados();
                    }
                };
                
                // Agregar iframe al contenedor
                container.appendChild(iframe);
                iframes.push(iframe);
            });
        }
        
        function todosDocumentosCargados() {
            console.log('✅ Todos los documentos cargados. Preparando impresión...');
            loadingText.textContent = 'Todos los documentos listos. Preparando impresión...';
            
            // Esperar un momento adicional para asegurar rendering completo
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
                container.classList.add('loaded');
                
                // Solo imprimir automáticamente si NO estamos en un iframe
                // (cuando se carga en iframe, la función JavaScript padre se encarga de imprimir)
                if (window.self === window.top) {
                    // Esperar antes de imprimir
                    setTimeout(() => {
                        console.log('🖨️ Abriendo diálogo de impresión...');
                        window.print();
                    }, 2000);
                } else {
                    console.log('📄 Documentos cargados en iframe, esperando llamada a print() desde padre...');
                }
            }, 1000);
        }
        
        // Iniciar carga cuando la página esté lista
        window.onload = function() {
            setTimeout(() => {
                cargarDocumentos();
            }, 500);
        };
    </script>
</body>
</html>
