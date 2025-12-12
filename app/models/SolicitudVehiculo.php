<?php
require_once __DIR__ . '/../../config/database.php';

class SolicitudVehiculo {
    private $conn;
    private $connDocDigitales;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->connDocDigitales = $database->getDocDigitalesConnection();
    }

    /**
     * Crear una nueva solicitud de vehículo
     */
    public function crear($datos) {
        try {
            // Generar token único
            $token = bin2hex(random_bytes(32));
            
            $sql = "INSERT INTO SIST_SOLICITUDES_VEHICULOS 
                    (SOL_CHASIS, SOL_MARCA, SOL_UBICACION, SOL_TIPO,
                     SOL_ASESOR_SOLICITANTE_NOMBRE, SOL_ASESOR_SOLICITANTE_EMAIL,
                     SOL_ASESOR_DUENO_NOMBRE, SOL_ASESOR_DUENO_EMAIL,
                     SOL_TOKEN, SOL_ESTADO) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDIENTE')";
            
            $params = [
                $datos['chasis'],
                $datos['marca'],
                $datos['ubicacion'],
                $datos['tipo'], // 'LIBRE' o 'REASIGNACION'
                $datos['solicitante_nombre'],
                $datos['solicitante_email'],
                $datos['dueno_nombre'] ?? null,
                $datos['dueno_email'] ?? null,
                $token
            ];
            
            $result = sqlsrv_query($this->conn, $sql, $params);
            
            if (!$result) {
                error_log("Error al crear solicitud: " . print_r(sqlsrv_errors(), true));
                return false;
            }
            
            return $token;
        } catch (Exception $e) {
            error_log("Excepción al crear solicitud: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener solicitud por token
     */
    public function obtenerPorToken($token) {
        try {
            $sql = "SELECT * FROM SIST_SOLICITUDES_VEHICULOS 
                    WHERE SOL_TOKEN = ?";
            
            $result = sqlsrv_query($this->conn, $sql, [$token]);
            
            if (!$result) {
                return null;
            }
            
            return sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener solicitud: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar estado de solicitud
     */
    public function actualizarEstado($token, $estado, $ipRespuesta = null, $observaciones = null) {
        try {
            if (!in_array($estado, ['ACEPTADO', 'RECHAZADO', 'CANCELADO'])) {
                return false;
            }
            
            $sql = "UPDATE SIST_SOLICITUDES_VEHICULOS 
                    SET SOL_ESTADO = ?, 
                        SOL_FECHA_RESPUESTA = GETDATE(),
                        SOL_IP_RESPUESTA = ?,
                        SOL_OBSERVACIONES = ?,
                        SOL_UPDATED_AT = GETDATE()
                    WHERE SOL_TOKEN = ?";
            
            $params = [$estado, $ipRespuesta, $observaciones, $token];
            $result = sqlsrv_query($this->conn, $sql, $params);
            
            if (!$result) {
                error_log("Error al actualizar estado: " . print_r(sqlsrv_errors(), true));
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Excepción al actualizar estado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar correo del asesor en la tabla firmas
     * Compara nombre normalizado (sin comas, ordenado)
     */
    public function buscarCorreoAsesor($nombreAsesor) {
        if (!$this->connDocDigitales) {
            error_log("No hay conexión a DOC_DIGITALES");
            return null;
        }

        try {
            // Normalizar nombre del asesor (quitar comas, convertir a mayúsculas)
            $nombreNormalizado = trim(strtoupper(str_replace(',', '', $nombreAsesor)));
            
            error_log("🔍 Buscando asesor en tabla firmas:");
            error_log("Nombre original: " . $nombreAsesor);
            error_log("Nombre normalizado: " . $nombreNormalizado);
            
            // Obtener todos los asesores de la tabla firmas
            $sql = "SELECT firma_nombre, firma_apellido, firma_mail 
                    FROM firmas 
                    WHERE firma_mail IS NOT NULL 
                    AND firma_mail != ''
                    AND rol = 'USER'";
            
            $result = sqlsrv_query($this->connDocDigitales, $sql);
            
            if (!$result) {
                error_log("Error al consultar firmas: " . print_r(sqlsrv_errors(), true));
                return null;
            }
            
            $totalRegistros = 0;
            
            // Buscar coincidencia
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $totalRegistros++;
                
                // Concatenar nombre + apellido de la BD
                $nombreCompleto = trim($row['firma_nombre'] . ' ' . $row['firma_apellido']);
                $nombreCompletoNormalizado = trim(strtoupper(str_replace(',', '', $nombreCompleto)));
                
                // Comparar usando la misma lógica de comparación
                if ($this->compararNombresNormalizados($nombreNormalizado, $nombreCompletoNormalizado)) {
                    error_log("✅ Asesor encontrado!");
                    error_log("Nombre completo: " . $nombreCompleto);
                    error_log("Email: " . $row['firma_mail']);
                    
                    return [
                        'nombre' => $nombreCompleto,
                        'email' => $row['firma_mail']
                    ];
                }
            }
            
            error_log("❌ No se encontró coincidencia");
            error_log("Total registros revisados: " . $totalRegistros);
            
            return null;
        } catch (Exception $e) {
            error_log("Excepción al buscar correo asesor: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Comparar nombres normalizados (sin comas)
     * Ordena las palabras alfabéticamente para comparar
     */
    private function compararNombresNormalizados($nombre1, $nombre2) {
        if ($nombre1 === $nombre2) {
            return true;
        }
        
        $palabras1 = preg_split('/\s+/', $nombre1);
        $palabras2 = preg_split('/\s+/', $nombre2);
        
        $palabras1 = array_filter($palabras1);
        $palabras2 = array_filter($palabras2);
        
        sort($palabras1);
        sort($palabras2);
        
        return $palabras1 === $palabras2;
    }

    /**
     * Enviar correo de solicitud de reasignación al asesor dueño
     */
    public function enviarCorreoSolicitud($solicitud) {
        try {
            $token = $solicitud['SOL_TOKEN'];
            $chasis = $solicitud['SOL_CHASIS'];
            $marca = $solicitud['SOL_MARCA'];
            $ubicacion = $solicitud['SOL_UBICACION'];
            $solicitante = $solicitud['SOL_ASESOR_SOLICITANTE_NOMBRE'];
            $dueno = $solicitud['SOL_ASESOR_DUENO_NOMBRE'];
            $emailDueno = $solicitud['SOL_ASESOR_DUENO_EMAIL'];
            
            // URLs para aceptar/rechazar
            $urlAceptar = "http://190.238.78.104:3800/digitalizacion-documentos/solicitud-vehiculo/aceptar?token=" . $token;
            $urlRechazar = "http://190.238.78.104:3800/digitalizacion-documentos/solicitud-vehiculo/rechazar?token=" . $token;
            
            // Construir HTML del correo
            $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #1e3a8a;'>🚗 Solicitud de Reasignación de Vehículo</h2>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <p>Hola <strong>{$dueno}</strong>,</p>
                    <p>El asesor <strong>{$solicitante}</strong> desea asignarse el siguiente vehículo que actualmente está bajo tu nombre:</p>
                </div>
                
                <div style='background: #e0f2fe; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #0284c7;'>
                    <p style='margin: 5px 0;'><strong>📋 Chasis:</strong> {$chasis}</p>
                    <p style='margin: 5px 0;'><strong>🚙 Marca:</strong> {$marca}</p>
                    <p style='margin: 5px 0;'><strong>📍 Ubicación:</strong> {$ubicacion}</p>
                </div>
                
                <div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;'>
                    <p style='margin: 0; color: #856404;'>
                        <strong>⚠️ Importante:</strong> Por favor, revisa la solicitud y decide si aceptas ceder este vehículo.
                    </p>
                </div>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$urlAceptar}' 
                       style='background-color: #10b981; color: white; padding: 15px 30px; 
                              text-decoration: none; border-radius: 8px; display: inline-block;
                              font-weight: bold; font-size: 16px; margin: 5px;'>
                        ✅ ACEPTAR
                    </a>
                    <a href='{$urlRechazar}' 
                       style='background-color: #ef4444; color: white; padding: 15px 30px; 
                              text-decoration: none; border-radius: 8px; display: inline-block;
                              font-weight: bold; font-size: 16px; margin: 5px;'>
                        ❌ RECHAZAR
                    </a>
                </div>
                
                <p style='color: #666; font-size: 12px; text-align: center;'>
                    Este correo fue generado automáticamente. Por favor no responder.
                </p>
            </div>
            ";
            
            // Preparar datos para la API de correo
            $emailData = [
                'to' => $emailDueno,
                'subject' => "🚗 Solicitud de Reasignación de Vehículo - {$chasis}",
                'html' => $htmlBody,
                'from' => 'comunica@interamericana.shop',
                'from_name' => 'Sistema de Digitalización Interamericana'
            ];
            
            error_log("=== ENVIANDO CORREO DE SOLICITUD ===");
            error_log("TO: " . $emailDueno);
            error_log("Chasis: " . $chasis);
            
            // Enviar correo usando cURL
            $ch = curl_init('http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                error_log("✅ Correo de solicitud enviado exitosamente");
                return true;
            } else {
                error_log("❌ Error al enviar correo. HTTP Code: " . $httpCode);
                return false;
            }
        } catch (Exception $e) {
            error_log("Excepción al enviar correo de solicitud: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar correo de confirmación de asignación de vehículo libre
     */
    public function enviarCorreoAsignacionLibre($solicitud) {
        try {
            $chasis = $solicitud['SOL_CHASIS'];
            $marca = $solicitud['SOL_MARCA'];
            $ubicacion = $solicitud['SOL_UBICACION'];
            $solicitante = $solicitud['SOL_ASESOR_SOLICITANTE_NOMBRE'];
            $emailSolicitante = $solicitud['SOL_ASESOR_SOLICITANTE_EMAIL'];
            
            $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #1e3a8a;'>✅ Vehículo Asignado Correctamente</h2>
                
                <div style='background: #d1fae5; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981;'>
                    <p>Hola <strong>{$solicitante}</strong>,</p>
                    <p>El siguiente vehículo ha sido <strong>asignado correctamente</strong> a tu nombre:</p>
                </div>
                
                <div style='background: #e0f2fe; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #0284c7;'>
                    <p style='margin: 5px 0;'><strong>📋 Chasis:</strong> {$chasis}</p>
                    <p style='margin: 5px 0;'><strong>🚙 Marca:</strong> {$marca}</p>
                    <p style='margin: 5px 0;'><strong>📍 Ubicación:</strong> {$ubicacion}</p>
                </div>
                
                <div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;'>
                    <p style='margin: 0; color: #856404;'>
                        <strong>ℹ️ Nota:</strong> Este vehículo ahora está bajo tu responsabilidad.
                    </p>
                </div>
                
                <p style='color: #666; font-size: 12px; text-align: center;'>
                    Este correo fue generado automáticamente. Por favor no responder.
                </p>
            </div>
            ";
            
            $emailData = [
                'to' => $emailSolicitante,
                'subject' => "✅ Vehículo Asignado - {$marca} {$chasis}",
                'html' => $htmlBody,
                'from' => 'comunica@interamericana.shop',
                'from_name' => 'Sistema de Digitalización Interamericana'
            ];
            
            error_log("=== ENVIANDO CORREO DE ASIGNACIÓN LIBRE ===");
            error_log("TO: " . $emailSolicitante);
            error_log("Chasis: " . $chasis);
            
            $ch = curl_init('http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                error_log("✅ Correo de asignación enviado exitosamente");
                return true;
            } else {
                error_log("❌ Error al enviar correo. HTTP Code: " . $httpCode);
                return false;
            }
        } catch (Exception $e) {
            error_log("Excepción al enviar correo de asignación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar correo de notificación al solicitante (para reasignaciones)
     */
    public function enviarCorreoNotificacion($solicitud, $aceptado) {
        try {
            $chasis = $solicitud['SOL_CHASIS'];
            $marca = $solicitud['SOL_MARCA'];
            $solicitante = $solicitud['SOL_ASESOR_SOLICITANTE_NOMBRE'];
            $emailSolicitante = $solicitud['SOL_ASESOR_SOLICITANTE_EMAIL'];
            $dueno = $solicitud['SOL_ASESOR_DUENO_NOMBRE'];
            
            if ($aceptado) {
                $titulo = "✅ Solicitud Aceptada";
                $mensaje = "Tu solicitud para asignarte el vehículo ha sido <strong>ACEPTADA</strong> por {$dueno}.";
                $color = "#10b981";
                $bgColor = "#d1fae5";
            } else {
                $titulo = "❌ Solicitud Rechazada";
                $mensaje = "Tu solicitud para asignarte el vehículo ha sido <strong>RECHAZADA</strong> por {$dueno}.";
                $color = "#ef4444";
                $bgColor = "#fee2e2";
            }
            
            $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #1e3a8a;'>{$titulo}</h2>
                
                <div style='background: {$bgColor}; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid {$color};'>
                    <p>Hola <strong>{$solicitante}</strong>,</p>
                    <p>{$mensaje}</p>
                </div>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <p style='margin: 5px 0;'><strong>📋 Chasis:</strong> {$chasis}</p>
                    <p style='margin: 5px 0;'><strong>🚙 Marca:</strong> {$marca}</p>
                </div>
                
                <p style='color: #666; font-size: 12px; text-align: center;'>
                    Este correo fue generado automáticamente. Por favor no responder.
                </p>
            </div>
            ";
            
            $emailData = [
                'to' => $emailSolicitante,
                'subject' => $titulo . " - Vehículo {$chasis}",
                'html' => $htmlBody,
                'from' => 'comunica@interamericana.shop',
                'from_name' => 'Sistema de Digitalización Interamericana'
            ];
            
            $ch = curl_init('http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            return ($httpCode >= 200 && $httpCode < 300);
        } catch (Exception $e) {
            error_log("Excepción al enviar correo de notificación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar vendedor en tabla STOCK
     */
    public function actualizarVendedorStock($chasis, $nombreVendedor) {
        try {
            $db = new Database();
            $stockConn = $db->getStockConnection();
            
            if (!$stockConn) {
                error_log("❌ No hay conexión a STOCK");
                return false;
            }
            
            error_log("📝 Actualizando STOCK:");
            error_log("Chasis: " . $chasis);
            error_log("Nuevo vendedor: " . $nombreVendedor);
            
            $sql = "UPDATE STOCK 
                    SET STO_VENDEDOR = ? 
                    WHERE STO_CHASIS = ?";
            
            $params = [$nombreVendedor, $chasis];
            $result = sqlsrv_query($stockConn, $sql, $params);
            
            if (!$result) {
                error_log("❌ Error al actualizar STOCK: " . print_r(sqlsrv_errors(), true));
                sqlsrv_close($stockConn);
                return false;
            }
            
            $rowsAffected = sqlsrv_rows_affected($result);
            error_log("✅ STOCK actualizado. Filas afectadas: " . $rowsAffected);
            
            sqlsrv_close($stockConn);
            return $rowsAffected > 0;
        } catch (Exception $e) {
            error_log("❌ Excepción al actualizar STOCK: " . $e->getMessage());
            return false;
        }
    }
}
