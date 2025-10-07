<?php
require_once __DIR__ . '/../../config/database.php';

class Document {
    private $conn;

    public function __construct() {
        // Crear instancia de Database y obtener conexión
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    private function subirArchivo($file, $prefix = 'file') {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Crear directorio si no existe
        $uploadDir = '../uploads/'; // Relativo al script
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Ruta relativa para remoto
            return '/digitalizacion-documentos/uploads/' . $filename;
        }

        return null;
    }

    public function guardarOrdenCompra($data, $files = []) {
        try {
            // Procesar archivos primero
            $archivos = [];
            if (!empty($files)) {
                $archivos['OC_ARCHIVO_DNI'] = $this->subirArchivo($files['OC_ARCHIVO_DNI'] ?? null, 'dni');
                $archivos['OC_ARCHIVO_VOUCHER'] = $this->subirArchivo($files['OC_ARCHIVO_VOUCHER'] ?? null, 'voucher');
                $archivos['OC_ARCHIVO_PEDIDO_SALESFORCE'] = $this->subirArchivo($files['OC_ARCHIVO_PEDIDO_SALESFORCE'] ?? null, 'salesforce');
                $archivos['OC_ARCHIVO_DERIVACION_SANTANDER'] = $this->subirArchivo($files['OC_ARCHIVO_DERIVACION_SANTANDER'] ?? null, 'santander');

                // Procesar abonos en campos separados
                for ($i = 1; $i <= 6; $i++) {
                    $archivos['OC_ARCHIVO_ABONO_' . $i] = $this->subirArchivo($files['OC_ARCHIVO_ABONO_' . $i] ?? null, 'abono_' . $i);
                }

                // Procesar otros documentos en campos separados
                for ($i = 1; $i <= 6; $i++) {
                    $archivos['OC_ARCHIVO_OTROS_' . $i] = $this->subirArchivo($files['OC_ARCHIVO_OTROS_' . $i] ?? null, 'otros_' . $i);
                }
            }

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
                'OC_PAGO_CUENTA', 'OC_SALDO_PENDIENTE',
                'OC_EQUIPAMIENTO_ADICIONAL_1', 'OC_EQUIPAMIENTO_ADICIONAL_2', 'OC_EQUIPAMIENTO_ADICIONAL_3',
                'OC_EQUIPAMIENTO_ADICIONAL_4', 'OC_EQUIPAMIENTO_ADICIONAL_5'
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
                                $val = $num; // Convertir a float
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

            // Agregar campos de archivos
            foreach ($archivos as $key => $ruta) {
                if ($ruta) {
                    $fields[] = $key;
                    $placeholders[] = "?";
                    $values[] = $ruta;
                }
            }

            if (empty($fields)) {
                throw new Exception("No hay campos OC_ para insertar");
            }

            $sql = "INSERT INTO SIST_ORDEN_COMPRA (" . implode(", ", $fields) . ") OUTPUT INSERTED.OC_ID VALUES (" . implode(", ", $placeholders) . ")";
            $result = sqlsrv_query($this->conn, $sql, $values);
            if (!$result) {
                throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
            }
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            if (!$row) {
                throw new Exception("No ID returned");
            }
            $id = $row['OC_ID'];


            return ['success' => true, 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    private $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    public function getOrdenCompra($id) {
        try {
            $sql = "SELECT * FROM SIST_ORDEN_COMPRA WHERE OC_ID = ?";
            $result = sqlsrv_query($this->conn, $sql, [$id]);
            if (!$result) {
                error_log("Error en getOrdenCompra: " . print_r(sqlsrv_errors(), true));
                return [];
            }
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            return $row ?: [];
        } catch (Exception $e) {
            error_log("Excepción en getOrdenCompra: " . $e->getMessage());
            return [];
        }
    }

    public function getDocumentData($documentId, $ordenId) {
        $tableMap = [
            'acta-conocimiento-conformidad' => ['table' => 'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD', 'field' => 'ACC_DOCUMENTO_VENTA_ID'],
            'actorizacion-datos-personales' => ['table' => 'SIST_AUTORIZACION_DATOS_PERSONALES', 'field' => 'ADP_DOCUMENTO_VENTA_ID'],
            'carta_conocimiento_aceptacion' => ['table' => 'SIST_CARTA_CONOCIMIENTO_ACEPTACION', 'field' => 'CCA_DOCUMENTO_VENTA_ID'],
            'carta_recepcion' => ['table' => 'SIST_CARTA_RECEPCION', 'field' => 'CR_DOCUMENTO_VENTA_ID'],
            'carta-caracteristicas' => ['table' => 'SIST_CARTA_CARACTERISTICAS', 'field' => 'CC_DOCUMENTO_VENTA_ID'],
            'carta_caracteristicas_banbif' => ['table' => 'SIST_CARTA_CARACTERISTICAS_BANBIF', 'field' => 'CCB_DOCUMENTO_VENTA_ID'],
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
        $result = sqlsrv_query($this->conn, $sql, [$ordenId]);
        if (!$result) {
            return [];
        }
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        return $row ?: [];
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

            // Consultar datos adicionales desde la API externa
            $apiUrl = 'https://opensheet.elk.sh/1HmRbKs7uTGhd5vN99bb_f621AzncNGO8iFe5s6ITkM0/BD';
            $apiData = @file_get_contents($apiUrl);
            if ($apiData) {
                $apiArray = json_decode($apiData, true);
                if ($apiArray && is_array($apiArray)) {
                    // Comparación exacta con normalización básica (trim y strtoupper)
                    $marcaBD = trim(strtoupper($vehiculo['MARCA'] ?? ''));
                    $modeloBD = trim(strtoupper($vehiculo['MODELO'] ?? ''));
                    $versionBD = trim(strtoupper($vehiculo['VERSION'] ?? ''));
                    $anioBD = trim($vehiculo['ANIO_FABRICACION'] ?? '');

                    // Buscar coincidencia por MARCA, MODELO, VERSION, MODEL YEAR
                    foreach ($apiArray as $item) {
                        $marcaAPI = trim(strtoupper($item['MARCA'] ?? ''));
                        $modeloAPI = trim(strtoupper($item['MODELO'] ?? ''));
                        $versionAPI = trim(strtoupper($item['VERSION'] ?? ''));
                        $anioAPI = trim($item['MODEL YEAR'] ?? '');

                        if ($marcaBD === $marcaAPI &&
                            $modeloBD === $modeloAPI &&
                            $versionBD === $versionAPI &&
                            $anioBD == $anioAPI) {
                            $vehiculo['GARANTIA'] = $item['GARANTIA'] ?? '';
                            $vehiculo['PERIODICIDAD'] = $item['PERIODICIDAD'] ?? '';
                            $vehiculo['PRIMER_INGRESO'] = $item['1 INGRESO'] ?? '';
                            break;
                        }
                    }
                }
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

    public function getBancos() {
        $sql = "SELECT TOP 100 BANCO FROM i_cosbanco ORDER BY BANCO";
        $result = sqlsrv_query($this->conn, $sql);
        if (!$result) {
            return [];
        }
        $bancos = [];
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $bancos[] = $row['BANCO'];
        }
        return $bancos;
    }

    public function guardarDocumentoIndividual($documentType, $data, $ordenId) {
        try {
            $tableMap = [
                'carta-caracteristicas' => 'SIST_CARTA_CARACTERISTICAS',
                'carta_caracteristicas_banbif' => 'SIST_CARTA_CARACTERISTICAS_BANBIF',
                'carta_felicitaciones' => 'SIST_CARTA_FELICITACIONES',
                'carta_recepcion' => 'SIST_CARTA_RECEPCION',
                // Agregar otros documentos aquí
            ];

            if (!isset($tableMap[$documentType])) {
                throw new Exception("Tipo de documento no válido");
            }

            $table = $tableMap[$documentType];
            $fields = [];
            $placeholders = [];
            $values = [];

            // Agregar OC_ID
            $fields[] = 'CC_DOCUMENTO_VENTA_ID';
            $placeholders[] = '?';
            $values[] = $ordenId;

            // Agregar fecha de creación
            $fields[] = 'CC_FECHA_CREACION';
            $placeholders[] = '?';
            $values[] = date('Y-m-d H:i:s');

            foreach ($data as $key => $value) {
                if (strpos($key, 'CC_') === 0 && $key !== 'CC_DOCUMENTO_VENTA_ID' && $key !== 'CC_FECHA_CREACION') {
                    $fields[] = $key;
                    $placeholders[] = '?';
                    $values[] = $value !== '' ? $value : null;
                }
            }

            if (empty($fields)) {
                throw new Exception("No hay campos para insertar");
            }

            $sql = "INSERT INTO $table (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            $result = sqlsrv_query($this->conn, $sql, $values);
            if (!$result) {
                throw new Exception("Error executing query: " . print_r(sqlsrv_errors(), true));
            }

            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
