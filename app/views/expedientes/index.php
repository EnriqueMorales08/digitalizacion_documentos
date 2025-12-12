<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Expedientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        .search-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .expediente-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .expediente-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        .expediente-numero {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }
        .btn-action {
            margin: 5px;
        }
        .pagination-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2"><i class="bi bi-folder2-open"></i> Gestión de Expedientes</h1>
                    <p class="text-muted mb-0">Buscar, visualizar e imprimir documentos por número de expediente</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/digitalizacion-documentos/documents" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Volver al Inicio
                    </a>
                    <button onclick="nuevaOrdenCompra()" class="btn btn-primary" style="background: linear-gradient(135deg, #3b82f6, #1e3a8a); border: none; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                        <i class="bi bi-file-earmark-plus"></i> Nueva Orden de Compra
                    </button>
                </div>
            </div>
        </div>

        <!-- Búsqueda -->
        <div class="search-box">
            <form method="GET" action="/digitalizacion-documentos/expedientes" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por número de expediente, nombre del cliente o DNI..." 
                               value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Buscar</button>
                </div>
            </form>
            <?php if (!empty($search)): ?>
                <div class="mt-2">
                    <a href="/digitalizacion-documentos/expedientes" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar búsqueda
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Resultados -->
        <div class="header-card">
            <h5 class="mb-3">
                <i class="bi bi-list-ul"></i> Expedientes Registrados 
                <span class="badge bg-primary"><?= $resultado['total'] ?? 0 ?></span>
            </h5>

            <?php if (empty($resultado['data'])): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No se encontraron expedientes.
                    <?php if (!empty($search)): ?>
                        Intenta con otros términos de búsqueda.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($resultado['data'] as $orden): ?>
                    <div class="expediente-card">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="expediente-numero">
                                    <i class="bi bi-file-earmark-text"></i> 
                                    <?= htmlspecialchars($orden['OC_NUMERO_EXPEDIENTE'] ?? 'N/A') ?>
                                </div>
                                <small class="text-muted">
                                    <?php 
                                    if (isset($orden['OC_FECHA_CREACION']) && $orden['OC_FECHA_CREACION'] instanceof DateTime) {
                                        echo $orden['OC_FECHA_CREACION']->format('d/m/Y H:i');
                                    }
                                    ?>
                                </small>
                                <br>
                                <?php 
                                $estado = $orden['OC_ESTADO_APROBACION'] ?? 'PENDIENTE';
                                $badgeClass = $estado === 'APROBADO' ? 'bg-success' : ($estado === 'RECHAZADO' ? 'bg-danger' : 'bg-warning text-dark');
                                $icono = $estado === 'APROBADO' ? 'check-circle' : ($estado === 'RECHAZADO' ? 'x-circle' : 'clock');
                                ?>
                                <span class="badge <?= $badgeClass ?> mt-2">
                                    <i class="bi bi-<?= $icono ?>"></i> <?= $estado ?>
                                </span>
                            </div>
                            <div class="col-md-5">
                                <strong><i class="bi bi-person"></i> Cliente:</strong> 
                                <?= htmlspecialchars($orden['OC_COMPRADOR_NOMBRE'] ?? 'N/A') ?><br>
                                <strong><i class="bi bi-card-text"></i> DNI:</strong> 
                                <?= htmlspecialchars($orden['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? 'N/A') ?><br>
                                <strong><i class="bi bi-car-front"></i> Vehículo:</strong> 
                                <?= htmlspecialchars($orden['OC_VEHICULO_MARCA'] ?? '') ?> 
                                <?= htmlspecialchars($orden['OC_VEHICULO_MODELO'] ?? '') ?>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="/digitalizacion-documentos/expedientes/ver?numero=<?= urlencode($orden['OC_NUMERO_EXPEDIENTE']) ?>" 
                                   class="btn btn-primary btn-action">
                                    <i class="bi bi-eye"></i> Ver Documentos
                                </a>
                                <?php if ($estado === 'APROBADO'): ?>
                                    <button onclick="imprimirTodos('<?= urlencode($orden['OC_NUMERO_EXPEDIENTE']) ?>')" 
                                       class="btn btn-success btn-action">
                                        <i class="bi bi-printer"></i> Imprimir Todo
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-action" disabled 
                                            title="Solo se puede imprimir cuando esté APROBADO">
                                        <i class="bi bi-printer"></i> Imprimir Todo
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Paginación -->
                <?php if ($resultado['pages'] > 1): ?>
                    <div class="pagination-container">
                        <nav>
                            <ul class="pagination justify-content-center mb-0">
                                <?php for ($i = 1; $i <= $resultado['pages']; $i++): ?>
                                    <li class="page-item <?= $i == $resultado['current_page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
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
            iframe.src = '/digitalizacion-documentos/expedientes/imprimir-todos?numero=' + decodeURIComponent(numeroExpediente);
            
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
                        window.open('/digitalizacion-documentos/expedientes/imprimir-todos?numero=' + decodeURIComponent(numeroExpediente), '_blank');
                        document.body.removeChild(iframe);
                    }
                }, 6000); // Esperar 6 segundos para que carguen todos los documentos
            };
        }

        // Función para crear nueva orden de compra
        function nuevaOrdenCompra() {
            if (confirm('¿Deseas crear una nueva orden de compra? Se abrirá un formulario en blanco.')) {
                // Limpiar sesión en el servidor
                fetch('/digitalizacion-documentos/documents/limpiar-sesion', {
                    method: 'POST'
                })
                .then(() => {
                    // Redirigir a orden de compra nueva
                    window.location.href = '/digitalizacion-documentos/documents/show?id=orden-compra';
                })
                .catch(error => {
                    console.error('Error al limpiar sesión:', error);
                    // Redirigir de todas formas
                    window.location.href = '/digitalizacion-documentos/documents/show?id=orden-compra';
                });
            }
        }

        // Sistema de actualización automática de estados
        let estadosOriginales = {};
        
        // Función para obtener todas las órdenes visibles y sus estados actuales
        function obtenerOrdenesVisibles() {
            const ordenes = [];
            document.querySelectorAll('.expediente-card').forEach(card => {
                const badge = card.querySelector('.badge');
                if (badge) {
                    const estadoActual = badge.textContent.trim().replace(/[✓✗⏳]/g, '').trim();
                    // Extraer el ID de la orden del botón "Ver Documentos"
                    const verBtn = card.querySelector('a[href*="expedientes/ver"]');
                    if (verBtn) {
                        const href = verBtn.getAttribute('href');
                        const numeroExpediente = new URLSearchParams(href.split('?')[1]).get('numero');
                        if (numeroExpediente) {
                            ordenes.push({
                                numeroExpediente: numeroExpediente,
                                estadoActual: estadoActual,
                                badge: badge,
                                card: card
                            });
                            // Guardar estado original si no existe
                            if (!estadosOriginales[numeroExpediente]) {
                                estadosOriginales[numeroExpediente] = estadoActual;
                            }
                        }
                    }
                }
            });
            return ordenes;
        }

        // Función para actualizar el estado de una orden en la interfaz
        function actualizarEstadoOrden(orden, nuevoEstado) {
            const badge = orden.badge;
            const card = orden.card;
            
            // Actualizar badge
            let badgeClass, icono;
            if (nuevoEstado === 'APROBADO') {
                badgeClass = 'bg-success';
                icono = 'check-circle';
            } else if (nuevoEstado === 'RECHAZADO') {
                badgeClass = 'bg-danger';
                icono = 'x-circle';
            } else {
                badgeClass = 'bg-warning text-dark';
                icono = 'clock';
            }
            
            badge.className = 'badge ' + badgeClass + ' mt-2';
            badge.innerHTML = '<i class="bi bi-' + icono + '"></i> ' + nuevoEstado;
            
            // Actualizar botón de imprimir
            const imprimirBtn = card.querySelector('button[onclick*="imprimirTodos"]');
            if (imprimirBtn) {
                if (nuevoEstado === 'APROBADO') {
                    imprimirBtn.disabled = false;
                    imprimirBtn.className = 'btn btn-success btn-action';
                    imprimirBtn.title = '';
                } else {
                    imprimirBtn.disabled = true;
                    imprimirBtn.className = 'btn btn-secondary btn-action';
                    imprimirBtn.title = 'Solo se puede imprimir cuando esté APROBADO';
                }
            }
            
            // Animación de actualización
            card.style.transition = 'all 0.3s ease';
            card.style.backgroundColor = '#d4edda';
            setTimeout(() => {
                card.style.backgroundColor = 'white';
            }, 2000);
            
            // Mostrar notificación
            mostrarNotificacion('Estado actualizado: ' + orden.numeroExpediente + ' ahora está ' + nuevoEstado, 'success');
        }

        // Función para mostrar notificaciones
        function mostrarNotificacion(mensaje, tipo = 'info') {
            const notif = document.createElement('div');
            notif.className = 'alert alert-' + tipo + ' alert-dismissible fade show';
            notif.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                min-width: 300px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            notif.innerHTML = `
                <i class="bi bi-${tipo === 'success' ? 'check-circle' : 'info-circle'}"></i> ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notif);
            
            // Auto-cerrar después de 5 segundos
            setTimeout(() => {
                if (notif.parentNode) {
                    notif.classList.remove('show');
                    setTimeout(() => {
                        if (notif.parentNode) {
                            notif.parentNode.removeChild(notif);
                        }
                    }, 300);
                }
            }, 5000);
        }

        // Función para verificar estados de todas las órdenes pendientes
        async function verificarEstados() {
            const ordenes = obtenerOrdenesVisibles();
            const ordenesPendientes = ordenes.filter(o => o.estadoActual === 'PENDIENTE');
            
            if (ordenesPendientes.length === 0) {
                return; // No hay órdenes pendientes, no hacer nada
            }
            
            // Verificar cada orden pendiente
            for (const orden of ordenesPendientes) {
                try {
                    // Buscar el ID de la orden desde el número de expediente
                    // Necesitamos hacer una llamada para obtener el ID
                    const response = await fetch('/digitalizacion-documentos/expedientes/buscar?numero=' + encodeURIComponent(orden.numeroExpediente));
                    const data = await response.json();
                    
                    if (data.success && data.orden && data.orden.OC_ID) {
                        const ordenId = data.orden.OC_ID;
                        
                        // Verificar el estado actual
                        const estadoResponse = await fetch('/digitalizacion-documentos/aprobacion/verificar-estado?id=' + ordenId);
                        const estadoData = await estadoResponse.json();
                        
                        if (estadoData.success && estadoData.estado !== orden.estadoActual) {
                            // El estado cambió, actualizar la interfaz
                            actualizarEstadoOrden(orden, estadoData.estado);
                            estadosOriginales[orden.numeroExpediente] = estadoData.estado;
                        }
                    }
                } catch (error) {
                    console.error('Error al verificar estado de orden:', error);
                }
            }
        }

        // Iniciar polling cada 5 segundos
        setInterval(verificarEstados, 5000);
        
        // Verificar inmediatamente al cargar la página
        setTimeout(verificarEstados, 1000);
    </script>
</body>
</html>
