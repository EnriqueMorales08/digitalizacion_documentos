<?php
// Test específico para el chasis KIA SOLUTO
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Document.php';

$document = new Document();

echo "<h2>Test para Chasis KIA SOLUTO</h2>";

$chasis = 'LJDOAA29AT0341067';

echo "<h3>1. Buscar vehículo por chasis</h3>";
echo "<p><strong>Chasis:</strong> $chasis</p>";

$vehiculo = $document->buscarVehiculoPorChasis($chasis);

if ($vehiculo) {
    echo "<div style='background:#e8f5e9; padding:15px; border:1px solid #4caf50; margin:10px 0;'>";
    echo "<h4>✅ Vehículo encontrado</h4>";
    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    echo "<tr><th>Campo</th><th>Valor</th></tr>";
    foreach ($vehiculo as $key => $value) {
        echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
    }
    echo "</table>";
    echo "</div>";
    
    $marca = $vehiculo['MARCA'] ?? '';
    $modelo = $vehiculo['MODELO'] ?? '';
    
    echo "<h3>2. Buscar datos de mantenimiento</h3>";
    echo "<p><strong>Marca:</strong> '$marca'</p>";
    echo "<p><strong>Modelo:</strong> '$modelo'</p>";
    
    $datosMantenimiento = $document->getDatosMantenimiento($marca, $modelo);
    
    if ($datosMantenimiento) {
        echo "<div style='background:#e8f5e9; padding:15px; border:1px solid #4caf50; margin:10px 0;'>";
        echo "<h4>✅ Datos de mantenimiento encontrados</h4>";
        echo "<table border='1' cellpadding='8' cellspacing='0'>";
        echo "<tr><th>Campo</th><th>Valor Formateado</th></tr>";
        echo "<tr><td><strong>Período Garantía</strong></td><td>" . htmlspecialchars($datosMantenimiento['GARANTIA']) . "</td></tr>";
        echo "<tr><td><strong>Primer Mantenimiento</strong></td><td>" . htmlspecialchars($datosMantenimiento['PRIMER_INGRESO']) . "</td></tr>";
        echo "<tr><td><strong>Periodicidad</strong></td><td>" . htmlspecialchars($datosMantenimiento['PERIODICIDAD']) . "</td></tr>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<div style='background:#ffebee; padding:15px; border:1px solid #f44336; margin:10px 0;'>";
        echo "<h4>❌ No se encontraron datos de mantenimiento</h4>";
        echo "<p>Verifica que exista una entrada en el JSON para:</p>";
        echo "<ul>";
        echo "<li><strong>MARCA:</strong> '$marca'</li>";
        echo "<li><strong>MODELO:</strong> '$modelo'</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    echo "<h3>3. Prueba del endpoint AJAX</h3>";
    $urlAjax = "/digitalizacion-documentos/documents/buscar-datos-mantenimiento?marca=" . urlencode($marca) . "&modelo=" . urlencode($modelo);
    echo "<p><a href='$urlAjax' target='_blank'>Probar endpoint: $urlAjax</a></p>";
    
} else {
    echo "<div style='background:#ffebee; padding:15px; border:1px solid #f44336;'>";
    echo "<h4>❌ No se encontró el vehículo con ese chasis</h4>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>Revisar logs</h3>";
echo "<p>Revisa el archivo de logs: <code>C:\\xampp\\apache\\logs\\error.log</code></p>";
echo "<p>Busca las líneas que empiezan con:</p>";
echo "<ul>";
echo "<li>Buscando archivo JSON en:</li>";
echo "<li>Buscando: MARCA=</li>";
echo "<li>Comparando con: MARCA=</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='/digitalizacion-documentos/documents/show?id=orden-compra'>← Volver al formulario</a></p>";
?>
