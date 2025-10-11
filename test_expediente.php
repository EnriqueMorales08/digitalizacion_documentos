<?php
/**
 * Script de prueba para el sistema de número de expediente
 * Ejecutar desde: http://localhost/digitalizacion-documentos/test_expediente.php
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Document.php';

echo "<html><head><meta charset='UTF-8'><title>Test Sistema de Expedientes</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".success{color:green;}.error{color:red;}.info{color:blue;}";
echo "h2{border-bottom:2px solid #333;padding-bottom:10px;}";
echo "pre{background:#fff;padding:10px;border:1px solid #ddd;}</style></head><body>";

echo "<h1>🧪 Test del Sistema de Número de Expediente</h1>";

$document = new Document();

// Test 1: Verificar conexión a BD
echo "<h2>1️⃣ Test de Conexión a Base de Datos</h2>";
try {
    $db = new Database();
    $conn = $db->getConnection();
    if ($conn) {
        echo "<p class='success'>✅ Conexión exitosa a la base de datos</p>";
    } else {
        echo "<p class='error'>❌ Error: No se pudo conectar a la base de datos</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Test 2: Verificar que existe el procedimiento almacenado
echo "<h2>2️⃣ Test de Procedimiento Almacenado</h2>";
$sql = "SELECT name FROM sys.procedures WHERE name = 'sp_GenerarNumeroExpediente'";
$result = sqlsrv_query($conn, $sql);
if ($result && sqlsrv_fetch_array($result)) {
    echo "<p class='success'>✅ Procedimiento sp_GenerarNumeroExpediente existe</p>";
} else {
    echo "<p class='error'>❌ Error: Procedimiento sp_GenerarNumeroExpediente NO existe</p>";
    echo "<p class='info'>💡 Ejecuta el script: database/add_expediente_system.sql</p>";
}

// Test 3: Verificar que existe la función
echo "<h2>3️⃣ Test de Función</h2>";
$sql = "SELECT name FROM sys.objects WHERE name = 'fn_GenerarNumeroExpediente' AND type = 'FN'";
$result = sqlsrv_query($conn, $sql);
if ($result && sqlsrv_fetch_array($result)) {
    echo "<p class='success'>✅ Función fn_GenerarNumeroExpediente existe</p>";
} else {
    echo "<p class='error'>❌ Error: Función fn_GenerarNumeroExpediente NO existe</p>";
}

// Test 4: Verificar campo OC_NUMERO_EXPEDIENTE
echo "<h2>4️⃣ Test de Campo en Tabla</h2>";
$sql = "SELECT name FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_NUMERO_EXPEDIENTE'";
$result = sqlsrv_query($conn, $sql);
if ($result && sqlsrv_fetch_array($result)) {
    echo "<p class='success'>✅ Campo OC_NUMERO_EXPEDIENTE existe en SIST_ORDEN_COMPRA</p>";
} else {
    echo "<p class='error'>❌ Error: Campo OC_NUMERO_EXPEDIENTE NO existe</p>";
}

// Test 5: Verificar índices
echo "<h2>5️⃣ Test de Índices</h2>";
$sql = "SELECT name FROM sys.indexes WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name IN ('UQ_OC_NUMERO_EXPEDIENTE', 'IDX_OC_NUMERO_EXPEDIENTE')";
$result = sqlsrv_query($conn, $sql);
$indices = [];
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $indices[] = $row['name'];
}
if (count($indices) >= 2) {
    echo "<p class='success'>✅ Índices creados correctamente: " . implode(', ', $indices) . "</p>";
} else {
    echo "<p class='error'>❌ Faltan índices. Encontrados: " . (count($indices) > 0 ? implode(', ', $indices) : 'ninguno') . "</p>";
}

// Test 6: Generar número de expediente de prueba
echo "<h2>6️⃣ Test de Generación de Número</h2>";
try {
    $sql = "DECLARE @NumeroExpediente NVARCHAR(50); EXEC sp_GenerarNumeroExpediente @NumeroExpediente OUTPUT; SELECT @NumeroExpediente AS NumeroExpediente";
    $result = sqlsrv_query($conn, $sql);
    
    if ($result) {
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        if ($row && isset($row['NumeroExpediente'])) {
            $numeroGenerado = $row['NumeroExpediente'];
            echo "<p class='success'>✅ Número de expediente generado exitosamente: <strong>{$numeroGenerado}</strong></p>";
            
            // Validar formato
            if (preg_match('/^\d{10}$/', $numeroGenerado)) {
                echo "<p class='success'>✅ Formato correcto (10 dígitos)</p>";
                
                $anioMes = substr($numeroGenerado, 0, 6);
                $secuencial = substr($numeroGenerado, 6, 4);
                echo "<p class='info'>📅 Año-Mes: {$anioMes}</p>";
                echo "<p class='info'>🔢 Secuencial: {$secuencial}</p>";
            } else {
                echo "<p class='error'>❌ Formato incorrecto. Esperado: YYYYMM0001</p>";
            }
        } else {
            echo "<p class='error'>❌ No se pudo obtener el número generado</p>";
        }
    } else {
        echo "<p class='error'>❌ Error al ejecutar el procedimiento: " . print_r(sqlsrv_errors(), true) . "</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Excepción: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 7: Listar expedientes existentes
echo "<h2>7️⃣ Test de Listado de Expedientes</h2>";
try {
    $resultado = $document->listarOrdenesCompra(1, 5);
    echo "<p class='info'>📊 Total de expedientes: {$resultado['total']}</p>";
    echo "<p class='info'>📄 Páginas: {$resultado['pages']}</p>";
    
    if (!empty($resultado['data'])) {
        echo "<p class='success'>✅ Expedientes encontrados:</p>";
        echo "<table border='1' cellpadding='10' style='background:white;border-collapse:collapse;width:100%;'>";
        echo "<tr><th>Número Expediente</th><th>Cliente</th><th>DNI</th><th>Vehículo</th><th>Fecha</th></tr>";
        foreach ($resultado['data'] as $orden) {
            $fecha = '';
            if (isset($orden['OC_FECHA_CREACION']) && $orden['OC_FECHA_CREACION'] instanceof DateTime) {
                $fecha = $orden['OC_FECHA_CREACION']->format('d/m/Y H:i');
            }
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($orden['OC_NUMERO_EXPEDIENTE'] ?? 'N/A') . "</strong></td>";
            echo "<td>" . htmlspecialchars($orden['OC_COMPRADOR_NOMBRE'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($orden['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($orden['OC_VEHICULO_MARCA'] ?? '') . " " . htmlspecialchars($orden['OC_VEHICULO_MODELO'] ?? '') . "</td>";
            echo "<td>{$fecha}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='info'>ℹ️ No hay expedientes registrados aún</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error al listar expedientes: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 8: Buscar expediente específico (si existe)
echo "<h2>8️⃣ Test de Búsqueda por Número</h2>";
if (!empty($resultado['data'])) {
    $primerExpediente = $resultado['data'][0]['OC_NUMERO_EXPEDIENTE'];
    try {
        $orden = $document->buscarPorNumeroExpediente($primerExpediente);
        if ($orden) {
            echo "<p class='success'>✅ Expediente encontrado: {$primerExpediente}</p>";
            echo "<pre>" . print_r($orden, true) . "</pre>";
        } else {
            echo "<p class='error'>❌ No se encontró el expediente</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='info'>ℹ️ No hay expedientes para buscar</p>";
}

// Test 9: Verificar rutas
echo "<h2>9️⃣ Test de Rutas del Sistema</h2>";
$rutas = [
    '/digitalizacion-documentos/expedientes' => 'Listar expedientes',
    '/digitalizacion-documentos/expedientes/ver' => 'Ver expediente específico',
    '/digitalizacion-documentos/expedientes/imprimir-todos' => 'Imprimir todos los documentos',
    '/digitalizacion-documentos/expedientes/buscar' => 'API de búsqueda'
];

echo "<ul>";
foreach ($rutas as $ruta => $descripcion) {
    $url = "http://" . $_SERVER['HTTP_HOST'] . $ruta;
    echo "<li><strong>{$descripcion}:</strong> <a href='{$url}' target='_blank'>{$url}</a></li>";
}
echo "</ul>";

// Resumen final
echo "<h2>📋 Resumen de Tests</h2>";
echo "<p class='success'>✅ Si todos los tests pasaron, el sistema está listo para usar</p>";
echo "<p class='info'>📖 Lee el archivo <strong>INSTRUCCIONES_EXPEDIENTES.md</strong> para más información</p>";

echo "<hr>";
echo "<p><a href='/digitalizacion-documentos/documents'>← Volver al Panel de Documentos</a></p>";
echo "<p><a href='/digitalizacion-documentos/expedientes'>→ Ir a Gestión de Expedientes</a></p>";

echo "</body></html>";
?>
