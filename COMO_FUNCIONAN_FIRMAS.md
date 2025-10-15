# 📝 Cómo Funcionan las Firmas en el Sistema

## ✅ Respuesta Rápida

**SÍ, las firmas aparecen automáticamente pre-llenadas** en todos los documentos que pertenecen a esa orden de compra, igual que los demás datos.

---

## 🔄 Flujo Completo de las Firmas

### 1️⃣ Crear Orden de Compra

Cuando creas una **Orden de Compra**, ingresas las credenciales para 3 firmas:

```
📄 Orden de Compra
├─ 🖊️ Firma del Asesor (OC_ASESOR_FIRMA)
├─ 🖊️ Firma del Cliente (OC_CLIENTE_FIRMA)
├─ 👆 Huella del Cliente (OC_CLIENTE_HUELLA)
├─ 🖊️ Firma del Jefe (OC_JEFE_FIRMA)
└─ 👆 Huella del Jefe (OC_JEFE_HUELLA)
```

Estas firmas se **guardan en la base de datos** en la tabla `SIST_ORDEN_COMPRA`:
- `OC_ASESOR_FIRMA` = URL de la imagen de firma del asesor
- `OC_CLIENTE_FIRMA` = URL de la imagen de firma del cliente
- `OC_CLIENTE_HUELLA` = URL de la imagen de huella del cliente
- `OC_JEFE_FIRMA` = URL de la imagen de firma del jefe
- `OC_JEFE_HUELLA` = URL de la imagen de huella del jefe

---

### 2️⃣ Generar Otros Documentos

Cuando generas **otros documentos** (Acta, Carta de Recepción, etc.), el sistema:

1. **Lee los datos de la orden de compra** (incluyendo las firmas)
2. **Pre-llena automáticamente** todos los campos, incluyendo las firmas
3. **Muestra la imagen de la firma** si existe

---

## 📋 Documentos que Usan las Firmas

### Documentos que usan `OC_CLIENTE_FIRMA` (Firma del Cliente)

1. ✅ **Acta de Conocimiento y Conformidad**
   ```php
   <?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?>
       <img src="<?php echo htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']); ?>">
   <?php endif; ?>
   ```

2. ✅ **Autorización de Datos Personales**
   ```php
   <?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?>
       <img src="<?php echo htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']); ?>">
   <?php endif; ?>
   ```

3. ✅ **Carta de Conocimiento y Aceptación**
   ```php
   <?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?>
       <img src="<?php echo htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']); ?>">
   <?php endif; ?>
   ```

4. ✅ **Carta de Recepción**
   ```php
   <?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?>
       <img src="<?php echo htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']); ?>">
   <?php endif; ?>
   ```

5. ✅ **Política de Protección de Datos**
   ```php
   <?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?>
       <img src="<?php echo htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']); ?>">
   <?php endif; ?>
   ```

---

## 🎯 Ejemplo Práctico

### Paso 1: Crear Orden de Compra
```
Usuario logueado: Juan Pérez (juan@test.com)

Firmas ingresadas:
├─ Asesor: usuario "asesor1", password "123"
│  → Sistema obtiene firma → OC_ASESOR_FIRMA = "http://...firma_asesor.png"
│
├─ Cliente: usuario "cliente1", password "456"
│  → Sistema obtiene firma → OC_CLIENTE_FIRMA = "http://...firma_cliente.png"
│  → Sistema obtiene huella → OC_CLIENTE_HUELLA = "http://...huella_cliente.png"
│
└─ Jefe: usuario "jefe1", password "789"
   → Sistema obtiene firma → OC_JEFE_FIRMA = "http://...firma_jefe.png"
   → Sistema obtiene huella → OC_JEFE_HUELLA = "http://...huella_jefe.png"

✅ Sesión se mantiene: Juan Pérez (juan@test.com)
✅ Orden guardada con todas las firmas
```

### Paso 2: Generar Acta de Conocimiento
```
1. Haces clic en "Generar Acta de Conocimiento y Conformidad"
   ↓
2. Sistema lee la orden de compra (incluyendo OC_CLIENTE_FIRMA)
   ↓
3. Sistema pre-llena el documento:
   - Nombre: [Pre-llenado con OC_COMPRADOR_NOMBRE]
   - DNI: [Pre-llenado con OC_COMPRADOR_NUMERO_DOCUMENTO]
   - Firma: [Pre-llenada con OC_CLIENTE_FIRMA] ✅
   ↓
4. ✅ La firma aparece automáticamente como imagen
```

---

## 🔍 Cómo Verificar

