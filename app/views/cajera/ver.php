<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Cajera - <?= htmlspecialchars($numeroExpediente) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .document-item {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            background: #f8f9fa;
            transition: all 0.3s;
        }
        .document-item:hover {
            border-color: #f59e0b;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }
        .signature-pad {
            border: 2px solid #d97706;
            border-radius: 10px;
            cursor: crosshair;
            background: white;
        }
        .btn-approve {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-approve:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
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
        }
        .success-message {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-card">
            <!-- Header -->
            <div class="header-section">
                <h1><i class="bi bi-cash-coin"></i> Confirmación de Cajera</h1>
                <p class="mb-0">Expediente: <strong><?= htmlspecialchars($numeroExpediente) ?></strong></p>
            </div>

            <!-- Content -->
            <div class="p-4">
                <?php if ($confirmacion['CAJERA_ESTADO'] === 'APROBADO'): ?>
                    <!-- Ya aprobado -->
                    <div class="success-message">
                        <h4><i class="bi bi-check-circle-fill"></i> ¡Documentos Aprobados!</h4>
                        <p class="mb-0">Ya aprobaste estos documentos el <?= $confirmacion['CAJERA_FECHA_RESPUESTA']->format('d/m/Y H:i') ?>.</p>
                    </div>
                <?php elseif ($confirmacion['CAJERA_ESTADO'] === 'RECHAZADO'): ?>
                    <!-- Ya rechazado -->
                    <div class="alert alert-danger">
                        <h4><i class="bi bi-x-circle-fill"></i> Documentos Rechazados</h4>
                        <p class="mb-0">Rechazaste estos documentos el <?= $confirmacion['CAJERA_FECHA_RESPUESTA']->format('d/m/Y H:i') ?>.</p>
                    </div>
                <?php else: ?>
                    <!-- Pendiente de confirmación -->
                    <div class="alert alert-warning">
                        <h5><i class="bi bi-info-circle-fill"></i> Importante</h5>
                        <p class="mb-0">Por favor revisa todos los documentos, firma y aprueba o rechaza.</p>
                    </div>

                    <!-- Información del Cliente -->
                    <div class="document-item">
                        <h5><i class="bi bi-person-circle"></i> Información del Cliente</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Cliente:</strong> <?= htmlspecialchars($orden['OC_COMPRADOR_NOMBRE'] ?? 'N/A') ?></p>
                                <p><strong>Documento:</strong> <?= htmlspecialchars($orden['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? 'N/A') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?= htmlspecialchars($orden['OC_EMAIL_CLIENTE'] ?? 'N/A') ?></p>
                                <p><strong>Teléfono:</strong> <?= htmlspecialchars($orden['OC_TELEFONO_CLIENTE'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Asesor / Vendedor -->
                    <div class="document-item mt-3">
                        <h5><i class="bi bi-person-badge"></i> Información del Asesor de Venta</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Asesor:</strong> <?= htmlspecialchars($orden['OC_ASESOR_VENTA'] ?? 'N/A') ?></p>
                                <p><strong>Agencia:</strong> <?= htmlspecialchars($orden['OC_ASESOR_AGENCIA'] ?? ($orden['OC_AGENCIA'] ?? 'N/A')) ?></p>
                                <p><strong>Marca:</strong> <?= htmlspecialchars($orden['OC_ASESOR_MARCA'] ?? 'N/A') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Celular:</strong> <?= htmlspecialchars($orden['OC_ASESOR_CELULAR'] ?? 'N/A') ?></p>
                                <p><strong>Email Asesor:</strong> <?= htmlspecialchars($orden['OC_USUARIO_EMAIL'] ?? '') ?: 'N/A' ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Documentos -->
                    <div class="mt-4">
                        <h5><i class="bi bi-files"></i> Documentos del Expediente</h5>
                        <?php foreach ($documentos as $doc): ?>
                            <div class="document-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($doc['nombre']) ?></strong>
                                        <?php if ($doc['existe']): ?>
                                            <span class="badge bg-success ms-2">Disponible</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary ms-2">No generado</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($doc['existe']): ?>
                                        <a href="/digitalizacion-documentos/documents/show?id=<?= $doc['id'] ?>&orden_id=<?= $orden['OC_ID'] ?>&modo=ver&cajera=1&token=<?= urlencode($confirmacion['CAJERA_TOKEN']) ?>" 
                                           target="_blank" 
                                           class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i> Ver Documento
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-4">

                    <!-- Firma Digital -->
                    <div class="mt-4">
                        <h5><i class="bi bi-pen"></i> Firma Digital de Cajera</h5>
                        <p class="text-muted">Ingrese su usuario y contraseña para firmar digitalmente:</p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="usuario-firma" class="form-label">Usuario:</label>
                                <input type="text" class="form-control" id="usuario-firma" placeholder="Ingrese su usuario">
                            </div>
                            <div class="col-md-6">
                                <label for="password-firma" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" id="password-firma" placeholder="Ingrese su contraseña">
                            </div>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <button type="button" class="btn btn-primary" onclick="verificarFirmaCajera()">
                                <i class="bi bi-shield-check"></i> Verificar y Cargar Firma
                            </button>
                        </div>
                        
                        <!-- Preview de la firma -->
                        <div id="firma-preview" class="mt-3 text-center" style="display: none;">
                            <p class="text-success"><i class="bi bi-check-circle"></i> Firma cargada correctamente</p>
                            <div id="firma-imagen"></div>
                        </div>
                        
                        <!-- Campo oculto para guardar la firma -->
                        <input type="hidden" id="firma-cajera-data" value="">
                    </div>

                    <!-- Observaciones -->
                    <div class="mt-4">
                        <label for="observaciones" class="form-label">Observaciones (opcional):</label>
                        <textarea class="form-control" id="observaciones" rows="3" placeholder="Ingrese sus observaciones aquí..."></textarea>
                    </div>

                    <hr class="my-4">

                    <!-- Botones de Acción -->
                    <div class="text-center">
                        <h4 class="mb-4">¿Aprueba o rechaza los documentos?</h4>
                        
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <button class="btn btn-approve" onclick="confirmarDocumentos('APROBADO')">
                                <i class="bi bi-check-circle"></i> Aprobar Documentos
                            </button>
                            <button class="btn btn-reject" onclick="confirmarDocumentos('RECHAZADO')">
                                <i class="bi bi-x-circle"></i> Rechazar Documentos
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const token = '<?= htmlspecialchars($confirmacion['CAJERA_TOKEN']) ?>';
        
        // Función para verificar firma de cajera
        function verificarFirmaCajera() {
            const usuario = document.getElementById('usuario-firma').value.trim();
            const password = document.getElementById('password-firma').value.trim();
            
            if (!usuario || !password) {
                alert('Por favor, ingrese usuario y contraseña.');
                return;
            }
            
            // Mostrar mensaje de carga
            const btn = event.target;
            const textoOriginal = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Verificando...';
            btn.disabled = true;
            
            fetch('/digitalizacion-documentos/documents/verificar-firma', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'usuario=' + encodeURIComponent(usuario) + '&password=' + encodeURIComponent(password)
            })
            .then(response => {
                console.log('Status:', response.status);
                console.log('Headers:', response.headers);
                
                // Intentar leer como texto primero para debugging
                return response.text().then(text => {
                    console.log('Respuesta raw:', text);
                    
                    if (!response.ok) {
                        throw new Error('Error HTTP ' + response.status + ': ' + text.substring(0, 200));
                    }
                    
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Respuesta no es JSON válido: ' + text.substring(0, 200));
                    }
                });
            })
            .then(data => {
                console.log('Respuesta:', data);
                
                if (data.success) {
                    // Guardar la firma en el campo oculto
                    document.getElementById('firma-cajera-data').value = data.firma;
                    
                    // Mostrar preview
                    document.getElementById('firma-preview').style.display = 'block';
                    document.getElementById('firma-imagen').innerHTML = '<img src="' + data.firma + '" style="max-width:200px; max-height:80px; border:1px solid #ddd; padding:5px;">';
                    
                    alert('✅ Firma cargada correctamente');
                } else {
                    const mensaje = data.message || 'Usuario o contraseña incorrectos';
                    alert('❌ ' + mensaje);
                }
                
                // Restaurar botón
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
            })
            .catch(error => {
                console.error('Error completo:', error);
                alert('Error al verificar la firma: ' + error.message);
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
            });
        }

        function confirmarDocumentos(respuesta) {
            // Validar firma
            const firmaData = document.getElementById('firma-cajera-data').value;
            if (!firmaData) {
                alert('Por favor, verifique su firma antes de continuar.');
                return;
            }

            const observaciones = document.getElementById('observaciones').value;
            
            const mensaje = respuesta === 'APROBADO' 
                ? '¿Confirma que aprueba todos los documentos?' 
                : '¿Está seguro de rechazar los documentos?';
            
            if (!confirm(mensaje)) {
                return;
            }

            // Usar la firma verificada
            const firmaBase64 = firmaData;

            // Deshabilitar botones
            const botones = document.querySelectorAll('.btn-approve, .btn-reject');
            botones.forEach(btn => btn.disabled = true);

            fetch('/digitalizacion-documentos/cajera/responder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'token=' + encodeURIComponent(token) + 
                      '&respuesta=' + encodeURIComponent(respuesta) + 
                      '&firma=' + encodeURIComponent(firmaBase64) +
                      '&observaciones=' + encodeURIComponent(observaciones)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    botones.forEach(btn => btn.disabled = false);
                }
            })
            .catch(error => {
                alert('Error al procesar la solicitud');
                botones.forEach(btn => btn.disabled = false);
            });
        }
    </script>
</body>
</html>
