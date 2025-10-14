<?php
// Test directo del endpoint de agencias en servidor remoto

session_start();

// Simular sesión de usuario logueado
$_SESSION['usuario_logueado'] = true;

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔗 Test Endpoint Agencias</h1>";
echo "<hr>";

echo "<h2>1. Probando endpoint directo</h2>";
echo "<p><strong>URL:</strong> /digitalizacion-documentos/documents/get-agencias</p>";

// Crear contexto para simular petición POST
$_SERVER['REQUEST_METHOD'] = 'GET';

try {
    // Incluir el controlador
    require_once __DIR__ . '/app/controllers/DocumentController.php';

    $controller = new DocumentController();

    echo "<h3>Llamando a getAgencias()...</h3>";
    ob_start(); // Capturar salida
    $controller->getAgencias();
    $output = ob_get_clean();

    echo "<p><strong>Output del controlador:</strong></p>";
    echo "<pre>$output</pre>";

    if (empty($output)) {
        echo "<p style='color: orange;'>⚠️ El controlador no produjo output (probablemente está enviando JSON directamente)</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en controlador: " . $e->getMessage() . "</p>";
}

echo "<hr>";

echo "<h2>2. Probando modelo directamente</h2>";
try {
    require_once __DIR__ . '/app/models/Document.php';

    $documentModel = new Document();

    echo "<h3>getAgencias():</h3>";
    $agencias = $documentModel->getAgencias();
    echo "<p><strong>Tipo de resultado:</strong> " . gettype($agencias) . "</p>";
    echo "<p><strong>Cantidad:</strong> " . count($agencias) . "</p>";
    echo "<pre>" . print_r($agencias, true) . "</pre>";

    echo "<h3>getCentrosCosto():</h3>";
    $centros = $documentModel->getCentrosCosto();
    echo "<p><strong>Tipo de resultado:</strong> " . gettype($centros) . "</p>";
    echo "<p><strong>Cantidad:</strong> " . count($centros) . "</p>";

    if (count($centros) > 0) {
        echo "<p><strong>Primer centro:</strong></p>";
        echo "<pre>" . print_r($centros[0], true) . "</pre>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en modelo: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>📋 Resumen</h2>";

if (isset($agencias) && count($agencias) > 0) {
    echo "<div style='background: #e8f5e9; padding: 20px; border-radius: 5px;'>";
    echo "<h3>✅ ¡Los datos están funcionando!</h3>";
    echo "<p>Si el frontend no funciona, el problema está en JavaScript.</p>";
    echo "</div>";
} else {
    echo "<div style='background: #ffeaea; padding: 20px; border-radius: 5px;'>";
    echo "<h3>❌ Problema en el backend</h3>";
    echo "<p>Los datos no se están obteniendo correctamente.</p>";
    echo "</div>";
}
