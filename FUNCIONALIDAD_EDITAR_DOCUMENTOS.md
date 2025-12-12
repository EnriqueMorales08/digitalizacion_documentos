# 📝 Funcionalidad de Edición de Documentos

## Fecha de Implementación
31 de Octubre de 2025

## Descripción General
Se ha implementado la funcionalidad para **editar documentos guardados** después de haberlos creado. Ahora cuando guardas un documento y luego lo visualizas, aparece un botón "EDITAR" que te permite modificar los datos y actualizarlos en la base de datos.

---

## 🎯 Características Implementadas

### 1. Botón EDITAR en Modo Visualización
- Cuando visualizas un documento guardado (modo `?modo=ver`), aparece un botón **"✏️ EDITAR"** en la esquina superior derecha
- El botón tiene diseño naranja para diferenciarlo del botón de guardar (verde)
- Al hacer clic, te lleva de vuelta al formulario editable con todos los datos cargados

### 2. Botón GUARDAR/ACTUALIZAR Dinámico
- Cuando creas un documento nuevo → Muestra **"💾 GUARDAR"**
- Cuando editas un documento existente → Muestra **"💾 ACTUALIZAR"**
- El sistema detecta automáticamente si hay datos guardados

### 3. Carga Automática de Datos
- Al editar, los datos guardados en la BD se cargan automáticamente en el formulario
- Prioridad: `$documentData` (datos guardados) > `$ordenCompraData` (datos de la orden)
- Incluye todos los campos: textos, fechas, firmas, etc.

---

## 📁 Archivos Modificados

### 1. DocumentController.php
**Ruta:** `app/controllers/DocumentController.php`

**Cambios:**
- Agregada variable `$modoEdicion` que detecta cuando hay datos guardados pero no está en modo visualización
- Líneas modificadas: 118-119

```php
// Modo edición: cuando hay datos guardados pero no está en modo visualización
$modoEdicion = !empty($documentData) && !$modoImpresion;
```

**⚠️ IMPORTANTE:** Los datos guardados (`$documentData`) se cargan tanto en modo edición como en modo visualización, para que siempre se vean los datos actualizados.

### 2. carta_conocimiento_aceptacion.php (Ejemplo)
**Ruta:** `app/views/documents/layouts/carta_conocimiento_aceptacion.php`

**Cambios realizados:**

#### a) Botón GUARDAR/ACTUALIZAR dinámico (líneas 197-205)
```php
<button type="submit" onclick="return copiarDatosAntesDeGuardar(event)" style="...">
    💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
</button>
```

#### b) Botón EDITAR en modo visualización (líneas 207-219)
```php
<?php if (isset($modoImpresion) && $modoImpresion): ?>
<div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
  <a href="/digitalizacion-documentos/documents/show?id=carta_conocimiento_aceptacion&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
     style="...">
    ✏️ EDITAR
  </a>
</div>
<?php endif; ?>
```

#### c) Deshabilitar edición en modo visualización (líneas 248-265)
```php
// Deshabilitar edición en modo visualización
<?php if (isset($modoImpresion) && $modoImpresion): ?>
document.addEventListener('DOMContentLoaded', function() {
  // Deshabilitar todos los contenteditable
  const editables = document.querySelectorAll('[contenteditable="true"]');
  editables.forEach(function(el) {
    el.setAttribute('contenteditable', 'false');
    el.style.cursor = 'default';
  });
  
  // Deshabilitar el click en la firma
  const firmaPreview = document.getElementById('firma-cliente-preview');
  if (firmaPreview) {
    firmaPreview.onclick = null;
    firmaPreview.style.cursor = 'default';
  }
});
<?php endif; ?>
```

#### d) Mostrar firma guardada (líneas 154-158)
```php
<?php if (!empty($documentData['CCA_FIRMA_CLIENTE'])): ?>
  <img src="<?php echo htmlspecialchars($documentData['CCA_FIRMA_CLIENTE']); ?>" 
       style="max-width:100%; max-height:50px; display:block;" alt="Firma del cliente">
<?php else: ?>
  <span style="color:#999; font-size:11px;">Haga clic aquí para firmar</span>
<?php endif; ?>
```

