<?php
require_once __DIR__ . '/../../config/database.php';

class Document {
    private $conn;

    public function __construct() {
        // Crear instancia de Database y obtener conexión
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function guardarOrdenCompra($data) {
        try {
            // Filtrar solo campos OC_ y truncar si es necesario
            $fields = [];
            $placeholders = [];
            $values = [];
            $maxLengths = [
                'OC_VEHICULO_ANIO_MODELO' => 10,
                // Agregar otros si es necesario
            ];
            // Campos que son DECIMAL y necesitan limpieza numérica
            $decimalFields = [
                'OC_PRECIO_VENTA', 'OC_BONO_FINANCIAMIENTO', 'OC_TOTAL_EQUIPAMIENTO',
                'OC_PRECIO_TOTAL_COMPRA', 'OC_TIPO_CAMBIO', 'OC_TIPO_CAMBIO_SOL',
                'OC_PAGO_CUENTA', 'OC_SALDO_PENDIENTE'
            ];
            foreach ($data as $key => $value) {
                if (strpos($key, 'OC_') === 0) {
                    $fields[] = $key;
                    $placeholders[] = "?";
                    $val = $value !== '' ? $value : null;
                    if ($val && isset($maxLengths[$key])) {
                        $val = substr($val, 0, $maxLengths[$key]);
                    }
                    // Limpiar y validar solo campos DECIMAL
                    if ($val && in_array($key, $decimalFields)) {
                        // Quitar símbolos comunes de moneda y espacios
                        $cleanVal = preg_replace('/[$ ,USMNusmn]+/', '', $val);
                        if (is_numeric($cleanVal)) {
                            $num = (float)$cleanVal;
                            // Verificar overflow para DECIMAL(12,2)
                            if ($num > 9999999999.99) {
                                $val = null; // O truncar, pero null para evitar error
                            } else {
                                $val = $cleanVal;
                            }
                        } else {
                            // Si no es numérico, setear a null para evitar errores de conversión
                            $val = null;
                        }
                    }
                    // Para otros campos (NVARCHAR), dejar el valor como está
                    if (!is_scalar($val) && !is_null($val)) {
                        $val = null;
                    }
                    $values[] = $val;
                }
            }

            if (empty($fields)) {
                throw new Exception("No hay campos OC_ para insertar");
            }

            $sql = "INSERT INTO SIST_ORDEN_COMPRA (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            $result = sqlsrv_query($this->conn, $sql, $values);
            if (!$result) {
                throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
            }

            // Obtener ID
            $result = sqlsrv_query($this->conn, "SELECT SCOPE_IDENTITY() AS id");
            if (!$result) {
                throw new Exception("Error getting ID: " . print_r(sqlsrv_errors(), true));
            }
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            if (!$row) {
                throw new Exception("No ID returned");
            }
            $id = $row['id'];

            // Precargar datos en otros documentos
            $this->precargarDocumentos($id, $data);

            return ['success' => true, 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function precargarDocumentos($ordenId, $data) {
        // Función para truncar
        $trunc = function($val, $len) {
            return $val ? substr($val, 0, $len) : $val;
        };

        // Acta Conocimiento Conformidad
        $sql = "INSERT INTO SIST_ACTA_CONOCIMIENTO_CONFORMIDAD (ACC_DOCUMENTO_VENTA_ID, ACC_NOMBRE_CLIENTE, ACC_DNI_CLIENTE, ACC_MARCA_VEHICULO, ACC_MODELO_VEHICULO, ACC_ANIO_VEHICULO, ACC_VIN_VEHICULO, ACC_COLOR_VEHICULO) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$ordenId, $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200), $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20), $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100), $trunc($data['OC_VEHICULO_MODELO'] ?? null, 100), $trunc($data['OC_VEHICULO_ANIO_MODELO'] ?? null, 10), $trunc($data['OC_VEHICULO_CHASIS'] ?? null, 50), $trunc($data['OC_VEHICULO_COLOR'] ?? null, 50)];
        if (!sqlsrv_query($this->conn, $sql, $params)) {
            throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
        }

        // Autorización Datos Personales
        $sql = "INSERT INTO SIST_AUTORIZACION_DATOS_PERSONALES (ADP_DOCUMENTO_VENTA_ID, ADP_NOMBRE_AUTORIZACION, ADP_DNI_AUTORIZACION) VALUES (?, ?, ?)";
        $params = [$ordenId, $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200), $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20)];
        if (!sqlsrv_query($this->conn, $sql, $params)) {
            throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
        }

        // Carta Conocimiento Aceptación
        $sql = "INSERT INTO SIST_CARTA_CONOCIMIENTO_ACEPTACION (CCA_DOCUMENTO_VENTA_ID, CCA_CLIENTE_NOMBRE_COMPLETO, CCA_CLIENTE_DOCUMENTO, CCA_VEHICULO_MARCA, CCA_VEHICULO_MODELO, CCA_VEHICULO_ANIO, CCA_VEHICULO_VIN) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [$ordenId, $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200), $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 50), $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100), $trunc($data['OC_VEHICULO_MODELO'] ?? null, 100), $trunc($data['OC_VEHICULO_ANIO_MODELO'] ?? null, 10), $trunc($data['OC_VEHICULO_CHASIS'] ?? null, 50)];
        if (!sqlsrv_query($this->conn, $sql, $params)) {
            throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
        }

        // Carta Recepción
        $sql = "INSERT INTO SIST_CARTA_RECEPCION (CR_DOCUMENTO_VENTA_ID, CR_CLIENTE_NOMBRE, CR_CLIENTE_DNI, CR_VEHICULO_MARCA, CR_VEHICULO_MODELO) VALUES (?, ?, ?, ?, ?)";
        $params = [$ordenId, $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200), $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20), $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100), $trunc($data['OC_VEHICULO_MODELO'] ?? null, 100)];
        if (!sqlsrv_query($this->conn, $sql, $params)) {
            throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
        }

