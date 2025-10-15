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
            
            // Inicializar estado de aprobación como PENDIENTE
            $data['OC_ESTADO_APROBACION'] = 'PENDIENTE';
            
            // Filtrar solo campos OC_ y truncar si es necesario
            $fields = [];
            $placeholders = [];
            $values = [];
            $maxLengths = [
                'OC_VEHICULO_ANIO_MODELO' => 10,
                // Agregar otros si es necesario
            ];
            // Campos que NO deben guardarse en la base de datos (no existen como columnas)
            $excludedFields = [
                // OC_TIPO_CLIENTE ya fue agregado a la BD, se puede guardar
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
                // Excluir campos que no existen en la tabla
                if (strpos($key, 'OC_') === 0 && !in_array($key, $excludedFields)) {
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
            
            error_log("=== INTENTANDO ENVIAR CORREO AL RESPONSABLE ===");
            
            // Priorizar el email guardado directamente en el formulario
            $emailResponsable = trim($data['OC_EMAIL_CENTRO_COSTO'] ?? '');
            
            // Si no está el email directo, buscar por agencia y responsable
            if (empty($emailResponsable) && !empty($data['OC_AGENCIA']) && !empty($data['OC_NOMBRE_RESPONSABLE'])) {
                error_log("Email no encontrado en formulario, buscando por Agencia y Responsable...");
                error_log("Agencia: " . $data['OC_AGENCIA']);
                error_log("Responsable: " . $data['OC_NOMBRE_RESPONSABLE']);
                $emailResponsable = $this->getEmailResponsable($data['OC_AGENCIA'], $data['OC_NOMBRE_RESPONSABLE']);
            }
            
            error_log("Email responsable: " . ($emailResponsable ?: 'NO ENCONTRADO'));
            error_log("Orden ID para correo: " . $id);
            error_log("Número expediente para correo: " . $numeroExpediente);
            
            if ($emailResponsable) {
                error_log("🚀 Iniciando envío de correo...");
                try {
                    $resultadoEnvio = $this->enviarCorreoResponsable($id, $emailResponsable, $numeroExpediente);
                    error_log("Resultado envío: " . ($resultadoEnvio ? '✅ EXITOSO' : '❌ FALLIDO'));
                } catch (Exception $e) {
                    error_log("❌ EXCEPCIÓN al enviar correo: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                }
            } else {
                error_log("ERROR: No se pudo obtener el email del responsable");
            }

            return ['success' => true, 'id' => $id, 'numero_expediente' => $numeroExpediente];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    private function enviarCorreoResponsable($ordenId, $emailDestino, $numeroExpediente) {
        try {
            // Validar email destino
            if (empty($emailDestino) || !filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
                error_log("❌ Email destino inválido o vacío: " . var_export($emailDestino, true));
                return false;
            }
            
            // Obtener datos de la orden para el correo
            $orden = $this->getOrdenCompra($ordenId);
            
            if (!$orden) {
                error_log("No se pudo obtener la orden $ordenId para enviar correo");
                return false;
            }
            
            // URL del panel de aprobación
            $urlAprobacion = "http://190.238.78.104:3800/digitalizacion-documentos/aprobacion/panel?id=" . $ordenId;
            
            // Obtener datos con valores por defecto
            $cliente = $orden['OC_COMPRADOR_NOMBRE'] ?? 'No especificado';
            $marca = $orden['OC_VEHICULO_MARCA'] ?? '';
            $modelo = $orden['OC_VEHICULO_MODELO'] ?? '';
            $vehiculo = trim($marca . ' ' . $modelo) ?: 'No especificado';
            $asesor = $orden['OC_ASESOR_VENTA'] ?? 'No especificado';
            
            // Construir el HTML del correo
            $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #1e3a8a;'>Orden de Compra Pendiente de Aprobación</h2>
                <p><strong>Número de Expediente:</strong> {$numeroExpediente}</p>
                <p><strong>Cliente:</strong> {$cliente}</p>
                <p><strong>Vehículo:</strong> {$vehiculo}</p>
                <p><strong>Asesor:</strong> {$asesor}</p>
                <p><strong>Estado:</strong> <span style='color: orange;'>Pendiente de aprobación</span></p>
                <br>
                <p>
                    <a href='{$urlAprobacion}' 
                       style='background-color: #1e3a8a; color: white; padding: 12px 24px; 
                              text-decoration: none; border-radius: 4px; display: inline-block;'>
                        Ver Orden Pendiente
                    </a>
                </p>
                <br>
                <p style='color: #666; font-size: 12px;'>
                    Este correo fue generado automáticamente. Por favor no responder.
                </p>
            </div>
            ";
            
            // Preparar datos para la API de correo (SIEMPRE desde comunica@interamericana.shop)
            $emailData = [
                'to' => $emailDestino,
                'subject' => "📬 Orden de Compra Pendiente de Aprobación - {$numeroExpediente}",
                'html' => $htmlBody,
                'from' => 'comunica@interamericana.shop',
                'from_name' => 'Sistema de Digitalización Interamericana'
            ];
            
            error_log("=== DATOS DEL CORREO ===");
            error_log("TO: " . $emailDestino);
            error_log("FROM: comunica@interamericana.shop");
            error_log("SUBJECT: 📬 Orden de Compra Pendiente de Aprobación - {$numeroExpediente}");
            error_log("JSON a enviar: " . json_encode($emailData));
            
            // Enviar correo usando cURL
            $ch = curl_init('http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            error_log("HTTP Code: {$httpCode}");
            error_log("Response: {$response}");
            if ($curlError) {
                error_log("cURL Error: {$curlError}");
            }
            
            if ($httpCode === 200) {
                error_log("✅ Correo de aprobación enviado exitosamente a: {$emailDestino}");
                return true;
            } else {
                error_log("❌ Error al enviar correo de aprobación. HTTP Code: {$httpCode}, Response: {$response}");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Excepción al enviar correo de aprobación: " . $e->getMessage());
            return false;
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
        // Conectar a BD DOC_DIGITALES para obtener firmas
        $db = new Database();
        $docDigitalesConn = $db->getDocDigitalesConnection();
        
        if (!$docDigitalesConn) {
            error_log("No se pudo conectar a DOC_DIGITALES para verificar firma");
            return null;
        }
        
        $sql = "SELECT firma_data, firma_mail, firma_nombre, firma_apellido FROM firmas WHERE usuario = ? AND password = ?";
        $result = sqlsrv_query($docDigitalesConn, $sql, [$usuario, $password]);
        if (!$result) {
            sqlsrv_close($docDigitalesConn);
            return null;
        }
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        sqlsrv_close($docDigitalesConn);
        
        if ($row) {
            // Guardar datos del usuario en sesión
            $_SESSION['usuario_email'] = $row['firma_mail'];
            $_SESSION['usuario_nombre'] = $row['firma_nombre'];
            $_SESSION['usuario_apellido'] = $row['firma_apellido'];
            $_SESSION['usuario_nombre_completo'] = trim($row['firma_nombre'] . ' ' . $row['firma_apellido']);
            return 'http://190.238.78.104:3800' . $row['firma_data'];
        }
        return null;
    }

    public function getCentrosCosto() {
        // Usar SIEMPRE el archivo JSON local (la API no funciona)
        $backupFile = __DIR__ . '/../../centros_costo_backup.json';
        
        if (file_exists($backupFile)) {
            $backupData = file_get_contents($backupFile);
            $centros = json_decode($backupData, true);
            
            if ($centros && is_array($centros)) {
                return $centros;
            }
        }
        
        return [];
    }
    
    public function getAgencias() {
        $centros = $this->getCentrosCosto();
        
        if (empty($centros)) {
            return [];
        }
        
        // Extraer agencias manualmente para evitar problemas con array_column
        $agencias = [];
        $agenciasNormalizadas = []; // Para evitar duplicados con diferentes espacios/encoding
        
        foreach ($centros as $centro) {
            // Intentar diferentes variaciones del nombre de la columna
            $agencia = $centro['AGENCIA'] ?? $centro['agencia'] ?? $centro['Agencia'] ?? null;
            
            // Limpiar espacios y normalizar
            if ($agencia) {
                $agencia = trim($agencia);
                // Normalizar para comparación (eliminar espacios múltiples, convertir a mayúsculas)
                $agenciaNormalizada = strtoupper(preg_replace('/\s+/', ' ', $agencia));
                
                if (!in_array($agenciaNormalizada, $agenciasNormalizadas)) {
                    $agenciasNormalizadas[] = $agenciaNormalizada;
                    $agencias[] = $agencia;
                }
            }
        }
        
        sort($agencias);
        return $agencias;
    }
    
    public function getNombresPorAgencia($agencia) {
        $centros = $this->getCentrosCosto();
        $nombres = [];
        $nombresNormalizados = []; // Para evitar duplicados con diferentes espacios/encoding
        
        foreach ($centros as $centro) {
            $centroAgencia = $centro['AGENCIA'] ?? $centro['agencia'] ?? $centro['Agencia'] ?? '';
            $centroNombre = $centro['NOMBRE'] ?? $centro['nombre'] ?? $centro['Nombre'] ?? '';
            
            // Limpiar espacios y caracteres especiales
            $centroAgencia = trim($centroAgencia);
            $centroNombre = trim($centroNombre);
            
            // Normalizar para comparación (eliminar espacios múltiples, convertir a mayúsculas)
            $nombreNormalizado = strtoupper(preg_replace('/\s+/', ' ', $centroNombre));
            
            if ($centroAgencia === $agencia && $centroNombre && !in_array($nombreNormalizado, $nombresNormalizados)) {
                $nombresNormalizados[] = $nombreNormalizado;
                $nombres[] = $centroNombre;
            }
        }
        
        // Ordenar alfabéticamente para mejor UX
        sort($nombres);
        
        return $nombres;
    }
    
    public function getEmailResponsable($agencia, $responsable) {
        $centros = $this->getCentrosCosto();
        
        foreach ($centros as $centro) {
            $centroAgencia = trim($centro['AGENCIA'] ?? $centro['agencia'] ?? $centro['Agencia'] ?? '');
            $centroNombre = trim($centro['NOMBRE'] ?? $centro['nombre'] ?? $centro['Nombre'] ?? '');
            
            if ($centroAgencia === $agencia && $centroNombre === $responsable) {
                return trim($centro['EMAIL'] ?? $centro['email'] ?? '');
            }
        }
        
        return null;
    }
    
    public function getCentrosCostoPorNombre($agencia, $nombre) {
        $centros = $this->getCentrosCosto();
        $centrosFiltrados = [];
        $centrosUnicos = [];

        foreach ($centros as $centro) {
            $centroAgencia = trim($centro['AGENCIA'] ?? $centro['agencia'] ?? '');
            $centroNombre = trim($centro['NOMBRE'] ?? $centro['nombre'] ?? '');

            // Limpiar espacios para comparación consistente
            if ($centroAgencia === $agencia && $centroNombre === $nombre) {
                $centroCosto = $centro['CENTRO DE COSTO'] ?? $centro['centro de costo'] ?? '';
                // Evitar duplicados usando el centro de costo como clave
                if (!in_array($centroCosto, $centrosUnicos)) {
                    $centrosUnicos[] = $centroCosto;
                    $centrosFiltrados[] = [
                        'CENTRO_COSTO' => $centroCosto,
                        'NOMBRE_CC' => $centro['NOMBRE CC'] ?? $centro['nombre cc'] ?? '',
                        'EMAIL' => $centro['EMAIL'] ?? $centro['email'] ?? ''
                    ];
                }
            }
        }

        return $centrosFiltrados;
    }

    public function procesarAprobacion($ordenId, $accion, $observaciones = '') {
        try {
            // Obtener datos de la orden
            $orden = $this->getOrdenCompra($ordenId);
            
            if (!$orden) {
                return ['success' => false, 'error' => 'Orden no encontrada'];
            }
            
            if ($orden['OC_ESTADO_APROBACION'] !== 'PENDIENTE') {
                return ['success' => false, 'error' => 'Esta orden ya fue procesada'];
            }
            
            $nuevoEstado = $accion === 'aprobar' ? 'APROBADO' : 'RECHAZADO';
            
            // Actualizar estado en la base de datos
            $sql = "UPDATE SIST_ORDEN_COMPRA 
                    SET OC_ESTADO_APROBACION = ?, 
                        OC_FECHA_APROBACION = GETDATE(),
                        OC_OBSERVACIONES_APROBACION = ?
                    WHERE OC_ID = ?";
            
            $result = sqlsrv_query($this->conn, $sql, [$nuevoEstado, $observaciones, $ordenId]);
            
            if (!$result) {
                throw new Exception("Error al actualizar estado: " . print_r(sqlsrv_errors(), true));
            }
            
            // Enviar correo al asesor (email guardado en sesión al hacer login)
            // Por ahora, obtener el email del asesor desde la BD de firmas
            $this->enviarCorreoAsesor($orden, $nuevoEstado, $observaciones);
            
            return ['success' => true, 'estado' => $nuevoEstado];
            
        } catch (Exception $e) {
            error_log("Error en procesarAprobacion: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function enviarCorreoAsesor($orden, $estado, $observaciones) {
        error_log("=== INICIANDO envíoCorreoAsesor ===");
        error_log("Orden ID: {$orden['OC_ID']}, Estado: $estado");

        try {
            // IMPORTANTE: El correo se envía al usuario que está logueado (quien creó la orden)
            // NO al asesor seleccionado en el formulario
            $emailAsesor = $_SESSION['usuario_email'] ?? null;
            $nombreAsesor = $_SESSION['usuario_nombre_completo'] ?? 'Asesor';

            if (!$emailAsesor) {
                error_log("ERROR: No hay email en la sesión del usuario logueado");
                return false;
            }

            error_log("Email del usuario logueado: $emailAsesor ($nombreAsesor)");

            // Construir HTML del correo
            $estadoTexto = $estado === 'APROBADO' ? 'APROBADA' : 'RECHAZADA';
            $colorEstado = $estado === 'APROBADO' ? '#10b981' : '#ef4444';

            $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #1e3a8a;'>Hola {$nombreAsesor},</h2>
                <p style='font-size: 16px; margin-bottom: 20px;'>Tu orden de compra ha sido <strong style='color: {$colorEstado};'>{$estadoTexto}</strong></p>

                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <p><strong>Número de Expediente:</strong> {$orden['OC_NUMERO_EXPEDIENTE']}</p>
                    <p><strong>Cliente:</strong> {$orden['OC_COMPRADOR_NOMBRE']}</p>
                    <p><strong>Vehículo:</strong> {$orden['OC_VEHICULO_MARCA']} {$orden['OC_VEHICULO_MODELO']}</p>
                    <p><strong>Estado:</strong> <span style='color: {$colorEstado}; font-weight: bold;'>{$estadoTexto}</span></p>
                    " . ($observaciones ? "<p><strong>Observaciones:</strong> {$observaciones}</p>" : "") . "
                </div>

                <p style='color: #666; font-size: 12px;'>
                    Este correo fue generado automáticamente. Por favor no responder.
                </p>
            </div>
            ";

            $emailData = [
                'to' => $emailAsesor,
                'subject' => "📬 Orden de Compra {$estadoTexto} - {$orden['OC_NUMERO_EXPEDIENTE']}",
                'html' => $htmlBody,
                'from' => 'comunica@interamericana.shop',
                'from_name' => 'Sistema de Digitalización Interamericana'
            ];

            error_log("=== ENVIANDO CORREO AL ASESOR ===");
            error_log("TO: $emailAsesor");
            error_log("FROM: comunica@interamericana.shop");
            error_log("SUBJECT: 📬 Orden de Compra {$estadoTexto} - {$orden['OC_NUMERO_EXPEDIENTE']}");

            $ch = curl_init('http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                error_log("Correo de notificación enviado exitosamente al asesor: {$emailAsesor}");
                return true;
            } else {
                error_log("Error al enviar correo al asesor. HTTP Code: {$httpCode}, Response: {$response}");
                return false;
            }

        } catch (Exception $e) {
            error_log("Excepción en enviarCorreoAsesor: " . $e->getMessage());
            return false;
        }
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

            // NO agregar fecha de creación manualmente, dejar que SQL Server use DEFAULT
            // $fields[] = $fechaField;
            // $placeholders[] = '?';
            // $values[] = date('Y-m-d H:i:s');

            // Agregar los demás campos del formulario
            foreach ($data as $key => $value) {
                if (strpos($key, $prefix) === 0 && $key !== $fkField && $key !== $fechaField) {
                    $fields[] = $key;
                    $placeholders[] = '?';
                    
                    // Validar campos de fecha específicamente
                    if (stripos($key, 'FECHA') !== false) {
                        // Si es un campo de fecha y está vacío, usar NULL
                        // Si tiene valor, validar que sea una fecha válida
                        if ($value === '' || $value === null) {
                            $values[] = null;
                        } else {
                            // Intentar convertir a formato Y-m-d
                            $timestamp = strtotime($value);
                            if ($timestamp !== false && $timestamp > 0) {
                                $date = date('Y-m-d', $timestamp);
                                $values[] = $date;
                            } else {
                                $values[] = null;
                            }
                        }
                    } else {
                        // Para otros campos, usar el valor o null si está vacío
                        $values[] = $value !== '' ? $value : null;
                    }
                }
            }

            if (empty($fields)) {
                throw new Exception("No hay campos para insertar");
            }

            $sql = "INSERT INTO $table (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            
            // Log para debug
            error_log("SQL: $sql");
            error_log("Valores: " . print_r($values, true));
            
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

    public function getAsesores() {
        try {
            // Conectar a la base de datos RSFACCAR12 usando el método de Database
            $db = new Database();
            $connAsesores = $db->getRsfaccar12Connection();
            
            if ($connAsesores === null || $connAsesores === false) {
                error_log("Error al conectar a RSFACCAR12");
                return [];
            }
            
            // Query con nombres de columnas en mayúsculas - seleccionando solo VE_CNOMBRE
            $sql = "SELECT VE_CCODIGO, VE_CNOMBRE FROM FT0002VEND WHERE VE_CTIPVEN != 'I' ORDER BY VE_CNOMBRE";
            $result = sqlsrv_query($connAsesores, $sql);
            
            if (!$result) {
                error_log("Error en query de asesores: " . print_r(sqlsrv_errors(), true));
                sqlsrv_close($connAsesores);
                return [];
            }
            
            $asesores = [];
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                // Intentar con mayúsculas y minúsculas para compatibilidad
                $nombre = $row['VE_CNOMBRE'] ?? $row['ve_cnombre'] ?? '';
                $codigo = $row['VE_CCODIGO'] ?? $row['ve_ccodigo'] ?? '';
                
                // Solo agregar si el nombre no está vacío
                if (!empty($nombre)) {
                    $asesores[] = [
                        'codigo' => trim($codigo),
                        'nombre' => trim($nombre)
                    ];
                }
            }
            
            sqlsrv_close($connAsesores);
            return $asesores;
            
        } catch (Exception $e) {
            error_log("Error en getAsesores: " . $e->getMessage());
            return [];
        }
    }

    public function getDatosMantenimiento($marca, $modelo) {
        try {
            // Leer el archivo JSON con los datos de mantenimiento
            $jsonFile = __DIR__ . '/../../config/vehiculos_mantenimiento.json';
            
            error_log("Buscando archivo JSON en: $jsonFile");
            
            if (!file_exists($jsonFile)) {
                error_log("❌ Archivo de mantenimiento no encontrado: $jsonFile");
                return null;
            }
            
            error_log("✓ Archivo JSON encontrado");
            
            $jsonContent = file_get_contents($jsonFile);
            $vehiculos = json_decode($jsonContent, true);
            
            if (!$vehiculos) {
                error_log("❌ Error al decodificar JSON de mantenimiento: " . json_last_error_msg());
                return null;
            }
            
            error_log("✓ JSON decodificado correctamente. Total vehículos: " . count($vehiculos));
            
            // Normalizar marca y modelo para comparación (eliminar espacios extras y caracteres especiales)
            $marcaBuscar = strtoupper(trim(preg_replace('/\s+/', ' ', $marca)));
            $modeloBuscar = strtoupper(trim(preg_replace('/\s+/', ' ', $modelo)));
            
            error_log("Buscando: MARCA='$marcaBuscar', MODELO='$modeloBuscar'");
            
            // Buscar coincidencia
            foreach ($vehiculos as $vehiculo) {
                $marcaVehiculo = strtoupper(trim(preg_replace('/\s+/', ' ', $vehiculo['MARCA'] ?? '')));
                $modeloVehiculo = strtoupper(trim(preg_replace('/\s+/', ' ', $vehiculo['MODELO'] ?? '')));
                
                error_log("Comparando con: MARCA='$marcaVehiculo', MODELO='$modeloVehiculo'");
                
                // Comparación exacta o por contención (para casos como "SOLUTO 1.4" vs "SOLUTO")
                $marcaCoincide = ($marcaVehiculo === $marcaBuscar);
                $modeloCoincide = ($modeloVehiculo === $modeloBuscar) || 
                                  (strpos($modeloBuscar, $modeloVehiculo) === 0) ||
                                  (strpos($modeloVehiculo, $modeloBuscar) === 0);
                
                if ($marcaCoincide && $modeloCoincide) {
                    error_log("✓ Coincidencia encontrada!");
                    
                    // Formatear los datos según los requisitos
                    $garantia = $vehiculo['GARANTIA'] ?? '';
                    $primerIngreso = $vehiculo['1 INGRESO'] ?? '';
                    $periodicidad = $vehiculo['PERIODICIDAD'] ?? '';
                    
                    $resultado = [
                        'GARANTIA' => $garantia ? $garantia . ', lo que pase primero' : '',
                        'PRIMER_INGRESO' => $primerIngreso ? $this->formatearPrimerIngreso($primerIngreso) : '',
                        'PERIODICIDAD' => $periodicidad ? 'cada ' . number_format((float)str_replace(',', '', $periodicidad), 0, '.', ',') . ' km' : ''
                    ];
                    
                    error_log("Datos formateados: " . json_encode($resultado));
                    return $resultado;
                }
            }
            
            error_log("❌ No se encontró coincidencia para MARCA='$marcaBuscar', MODELO='$modeloBuscar'");
            return null;
            
        } catch (Exception $e) {
            error_log("❌ Error en getDatosMantenimiento: " . $e->getMessage());
            return null;
        }
    }
    
    private function formatearPrimerIngreso($valor) {
        // Si el valor ya contiene "meses", dejarlo como está y agregar km al número
        // Ejemplo: "5,000 o 6 meses" -> "5,000 km o 6 meses"
        if (stripos($valor, 'meses') !== false) {
            // Buscar el número al inicio y agregarle km
            $valor = preg_replace('/^([\d,]+)/', '$1 km', $valor);
            return $valor;
        }
        
        // Si solo es un número, agregar km
        return number_format((float)str_replace(',', '', $valor), 0, '.', ',') . ' km';
    }
}
