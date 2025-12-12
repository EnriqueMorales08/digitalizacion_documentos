<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/AuditLog.php';

class Document {
    private $conn;

    public function __construct() {
        // Crear instancia de Database y obtener conexión
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getConnection() {
        return $this->conn;
    }

    /**
     * Verificar si el usuario es jefe de tienda por su cargo
     * @return bool
     */
    private static function esJefeDeTienda() {
        $cargo = $_SESSION['usuario_cargo'] ?? '';
        return (stripos($cargo, 'JEFE DE TIENDA') !== false);
    }

    /**
     * Obtener emails de asesores que pertenecen a una tienda específica (por código)
     * @param string $codigoTienda Código de la tienda (ej: "01367")
     * @return array Lista de emails
     */
    private function getEmailsAsesoresPorTienda($codigoTienda) {
        $db = new Database();
        $docDigitalesConn = $db->getDocDigitalesConnection();
        
        if (!$docDigitalesConn) {
            return [];
        }
        
        // Buscar asesores que tengan el mismo código de tienda
        $sql = "SELECT firma_mail FROM firmas WHERE tienda LIKE ? AND rol = 'USER'";
        $result = sqlsrv_query($docDigitalesConn, $sql, ['%' . $codigoTienda . '%']);
        
        $emails = [];
        if ($result) {
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                if (!empty($row['firma_mail'])) {
                    $emails[] = $row['firma_mail'];
                }
            }
        }
        
        sqlsrv_close($docDigitalesConn);
        return $emails;
    }

