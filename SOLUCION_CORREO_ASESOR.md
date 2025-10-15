# 🔧 Solución: Correo no se envía al Asesor

## 🐛 Problema Identificado

Cuando el **RESPONSABLE** aprueba una orden, el correo **NO se está enviando al ASESOR** que creó la orden.

### Causa Raíz

El código intentaba obtener el email del asesor desde `$_SESSION['usuario_email']`, pero:

- Cuando el **ASESOR** crea la orden → Sesión activa: ASESOR ✅
- Cuando el **RESPONSABLE** aprueba la orden → Sesión activa: RESPONSABLE ❌

Por lo tanto, el email que se obtenía era el del **RESPONSABLE**, no el del **ASESOR**.

---

## ✅ Solución Implementada

### 1. Guardar el Email del Asesor al Crear la Orden

**Archivo:** `app/models/Document.php` → Método `guardarOrdenCompra()`

```php
// Guardar el email y nombre del usuario LOGUEADO (asesor que crea la orden)
// Esto es necesario para enviarle el correo cuando se apruebe/rechace la orden
if (isset($_SESSION['usuario_email'])) {
    $data['OC_USUARIO_EMAIL'] = $_SESSION['usuario_email'];
}
if (isset($_SESSION['usuario_nombre_completo'])) {
    $data['OC_USUARIO_NOMBRE'] = $_SESSION['usuario_nombre_completo'];
}
```

### 2. Usar el Email Guardado al Enviar el Correo

**Archivo:** `app/models/Document.php` → Método `enviarCorreoAsesor()`

```php
// ANTES (incorrecto - usaba la sesión actual)
$emailAsesor = $_SESSION['usuario_email'] ?? null;
$nombreAsesor = $_SESSION['usuario_nombre_completo'] ?? 'Asesor';

// AHORA (correcto - usa el email guardado en la orden)
$emailAsesor = $orden['OC_USUARIO_EMAIL'] ?? null;
$nombreAsesor = $orden['OC_USUARIO_NOMBRE'] ?? 'Asesor';
```

---

## ⚠️ IMPORTANTE: Ejecutar Script SQL

**DEBES ejecutar el siguiente script SQL en la base de datos:**

### Ubicación del Script
```
database/add_usuario_email_columns.sql
```

### ¿Qué hace el script?

Agrega dos columnas nuevas a la tabla `SIST_ORDEN_COMPRA`:
- `OC_USUARIO_EMAIL` (NVARCHAR(255)) - Email del asesor que crea la orden
- `OC_USUARIO_NOMBRE` (NVARCHAR(255)) - Nombre del asesor que crea la orden

### Cómo Ejecutar

1. Abre **SQL Server Management Studio (SSMS)**
2. Conéctate al servidor de base de datos
3. Abre el archivo `add_usuario_email_columns.sql`
4. Ejecuta el script (F5)
5. Verifica que aparezcan los mensajes:
   - "Columna OC_USUARIO_EMAIL agregada exitosamente"
   - "Columna OC_USUARIO_NOMBRE agregada exitosamente"

---

## 📋 Flujo Correcto Ahora

### 1. Crear Orden
```
Usuario logueado: ASESOR (email: asesor@interamericana.shop)
↓
Se guarda en la orden:
- OC_USUARIO_EMAIL = asesor@interamericana.shop
- OC_USUARIO_NOMBRE = Nombre del Asesor
↓
Se envía correo al RESPONSABLE
```

### 2. Aprobar Orden
```
Usuario logueado: RESPONSABLE (email: responsable@interamericana.shop)
↓
Se aprueba la orden
↓
Se obtiene el email de la orden: OC_USUARIO_EMAIL
↓
Se envía correo a: asesor@interamericana.shop ✅
```

---

## 🔍 Cómo Verificar que Funciona

### Paso 1: Ejecutar el Script SQL
```sql
-- Verificar que las columnas existan
SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'SIST_ORDEN_COMPRA'
AND COLUMN_NAME IN ('OC_USUARIO_EMAIL', 'OC_USUARIO_NOMBRE');
```

Deberías ver:
```
OC_USUARIO_EMAIL    nvarchar    255
OC_USUARIO_NOMBRE   nvarchar    255
```

### Paso 2: Crear una Orden Nueva
1. Login como **ASESOR** (ejemplo: usuario con email `asesor@test.com`)
2. Crear una orden de compra
3. Verificar en la base de datos:

```sql
SELECT OC_ID, OC_NUMERO_EXPEDIENTE, OC_USUARIO_EMAIL, OC_USUARIO_NOMBRE
FROM SIST_ORDEN_COMPRA
WHERE OC_NUMERO_EXPEDIENTE = '2025100028'; -- Tu número de expediente
```

Deberías ver:
```
OC_USUARIO_EMAIL: asesor@test.com
OC_USUARIO_NOMBRE: Nombre del Asesor
```

### Paso 3: Aprobar la Orden
1. Login como **RESPONSABLE**
2. Aprobar la orden desde el panel de aprobación
3. Verificar los logs de PHP:

```
=== INICIANDO envíoCorreoAsesor ===
Orden ID: 123, Estado: APROBADO
Email del usuario logueado: asesor@test.com (Nombre del Asesor)
=== ENVIANDO CORREO AL ASESOR ===
TO: asesor@test.com
FROM: comunica@interamericana.shop
SUBJECT: 📬 Orden de Compra APROBADA - 2025100028
Correo de notificación enviado exitosamente al asesor: asesor@test.com
```

### Paso 4: Verificar el Correo
- El correo debe llegar a: `asesor@test.com`
- **NO** debe llegar al email del responsable

---

## 📁 Archivos Modificados

1. ✅ `app/models/Document.php`
   - Método `guardarOrdenCompra()` - Guarda email y nombre del asesor
   - Método `enviarCorreoAsesor()` - Usa email guardado en la orden

2. ✅ `database/add_usuario_email_columns.sql`
   - Script SQL para agregar columnas (ya existía)

---

## ⚠️ Nota Importante

**Las órdenes creadas ANTES de ejecutar el script SQL:**
- NO tendrán `OC_USUARIO_EMAIL` ni `OC_USUARIO_NOMBRE`
- NO se les podrá enviar correo al asesor
- Solución: Crear órdenes nuevas después de ejecutar el script

**Las órdenes creadas DESPUÉS de ejecutar el script SQL:**
- SÍ tendrán `OC_USUARIO_EMAIL` y `OC_USUARIO_NOMBRE`
- SÍ se les enviará correo al asesor correcto ✅

---

## 🎯 Resumen

| Antes | Ahora |
|-------|-------|
| Email del responsable (incorrecto) | Email del asesor (correcto) ✅ |
| Usaba `$_SESSION['usuario_email']` | Usa `$orden['OC_USUARIO_EMAIL']` |
| No guardaba email en la orden | Guarda email al crear la orden |
| Requiere ejecutar script SQL | ✅ Script SQL disponible |

---

**Fecha:** Octubre 2025  
**Versión:** 3.0 (Final)
