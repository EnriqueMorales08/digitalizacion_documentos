<?php
require_once __DIR__ . '/../../config/database.php';

class Document {
    private $conn;

    public function __construct() {
        // Crear instancia de Database y obtener conexión
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function guardarOrdenCompra($data) {
        try {
            // Filtrar solo campos OC_ y truncar si es necesario
            $fields = [];
            $placeholders = [];
            $values = [];
            $maxLengths = [
                'OC_VEHICULO_ANIO_MODELO' => 10,
                // Agregar otros si es necesario
            ];
            foreach ($data as $key => $value) {
                if (strpos($key, 'OC_') === 0) {
                    $fields[] = $key;
                    $placeholders[] = "?";
                    $val = $value !== '' ? $value : null;
                    if ($val && isset($maxLengths[$key])) {
                        $val = substr($val, 0, $maxLengths[$key]);
                    }
                    // Limpiar símbolos de moneda y convertir a numérico si es posible
                    if ($val) {
                        // Quitar símbolos comunes de moneda y espacios
                        $cleanVal = preg_replace('/[$ ,USMNusmn]+/', '', $val);
                        if (is_numeric($cleanVal)) {
                            $val = (float)$cleanVal; // Siempre float para DECIMAL
                        }
                        // Si no es numérico, dejar el valor original (para campos de texto)
                    }
                    $values[] = $val;
                }
            }

            if (empty($fields)) {
                throw new Exception("No hay campos OC_ para insertar");
            }

            $sql = "INSERT INTO SIST_ORDEN_COMPRA (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($values);

            // Obtener ID
            $id = $this->conn->query("SELECT SCOPE_IDENTITY() AS id")->fetch(PDO::FETCH_ASSOC)['id'];

            // Precargar datos en otros documentos
            $this->precargarDocumentos($id, $data);

            return ['success' => true, 'id' => $id];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function precargarDocumentos($ordenId, $data) {
        // Función para truncar
        $trunc = function($val, $len) {
            return $val ? substr($val, 0, $len) : $val;
        };

        // Acta Conocimiento Conformidad
        $sql = "INSERT INTO SIST_ACTA_CONOCIMIENTO_CONFORMIDAD (ACC_DOCUMENTO_VENTA_ID, ACC_NOMBRE_CLIENTE, ACC_DNI_CLIENTE, ACC_MARCA_VEHICULO, ACC_MODELO_VEHICULO, ACC_ANIO_VEHICULO, ACC_VIN_VEHICULO, ACC_COLOR_VEHICULO) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $ordenId,
            $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200),
            $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20),
            $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100),
            $trunc($data['OC_VEHICULO_MODELO'] ?? null, 100),
            $trunc($data['OC_VEHICULO_ANIO_MODELO'] ?? null, 10),
            $trunc($data['OC_VEHICULO_CHASIS'] ?? null, 50),
            $trunc($data['OC_VEHICULO_COLOR'] ?? null, 50)
        ]);

        // Autorización Datos Personales
        $sql = "INSERT INTO SIST_AUTORIZACION_DATOS_PERSONALES (ADP_DOCUMENTO_VENTA_ID, ADP_NOMBRE_AUTORIZACION, ADP_DNI_AUTORIZACION) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $ordenId,
            $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200),
            $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20)
        ]);

        // Carta Conocimiento Aceptación
        $sql = "INSERT INTO SIST_CARTA_CONOCIMIENTO_ACEPTACION (CCA_DOCUMENTO_VENTA_ID, CCA_CLIENTE_NOMBRE_COMPLETO, CCA_CLIENTE_DOCUMENTO, CCA_VEHICULO_MARCA, CCA_VEHICULO_MODELO, CCA_VEHICULO_ANIO, CCA_VEHICULO_VIN) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $ordenId,
            $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200),
            $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 50),
            $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100),
            $trunc($data['OC_VEHICULO_MODELO'] ?? null, 100),
            $trunc($data['OC_VEHICULO_ANIO_MODELO'] ?? null, 10),
            $trunc($data['OC_VEHICULO_CHASIS'] ?? null, 50)
        ]);

        // Carta Recepción
        $sql = "INSERT INTO SIST_CARTA_RECEPCION (CR_DOCUMENTO_VENTA_ID, CR_CLIENTE_NOMBRE, CR_CLIENTE_DNI, CR_VEHICULO_MARCA, CR_VEHICULO_MODELO) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $ordenId,
            $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200),
            $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20),
            $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100),
            $trunc($data['OC_VEHICULO_MODELO'] ?? null, 100)
        ]);

        // Carta Características
        $sql = "INSERT INTO SIST_CARTA_CARACTERISTICAS (CC_DOCUMENTO_VENTA_ID, CC_CLIENTE_NOMBRE, CC_CLIENTE_DNI, CC_VEHICULO_MARCA, CC_VEHICULO_MODELO, CC_VEHICULO_ANIO_MODELO, CC_VEHICULO_CHASIS, CC_VEHICULO_MOTOR, CC_VEHICULO_COLOR, CC_PROPIETARIO_TARJETA) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $ordenId,
            $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200),
            $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20),
            $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100),
            $trunc($data['OC_VEHICULO_MODELO'] ?? null, 100),
            $trunc($data['OC_VEHICULO_ANIO_MODELO'] ?? null, 10),
            $trunc($data['OC_VEHICULO_CHASIS'] ?? null, 50),
            $trunc($data['OC_VEHICULO_MOTOR'] ?? null, 50),
            $trunc($data['OC_VEHICULO_COLOR'] ?? null, 50),
            $trunc($data['OC_PROPIETARIO_NOMBRE'] ?? null, 200)
        ]);

        // Carta Felicitaciones
        $sql = "INSERT INTO SIST_CARTA_FELICITACIONES (CF_DOCUMENTO_VENTA_ID, CF_CLIENTE_NOMBRE, CF_VEHICULO_MARCA, CF_ASESOR_NOMBRE) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $ordenId,
            $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200),
            $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100),
            $trunc($data['OC_ASESOR_VENTA'] ?? null, 200)
        ]);

        // Carta Obsequios
        $sql = "INSERT INTO SIST_CARTA_OBSEQUIOS (CO_DOCUMENTO_VENTA_ID, CO_CLIENTE_NOMBRE, CO_CLIENTE_DNI, CO_VEHICULO_MARCA, CO_VEHICULO_MODELO) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $ordenId,
            $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200),
            $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20),
            $trunc($data['OC_VEHICULO_MARCA'] ?? null, 100),
            $trunc($data['OC_VEHICULO_MODELO'] ?? null, 100)
        ]);

        // Política Protección Datos
        $sql = "INSERT INTO SIST_POLITICA_PROTECCION_DATOS (PPD_DOCUMENTO_VENTA_ID, PPD_CLIENTE_NOMBRE, PPD_CLIENTE_DNI) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $ordenId,
            $trunc($data['OC_COMPRADOR_NOMBRE'] ?? null, 200),
            $trunc($data['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? null, 20)
        ]);
    }

    public function getOrdenCompra($id) {
        $sql = "SELECT * FROM SIST_ORDEN_COMPRA WHERE OC_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDocumentData($documentId, $ordenId) {
        $tableMap = [
            'acta-conocimiento-conformidad' => ['table' => 'SIST_ACTA_CONOCIMIENTO_CONFORMIDAD', 'field' => 'ACC_DOCUMENTO_VENTA_ID'],
            'actorizacion-datos-personales' => ['table' => 'SIST_AUTORIZACION_DATOS_PERSONALES', 'field' => 'ADP_DOCUMENTO_VENTA_ID'],
            'carta_conocimiento_aceptacion' => ['table' => 'SIST_CARTA_CONOCIMIENTO_ACEPTACION', 'field' => 'CCA_DOCUMENTO_VENTA_ID'],
            'carta_recepcion' => ['table' => 'SIST_CARTA_RECEPCION', 'field' => 'CR_DOCUMENTO_VENTA_ID'],
            'carta-caracteristicas' => ['table' => 'SIST_CARTA_CARACTERISTICAS', 'field' => 'CC_DOCUMENTO_VENTA_ID'],
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
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$ordenId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
