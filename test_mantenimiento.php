<?php
// Script de prueba para verificar el formateo de datos de mantenimiento
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Document.php';

echo "<h2>Test de Datos de Mantenimiento</h2>";

$document = new Document();

// Casos de prueba
$casos = [
    ['CHERY', 'ARRIZO 5'],
    ['CHERY', 'ARRIZO 6'],
    ['CHERY', 'TIGGO 7 PRO'],
    ['TOYOTA', 'COROLLA'],
    ['NISSAN', 'SENTRA'],
    ['MARCA', 'NO EXISTE'] // Caso que no existe
];

echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse;'>";
echo "<tr style='background:#f0f0f0;'>";
echo "<th>Marca</th>";
echo "<th>Modelo</th>";
echo "<th>Garantía (Formateada)</th>";
echo "<th>Primer Ingreso (Formateado)</th>";
echo "<th>Periodicidad (Formateada)</th>";
echo "</tr>";

foreach ($casos as $caso) {
    $marca = $caso[0];
    $modelo = $caso[1];
    
    $datos = $document->getDatosMantenimiento($marca, $modelo);
    
    echo "<tr>";
    echo "<td><strong>" . htmlspecialchars($marca) . "</strong></td>";
    echo "<td><strong>" . htmlspecialchars($modelo) . "</strong></td>";
    
    if ($datos) {
        echo "<td style='color:green;'>" . htmlspecialchars($datos['GARANTIA']) . "</td>";
        echo "<td style='color:green;'>" . htmlspecialchars($datos['PRIMER_INGRESO']) . "</td>";
        echo "<td style='color:green;'>" . htmlspecialchars($datos['PERIODICIDAD']) . "</td>";
    } else {
        echo "<td colspan='3' style='color:red; text-align:center;'>No encontrado</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h3>Ejemplos de Formateo Esperado:</h3>";
echo "<ul>";
echo "<li><strong>Garantía:</strong> \"5 años o 100 mil km\" → \"5 años o 100 mil km, lo que pase primero\"</li>";
echo "<li><strong>Primer Ingreso:</strong> \"5,000 o 6 meses\" → \"5,000 km o 6 meses\"</li>";
echo "<li><strong>Periodicidad:</strong> \"5,000\" → \"cada 5,000 km\"</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>Prueba del Endpoint AJAX</h3>";
echo "<p>Puedes probar el endpoint directamente:</p>";
echo "<ul>";
echo "<li><a href='/digitalizacion-documentos/documents/buscar-datos-mantenimiento?marca=CHERY&modelo=ARRIZO 5' target='_blank'>CHERY ARRIZO 5</a></li>";
echo "<li><a href='/digitalizacion-documentos/documents/buscar-datos-mantenimiento?marca=CHERY&modelo=TIGGO 7 PRO' target='_blank'>CHERY TIGGO 7 PRO</a></li>";
echo "<li><a href='/digitalizacion-documentos/documents/buscar-datos-mantenimiento?marca=TOYOTA&modelo=COROLLA' target='_blank'>TOYOTA COROLLA</a></li>";
echo "</ul>";

echo "<p><a href='/digitalizacion-documentos/documents/show?id=orden-compra'>Ir a Orden de Compra</a></p>";
?>
