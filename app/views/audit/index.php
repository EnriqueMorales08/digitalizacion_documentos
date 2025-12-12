<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Auditoría - Sistema de Digitalización</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .audit-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
            margin: 20px auto;
            max-width: 1400px;
        }
        .audit-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .audit-header h1 {
            color: #667eea;
            font-weight: bold;
            margin: 0;
        }
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .filter-section h5 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .audit-table {
            font-size: 0.9rem;
        }
        .audit-table th {
            background: #667eea;
            color: white;
            font-weight: 600;
            border: none;
            padding: 12px 8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .audit-table td {
            vertical-align: middle;
            padding: 10px 8px;
        }
        .badge-action {
            font-size: 0.75rem;
            padding: 5px 10px;
        }
        .old-value, .new-value {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }
        .old-value {
            color: #dc3545;
            text-decoration: line-through;
        }
        .new-value {
            color: #28a745;
            font-weight: 600;
        }
        .pagination-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .stats-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }
        .stats-card p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .btn-export {
            background: #28a745;
            color: white;
            border: none;
        }
        .btn-export:hover {
            background: #218838;
            color: white;
        }
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="audit-container">
        <!-- Header -->
        <div class="audit-header d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-clipboard-list"></i> Reporte de Auditoría</h1>
                <p class="text-muted mb-0">Monitoreo de cambios en documentos del sistema</p>
            </div>
            <div>
                <a href="/digitalizacion-documentos/" class="btn btn-outline-secondary">
                    <i class="fas fa-home"></i> Inicio
                </a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>" 
                   onclick="window.location.href='/digitalizacion-documentos/audit/exportar-csv?<?php echo http_build_query($_GET); ?>'; return false;"
                   class="btn btn-export">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </a>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?php echo number_format($totalLogs); ?></h3>
                    <p>Total de Registros</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h3><?php echo count($usuarios); ?></h3>
                    <p>Usuarios Activos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <h3><?php echo $totalPaginas; ?></h3>
                    <p>Páginas</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <h3><?php echo $paginaActual; ?></h3>
                    <p>Página Actual</p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-section">
            <h5><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
            <form method="GET" action="/digitalizacion-documentos/audit">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Fecha Desde</label>
                        <input type="date" name="fecha_desde" class="form-control" 
                               value="<?php echo htmlspecialchars($_GET['fecha_desde'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" 
                               value="<?php echo htmlspecialchars($_GET['fecha_hasta'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Usuario</label>
                        <select name="usuario" class="form-select">
                            <option value="">Todos los usuarios</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?php echo htmlspecialchars($usuario['AUDIT_USER_ID']); ?>"
                                        <?php echo (($_GET['usuario'] ?? '') === $usuario['AUDIT_USER_ID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($usuario['AUDIT_USER_NAME']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nº Expediente</label>
                        <input type="text" name="numero_expediente" class="form-control" 
                               placeholder="Ej: 2024110001"
                               value="<?php echo htmlspecialchars($_GET['numero_expediente'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ID Orden</label>
                        <input type="number" name="orden_id" class="form-control" 
                               placeholder="ID de orden"
                               value="<?php echo htmlspecialchars($_GET['orden_id'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo Documento</label>
                        <select name="document_type" class="form-select">
                            <option value="">Todos los tipos</option>
                            <option value="ORDEN_COMPRA" <?php echo (($_GET['document_type'] ?? '') === 'ORDEN_COMPRA') ? 'selected' : ''; ?>>Orden de Compra</option>
                            <option value="ACTA" <?php echo (($_GET['document_type'] ?? '') === 'ACTA') ? 'selected' : ''; ?>>Acta</option>
                            <option value="CARTA" <?php echo (($_GET['document_type'] ?? '') === 'CARTA') ? 'selected' : ''; ?>>Carta</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="/digitalizacion-documentos/audit" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de logs -->
        <?php if (empty($logs)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h4>No se encontraron registros</h4>
                <p>No hay cambios registrados con los filtros seleccionados.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover audit-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Fecha/Hora</th>
                            <th style="width: 150px;">Usuario</th>
                            <th style="width: 80px;">Rol</th>
                            <th style="width: 100px;">Nº Expediente</th>
                            <th style="width: 80px;">Acción</th>
                            <th style="width: 180px;">Campo</th>
                            <th style="width: 200px;">Valor Anterior</th>
                            <th style="width: 200px;">Valor Nuevo</th>
                            <th style="width: 100px;">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <small>
                                        <?php 
                                        $timestamp = $log['AUDIT_TIMESTAMP'];
                                        if ($timestamp instanceof DateTime) {
                                            echo $timestamp->format('d/m/Y H:i:s');
                                        } else {
                                            echo date('d/m/Y H:i:s', strtotime($timestamp));
                                        }
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($log['AUDIT_USER_NAME'] ?? 'N/A'); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($log['AUDIT_USER_ID'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <?php 
                                    $rolClass = ($log['AUDIT_USER_ROLE'] === 'ADMIN') ? 'bg-danger' : 'bg-primary';
                                    ?>
                                    <span class="badge <?php echo $rolClass; ?>">
                                        <?php echo htmlspecialchars($log['AUDIT_USER_ROLE'] ?? 'USER'); ?>
                                    </span>
                                </td>
                                <td>
                                    <code><?php echo htmlspecialchars($log['AUDIT_NUMERO_EXPEDIENTE'] ?? 'N/A'); ?></code>
                                </td>
                                <td>
                                    <?php 
                                    $action = $log['AUDIT_ACTION'] ?? 'UPDATE';
                                    $actionClass = 'bg-info';
                                    if ($action === 'INSERT') $actionClass = 'bg-success';
                                    if ($action === 'DELETE') $actionClass = 'bg-danger';
                                    ?>
                                    <span class="badge badge-action <?php echo $actionClass; ?>">
                                        <?php echo htmlspecialchars($action); ?>
                                    </span>
                                </td>
                                <td>
                                    <code style="font-size: 0.8rem;">
                                        <?php echo htmlspecialchars($log['AUDIT_FIELD_NAME'] ?? ''); ?>
                                    </code>
                                </td>
                                <td>
                                    <span class="old-value" title="<?php echo htmlspecialchars($log['AUDIT_OLD_VALUE'] ?? ''); ?>">
                                        <?php echo htmlspecialchars(substr($log['AUDIT_OLD_VALUE'] ?? '[VACÍO]', 0, 50)); ?>
                                        <?php if (strlen($log['AUDIT_OLD_VALUE'] ?? '') > 50) echo '...'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="new-value" title="<?php echo htmlspecialchars($log['AUDIT_NEW_VALUE'] ?? ''); ?>">
                                        <?php echo htmlspecialchars(substr($log['AUDIT_NEW_VALUE'] ?? '[VACÍO]', 0, 50)); ?>
                                        <?php if (strlen($log['AUDIT_NEW_VALUE'] ?? '') > 50) echo '...'; ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?php echo htmlspecialchars($log['AUDIT_IP_ADDRESS'] ?? 'N/A'); ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <div class="pagination-container">
                    <nav>
                        <ul class="pagination">
                            <!-- Botón anterior -->
                            <?php if ($paginaActual > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $paginaActual - 1])); ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <!-- Números de página -->
                            <?php 
                            $rango = 2; // Mostrar 2 páginas a cada lado
                            $inicio = max(1, $paginaActual - $rango);
                            $fin = min($totalPaginas, $paginaActual + $rango);
                            
                            for ($i = $inicio; $i <= $fin; $i++): 
                            ?>
                                <li class="page-item <?php echo ($i === $paginaActual) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Botón siguiente -->
                            <?php if ($paginaActual < $totalPaginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $paginaActual + 1])); ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
