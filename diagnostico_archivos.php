<?php
// Script de diagnóstico para verificar rutas de archivos en la BD
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Consultar las últimas 10 órdenes de compra con sus archivos
$sql = "SELECT TOP 10 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_FECHA_CREACION,
    OC_ARCHIVO_DNI,
    OC_ARCHIVO_VOUCHER,
    OC_ARCHIVO_ABONO1,
    OC_ARCHIVO_ABONO2,
    OC_ARCHIVO_OTROS_1
FROM SIST_ORDEN_COMPRA
ORDER BY OC_ID DESC";

$result = sqlsrv_query($conn, $sql);

if (!$result) {
    die("Error en consulta: " . print_r(sqlsrv_errors(), true));
}

echo "<h2>Diagnóstico de Archivos Adjuntos</h2>";
echo "<p>Verificando las últimas 10 órdenes de compra...</p>";
echo "<hr>";

while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    echo "<h3>Orden #{$row['OC_ID']} - Expediente: {$row['OC_NUMERO_EXPEDIENTE']}</h3>";
    echo "<p><strong>Fecha:</strong> " . ($row['OC_FECHA_CREACION'] ? $row['OC_FECHA_CREACION']->format('Y-m-d H:i:s') : 'N/A') . "</p>";
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Campo</th><th>Ruta Guardada</th><th>Archivo Existe?</th></tr>";
    
    $archivos = [
        'OC_ARCHIVO_DNI' => $row['OC_ARCHIVO_DNI'],
        'OC_ARCHIVO_VOUCHER' => $row['OC_ARCHIVO_VOUCHER'],
        'OC_ARCHIVO_ABONO1' => $row['OC_ARCHIVO_ABONO1'],
        'OC_ARCHIVO_ABONO2' => $row['OC_ARCHIVO_ABONO2'],
        'OC_ARCHIVO_OTROS_1' => $row['OC_ARCHIVO_OTROS_1']
    ];
    
    foreach ($archivos as $campo => $ruta) {
        if (!empty($ruta)) {
            // Verificar si el archivo existe
            $rutaCompleta = '';
            $existe = false;
            
            // Intentar diferentes rutas posibles
            if (strpos($ruta, '/digitalizacion-documentos/uploads/') === 0) {
                // Ruta absoluta web
                $rutaCompleta = __DIR__ . str_replace('/digitalizacion-documentos', '', $ruta);
                $existe = file_exists($rutaCompleta);
            } elseif (strpos($ruta, '../uploads/') === 0) {
                // Ruta relativa
                $rutaCompleta = __DIR__ . '/' . str_replace('../', '', $ruta);
                $existe = file_exists($rutaCompleta);
            } elseif (strpos($ruta, 'uploads/') === 0) {
                // Ruta relativa sin ../
                $rutaCompleta = __DIR__ . '/' . $ruta;
                $existe = file_exists($rutaCompleta);
            }
            
            $color = $existe ? 'green' : 'red';
            $status = $existe ? '✅ SÍ' : '❌ NO';
            
            echo "<tr>";
            echo "<td><strong>{$campo}</strong></td>";
            echo "<td style='font-size: 11px;'>{$ruta}</td>";
            echo "<td style='color: {$color}; font-weight: bold;'>{$status}</td>";
            echo "</tr>";
            
            if (!$existe && !empty($rutaCompleta)) {
                echo "<tr><td colspan='3' style='background: #fff3cd; font-size: 11px;'>Ruta completa buscada: {$rutaCompleta}</td></tr>";
            }
        }
    }
    
    echo "</table>";
    echo "<hr>";
}

sqlsrv_close($conn);

// Listar archivos físicos en el directorio uploads
echo "<h3>Archivos físicos en /uploads/</h3>";
$uploadDir = __DIR__ . '/uploads/';
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    $files = array_diff($files, ['.', '..']);
    
    if (count($files) > 0) {
        echo "<ul>";
        foreach ($files as $file) {
            $filepath = $uploadDir . $file;
            $size = filesize($filepath);
            $date = date('Y-m-d H:i:s', filemtime($filepath));
            echo "<li><strong>{$file}</strong> - {$size} bytes - {$date}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ El directorio /uploads/ está VACÍO</p>";
    }
} else {
    echo "<p style='color: red;'>❌ El directorio /uploads/ NO EXISTE</p>";
}
?>
