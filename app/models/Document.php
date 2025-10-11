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

    /**
     * Genera el siguiente número de expediente directamente en PHP
     * Formato: YYYYMM0001 (se reinicia cada mes)
     */
    private function generarNumeroExpediente() {
        try {
            // Obtener año y mes actual (YYYYMM)
            $anioMes = date('Ym'); // Ejemplo: 202510
            
            // Buscar el último número del mes actual con bloqueo
            $sql = "SELECT TOP 1 OC_NUMERO_EXPEDIENTE 
                    FROM SIST_ORDEN_COMPRA WITH (UPDLOCK, HOLDLOCK)
                    WHERE OC_NUMERO_EXPEDIENTE LIKE ? 
                    AND LEN(OC_NUMERO_EXPEDIENTE) = 10
                    ORDER BY OC_NUMERO_EXPEDIENTE DESC";
            
            $params = [$anioMes . '%'];
            $result = sqlsrv_query($this->conn, $sql, $params);
            
            if (!$result) {
                throw new Exception("Error al buscar último expediente: " . print_r(sqlsrv_errors(), true));
            }
            
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            
            if ($row && isset($row['OC_NUMERO_EXPEDIENTE'])) {
                // Extraer los últimos 4 dígitos y sumar 1
                $ultimoNumero = (int)substr($row['OC_NUMERO_EXPEDIENTE'], -4);
                $siguienteNumero = $ultimoNumero + 1;
            } else {
                // No hay registros este mes, empezar desde 1
                $siguienteNumero = 1;
            }
            
            // Formatear con ceros a la izquierda (4 dígitos)
            $numeroExpediente = $anioMes . str_pad($siguienteNumero, 4, '0', STR_PAD_LEFT);
            
            return $numeroExpediente;
            
        } catch (Exception $e) {
            error_log("Error en generarNumeroExpediente: " . $e->getMessage());
            throw $e;
        }
    }

    public function guardarOrdenCompra($data, $files = []) {
        try {
            // 🎯 GENERAR NÚMERO DE EXPEDIENTE AUTOMÁTICAMENTE
            $numeroExpediente = $this->generarNumeroExpediente();
            
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

            // Agregar el número de expediente generado a los datos
            $data['OC_NUMERO_EXPEDIENTE'] = $numeroExpediente;
            
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

            // Guardar el número de expediente en la sesión
            $_SESSION['numero_expediente'] = $numeroExpediente;

            return ['success' => true, 'id' => $id, 'numero_expediente' => $numeroExpediente];
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
            // Mapeo de tipos de documento a tablas y prefijos
            $tableMap = [
                'acta-conocimiento-conformidad' => [
                    'table' => 'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD',
                    'prefix' => 'ACC_',
                    'fk' => 'ACC_DOCUMENTO_VENTA_ID',
                    'fecha' => 'ACC_FECHA_CREACION'
                ],
                'actorizacion-datos-personales' => [
                    'table' => 'SIST_AUTORIZACION_DATOS_PERSONALES',
                    'prefix' => 'ADP_',
                    'fk' => 'ADP_DOCUMENTO_VENTA_ID',
                    'fecha' => 'ADP_FECHA_CREACION'
                ],
                'carta_conocimiento_aceptacion' => [
                    'table' => 'SIST_CARTA_CONOCIMIENTO_ACEPTACION',
                    'prefix' => 'CCA_',
                    'fk' => 'CCA_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CCA_FECHA_CREACION'
                ],
                'carta_recepcion' => [
                    'table' => 'SIST_CARTA_RECEPCION',
                    'prefix' => 'CR_',
                    'fk' => 'CR_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CR_FECHA_CREACION'
                ],
                'carta-caracteristicas' => [
                    'table' => 'SIST_CARTA_CARACTERISTICAS',
                    'prefix' => 'CC_',
                    'fk' => 'CC_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CC_FECHA_CREACION'
                ],
                'carta_caracteristicas_banbif' => [
                    'table' => 'SIST_CARTA_CARACTERISTICAS_BANBIF',
                    'prefix' => 'CCB_',
                    'fk' => 'CCB_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CCB_FECHA_CREACION'
                ],
                'carta_felicitaciones' => [
                    'table' => 'SIST_CARTA_FELICITACIONES',
                    'prefix' => 'CF_',
                    'fk' => 'CF_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CF_FECHA_CREACION'
                ],
                'carta_obsequios' => [
                    'table' => 'SIST_CARTA_OBSEQUIOS',
                    'prefix' => 'CO_',
                    'fk' => 'CO_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CO_FECHA_CREACION'
                ],
                'politica_proteccion_datos' => [
                    'table' => 'SIST_POLITICA_PROTECCION_DATOS',
                    'prefix' => 'PPD_',
                    'fk' => 'PPD_DOCUMENTO_VENTA_ID',
                    'fecha' => 'PPD_FECHA_CREACION'
                ]
            ];

            if (!isset($tableMap[$documentType])) {
                throw new Exception("Tipo de documento no válido: $documentType");
            }

            $config = $tableMap[$documentType];
            $table = $config['table'];
            $prefix = $config['prefix'];
            $fkField = $config['fk'];
            $fechaField = $config['fecha'];
            
            $fields = [];
            $placeholders = [];
            $values = [];

            // Agregar OC_ID (clave foránea)
            $fields[] = $fkField;
            $placeholders[] = '?';
            $values[] = $ordenId;

            // Agregar fecha de creación
            $fields[] = $fechaField;
            $placeholders[] = '?';
            $values[] = date('Y-m-d H:i:s');

            // Agregar los demás campos del formulario
            foreach ($data as $key => $value) {
                if (strpos($key, $prefix) === 0 && $key !== $fkField && $key !== $fechaField) {
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

    /**
     * Buscar orden de compra por número de expediente
     */
    public function buscarPorNumeroExpediente($numeroExpediente) {
        try {
            $sql = "SELECT * FROM SIST_ORDEN_COMPRA WHERE OC_NUMERO_EXPEDIENTE = ?";
            $result = sqlsrv_query($this->conn, $sql, [$numeroExpediente]);
            
            if (!$result) {
                error_log("Error en buscarPorNumeroExpediente: " . print_r(sqlsrv_errors(), true));
                return null;
            }
            
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            return $row ?: null;
        } catch (Exception $e) {
            error_log("Excepción en buscarPorNumeroExpediente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Listar todas las órdenes de compra con paginación
     */
    public function listarOrdenesCompra($page = 1, $perPage = 20, $search = '') {
        try {
            $offset = ($page - 1) * $perPage;
            
            $whereClause = '';
            $params = [];
            
            if (!empty($search)) {
                $whereClause = "WHERE OC_NUMERO_EXPEDIENTE LIKE ? OR OC_COMPRADOR_NOMBRE LIKE ? OR OC_COMPRADOR_NUMERO_DOCUMENTO LIKE ?";
                $searchParam = '%' . $search . '%';
                $params = [$searchParam, $searchParam, $searchParam];
            }
            
            // Contar total de registros
            $sqlCount = "SELECT COUNT(*) as total FROM SIST_ORDEN_COMPRA $whereClause";
            $resultCount = sqlsrv_query($this->conn, $sqlCount, $params);
            $rowCount = sqlsrv_fetch_array($resultCount, SQLSRV_FETCH_ASSOC);
            $total = $rowCount['total'];
            
            // Obtener registros paginados
            $sql = "SELECT OC_ID, OC_NUMERO_EXPEDIENTE, OC_COMPRADOR_NOMBRE, OC_COMPRADOR_NUMERO_DOCUMENTO, 
                           OC_VEHICULO_MARCA, OC_VEHICULO_MODELO, OC_FECHA_ORDEN, OC_FECHA_CREACION
                    FROM SIST_ORDEN_COMPRA 
                    $whereClause
                    ORDER BY OC_FECHA_CREACION DESC
                    OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
            
            $params[] = $offset;
            $params[] = $perPage;
            
            $result = sqlsrv_query($this->conn, $sql, $params);
            
            if (!$result) {
                error_log("Error en listarOrdenesCompra: " . print_r(sqlsrv_errors(), true));
                return ['data' => [], 'total' => 0, 'pages' => 0];
            }
            
            $ordenes = [];
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $ordenes[] = $row;
            }
            
            return [
                'data' => $ordenes,
                'total' => $total,
                'pages' => ceil($total / $perPage),
                'current_page' => $page
            ];
        } catch (Exception $e) {
            error_log("Excepción en listarOrdenesCompra: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0];
        }
    }

    /**
     * Obtener todos los documentos asociados a una orden de compra
     */
    public function getDocumentosPorOrden($ordenId) {
        try {
            $documentos = [];
            
            // Mapeo de tablas y sus campos de ID
            $tablas = [
                'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD' => ['id_field' => 'ACC_ID', 'fk_field' => 'ACC_DOCUMENTO_VENTA_ID', 'nombre' => 'Acta Conocimiento Conformidad'],
                'SIST_AUTORIZACION_DATOS_PERSONALES' => ['id_field' => 'ADP_ID', 'fk_field' => 'ADP_DOCUMENTO_VENTA_ID', 'nombre' => 'Autorización Datos Personales'],
                'SIST_CARTA_CONOCIMIENTO_ACEPTACION' => ['id_field' => 'CCA_ID', 'fk_field' => 'CCA_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Conocimiento Aceptación'],
                'SIST_CARTA_RECEPCION' => ['id_field' => 'CR_ID', 'fk_field' => 'CR_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Recepción'],
                'SIST_CARTA_CARACTERISTICAS' => ['id_field' => 'CC_ID', 'fk_field' => 'CC_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Características'],
                'SIST_CARTA_CARACTERISTICAS_BANBIF' => ['id_field' => 'CCB_ID', 'fk_field' => 'CCB_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Características Banbif'],
                'SIST_CARTA_FELICITACIONES' => ['id_field' => 'CF_ID', 'fk_field' => 'CF_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Felicitaciones'],
                'SIST_CARTA_OBSEQUIOS' => ['id_field' => 'CO_ID', 'fk_field' => 'CO_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Obsequios'],
                'SIST_POLITICA_PROTECCION_DATOS' => ['id_field' => 'PPD_ID', 'fk_field' => 'PPD_DOCUMENTO_VENTA_ID', 'nombre' => 'Política Protección Datos']
            ];
            
            foreach ($tablas as $tabla => $config) {
                $sql = "SELECT {$config['id_field']} FROM $tabla WHERE {$config['fk_field']} = ?";
                $result = sqlsrv_query($this->conn, $sql, [$ordenId]);
                
                if ($result) {
                    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                    if ($row) {
                        $documentos[] = [
                            'tabla' => $tabla,
                            'nombre' => $config['nombre'],
                            'existe' => true,
                            'id' => $row[$config['id_field']]
                        ];
                    }
                }
            }
            
            return $documentos;
        } catch (Exception $e) {
            error_log("Error en getDocumentosPorOrden: " . $e->getMessage());
            return [];
        }
    }
}
