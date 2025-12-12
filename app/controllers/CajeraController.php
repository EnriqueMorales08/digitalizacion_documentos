<?php
require_once __DIR__ . '/../models/ConfirmacionCajera.php';
require_once __DIR__ . '/../models/Document.php';

class CajeraController {
    private $confirmacionModel;
    private $documentModel;
    
    public function __construct() {
        $this->confirmacionModel = new ConfirmacionCajera();
        $this->documentModel = new Document();
    }
    
    /**
     * Mostrar página de confirmación para cajera
     */
    public function ver() {
        if (!isset($_GET['token'])) {
            die('Token no proporcionado');
        }
        
        $token = $_GET['token'];
        
        // Obtener confirmación
        $confirmacion = $this->confirmacionModel->obtenerPorToken($token);
        
        if (!$confirmacion) {
            die('Token inválido o expirado');
        }
        
        // Obtener datos de la orden
        $numeroExpediente = $confirmacion['CAJERA_NUMERO_EXPEDIENTE'];
        $orden = $this->documentModel->getOrdenCompraPorExpediente($numeroExpediente);
        
        if (!$orden) {
            die('Orden de compra no encontrada');
        }
        
        // Obtener todos los documentos del expediente
        $documentos = $this->documentModel->getDocumentosPorOrden($orden['OC_ID']);
        
        // Agregar la orden de compra al inicio de la lista
        array_unshift($documentos, [
            'id' => 'orden-compra',
            'nombre' => 'Orden de Compra',
            'existe' => true
        ]);
        
        // Cargar vista de confirmación de cajera
        require __DIR__ . '/../views/cajera/ver.php';
    }
    
    /**
     * Procesar respuesta de cajera (aprobar/rechazar con firma)
     */
    public function responder() {
        header('Content-Type: application/json');
        
        try {
            // Validar datos
            if (!isset($_POST['token']) || !isset($_POST['respuesta']) || !isset($_POST['firma'])) {
                throw new Exception('Datos incompletos');
            }
            
            $token = $_POST['token'];
            $respuesta = $_POST['respuesta']; // APROBADO o RECHAZADO
            $firma = $_POST['firma']; // Base64 de la firma
            $observaciones = $_POST['observaciones'] ?? '';
            
            // Validar respuesta
            if (!in_array($respuesta, ['APROBADO', 'RECHAZADO'])) {
                throw new Exception('Respuesta inválida');
            }
            
            // Validar firma
            if (empty($firma)) {
                throw new Exception('La firma es obligatoria');
            }
            
            // Actualizar respuesta
            $resultado = $this->confirmacionModel->actualizarRespuesta($token, $respuesta, $firma, $observaciones);
            
            if (!$resultado) {
                throw new Exception('Error al guardar la respuesta');
            }
            
            echo json_encode([
                'success' => true,
                'message' => $respuesta === 'APROBADO' ? 'Documentos aprobados exitosamente' : 'Documentos rechazados'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Enviar correo a cajera (llamado desde ConfirmacionController)
     */
    public function enviar() {
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
            
            // Enviar correo
            $resultado = $this->confirmacionModel->enviarCorreo($numeroExpediente, $emailCajera);
            
            if (!$resultado['success']) {
                throw new Exception($resultado['message']);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Correo enviado exitosamente a la cajera'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