        // Carta Características
        $sql = "INSERT INTO SIST_CARTA_CARACTERISTICAS (CC_DOCUMENTO_VENTA_ID, CC_CLIENTE_NOMBRE, CC_CLIENTE_DNI, CC_VEHICULO_MARCA, CC_VEHICULO_MODELO, CC_VEHICULO_ANIO_MODELO, CC_VEHICULO_CHASIS, CC_VEHICULO_MOTOR, CC_VEHICULO_COLOR, CC_PROPIETARIO_TARJETA) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$ordenId, $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200), $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20), $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100), $trunc($data['OC_VEHICULO_MODELO'] ?? null, 100), $trunc($data['OC_VEHICULO_ANIO_MODELO'] ?? null, 10), $trunc($data['OC_VEHICULO_CHASIS'] ?? null, 50), $trunc($data['OC_VEHICULO_MOTOR'] ?? null, 50), $trunc($data['OC_VEHICULO_COLOR'] ?? null, 50), $trunc($data['OC_PROPIETARIO_NOMBRE'] ?? null, 200)];
        if (!sqlsrv_query($this->conn, $sql, $params)) {
            throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
        }

        // Carta Felicitaciones
        $sql = "INSERT INTO SIST_CARTA_FELICITACIONES (CF_DOCUMENTO_VENTA_ID, CF_CLIENTE_NOMBRE, CF_VEHICULO_MARCA, CF_ASESOR_NOMBRE) VALUES (?, ?, ?, ?)";
        $params = [$ordenId, $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200), $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100), $trunc($data['OC_ASESOR_VENTA'] ?? null, 200)];
        if (!sqlsrv_query($this->conn, $sql, $params)) {
            throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
        }

        // Carta Obsequios
        $sql = "INSERT INTO SIST_CARTA_OBSEQUIOS (CO_DOCUMENTO_VENTA_ID, CO_CLIENTE_NOMBRE, CO_CLIENTE_DNI, CO_VEHICULO_MARCA, CO_VEHICULO_MODELO) VALUES (?, ?, ?, ?, ?)";
        $params = [$ordenId, $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200), $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20), $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100), $trunc($data['OC_VEHICULO_MODELO'] ?? null, 100)];
        if (!sqlsrv_query($this->conn, $sql, $params)) {
            throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
        }

        // Política Protección Datos
        $sql = "INSERT INTO SIST_POLITICA_PROTECCION_DATOS (PPD_DOCUMENTO_VENTA_ID, PPD_CLIENTE_NOMBRE, PPD_CLIENTE_DNI) VALUES (?, ?, ?)";
        $params = [$ordenId, $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200), $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20)];
        if (!sqlsrv_query($this->conn, $sql, $params)) {
            throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
        }
    }

    public function getOrdenCompra($id) {
        $sql = "SELECT * FROM SIST_ORDEN_COMPRA WHERE OC_ID = ?";
        $stmt = sqlsrv_prepare($this->conn, $sql);
        sqlsrv_execute($stmt, [$id]);
        return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }

    public function getDocumentData($documentId, $ordenId) {
        $tableMap = [
            'acta-conocimiento-conformidad' => ['table' => 'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD', 'field' => 'ACC_DOCUMENTO_VENTA_ID'],
            'actorizacion-datos-personales' => ['table' => 'SIST_AUTORIZACION_DATOS_PERSONALES', 'field' => 'ADP_DOCUMENTO_VENTA_ID'],
            'carta_conocimiento_aceptacion' => ['table' => 'SIST_CARTA_CONOCIMIENTO_ACEPTACION', 'field' => 'CCA_DOCUMENTO_VENTA_ID'],
            'carta_recepcion' => ['table' => 'SIST_CARTA_RECEPCION', 'field' => 'CR_DOCUMENTO_VENTA_ID'],
            'carta-caracteristicas' => ['table' => 'SIST_CARTA_CARACTERISTICAS', 'field' => 'CC_DOCUMENTO_VENTA_ID'],
            'carta_felicitaciones' => ['table' => 'SIST_CARTA_FELICITACIONES', 'field' => 'CF_DOCUMENTO_VENTA_ID'],
            'carta_obsequios' => ['table' => 'SIST_CARTA_OBSEQUIOS', 'field' => 'CO_DOCUMENTO_VENTA_ID'],
            'politica_proteccion_datos' => ['table' => 'SIST_POLITICA_PROTECCION_DATOS', 'field' => 'PPD_DOCUMENTO_VENTA_ID']
        ];

        if (!isset($tableMap[$documentId])) {
            return [];
        }

        $table = $tableMap[$documentId]['table'];
        $field = $tableMap[$documentId]['field'];
        $sql = "SELECT * FROM $table WHERE $field = ?";
        $stmt = sqlsrv_prepare($this->conn, $sql);
        sqlsrv_execute($stmt, [$ordenId]);
        return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }

    public function buscarVehiculoPorChasis($chasis) {
        $sql = "SELECT TOP 1
                    V.VE_CCHASIS,
                    MO.TG_CDESCRI AS MODELO,
                    CO.TG_CDESCRI AS COLOR,
                    MA.TG_CDESCRI AS MARCA,
                    V.VE_CANOFAB AS ANIO_FABRICACION,
                    CL.TG_CDESCRI AS CLASE,
                    V.VE_CVERSIO AS VERSION,
                    V.VE_CNROMOT AS MOTOR,
                    A.AR_NPRECI1 AS PRECIO
                FROM FT0002VEHI V
                LEFT JOIN AL0002TABL CL
                    ON V.VE_CCLASE = CL.TG_CCLAVE
                   AND CL.TG_CCOD = 'V9'
                LEFT JOIN AL0002TABL MA
                    ON V.VE_CMARCA = MA.TG_CCLAVE
                   AND MA.TG_CCOD = 'V7'
                LEFT JOIN AL0002TABL MO
                    ON V.VE_CMODELO = MO.TG_CCLAVE
                   AND MO.TG_CCOD = '39'
                LEFT JOIN AL0002TABL CO
                    ON V.VE_CCOLOR = CO.TG_CCLAVE
                   AND CO.TG_CCOD = 'V8'
                LEFT JOIN AL0002ARTI A
                    ON V.VE_CCHASIS = A.AR_CCODIGO
                WHERE V.VE_CCHASIS = ?
                ORDER BY V.VE_CANOFAB DESC";
        $result = sqlsrv_query($this->conn, $sql, [$chasis]);
        if (!$result) {
            return null;
        }
        $vehiculo = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

        // Consultar FSC en BD Stock
        if ($vehiculo) {
            $db = new Database();
            $stockConn = $db->getStockConnection();
            if ($stockConn) {
                $sqlStock = "SELECT STO_FSC FROM STOCK WHERE STO_CHASIS = ?";
                $resultStock = sqlsrv_query($stockConn, $sqlStock, [$chasis]);
                if ($resultStock) {
                    $stockData = sqlsrv_fetch_array($resultStock, SQLSRV_FETCH_ASSOC);
                    if ($stockData) {
                        $vehiculo['FSC'] = $stockData['STO_FSC'];
                    }
                }
                sqlsrv_close($stockConn);
            }
        }

        return $vehiculo;
    }

    public function verificarFirma($usuario, $password) {
        $sql = "SELECT firma_data FROM firmas WHERE usuario = ? AND password = ?";
        $result = sqlsrv_query($this->conn, $sql, [$usuario, $password]);
        if (!$result) {
            return null;
        }
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        return $row ? 'http://190.238.78.104:3800' . $row['firma_data'] : null;
    }
}
