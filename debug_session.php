<?php
session_start();

echo "<h2>🔍 Diagnóstico de Sesión</h2>";
echo "<pre>";
echo "=== VARIABLES DE SESIÓN ===\n\n";

echo "orden_guardada: " . (isset($_SESSION['orden_guardada']) ? ($_SESSION['orden_guardada'] ? 'true' : 'false') : 'NO EXISTE') . "\n";
echo "forma_pago: " . ($_SESSION['forma_pago'] ?? 'NO EXISTE') . "\n";
echo "banco_abono: " . ($_SESSION['banco_abono'] ?? 'NO EXISTE') . "\n";
echo "orden_id: " . ($_SESSION['orden_id'] ?? 'NO EXISTE') . "\n";

echo "\n=== ANÁLISIS DETALLADO DEL BANCO ===\n\n";
$banco_abono = $_SESSION['banco_abono'] ?? '';
echo "Valor guardado: '" . $banco_abono . "'\n";
echo "Longitud: " . strlen($banco_abono) . " caracteres\n";
echo "Bytes (hex): " . bin2hex($banco_abono) . "\n";
echo "Comparación exacta con 'Banco Interamericano de Finanzas': " . ($banco_abono === 'Banco Interamericano de Finanzas' ? '✅ SÍ COINCIDE' : '❌ NO COINCIDE') . "\n";
echo "Comparación con trim: " . (trim($banco_abono) === 'Banco Interamericano de Finanzas' ? '✅ SÍ COINCIDE' : '❌ NO COINCIDE') . "\n";
echo "Comparación case-insensitive: " . (strtoupper(trim($banco_abono)) === strtoupper('Banco Interamericano de Finanzas') ? '✅ SÍ COINCIDE' : '❌ NO COINCIDE') . "\n";

// Buscar variaciones comunes
$variaciones = [
    'Banco Interamericano de Finanzas',
    'BANCO INTERAMERICANO DE FINANZAS',
    'Banbif',
    'BANBIF',
    'BanBif',
    'Banco Interamericano De Finanzas',
    'INTERAMERICANO DE FINANZAS'
];

echo "\n=== PRUEBA CON VARIACIONES COMUNES ===\n\n";
foreach ($variaciones as $var) {
    $coincide = (strtoupper(trim($banco_abono)) === strtoupper(trim($var)));
    echo ($coincide ? '✅' : '❌') . " '" . $var . "'\n";
}

echo "\n=== TODAS LAS VARIABLES DE SESIÓN ===\n\n";
print_r($_SESSION);

echo "</pre>";

echo "<br><a href='/digitalizacion-documentos/documents' style='padding: 10px 20px; background: #1e3a8a; color: white; text-decoration: none; border-radius: 5px;'>← Volver al Panel</a>";
?>