#### e) Carga de datos guardados (líneas 128-138, 161-168)
```php
// Ejemplo: Cargar nombre del cliente
<?php echo htmlspecialchars($documentData['CCA_CLIENTE_NOMBRE_COMPLETO'] ?? $ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>

// Ejemplo: Cargar fecha de firma
<?php 
if (!empty($documentData['CCA_FECHA_FIRMA'])) {
    $fechaFirma = $documentData['CCA_FECHA_FIRMA'];
    if ($fechaFirma instanceof DateTime) { 
        echo $fechaFirma->format('d/m/Y'); 
    } else { 
        echo date('d/m/Y', strtotime($fechaFirma)); 
    }
} else {
    // Usar fecha de la orden si no hay fecha guardada
    $fechaOrden = $ordenCompraData['OC_FECHA_ORDEN'] ?? date('Y-m-d'); 
    if ($fechaOrden instanceof DateTime) { 
        $fechaOrden = $fechaOrden->format('Y-m-d'); 
    } 
    echo date('d/m/Y', strtotime($fechaOrden));
}
?>
```

---

## 🔄 Flujo de Funcionamiento

```
┌─────────────────────────────────────────────────────────────┐
│                  CREAR DOCUMENTO NUEVO                       │
│  1. Usuario accede al documento                              │
│  2. Llena los campos del formulario                          │
│  3. Hace clic en "💾 GUARDAR"                                │
│  4. Se hace INSERT en la base de datos                       │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                  VISUALIZAR DOCUMENTO                        │
│  1. Usuario hace clic en "Ver" desde el panel                │
│  2. Aparece el documento en modo visualización               │
│  3. Se muestra botón "✏️ EDITAR" (naranja)                   │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                  EDITAR DOCUMENTO                            │
│  1. Usuario hace clic en "✏️ EDITAR"                         │
│  2. Se carga el formulario con datos guardados               │
│  3. Botón cambia a "💾 ACTUALIZAR"                           │
│  4. Usuario modifica los campos necesarios                   │
│  5. Hace clic en "💾 ACTUALIZAR"                             │
│  6. Se hace UPDATE en la base de datos                       │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎨 Diseño Visual

### Botón EDITAR
- **Color:** Naranja (gradiente #f59e0b a #d97706)
- **Posición:** Esquina superior derecha (top: 80px, right: 20px)
- **Icono:** ✏️ + SVG de lápiz
- **Estilo:** Botón flotante con sombra

### Botón GUARDAR/ACTUALIZAR
- **Color:** Verde (gradiente #10b981 a #059669)
- **Posición:** Esquina inferior derecha (bottom: 20px, right: 20px)
- **Texto dinámico:** "GUARDAR" o "ACTUALIZAR" según el contexto
- **Estilo:** Botón flotante con sombra

---

## 🔧 Cómo Aplicar a Otros Documentos

Para aplicar esta funcionalidad a otros documentos, sigue estos pasos:

### Paso 1: Agregar botón EDITAR
Busca el bloque del botón GUARDAR y agrega después:

```php
<!-- Botón de EDITAR cuando está en modo visualización -->
<?php if (isset($modoImpresion) && $modoImpresion): ?>
<div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
  <a href="/digitalizacion-documentos/documents/show?id=NOMBRE_DEL_DOCUMENTO&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
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

**⚠️ IMPORTANTE:** Reemplaza `NOMBRE_DEL_DOCUMENTO` con el ID del documento (ej: `acta-conocimiento-conformidad`, `carta_recepcion`, etc.)

### Paso 2: Hacer dinámico el botón GUARDAR
Modifica el texto del botón:

```php
<button type="submit" ...>
    💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
</button>
```

### Paso 3: Cargar datos guardados
Para cada campo del formulario, usa el operador `??` para priorizar datos guardados:

```php
<!-- Ejemplo para un campo de texto -->
<?php echo htmlspecialchars($documentData['PREFIJO_CAMPO'] ?? $ordenCompraData['OC_CAMPO'] ?? ''); ?>

<!-- Ejemplo para un campo de fecha -->
<?php 
if (!empty($documentData['PREFIJO_FECHA'])) {
    $fecha = $documentData['PREFIJO_FECHA'];
    if ($fecha instanceof DateTime) { 
        echo $fecha->format('d/m/Y'); 
    } else { 
        echo date('d/m/Y', strtotime($fecha)); 
    }
} else {
    // Valor por defecto
}
?>
```

**Prefijos por documento:**
- Acta Conocimiento Conformidad: `ACC_`
- Autorización Datos Personales: `ADP_`
- Carta Conocimiento Aceptación: `CCA_`
- Carta Recepción: `CR_`
- Carta Características: `CC_`
- Carta Características Banbif: `CCB_`
- Carta Felicitaciones: `CF_`
- Carta Obsequios: `CO_`
- Política Protección Datos: `PPD_`

---

## ✅ Ventajas de Esta Implementación

1. **No rompe funcionalidad existente:** Todo sigue funcionando igual
2. **Detección automática:** El sistema sabe si es nuevo o edición
3. **UPDATE automático:** Si existe el registro, se actualiza; si no, se crea
4. **Interfaz intuitiva:** Botones claros y diferenciados por color
5. **Datos persistentes:** Los cambios se guardan en la BD inmediatamente

---

## 🧪 Pruebas Recomendadas

### Caso 1: Crear documento nuevo
1. Acceder a un documento sin datos guardados
2. Verificar que el botón diga "💾 GUARDAR"
3. Llenar campos y guardar
4. Verificar que se cree el registro en la BD

### Caso 2: Editar documento existente
1. Visualizar un documento guardado (modo `?modo=ver`)
2. Verificar que aparezca el botón "✏️ EDITAR"
3. Hacer clic en EDITAR
4. Verificar que los datos se carguen correctamente
5. Verificar que el botón diga "💾 ACTUALIZAR"
6. Modificar un campo y guardar
7. Verificar que se actualice en la BD

### Caso 3: Firma guardada
1. Editar un documento con firma guardada
2. Verificar que la firma se muestre correctamente
3. Opcionalmente cambiar la firma
4. Guardar y verificar que se actualice

---

## 📊 Base de Datos

El sistema usa la función `guardarDocumentoIndividual()` del modelo `Document.php` que ya implementa la lógica de INSERT/UPDATE:

```php
// Si ya existe, hacer UPDATE; si no, hacer INSERT
if ($existingRow) {
    // UPDATE: construir SET clause
    $sql = "UPDATE $table SET " . implode(", ", $setClauses) . " WHERE $fkField = ?";
} else {
    // INSERT
    $sql = "INSERT INTO $table (" . implode(", ", $fields) . ") VALUES (...)";
}
```

**No se requieren cambios en la base de datos.** Todo funciona con la estructura actual.

---

## 🎉 Resumen

✅ **Implementado:** Funcionalidad completa de edición de documentos  
✅ **Sin cambios en BD:** Usa la estructura existente  
✅ **Compatible:** No afecta funcionalidad actual  
✅ **Fácil de replicar:** Patrón claro para aplicar a otros documentos  
✅ **Interfaz intuitiva:** Botones claros y diferenciados  

---

## 📝 Próximos Pasos (Opcional)

Para completar la funcionalidad en todos los documentos, aplicar los mismos cambios a:

- [ ] `acta-conocimiento-conformidad.php`
- [ ] `actorizacion-datos-personales.php`
- [ ] `carta_recepcion.php`
- [ ] `carta-caracteristicas.php`
- [ ] `carta_caracteristicas_banbif.php`
- [ ] `carta_felicitaciones.php`
- [ ] `carta_obsequios.php`
- [ ] `politica_proteccion_datos.php`

**Nota:** La funcionalidad ya está implementada en `carta_conocimiento_aceptacion.php` como ejemplo.
