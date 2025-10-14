<?php
// Test directo del endpoint de mantenimiento
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test del Endpoint de Mantenimiento</h2>";

// Simular la llamada al endpoint
$_GET['marca'] = 'CHERY';
$_GET['modelo'] = 'PLATEAU';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Document.php';

$document = new Document();
$datos = $document->getDatosMantenimiento($_GET['marca'], $_GET['modelo']);

echo "<h3>Parámetros de búsqueda:</h3>";
echo "<ul>";
echo "<li><strong>Marca:</strong> " . htmlspecialchars($_GET['marca']) . "</li>";
echo "<li><strong>Modelo:</strong> " . htmlspecialchars($_GET['modelo']) . "</li>";
echo "</ul>";

echo "<h3>Resultado:</h3>";
if ($datos) {
    echo "<pre style='background:#e8f5e9; padding:15px; border:1px solid #4caf50;'>";
    echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "</pre>";
    
    echo "<h3>Valores formateados:</h3>";
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr><th>Campo</th><th>Valor</th></tr>";
    echo "<tr><td><strong>GARANTIA</strong></td><td>" . htmlspecialchars($datos['GARANTIA']) . "</td></tr>";
    echo "<tr><td><strong>PRIMER_INGRESO</strong></td><td>" . htmlspecialchars($datos['PRIMER_INGRESO']) . "</td></tr>";
    echo "<tr><td><strong>PERIODICIDAD</strong></td><td>" . htmlspecialchars($datos['PERIODICIDAD']) . "</td></tr>";
    echo "</table>";
} else {
    echo "<p style='color:red; background:#ffebee; padding:15px; border:1px solid #f44336;'>";
    echo "❌ No se encontraron datos para esta marca/modelo";
    echo "</p>";
}

echo "<hr>";
echo "<h3>Revisar logs de error de PHP</h3>";
echo "<p>Los logs de depuración se escriben en el error_log de PHP. Revisa:</p>";
echo "<ul>";
echo "<li>C:\\xampp\\apache\\logs\\error.log</li>";
echo "<li>O el archivo de log configurado en tu php.ini</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>Pruebas adicionales:</h3>";
echo "<ul>";
echo "<li><a href='?marca=CHERY&modelo=PLATEAU'>CHERY PLATEAU</a></li>";
echo "<li><a href='?marca=CHERY&modelo=ARRIZO 5'>CHERY ARRIZO 5</a></li>";
echo "<li><a href='?marca=CHERY&modelo=TIGGO 7 PRO'>CHERY TIGGO 7 PRO</a></li>";
echo "<li><a href='?marca=TOYOTA&modelo=COROLLA'>TOYOTA COROLLA</a></li>";
echo "</ul>";

echo "<p><a href='/digitalizacion-documentos/documents/show?id=orden-compra'>← Volver al formulario</a></p>";
?>
