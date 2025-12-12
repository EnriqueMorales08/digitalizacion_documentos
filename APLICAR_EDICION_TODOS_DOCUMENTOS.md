# ✅ Aplicación de Funcionalidad de Edición a Todos los Documentos

## Estado de Implementación

### ✅ Completados:
1. **carta_conocimiento_aceptacion.php** - ✅ Completado y probado
2. **acta-conocimiento-conformidad.php** - ✅ Completado
3. **actorizacion-datos-personales.php** - ✅ Completado
4. **carta_recepcion.php** - ✅ Completado

### 🔄 Pendientes de aplicar:
5. **carta-caracteristicas.php**
6. **carta_caracteristicas_banbif.php**
7. **carta_felicitaciones.php**
8. **carta_obsequios.php**
9. **politica_proteccion_datos.php**
10. **orden-compra.php**

---

## Cambios Aplicados en Cada Documento

### 1. Botón GUARDAR/ACTUALIZAR Dinámico

**Buscar:**
```php
💾 GUARDAR
```

**Reemplazar con:**
```php
💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
```

### 2. Agregar Botón EDITAR (después del bloque de GUARDAR)

**Agregar después de `<?php endif; ?>` del botón GUARDAR:**
```php
<!-- Botón de EDITAR cuando está en modo visualización -->
<?php if (isset($modoImpresion) && $modoImpresion): ?>
<div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
  <a href="/digitalizacion-documentos/documents/show?id=NOMBRE_DOCUMENTO&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
     style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4); font-family: Arial, sans-serif; font-size: 14px; font-weight: 600; transition: all 0.3s ease;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    ✏️ EDITAR
  </a>
</div>
<?php endif; ?>
```

**⚠️ IMPORTANTE:** Reemplazar `NOMBRE_DOCUMENTO` con el ID del documento correspondiente.

### 3. Script para Deshabilitar Edición en Modo Visualización

**Agregar después del botón EDITAR:**
```php
<script>
<?php if (isset($modoImpresion) && $modoImpresion): ?>
document.addEventListener('DOMContentLoaded', function() {
  // Deshabilitar contenteditable
  const editables = document.querySelectorAll('[contenteditable="true"]');
  editables.forEach(function(el) {
    el.setAttribute('contenteditable', 'false');
    el.style.cursor = 'default';
  });
  
  // Deshabilitar inputs, selects y textareas
  const inputs = document.querySelectorAll('input:not([type="hidden"]), select, textarea');
  inputs.forEach(function(el) {
    el.setAttribute('readonly', 'readonly');
    el.setAttribute('disabled', 'disabled');
    el.style.cursor = 'default';
    el.style.pointerEvents = 'none';
  });
});
<?php endif; ?>
</script>
```

---

## IDs de Documentos para el Botón EDITAR

| Archivo | ID del Documento |
|---------|------------------|
| carta-caracteristicas.php | `carta-caracteristicas` |
| carta_caracteristicas_banbif.php | `carta_caracteristicas_banbif` |
| carta_felicitaciones.php | `carta_felicitaciones` |
| carta_obsequios.php | `carta_obsequios` |
| politica_proteccion_datos.php | `politica_proteccion_datos` |
| orden-compra.php | `orden-compra` |

---

## Instrucciones para Aplicar Manualmente

Para cada documento pendiente:

1. **Abrir el archivo** en el editor
2. **Buscar** el botón `💾 GUARDAR`
3. **Reemplazar** con el código del botón dinámico
4. **Agregar** el botón EDITAR después del `<?php endif; ?>`
5. **Agregar** el script de deshabilitar edición
6. **Reemplazar** `NOMBRE_DOCUMENTO` con el ID correcto
7. **Guardar** el archivo

---

## Verificación

Para verificar que funciona correctamente:

1. ✅ Crear/guardar un documento
2. ✅ Hacer clic en "Ver" → Debe aparecer botón "✏️ EDITAR"
3. ✅ Los campos deben estar deshabilitados (no editables)
4. ✅ Hacer clic en "EDITAR" → Debe cargar el formulario editable
5. ✅ El botón debe decir "💾 ACTUALIZAR"
6. ✅ Modificar un campo y guardar
7. ✅ Volver a "Ver" → Los cambios deben reflejarse

---

## Notas Importantes

- **No eliminar** el código existente de los botones GUARDAR
- **Solo agregar** el código nuevo después de los bloques existentes
- **Verificar** que el ID del documento sea correcto en el href del botón EDITAR
- **Probar** cada documento después de aplicar los cambios

---

## Documentos Completados con Éxito

✅ **carta_conocimiento_aceptacion.php** - Probado y funcionando correctamente  
✅ **acta-conocimiento-conformidad.php** - Botones agregados  
✅ **actorizacion-datos-personales.php** - Botones agregados  
✅ **carta_recepcion.php** - Botones agregados  

---

## Próximos Pasos

Continuar aplicando los cambios a los 6 documentos restantes siguiendo el mismo patrón.
