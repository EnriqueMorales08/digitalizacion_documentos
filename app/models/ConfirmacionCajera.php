<?php
require_once __DIR__ . '/../../config/database.php';

class ConfirmacionCajera {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Crear nueva confirmación de cajera
     */
    public function crear($numeroExpediente, $emailCajera) {
        try {
            // Generar token único
            $token = bin2hex(random_bytes(32));
            
            $sql = "INSERT INTO SIST_CONFIRMACIONES_CAJERA 
                    (CAJERA_NUMERO_EXPEDIENTE, CAJERA_EMAIL, CAJERA_TOKEN, CAJERA_FECHA_ENVIO) 
                    VALUES (?, ?, ?, GETDATE())";
            
            $stmt = sqlsrv_prepare($this->conn, $sql, array(&$numeroExpediente, &$emailCajera, &$token));
            
            if ($stmt && sqlsrv_execute($stmt)) {
                return $token;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error al crear confirmación de cajera: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener confirmación por token
     */
    public function obtenerPorToken($token) {
        try {
            $sql = "SELECT * FROM SIST_CONFIRMACIONES_CAJERA WHERE CAJERA_TOKEN = ?";
            $stmt = sqlsrv_prepare($this->conn, $sql, array(&$token));
            
            if ($stmt && sqlsrv_execute($stmt)) {
                return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error al obtener confirmación de cajera: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Actualizar respuesta de cajera (aprobar/rechazar con firma)
     */
    public function actualizarRespuesta($token, $estado, $firma, $observaciones = '') {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'DESCONOCIDO';
            
            $sql = "UPDATE SIST_CONFIRMACIONES_CAJERA 
                    SET CAJERA_ESTADO = ?, 
                        CAJERA_FIRMA = ?,
                        CAJERA_OBSERVACIONES = ?,
                        CAJERA_FECHA_RESPUESTA = GETDATE(),
                        CAJERA_IP = ?,
                        CAJERA_UPDATED_AT = GETDATE()
                    WHERE CAJERA_TOKEN = ?";
            
            $stmt = sqlsrv_prepare($this->conn, $sql, array(
                &$estado, 
                &$firma, 
                &$observaciones, 
                &$ip, 
                &$token
            ));
            
            if ($stmt && sqlsrv_execute($stmt)) {
                // Actualizar la firma en la orden de compra
                $confirmacion = $this->obtenerPorToken($token);
                if ($confirmacion) {
                    $this->actualizarFirmaEnOrden($confirmacion['CAJERA_NUMERO_EXPEDIENTE'], $firma);
                }
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error al actualizar respuesta de cajera: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar firma de cajera en la orden de compra
     */
    private function actualizarFirmaEnOrden($numeroExpediente, $firma) {
        try {
            $sql = "UPDATE SIST_ORDEN_COMPRA 
                    SET OC_VISTO_ADV = ?
                    WHERE OC_NUMERO_EXPEDIENTE = ?";
            
            $stmt = sqlsrv_prepare($this->conn, $sql, array(&$firma, &$numeroExpediente));
            
            return ($stmt && sqlsrv_execute($stmt));
        } catch (Exception $e) {
            error_log("Error al actualizar firma en orden: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Enviar correo a cajera con link de confirmación
     */
    public function enviarCorreo($numeroExpediente, $emailCajera) {
        try {
            // Crear token de confirmación
            $token = $this->crear($numeroExpediente, $emailCajera);
            
            if (!$token) {
                throw new Exception("No se pudo crear el token de confirmación");
            }
            
            // Construir URL de confirmación
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
            $urlConfirmacion = $baseUrl . "/digitalizacion-documentos/cajera/ver?token=" . urlencode($token);
            
            // Preparar datos del correo
            $asunto = "Confirmación de Documentos - Expediente " . $numeroExpediente;
            $mensaje = "
                <h2>Confirmación de Documentos</h2>
                <p>Estimada Cajera,</p>
                <p>Se requiere su revisión y aprobación de los documentos del expediente <strong>{$numeroExpediente}</strong>.</p>
                <p>Por favor, haga clic en el siguiente enlace para revisar los documentos y proporcionar su firma digital:</p>
                <p><a href='{$urlConfirmacion}' style='background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>Ver Documentos y Firmar</a></p>
                <p>Este enlace es único y personal.</p>
                <p>Gracias,<br>Sistema de Gestión de Documentos</p>
            ";
            
            // Enviar correo usando API externa
            $resultado = $this->enviarCorreoAPI($emailCajera, $asunto, $mensaje);
            
            if (!$resultado['success']) {
                throw new Exception($resultado['message']);
            }
            
            return array('success' => true, 'token' => $token);
            
        } catch (Exception $e) {
            error_log("Error al enviar correo a cajera: " . $e->getMessage());
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
    
    /**
     * Enviar correo usando API externa
     */
    private function enviarCorreoAPI($destinatario, $asunto, $mensaje) {
        try {
            // Preparar datos para la API de correo (mismo formato que ConfirmacionCliente)
            $emailData = [
                'to' => $destinatario,
                'subject' => $asunto,
                'html' => $mensaje,
                'from' => 'comunica@interamericana.shop',
                'from_name' => 'Sistema de Digitalización Interamericana'
            ];
            
            error_log("=== ENVIANDO CORREO A CAJERA ===");
            error_log("TO: " . $destinatario);
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
                return array('success' => true, 'message' => 'Correo enviado exitosamente');
            } else {
                error_log("❌ Error al enviar correo. HTTP Code: " . $httpCode);
                error_log("Response body: " . $response);
                return array('success' => false, 'message' => 'Error al enviar correo. HTTP Code: ' . $httpCode);
            }
            
        } catch (Exception $e) {
            error_log("Excepción al enviar correo a cajera: " . $e->getMessage());
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
    
    /**
     * Verificar estado de confirmación de cajera
     */
    public function verificarEstado($numeroExpediente) {
        try {
            $sql = "SELECT CAJERA_ESTADO, CAJERA_FECHA_RESPUESTA 
                    FROM SIST_CONFIRMACIONES_CAJERA 
                    WHERE CAJERA_NUMERO_EXPEDIENTE = ? 
                    ORDER BY CAJERA_FECHA_ENVIO DESC";
            
            $stmt = sqlsrv_prepare($this->conn, $sql, array(&$numeroExpediente));
            
            if ($stmt && sqlsrv_execute($stmt)) {
                $result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                return $result ?: array('CAJERA_ESTADO' => 'NO_ENVIADO');
            }
            
            return array('CAJERA_ESTADO' => 'NO_ENVIADO');
        } catch (Exception $e) {
            error_log("Error al verificar estado de cajera: " . $e->getMessage());
            return array('CAJERA_ESTADO' => 'ERROR');
        }
    }
}
