<?php
// Test de la API real - Muestra todos los datos

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 Datos Completos de la API Google Sheets</h1>";
echo "<hr>";

$apiUrl = 'https://opensheet.elk.sh/155IT8et2XYhMK6bkr7OJBtCziHS6X9Ia_6Q99Gm0WAk/Hoja%201';

echo "<h2>URL de la API:</h2>";
echo "<p><code>$apiUrl</code></p>";

echo "<h2>Intentando obtener datos...</h2>";

// Método simple como en buscarVehiculoPorChasis
$jsonContent = @file_get_contents($apiUrl);

if ($jsonContent === false) {
    echo "<p style='color: red;'>❌ ERROR: No se pudo acceder a la API</p>";
    echo "<p>Posibles causas:</p>";
    echo "<ul>";
    echo "<li>Servidor remoto no puede acceder a URLs externas</li>";
    echo "<li>Firewall bloqueando la conexión</li>";
    echo "<li>URL incorrecta</li>";
    echo "</ul>";
    exit;
}

echo "<p style='color: green;'>✅ Datos obtenidos exitosamente</p>";

$centros = json_decode($jsonContent, true);

if (!$centros || !is_array($centros)) {
    echo "<p style='color: red;'>❌ ERROR: JSON inválido</p>";
    echo "<pre>" . htmlspecialchars(substr($jsonContent, 0, 1000)) . "</pre>";
    exit;
}

echo "<h2>📊 Resumen:</h2>";
echo "<ul>";
echo "<li><strong>Total de registros:</strong> " . count($centros) . "</li>";
echo "</ul>";

// Extraer agencias únicas
$agencias = [];
foreach ($centros as $centro) {
    $agencia = $centro['AGENCIA'] ?? '';
    if ($agencia && !in_array($agencia, $agencias)) {
        $agencias[] = $agencia;
    }
}
sort($agencias);

echo "<li><strong>Agencias encontradas:</strong> " . count($agencias) . "</li>";
echo "</ul>";

echo "<h3>Lista de Agencias:</h3>";
echo "<ul>";
foreach ($agencias as $agencia) {
    $registros = count(array_filter($centros, function($c) { return ($c['AGENCIA'] ?? '') === $agencia; }));
    echo "<li><strong>$agencia</strong> ($registros registros)</li>";
}
echo "</ul>";

echo "<hr>";
echo "<h2>📋 TODOS los registros:</h2>";

foreach ($centros as $i => $centro) {
    echo "<h3>Registro #" . ($i + 1) . "</h3>";
    echo "<pre>" . json_encode($centro, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    echo "<hr>";
}

echo "<h2>📄 JSON Completo para Copiar:</h2>";
echo "<p>Copia este JSON y pégalo en el archivo <code>centros_costo_backup.json</code>:</p>";
echo "<textarea style='width: 100%; height: 300px; font-family: monospace;'>";
echo json_encode($centros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "</textarea>";
