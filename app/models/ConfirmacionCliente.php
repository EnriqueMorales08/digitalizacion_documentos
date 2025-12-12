<?php
require_once __DIR__ . '/../../config/database.php';

class ConfirmacionCliente {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Crear una nueva confirmación de cliente
     */
    public function crear($numeroExpediente, $emailCliente) {
        try {
            // Generar token único
            $token = bin2hex(random_bytes(32)); // 64 caracteres
            
            $sql = "INSERT INTO SIST_CONFIRMACIONES_CLIENTE 
                    (CONF_NUMERO_EXPEDIENTE, CONF_EMAIL_CLIENTE, CONF_TOKEN_CONFIRMACION, 
                     CONF_ESTADO, CONF_FECHA_ENVIO) 
                    VALUES (?, ?, ?, 'PENDIENTE', GETDATE())";
            
            $params = [$numeroExpediente, $emailCliente, $token];
            $result = sqlsrv_query($this->conn, $sql, $params);
            
            if (!$result) {
                error_log("Error al crear confirmación: " . print_r(sqlsrv_errors(), true));
                return false;
            }
            
            return $token;
        } catch (Exception $e) {
            error_log("Excepción al crear confirmación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener confirmación por token
     */
    public function obtenerPorToken($token) {
        try {
            $sql = "SELECT * FROM SIST_CONFIRMACIONES_CLIENTE 
                    WHERE CONF_TOKEN_CONFIRMACION = ?";
            
            $result = sqlsrv_query($this->conn, $sql, [$token]);
            
            if (!$result) {
                return null;
            }
            
            return sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener confirmación: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener confirmación por número de expediente
     */
    public function obtenerPorExpediente($numeroExpediente) {
        try {
            $sql = "SELECT * FROM SIST_CONFIRMACIONES_CLIENTE 
                    WHERE CONF_NUMERO_EXPEDIENTE = ? 
                    ORDER BY CONF_FECHA_ENVIO DESC";
            
            $result = sqlsrv_query($this->conn, $sql, [$numeroExpediente]);
            
            if (!$result) {
                return null;
            }
            
            return sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener confirmación por expediente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar estado de confirmación (ACEPTADO o RECHAZADO)
     */
    public function actualizarEstado($token, $estado, $ipCliente = null, $observaciones = null) {
        try {
            // Validar estado
            if (!in_array($estado, ['ACEPTADO', 'RECHAZADO'])) {
                return false;
            }
            
            $sql = "UPDATE SIST_CONFIRMACIONES_CLIENTE 
                    SET CONF_ESTADO = ?, 
                        CONF_FECHA_RESPUESTA = GETDATE(),
                        CONF_IP_CLIENTE = ?,
                        CONF_OBSERVACIONES = ?,
                        CONF_UPDATED_AT = GETDATE()
                    WHERE CONF_TOKEN_CONFIRMACION = ?";
            
            $params = [$estado, $ipCliente, $observaciones, $token];
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
     * Marcar como enviado a cajera
     */
    public function marcarEnviadoCajera($numeroExpediente) {
        try {
            $sql = "UPDATE SIST_CONFIRMACIONES_CLIENTE 
                    SET CONF_ENVIADO_CAJERA = 1,
                        CONF_FECHA_ENVIO_CAJERA = GETDATE(),
                        CONF_UPDATED_AT = GETDATE()
                    WHERE CONF_NUMERO_EXPEDIENTE = ?";
            
            $result = sqlsrv_query($this->conn, $sql, [$numeroExpediente]);
            
            if (!$result) {
                error_log("Error al marcar enviado a cajera: " . print_r(sqlsrv_errors(), true));
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Excepción al marcar enviado a cajera: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el cliente ya aceptó
     */
    public function clienteAcepto($numeroExpediente) {
        try {
            $sql = "SELECT COUNT(*) as total FROM SIST_CONFIRMACIONES_CLIENTE 
                    WHERE CONF_NUMERO_EXPEDIENTE = ? 
                    AND CONF_ESTADO = 'ACEPTADO'";
            
            $result = sqlsrv_query($this->conn, $sql, [$numeroExpediente]);
            
            if (!$result) {
                return false;
            }
            
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (Exception $e) {
            error_log("Error al verificar aceptación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si ya se envió a cajera
     */
    public function yaEnviadoCajera($numeroExpediente) {
        try {
            $sql = "SELECT CONF_ENVIADO_CAJERA FROM SIST_CONFIRMACIONES_CLIENTE 
                    WHERE CONF_NUMERO_EXPEDIENTE = ? 
                    ORDER BY CONF_FECHA_ENVIO DESC";
            
            $result = sqlsrv_query($this->conn, $sql, [$numeroExpediente]);
            
            if (!$result) {
                return false;
            }
            
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            return $row && $row['CONF_ENVIADO_CAJERA'] == 1;
        } catch (Exception $e) {
            error_log("Error al verificar envío a cajera: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar correo de confirmación al cliente
     */
    public function enviarCorreoCliente($numeroExpediente, $emailCliente, $token) {
        try {
            // URL de confirmación
            $urlConfirmacion = "http://190.238.78.104:3800/digitalizacion-documentos/confirmacion/ver?token=" . $token;
            
            // Construir HTML del correo
            $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #1e3a8a;'>📄 Confirmación de Documentos - Expediente {$numeroExpediente}</h2>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <p>Estimado cliente,</p>
                    <p>Hemos preparado todos los documentos correspondientes a su expediente <strong>{$numeroExpediente}</strong>.</p>
                    <p>Por favor, revise los documentos y confirme que está de acuerdo con la información presentada.</p>
                </div>
                
                <p style='text-align: center;'>
                    <a href='{$urlConfirmacion}' 
                       style='background-color: #1e3a8a; color: white; padding: 15px 30px; 
                              text-decoration: none; border-radius: 8px; display: inline-block;
                              font-weight: bold; font-size: 16px;'>
                        👁️ Ver Documentos y Confirmar
                    </a>
                </p>
                
                <div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;'>
                    <p style='margin: 0; color: #856404;'>
                        <strong>⚠️ Importante:</strong> Una vez que confirme, procederemos con el proceso de validación final.
                    </p>
                </div>
                
                <p style='color: #666; font-size: 12px; text-align: center;'>
                    Este correo fue generado automáticamente. Por favor no responder.
                </p>
            </div>
            ";
            
            // Preparar datos para la API de correo
            $emailData = [
                'to' => $emailCliente,
                'subject' => "📬 Confirmación de Documentos - Expediente {$numeroExpediente}",
                'html' => $htmlBody,
                'from' => 'comunica@interamericana.shop',
                'from_name' => 'Sistema de Digitalización Interamericana'
            ];
            
            error_log("=== ENVIANDO CORREO AL CLIENTE ===");
            error_log("TO: " . $emailCliente);
            error_log("Expediente: " . $numeroExpediente);
            error_log("URL API: http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php");
            
            // Enviar correo usando cURL
            $ch = curl_init('http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            error_log("HTTP Code: " . $httpCode);
            error_log("Response: " . $response);
            if ($curlError) {
                error_log("cURL Error: " . $curlError);
            }
            
            if ($httpCode >= 200 && $httpCode < 300) {
                error_log("✅ Correo enviado exitosamente al cliente");
                return true;
            } else {
                error_log("❌ Error al enviar correo. HTTP Code: " . $httpCode);
                error_log("Response body: " . $response);
                return false;
            }
        } catch (Exception $e) {
            error_log("Excepción al enviar correo al cliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar correo a la cajera con el folio completo
     */
    public function enviarCorreoCajera($numeroExpediente, $emailCajera = 'caja@interamericana.shop') {
        try {
            // URL del folio completo en modo impresión
            $urlFolio = "http://190.238.78.104:3800/digitalizacion-documentos/expedientes/imprimir?numero=" . $numeroExpediente;
            
            // Construir HTML del correo
            $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #1e3a8a;'>💼 Expediente Listo para Validación - {$numeroExpediente}</h2>
                
                <div style='background: #d1fae5; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981;'>
                    <p style='margin: 0; color: #065f46;'>
                        <strong>✅ El cliente ha confirmado y aceptado todos los documentos.</strong>
                    </p>
                </div>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <p><strong>Número de Expediente:</strong> {$numeroExpediente}</p>
                    <p>El expediente está listo para su validación y procesamiento final.</p>
                </div>
                
                <p style='text-align: center;'>
                    <a href='{$urlFolio}' 
                       style='background-color: #10b981; color: white; padding: 15px 30px; 
                              text-decoration: none; border-radius: 8px; display: inline-block;
                              font-weight: bold; font-size: 16px;'>
                        📋 Ver Folio Completo
                    </a>
                </p>
                
                <p style='color: #666; font-size: 12px; text-align: center;'>
                    Este correo fue generado automáticamente. Por favor no responder.
                </p>
            </div>
            ";
            
            // Preparar datos para la API de correo
            $emailData = [
                'to' => $emailCajera,
                'subject' => "💼 Expediente Listo para Validación - {$numeroExpediente}",
                'html' => $htmlBody,
                'from' => 'comunica@interamericana.shop',
                'from_name' => 'Sistema de Digitalización Interamericana'
            ];
            
            error_log("=== ENVIANDO CORREO A CAJERA ===");
            error_log("TO: " . $emailCajera);
            error_log("Expediente: " . $numeroExpediente);
            error_log("URL API: http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php");
            
            // Enviar correo usando cURL
            $ch = curl_init('http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            error_log("HTTP Code: " . $httpCode);
            error_log("Response: " . $response);
            if ($curlError) {
                error_log("cURL Error: " . $curlError);
            }
            
            if ($httpCode >= 200 && $httpCode < 300) {
                error_log("✅ Correo enviado exitosamente a cajera");
                return true;
            } else {
                error_log("❌ Error al enviar correo a cajera. HTTP Code: " . $httpCode);
                error_log("Response body: " . $response);
                return false;
            }
        } catch (Exception $e) {
            error_log("Excepción al enviar correo a cajera: " . $e->getMessage());
            return false;
        }
    }
}
