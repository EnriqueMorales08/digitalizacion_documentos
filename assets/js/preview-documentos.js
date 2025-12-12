/**
 * Script compartido para cargar preview de documentos desde localStorage
 * Usado por todos los documentos relacionados a la orden de compra
 */

function cargarPreviewDocumento() {
    const urlParams = new URLSearchParams(window.location.search);
    const esPreview = urlParams.get('preview') === '1';
    const tieneOrdenId = urlParams.get('orden_id') !== null;
    
    // Si tiene orden_id, significa que viene del expediente (usa BD, no preview)
    if (tieneOrdenId) {
        console.log('📊 Documento cargado desde BD (expediente)');
        return false;
    }
    
    // Si NO es preview explícito, no cargar
    if (!esPreview) {
        return false;
    }
    
    console.log('👁️ Modo PREVIEW activado (desde orden de compra)');
    
    const datosPreviewStr = localStorage.getItem('preview_orden_compra');
    
    if (!datosPreviewStr) {
        console.warn('⚠️ No hay datos de preview en localStorage');
        return false;
    }
    
    try {
        const datos = JSON.parse(datosPreviewStr);
        console.log('📦 Datos de preview cargados:', datos);
        return datos;
    } catch (e) {
        console.error('❌ Error al parsear datos de preview:', e);
        return false;
    }
}

// Función helper para llenar un elemento por ID
function llenarCampo(elementId, valor, usarTextContent = false) {
    const elem = document.getElementById(elementId);
    if (elem && valor) {
        if (usarTextContent) {
            elem.textContent = valor;
        } else {
            elem.value = valor;
        }
        console.log('✅', elementId, ':', valor);
        return true;
    }
    return false;
}

// Función helper para mostrar firma
function mostrarFirma(previewElementId, hiddenInputId, rutaFirma) {
    if (!rutaFirma) return false;
    
    const firmaPreview = document.getElementById(previewElementId);
    const firmaInput = document.getElementById(hiddenInputId);
    
    if (firmaPreview) {
        firmaPreview.innerHTML = '<img src="' + rutaFirma + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;" alt="Firma del cliente">';
        if (firmaInput) {
            firmaInput.value = rutaFirma;
        }
        console.log('✅ Firma del cliente cargada');
        return true;
    }
    return false;
}
