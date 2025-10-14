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
                        <p class="mb-0"><strong>Chasis:</strong> <?= htmlspecialchars($ordenCompra['OC_VEHICULO_CHASIS'] ?? 'N/A') ?></p>
                    </div>
                </div>
            </div>

            <!-- Documentos -->
            <h4 class="mb-3"><i class="bi bi-files"></i> Documentos del Expediente</h4>

            <!-- Orden de Compra (siempre existe) -->
            <div class="documento-card documento-disponible">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1"><i class="bi bi-file-earmark-text"></i> Orden de Compra</h5>
                        <small class="text-muted">Documento principal del expediente</small>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="/digitalizacion-documentos/documents/show?id=orden-compra" 
                           class="btn btn-primary btn-sm me-2" target="_blank">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                        <button onclick="imprimirDocumento('<?= htmlspecialchars($ordenCompra['OC_NUMERO_EXPEDIENTE']) ?>', 'orden-compra')" 
                           class="btn btn-success btn-sm">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>

            <!-- Otros Documentos -->
            <?php 
            // Determinar qué carta de características mostrar según el banco
            $formaPago = trim($ordenCompra['OC_FORMA_PAGO'] ?? '');
            $bancoAbono = trim($ordenCompra['OC_BANCO_ABONO'] ?? '');
            
            $todosDocumentos = [
                'acta-conocimiento-conformidad' => 'Acta Conocimiento Conformidad',
                'actorizacion-datos-personales' => 'Autorización Datos Personales',
                'carta_conocimiento_aceptacion' => 'Carta Conocimiento Aceptación',
                'carta_recepcion' => 'Carta Recepción',
                'carta_felicitaciones' => 'Carta Felicitaciones',
                'carta_obsequios' => 'Carta Obsequios',
                'politica_proteccion_datos' => 'Política Protección Datos'
            ];
            
            // Solo agregar carta de características si la forma de pago es CRÉDITO
            if ($formaPago === 'CRÉDITO') {
                if ($bancoAbono === 'Banco Interamericano de Finanzas') {
                    $todosDocumentos['carta_caracteristicas_banbif'] = 'Carta Características Banbif';
                } else {
                    $todosDocumentos['carta-caracteristicas'] = 'Carta Características';
                }
            }

            $documentosExistentes = [];
            foreach ($documentos as $doc) {
                $documentosExistentes[] = $doc['nombre'];
            }
            ?>

            <?php foreach ($todosDocumentos as $docId => $docNombre): ?>
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
                                <a href="/digitalizacion-documentos/documents/show?id=<?= $docId ?>" 
                                   class="btn btn-primary btn-sm me-2" target="_blank">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <button onclick="imprimirDocumento('<?= urlencode($ordenCompra['OC_NUMERO_EXPEDIENTE']) ?>', '<?= $docId ?>')" 
                                   class="btn btn-success btn-sm">
                                    <i class="bi bi-printer"></i> Imprimir
                                </button>
                            <?php else: ?>
                                <a href="/digitalizacion-documentos/documents/show?id=<?= $docId ?>" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Generar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function imprimirTodos(numeroExpediente) {
            const url = `/digitalizacion-documentos/expedientes/imprimir-todos?numero=${numeroExpediente}`;
            const ventana = window.open(url, '_blank');
            
            // Esperar a que la ventana cargue completamente
            ventana.addEventListener('load', function() {
                // Esperar 2 segundos adicionales para que todos los documentos se rendericen
                setTimeout(() => {
                    ventana.print();
                }, 2000);
            });
        }
        
        function imprimirDocumento(numeroExpediente, documentoId) {
            const url = `/digitalizacion-documentos/expedientes/imprimir-documento?numero=${numeroExpediente}&documento=${documentoId}`;
            const ventana = window.open(url, '_blank');
            
            ventana.addEventListener('load', function() {
                setTimeout(() => {
                    ventana.print();
                }, 1000);
            });
        }
    </script>
</body>
</html>