    /**
     * Verificar si el usuario actual puede editar documentos
     * @return bool true si puede editar, false si solo puede visualizar
     */
    public static function puedeEditar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Si es ADMIN, siempre puede editar
        if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'ADMIN') {
            return true;
        }
        
        // Si es USER (incluye JEFE DE TIENDA), puede editar
        if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'USER') {
            return true;
        }
        
        return false; // Por defecto no puede editar
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
    public function generarNumeroExpediente() {
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
            // Verificar si es una actualización (si viene orden_id en sesión y estamos editando)
            $ordenId = $_SESSION['orden_id'] ?? null;
            $esActualizacion = !empty($ordenId);
            
            // 🎯 GENERAR NÚMERO DE EXPEDIENTE AUTOMÁTICAMENTE (solo si es nuevo)
            if ($esActualizacion) {
                // Si es actualización, obtener el número de expediente existente
                $sqlExpediente = "SELECT OC_NUMERO_EXPEDIENTE FROM SIST_ORDEN_COMPRA WHERE OC_ID = ?";
                $resultExpediente = sqlsrv_query($this->conn, $sqlExpediente, [$ordenId]);
                if ($resultExpediente && $row = sqlsrv_fetch_array($resultExpediente, SQLSRV_FETCH_ASSOC)) {
                    $numeroExpediente = $row['OC_NUMERO_EXPEDIENTE'];
                } else {
                    $numeroExpediente = $this->generarNumeroExpediente();
                }
            } else {
                // Si existe un número temporal en sesión, usarlo; sino generar uno nuevo
                if (isset($_SESSION['numero_expediente_temporal'])) {
                    $numeroExpediente = $_SESSION['numero_expediente_temporal'];
                    // Limpiar el número temporal de la sesión
                    unset($_SESSION['numero_expediente_temporal']);
                } else {
                    $numeroExpediente = $this->generarNumeroExpediente();
                }
            }
            
            // Procesar archivos primero
            $archivos = [];
            if (!empty($files)) {
                $archivos['OC_ARCHIVO_DNI'] = $this->subirArchivo($files['OC_ARCHIVO_DNI'] ?? null, 'dni');
                $archivos['OC_ARCHIVO_VOUCHER'] = $this->subirArchivo($files['OC_ARCHIVO_VOUCHER'] ?? null, 'voucher');
                $archivos['OC_ARCHIVO_PEDIDO_SALESFORCE'] = $this->subirArchivo($files['OC_ARCHIVO_PEDIDO_SALESFORCE'] ?? null, 'salesforce');
                $archivos['OC_ARCHIVO_DERIVACION_SANTANDER'] = $this->subirArchivo($files['OC_ARCHIVO_DERIVACION_SANTANDER'] ?? null, 'santander');
                
                // Procesar confirmación Santander (antes huella digital del cliente)
                $archivos['OC_CONFIRMACION_SANTANDER'] = $this->subirArchivo($files['OC_CONFIRMACION_SANTANDER_FILE'] ?? null, 'confirmacion_santander');

                // Procesar abonos en campos separados (hasta 7 abonos)
                for ($i = 1; $i <= 7; $i++) {
                    $archivos['OC_ARCHIVO_ABONO' . $i] = $this->subirArchivo($files['OC_ARCHIVO_ABONO' . $i] ?? null, 'abono_' . $i);
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
            
            // Generar token único para aprobación sin login (solo si es nuevo)
            if (!$esActualizacion) {
                $data['OC_TOKEN_APROBACION'] = bin2hex(random_bytes(32)); // Token de 64 caracteres
            }
            
            // Guardar el email y nombre del usuario LOGUEADO (asesor que crea la orden)
            // Esto es necesario para enviarle el correo cuando se apruebe/rechace la orden
            if (isset($_SESSION['usuario_email'])) {
                $data['OC_USUARIO_EMAIL'] = $_SESSION['usuario_email'];
            }
            if (isset($_SESSION['usuario_nombre_completo'])) {
                $data['OC_USUARIO_NOMBRE'] = $_SESSION['usuario_nombre_completo'];
            }
            
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
                'OC_ARCHIVO_DNI_EXISTENTE',
                'OC_ARCHIVO_VOUCHER_EXISTENTE',
                'OC_ARCHIVO_PEDIDO_SALESFORCE_EXISTENTE',
                'OC_ARCHIVO_DERIVACION_SANTANDER_EXISTENTE',
                'OC_CONFIRMACION_SANTANDER_FILE', // Archivo de confirmación Santander (se procesa aparte)
                'OC_CONFIRMACION_SANTANDER_EXISTENTE', // Confirmación Santander existente (no se modifica)
                // OC_CONFIRMACION_SANTANDER se procesa desde $archivos y SÍ debe guardarse
                'OC_CLIENTE_HUELLA', // Campo del modal de firmas (ya no existe en la tabla, fue renombrado a OC_CONFIRMACION_SANTANDER)
                // Campos de abonos existentes
                'OC_ARCHIVO_ABONO1_EXISTENTE',
                'OC_ARCHIVO_ABONO2_EXISTENTE',
                'OC_ARCHIVO_ABONO3_EXISTENTE',
                'OC_ARCHIVO_ABONO4_EXISTENTE',
                'OC_ARCHIVO_ABONO5_EXISTENTE',
                'OC_ARCHIVO_ABONO6_EXISTENTE',
                'OC_ARCHIVO_ABONO7_EXISTENTE',
                // Campos de otros documentos existentes
                'OC_ARCHIVO_OTROS_1_EXISTENTE',
                'OC_ARCHIVO_OTROS_2_EXISTENTE',
                'OC_ARCHIVO_OTROS_3_EXISTENTE',
                'OC_ARCHIVO_OTROS_4_EXISTENTE',
                'OC_ARCHIVO_OTROS_5_EXISTENTE',
                'OC_ARCHIVO_OTROS_6_EXISTENTE',
            ];
            // Campos que son DECIMAL y necesitan limpieza numérica
            $decimalFields = [
                'OC_PRECIO_VENTA', 'OC_BONO_FINANCIAMIENTO', 'OC_TOTAL_EQUIPAMIENTO',
                'OC_PRECIO_TOTAL_COMPRA', 'OC_TIPO_CAMBIO', 'OC_TIPO_CAMBIO_SOL',
                'OC_PAGO_CUENTA', 'OC_SALDO_PENDIENTE', 'OC_CUOTA_INICIAL',
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
                    // Normalizar campo booleano OC_FAKE_PRECIO (checkbox) a bit 0/1
                    if ($key === 'OC_FAKE_PRECIO') {
                        // Si viene marcado, normalmente llega como 'on' u otro valor no vacío
                        $val = !empty($val) ? 1 : 0;
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
            
            // Mantener confirmación Santander existente si no se subió una nueva (solo en actualización)
            if ($esActualizacion && empty($archivos['OC_CONFIRMACION_SANTANDER']) && !empty($data['OC_CONFIRMACION_SANTANDER_EXISTENTE'])) {
                $fields[] = 'OC_CONFIRMACION_SANTANDER';
                $placeholders[] = "?";
                $values[] = $data['OC_CONFIRMACION_SANTANDER_EXISTENTE'];
            }

            if (empty($fields)) {
                throw new Exception("No hay campos OC_ para insertar/actualizar");
            }

            // Si es actualización, hacer UPDATE; si no, hacer INSERT
            if ($esActualizacion) {
                // 🔍 AUDITORÍA: Obtener datos anteriores antes de actualizar
                $datosAnteriores = $this->getOrdenCompra($ordenId);
                
                // Construir SET clause para UPDATE
                $setClauses = [];
                foreach ($fields as $field) {
                    $setClauses[] = "$field = ?";
                }
                $sql = "UPDATE SIST_ORDEN_COMPRA SET " . implode(", ", $setClauses) . " WHERE OC_ID = ?";
                $values[] = $ordenId; // Agregar el ID al final para el WHERE
                
                $result = sqlsrv_query($this->conn, $sql, $values);
                if (!$result) {
                    throw new Exception("Error executing UPDATE query: " . print_r(sqlsrv_errors(), true));
                }
                $id = $ordenId; // Usar el ID existente
                
                // 📝 AUDITORÍA: Registrar cambios después de la actualización
                try {
                    $auditLog = new AuditLog();
                    
                    // Preparar datos nuevos para comparación
                    $datosNuevos = [];
                    for ($i = 0; $i < count($fields); $i++) {
                        $datosNuevos[$fields[$i]] = $values[$i];
                    }
                    
                    // Comparar y obtener lista de cambios
                    $cambios = $auditLog->compararCambios($datosAnteriores, $datosNuevos);
                    
                    // Registrar cada cambio en la auditoría
                    foreach ($cambios as $cambio) {
                        $auditLog->registrarCambio([
                            'document_type' => 'ORDEN_COMPRA',
                            'document_id' => $id,
                            'orden_id' => $id,
                            'numero_expediente' => $numeroExpediente,
                            'action' => 'UPDATE',
                            'field_name' => $cambio['field_name'],
                            'old_value' => $cambio['old_value'],
                            'new_value' => $cambio['new_value'],
                            'description' => 'Actualización de orden de compra'
                        ]);
                    }
                    
                    if (!empty($cambios)) {
                        error_log("✅ Auditoría: Se registraron " . count($cambios) . " cambios en orden #$id");
                    }
                } catch (Exception $e) {
                    // No fallar la operación si falla la auditoría, solo registrar el error
                    error_log("⚠️ Error al registrar auditoría: " . $e->getMessage());
                }
            } else {
                // INSERT normal
                $sql = "INSERT INTO SIST_ORDEN_COMPRA (" . implode(", ", $fields) . ") OUTPUT INSERTED.OC_ID VALUES (" . implode(", ", $placeholders) . ")";
                $result = sqlsrv_query($this->conn, $sql, $values);
                if (!$result) {
                    throw new Exception("Error executing INSERT query: " . print_r(sqlsrv_errors(), true));
                }
                $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                if (!$row) {
                    throw new Exception("No ID returned");
                }
                $id = $row['OC_ID'];
            }

            // Guardar el número de expediente en la sesión
            $_SESSION['numero_expediente'] = $numeroExpediente;
            
            // ❌ DESHABILITADO: No enviar correo automático al guardar
            // El correo solo se enviará cuando se use el botón "Enviar a Cajera"
            /*
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
            */

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
            
            // URL del panel de aprobación con token (permite acceso sin login)
            $token = $orden['OC_TOKEN_APROBACION'] ?? '';
            $urlAprobacion = "http://190.238.78.104:3800/digitalizacion-documentos/aprobacion/panel?id=" . $ordenId . "&token=" . $token;
            
            // Obtener datos con valores por defecto
            $cliente = $orden['OC_COMPRADOR_NOMBRE'] ?? 'No especificado';
            $marca = $orden['OC_VEHICULO_MARCA'] ?? '';
            $modelo = $orden['OC_VEHICULO_MODELO'] ?? '';
            $vehiculo = trim($marca . ' ' . $modelo) ?: 'No especificado';
            $asesor = $orden['OC_ASESOR_VENTA'] ?? 'No especificado';
            $chasis = $orden['OC_VEHICULO_CHASIS'] ?? 'No especificado';
            $precio = $orden['OC_PRECIO_VENTA'] ?? 0;
            $precioFormateado = $precio ? 'S/ ' . number_format($precio, 2) : 'No especificado';
            
            // Construir el HTML del correo
            $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #1e3a8a;'>📝 Orden de Compra Pendiente de Aprobación</h2>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <p><strong>Número de Expediente:</strong> {$numeroExpediente}</p>
                    <p><strong>Cliente:</strong> {$cliente}</p>
                    <hr style='border: none; border-top: 1px solid #e0e0e0; margin: 15px 0;'>
                    <p><strong>Vehículo:</strong> {$vehiculo}</p>
                    <p><strong>Marca:</strong> {$marca}</p>
                    <p><strong>Modelo:</strong> {$modelo}</p>
                    <p><strong>Chasis:</strong> {$chasis}</p>
                    <p><strong>Precio:</strong> {$precioFormateado}</p>
                    <hr style='border: none; border-top: 1px solid #e0e0e0; margin: 15px 0;'>
                    <p><strong>Asesor:</strong> {$asesor}</p>
                    <p><strong>Estado:</strong> <span style='color: orange; font-weight: bold;'>⏳ Pendiente de aprobación</span></p>
                </div>
                
                <p style='text-align: center;'>
                    <a href='{$urlAprobacion}' 
                       style='background-color: #1e3a8a; color: white; padding: 15px 30px; 
                              text-decoration: none; border-radius: 8px; display: inline-block;
                              font-weight: bold; font-size: 16px;'>
                        👁️ Ver y Aprobar Orden
                    </a>
                </p>
                <br>
                <p style='color: #666; font-size: 12px; text-align: center;'>
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
            $params = [$id];
            $whereConditions = ["OC_ID = ?"];
            
            // 🔒 FILTRO POR ROL
            if (isset($_SESSION['usuario_rol'])) {
                $rol = $_SESSION['usuario_rol'];
                $esJefeTienda = self::esJefeDeTienda();
                
                if ($rol === 'USER' && $esJefeTienda) {
                    // JEFE DE TIENDA: Filtrar por emails de asesores de su tienda
                    $tiendaJefe = $_SESSION['usuario_tiendas'] ?? '';
                    if (!empty($tiendaJefe)) {
                        $emailsAsesores = $this->getEmailsAsesoresPorTienda($tiendaJefe);
                        
                        if (!empty($emailsAsesores)) {
                            $emailConditions = [];
                            foreach ($emailsAsesores as $email) {
                                $emailConditions[] = "OC_USUARIO_EMAIL = ?";
                                $params[] = $email;
                            }
                            $whereConditions[] = '(' . implode(' OR ', $emailConditions) . ')';
                        }
                    }
                    
                } elseif ($rol === 'USER' && !$esJefeTienda) {
                    // USER normal: Solo ve sus propias órdenes
                    if (isset($_SESSION['usuario_email'])) {
                        $whereConditions[] = "OC_USUARIO_EMAIL = ?";
                        $params[] = $_SESSION['usuario_email'];
                    }
                }
                // Si es ADMIN, no se agrega filtro adicional (puede ver cualquier orden)
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            $sql = "SELECT * FROM SIST_ORDEN_COMPRA WHERE $whereClause";
            $result = sqlsrv_query($this->conn, $sql, $params);
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

    /**
     * Obtener orden de compra por número de expediente
     */
    public function getOrdenCompraPorExpediente($numeroExpediente) {
        try {
            $sql = "SELECT * FROM SIST_ORDEN_COMPRA WHERE OC_NUMERO_EXPEDIENTE = ?";
            $result = sqlsrv_query($this->conn, $sql, [$numeroExpediente]);
            
            if (!$result) {
                error_log("Error en getOrdenCompraPorExpediente: " . print_r(sqlsrv_errors(), true));
                return null;
            }
            
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            return $row ?: null;
        } catch (Exception $e) {
            error_log("Excepción en getOrdenCompraPorExpediente: " . $e->getMessage());
            return null;
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
        // Primero consultar en BD Stock
        $db = new Database();
        $stockConn = $db->getStockConnection();
        if (!$stockConn) {
            return null;
        }

        $sqlStock = "SELECT TOP 1 STO_CHASIS, STO_MARCA, STO_MODELO, STO_VERSION, STO_COLOR, STO_FSC, STO_AFAB, STO_COSTP, STO_VENDEDOR
                     FROM STOCK
                     WHERE STO_CHASIS = ?
                     ORDER BY STO_AFAB DESC";
        $resultStock = sqlsrv_query($stockConn, $sqlStock, [$chasis]);
        if (!$resultStock) {
            sqlsrv_close($stockConn);
            return null;
        }
        $vehiculo = sqlsrv_fetch_array($resultStock, SQLSRV_FETCH_ASSOC);
        sqlsrv_close($stockConn);

        if (!$vehiculo) {
            return null;
        }

        // Ahora consultar motor, clase y tipo de combustible en BD RSFACCAR12
        $rsfaccarConn = $db->getRsfaccar12Connection();
        if ($rsfaccarConn) {
            $sqlFac = "SELECT V.VE_CNROMOT AS MOTOR, V.VE_CCLASE, CL.TG_CDESCRI AS CLASE, V.VE_CTIPCOM AS TIPO_COMBUSTIBLE
                       FROM FT0002VEHI V
                       LEFT JOIN AL0002TABL CL
                           ON V.VE_CCLASE = CL.TG_CCLAVE
                          AND CL.TG_CCOD = 'V9'
                       WHERE V.VE_CCHASIS = ?";
            $resultFac = sqlsrv_query($rsfaccarConn, $sqlFac, [$chasis]);
            if ($resultFac) {
                $facData = sqlsrv_fetch_array($resultFac, SQLSRV_FETCH_ASSOC);
                if ($facData) {
                    $vehiculo['MOTOR'] = $facData['MOTOR'];
                    $vehiculo['CLASE'] = $facData['CLASE'];
                    $vehiculo['TIPO_COMBUSTIBLE'] = $facData['TIPO_COMBUSTIBLE'];
                }
            }
            sqlsrv_close($rsfaccarConn);
        }

        // Mapear campos para compatibilidad con el código existente
        $vehiculo['VE_CCHASIS'] = $vehiculo['STO_CHASIS'];
        $vehiculo['MARCA'] = $vehiculo['STO_MARCA'];
        $vehiculo['MODELO'] = $vehiculo['STO_MODELO'];
        $vehiculo['VERSION'] = $vehiculo['STO_VERSION'];
        $vehiculo['COLOR'] = $vehiculo['STO_COLOR'];
        $vehiculo['FSC'] = $vehiculo['STO_FSC'];
        $vehiculo['ANIO_FABRICACION'] = $vehiculo['STO_AFAB'];
        $vehiculo['PRECIO'] = $vehiculo['STO_COSTP'];
        $vehiculo['VENDEDOR'] = $vehiculo['STO_VENDEDOR'] ?? '';

        // Consultar datos adicionales desde la API externa (garantía, etc.)
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

        return $vehiculo;
    }

    /**
     * Comparar si dos nombres completos son la misma persona
     * Maneja diferentes formatos: "APELLIDOS NOMBRES" vs "NOMBRES APELLIDOS"
     * Quita comas para evitar problemas de comparación
     */
    private function compararNombres($nombre1, $nombre2) {
        // Normalizar: quitar comas, espacios extras, convertir a mayúsculas
        $nombre1 = trim(strtoupper(str_replace(',', '', $nombre1)));
        $nombre2 = trim(strtoupper(str_replace(',', '', $nombre2)));
        
        // Si son exactamente iguales, retornar true
        if ($nombre1 === $nombre2) {
            return true;
        }
        
        // Dividir en palabras y ordenar alfabéticamente
        $palabras1 = preg_split('/\s+/', $nombre1);
        $palabras2 = preg_split('/\s+/', $nombre2);
        
        sort($palabras1);
        sort($palabras2);
        
        // Comparar las palabras ordenadas
        return $palabras1 === $palabras2;
    }

    /**
     * Validar si el vehículo está asignado al asesor logueado
     * Retorna: ['valido' => bool, 'vendedor_asignado' => string, 'mensaje' => string]
     */
    public function validarAsignacionVehiculo($chasis) {
        // Obtener datos del vehículo
        $vehiculo = $this->buscarVehiculoPorChasis($chasis);
        
        if (!$vehiculo) {
            return [
                'valido' => false,
                'vendedor_asignado' => null,
                'mensaje' => 'Vehículo no encontrado en el sistema'
            ];
        }
        
        $vendedorAsignado = trim($vehiculo['VENDEDOR'] ?? '');
        
        // Si no tiene vendedor asignado, permitir continuar
        if (empty($vendedorAsignado)) {
            return [
                'valido' => true,
                'vendedor_asignado' => null,
                'mensaje' => 'Vehículo sin asignación específica'
            ];
        }
        
        // Obtener nombre del usuario logueado
        $nombreUsuario = $_SESSION['usuario_nombre_completo'] ?? '';
        
        if (empty($nombreUsuario)) {
            return [
                'valido' => false,
                'vendedor_asignado' => $vendedorAsignado,
                'mensaje' => 'No se pudo verificar el usuario logueado'
            ];
        }
        
        // Comparar nombres usando la misma lógica que en obtenerVehiculosPorNombreAsesor:
        // se considera el mismo asesor si TODAS las palabras del nombre de sesión
        // están contenidas en el nombre del vendedor del STOCK, aunque este tenga
        // más apellidos o nombres (caso: "SAMAI ROJAS" vs "ROJAS MEZA SAMAI YISLETH").

        // Normalizar ambos nombres (mayúsculas, sin comas, espacios extra)
        $nombreUsuarioNorm = trim(strtoupper(str_replace(',', '', $nombreUsuario)));
        $vendedorAsignadoNorm = trim(strtoupper(str_replace(',', '', $vendedorAsignado)));

        // Dividir el nombre del usuario en palabras y verificar que cada una
        // esté presente en el nombre del vendedor asignado.
        $palabrasUsuario = array_filter(explode(' ', $nombreUsuarioNorm));
        $esElMismo = true;

        foreach ($palabrasUsuario as $palabra) {
            if ($palabra === '') {
                continue;
            }
            if (stripos($vendedorAsignadoNorm, $palabra) === false) {
                $esElMismo = false;
                break;
            }
        }
        
        return [
            'valido' => $esElMismo,
            'vendedor_asignado' => $vendedorAsignado,
            'mensaje' => $esElMismo 
                ? 'Vehículo asignado correctamente' 
                : "Este vehículo está asignado a: $vendedorAsignado"
        ];
    }
    
    /**
     * Enviar notificación por correo al asesor cuando otro usuario intenta usar su vehículo
     * NO hace UPDATE en la base de datos, solo envía correo
     */
    public function enviarNotificacionIntentoUso($chasis, $vendedorAsignado) {
        try {
            // Obtener email del asesor asignado desde la tabla firmas
            $db = new Database();
            $conn = $db->getDocDigitalesConnection();
            
            if (!$conn) {
                return ['success' => false, 'message' => 'Error de conexión'];
            }
            
            // Buscar email del asesor por su nombre en la tabla firmas
            // Concatenar firma_nombre + ' ' + firma_apellido y comparar con el vendedor asignado
            $sql = "SELECT firma_mail FROM firmas WHERE firma_nombre + ' ' + firma_apellido LIKE ?";
            $params = ['%' . $vendedorAsignado . '%'];
            $result = sqlsrv_query($conn, $sql, $params);
            
            $emailAsesor = null;
            if ($result && $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $emailAsesor = $row['firma_mail'];
            }
            
            sqlsrv_close($conn);
            
            if (!$emailAsesor) {
                return ['success' => false, 'message' => 'No se encontró email del asesor en la tabla firmas'];
            }
            
            // Obtener datos del usuario que intentó usar el vehículo
            $usuarioIntento = $_SESSION['usuario_nombre_completo'] ?? 'Usuario desconocido';
            
            // Enviar correo al asesor asignado
            $emailData = [
                'to' => $emailAsesor,
                'subject' => '⚠️ Intento de uso de vehículo asignado - Chasis: ' . $chasis,
                'html' => "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                        <div style='background: #dc2626; color: white; padding: 20px; text-align: center;'>
                            <h1 style='margin: 0;'>⚠️ Notificación de Intento de Uso</h1>
                        </div>
                        
                        <div style='padding: 30px; background: #f8f9fa;'>
                            <p style='font-size: 16px; color: #333;'>Hola <strong>{$vendedorAsignado}</strong>,</p>
                            
                            <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                                Te informamos que <strong>{$usuarioIntento}</strong> ha intentado usar un vehículo que está asignado a ti.
                            </p>
                            
                            <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;'>
                                <p style='margin: 0; font-size: 14px; color: #856404;'>
                                    <strong>Chasis del vehículo:</strong> {$chasis}
                                </p>
                            </div>
                            
                            <p style='font-size: 14px; color: #666;'>
                                El sistema ha bloqueado el intento automáticamente. No se requiere ninguna acción de tu parte.
                            </p>
                        </div>
                        
                        <div style='background: #e9ecef; padding: 15px; text-align: center; font-size: 12px; color: #666;'>
                            Sistema de Digitalización - Interamericana Norte
                        </div>
                    </div>
                "
            ];
            
            $emailSent = $this->enviarCorreo($emailData);
            
            return [
                'success' => $emailSent,
                'message' => $emailSent ? 'Notificación enviada al asesor' : 'Error al enviar notificación',
                'email_destino' => $emailAsesor
            ];
            
        } catch (Exception $e) {
            error_log("Error al enviar notificación: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
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
            // NO guardar en sesión - solo retornar la firma
            // La sesión debe mantenerse con el usuario que hizo login
            return 'http://190.238.78.104:3800' . $row['firma_data'];
        }
        return null;
    }

    public function getCentrosCosto() {
        // Intentar obtener datos desde la API primero
        $apiUrl = 'https://opensheet.elk.sh/155IT8et2XYhMK6bkr7OJBtCzIHS6X9Ia_6O99Gm0WAk/Hoja%201';
        
        $apiData = @file_get_contents($apiUrl);
        if ($apiData !== false) {
            $centros = json_decode($apiData, true);
            if ($centros && is_array($centros)) {
                return $centros;
            }
        }
        
        // Si la API falla, usar el archivo JSON local como respaldo
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
        $centrosMap = []; // Usar map para manejar duplicados mejor

        foreach ($centros as $centro) {
            $centroAgencia = trim($centro['AGENCIA'] ?? $centro['agencia'] ?? '');
            $centroNombre = trim($centro['NOMBRE'] ?? $centro['nombre'] ?? '');

            // Limpiar espacios para comparación consistente
            if ($centroAgencia === $agencia && $centroNombre === $nombre) {
                $centroCosto = trim($centro['CENTRO DE COSTO'] ?? $centro['centro de costo'] ?? '');
                $nombreCC = trim($centro['NOMBRE CC'] ?? $centro['nombre cc'] ?? '');
                $email = trim($centro['EMAIL'] ?? $centro['email'] ?? '');
                
                // Si ya existe este centro de costo, solo actualizar si el nuevo tiene NOMBRE_CC no vacío
                if (isset($centrosMap[$centroCosto])) {
                    // Si el registro actual tiene NOMBRE_CC y el anterior no, reemplazar
                    if (!empty($nombreCC) && empty($centrosMap[$centroCosto]['NOMBRE_CC'])) {
                        $centrosMap[$centroCosto] = [
                            'CENTRO_COSTO' => $centroCosto,
                            'NOMBRE_CC' => $nombreCC,
                            'EMAIL' => $email
                        ];
                    }
                } else {
                    // Primera vez que vemos este centro de costo
                    $centrosMap[$centroCosto] = [
                        'CENTRO_COSTO' => $centroCosto,
                        'NOMBRE_CC' => $nombreCC,
                        'EMAIL' => $email
                    ];
                }
            }
        }

        // Convertir el map a array indexado
        return array_values($centrosMap);
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
            // IMPORTANTE: El correo se envía al ASESOR que CREÓ la orden
            // El email se obtiene de la orden (guardado al momento de crearla)
            $emailAsesor = $orden['OC_USUARIO_EMAIL'] ?? null;
            $nombreAsesor = $orden['OC_USUARIO_NOMBRE'] ?? 'Asesor';

            if (!$emailAsesor) {
                error_log("ERROR: No hay email del asesor guardado en la orden");
                error_log("Orden completa: " . print_r($orden, true));
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
                    'fecha' => 'ACC_FECHA_CREACION',
                    'id' => 'ACC_ID'
                ],
                'actorizacion-datos-personales' => [
                    'table' => 'SIST_AUTORIZACION_DATOS_PERSONALES',
                    'prefix' => 'ADP_',
                    'fk' => 'ADP_DOCUMENTO_VENTA_ID',
                    'fecha' => 'ADP_FECHA_CREACION',
                    'id' => 'ADP_ID'
                ],
                'carta_conocimiento_aceptacion' => [
                    'table' => 'SIST_CARTA_CONOCIMIENTO_ACEPTACION',
                    'prefix' => 'CCA_',
                    'fk' => 'CCA_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CCA_FECHA_CREACION',
                    'id' => 'CCA_ID'
                ],
                'carta_recepcion' => [
                    'table' => 'SIST_CARTA_RECEPCION',
                    'prefix' => 'CR_',
                    'fk' => 'CR_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CR_FECHA_CREACION',
                    'id' => 'CR_ID'
                ],
                'carta-caracteristicas' => [
                    'table' => 'SIST_CARTA_CARACTERISTICAS',
                    'prefix' => 'CC_',
                    'fk' => 'CC_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CC_FECHA_CREACION',
                    'id' => 'CC_ID'
                ],
                'carta_caracteristicas_banbif' => [
                    'table' => 'SIST_CARTA_CARACTERISTICAS_BANBIF',
                    'prefix' => 'CCB_',
                    'fk' => 'CCB_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CCB_FECHA_CREACION',
                    'id' => 'CCB_ID'
                ],
                'carta_felicitaciones' => [
                    'table' => 'SIST_CARTA_FELICITACIONES',
                    'prefix' => 'CF_',
                    'fk' => 'CF_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CF_FECHA_CREACION',
                    'id' => 'CF_ID'
                ],
                'carta_obsequios' => [
                    'table' => 'SIST_CARTA_OBSEQUIOS',
                    'prefix' => 'CO_',
                    'fk' => 'CO_DOCUMENTO_VENTA_ID',
                    'fecha' => 'CO_FECHA_CREACION',
                    'id' => 'CO_ID'
                ],
                'politica_proteccion_datos' => [
                    'table' => 'SIST_POLITICA_PROTECCION_DATOS',
                    'prefix' => 'PPD_',
                    'fk' => 'PPD_DOCUMENTO_VENTA_ID',
                    'fecha' => 'PPD_FECHA_CREACION',
                    'id' => 'PPD_ID'
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
            $idField = $config['id'];
            
            // Verificar si ya existe un registro para esta orden
            $sqlCheck = "SELECT {$idField} FROM $table WHERE $fkField = ?";
            $resultCheck = sqlsrv_query($this->conn, $sqlCheck, [$ordenId]);
            $existingRow = $resultCheck ? sqlsrv_fetch_array($resultCheck, SQLSRV_FETCH_ASSOC) : null;
            
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
                    
                    // Campos que NO deben ser convertidos a fecha aunque contengan "FECHA" en el nombre
                    $camposTextoFecha = ['CR_FECHA_DIA', 'CR_FECHA_MES', 'CR_FECHA_ANIO'];
                    
                    // Debug: registrar campos de fecha
                    if (in_array($key, $camposTextoFecha)) {
                        error_log("📅 Campo texto fecha: $key = $value (NO se convertirá)");
                    }
                    
                    // Validar campos de fecha específicamente (excepto los que son texto)
                    if (stripos($key, 'FECHA') !== false && !in_array($key, $camposTextoFecha)) {
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
                    } 
                    // Validar campos numéricos (PRECIO, CUOTA, MONTO, etc.)
                    elseif (stripos($key, 'PRECIO') !== false || 
                            stripos($key, 'CUOTA') !== false || 
                            stripos($key, 'MONTO') !== false) {
                        // Limpiar el valor: quitar monedas (US$, MN, S/.), espacios, comas
                        if ($value === '' || $value === null) {
                            $values[] = null;
                        } else {
                            // Quitar símbolos de moneda y espacios
                            $cleanValue = preg_replace('/[^\d.-]/', '', $value);
                            // Convertir a número o null si no es válido
                            $numericValue = is_numeric($cleanValue) ? (float)$cleanValue : null;
                            $values[] = $numericValue;
                        }
                    } 
                    else {
                        // Para otros campos, usar el valor o null si está vacío
                        $values[] = $value !== '' ? $value : null;
                    }
                }
            }

            if (empty($fields)) {
                throw new Exception("No hay campos para insertar/actualizar");
            }

            // Si ya existe, hacer UPDATE; si no, hacer INSERT
            if ($existingRow) {
                // 🔍 AUDITORÍA: Obtener datos anteriores antes de actualizar
                $sqlGetOld = "SELECT * FROM $table WHERE $fkField = ?";
                $resultOld = sqlsrv_query($this->conn, $sqlGetOld, [$ordenId]);
                $datosAnteriores = $resultOld ? sqlsrv_fetch_array($resultOld, SQLSRV_FETCH_ASSOC) : [];
                
                // UPDATE: construir SET clause
                $setClauses = [];
                $updateValues = [];
                
                foreach ($fields as $index => $field) {
                    if ($field !== $fkField) { // No actualizar la clave foránea
                        $setClauses[] = "$field = ?";
                        $updateValues[] = $values[$index];
                    }
                }
                
                // Verificar que hay campos para actualizar
                if (empty($setClauses)) {
                    error_log("⚠️ No hay campos para actualizar (solo FK). Saltando UPDATE.");
                    return ['success' => true];
                }
                
                $updateValues[] = $ordenId; // Para el WHERE
                
                $sql = "UPDATE $table SET " . implode(", ", $setClauses) . " WHERE $fkField = ?";
                error_log("🔄 UPDATE - SQL: $sql");
                error_log("Valores: " . print_r($updateValues, true));
                
                $result = sqlsrv_query($this->conn, $sql, $updateValues);
                
                // 📝 AUDITORÍA: Registrar cambios después de la actualización
                if ($result && !empty($datosAnteriores)) {
                    try {
                        $auditLog = new AuditLog();
                        
                        // Preparar datos nuevos para comparación
                        $datosNuevos = [];
                        foreach ($fields as $index => $field) {
                            $datosNuevos[$field] = $values[$index];
                        }
                        
                        // Obtener número de expediente de la orden
                        $sqlExpediente = "SELECT OC_NUMERO_EXPEDIENTE FROM SIST_ORDEN_COMPRA WHERE OC_ID = ?";
                        $resultExpediente = sqlsrv_query($this->conn, $sqlExpediente, [$ordenId]);
                        $numeroExpediente = '';
                        if ($resultExpediente && $rowExp = sqlsrv_fetch_array($resultExpediente, SQLSRV_FETCH_ASSOC)) {
                            $numeroExpediente = $rowExp['OC_NUMERO_EXPEDIENTE'];
                        }
                        
                        // Comparar y obtener lista de cambios
                        $cambios = $auditLog->compararCambios($datosAnteriores, $datosNuevos);
                        
                        // Registrar cada cambio en la auditoría
                        foreach ($cambios as $cambio) {
                            $auditLog->registrarCambio([
                                'document_type' => strtoupper(str_replace(['-', '_'], ' ', $documentType)),
                                'document_id' => $existingRow[$idField],
                                'orden_id' => $ordenId,
                                'numero_expediente' => $numeroExpediente,
                                'action' => 'UPDATE',
                                'field_name' => $cambio['field_name'],
                                'old_value' => $cambio['old_value'],
                                'new_value' => $cambio['new_value'],
                                'description' => 'Actualización de ' . $config['table']
                            ]);
                        }
                        
                        if (!empty($cambios)) {
                            error_log("✅ Auditoría: Se registraron " . count($cambios) . " cambios en $table");
                        }
                    } catch (Exception $e) {
                        // No fallar la operación si falla la auditoría, solo registrar el error
                        error_log("⚠️ Error al registrar auditoría en $table: " . $e->getMessage());
                    }
                }
            } else {
                // INSERT
                $sql = "INSERT INTO $table (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
                error_log("➕ INSERT - SQL: $sql");
                error_log("Valores: " . print_r($values, true));
                
                $result = sqlsrv_query($this->conn, $sql, $values);
            }
            
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
     * Filtra por rol: ADMIN ve todo, JEFE DE MARCA ve por marca+tienda, USER ve solo lo suyo
     */
    public function buscarPorNumeroExpediente($numeroExpediente) {
        try {
            $params = [$numeroExpediente];
            $whereConditions = ["OC_NUMERO_EXPEDIENTE = ?"];
            
            // 🔒 FILTRO POR ROL
            if (isset($_SESSION['usuario_rol'])) {
                $rol = $_SESSION['usuario_rol'];
                $esJefeTienda = self::esJefeDeTienda();
                
                if ($rol === 'USER' && $esJefeTienda) {
                    // JEFE DE TIENDA: Filtrar por emails de asesores de su tienda
                    $tiendaJefe = $_SESSION['usuario_tiendas'] ?? '';
                    if (!empty($tiendaJefe)) {
                        $emailsAsesores = $this->getEmailsAsesoresPorTienda($tiendaJefe);
                        
                        if (!empty($emailsAsesores)) {
                            $emailConditions = [];
                            foreach ($emailsAsesores as $email) {
                                $emailConditions[] = "OC_USUARIO_EMAIL = ?";
                                $params[] = $email;
                            }
                            $whereConditions[] = '(' . implode(' OR ', $emailConditions) . ')';
                        }
                    }
                    
                } elseif ($rol === 'USER' && !$esJefeTienda) {
                    // USER normal: Solo ve sus propias órdenes
                    if (isset($_SESSION['usuario_email'])) {
                        $whereConditions[] = "OC_USUARIO_EMAIL = ?";
                        $params[] = $_SESSION['usuario_email'];
                    }
                }
                // Si es ADMIN, no se agrega filtro adicional
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            $sql = "SELECT * FROM SIST_ORDEN_COMPRA WHERE $whereClause";
            $result = sqlsrv_query($this->conn, $sql, $params);
            
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
     * Filtra por rol: ADMIN ve todo, JEFE DE MARCA ve por marca+tienda, USER ve solo lo suyo
     */
    public function listarOrdenesCompra($page = 1, $perPage = 20, $search = '') {
        try {
            $offset = ($page - 1) * $perPage;
            
            $whereConditions = [];
            $params = [];
            
            // 🔒 FILTRO POR ROL
            if (isset($_SESSION['usuario_rol'])) {
                $rol = $_SESSION['usuario_rol'];
                $esJefeTienda = self::esJefeDeTienda();
                
                if ($rol === 'USER' && $esJefeTienda) {
                    // JEFE DE TIENDA: Filtrar por emails de asesores de su tienda
                    $tiendaJefe = $_SESSION['usuario_tiendas'] ?? '';
                    if (!empty($tiendaJefe)) {
                        $emailsAsesores = $this->getEmailsAsesoresPorTienda($tiendaJefe);
                        
                        if (!empty($emailsAsesores)) {
                            $emailConditions = [];
                            foreach ($emailsAsesores as $email) {
                                $emailConditions[] = "OC_USUARIO_EMAIL = ?";
                                $params[] = $email;
                            }
                            $whereConditions[] = '(' . implode(' OR ', $emailConditions) . ')';
                        }
                    }
                    
                } elseif ($rol === 'USER' && !$esJefeTienda) {
                    // USER normal: Solo ve sus propias órdenes
                    if (isset($_SESSION['usuario_email'])) {
                        $whereConditions[] = "OC_USUARIO_EMAIL = ?";
                        $params[] = $_SESSION['usuario_email'];
                    }
                }
            }
            // Si es ADMIN, no se agrega filtro (ve todas las órdenes)
            
            // Filtro de búsqueda
            if (!empty($search)) {
                $whereConditions[] = "(OC_NUMERO_EXPEDIENTE LIKE ? OR OC_COMPRADOR_NOMBRE LIKE ? OR OC_COMPRADOR_NUMERO_DOCUMENTO LIKE ?)";
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            
            // Contar total de registros
            $sqlCount = "SELECT COUNT(*) as total FROM SIST_ORDEN_COMPRA $whereClause";
            $resultCount = sqlsrv_query($this->conn, $sqlCount, $params);
            $rowCount = sqlsrv_fetch_array($resultCount, SQLSRV_FETCH_ASSOC);
            $total = $rowCount['total'];
            
            // Obtener registros paginados
            $sql = "SELECT OC_ID, OC_NUMERO_EXPEDIENTE, OC_COMPRADOR_NOMBRE, OC_COMPRADOR_NUMERO_DOCUMENTO, 
                           OC_VEHICULO_MARCA, OC_VEHICULO_MODELO, OC_FECHA_ORDEN, OC_FECHA_CREACION,
                           OC_ESTADO_APROBACION
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
                'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD' => ['id_field' => 'ACC_ID', 'fk_field' => 'ACC_DOCUMENTO_VENTA_ID', 'nombre' => 'Acta Conocimiento Conformidad', 'layout' => 'acta_conocimiento_conformidad'],
                'SIST_AUTORIZACION_DATOS_PERSONALES' => ['id_field' => 'ADP_ID', 'fk_field' => 'ADP_DOCUMENTO_VENTA_ID', 'nombre' => 'Autorización de Uso de Imagen', 'layout' => 'actorizacion-datos-personales'],
                'SIST_CARTA_CONOCIMIENTO_ACEPTACION' => ['id_field' => 'CCA_ID', 'fk_field' => 'CCA_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Conocimiento Aceptación', 'layout' => 'carta_conocimiento_aceptacion'],
                'SIST_CARTA_RECEPCION' => ['id_field' => 'CR_ID', 'fk_field' => 'CR_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Recepción', 'layout' => 'carta_recepcion'],
                'SIST_CARTA_CARACTERISTICAS' => ['id_field' => 'CC_ID', 'fk_field' => 'CC_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Características', 'layout' => 'carta-caracteristicas'],
                'SIST_CARTA_CARACTERISTICAS_BANBIF' => ['id_field' => 'CCB_ID', 'fk_field' => 'CCB_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Características Banbif', 'layout' => 'carta_caracteristicas_banbif'],
                'SIST_CARTA_FELICITACIONES' => ['id_field' => 'CF_ID', 'fk_field' => 'CF_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Felicitaciones', 'layout' => 'carta_felicitaciones'],
                'SIST_CARTA_OBSEQUIOS' => ['id_field' => 'CO_ID', 'fk_field' => 'CO_DOCUMENTO_VENTA_ID', 'nombre' => 'Carta Obsequios', 'layout' => 'carta_obsequios'],
                'SIST_POLITICA_PROTECCION_DATOS' => ['id_field' => 'PPD_ID', 'fk_field' => 'PPD_DOCUMENTO_VENTA_ID', 'nombre' => 'Política Protección Datos', 'layout' => 'politica_proteccion_datos']
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
                            'id' => $config['layout'],  // Usar el nombre del layout en lugar del ID numérico
                            'db_id' => $row[$config['id_field']]  // Guardar el ID de BD por si se necesita
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
            // Obtener datos desde la API en lugar del archivo JSON local
            $apiUrl = 'https://opensheet.elk.sh/1HmRbKs7uTGhd5vN99bb_f621AzncNGO8iFe5s6ITkM0/BD';

            error_log("Obteniendo datos de mantenimiento desde API: $apiUrl");

            $apiData = @file_get_contents($apiUrl);
            if (!$apiData) {
                error_log("❌ Error al obtener datos de la API de mantenimiento");
                return null;
            }

            $vehiculos = json_decode($apiData, true);
            if (!$vehiculos || !is_array($vehiculos)) {
                error_log("❌ Error al decodificar JSON de la API: " . json_last_error_msg());
                return null;
            }

            error_log("✓ Datos obtenidos correctamente de la API. Total vehículos: " . count($vehiculos));

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
                        'PERIODICIDAD' => $periodicidad ? 'cada ' . $periodicidad : ''
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

    /**
     * Obtener vehículos asignados al asesor logueado
     * Filtra por marca del asesor y nombre del vendedor (sin comas)
     */
    public function obtenerVehiculosAsesor() {
        $db = new Database();
        $stockConn = $db->getStockConnection();
        
        if (!$stockConn) {
            return [];
        }
        
        // Obtener datos del asesor logueado
        $nombreAsesor = $_SESSION['usuario_nombre_completo'] ?? '';
        $marcasAsesor = $_SESSION['usuario_marcas'] ?? '';
        
        // error_log("🔍 DEBUG - Nombre asesor: " . $nombreAsesor);
        // error_log("🔍 DEBUG - Marcas asesor: " . $marcasAsesor);
        
        if (empty($nombreAsesor) || empty($marcasAsesor)) {
            // error_log("❌ DEBUG - Nombre o marca vacíos");
            sqlsrv_close($stockConn);
            return [];
        }
        
        // Normalizar nombre (quitar comas)
        $nombreNormalizado = trim(strtoupper(str_replace(',', '', $nombreAsesor)));
        // error_log("🔍 DEBUG - Nombre normalizado: " . $nombreNormalizado);
        
        // Convertir marcas a array
        $arrayMarcas = array_map('trim', explode(',', $marcasAsesor));
        
        // Verificar si el asesor tiene MULTIMARCA o MULTIMARCAS
        $esMultimarca = false;
        foreach ($arrayMarcas as $marca) {
            $marcaUpper = strtoupper($marca);
            if ($marcaUpper === 'MULTIMARCA' || $marcaUpper === 'MULTIMARCAS') {
                $esMultimarca = true;
                break;
            }
        }
        
        // Construir condiciones de marca
        $marcasConditions = [];
        $marcasParams = [];
        
        if ($esMultimarca) {
            // Si es MULTIMARCA, no filtrar por marca (mostrar todos)
            $marcasWhere = "1=1"; // Condición siempre verdadera
        } else {
            // Filtrar por marcas específicas
            foreach ($arrayMarcas as $marca) {
                $marcasConditions[] = "STO_MARCA LIKE ?";
                $marcasParams[] = "%{$marca}%";
            }
            $marcasWhere = implode(' OR ', $marcasConditions);
        }
        
        // Consulta SQL
        $sql = "SELECT 
                    STO_CHASIS,
                    STO_MARCA,
                    STO_UBICACION,
                    STO_VENDEDOR,
                    STO_FFACT
                FROM STOCK
                WHERE 
                    ({$marcasWhere})
                    AND STO_VENDEDOR IS NOT NULL
                    AND STO_VENDEDOR != ''
                ORDER BY STO_FFACT ASC";
        
        // error_log("🔍 DEBUG - SQL: " . $sql);
        // error_log("🔍 DEBUG - Es Multimarca: " . ($esMultimarca ? 'SÍ' : 'NO'));
        // error_log("🔍 DEBUG - Params: " . print_r($marcasParams, true));
        
        $result = sqlsrv_query($stockConn, $sql, $marcasParams);
        
        if (!$result) {
            // error_log("❌ Error en consulta de vehículos: " . print_r(sqlsrv_errors(), true));
            sqlsrv_close($stockConn);
            return [];
        }
        
        $vehiculos = [];
        $totalFilas = 0;
        $coincidencias = 0;
        
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $totalFilas++;
            
            // Normalizar nombre del vendedor (quitar comas)
            $vendedorNormalizado = trim(strtoupper(str_replace(',', '', $row['STO_VENDEDOR'] ?? '')));
            
            // error_log("🔍 DEBUG - Fila {$totalFilas} - Vendedor BD: '{$row['STO_VENDEDOR']}' | Normalizado: '{$vendedorNormalizado}'");
            
            // Comparar nombres usando la función existente
            if ($this->compararNombresNormalizados($nombreNormalizado, $vendedorNormalizado)) {
                $coincidencias++;
                // error_log("✅ DEBUG - COINCIDE! Agregando vehículo: " . $row['STO_CHASIS']);
                // Formatear fecha
                $fecha = '';
                if ($row['STO_FFACT'] instanceof DateTime) {
                    $fecha = $row['STO_FFACT']->format('d/m/Y');
                }
                
                $vehiculos[] = [
                    'chasis' => $row['STO_CHASIS'] ?? '',
                    'marca' => $row['STO_MARCA'] ?? '',
                    'ubicacion' => $row['STO_UBICACION'] ?? '',
                    'vendedor' => $row['STO_VENDEDOR'] ?? '',
                    'fecha' => $fecha
                ];
            }
        }
        
        // error_log("📊 DEBUG - Total filas: {$totalFilas} | Coincidencias: {$coincidencias}");
        // error_log("📊 DEBUG - Vehículos retornados: " . count($vehiculos));
        
        sqlsrv_close($stockConn);
        return $vehiculos;
    }
    
    /**
     * Comparar nombres normalizados (ya sin comas)
     * Prueba ambos órdenes: "NOMBRE APELLIDO" vs "APELLIDO NOMBRE"
     */
    private function compararNombresNormalizados($nombre1, $nombre2) {
        // Si son exactamente iguales
        if ($nombre1 === $nombre2) {
            return true;
        }
        
        // Dividir en palabras y ordenar alfabéticamente
        $palabras1 = preg_split('/\s+/', $nombre1);
        $palabras2 = preg_split('/\s+/', $nombre2);
        
        // Filtrar palabras vacías
        $palabras1 = array_filter($palabras1);
        $palabras2 = array_filter($palabras2);
        
        sort($palabras1);
        sort($palabras2);
        
        // Comparar las palabras ordenadas
        return $palabras1 === $palabras2;
    }

    /**
     * Calcular prioridad de un vehículo según reglas de negocio
     * @param array $row Fila de datos del vehículo
     * @return int Nivel de prioridad (0-3)
     */
    private function calcularPrioridad($row) {
        $prioridad = 0;
        
        // Paso 1: Calcular DVENCI (días vencidos)
        $stoDias = (int)($row['STO_DIAS'] ?? 0);
        $dvenci = 0;
        
        if ($stoDias > 0 && isset($row['STO_FFACT']) && $row['STO_FFACT'] instanceof DateTime) {
            $hoy = new DateTime();
            $diasTranscurridos = $hoy->diff($row['STO_FFACT'])->days;
            $dvenci = $diasTranscurridos - $stoDias;
        }
        
        // Si STO_DIAS <= 0, DVENCI se mantiene en 0
        if ($stoDias <= 0) {
            $dvenci = 0;
        }
        
        // Paso 2: Sumar por cancelación
        if (isset($row['STO_CANCELADA']) && $row['STO_CANCELADA'] == 1) {
            $prioridad += 1;
        }
        
        // Paso 3: Sumar por vencimiento
        if ($dvenci >= 1) {
            $prioridad += 1;
        }
        
        // Paso 4: Sobrescribir si hay comprobante (SIN STOCK)
        // Nota: Como filtramos STO_COMPRO = '', esto nunca será 3
        $stoCompro = trim($row['STO_COMPRO'] ?? '');
        if (!empty($stoCompro)) {
            $prioridad = 3;
        }
        
        return $prioridad;
    }
    
    /**
     * Obtener etiqueta de prioridad
     * @param int $prioridad Nivel de prioridad (0-3)
     * @return string Etiqueta descriptiva
     */
    private function getPrioridadLabel($prioridad) {
        switch ($prioridad) {
            case 0: return 'NORMAL';
            case 1: return 'ALTA';
            case 2: return 'MUY ALTA';
            case 3: return 'SIN STOCK';
            default: return 'NORMAL';
        }
    }

    /**
     * Obtener vehículos asignados a un asesor específico por nombre
     * Retorna 3 categorías: asignados al asesor, libres, y asignados a otros
     * Excluye vehículos con ubicación "ENTREGADO"
     * @param string $nombreAsesor Nombre del asesor en formato "Nombre Apellido"
     * @return array Array con 3 categorías de vehículos
     */
    public function obtenerVehiculosPorNombreAsesor($nombreAsesor) {
        $db = new Database();
        $stockConn = $db->getStockConnection();
        
        if (!$stockConn) {
            return [
                'asignados' => [],
                'libres' => [],
                'otros' => []
            ];
        }
        
        if (empty($nombreAsesor)) {
            sqlsrv_close($stockConn);
            return [
                'asignados' => [],
                'libres' => [],
                'otros' => []
            ];
        }
        
        // Normalizar nombre (quitar comas y convertir a mayúsculas)
        $nombreNormalizado = trim(strtoupper(str_replace(',', '', $nombreAsesor)));
        
        // Obtener marcas del asesor desde la sesión
        $marcasAsesor = $_SESSION['usuario_marcas'] ?? '';
        $esMarcaMultiple = (strtoupper(trim($marcasAsesor)) === 'MULTIMARCAS');
        
        // Convertir marcas en array (separadas por comas)
        $marcasPermitidas = [];
        if (!$esMarcaMultiple && !empty($marcasAsesor)) {
            $marcasPermitidas = array_map('trim', array_map('strtoupper', explode(',', $marcasAsesor)));
        }
        
        error_log("🔍 DEBUG obtenerVehiculosPorNombreAsesor - Nombre recibido: '{$nombreAsesor}'");
        error_log("🔍 DEBUG obtenerVehiculosPorNombreAsesor - Nombre normalizado: '{$nombreNormalizado}'");
        error_log("🔍 DEBUG obtenerVehiculosPorNombreAsesor - Marcas asesor: '{$marcasAsesor}'");
        error_log("🔍 DEBUG obtenerVehiculosPorNombreAsesor - Es multimarca: " . ($esMarcaMultiple ? 'SÍ' : 'NO'));
        if (!$esMarcaMultiple && !empty($marcasPermitidas)) {
            error_log("🔍 DEBUG obtenerVehiculosPorNombreAsesor - Marcas permitidas: " . implode(', ', $marcasPermitidas));
        }
        
        // Consulta SQL BASE - SIEMPRE la misma para todos
        // Filtrar por: STO_EMPRESA = 'INTER' AND STO_COMPRO = '' AND STO_UBICACION != 'ENTREGADO'
        $sql = "SELECT 
                    STO_CHASIS,
                    STO_MARCA,
                    STO_MODELO,
                    STO_VERSION,
                    STO_COLOR,
                    STO_UBICACION,
                    STO_VENDEDOR,
                    STO_FFACT,
                    STO_DIAS,
                    STO_CANCELADA,
                    STO_COMPRO
                FROM STOCK
                WHERE 
                    UPPER(LTRIM(RTRIM(STO_EMPRESA))) = 'INTER'
                    AND (STO_COMPRO IS NULL OR STO_COMPRO = '')
                    AND (STO_UBICACION IS NULL OR UPPER(LTRIM(RTRIM(STO_UBICACION))) != 'ENTREGADO')
                ORDER BY STO_FFACT ASC";
        
        $result = sqlsrv_query($stockConn, $sql);
        
        if (!$result) {
            error_log("❌ ERROR en query SQL: " . print_r(sqlsrv_errors(), true));
            sqlsrv_close($stockConn);
            return [
                'asignados' => [],
                'libres' => [],
                'otros' => [],
                'debug' => [['error' => 'Error en query SQL']]
            ];
        }
        
        $vehiculosAsignados = [];
        $vehiculosLibres = [];
        $vehiculosOtros = [];
        $debugLogs = [];
        $totalRegistros = 0;
        
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $totalRegistros++;
            // Formatear fecha
            $fecha = '';
            if ($row['STO_FFACT'] instanceof DateTime) {
                $fecha = $row['STO_FFACT']->format('d/m/Y');
            }
            
            // Calcular prioridad
            $prioridad = $this->calcularPrioridad($row);
            $prioridadLabel = $this->getPrioridadLabel($prioridad);
            
            // Determinar si es dinero propio (STO_CANCELADA == 1)
            $esDineroPropio = (isset($row['STO_CANCELADA']) && $row['STO_CANCELADA'] == 1);
            
            $vehiculo = [
                'chasis' => $row['STO_CHASIS'] ?? '',
                'marca' => $row['STO_MARCA'] ?? '',
                'modelo' => $row['STO_MODELO'] ?? '',
                'version' => $row['STO_VERSION'] ?? '',
                'color' => $row['STO_COLOR'] ?? '',
                'ubicacion' => $row['STO_UBICACION'] ?? '',
                'vendedor' => $row['STO_VENDEDOR'] ?? '',
                'fecha' => $fecha,
                'prioridad' => $prioridad,
                'prioridadLabel' => $prioridadLabel,
                'dineroPropio' => $esDineroPropio
            ];
            
            $vendedor = trim($row['STO_VENDEDOR'] ?? '');
            $marcaVehiculo = strtoupper(trim($vehiculo['marca']));
            
            // Categoría 1: Vehículos SIN vendedor asignado (LIBRES)
            if (empty($vendedor)) {
                // Filtrar por marca del asesor
                $puedeVerVehiculo = false;
                
                if ($esMarcaMultiple) {
                    // Si es MULTIMARCA, puede ver todos los vehículos libres
                    $puedeVerVehiculo = true;
                } elseif (!empty($marcasPermitidas)) {
                    // Si tiene marcas específicas, verificar si la marca del vehículo está en su lista
                    $puedeVerVehiculo = in_array($marcaVehiculo, $marcasPermitidas);
                } else {
                    // Si no tiene marcas definidas, no puede ver vehículos libres
                    $puedeVerVehiculo = false;
                }
                
                if ($puedeVerVehiculo) {
                    $vehiculosLibres[] = $vehiculo;
                } else {
                    error_log("🚫 Vehículo libre filtrado por marca - Marca: {$marcaVehiculo} | Chasis: {$vehiculo['chasis']}");
                }
            } else {
                // Normalizar nombre del vendedor (quitar comas)
                $vendedorOriginal = $vendedor;
                $vendedorNormalizado = trim(strtoupper(str_replace(',', '', $vendedor)));
                
                // Log especial para SAMAI
                $esSamai = (stripos($vendedorNormalizado, 'SAMAI') !== false || stripos($vendedorNormalizado, 'ROJAS') !== false);
                
                if ($esSamai) {
                    error_log("🔍 DEBUG Comparación (SAMAI/ROJAS encontrado):");
                    error_log("  - Asesor original: '{$nombreAsesor}'");
                    error_log("  - Asesor normalizado: '{$nombreNormalizado}'");
                    error_log("  - Vendedor original: '{$vendedorOriginal}'");
                    error_log("  - Vendedor normalizado: '{$vendedorNormalizado}'");
                }
                
                // Categoría 2: Vehículos asignados AL ASESOR
                // Comparar palabra por palabra (cada palabra del asesor debe estar en el vendedor)
                $palabrasAsesor = array_filter(explode(' ', $nombreNormalizado));
                $coincide = true;
                
                foreach ($palabrasAsesor as $palabra) {
                    if (stripos($vendedorNormalizado, $palabra) === false) {
                        $coincide = false;
                        break;
                    }
                }
                
                $coincide1 = $coincide; // Para mantener compatibilidad con logs
                $coincide2 = false; // Ya no usamos comparación inversa
                
                if ($esSamai) {
                    error_log("  - Asesor en vendedor: " . ($coincide1 ? 'SÍ' : 'NO'));
                    error_log("  - Vendedor en asesor: " . ($coincide2 ? 'SÍ' : 'NO'));
                    error_log("  - Resultado final: " . ($coincide ? 'COINCIDE' : 'NO COINCIDE'));
                }
                
                // Agregar log para debug (solo SAMAI para no saturar)
                if ($esSamai) {
                    $debugLogs[] = [
                        'asesor_original' => $nombreAsesor,
                        'asesor_normalizado' => $nombreNormalizado,
                        'vendedor_original' => $vendedorOriginal,
                        'vendedor_normalizado' => $vendedorNormalizado,
                        'asesor_en_vendedor' => $coincide1,
                        'vendedor_en_asesor' => $coincide2,
                        'resultado' => $coincide,
                        'chasis' => $vehiculo['chasis']
                    ];
                }
                
                if ($coincide) {
                    error_log("✅ COINCIDE - Asesor: '{$nombreNormalizado}' == Vendedor: '{$vendedorNormalizado}' | Chasis: {$vehiculo['chasis']}");
                    $vehiculosAsignados[] = $vehiculo;
                } else {
                    error_log("❌ NO COINCIDE - Asesor: '{$nombreNormalizado}' != Vendedor: '{$vendedorNormalizado}' | Chasis: {$vehiculo['chasis']}");
                    
                    // Categoría 3: Vehículos asignados a OTROS asesores
                    // Filtrar por marca del asesor (igual que en vehículos libres)
                    $puedeVerVehiculoOtro = false;
                    
                    if ($esMarcaMultiple) {
                        // Si es MULTIMARCA, puede ver todos los vehículos de otros asesores
                        $puedeVerVehiculoOtro = true;
                    } elseif (!empty($marcasPermitidas)) {
                        // Si tiene marcas específicas, verificar si la marca del vehículo está en su lista
                        $puedeVerVehiculoOtro = in_array($marcaVehiculo, $marcasPermitidas);
                    } else {
                        // Si no tiene marcas definidas, no puede ver vehículos de otros
                        $puedeVerVehiculoOtro = false;
                    }
                    
                    if ($puedeVerVehiculoOtro) {
                        $vehiculosOtros[] = $vehiculo;
                    } else {
                        error_log("🚫 Vehículo de otro asesor filtrado por marca - Marca: {$marcaVehiculo} | Chasis: {$vehiculo['chasis']}");
                    }
                }
            }
        }
        
        sqlsrv_close($stockConn);
        
        error_log("📊 Total registros procesados: {$totalRegistros}");
        error_log("📊 Asignados: " . count($vehiculosAsignados));
        error_log("📊 Libres: " . count($vehiculosLibres));
        error_log("📊 Otros: " . count($vehiculosOtros));
        
        // Agregar info general al debug
        array_unshift($debugLogs, [
            'info' => 'Resumen',
            'total_registros' => $totalRegistros,
            'asignados_count' => count($vehiculosAsignados),
            'libres_count' => count($vehiculosLibres),
            'otros_count' => count($vehiculosOtros),
            'asesor_buscado' => $nombreAsesor
        ]);
        
        // Ordenar por:
        // 1. Prioridad (mayor a menor = más urgente primero)
        // 2. Fecha (más antiguo primero = dentro de cada prioridad)
        $ordenarVehiculos = function($a, $b) {
            // Primero comparar por prioridad (descendente)
            if ($b['prioridad'] != $a['prioridad']) {
                return $b['prioridad'] - $a['prioridad'];
            }
            
            // Si tienen la misma prioridad, ordenar por fecha (ascendente = más antiguo primero)
            // Convertir fechas DD/MM/YYYY a timestamp para comparar
            $fechaA = DateTime::createFromFormat('d/m/Y', $a['fecha']);
            $fechaB = DateTime::createFromFormat('d/m/Y', $b['fecha']);
            
            if ($fechaA && $fechaB) {
                return $fechaA->getTimestamp() - $fechaB->getTimestamp();
            }
            
            return 0;
        };
        
        usort($vehiculosAsignados, $ordenarVehiculos);
        usort($vehiculosLibres, $ordenarVehiculos);
        usort($vehiculosOtros, $ordenarVehiculos);
        
        return [
            'asignados' => $vehiculosAsignados,
            'libres' => $vehiculosLibres,
            'otros' => $vehiculosOtros,
            'debug' => $debugLogs
        ];
    }

    /**
     * Obtener datos de Agencia, Cajera y Centro de Costo del asesor logueado
     * Busca en la tabla firmas el centro de costo del usuario
     * Luego consulta el sheet para obtener los datos completos
     */
    public function obtenerDatosAsesorParaOrden() {
        try {
            // 1. Obtener email del usuario logueado
            $emailUsuario = $_SESSION['usuario_email'] ?? '';
            
            if (empty($emailUsuario)) {
                return [
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ];
            }

            // 2. Consultar tabla firmas para obtener el centro de costo (tienda)
            $db = new Database();
            $docDigitalesConn = $db->getDocDigitalesConnection();
            
            if (!$docDigitalesConn) {
                return [
                    'success' => false,
                    'error' => 'Error de conexión a base de datos'
                ];
            }

            $sqlFirmas = "SELECT tienda FROM firmas WHERE firma_mail = ?";
            $resultFirmas = sqlsrv_query($docDigitalesConn, $sqlFirmas, [$emailUsuario]);
            
            if (!$resultFirmas) {
                sqlsrv_close($docDigitalesConn);
                return [
                    'success' => false,
                    'error' => 'Error al consultar datos del usuario'
                ];
            }

            $rowFirmas = sqlsrv_fetch_array($resultFirmas, SQLSRV_FETCH_ASSOC);
            sqlsrv_close($docDigitalesConn);

            if (!$rowFirmas || empty($rowFirmas['tienda'])) {
                return [
                    'success' => false,
                    'error' => 'No se encontró centro de costo para el usuario'
                ];
            }

            $centroCosto = trim($rowFirmas['tienda']);

            // 3. Consultar el sheet para obtener Agencia, Cajera y Email
            $apiUrl = 'https://opensheet.elk.sh/155IT8et2XYhMK6bkr7OJBtCzIHS6X9Ia_6O99Gm0WAk/Hoja%201';
            $apiData = @file_get_contents($apiUrl);
            
            if (!$apiData) {
                return [
                    'success' => false,
                    'error' => 'Error al consultar datos del sheet'
                ];
            }

            $apiArray = json_decode($apiData, true);
            
            if (!$apiArray || !is_array($apiArray)) {
                return [
                    'success' => false,
                    'error' => 'Error al procesar datos del sheet'
                ];
            }

            // 4. Buscar en el sheet donde CENTRO DE COSTO coincida
            error_log("🔍 Buscando centro de costo: '" . $centroCosto . "' (length: " . strlen($centroCosto) . ") en el sheet");
            
            foreach ($apiArray as $item) {
                $centroCostoSheet = trim($item['CENTRO DE COSTO'] ?? '');
                
                // Normalizar ambos valores para comparación (quitar espacios, convertir a string)
                $centroCostoNormalizado = trim(strval($centroCosto));
                $centroCostoSheetNormalizado = trim(strval($centroCostoSheet));
                
                // Log para debug (solo primeros 10 para no saturar)
                static $logCount = 0;
                if ($logCount < 10 && !empty($centroCostoSheet)) {
                    error_log("  Comparando: '" . $centroCostoSheetNormalizado . "' (len:" . strlen($centroCostoSheetNormalizado) . ") === '" . $centroCostoNormalizado . "' (len:" . strlen($centroCostoNormalizado) . ") → " . ($centroCostoSheetNormalizado === $centroCostoNormalizado ? 'MATCH' : 'NO MATCH'));
                    $logCount++;
                }
                
                if ($centroCostoSheetNormalizado === $centroCostoNormalizado) {
                    $agencia = trim($item['AGENCIA'] ?? '');
                    $cajera = trim($item['NOMBRE'] ?? '');
                    $emailCajera = trim($item['EMAIL'] ?? '');
                    
                    error_log("✅ ENCONTRADO! Agencia: " . $agencia . ", Cajera: " . $cajera . ", Email: " . $emailCajera);
                    
                    return [
                        'success' => true,
                        'agencia' => $agencia,
                        'cajera' => $cajera,
                        'centro_costo' => $centroCosto,
                        'email_cajera' => $emailCajera  // ✅ Agregar email para envío de correos
                    ];
                }
            }

            error_log("❌ NO se encontró el centro de costo '" . $centroCosto . "' en el sheet");
            
            // Si no se encuentra en el sheet, retornar solo el centro de costo
            return [
                'success' => true,
                'agencia' => '',
                'cajera' => '',
                'centro_costo' => $centroCosto,
                'warning' => 'Centro de costo encontrado pero sin datos en el sheet'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function actualizarFirmaClienteEnOrden($ordenId, $firmaUrl)
    {
        try {
            $sql = "UPDATE SIST_ORDEN_COMPRA SET OC_CLIENTE_FIRMA = ? WHERE OC_ID = ?";
            $params = [$firmaUrl, $ordenId];

            $result = sqlsrv_query($this->conn, $sql, $params);

            if (!$result) {
                error_log("❌ Error al actualizar firma del cliente en orden: " . print_r(sqlsrv_errors(), true));
                return false;
            }

            return sqlsrv_rows_affected($result) > 0;
        } catch (Exception $e) {
            error_log("❌ Excepción en actualizarFirmaClienteEnOrden: " . $e->getMessage());
            return false;
        }
    }

    public function sincronizarFirmaClienteEnDocumentos($ordenId, $firmaUrl)
    {
        try {
            error_log("🔄 [Document] Sincronizando firma del cliente en documentos para orden ID: $ordenId");

            $tablasFirma = [
                'SIST_CARTA_CONOCIMIENTO_ACEPTACION' => 'CCA_FIRMA_CLIENTE',
                'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD' => 'ACC_FIRMA_CLIENTE',
                'SIST_CARTA_RECEPCION' => 'CR_FIRMA_CLIENTE',
                'SIST_AUTORIZACION_DATOS_PERSONALES' => 'ADP_FIRMA_CLIENTE',
                'SIST_CARTA_CARACTERISTICAS' => 'CC_FIRMA_CLIENTE',
                'SIST_POLITICA_PROTECCION_DATOS' => 'PPD_FIRMA_CLIENTE',
            ];

            $actualizaciones = 0;

            foreach ($tablasFirma as $tabla => $campoFirma) {
                $sql = "UPDATE $tabla SET $campoFirma = ? WHERE ";

                switch ($tabla) {
                    case 'SIST_CARTA_CONOCIMIENTO_ACEPTACION':
                        $sql .= "CCA_ORDEN_ID = ?";
                        break;
                    case 'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD':
                        $sql .= "ACC_ORDEN_ID = ?";
                        break;
                    case 'SIST_CARTA_RECEPCION':
                        $sql .= "CR_ORDEN_ID = ?";
                        break;
                    case 'SIST_AUTORIZACION_DATOS_PERSONALES':
                        $sql .= "ADP_ORDEN_ID = ?";
                        break;
                    case 'SIST_CARTA_CARACTERISTICAS':
                        $sql .= "CC_ORDEN_ID = ?";
                        break;
                    case 'SIST_POLITICA_PROTECCION_DATOS':
                        $sql .= "PPD_ORDEN_ID = ?";
                        break;
                }

                $params = [$firmaUrl, $ordenId];
                $result = sqlsrv_query($this->conn, $sql, $params);

                if ($result) {
                    $filasAfectadas = sqlsrv_rows_affected($result);
                    if ($filasAfectadas > 0) {
                        $actualizaciones++;
                        error_log("✅ [Document] Firma sincronizada en $tabla ($filasAfectadas filas)");
                    }
                } else {
                    error_log("❌ [Document] Error al sincronizar firma en $tabla: " . print_r(sqlsrv_errors(), true));
                }
            }

            error_log("🎯 [Document] Sincronización completada: $actualizaciones tablas actualizadas");
            return $actualizaciones > 0;
        } catch (Exception $e) {
            error_log("❌ Excepción en sincronizarFirmaClienteEnDocumentos: " . $e->getMessage());
            return false;
        }
    }
}
