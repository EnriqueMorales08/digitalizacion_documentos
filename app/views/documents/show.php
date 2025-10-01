<?php
// app/views/documents/show.php

if (isset($id)) {
    $file = __DIR__ . "/layouts/{$id}.php";
    if (file_exists($file)) {
        include $file;   // solo muestra el contenido del documento
    } else {
        echo "<p>Documento no encontrado.</p>";
    }
}
?>

