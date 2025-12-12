<?php
require_once __DIR__ . '/../models/ConfirmacionCliente.php';
require_once __DIR__ . '/../models/ConfirmacionCajera.php';
require_once __DIR__ . '/../models/Document.php';

class ConfirmacionController {
    private $confirmacionModel;
    private $cajeraModel;
    private $documentModel;

    public function __construct() {
        $this->confirmacionModel = new ConfirmacionCliente();
        $this->cajeraModel = new ConfirmacionCajera();
        $this->documentModel = new Document();
    }

    /**
     * Enviar correo de confirmación al cliente
     */
    public function enviarCliente() {
        header('Content-Type: application/json');
        
        try {
            // Validar que venga el número de expediente
            if (!isset($_POST['numero_expediente'])) {
                echo json_encode(['success' => false, 'error' => 'Número de expediente no proporcionado']);
                return;
            }
            
            $numeroExpediente = trim($_POST['numero_expediente']);
            
            // Obtener datos de la orden de compra
            $orden = $this->documentModel->getOrdenCompraPorExpediente($numeroExpediente);
            
            if (!$orden) {
                echo json_encode(['success' => false, 'error' => 'Orden de compra no encontrada']);
                return;
            }
            
            // Obtener email del cliente (OC_EMAIL_CLIENTE, no OC_EMAIL_CLIENTE_2)
            $emailCliente = trim($orden['OC_EMAIL_CLIENTE'] ?? '');
            
            if (empty($emailCliente) || !filter_var($emailCliente, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'error' => 'Email del cliente no válido']);
                return;
            }
            
            // Verificar si ya existe una confirmación pendiente o aceptada
            $confirmacionExistente = $this->confirmacionModel->obtenerPorExpediente($numeroExpediente);
            
            if ($confirmacionExistente && $confirmacionExistente['CONF_ESTADO'] === 'ACEPTADO') {
                echo json_encode(['success' => false, 'error' => 'El cliente ya aceptó los documentos']);
                return;
            }
            
            // Crear nueva confirmación
            $token = $this->confirmacionModel->crear($numeroExpediente, $emailCliente);
            
            if (!$token) {
                echo json_encode(['success' => false, 'error' => 'Error al crear confirmación']);
                return;
            }
            
            // Enviar correo al cliente
            $envioExitoso = $this->confirmacionModel->enviarCorreoCliente($numeroExpediente, $emailCliente, $token);
            
            if ($envioExitoso) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Correo enviado exitosamente al cliente: ' . $emailCliente
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al enviar correo']);
            }
            
        } catch (Exception $e) {
            error_log("Error en enviarCliente: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    /**
     * Enviar correo a la cajera
     */
    public function enviarCajera() {
        header('Content-Type: application/json');
        
        try {
            // Validar datos
            if (!isset($_POST['numero_expediente']) || !isset($_POST['email_cajera'])) {
                throw new Exception('Datos incompletos');
            }
            
            $numeroExpediente = trim($_POST['numero_expediente']);
            $emailCajera = trim($_POST['email_cajera']);
            
            // Validar email
            if (!filter_var($emailCajera, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email de cajera inválido');
            }
            
            // Verificar que el cliente haya aceptado
            if (!$this->confirmacionModel->clienteAcepto($numeroExpediente)) {
                throw new Exception('El cliente aún no ha aceptado la orden de compra');
            }
            
            // Enviar correo a cajera
            $resultado = $this->cajeraModel->enviarCorreo($numeroExpediente, $emailCajera);
            
            if (!$resultado['success']) {
                throw new Exception($resultado['message']);
            }
            
            // Marcar como enviado en la tabla de confirmaciones de cliente
            $this->confirmacionModel->marcarEnviadoCajera($numeroExpediente);
            
            echo json_encode([
                'success' => true,
                'message' => 'Correo enviado exitosamente a la cajera'
            ]);
            
        } catch (Exception $e) {
            error_log("Error en enviarCajera: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mostrar página de confirmación para el cliente
     */
    public function ver() {
        // Validar token
        if (!isset($_GET['token'])) {
            die('Token no proporcionado');
        }
        
        $token = trim($_GET['token']);
        
        // Obtener confirmación
        $confirmacion = $this->confirmacionModel->obtenerPorToken($token);
        
        if (!$confirmacion) {
            die('Token inválido o expirado');
        }
        
        // Obtener datos de la orden
        $numeroExpediente = $confirmacion['CONF_NUMERO_EXPEDIENTE'];
        $orden = $this->documentModel->getOrdenCompraPorExpediente($numeroExpediente);
        
        if (!$orden) {
            die('Orden de compra no encontrada');
        }

        // Obtener documentos asociados a la orden (para listar al cliente)
        $documentos = $this->documentModel->getDocumentosPorOrden($orden['OC_ID']);
        
        // Cargar vista de confirmación
        require __DIR__ . '/../views/confirmacion/ver.php';
    }

    /**
     * Procesar respuesta del cliente (aceptar/rechazar)
     */
    public function responder() {
        header('Content-Type: application/json');
        
        try {
            // Validar datos
            if (!isset($_POST['token']) || !isset($_POST['respuesta'])) {
                echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
                return;
            }
            
            $token = trim($_POST['token']);
            $respuesta = trim($_POST['respuesta']); // 'ACEPTADO' o 'RECHAZADO'
            $observaciones = trim($_POST['observaciones'] ?? '');
            
            // Validar respuesta
            if (!in_array($respuesta, ['ACEPTADO', 'RECHAZADO'])) {
                echo json_encode(['success' => false, 'error' => 'Respuesta inválida']);
                return;
            }
            
            // Obtener IP del cliente
            $ipCliente = $_SERVER['REMOTE_ADDR'] ?? null;
            
            // Actualizar estado
            $actualizado = $this->confirmacionModel->actualizarEstado($token, $respuesta, $ipCliente, $observaciones);
            
            if ($actualizado) {
                echo json_encode([
                    'success' => true, 
                    'message' => $respuesta === 'ACEPTADO' 
                        ? 'Gracias por confirmar. Procederemos con el proceso.' 
                        : 'Su respuesta ha sido registrada.'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al registrar respuesta']);
            }
            
        } catch (Exception $e) {
            error_log("Error en responder: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    /**
     * Verificar estado de confirmación (para habilitar botón de cajera)
     */
    public function verificarEstado() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_GET['numero_expediente'])) {
                echo json_encode(['success' => false, 'error' => 'Número de expediente no proporcionado']);
                return;
            }
            
            $numeroExpediente = trim($_GET['numero_expediente']);
            
            $clienteAcepto = $this->confirmacionModel->clienteAcepto($numeroExpediente);
            $yaEnviado = $this->confirmacionModel->yaEnviadoCajera($numeroExpediente);
            
            echo json_encode([
                'success' => true,
                'cliente_acepto' => $clienteAcepto,
                'ya_enviado_cajera' => $yaEnviado,
                'puede_enviar_cajera' => $clienteAcepto && !$yaEnviado
            ]);
            
        } catch (Exception $e) {
            error_log("Error en verificarEstado: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }

    /**
     * Ver TODOS los documentos para el cliente (vista solo lectura, sin login)
     * Se basa en el token de confirmación del cliente.
     */
    public function verTodos() {
        if (!isset($_GET['token'])) {
            die('Token no proporcionado');
        }

        $token = trim($_GET['token']);

        // Obtener confirmación por token
        $confirmacion = $this->confirmacionModel->obtenerPorToken($token);
        if (!$confirmacion) {
            die('Token inválido o expirado');
        }

        $numeroExpediente = $confirmacion['CONF_NUMERO_EXPEDIENTE'];

        // Buscar la orden de compra por número de expediente
        $ordenCompra = $this->documentModel->buscarPorNumeroExpediente($numeroExpediente);
        if (!$ordenCompra) {
            die('Expediente no encontrado');
        }

        $ordenId = $ordenCompra['OC_ID'];

        // Configurar variables de sesión necesarias para los layouts de documentos
        $_SESSION['orden_id'] = $ordenId;
        $_SESSION['forma_pago'] = $ordenCompra['OC_FORMA_PAGO'] ?? '';
        $_SESSION['banco_abono'] = $ordenCompra['OC_BANCO_ABONO'] ?? '';

        // FLAG para indicar que es modo impresión/visualización
        $modoImpresion = true;

        // Normalizar fechas a string para los inputs type="date"
        if (isset($ordenCompra['OC_FECHA_ORDEN']) && $ordenCompra['OC_FECHA_ORDEN'] instanceof DateTime) {
            $ordenCompra['OC_FECHA_ORDEN'] = $ordenCompra['OC_FECHA_ORDEN']->format('Y-m-d');
        }
        if (isset($ordenCompra['OC_FECHA_NACIMIENTO']) && $ordenCompra['OC_FECHA_NACIMIENTO'] instanceof DateTime) {
            $ordenCompra['OC_FECHA_NACIMIENTO'] = $ordenCompra['OC_FECHA_NACIMIENTO']->format('Y-m-d');
        }

        // Preparar datos para la vista reutilizada
        $ordenCompraData = $ordenCompra;
        $id = $ordenId;

        // Flag para que la vista sepa que está siendo usada por el cliente (sin login)
        $esVistaCliente = true;

        // Obtener todos los documentos asociados
        $documentos = $this->documentModel->getDocumentosPorOrden($ordenId);

        // Cargar datos del vehículo si es necesario
        $vehiculoData = [];
        $chasis = $ordenCompra['OC_VEHICULO_CHASIS'] ?? '';
        if ($chasis) {
            $vehiculoData = $this->documentModel->buscarVehiculoPorChasis($chasis);
        }

        // Reutilizar la misma vista de "imprimir todos" de expedientes
        require __DIR__ . '/../views/expedientes/imprimir_todos.php';
    }

    public function guardarFirmaCliente()
    {
        header('Content-Type: application/json');

        try {
            if (!isset($_POST['token']) || !isset($_POST['firma_base64'])) {
                echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
                return;
            }

            $token = trim($_POST['token']);
            $firmaBase64 = $_POST['firma_base64'];

            $confirmacion = $this->confirmacionModel->obtenerPorToken($token);

            if (!$confirmacion) {
                echo json_encode(['success' => false, 'error' => 'Token inválido o expirado']);
                return;
            }

            $firmaData = explode(',', $firmaBase64);
            if (count($firmaData) < 2) {
                echo json_encode(['success' => false, 'error' => 'Formato de firma inválido']);
                return;
            }

            $imagenBase64 = $firmaData[1];
            $imagenBinaria = base64_decode($imagenBase64);

            if ($imagenBinaria === false) {
                echo json_encode(['success' => false, 'error' => 'Error al decodificar la firma']);
                return;
            }

            $carpetaFirmas = __DIR__ . '/../../uploads/firmas_cliente';
            if (!file_exists($carpetaFirmas)) {
                mkdir($carpetaFirmas, 0777, true);
            }

            $nombreArchivo = 'firma_cliente_' . uniqid() . '_' . time() . '.png';
            $rutaCompleta = $carpetaFirmas . '/' . $nombreArchivo;

            if (file_put_contents($rutaCompleta, $imagenBinaria) === false) {
                echo json_encode(['success' => false, 'error' => 'Error al guardar el archivo de firma']);
                return;
            }

            $rutaRelativa = '/digitalizacion-documentos/uploads/firmas_cliente/' . $nombreArchivo;

            $numeroExpediente = $confirmacion['CONF_NUMERO_EXPEDIENTE'];
            $orden = $this->documentModel->getOrdenCompraPorExpediente($numeroExpediente);

            if (!$orden) {
                echo json_encode(['success' => false, 'error' => 'Orden de compra no encontrada']);
                return;
            }

            $ordenId = $orden['OC_ID'];

            // Actualizar siempre la firma del cliente en la orden de compra
            $this->documentModel->actualizarFirmaClienteEnOrden($ordenId, $rutaRelativa);

            // Aplicar firma solo a los documentos seleccionados por el cliente
            $documentosSeleccionados = $_POST['documentos_seleccionados'] ?? [];
            if (!is_array($documentosSeleccionados)) {
                $documentosSeleccionados = [];
            }

            if (!empty($documentosSeleccionados)) {
                // Mapa de documentos firmables: tabla, campo de firma y campo de relación con la orden (DOCUMENTO_VENTA_ID)
                $mapaFirmas = [
                    'carta_conocimiento_aceptacion'   => ['tabla' => 'SIST_CARTA_CONOCIMIENTO_ACEPTACION',     'campo' => 'CCA_FIRMA_CLIENTE', 'pk' => 'CCA_DOCUMENTO_VENTA_ID'],
                    'carta_recepcion'                  => ['tabla' => 'SIST_CARTA_RECEPCION',                   'campo' => 'CR_FIRMA_CLIENTE',  'pk' => 'CR_DOCUMENTO_VENTA_ID'],
                    // Nota: la tabla SIST_CARTA_CARACTERISTICAS no tiene campo de firma de cliente en el esquema actual
                    'politica_proteccion_datos'        => ['tabla' => 'SIST_POLITICA_PROTECCION_DATOS',        'campo' => 'PPD_FIRMA_CLIENTE', 'pk' => 'PPD_DOCUMENTO_VENTA_ID'],
                    'acta-conocimiento-conformidad'    => ['tabla' => 'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD',    'campo' => 'ACC_FIRMA_CLIENTE', 'pk' => 'ACC_DOCUMENTO_VENTA_ID'],
                    'actorizacion-datos-personales'    => ['tabla' => 'SIST_AUTORIZACION_DATOS_PERSONALES',    'campo' => 'ADP_FIRMA_CLIENTE', 'pk' => 'ADP_DOCUMENTO_VENTA_ID'],
                ];

                foreach ($documentosSeleccionados as $docId) {
                    if (!isset($mapaFirmas[$docId])) {
                        continue;
                    }
                    $info = $mapaFirmas[$docId];
                    $tabla = $info['tabla'];
                    $campo = $info['campo'];
                    $pk    = $info['pk'];

                    $sql = "UPDATE $tabla SET $campo = ? WHERE $pk = ?";
                    $params = [$rutaRelativa, $ordenId];
                    sqlsrv_query($this->documentModel->getConnection(), $sql, $params);
                }
            }

            echo json_encode([
                'success' => true,
                'ruta' => $rutaRelativa,
                'message' => 'Firma guardada correctamente'
            ]);

        } catch (Exception $e) {
            error_log('Error en guardarFirmaCliente (confirmación): ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }
}
