# 🔧 Correcciones Finales Necesarias

## Problemas Identificados

### ✅ Problema 1: Orden de Compra no actualiza (SOLUCIONADO)
**Estado:** ✅ CORREGIDO

**Causa:** La función `guardarOrdenCompra()` siempre hacía INSERT, nunca UPDATE.

**Solución aplicada:**
- Modificado `Document.php` líneas 81-229
- Ahora detecta si `$_SESSION['orden_id']` existe
- Si existe → hace UPDATE
- Si no existe → hace INSERT

### ⚠️ Problema 2: Documentos no muestran datos actualizados en visualización
**Estado:** ⚠️ PENDIENTE DE CORRECCIÓN

**Causa:** Los documentos tienen el script para deshabilitar edición, pero los campos NO están cargando los datos de `$documentData`.

**Documentos afectados:**
- actorizacion-datos-personales.php
- carta_recepcion.php  
- carta-caracteristicas.php
- carta_caracteristicas_banbif.php
- carta_felicitaciones.php
- politica_proteccion_datos.php

**Nota:** `acta-conocimiento-conformidad.php` YA tiene la carga correcta de datos.

---

## Solución para Problema 2

Cada documento necesita que TODOS sus campos carguen datos con esta prioridad:

```php
$documentData['PREFIJO_CAMPO'] ?? $ordenCompraData['OC_CAMPO'] ?? ''
```

### Ejemplo de Corrección

**ANTES (solo carga de orden de compra):**
```php
<input type="text" name="ADP_NOMBRE_AUTORIZACION" 
       value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>">
```

**DESPUÉS (carga datos guardados primero):**
```php
<input type="text" name="ADP_NOMBRE_AUTORIZACION" 
       value="<?php echo htmlspecialchars($documentData['ADP_NOMBRE_AUTORIZACION'] ?? $ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>">
```

---

## Mapeo de Prefijos por Documento

| Documento | Prefijo | Tabla BD |
|-----------|---------|----------|
| acta-conocimiento-conformidad.php | `ACC_` | SIST_ACTA_CONOCIMIENTO_CONFORMIDAD |
| actorizacion-datos-personales.php | `ADP_` | SIST_AUTORIZACION_DATOS_PERSONALES |
| carta_conocimiento_aceptacion.php | `CCA_` | SIST_CARTA_CONOCIMIENTO_ACEPTACION |
| carta_recepcion.php | `CR_` | SIST_CARTA_RECEPCION |
| carta-caracteristicas.php | `CC_` | SIST_CARTA_CARACTERISTICAS |
| carta_caracteristicas_banbif.php | `CCB_` | SIST_CARTA_CARACTERISTICAS_BANBIF |
| carta_felicitaciones.php | `CF_` | SIST_CARTA_FELICITACIONES |
| politica_proteccion_datos.php | `PPD_` | SIST_POLITICA_PROTECCION_DATOS |

---

## Pasos para Corregir Cada Documento

### 1. Identificar todos los campos del formulario
Buscar todos los `<input>`, `<select>`, `<textarea>` que tengan `name="PREFIJO_*"`

### 2. Modificar el atributo `value` o contenido
Cambiar de:
```php
value="<?php echo htmlspecialchars($ordenCompraData['OC_*'] ?? ''); ?>"
```

A:
```php
value="<?php echo htmlspecialchars($documentData['PREFIJO_*'] ?? $ordenCompraData['OC_*'] ?? ''); ?>"
```

### 3. Para campos de fecha
```php
<?php 
$fecha = $documentData['PREFIJO_FECHA'] ?? $ordenCompraData['OC_FECHA'] ?? date('Y-m-d');
if ($fecha instanceof DateTime) { 
    $fecha = $fecha->format('Y-m-d'); 
}
echo htmlspecialchars($fecha);
?>
```

### 4. Para firmas (si aplica)
Agregar script para cargar firma guardada:
```php
<script>
document.addEventListener('DOMContentLoaded', function() {
  <?php if (isset($documentData) && !empty($documentData['PREFIJO_FIRMA'])): ?>
    const firmaRuta = '<?php echo htmlspecialchars($documentData['PREFIJO_FIRMA']); ?>';
    document.getElementById('firma_preview_id').innerHTML = 
      '<img src="' + firmaRuta + '" style="max-width:100%; max-height:50px;">';
  <?php endif; ?>
});
</script>
```

---

## Estado Actual de Correcciones

### ✅ Completados:
- [x] Document.php - Función guardarOrdenCompra() modificada para UPDATE
- [x] carta_conocimiento_aceptacion.php - Carga datos correctamente

### ⚠️ Pendientes:
- [ ] actorizacion-datos-personales.php - Necesita cargar $documentData en campos
- [ ] carta_recepcion.php - Necesita cargar $documentData en campos
- [ ] carta-caracteristicas.php - Necesita cargar $documentData en campos
- [ ] carta_caracteristicas_banbif.php - Necesita cargar $documentData en campos
- [ ] carta_felicitaciones.php - Necesita cargar $documentData en campos
- [ ] politica_proteccion_datos.php - Necesita cargar $documentData en campos

---

## Verificación Post-Corrección

Para cada documento corregido, verificar:

1. ✅ Crear documento nuevo → Guardar → Ver
   - Datos se muestran correctamente
   
2. ✅ Hacer clic en EDITAR → Modificar campo → ACTUALIZAR → Ver
   - Cambios se reflejan en la visualización
   
3. ✅ Firma guardada se muestra como imagen (si aplica)

4. ✅ Campos deshabilitados en modo visualización

---

## Prioridad de Corrección

**ALTA PRIORIDAD:**
1. actorizacion-datos-personales.php (documento con firma)
2. carta_recepcion.php (documento con firma)

**MEDIA PRIORIDAD:**
3. carta-caracteristicas.php
4. carta_caracteristicas_banbif.php
5. carta_felicitaciones.php

**BAJA PRIORIDAD:**
6. politica_proteccion_datos.php (solo campos ocultos)

---

## Notas Importantes

- **NO modificar** acta-conocimiento-conformidad.php (ya está correcto)
- **NO modificar** carta_conocimiento_aceptacion.php (ya está correcto)
- **NO modificar** orden-compra.php (ya se corrigió el UPDATE)

- La carga de datos debe ser **consistente** en todos los documentos
- Siempre usar el operador `??` para la prioridad de datos
- Verificar que los nombres de campos coincidan con la BD

---

## Resumen

✅ **Orden de compra:** Ahora actualiza correctamente (no crea duplicados)  
⚠️ **Demás documentos:** Necesitan cargar `$documentData` en sus campos  
✅ **Scripts de deshabilitar:** Ya están agregados en todos los documentos  
✅ **Botones EDITAR:** Ya están agregados en todos los documentos  

**Próximo paso:** Corregir la carga de datos en los 6 documentos pendientes.
