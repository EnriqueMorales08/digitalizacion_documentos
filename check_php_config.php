<?php
// Verificar configuración de PHP

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔧 Verificación de Configuración PHP</h1>";
echo "<hr>";

echo "<h2>1. allow_url_fopen</h2>";
$allow_url_fopen = ini_get('allow_url_fopen');
if ($allow_url_fopen) {
    echo "<p style='color: green;'>✅ <strong>Habilitado</strong></p>";
} else {
    echo "<p style='color: red;'>❌ <strong>Deshabilitado</strong> - Este es el problema</p>";
    echo "<p><strong>Solución:</strong></p>";
    echo "<ol>";
    echo "<li>Abre: <code>C:\\xampp\\php\\php.ini</code></li>";
    echo "<li>Busca: <code>allow_url_fopen</code></li>";
    echo "<li>Cambia a: <code>allow_url_fopen = On</code></li>";
    echo "<li>Reinicia Apache</li>";
    echo "</ol>";
}

echo "<h2>2. Extensiones cURL</h2>";
if (function_exists('curl_version')) {
    echo "<p style='color: green;'>✅ <strong>cURL disponible</strong></p>";
    $curl_version = curl_version();
    echo "<p>Versión: " . $curl_version['version'] . "</p>";
} else {
    echo "<p style='color: orange;'>⚠️ <strong>cURL no disponible</strong></p>";
}

echo "<h2>3. OpenSSL</h2>";
if (extension_loaded('openssl')) {
    echo "<p style='color: green;'>✅ <strong>OpenSSL habilitado</strong></p>";
} else {
    echo "<p style='color: orange;'>⚠️ <strong>OpenSSL no habilitado</strong></p>";
}

echo "<h2>4. Test de conexión externa</h2>";
echo "<p>Intentando conectar a Google...</p>";

$test_url = 'https://www.google.com';
$result = @file_get_contents($test_url);

if ($result !== false) {
    echo "<p style='color: green;'>✅ <strong>Conexión exitosa</strong></p>";
} else {
    echo "<p style='color: red;'>❌ <strong>No se puede conectar a URLs externas</strong></p>";
}

echo "<h2>5. Información de PHP</h2>";
echo "<p><strong>Versión PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Archivo php.ini:</strong> " . php_ini_loaded_file() . "</p>";

echo "<hr>";
echo "<h2>📋 Resumen</h2>";

if (!$allow_url_fopen) {
    echo "<div style='background: #fee; padding: 20px; border-left: 4px solid red;'>";
    echo "<h3>⚠️ Acción Requerida</h3>";
    echo "<p>Debes habilitar <code>allow_url_fopen</code> en php.ini</p>";
    echo "<p><strong>Pasos:</strong></p>";
    echo "<ol>";
    echo "<li>Cierra XAMPP</li>";
    echo "<li>Abre: <code>C:\\xampp\\php\\php.ini</code></li>";
    echo "<li>Busca (Ctrl+F): <code>allow_url_fopen</code></li>";
    echo "<li>Cambia de <code>Off</code> a <code>On</code></li>";
    echo "<li>Guarda el archivo</li>";
    echo "<li>Abre XAMPP y reinicia Apache</li>";
    echo "<li>Recarga esta página</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background: #efe; padding: 20px; border-left: 4px solid green;'>";
    echo "<h3>✅ Configuración Correcta</h3>";
    echo "<p>PHP puede acceder a URLs externas.</p>";
    echo "<p>Si aún no funciona, el problema puede ser:</p>";
    echo "<ul>";
    echo "<li>Firewall bloqueando la conexión</li>";
    echo "<li>Proxy corporativo</li>";
    echo "<li>Antivirus bloqueando PHP</li>";
    echo "</ul>";
    echo "</div>";
}
