<?php
// Test de API de Google Sheets

header('Content-Type: application/json; charset=utf-8');

$apiUrl = 'https://opensheet.elk.sh/155IT8et2XYhMK6bkr7OJBtCziHS6X9Ia_6Q99Gm0WAk/Hoja%201';

echo "=== TEST DE API DE GOOGLE SHEETS ===\n\n";
echo "URL: $apiUrl\n\n";

try {
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);
    
    echo "Intentando obtener datos...\n";
    $jsonContent = @file_get_contents($apiUrl, false, $context);
    
    if ($jsonContent === false) {
        echo "ERROR: No se pudo obtener datos de la API\n";
        echo "Posibles causas:\n";
        echo "- El servidor no tiene acceso a internet\n";
        echo "- La URL está bloqueada\n";
        echo "- Problema de firewall\n";
        exit;
    }
    
    echo "✓ Datos obtenidos exitosamente\n\n";
    
    $centros = json_decode($jsonContent, true);
    
    if (!$centros) {
        echo "ERROR: No se pudo decodificar JSON\n";
        echo "Contenido recibido:\n";
        echo substr($jsonContent, 0, 500) . "...\n";
        exit;
    }
    
    echo "✓ JSON decodificado exitosamente\n\n";
    echo "Total de registros: " . count($centros) . "\n\n";
    
    // Mostrar primeros 3 registros
    echo "=== PRIMEROS 3 REGISTROS ===\n";
    for ($i = 0; $i < min(3, count($centros)); $i++) {
        echo "\nRegistro " . ($i + 1) . ":\n";
        print_r($centros[$i]);
    }
    
    // Extraer agencias únicas
    echo "\n\n=== AGENCIAS ÚNICAS ===\n";
    $agencias = array_unique(array_column($centros, 'AGENCIA'));
    sort($agencias);
    print_r($agencias);
    
    echo "\n\n=== JSON DE AGENCIAS (para el frontend) ===\n";
    echo json_encode($agencias, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo "EXCEPCIÓN: " . $e->getMessage() . "\n";
}
