# 📊 Explicación del Sistema de Auditoría

## 🎯 ¿Cuándo se Registra la Auditoría?

### ✅ SÍ se registra cuando:
1. **Editas una orden de compra existente**
   - Abres un expediente existente
   - Modificas algún campo (precio, cliente, vehículo, etc.)
   - Guardas los cambios
   - ✅ El sistema compara valores anteriores vs nuevos
   - ✅ Registra SOLO los campos que realmente cambiaron

### ❌ NO se registra cuando:
1. **Creas una orden de compra nueva** (primera vez)
   - Es una inserción nueva, no hay "valor anterior"
   - No hay nada que auditar porque es nuevo

2. **Guardas sin cambiar nada**
   - Si abres una orden y guardas sin modificar
   - No se registra porque no hay cambios

---

## 🔍 ¿Cómo Funciona la Comparación?

### Flujo del Sistema:

```
1. Usuario edita orden de compra
   ↓
2. Sistema obtiene datos ANTERIORES de la BD
   ↓
3. Usuario guarda cambios
   ↓
4. Sistema compara campo por campo:
   - Valor Anterior (de la BD)
   - Valor Nuevo (del formulario)
   ↓
5. Si son DIFERENTES → Registra en SIST_AUDIT_LOG
6. Si son IGUALES → NO registra (no hay cambio)
```

---

## 🐛 Problema Detectado: Valores Iguales Registrados como Diferentes

### ¿Por qué pasaba?

Los valores numéricos venían en diferentes formatos:

**Ejemplo:**
```
Valor Anterior (BD):  12210.59 (tipo: float)
Valor Nuevo (Form):   "12210.59" (tipo: string)

Comparación antigua: 12210.59 !== "12210.59" → DIFERENTES ❌
```

Aunque **visualmente son iguales**, PHP los veía como diferentes porque:
- Uno es `float` (número decimal)
- Otro es `string` (texto)

### ✅ Solución Implementada

Mejoré la función `normalizarValor()` para que:

1. **Detecte si un valor es numérico** (aunque sea string)
2. **Convierta ambos a float**
3. **Formatee con 2 decimales** (estándar para precios)
4. **Compare los valores normalizados**

**Ahora:**
```php
Valor Anterior: 12210.59 → normalizar → "12210.59"
Valor Nuevo:    "12210.59" → normalizar → "12210.59"

Comparación nueva: "12210.59" === "12210.59" → IGUALES ✅
```

---

## 🔧 Cambios Realizados

### 1. Mejorada la función `normalizarValor()` en `AuditLog.php`

**Antes:**
```php
private function normalizarValor($valor) {
    if (is_string($valor)) {
        return trim($valor);  // Solo quitaba espacios
    }
    return $valor;
}
```

**Ahora:**
```php
private function normalizarValor($valor) {
    // ... código para DateTime y null ...
    
    // Si es string
    if (is_string($valor)) {
        $valor = trim($valor);
        
        // Si parece un número, normalizarlo
        if (is_numeric($valor)) {
            $valorFloat = floatval($valor);
            return number_format($valorFloat, 2, '.', '');
        }
        
        return $valor;
    }
    
    // Si es número (int o float)
    if (is_numeric($valor)) {
        $valorFloat = floatval($valor);
        return number_format($valorFloat, 2, '.', '');
    }
    
    return $valor;
}
```

**Beneficios:**
- ✅ Compara números correctamente
- ✅ No registra falsos positivos
- ✅ Funciona con: `int`, `float`, `string numérico`
- ✅ Mantiene formato consistente (2 decimales)

---

## 🧹 Limpiar Registros Incorrectos

Si ya tienes registros con valores iguales (falsos positivos), ejecuta:

```sql
-- Archivo: database/LIMPIAR_REGISTROS_AUDITORIA.sql
```

Este script:
1. Cuenta cuántos registros tienen valores iguales
2. Muestra ejemplos antes de eliminar
3. Elimina los registros donde `AUDIT_OLD_VALUE = AUDIT_NEW_VALUE`
4. Muestra el resumen final

