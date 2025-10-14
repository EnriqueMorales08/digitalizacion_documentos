<?php
// Script de prueba para verificar conexión a RSFACCAR12 y obtener asesores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de Conexión a RSFACCAR12</h2>";

// Probar conexión directa
$connectionInfo = array(
    "Database" => "RSFACCAR12",
    "UID" => "sa",
    "PWD" => "sistemasi",
    "CharacterSet" => "UTF-8"
);

echo "<p>Intentando conectar a: 192.168.10.10 - Base de datos: RSFACCAR12</p>";

$conn = sqlsrv_connect("192.168.10.10", $connectionInfo);

if ($conn === false) {
    echo "<p style='color:red;'><strong>Error de conexión:</strong></p>";
    echo "<pre>";
    print_r(sqlsrv_errors());
    echo "</pre>";
    die();
}

echo "<p style='color:green;'><strong>✓ Conexión exitosa a RSFACCAR12</strong></p>";

// Probar query
$sql = "SELECT VE_CCODIGO, VE_CNOMBRE FROM FT0002VEND WHERE VE_CTIPVEN != 'I' ORDER BY VE_CNOMBRE";
echo "<p>Ejecutando query: <code>$sql</code></p>";

$result = sqlsrv_query($conn, $sql);

if (!$result) {
    echo "<p style='color:red;'><strong>Error en query:</strong></p>";
    echo "<pre>";
    print_r(sqlsrv_errors());
    echo "</pre>";
    sqlsrv_close($conn);
    die();
}

echo "<p style='color:green;'><strong>✓ Query ejecutado exitosamente</strong></p>";

// Mostrar resultados
echo "<h3>Asesores encontrados:</h3>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Código</th><th>Nombre</th></tr>";

$count = 0;
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $codigo = $row['VE_CCODIGO'] ?? $row['ve_ccodigo'] ?? 'N/A';
    $nombre = $row['VE_CNOMBRE'] ?? $row['ve_cnombre'] ?? 'N/A';
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($codigo) . "</td>";
    echo "<td>" . htmlspecialchars($nombre) . "</td>";
    echo "</tr>";
    $count++;
}

echo "</table>";
echo "<p><strong>Total de asesores: $count</strong></p>";

sqlsrv_close($conn);

echo "<hr>";
echo "<h3>Test usando el modelo Document</h3>";

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Document.php';

$document = new Document();
$asesores = $document->getAsesores();

echo "<p>Asesores obtenidos del modelo: <strong>" . count($asesores) . "</strong></p>";

if (count($asesores) > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Código</th><th>Nombre</th></tr>";
    foreach ($asesores as $asesor) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($asesor['codigo']) . "</td>";
        echo "<td>" . htmlspecialchars($asesor['nombre']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:orange;'>No se obtuvieron asesores del modelo.</p>";
}

echo "<p><a href='/digitalizacion-documentos/documents/show?id=orden-compra'>Ir a Orden de Compra</a></p>";
?>
