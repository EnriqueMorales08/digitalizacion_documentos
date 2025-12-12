<?php
require_once __DIR__ . '/../../config/database.php';

class AuditLog {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Registrar un cambio en la auditoría
     * 
     * @param array $data Datos del cambio:
     *   - document_type: Tipo de documento (ORDEN_COMPRA, ACTA, etc.)
     *   - document_id: ID del documento
     *   - orden_id: ID de la orden de compra
     *   - numero_expediente: Número de expediente
     *   - action: INSERT, UPDATE, DELETE
     *   - field_name: Nombre del campo modificado
     *   - old_value: Valor anterior
     *   - new_value: Valor nuevo
     *   - description: Descripción adicional (opcional)
     * @return bool True si se registró correctamente
     */
    public function registrarCambio($data) {
        try {
            // Obtener información del usuario de la sesión
            $userId = $_SESSION['usuario'] ?? 'SISTEMA';
            $userName = $_SESSION['usuario_nombre_completo'] ?? 'Sistema';
            $userEmail = $_SESSION['usuario_email'] ?? '';
            $userRole = $_SESSION['usuario_rol'] ?? 'USER';
            
            // Obtener IP del usuario
            $ipAddress = $this->getClientIP();
            
            // Obtener ID de sesión
            $sessionId = session_id();
            
            $sql = "INSERT INTO SIST_AUDIT_LOG (
                AUDIT_USER_ID, 
                AUDIT_USER_NAME, 
                AUDIT_USER_EMAIL, 
                AUDIT_USER_ROLE,
                AUDIT_DOCUMENT_TYPE, 
                AUDIT_DOCUMENT_ID, 
                AUDIT_ORDEN_ID, 
                AUDIT_NUMERO_EXPEDIENTE,
                AUDIT_ACTION, 
                AUDIT_FIELD_NAME, 
                AUDIT_OLD_VALUE, 
                AUDIT_NEW_VALUE,
                AUDIT_IP_ADDRESS,
                AUDIT_SESSION_ID,
                AUDIT_DESCRIPTION
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $userId,
                $userName,
                $userEmail,
                $userRole,
                $data['document_type'] ?? 'UNKNOWN',
                $data['document_id'] ?? null,
                $data['orden_id'] ?? null,
                $data['numero_expediente'] ?? null,
                $data['action'] ?? 'UPDATE',
                $data['field_name'] ?? '',
                $data['old_value'] ?? null,
                $data['new_value'] ?? null,
                $ipAddress,
                $sessionId,
                $data['description'] ?? null
            ];
            
            $result = sqlsrv_query($this->conn, $sql, $params);
            
            if (!$result) {
                error_log("Error al registrar auditoría: " . print_r(sqlsrv_errors(), true));
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Excepción en registrarCambio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Registrar múltiples cambios en una sola operación
     * Útil cuando se actualizan varios campos a la vez
     * 
     * @param array $cambios Array de cambios, cada uno con la estructura de registrarCambio
     * @return bool True si todos se registraron correctamente
     */
    public function registrarCambiosMultiples($cambios) {
        $todosExitosos = true;
        
        foreach ($cambios as $cambio) {
            if (!$this->registrarCambio($cambio)) {
                $todosExitosos = false;
            }
        }
        
        return $todosExitosos;
    }

    /**
     * Comparar dos arrays y generar lista de cambios
     * 
     * @param array $datosAnteriores Datos antes del cambio
     * @param array $datosNuevos Datos después del cambio
     * @param array $camposExcluidos Campos que no se deben auditar
     * @return array Lista de cambios detectados
     */
    public function compararCambios($datosAnteriores, $datosNuevos, $camposExcluidos = []) {
        $cambios = [];
        
        // Campos que por defecto no se auditan (timestamps, IDs auto-generados, etc.)
        $excluirPorDefecto = [
            'OC_FECHA_CREACION',
            'OC_FECHA_APROBACION',
            'ACC_FECHA_CREACION',
            'ADP_FECHA_CREACION',
            'CCA_FECHA_CREACION',
            'CR_FECHA_CREACION',
            'CC_FECHA_CREACION',
            'CCB_FECHA_CREACION',
            'PPD_FECHA_CREACION'
        ];
        
        $camposExcluidos = array_merge($camposExcluidos, $excluirPorDefecto);
        
        // Comparar cada campo
        foreach ($datosNuevos as $campo => $valorNuevo) {
            // Saltar campos excluidos
            if (in_array($campo, $camposExcluidos)) {
                continue;
            }
            
            // Obtener valor anterior (si existe)
            $valorAnterior = $datosAnteriores[$campo] ?? null;
            
            // Normalizar valores para comparación
            $valorAnteriorNorm = $this->normalizarValor($valorAnterior);
            $valorNuevoNorm = $this->normalizarValor($valorNuevo);
            
            // Si son diferentes, registrar el cambio
            if ($valorAnteriorNorm !== $valorNuevoNorm) {
                $cambios[] = [
                    'field_name' => $campo,
                    'old_value' => $this->formatearValorParaAuditoria($valorAnterior),
                    'new_value' => $this->formatearValorParaAuditoria($valorNuevo)
                ];
            }
        }
        
        return $cambios;
    }

    /**
     * Normalizar valor para comparación (maneja nulos, strings vacíos, números, fechas, etc.)
     */
    private function normalizarValor($valor) {
        // DateTime a string (solo fecha, sin hora)
        if ($valor instanceof DateTime) {
            return $valor->format('Y-m-d');
        }
        
        // Null y string vacío se consideran iguales
        if ($valor === null || $valor === '') {
            return null;
        }
        
        // Trimear strings
        if (is_string($valor)) {
            $valor = trim($valor);
            
            // Si es un string vacío después del trim, retornar null
            if ($valor === '') {
                return null;
            }
            
            // Si parece una fecha, normalizarla (solo la parte de fecha, sin hora)
            if ($this->esFecha($valor)) {
                try {
                    $fecha = new DateTime($valor);
                    return $fecha->format('Y-m-d');
                } catch (Exception $e) {
                    // Si falla el parseo, continuar con el valor original
                }
            }
            
            // Si parece un número, normalizarlo
            if (is_numeric($valor)) {
                // Convertir a float para comparación consistente
                $valorFloat = floatval($valor);
                // Retornar como string con formato consistente (2 decimales para precios)
                return number_format($valorFloat, 2, '.', '');
            }
            
            return $valor;
        }
        
        // Si es un número (int o float), normalizarlo
        if (is_numeric($valor)) {
            $valorFloat = floatval($valor);
            return number_format($valorFloat, 2, '.', '');
        }
        
        return $valor;
    }
    
    /**
     * Detectar si un string parece una fecha
     */
    private function esFecha($valor) {
        // Patrones comunes de fecha
        $patronesFecha = [
            '/^\d{4}-\d{2}-\d{2}$/',                    // 2025-11-04
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',  // 2025-11-04 00:00:00
            '/^\d{2}\/\d{2}\/\d{4}$/',                  // 04/11/2025
            '/^\d{4}\/\d{2}\/\d{2}$/'                   // 2025/11/04
        ];
        
        foreach ($patronesFecha as $patron) {
            if (preg_match($patron, $valor)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Formatear valor para mostrar en auditoría
     */
    private function formatearValorParaAuditoria($valor) {
        if ($valor === null || $valor === '') {
            return '[VACÍO]';
        }
        
        if ($valor instanceof DateTime) {
            return $valor->format('Y-m-d H:i:s');
        }
        
        // Truncar valores muy largos
        $valorStr = (string)$valor;
        if (strlen($valorStr) > 500) {
            return substr($valorStr, 0, 497) . '...';
        }
        
        return $valorStr;
    }

    /**
     * Obtener la IP real del cliente (considerando proxies)
     */
    private function getClientIP() {
        $ipAddress = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
        
        return $ipAddress;
    }

    /**
     * Obtener logs de auditoría con filtros
     * 
     * @param array $filtros Filtros opcionales:
     *   - fecha_desde: Fecha inicio (Y-m-d)
     *   - fecha_hasta: Fecha fin (Y-m-d)
     *   - usuario: Usuario específico
     *   - orden_id: ID de orden
     *   - numero_expediente: Número de expediente
     *   - document_type: Tipo de documento
     *   - limit: Límite de registros (default: 100)
     *   - offset: Offset para paginación (default: 0)
     * @return array Lista de logs
     */
    public function obtenerLogs($filtros = []) {
        try {
            $where = [];
            $params = [];
            
            // Filtro por fecha desde
            if (!empty($filtros['fecha_desde'])) {
                $where[] = "AUDIT_TIMESTAMP >= ?";
                $params[] = $filtros['fecha_desde'] . ' 00:00:00';
            }
            
            // Filtro por fecha hasta
            if (!empty($filtros['fecha_hasta'])) {
                $where[] = "AUDIT_TIMESTAMP <= ?";
                $params[] = $filtros['fecha_hasta'] . ' 23:59:59';
            }
            
            // Filtro por usuario
            if (!empty($filtros['usuario'])) {
                $where[] = "AUDIT_USER_ID = ?";
                $params[] = $filtros['usuario'];
            }
            
            // Filtro por orden ID
            if (!empty($filtros['orden_id'])) {
                $where[] = "AUDIT_ORDEN_ID = ?";
                $params[] = $filtros['orden_id'];
            }
            
            // Filtro por número de expediente
            if (!empty($filtros['numero_expediente'])) {
                $where[] = "AUDIT_NUMERO_EXPEDIENTE = ?";
                $params[] = $filtros['numero_expediente'];
            }
            
            // Filtro por tipo de documento
            if (!empty($filtros['document_type'])) {
                $where[] = "AUDIT_DOCUMENT_TYPE = ?";
                $params[] = $filtros['document_type'];
            }
            
            // Construir WHERE clause
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            // Límite y offset
            $limit = $filtros['limit'] ?? 100;
            $offset = $filtros['offset'] ?? 0;
            
            $sql = "SELECT * FROM SIST_AUDIT_LOG 
                    $whereClause 
                    ORDER BY AUDIT_TIMESTAMP DESC 
                    OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
            
            $params[] = $offset;
            $params[] = $limit;
            
            $result = sqlsrv_query($this->conn, $sql, $params);
            
            if (!$result) {
                error_log("Error al obtener logs: " . print_r(sqlsrv_errors(), true));
                return [];
            }
            
            $logs = [];
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                // Convertir DateTime a string
                if ($row['AUDIT_TIMESTAMP'] instanceof DateTime) {
                    $row['AUDIT_TIMESTAMP'] = $row['AUDIT_TIMESTAMP']->format('Y-m-d H:i:s');
                }
                $logs[] = $row;
            }
            
            return $logs;
            
        } catch (Exception $e) {
            error_log("Excepción en obtenerLogs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar total de logs con filtros (para paginación)
     */
    public function contarLogs($filtros = []) {
        try {
            $where = [];
            $params = [];
            
            // Aplicar los mismos filtros que en obtenerLogs
            if (!empty($filtros['fecha_desde'])) {
                $where[] = "AUDIT_TIMESTAMP >= ?";
                $params[] = $filtros['fecha_desde'] . ' 00:00:00';
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $where[] = "AUDIT_TIMESTAMP <= ?";
                $params[] = $filtros['fecha_hasta'] . ' 23:59:59';
            }
            
            if (!empty($filtros['usuario'])) {
                $where[] = "AUDIT_USER_ID = ?";
                $params[] = $filtros['usuario'];
            }
            
            if (!empty($filtros['orden_id'])) {
                $where[] = "AUDIT_ORDEN_ID = ?";
                $params[] = $filtros['orden_id'];
            }
            
            if (!empty($filtros['numero_expediente'])) {
                $where[] = "AUDIT_NUMERO_EXPEDIENTE = ?";
                $params[] = $filtros['numero_expediente'];
            }
            
            if (!empty($filtros['document_type'])) {
                $where[] = "AUDIT_DOCUMENT_TYPE = ?";
                $params[] = $filtros['document_type'];
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            $sql = "SELECT COUNT(*) as total FROM SIST_AUDIT_LOG $whereClause";
            
            $result = sqlsrv_query($this->conn, $sql, $params);
            
            if (!$result) {
                return 0;
            }
            
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            return $row['total'] ?? 0;
            
        } catch (Exception $e) {
            error_log("Excepción en contarLogs: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener lista de usuarios que han realizado cambios (para filtro)
     */
    public function obtenerUsuariosConCambios() {
        try {
            $sql = "SELECT DISTINCT AUDIT_USER_ID, AUDIT_USER_NAME 
                    FROM SIST_AUDIT_LOG 
                    WHERE AUDIT_USER_ID IS NOT NULL 
                    ORDER BY AUDIT_USER_NAME";
            
            $result = sqlsrv_query($this->conn, $sql);
            
            if (!$result) {
                return [];
            }
            
            $usuarios = [];
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $usuarios[] = $row;
            }
            
            return $usuarios;
            
        } catch (Exception $e) {
            error_log("Excepción en obtenerUsuariosConCambios: " . $e->getMessage());
            return [];
        }
    }
}