### Verificar en la Base de Datos
```sql
SELECT 
    OC_NUMERO_EXPEDIENTE,
    OC_ASESOR_FIRMA,
    OC_CLIENTE_FIRMA,
    OC_CLIENTE_HUELLA,
    OC_JEFE_FIRMA,
    OC_JEFE_HUELLA
FROM SIST_ORDEN_COMPRA
WHERE OC_NUMERO_EXPEDIENTE = '2025100028';
```

**Resultado esperado:**
```
OC_ASESOR_FIRMA:  http://190.238.78.104:3800/robot-sdg-ford/firmas/firma_asesor.png
OC_CLIENTE_FIRMA: http://190.238.78.104:3800/robot-sdg-ford/firmas/firma_cliente.png
OC_CLIENTE_HUELLA: http://190.238.78.104:3800/robot-sdg-ford/firmas/huella_cliente.png
OC_JEFE_FIRMA:    http://190.238.78.104:3800/robot-sdg-ford/firmas/firma_jefe.png
OC_JEFE_HUELLA:   http://190.238.78.104:3800/robot-sdg-ford/firmas/huella_jefe.png
```

### Verificar en los Documentos

1. **Crear orden de compra** con firmas
2. **Generar cualquier documento** (Acta, Carta, etc.)
3. **Verificar que aparezca la firma** automáticamente

**Resultado esperado:**
- ✅ La firma aparece como **imagen** (no como texto "Firma")
- ✅ El nombre y DNI aparecen pre-llenados
- ✅ NO necesitas volver a ingresar credenciales

---

## ⚙️ Cómo Funciona Técnicamente

### En la Orden de Compra (al crear)
```javascript
// Usuario ingresa credenciales para firma
usuario: "cliente1"
password: "456"
   ↓
// Sistema llama a verificarFirma()
Document.php → verificarFirma($usuario, $password)
   ↓
// Sistema consulta tabla firmas
SELECT firma_data FROM firmas WHERE usuario = 'cliente1' AND password = '456'
   ↓
// Sistema retorna URL de la firma (SIN modificar sesión)
return 'http://190.238.78.104:3800/robot-sdg-ford/firmas/firma_cliente.png'
   ↓
// Sistema guarda en la orden
OC_CLIENTE_FIRMA = 'http://190.238.78.104:3800/robot-sdg-ford/firmas/firma_cliente.png'
```

### En Otros Documentos (al generar)
```php
// Sistema lee la orden de compra
$ordenCompraData = getOrdenCompra($ordenId);
   ↓
// Sistema verifica si existe la firma
<?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?>
   ↓
// Sistema muestra la imagen
<img src="<?php echo htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']); ?>">
   ↓
// ✅ Firma aparece automáticamente
```

---

## 📊 Resumen Visual

```
┌─────────────────────────────────────────────────────────┐
│  ORDEN DE COMPRA (Documento Principal)                 │
├─────────────────────────────────────────────────────────┤
│  1. Ingresas credenciales para 3 firmas                │
│  2. Sistema obtiene URLs de las imágenes               │
│  3. Sistema guarda en BD: OC_ASESOR_FIRMA,             │
│     OC_CLIENTE_FIRMA, OC_JEFE_FIRMA                    │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│  OTROS DOCUMENTOS (Acta, Cartas, etc.)                 │
├─────────────────────────────────────────────────────────┤
│  1. Sistema lee orden de compra                         │
│  2. Sistema obtiene OC_CLIENTE_FIRMA                    │
│  3. Sistema muestra imagen automáticamente ✅           │
│  4. NO necesitas volver a ingresar credenciales ✅      │
└─────────────────────────────────────────────────────────┘
```

---

## ✅ Ventajas del Sistema

1. ✅ **Ingresas credenciales UNA SOLA VEZ** (en la orden de compra)
2. ✅ **Las firmas se reutilizan** en todos los documentos
3. ✅ **Pre-llenado automático** como cualquier otro dato
4. ✅ **No pierdes tu sesión** al ingresar credenciales de firmas
5. ✅ **Consistencia** - La misma firma en todos los documentos

---

## 🎯 Conclusión

**SÍ, las firmas aparecen automáticamente pre-llenadas** en todos los documentos que pertenecen a esa orden de compra. 

Funciona exactamente igual que los demás datos:
- Nombre del cliente → Pre-llenado ✅
- DNI del cliente → Pre-llenado ✅
- Vehículo → Pre-llenado ✅
- **Firma del cliente → Pre-llenada ✅**

**NO necesitas volver a ingresar las credenciales** en cada documento. Las firmas se guardan una vez y se reutilizan automáticamente.

---

**Fecha:** Octubre 2025  
**Versión:** 5.0 (Final)
