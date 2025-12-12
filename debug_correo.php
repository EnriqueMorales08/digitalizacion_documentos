<?php
/**
 * SCRIPT DE DEBUG PARA VERIFICAR ENVÍO DE CORREOS
 * 
 * USO: http://localhost/digitalizacion-documentos/debug_correo.php?expediente=2025110022
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Document.php';
require_once __DIR__ . '/app/models/ConfirmacionCliente.php';

// Obtener número de expediente
$numeroExpediente = $_GET['expediente'] ?? '';

if (empty($numeroExpediente)) {
    die('❌ ERROR: Debes proporcionar un número de expediente. Ejemplo: ?expediente=2025110022');
}

echo "<h1>🔍 DEBUG - Envío de Correo al Cliente</h1>";
echo "<hr>";

// PASO 1: Verificar que existe la orden
echo "<h2>PASO 1: Verificar Orden de Compra</h2>";
$documentModel = new Document();
$orden = $documentModel->getOrdenCompraPorExpediente($numeroExpediente);

if (!$orden) {
    echo "❌ <strong>ERROR:</strong> No se encontró orden con expediente: {$numeroExpediente}<br>";
    die();
}

echo "✅ Orden encontrada<br>";
echo "📋 <strong>ID Orden:</strong> " . ($orden['OC_ID'] ?? 'N/A') . "<br>";
echo "👤 <strong>Cliente:</strong> " . ($orden['OC_CLIENTE_NOMBRE'] ?? 'N/A') . "<br>";
echo "<hr>";

// PASO 2: Verificar email del cliente
echo "<h2>PASO 2: Verificar Email del Cliente</h2>";
$emailCliente = trim($orden['OC_EMAIL_CLIENTE'] ?? '');

if (empty($emailCliente)) {
    echo "❌ <strong>ERROR:</strong> La orden NO tiene email del cliente (campo OC_EMAIL_CLIENTE está vacío)<br>";
    echo "💡 <strong>Solución:</strong> Edita la orden y agrega el email del cliente en el formulario<br>";
    die();
}

echo "✅ Email encontrado: <strong>{$emailCliente}</strong><br>";

// Validar formato de email
if (!filter_var($emailCliente, FILTER_VALIDATE_EMAIL)) {
    echo "❌ <strong>ERROR:</strong> El email NO es válido: {$emailCliente}<br>";
    echo "💡 <strong>Solución:</strong> Corrige el email en la orden de compra<br>";
    die();
}

echo "✅ Email válido<br>";
echo "<hr>";

// PASO 3: Verificar si ya existe confirmación
echo "<h2>PASO 3: Verificar Confirmación Existente</h2>";
$confirmacionModel = new ConfirmacionCliente();
$confirmacionExistente = $confirmacionModel->obtenerPorExpediente($numeroExpediente);

if ($confirmacionExistente) {
    echo "⚠️ Ya existe una confirmación:<br>";
    echo "📅 <strong>Fecha:</strong> " . ($confirmacionExistente['CONF_FECHA_CREACION']->format('Y-m-d H:i:s') ?? 'N/A') . "<br>";
    echo "📊 <strong>Estado:</strong> " . ($confirmacionExistente['CONF_ESTADO'] ?? 'N/A') . "<br>";
    echo "🔑 <strong>Token:</strong> " . substr($confirmacionExistente['CONF_TOKEN'] ?? '', 0, 20) . "...<br>";
    
    if ($confirmacionExistente['CONF_ESTADO'] === 'ACEPTADO') {
        echo "❌ <strong>ERROR:</strong> El cliente ya aceptó los documentos. No se puede enviar otro correo.<br>";
        die();
    }
} else {
    echo "✅ No existe confirmación previa<br>";
}
echo "<hr>";

// PASO 4: Crear token
echo "<h2>PASO 4: Crear Token de Confirmación</h2>";
$token = $confirmacionModel->crear($numeroExpediente, $emailCliente);

if (!$token) {
    echo "❌ <strong>ERROR:</strong> No se pudo crear el token en la base de datos<br>";
    die();
}

echo "✅ Token creado: <strong>" . substr($token, 0, 20) . "...</strong><br>";
echo "<hr>";

// PASO 5: Preparar datos del correo
echo "<h2>PASO 5: Datos del Correo</h2>";
$linkConfirmacion = "http://190.238.78.104:3800/digitalizacion-documentos/confirmacion/ver?token=" . $token;

echo "📧 <strong>Para:</strong> {$emailCliente}<br>";
echo "📝 <strong>Asunto:</strong> 📬 Confirmación de Documentos - Expediente {$numeroExpediente}<br>";
echo "🔗 <strong>Link:</strong> <a href='{$linkConfirmacion}' target='_blank'>{$linkConfirmacion}</a><br>";
echo "🌐 <strong>API:</strong> http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php<br>";
echo "<hr>";

// PASO 6: Enviar correo
echo "<h2>PASO 6: Enviar Correo</h2>";
echo "⏳ Enviando correo...<br><br>";

$envioExitoso = $confirmacionModel->enviarCorreoCliente($numeroExpediente, $emailCliente, $token);

if ($envioExitoso) {
    echo "✅ <strong style='color: green; font-size: 18px;'>CORREO ENVIADO EXITOSAMENTE</strong><br>";
    echo "📬 Revisa la bandeja de entrada de: <strong>{$emailCliente}</strong><br>";
    echo "📂 También revisa la carpeta de SPAM/Correo no deseado<br>";
} else {
    echo "❌ <strong style='color: red; font-size: 18px;'>ERROR AL ENVIAR CORREO</strong><br>";
    echo "💡 <strong>Posibles causas:</strong><br>";
    echo "   - La API de correos no está disponible<br>";
    echo "   - El servidor de correos está caído<br>";
    echo "   - Timeout de conexión<br>";
    echo "<br>";
    echo "📋 <strong>Revisa los logs en:</strong> C:\\xampp\\htdocs\\digitalizacion-documentos\\logs\\<br>";
}

echo "<hr>";
echo "<h2>✅ DEBUG COMPLETADO</h2>";
echo "<p><a href='?expediente={$numeroExpediente}'>🔄 Volver a intentar</a></p>";
