<?php
require_once __DIR__ . '/../models/SolicitudVehiculo.php';

class SolicitudVehiculoController {
    private $solicitudModel;

    public function __construct() {
        $this->solicitudModel = new SolicitudVehiculo();
    }

    /**
     * Procesar solicitud de vehículo libre
     */
    public function solicitarLibre() {
        header('Content-Type: application/json');
        
        try {
            // Validar datos
            if (!isset($_POST['chasis']) || !isset($_POST['marca'])) {
                echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
                return;
            }
            
            // Obtener datos del asesor logueado
            $solicitanteNombre = $_SESSION['usuario_nombre_completo'] ?? '';
            $solicitanteEmail = $_SESSION['usuario_email'] ?? '';
            
            error_log("=== SOLICITAR LIBRE - DEBUG ===");
            error_log("Nombre: " . $solicitanteNombre);
            error_log("Email: " . $solicitanteEmail);
            error_log("Email vacío? " . (empty($solicitanteEmail) ? 'SI' : 'NO'));
            
            if (empty($solicitanteNombre) || empty($solicitanteEmail)) {
                echo json_encode([
                    'success' => false, 
                    'error' => 'No se pudo identificar al usuario. Nombre: ' . $solicitanteNombre . ', Email: ' . $solicitanteEmail
                ]);
                return;
            }
            
            // Crear solicitud
            $datos = [
                'chasis' => trim($_POST['chasis']),
                'marca' => trim($_POST['marca']),
                'ubicacion' => trim($_POST['ubicacion'] ?? ''),
                'tipo' => 'LIBRE',
                'solicitante_nombre' => $solicitanteNombre,
                'solicitante_email' => $solicitanteEmail
            ];
            
            $token = $this->solicitudModel->crear($datos);
            
            if (!$token) {
                echo json_encode(['success' => false, 'error' => 'Error al crear solicitud']);
                return;
            }
            
            // ✅ ACTUALIZAR STOCK inmediatamente (vehículo libre)
            $stockActualizado = $this->solicitudModel->actualizarVendedorStock(
                $datos['chasis'],
                $solicitanteNombre
            );
            
            if (!$stockActualizado) {
                error_log("⚠️ ADVERTENCIA: No se pudo actualizar STOCK para chasis: " . $datos['chasis']);
            }
            
            // Obtener la solicitud creada
            $solicitud = $this->solicitudModel->obtenerPorToken($token);
            
            error_log("📧 Solicitud obtenida de BD:");
            error_log("Nombre: " . ($solicitud['SOL_ASESOR_SOLICITANTE_NOMBRE'] ?? 'NULL'));
            error_log("Email: " . ($solicitud['SOL_ASESOR_SOLICITANTE_EMAIL'] ?? 'NULL'));
            
            // Actualizar estado a ACEPTADO (ya que es automático)
            $this->solicitudModel->actualizarEstado($token, 'ACEPTADO', $_SERVER['REMOTE_ADDR'] ?? null, 'Asignación automática de vehículo libre');
            
            // Enviar correo de confirmación al usuario logueado
            $envioExitoso = $this->solicitudModel->enviarCorreoAsignacionLibre($solicitud);
            
            error_log("📬 Resultado envío correo: " . ($envioExitoso ? 'EXITOSO' : 'FALLIDO'));
            
            // Retornar éxito
            echo json_encode([
                'success' => true,
                'message' => 'Vehículo asignado correctamente. ' . ($envioExitoso ? 'Correo enviado.' : 'Error al enviar correo.'),
                'chasis' => $datos['chasis'],
                'correo_enviado' => $envioExitoso,
                'email_destino' => $solicitanteEmail,
                'stock_actualizado' => $stockActualizado
            ]);
            
        } catch (Exception $e) {
            error_log("Error en solicitarLibre: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    /**
     * Procesar solicitud de reasignación (vehículo de otro asesor)
     */
    public function solicitarReasignacion() {
        header('Content-Type: application/json');
        
        try {
            // Validar datos
            if (!isset($_POST['chasis']) || !isset($_POST['marca']) || !isset($_POST['asesor_dueno'])) {
                echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
                return;
            }
            
            // Obtener datos del asesor logueado (solicitante)
            $solicitanteNombre = $_SESSION['usuario_nombre_completo'] ?? '';
            $solicitanteEmail = $_SESSION['usuario_email'] ?? '';
            
            error_log("=== SOLICITAR REASIGNACIÓN - DEBUG ===");
            error_log("Solicitante: " . $solicitanteNombre);
            error_log("Email solicitante: " . $solicitanteEmail);
            
            if (empty($solicitanteNombre) || empty($solicitanteEmail)) {
                echo json_encode(['success' => false, 'error' => 'No se pudo identificar al usuario']);
                return;
            }
            
            // Buscar correo del asesor dueño
            $asesorDuenoNombre = trim($_POST['asesor_dueno']);
            error_log("Buscando correo del asesor dueño: " . $asesorDuenoNombre);
            
            $asesorDueno = $this->solicitudModel->buscarCorreoAsesor($asesorDuenoNombre);
            
            if (!$asesorDueno || empty($asesorDueno['email'])) {
                error_log("❌ No se encontró el correo del asesor: " . $asesorDuenoNombre);
                echo json_encode(['success' => false, 'error' => 'No se encontró el correo del asesor dueño: ' . $asesorDuenoNombre]);
                return;
            }
            
            error_log("✅ Asesor dueño encontrado:");
            error_log("Nombre: " . $asesorDueno['nombre']);
            error_log("Email: " . $asesorDueno['email']);
            
            // Crear solicitud
            $datos = [
                'chasis' => trim($_POST['chasis']),
                'marca' => trim($_POST['marca']),
                'ubicacion' => trim($_POST['ubicacion'] ?? ''),
                'tipo' => 'REASIGNACION',
                'solicitante_nombre' => $solicitanteNombre,
                'solicitante_email' => $solicitanteEmail,
                'dueno_nombre' => $asesorDueno['nombre'],
                'dueno_email' => $asesorDueno['email']
            ];
            
            $token = $this->solicitudModel->crear($datos);
            
            if (!$token) {
                echo json_encode(['success' => false, 'error' => 'Error al crear solicitud']);
                return;
            }
            
            // Obtener la solicitud creada
            $solicitud = $this->solicitudModel->obtenerPorToken($token);
            
            error_log("📧 Solicitud creada en BD:");
            error_log("Token: " . $token);
            error_log("Dueño: " . ($solicitud['SOL_ASESOR_DUENO_NOMBRE'] ?? 'NULL'));
            error_log("Email dueño: " . ($solicitud['SOL_ASESOR_DUENO_EMAIL'] ?? 'NULL'));
            
            // Enviar correo al asesor dueño
            $envioExitoso = $this->solicitudModel->enviarCorreoSolicitud($solicitud);
            
            error_log("📬 Resultado envío correo: " . ($envioExitoso ? 'EXITOSO' : 'FALLIDO'));
            
            echo json_encode([
                'success' => true,
                'message' => 'Solicitud enviada al asesor ' . $asesorDueno['nombre'] . '. ' . 
                            ($envioExitoso ? 'Correo enviado.' : 'Error al enviar correo.'),
                'correo_enviado' => $envioExitoso,
                'email_dueno' => $asesorDueno['email'],
                'nombre_dueno' => $asesorDueno['nombre']
            ]);
            
        } catch (Exception $e) {
            error_log("Error en solicitarReasignacion: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    /**
     * Aceptar solicitud de reasignación (SIN LOGIN - usa token)
     */
    public function aceptar() {
        try {
            $token = $_GET['token'] ?? '';
            
            if (empty($token)) {
                $this->mostrarError('Token inválido o no proporcionado');
                return;
            }
            
            // Obtener solicitud por token (SIN VALIDAR SESIÓN)
            $solicitud = $this->solicitudModel->obtenerPorToken($token);
            
            if (!$solicitud) {
                $this->mostrarError('Solicitud no encontrada. El enlace puede ser inválido.');
                return;
            }
            
            if ($solicitud['SOL_ESTADO'] !== 'PENDIENTE') {
                $estado = $solicitud['SOL_ESTADO'];
                $this->mostrarError("Esta solicitud ya fue procesada anteriormente. Estado actual: {$estado}");
                return;
            }
            
            // ✅ ACTUALIZAR STOCK con el nuevo vendedor (solicitante)
            $nuevoVendedor = $solicitud['SOL_ASESOR_SOLICITANTE_NOMBRE'];
            $chasis = $solicitud['SOL_CHASIS'];
            
            $stockActualizado = $this->solicitudModel->actualizarVendedorStock($chasis, $nuevoVendedor);
            
            if (!$stockActualizado) {
                error_log("⚠️ ADVERTENCIA: No se pudo actualizar STOCK al aceptar reasignación. Chasis: {$chasis}");
            }
            
            // Actualizar estado de la solicitud
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $this->solicitudModel->actualizarEstado($token, 'ACEPTADO', $ip, 'Aceptado por el asesor dueño');
            
            // Enviar correo de notificación al solicitante
            $this->solicitudModel->enviarCorreoNotificacion($solicitud, true);
            
            // Mostrar página de confirmación
            $this->mostrarRespuesta('aceptado', $solicitud);
            
        } catch (Exception $e) {
            error_log("Error en aceptar: " . $e->getMessage());
            $this->mostrarError('Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar solicitud de reasignación (SIN LOGIN - usa token)
     */
    public function rechazar() {
        try {
            $token = $_GET['token'] ?? '';
            
            if (empty($token)) {
                $this->mostrarError('Token inválido o no proporcionado');
                return;
            }
            
            // Obtener solicitud por token (SIN VALIDAR SESIÓN)
            $solicitud = $this->solicitudModel->obtenerPorToken($token);
            
            if (!$solicitud) {
                $this->mostrarError('Solicitud no encontrada. El enlace puede ser inválido.');
                return;
            }
            
            if ($solicitud['SOL_ESTADO'] !== 'PENDIENTE') {
                $estado = $solicitud['SOL_ESTADO'];
                $this->mostrarError("Esta solicitud ya fue procesada anteriormente. Estado actual: {$estado}");
                return;
            }
            
            // ❌ NO actualizar STOCK (el vehículo sigue con el dueño actual)
            
            // Actualizar estado de la solicitud
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $this->solicitudModel->actualizarEstado($token, 'RECHAZADO', $ip, 'Rechazado por el asesor dueño');
            
            // Enviar correo de notificación al solicitante
            $this->solicitudModel->enviarCorreoNotificacion($solicitud, false);
            
            // Mostrar página de confirmación
            $this->mostrarRespuesta('rechazado', $solicitud);
            
        } catch (Exception $e) {
            error_log("Error en rechazar: " . $e->getMessage());
            $this->mostrarError('Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar página de respuesta
     */
    private function mostrarRespuesta($tipo, $solicitud) {
        $chasis = $solicitud['SOL_CHASIS'];
        $marca = $solicitud['SOL_MARCA'];
        $solicitante = $solicitud['SOL_ASESOR_SOLICITANTE_NOMBRE'];
        
        if ($tipo === 'aceptado') {
            $titulo = "✅ Solicitud Aceptada";
            $mensaje = "Has aceptado ceder el vehículo <strong>{$marca} - {$chasis}</strong> al asesor <strong>{$solicitante}</strong>.";
            $color = "#10b981";
        } else {
            $titulo = "❌ Solicitud Rechazada";
            $mensaje = "Has rechazado la solicitud del asesor <strong>{$solicitante}</strong> para el vehículo <strong>{$marca} - {$chasis}</strong>.";
            $color = "#ef4444";
        }
        
        include __DIR__ . '/../views/solicitud-vehiculo/respuesta.php';
    }

    /**
     * Mostrar página de error
     */
    private function mostrarError($mensaje) {
        $titulo = "⚠️ Error";
        $color = "#f59e0b";
        
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$titulo}</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .container {
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                    max-width: 500px;
                    width: 100%;
                    padding: 40px;
                    text-align: center;
                }
                .icon {
                    font-size: 64px;
                    margin-bottom: 20px;
                }
                h1 {
                    color: {$color};
                    font-size: 28px;
                    margin-bottom: 20px;
                }
                p {
                    color: #666;
                    font-size: 16px;
                    line-height: 1.6;
                    margin-bottom: 30px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='icon'>⚠️</div>
                <h1>{$titulo}</h1>
                <p>{$mensaje}</p>
            </div>
        </body>
        </html>";
    }
}