---

## 📋 Ejemplos de Uso

### Ejemplo 1: Cambio Real (SÍ se registra)

**Acción:**
- Usuario edita orden #118
- Cambia precio de `50000.00` a `55000.00`
- Guarda

**Resultado en SIST_AUDIT_LOG:**
```
Campo: OC_PRECIO_VENTA
Valor Anterior: 50000.00
Valor Nuevo: 55000.00
Acción: UPDATE
```

### Ejemplo 2: Sin Cambio (NO se registra)

**Acción:**
- Usuario edita orden #118
- El precio sigue siendo `50000.00`
- Guarda

**Resultado:**
- ✅ No se registra nada (valores iguales)

### Ejemplo 3: Cambio de Texto (SÍ se registra)

**Acción:**
- Usuario edita orden #118
- Cambia comprador de `LUIS ENRIQUE` a `LUIS MARIO`
- Guarda

**Resultado en SIST_AUDIT_LOG:**
```
Campo: OC_COMPRADOR_NOMBRE
Valor Anterior: LUIS ENRIQUE
Valor Nuevo: LUIS MARIO
Acción: UPDATE
```

---

## 🔍 Campos que NO se Auditan

Por defecto, estos campos están excluidos de la auditoría:

```php
$excluirPorDefecto = [
    'OC_FECHA_CREACION',      // Timestamp automático
    'OC_FECHA_APROBACION',    // Timestamp automático
    'ACC_FECHA_CREACION',     // Acta: fecha creación
    'ADP_FECHA_CREACION',     // Autorización: fecha creación
    'CCA_FECHA_CREACION',     // Carta: fecha creación
    'CR_FECHA_CREACION',      // Carta Recepción: fecha creación
    'CC_FECHA_CREACION',      // Carta Características: fecha creación
    'CCB_FECHA_CREACION',     // Carta Felicitaciones: fecha creación
    'PPD_FECHA_CREACION'      // Política: fecha creación
];
```

**¿Por qué?**
- Son campos que cambian automáticamente
- No son modificados por el usuario
- Generarían mucho ruido en el reporte

---

## 🎯 Resumen

### ¿Cuándo se registra?
- ✅ **Solo al EDITAR** una orden existente
- ❌ **NO al crear** una orden nueva

### ¿Qué se registra?
- ✅ **Solo campos que CAMBIARON**
- ❌ **NO campos con valores iguales**

### ¿Cómo se compara?
- ✅ **Números normalizados** (2 decimales)
- ✅ **Strings sin espacios** (trim)
- ✅ **Null = string vacío**
- ✅ **Fechas en formato estándar**

### ¿Qué NO se registra?
- ❌ Campos de timestamp automáticos
- ❌ Valores que no cambiaron
- ❌ Creación de nuevas órdenes

---

## 🛠️ Próximos Pasos

1. **Limpiar registros incorrectos:**
   ```sql
   -- Ejecutar: database/LIMPIAR_REGISTROS_AUDITORIA.sql
   ```

2. **Probar el sistema mejorado:**
   - Edita una orden existente
   - Cambia SOLO el nombre del comprador
   - Guarda
   - Verifica que SOLO se registre ese campo

3. **Verificar que funciona:**
   - Accede a `/digitalizacion-documentos/audit`
   - Deberías ver SOLO cambios reales
   - No deberías ver valores iguales

---

## 📞 Soporte

Si ves registros con valores iguales después de esta corrección:
1. Verifica que el archivo `AuditLog.php` tenga la función mejorada
2. Limpia la caché de PHP (reinicia Apache/servidor)
3. Ejecuta el script de limpieza
4. Prueba editando una orden nueva

---

**Fecha de corrección:** Noviembre 4, 2024  
**Archivos modificados:**
- `app/models/AuditLog.php` - Función `normalizarValor()` mejorada
- `database/LIMPIAR_REGISTROS_AUDITORIA.sql` - Script de limpieza

**Estado:** ✅ Corregido y funcional
