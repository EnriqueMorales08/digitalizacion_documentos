<?php
require_once __DIR__ . '/../models/AuditLog.php';

class AuditController {
    private $auditModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->auditModel = new AuditLog();
        
        // Verificar que el usuario esté logueado y sea ADMIN
        $this->verificarAccesoAdmin();
    }

    /**
     * Verificar que el usuario sea administrador o jefe de marca
     * Si no lo es, redirigir al inicio
     */
    private function verificarAccesoAdmin() {
        // Verificar que esté logueado
        if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
            header('Location: /digitalizacion-documentos/auth/login');
            exit;
        }
        
        // Verificar que sea ADMIN o JEFE DE TIENDA
        $rol = $_SESSION['usuario_rol'] ?? 'USER';
        $cargo = $_SESSION['usuario_cargo'] ?? '';
        $esJefeTienda = (stripos($cargo, 'JEFE DE TIENDA') !== false);
        
        if ($rol !== 'ADMIN' && !$esJefeTienda) {
            header('Location: /digitalizacion-documentos/?error=' . urlencode('Acceso denegado. Solo administradores y jefes de tienda pueden ver los reportes de auditoría.'));
            exit;
        }
    }

    /**
     * Mostrar página principal de reportes de auditoría
     */
    public function index() {
        // Obtener filtros de la URL
        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'usuario' => $_GET['usuario'] ?? '',
            'orden_id' => $_GET['orden_id'] ?? '',
            'numero_expediente' => $_GET['numero_expediente'] ?? '',
            'document_type' => $_GET['document_type'] ?? '',
            'limit' => 50, // Mostrar 50 registros por página
            'offset' => isset($_GET['page']) ? (((int)$_GET['page'] - 1) * 50) : 0
        ];
        
        // Obtener logs con filtros
        $logs = $this->auditModel->obtenerLogs($filtros);
        
        // Contar total para paginación
        $totalLogs = $this->auditModel->contarLogs($filtros);
        $totalPaginas = ceil($totalLogs / 50);
        $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Obtener lista de usuarios para el filtro
        $usuarios = $this->auditModel->obtenerUsuariosConCambios();
        
        // Cargar vista
        require __DIR__ . '/../views/audit/index.php';
    }

    /**
     * Exportar logs a CSV
     */
    public function exportarCSV() {
        // Obtener filtros (sin límite para exportar todo)
        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'usuario' => $_GET['usuario'] ?? '',
            'orden_id' => $_GET['orden_id'] ?? '',
            'numero_expediente' => $_GET['numero_expediente'] ?? '',
            'document_type' => $_GET['document_type'] ?? '',
            'limit' => 10000, // Límite alto para exportación
            'offset' => 0
        ];
        
        $logs = $this->auditModel->obtenerLogs($filtros);
        
        // Configurar headers para descarga
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="auditoria_' . date('Y-m-d_His') . '.csv"');
        
        // Crear output
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8 (para que Excel lo reconozca)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, [
            'ID',
            'Fecha/Hora',
            'Usuario',
            'Nombre Usuario',
            'Email',
            'Rol',
            'Tipo Documento',
            'ID Documento',
            'ID Orden',
            'Nº Expediente',
            'Acción',
            'Campo Modificado',
            'Valor Anterior',
            'Valor Nuevo',
            'IP',
            'Descripción'
        ]);
        
        // Datos
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['AUDIT_ID'],
                $log['AUDIT_TIMESTAMP'],
                $log['AUDIT_USER_ID'],
                $log['AUDIT_USER_NAME'],
                $log['AUDIT_USER_EMAIL'],
                $log['AUDIT_USER_ROLE'],
                $log['AUDIT_DOCUMENT_TYPE'],
                $log['AUDIT_DOCUMENT_ID'],
                $log['AUDIT_ORDEN_ID'],
                $log['AUDIT_NUMERO_EXPEDIENTE'],
                $log['AUDIT_ACTION'],
                $log['AUDIT_FIELD_NAME'],
                $log['AUDIT_OLD_VALUE'],
                $log['AUDIT_NEW_VALUE'],
                $log['AUDIT_IP_ADDRESS'],
                $log['AUDIT_DESCRIPTION']
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Ver detalle de cambios de un documento específico (AJAX)
     */
    public function verDetalleDocumento() {
        header('Content-Type: application/json');
        
        if (!isset($_GET['orden_id'])) {
            echo json_encode(['success' => false, 'error' => 'ID de orden no especificado']);
            exit;
        }
        
        $ordenId = (int)$_GET['orden_id'];
        
        $filtros = [
            'orden_id' => $ordenId,
            'limit' => 1000,
            'offset' => 0
        ];
        
        $logs = $this->auditModel->obtenerLogs($filtros);
        
        echo json_encode([
            'success' => true,
            'logs' => $logs,
            'total' => count($logs)
        ]);
        exit;
    }

    /**
     * Obtener estadísticas de auditoría (para dashboard)
     */
    public function estadisticas() {
        header('Content-Type: application/json');
        
        try {
            // Cambios en las últimas 24 horas
            $filtros24h = [
                'fecha_desde' => date('Y-m-d', strtotime('-1 day')),
                'limit' => 10000
            ];
            $cambios24h = $this->auditModel->contarLogs($filtros24h);
            
            // Cambios en los últimos 7 días
            $filtros7d = [
                'fecha_desde' => date('Y-m-d', strtotime('-7 days')),
                'limit' => 10000
            ];
            $cambios7d = $this->auditModel->contarLogs($filtros7d);
            
            // Cambios en el último mes
            $filtros30d = [
                'fecha_desde' => date('Y-m-d', strtotime('-30 days')),
                'limit' => 10000
            ];
            $cambios30d = $this->auditModel->contarLogs($filtros30d);
            
            // Usuarios más activos (últimos 30 días)
            $usuarios = $this->auditModel->obtenerUsuariosConCambios();
            
            echo json_encode([
                'success' => true,
                'estadisticas' => [
                    'cambios_24h' => $cambios24h,
                    'cambios_7d' => $cambios7d,
                    'cambios_30d' => $cambios30d,
                    'usuarios_activos' => count($usuarios)
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        
        exit;
    }
}
