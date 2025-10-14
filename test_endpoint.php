<?php
// Test directo del endpoint de agencias

session_start();

// Simular sesión de usuario logueado para evitar redirección
$_SESSION['usuario_logueado'] = true;
$_SESSION['usuario'] = 'test';

require_once __DIR__ . '/app/models/Document.php';

header('Content-Type: application/json; charset=utf-8');

echo "=== TEST DEL ENDPOINT DE AGENCIAS ===\n\n";

try {
    $documentModel = new Document();
    
    echo "1. Obteniendo centros de costo...\n";
    $centros = $documentModel->getCentrosCosto();
    echo "   Total de centros: " . count($centros) . "\n\n";
    
    if (count($centros) === 0) {
        echo "   ❌ ERROR: No se obtuvieron centros de costo\n";
        echo "   Verifica que la API de Google Sheets esté funcionando\n";
        exit;
    }
    
    echo "   ✓ Centros obtenidos correctamente\n\n";
    
    echo "2. Extrayendo agencias...\n";
    $agencias = $documentModel->getAgencias();
    echo "   Total de agencias: " . count($agencias) . "\n\n";
    
    if (count($agencias) === 0) {
        echo "   ❌ ERROR: No se extrajeron agencias\n";
        exit;
    }
    
    echo "   ✓ Agencias extraídas correctamente\n\n";
    
    echo "=== LISTA DE AGENCIAS ===\n";
    foreach ($agencias as $agencia) {
        echo "   - $agencia\n";
    }
    
    echo "\n\n=== JSON PARA EL FRONTEND ===\n";
    echo json_encode($agencias, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    echo "\n\n=== TEST DE NOMBRES POR AGENCIA ===\n";
    if (count($agencias) > 0) {
        $primeraAgencia = $agencias[0];
        echo "Agencia seleccionada: $primeraAgencia\n\n";
        
        $nombres = $documentModel->getNombresPorAgencia($primeraAgencia);
        echo "Total de nombres: " . count($nombres) . "\n";
        foreach ($nombres as $nombre) {
            echo "   - $nombre\n";
        }
    }
    
    echo "\n\n✅ TODOS LOS TESTS PASARON CORRECTAMENTE\n";
    
} catch (Exception $e) {
    echo "❌ EXCEPCIÓN: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
