<?php
// app/views/documents/show.php

// Mostrar mensaje de éxito si existe
if (isset($_GET['success']) && $_GET['success'] === 'documento_guardado') {
    echo '<div style="position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 20px 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); animation: slideIn 0.5s ease;">';
    echo '<strong>✅ ¡Documento guardado exitosamente!</strong>';
    echo '</div>';
    echo '<style>@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }</style>';
    echo '<script>setTimeout(function(){ document.querySelector("div[style*=\'fixed\']").style.display = "none"; }, 3000);</script>';
}

if (isset($id)) {
    $file = __DIR__ . "/layouts/{$id}.php";
    if (file_exists($file)) {
        include $file;   // solo muestra el contenido del documento
    } else {
        echo "<p>Documento no encontrado.</p>";
    }
}
?>

