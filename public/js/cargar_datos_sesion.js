/**
 * Script para cargar datos de la orden de compra desde la sesión PHP
 * Esto permite que los datos persistan después de guardar
 */

// Los datos se pasan desde PHP como variable global
if (typeof ordenData !== 'undefined' && ordenData) {
    document.addEventListener('DOMContentLoaded', function() {
        // Selectores que se cargan dinámicamente y NO deben ser llenados aquí
        const selectoresDinamicos = ['OC_AGENCIA', 'OC_NOMBRE_RESPONSABLE', 'OC_CENTRO_COSTO'];
        
        // Cargar todos los campos del formulario
        for (const [campo, valor] of Object.entries(ordenData)) {
            // IGNORAR selectores dinámicos (se cargan con otro script)
            if (selectoresDinamicos.includes(campo)) {
                console.log(`⏭️ Saltando ${campo} (se carga dinámicamente)`);
                continue;
            }
            
            const elemento = document.querySelector(`[name="${campo}"]`);
            
            if (elemento) {
                // IGNORAR inputs de tipo file (no se pueden llenar por seguridad)
                if (elemento.type === 'file') {
                    continue; // Saltar este campo
                }
                
                if (elemento.type === 'radio') {
                    // Para radio buttons
                    const radio = document.querySelector(`[name="${campo}"][value="${valor}"]`);
                    if (radio) radio.checked = true;
                } else if (elemento.type === 'checkbox') {
                    // Para checkboxes
                    elemento.checked = (valor === '1' || valor === 'on' || valor === true);
                } else if (elemento.tagName === 'SELECT') {
                    // Para selects normales (no dinámicos)
                    elemento.value = valor;
                } else {
                    // Para inputs normales (text, date, etc.)
                    elemento.value = valor;
                }
            }
        }
        
        console.log('✅ Datos de la orden cargados desde sesión (excepto selectores dinámicos)');
    });
}
