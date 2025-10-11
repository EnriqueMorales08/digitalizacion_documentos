/**
 * Script para cargar datos de la orden de compra desde la sesión PHP
 * Esto permite que los datos persistan después de guardar
 */

// Los datos se pasan desde PHP como variable global
if (typeof ordenData !== 'undefined' && ordenData) {
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar todos los campos del formulario
        for (const [campo, valor] of Object.entries(ordenData)) {
            const elemento = document.querySelector(`[name="${campo}"]`);
            
            if (elemento) {
                if (elemento.type === 'radio') {
                    // Para radio buttons
                    const radio = document.querySelector(`[name="${campo}"][value="${valor}"]`);
                    if (radio) radio.checked = true;
                } else if (elemento.type === 'checkbox') {
                    // Para checkboxes
                    elemento.checked = (valor === '1' || valor === 'on' || valor === true);
                } else if (elemento.tagName === 'SELECT') {
                    // Para selects
                    elemento.value = valor;
                } else {
                    // Para inputs normales (text, date, etc.)
                    elemento.value = valor;
                }
            }
        }
        
        console.log('✅ Datos de la orden cargados desde sesión');
    });
}
