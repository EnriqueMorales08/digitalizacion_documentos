<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Aprobación - Orden de Compra</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .header {
            background: #1e3a8a;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #1e3a8a;
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .info-item .value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .estado {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .estado.pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .estado.aprobado {
            background: #d1fae5;
            color: #065f46;
        }

        .estado.rechazado {
            background: #fee2e2;
            color: #991b1b;
        }

        .observaciones {
            margin: 30px 0;
        }

        .observaciones label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .observaciones textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            min-height: 100px;
        }

        .observaciones textarea:focus {
            outline: none;
            border-color: #1e3a8a;
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-aprobar {
            background: #10b981;
            color: white;
        }

        .btn-aprobar:hover {
            background: #059669;
        }

        .btn-rechazar {
            background: #ef4444;
            color: white;
        }

        .btn-rechazar:hover {
            background: #dc2626;
        }

        .mensaje {
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
        }

        .mensaje.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .mensaje.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Aprobación de Orden de Compra</h1>
            <p>Revise los detalles y tome una decisión</p>
        </div>

        <div class="content">
            <div class="info-grid">
                <div class="info-item">
                    <label>Número de Expediente</label>
                    <div class="value"><?= htmlspecialchars($orden['OC_NUMERO_EXPEDIENTE'] ?? 'N/A') ?></div>
                </div>

                <div class="info-item">
                    <label>Estado</label>
                    <div class="value">
                        <?php
                        $estado = $orden['OC_ESTADO_APROBACION'] ?? 'PENDIENTE';
                        $estadoClass = strtolower($estado);
                        $estadoEmoji = $estado === 'PENDIENTE' ? '⏳' : ($estado === 'APROBADO' ? '✅' : '❌');
                        ?>
                        <span class="estado <?= $estadoClass ?>"><?= $estadoEmoji ?> <?= htmlspecialchars($estado) ?></span>
                    </div>
                </div>

                <div class="info-item">
                    <label>Cliente</label>
                    <div class="value"><?= htmlspecialchars($orden['OC_COMPRADOR_NOMBRE'] ?? 'N/A') ?></div>
                </div>

                <div class="info-item">
                    <label>Asesor de Venta</label>
                    <div class="value"><?= htmlspecialchars($orden['OC_ASESOR_VENTA'] ?? 'N/A') ?></div>
                </div>

                <div class="info-item">
                    <label>Marca del Vehículo</label>
                    <div class="value"><?= htmlspecialchars($orden['OC_VEHICULO_MARCA'] ?? 'N/A') ?></div>
                </div>

                <div class="info-item">
                    <label>Modelo del Vehículo</label>
                    <div class="value"><?= htmlspecialchars($orden['OC_VEHICULO_MODELO'] ?? 'N/A') ?></div>
                </div>

                <div class="info-item">
                    <label>Chasis</label>
                    <div class="value"><?= htmlspecialchars($orden['OC_VEHICULO_CHASIS'] ?? 'N/A') ?></div>
                </div>

                <div class="info-item">
                    <label>Precio de Venta</label>
                    <div class="value">
                        <?php 
                        $precio = $orden['OC_PRECIO_VENTA'] ?? 0;
                        echo $precio ? 'S/ ' . number_format($precio, 2) : 'N/A';
                        ?>
                    </div>
                </div>

                <div class="info-item">
                    <label>Fecha de Aprobación</label>
                    <div class="value">
                        <?php 
                        if (!empty($orden['OC_FECHA_APROBACION'])) {
                            if ($orden['OC_FECHA_APROBACION'] instanceof DateTime) {
                                echo $orden['OC_FECHA_APROBACION']->format('d/m/Y H:i');
                            } else {
                                echo htmlspecialchars(date('d/m/Y H:i', strtotime($orden['OC_FECHA_APROBACION'])));
                            }
                        } else {
                            echo 'Pendiente';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="observaciones">
                <label for="observaciones">Observaciones (opcional)</label>
                <textarea id="observaciones" placeholder="Ingrese observaciones sobre esta decisión..."></textarea>
            </div>

            <div class="actions">
                <?php if (($orden['OC_ESTADO_APROBACION'] ?? 'PENDIENTE') === 'PENDIENTE'): ?>
                <button class="btn btn-aprobar" onclick="procesarDecision('aprobar')" id="btnAprobar">
                    ✓ Aprobar Orden
                </button>
                <button class="btn btn-rechazar" onclick="procesarDecision('rechazar')" id="btnRechazar">
                    ✗ Rechazar Orden
                </button>
                <?php else: ?>
                <div style="text-align: center; color: #666; padding: 20px;">
                    Esta orden ya fue procesada (<?= htmlspecialchars($orden['OC_ESTADO_APROBACION']) ?>)
                </div>
                <?php endif; ?>
            </div>

            <div id="mensaje" class="mensaje"></div>
        </div>
    </div>

    <script>
        function procesarDecision(accion) {
            const observaciones = document.getElementById('observaciones').value;
            const mensaje = document.getElementById('mensaje');
            const btnAprobar = document.getElementById('btnAprobar');
            const btnRechazar = document.getElementById('btnRechazar');

            // Confirmar acción
            const textoConfirmacion = accion === 'aprobar' 
                ? '¿Está seguro de APROBAR esta orden de compra?' 
                : '¿Está seguro de RECHAZAR esta orden de compra?';

            if (!confirm(textoConfirmacion)) {
                return;
            }

            // Deshabilitar botones
            btnAprobar.disabled = true;
            btnRechazar.disabled = true;
            btnAprobar.textContent = 'Procesando...';
            btnRechazar.textContent = 'Procesando...';

            // Enviar solicitud
            const formData = new FormData();
            formData.append('orden_id', <?= $ordenId ?>);
            formData.append('accion', accion);
            formData.append('observaciones', observaciones);

            fetch('/digitalizacion-documentos/aprobacion/procesar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mensaje.className = 'mensaje success';
                    mensaje.style.display = 'block';
                    mensaje.textContent = accion === 'aprobar' 
                        ? '✓ Orden aprobada exitosamente. Se ha enviado notificación al asesor.' 
                        : '✓ Orden rechazada. Se ha enviado notificación al asesor.';
                    
                    // Ocultar botones de aprobación
                    btnAprobar.style.display = 'none';
                    btnRechazar.style.display = 'none';
                    
                    // Actualizar el estado en la interfaz INMEDIATAMENTE
                    if (accion === 'aprobar') {
                        actualizarEstadoEnInterfaz('APROBADO');
                    } else {
                        actualizarEstadoEnInterfaz('RECHAZADO');
                    }
                    
                    // Recargar después de mostrar el cambio
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    mensaje.className = 'mensaje error';
                    mensaje.style.display = 'block';
                    mensaje.textContent = '✗ Error: ' + (data.error || 'No se pudo procesar la solicitud');
                    
                    // Rehabilitar botones
                    btnAprobar.disabled = false;
                    btnRechazar.disabled = false;
                    btnAprobar.textContent = '✓ Aprobar Orden';
                    btnRechazar.textContent = '✗ Rechazar Orden';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mensaje.className = 'mensaje error';
                mensaje.style.display = 'block';
                mensaje.textContent = '✗ Error de conexión. Por favor intente nuevamente.';
                
                // Rehabilitar botones
                btnAprobar.disabled = false;
                btnRechazar.disabled = false;
                btnAprobar.textContent = '✓ Aprobar Orden';
                btnRechazar.textContent = '✗ Rechazar Orden';
            });
        }

        function actualizarEstadoEnInterfaz(nuevoEstado) {
            const estadoElement = document.querySelector('.estado');
            if (estadoElement) {
                const emoji = nuevoEstado === 'APROBADO' ? '✅' : '❌';
                const texto = nuevoEstado;
                const clase = nuevoEstado.toLowerCase();

                estadoElement.textContent = emoji + ' ' + texto;
                estadoElement.className = 'estado ' + clase;
            }
        }
    </script>
</body>
</html>
