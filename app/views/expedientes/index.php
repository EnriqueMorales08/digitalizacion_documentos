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
                <div>
                    <a href="/digitalizacion-documentos/documents" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Volver al Inicio
                    </a>
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
                                    <a href="/digitalizacion-documentos/expedientes/imprimir-todos?numero=<?= urlencode($orden['OC_NUMERO_EXPEDIENTE']) ?>" 
                                       class="btn btn-success btn-action" target="_blank">
                                        <i class="bi bi-printer"></i> Imprimir Todo
                                    </a>
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
</body>
</html>
