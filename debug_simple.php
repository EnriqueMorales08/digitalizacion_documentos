<?php
// Debug simple - Ver exactamente qué devuelve la API

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 Debug Simple - API Google Sheets</h1>";
echo "<hr>";

$apiUrl = 'https://opensheet.elk.sh/155IT8et2XYhMK6bkr7OJBtCziHS6X9Ia_6Q99Gm0WAk/Hoja%201';

echo "<h2>1. Llamando a la API...</h2>";
echo "<p><strong>URL:</strong> $apiUrl</p>";

$jsonContent = @file_get_contents($apiUrl);

if ($jsonContent === false) {
    echo "<p style='color: red;'>❌ ERROR: No se pudo obtener datos</p>";
    exit;
}

echo "<p style='color: green;'>✅ Datos obtenidos: " . strlen($jsonContent) . " bytes</p>";

echo "<h2>2. Decodificando JSON...</h2>";
$centros = json_decode($jsonContent, true);

if (!$centros) {
    echo "<p style='color: red;'>❌ ERROR: No se pudo decodificar JSON</p>";
    echo "<pre>" . htmlspecialchars(substr($jsonContent, 0, 1000)) . "</pre>";
    exit;
}

echo "<p style='color: green;'>✅ JSON decodificado: " . count($centros) . " registros</p>";

echo "<h2>3. Estructura del primer registro:</h2>";
if (count($centros) > 0) {
    echo "<pre>";
    print_r($centros[0]);
    echo "</pre>";
    
    echo "<h3>Columnas disponibles:</h3>";
    echo "<ul>";
    foreach (array_keys($centros[0]) as $key) {
        echo "<li><strong>$key</strong></li>";
    }
    echo "</ul>";
}

echo "<h2>4. Extrayendo agencias...</h2>";

// Método 1: array_column
$agencias1 = array_unique(array_column($centros, 'AGENCIA'));
echo "<p><strong>Método 1 (array_column):</strong> " . count($agencias1) . " agencias</p>";
echo "<pre>" . print_r($agencias1, true) . "</pre>";

// Método 2: foreach manual
$agencias2 = [];
foreach ($centros as $centro) {
    if (isset($centro['AGENCIA']) && !in_array($centro['AGENCIA'], $agencias2)) {
        $agencias2[] = $centro['AGENCIA'];
    }
}
echo "<p><strong>Método 2 (foreach):</strong> " . count($agencias2) . " agencias</p>";
echo "<pre>" . print_r($agencias2, true) . "</pre>";

echo "<h2>5. Probando getNombresPorAgencia...</h2>";
if (count($agencias2) > 0) {
    $primeraAgencia = $agencias2[0];
    echo "<p>Agencia seleccionada: <strong>$primeraAgencia</strong></p>";
    
    $nombres = [];
    foreach ($centros as $centro) {
        if ($centro['AGENCIA'] === $primeraAgencia && !in_array($centro['NOMBRE'], $nombres)) {
            $nombres[] = $centro['NOMBRE'];
        }
    }
    
    echo "<p>Nombres encontrados: " . count($nombres) . "</p>";
    echo "<ul>";
    foreach ($nombres as $nombre) {
        echo "<li>$nombre</li>";
    }
    echo "</ul>";
}

echo "<h2>6. Probando getCentrosCostoPorNombre...</h2>";
if (count($nombres) > 0) {
    $primerNombre = $nombres[0];
    echo "<p>Nombre seleccionado: <strong>$primerNombre</strong></p>";
    
    $centrosFiltrados = [];
    foreach ($centros as $centro) {
        if ($centro['AGENCIA'] === $primeraAgencia && $centro['NOMBRE'] === $primerNombre) {
            $centrosFiltrados[] = [
                'CENTRO_COSTO' => $centro['CENTRO DE COSTO'],
                'NOMBRE_CC' => $centro['NOMBRE CC'],
                'EMAIL' => $centro['EMAIL']
            ];
        }
    }
    
    echo "<p>Centros encontrados: " . count($centrosFiltrados) . "</p>";
    echo "<pre>" . print_r($centrosFiltrados, true) . "</pre>";
}

echo "<hr>";
echo "<h2>✅ Debug completado</h2>";
