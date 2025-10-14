<?php
// Test de centros de costo para servidor remoto

session_start();

// Simular sesión de usuario logueado
$_SESSION['usuario_logueado'] = true;

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🧪 Test Centros de Costo - Servidor Remoto</h1>";
echo "<hr>";

require_once __DIR__ . '/app/models/Document.php';

try {
    $documentModel = new Document();

    echo "<h2>1. Probando getCentrosCosto()</h2>";
    $centros = $documentModel->getCentrosCosto();
    echo "<p><strong>Resultado:</strong> " . count($centros) . " centros obtenidos</p>";

    if (count($centros) === 0) {
        echo "<p style='color: red;'>❌ ERROR: No se obtuvieron centros</p>";
        echo "<p>El problema está en getCentrosCosto(). Revisa los logs del servidor.</p>";
        exit;
    }

    echo "<p style='color: green;'>✅ Éxito: Centros obtenidos</p>";

    echo "<h2>2. Estructura del primer centro</h2>";
    echo "<pre>" . print_r($centros[0], true) . "</pre>";

    echo "<h2>3. Probando getAgencias()</h2>";
    $agencias = $documentModel->getAgencias();
    echo "<p><strong>Agencias encontradas:</strong> " . count($agencias) . "</p>";
    echo "<pre>" . print_r($agencias, true) . "</pre>";

    if (count($agencias) === 0) {
        echo "<p style='color: red;'>❌ ERROR: No se pudieron extraer agencias</p>";
        exit;
    }

    echo "<h2>4. Probando getNombresPorAgencia()</h2>";
    $primeraAgencia = $agencias[0];
    echo "<p>Agencia seleccionada: <strong>$primeraAgencia</strong></p>";

    $nombres = $documentModel->getNombresPorAgencia($primeraAgencia);
    echo "<p>Nombres encontrados: " . count($nombres) . "</p>";
    echo "<pre>" . print_r($nombres, true) . "</pre>";

    if (count($nombres) === 0) {
        echo "<p style='color: orange;'>⚠️ ADVERTENCIA: No hay nombres para esta agencia</p>";
    }

    echo "<h2>5. Probando getCentrosCostoPorNombre()</h2>";
    if (count($nombres) > 0) {
        $primerNombre = $nombres[0];
        echo "<p>Nombre seleccionado: <strong>$primerNombre</strong></p>";

        $centrosFiltrados = $documentModel->getCentrosCostoPorNombre($primeraAgencia, $primerNombre);
        echo "<p>Centros encontrados: " . count($centrosFiltrados) . "</p>";
        echo "<pre>" . print_r($centrosFiltrados, true) . "</pre>";
    }

    echo "<hr>";
    echo "<h2>✅ TEST COMPLETADO</h2>";

    if (count($agencias) > 0) {
        echo "<div style='background: #e8f5e9; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>🎉 ¡Los selects deberían funcionar!</h3>";
        echo "<p>Ahora prueba la orden de compra:</p>";
        echo "<p><strong>URL:</strong> /digitalizacion-documentos/documents/show?id=orden-compra</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #ffeaea; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>❌ Aún hay problemas</h3>";
        echo "<p>Los datos no se están procesando correctamente.</p>";
        echo "<p>Revisa los logs del servidor para más detalles.</p>";
        echo "</div>";
    }

} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ EXCEPCIÓN</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . " línea " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
