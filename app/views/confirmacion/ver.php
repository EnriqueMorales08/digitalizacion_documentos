<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Orden de Compra - <?= htmlspecialchars($numeroExpediente) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .confirmation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .document-preview {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            background: #f8f9fa;
        }
        .btn-accept {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-accept:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
        .btn-reject {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }
        .btn-reject:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }
        .info-badge {
            background: #e0e7ff;
            color: #4338ca;
            padding: 10px 20px;
            border-radius: 20px;
            display: inline-block;
            margin: 5px;
        }
        .success-message {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .warning-message {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1050;
        }
        .loading-box {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .loading-spinner {
            width: 2rem;
            height: 2rem;
            border: 0.25em solid #e5e7eb;
            border-top-color: #4f46e5;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="overlayImprimirTodos">
        <div class="loading-box">
            <div class="loading-spinner"></div>
            <div class="mt-2 fw-semibold">Cargando documentos...</div>
            <div class="text-muted small">Por favor espera mientras preparamos todos los documentos.</div>
        </div>
    </div>

    <div class="container">
        <div class="confirmation-card">
            <!-- Header -->
            <div class="header-section">
                <h1><i class="bi bi-file-earmark-check"></i> Confirmación de Orden de Compra</h1>
                <p class="mb-0">Expediente: <strong><?= htmlspecialchars($numeroExpediente) ?></strong></p>
            </div>

            <!-- Content -->
            <div class="p-4">
                <?php if ($confirmacion['CONF_ESTADO'] === 'ACEPTADO'): ?>
                    <!-- Ya aceptado -->
                    <div class="success-message">
                        <h4><i class="bi bi-check-circle-fill"></i> ¡Orden de Compra Confirmada!</h4>
                        <p class="mb-0">Ya has confirmado tu orden de compra el <?= $confirmacion['CONF_FECHA_RESPUESTA']->format('d/m/Y H:i') ?>.</p>
                        <p class="mb-0">Gracias por tu confirmación.</p>
                    </div>
                <?php elseif ($confirmacion['CONF_ESTADO'] === 'RECHAZADO'): ?>
                    <!-- Ya rechazado -->
                    <div class="alert alert-danger">
                        <h4><i class="bi bi-x-circle-fill"></i> Orden de Compra Rechazada</h4>
                        <p class="mb-0">Rechazaste tu orden de compra el <?= $confirmacion['CONF_FECHA_RESPUESTA']->format('d/m/Y H:i') ?>.</p>
                    </div>
                <?php else: ?>
                    <!-- Pendiente de confirmación -->
                    <div class="warning-message">
                        <h5><i class="bi bi-info-circle-fill"></i> Importante</h5>
                        <p class="mb-0">¿Estás de acuerdo con la firma de la orden de compra?</p>
                    </div>

                    <!-- Información del Cliente -->
                    <div class="document-preview">
                        <h5><i class="bi bi-person-circle"></i> Información del Cliente</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Cliente:</strong> <?= htmlspecialchars($orden['OC_COMPRADOR_NOMBRE'] ?? 'N/A') ?> <?= htmlspecialchars($orden['OC_COMPRADOR_APELLIDO'] ?? '') ?></p>
                                <p><strong>Documento:</strong> <?= htmlspecialchars($orden['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? 'N/A') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?= htmlspecialchars($orden['OC_EMAIL_CLIENTE'] ?? 'N/A') ?></p>
                                <p><strong>Teléfono:</strong> <?= htmlspecialchars($orden['OC_TELEFONO_CLIENTE'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Vehículo -->
                    <div class="document-preview">
                        <h5><i class="bi bi-car-front"></i> Información del Vehículo</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Marca:</strong> <?= htmlspecialchars($orden['OC_VEHICULO_MARCA'] ?? 'N/A') ?></p>
                                <p><strong>Modelo:</strong> <?= htmlspecialchars($orden['OC_VEHICULO_MODELO'] ?? 'N/A') ?></p>
                                <p><strong>Año:</strong> <?= htmlspecialchars($orden['OC_VEHICULO_ANIO_MODELO'] ?? 'N/A') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Color:</strong> <?= htmlspecialchars($orden['OC_VEHICULO_COLOR'] ?? 'N/A') ?></p>
                                <p><strong>Chasis:</strong> <?= htmlspecialchars($orden['OC_VEHICULO_CHASIS'] ?? 'N/A') ?></p>
                                <p><strong>Motor:</strong> <?= htmlspecialchars($orden['OC_VEHICULO_MOTOR'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- Lista de Documentos del Expediente (sin incluir Orden de Compra) -->
                    <?php if (!empty($documentos) && is_array($documentos)): ?>
                        <?php
                            $documentosFirmables = [
                                'carta_conocimiento_aceptacion',
                                'carta_recepcion',
                                'carta-caracteristicas',
                                'politica_proteccion_datos',
                                'acta-conocimiento-conformidad',
                                'actorizacion-datos-personales'
                            ];
                        ?>
                        <div class="mt-4">
                            <h5><i class="bi bi-files"></i> Documentos de tu Expediente</h5>
                            <p class="text-muted small mb-2">Revisa cada documento. La <strong>Orden de Compra</strong> se muestra al final.</p>

                            <?php foreach ($documentos as $doc): ?>
                                <div class="document-preview">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                        <div>
                                            <strong><?= htmlspecialchars($doc['nombre']) ?></strong>
                                            <?php if (!empty($doc['existe'])): ?>
                                                <span class="badge bg-success ms-2">Disponible</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary ms-2">No generado</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($doc['existe']) && in_array($doc['id'], $documentosFirmables, true)): ?>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input chk-firmar-doc" type="checkbox"
                                                           id="chk_firmar_<?= htmlspecialchars($doc['id'], ENT_QUOTES) ?>"
                                                           data-doc-id="<?= htmlspecialchars($doc['id'], ENT_QUOTES) ?>" checked>
                                                    <label class="form-check-label small" for="chk_firmar_<?= htmlspecialchars($doc['id'], ENT_QUOTES) ?>">
                                                        Firmar documento
                                                    </label>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($doc['existe'])): ?>
                                                <button type="button" 
                                                        class="btn btn-outline-primary btn-sm"
                                                        onclick="abrirDocumentoModal('<?= htmlspecialchars($doc['id'], ENT_QUOTES) ?>')">
                                                    <i class="bi bi-eye"></i> Ver
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Botones de acciones de documentos (misma fila) -->
                    <div class="my-4">
                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 mb-2">
                            <button type="button"
                                    class="btn btn-outline-secondary btn-lg"
                                    onclick="imprimirTodosDocumentos()">
                                <i class="bi bi-files"></i> Ver todos los documentos en PDF
                            </button>

                            <!-- Ver Orden de Compra (SIEMPRE AL FINAL) -->
                            <a href="/digitalizacion-documentos/expedientes/imprimir-documento?numero=<?= urlencode($numeroExpediente) ?>&documento=orden-compra&cliente=1" 
                               target="_blank" 
                               class="btn btn-primary btn-lg">
                                <i class="bi bi-file-earmark-text"></i> Ver Orden de Compra
                            </a>
                        </div>

                        <div class="text-center">
                            <p class="text-muted mt-1 small mb-1">
                                <i class="bi bi-info-circle"></i> Se mostrará la ventana de impresión del navegador (Guardar como PDF) y esta pantalla quedará de fondo.
                            </p>
                            <p class="text-muted mt-1 small">
                                <i class="bi bi-info-circle"></i> La Orden de Compra es el documento principal. Revísala con especial atención.
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- Pregunta de Confirmación -->
                    <div class="text-center">
                        <h3 class="mb-3">¿Confirmas que estás de acuerdo con la orden de compra?</h3>

                        <p class="text-muted mb-2">Primero firma electrónicamente y luego confirma tu decisión.</p>

                        <div class="mb-4">
                            <button type="button" class="btn btn-lg" onclick="abrirModalFirmaCliente()"
                                    style="padding: 14px 40px; border-radius: 9999px; font-size: 18px; font-weight: 600;
                                           background: linear-gradient(135deg, #2563eb, #4f46e5); border:none; color:white;
                                           box-shadow: 0 10px 25px rgba(37,99,235,0.35);">
                                <i class="bi bi-pen"></i> Firmar electrónicamente ahora
                            </button>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <button class="btn btn-accept" onclick="confirmarDocumentos('ACEPTADO')">
                                <i class="bi bi-check-circle"></i> Sí, Acepto y Confirmo
                            </button>
                            <button class="btn btn-reject" onclick="confirmarDocumentos('RECHAZADO')">
                                <i class="bi bi-x-circle"></i> No, Rechazar
                            </button>
                        </div>
                        
                        <!-- Área de observaciones (opcional) -->
                        <div class="mt-4">
                            <label for="observaciones" class="form-label">Observaciones (opcional):</label>
                            <textarea id="observaciones" class="form-control" rows="3" placeholder="Si tienes algún comentario, escríbelo aquí..."></textarea>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div class="text-center p-3 bg-light">
                <small class="text-muted">
                    <i class="bi bi-shield-check"></i> Sistema de Digitalización - Interamericana Norte SAC
                </small>
            </div>
        </div>
    </div>

    <!-- Modal para ver documentos -->
    <div class="modal fade" id="modalDocumento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-text"></i> Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="height: 80vh;">
                    <iframe id="iframeDocumento" src="" style="width:100%; height:100%; border:0;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="descargarPdfDocumentoActual()">
                        <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de firma del cliente -->
    <div class="modal fade" id="modalFirmaCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pen"></i> Firma del Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-2 small">
                        Por favor firma en el recuadro usando el mouse o tu dedo (si estás en un dispositivo táctil).
                    </p>
                    <div class="border rounded p-2" style="background-color:#f9fafb;">
                        <canvas id="canvasFirmaCliente" style="width:100%; max-width:100%; height:200px; touch-action:none; cursor:crosshair; background-color:#ffffff;"></canvas>
                    </div>
                    <div class="mt-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFirmaCliente()">
                            <i class="bi bi-eraser"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="guardarFirmaCliente()">
                            <i class="bi bi-save"></i> Guardar firma
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <small class="text-muted">
                        La firma se guardará asociada a este expediente y se mostrará en la Orden de Compra y documentos relacionados.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const token = '<?= htmlspecialchars($confirmacion['CONF_TOKEN_CONFIRMACION']) ?>';
        const numeroExpediente = '<?= htmlspecialchars($numeroExpediente) ?>';
        let urlDocumentoActual = '';
        let firmaClienteGuardada = false;
        let canvasFirma = null;
        let ctxFirma = null;
        let dibujando = false;
        let ultimaPosicion = { x: 0, y: 0 };

        function inicializarCanvasFirma() {
            const canvas = document.getElementById('canvasFirmaCliente');
            if (!canvas) return;

            canvasFirma = canvas;
            ctxFirma = canvas.getContext('2d');

            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;

            ctxFirma.fillStyle = '#ffffff';
            ctxFirma.fillRect(0, 0, canvas.width, canvas.height);

            const logo = new Image();
            logo.onload = function () {
                const logoWidth = canvas.width * 0.4;
                const logoHeight = logoWidth * (logo.height / logo.width);
                const x = (canvas.width - logoWidth) / 2;
                const y = (canvas.height - logoHeight) / 2;

                ctxFirma.save();
                ctxFirma.globalAlpha = 0.15;
                ctxFirma.drawImage(logo, x, y, logoWidth, logoHeight);
                ctxFirma.restore();
            };
            logo.src = '/digitalizacion-documentos/assets/images/logo_interamericana.jpg';

            canvas.onmousedown = function (e) {
                dibujando = true;
                ultimaPosicion = obtenerPosicionCanvas(e, canvas);
            };
            canvas.onmousemove = function (e) {
                if (!dibujando) return;
                const posicion = obtenerPosicionCanvas(e, canvas);
                dibujarLinea(ultimaPosicion, posicion);
                ultimaPosicion = posicion;
            };
            canvas.onmouseup = function () { dibujando = false; };
            canvas.onmouseleave = function () { dibujando = false; };

            canvas.addEventListener('touchstart', function (e) {
                e.preventDefault();
                dibujando = true;
                ultimaPosicion = obtenerPosicionCanvas(e.touches[0], canvas);
            }, { passive: false });

            canvas.addEventListener('touchmove', function (e) {
                e.preventDefault();
                if (!dibujando) return;
                const posicion = obtenerPosicionCanvas(e.touches[0], canvas);
                dibujarLinea(ultimaPosicion, posicion);
                ultimaPosicion = posicion;
            }, { passive: false });

            canvas.addEventListener('touchend', function (e) {
                e.preventDefault();
                dibujando = false;
            });
        }

        function obtenerPosicionCanvas(evento, canvas) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: evento.clientX - rect.left,
                y: evento.clientY - rect.top
            };
        }

        function dibujarLinea(desde, hasta) {
            if (!ctxFirma) return;
            ctxFirma.strokeStyle = '#000000';
            ctxFirma.lineWidth = 2;
            ctxFirma.lineCap = 'round';
            ctxFirma.beginPath();
            ctxFirma.moveTo(desde.x, desde.y);
            ctxFirma.lineTo(hasta.x, hasta.y);
            ctxFirma.stroke();
        }

        function abrirModalFirmaCliente() {
            const modalEl = document.getElementById('modalFirmaCliente');
            if (!modalEl) return;
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            setTimeout(() => {
                inicializarCanvasFirma();
            }, 200);
        }

        function limpiarFirmaCliente() {
            if (!canvasFirma || !ctxFirma) return;
            ctxFirma.clearRect(0, 0, canvasFirma.width, canvasFirma.height);
            ctxFirma.fillStyle = '#ffffff';
            ctxFirma.fillRect(0, 0, canvasFirma.width, canvasFirma.height);

            const logo = new Image();
            logo.onload = function () {
                const logoWidth = canvasFirma.width * 0.4;
                const logoHeight = logoWidth * (logo.height / logo.width);
                const x = (canvasFirma.width - logoWidth) / 2;
                const y = (canvasFirma.height - logoHeight) / 2;

                ctxFirma.save();
                ctxFirma.globalAlpha = 0.15;
                ctxFirma.drawImage(logo, x, y, logoWidth, logoHeight);
                ctxFirma.restore();
            };
            logo.src = '/digitalizacion-documentos/assets/images/logo_interamericana.jpg';
        }

        function guardarFirmaCliente() {
            if (!canvasFirma) {
                alert('No se encontró el área de firma');
                return;
            }

            const dataUrl = canvasFirma.toDataURL('image/png');

            const checks = document.querySelectorAll('.chk-firmar-doc');
            let body = `token=${encodeURIComponent(token)}&firma_base64=${encodeURIComponent(dataUrl)}`;
            checks.forEach(chk => {
                if (chk.checked) {
                    const docId = chk.getAttribute('data-doc-id');
                    if (docId) {
                        body += `&documentos_seleccionados[]=${encodeURIComponent(docId)}`;
                    }
                }
            });

            fetch('/digitalizacion-documentos/confirmacion/guardar-firma-cliente', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    firmaClienteGuardada = true;
                    alert('✅ Firma guardada correctamente');

                    const modalEl = document.getElementById('modalFirmaCliente');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();
                    }
                } else {
                    alert('❌ Error al guardar la firma: ' + (data.error || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error al guardar firma:', error);
                alert('❌ Error al guardar la firma');
            });
        }

        function abrirDocumentoModal(docId) {
            // Construir URL del documento en modo impresión (mismo que antes, pero en iframe)
            urlDocumentoActual = `/digitalizacion-documentos/expedientes/imprimir-documento?numero=${encodeURIComponent(numeroExpediente)}&documento=${encodeURIComponent(docId)}&cliente=1`;
            const iframe = document.getElementById('iframeDocumento');
            if (iframe) {
                iframe.src = urlDocumentoActual;

                // Cuando cargue el documento dentro del iframe, ocultar botones de navegación/edición
                iframe.onload = function () {
                    try {
                        const doc = iframe.contentDocument || iframe.contentWindow.document;
                        if (!doc) return;

                        // Ocultar botones o enlaces que tengan texto Regresar / Volver / Editar / Guardar
                        const elementos = doc.querySelectorAll('button, a, input[type="submit"]');
                        elementos.forEach(el => {
                            const texto = (el.textContent || '').toLowerCase();
                            if (texto.includes('regresar') || texto.includes('volver') || texto.includes('editar') || texto.includes('guardar')) {
                                el.style.display = 'none';
                            }
                        });
                    } catch (e) {
                        console.error('No se pudieron ocultar botones internos del documento:', e);
                    }
                };
            }

            const modalEl = document.getElementById('modalDocumento');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }

        function abrirTodosDocumentosModal() {
            // Usar una ruta específica para el cliente basada en el token (sin pasar por login interno)
            urlDocumentoActual = `/digitalizacion-documentos/confirmacion/ver-todos?token=${encodeURIComponent(token)}`;

            const iframe = document.getElementById('iframeDocumento');
            if (iframe) {
                iframe.src = urlDocumentoActual;

                iframe.onload = function () {
                    try {
                        const doc = iframe.contentDocument || iframe.contentWindow.document;
                        if (!doc) return;

                        // Ocultar botones internos de navegación o impresión masiva que no aplican al cliente
                        const elementos = doc.querySelectorAll('button, a, input[type="submit"]');
                        elementos.forEach(el => {
                            const texto = (el.textContent || '').toLowerCase();
                            if (texto.includes('volver') || texto.includes('regresar') || texto.includes('editar') || texto.includes('imprimir')) {
                                el.style.display = 'none';
                            }
                        });

                        // Una vez cargados y limpiados los controles, abrir directamente el diálogo de impresión
                        // para que el usuario pueda elegir "Guardar como PDF"
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch (e) {
                        console.error('No se pudieron ocultar botones internos en imprimir-todos:', e);
                    }
                };
            }

            const modalEl = document.getElementById('modalDocumento');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }

        function descargarPdfDocumentoActual() {
            const iframe = document.getElementById('iframeDocumento');
            if (iframe && iframe.contentWindow) {
                // Usar el diálogo de impresión del navegador para permitir "Guardar como PDF"
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } else if (urlDocumentoActual) {
                // Fallback: abrir en misma pestaña
                window.location.href = urlDocumentoActual;
            }
        }

        // Mostrar/ocultar overlay de carga para "Ver todos los documentos en PDF"
        function mostrarOverlayImprimirTodos(mostrar) {
            const overlay = document.getElementById('overlayImprimirTodos');
            if (!overlay) return;
            overlay.style.display = mostrar ? 'flex' : 'none';
        }

        // Iframe oculto para imprimir TODOS los documentos sin salir de la pantalla actual
        function imprimirTodosDocumentos() {
            const url = `/digitalizacion-documentos/confirmacion/ver-todos?token=${encodeURIComponent(token)}`;

            mostrarOverlayImprimirTodos(true);

            let iframe = document.getElementById('iframeImprimirTodos');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'iframeImprimirTodos';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                iframe.style.visibility = 'hidden';
                document.body.appendChild(iframe);
            }

            iframe.onload = function () {
                try {
                    const win = iframe.contentWindow;
                    if (!win) {
                        mostrarOverlayImprimirTodos(false);
                        return;
                    }

                    // Cuando la vista de "imprimir todos" termine de armar los documentos,
                    // ella misma llamará a window.print(). Si no lo hace, forzamos el print aquí.
                    setTimeout(() => {
                        try {
                            win.focus();
                            win.print();
                        } catch (e) {
                            console.error('No se pudo ejecutar print() desde iframe imprimir-todos:', e);
                        } finally {
                            mostrarOverlayImprimirTodos(false);
                        }
                    }, 3000);
                } catch (e) {
                    console.error('Error al preparar impresión de todos los documentos:', e);
                    mostrarOverlayImprimirTodos(false);
                }
            };

            iframe.src = url;
        }

        function confirmarDocumentos(respuesta) {
            const observaciones = document.getElementById('observaciones')?.value || '';

            if (respuesta === 'ACEPTADO' && !firmaClienteGuardada) {
                alert('Por favor, firma electrónicamente antes de aceptar la orden de compra.');
                return;
            }

            const mensaje = respuesta === 'ACEPTADO'
                ? '¿Confirmas que aceptas la orden de compra?'
                : '¿Estás seguro de rechazar la orden de compra?';
            
            if (!confirm(mensaje)) {
                return;
            }

            // Deshabilitar botones
            const botones = document.querySelectorAll('.btn-accept, .btn-reject');
            botones.forEach(btn => btn.disabled = true);

            fetch('/digitalizacion-documentos/confirmacion/responder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `token=${encodeURIComponent(token)}&respuesta=${respuesta}&observaciones=${encodeURIComponent(observaciones)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    // Recargar página para mostrar estado actualizado
                    window.location.reload();
                } else {
                    alert('❌ Error: ' + data.error);
                    botones.forEach(btn => btn.disabled = false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al procesar respuesta');
                botones.forEach(btn => btn.disabled = false);
            });
        }
    </script>
</body>
</html>
