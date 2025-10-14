# 📧 Proceso de Aprobación de Orden de Compra

## 🔄 Flujo Completo del Sistema

### **Paso 1: Asesor Guarda la Orden de Compra**

1. El asesor ingresa al sistema con su usuario y contraseña (tabla `firmas` en BD `DOC_DIGITALES`)
2. Al hacer login, el sistema guarda en sesión:
   - `$_SESSION['usuario_email']` → Email del asesor (ej: evegas@interamericananorte.com)
   - `$_SESSION['usuario_nombre']` → Nombre (ej: Eduardo)
   - `$_SESSION['usuario_apellido']` → Apellido (ej: Vegas García)
   - `$_SESSION['usuario_nombre_completo']` → "Eduardo Vegas García"

3. El asesor llena el formulario de orden de compra:
   - Selecciona **Agencia** (ej: CHICLAYO)
   - Selecciona **Responsable** (ej: NANCY VILCA BENAVIDES)
   - Selecciona **Centro de Costo** (ej: 02490)
   - El sistema guarda automáticamente el email del centro de costo (ej: nvilca@interamericananorte.com)

4. Al hacer clic en **"Guardar Orden de Compra"**:
   - Se guarda en la BD con estado `OC_ESTADO_APROBACION = 'PENDIENTE'`
   - Se genera un número de expediente automático

---

### **Paso 2: Sistema Envía Correo al Centro de Costo**

**Método:** `enviarCorreoAprobacion()` en `Document.php` (línea 201)

**Destinatario:** Email del centro de costo seleccionado (guardado en `OC_EMAIL_CENTRO_COSTO`)

**Contenido del correo:**
```
Asunto: Orden de Compra Pendiente - [NUMERO_EXPEDIENTE]

Hola,

Tienes una nueva orden de compra pendiente de aprobación:

- Número de Expediente: [NUMERO]
- Cliente: [NOMBRE_CLIENTE]
- Vehículo: [MARCA] [MODELO]
- Asesor: [NOMBRE_ASESOR]
- Estado: Pendiente de aprobación

[Botón: Ver Orden Pendiente]
```

**Link del botón:** 
```
http://190.238.78.104:3800/digitalizacion-documentos/aprobacion/panel?id=[OC_ID]
```

**API utilizada:**
```
POST http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php

Headers:
- Content-Type: application/json

Body:
{
  "to": "nvilca@interamericananorte.com",
  "subject": "Orden de Compra Pendiente - EXP-2025-001",
  "html": "[HTML del correo]",
  "from": "noreply@interamericananorte.com",
  "from_name": "Sistema de Digitalización - Interamericana"
}
```

---

### **Paso 3: Responsable del Centro de Costo Abre el Link**

1. El responsable recibe el correo
2. Hace clic en **"Ver Orden Pendiente"**
3. Se abre el **Panel de Aprobación** (`/aprobacion/panel?id=X`)

**Panel muestra:**
- Número de expediente
- Cliente
- Asesor de venta
- Marca del vehículo
- Modelo del vehículo
- Versión
- Precio de venta
- Campo de observaciones (opcional)
- Botones: **Aprobar** / **Rechazar**

---

### **Paso 4: Responsable Aprueba o Rechaza**

1. El responsable revisa los datos
2. Opcionalmente escribe observaciones
3. Hace clic en **"Aprobar"** o **"Rechazar"**
4. Confirma la acción

**Proceso en el backend:**

**Método:** `procesarAprobacion()` en `Document.php` (línea 506)

1. Actualiza la BD:
```sql
UPDATE SIST_ORDEN_COMPRA 
SET OC_ESTADO_APROBACION = 'APROBADO' (o 'RECHAZADO'),
    OC_FECHA_APROBACION = GETDATE(),
    OC_OBSERVACIONES_APROBACION = '[observaciones]'
WHERE OC_ID = [id]
```

2. Llama a `enviarCorreoAsesor()` (línea 546)

---

### **Paso 5: Sistema Envía Correo al Asesor**

**Método:** `enviarCorreoAsesor()` en `Document.php` (línea 546)

**Destinatario:** Email del asesor obtenido de la tabla `firmas` en BD `DOC_DIGITALES`

**Búsqueda del asesor:**
```sql
SELECT TOP 1 firma_mail, firma_nombre, firma_apellido 
FROM firmas 
WHERE CONCAT(firma_nombre, ' ', firma_apellido) LIKE '%[NOMBRE_ASESOR]%' 
   OR usuario LIKE '%[NOMBRE_ASESOR]%'
```

**Contenido del correo:**
```
Asunto: Orden de Compra APROBADA - [NUMERO_EXPEDIENTE]

Hola Eduardo Vegas García,

Tu orden de compra ha sido APROBADA

╔════════════════════════════════╗
║ Número de Expediente: EXP-001  ║
║ Cliente: Juan Pérez            ║
║ Vehículo: CHERY PLATEAU        ║
║ Estado: APROBADA ✓             ║
║ Observaciones: [si hay]        ║
╚════════════════════════════════╝
```

**API utilizada:** La misma API de correos

---

## 🔑 Datos Importantes

### **Tabla `firmas` en BD `DOC_DIGITALES`:**
```
- usuario          → Nombre de usuario para login
- password         → Contraseña para login
- firma_nombre     → Nombre del usuario (ej: Eduardo)
- firma_apellido   → Apellido del usuario (ej: Vegas García)
- firma_mail       → Email del usuario (ej: evegas@interamericananorte.com)
- firma_data       → Ruta de la imagen de firma
```

### **Tabla `SIST_ORDEN_COMPRA` en BD `FACCARPRUEBA`:**
```
- OC_AGENCIA                    → Agencia seleccionada
- OC_NOMBRE_RESPONSABLE         → Nombre del responsable
- OC_CENTRO_COSTO               → Código del centro de costo
- OC_EMAIL_CENTRO_COSTO         → Email del centro de costo
- OC_ESTADO_APROBACION          → PENDIENTE / APROBADO / RECHAZADO
- OC_FECHA_APROBACION           → Fecha de aprobación/rechazo
- OC_OBSERVACIONES_APROBACION   → Observaciones del responsable
```

---

## 📊 Diagrama de Flujo

```
┌─────────────────┐
│ Asesor hace     │
│ Login           │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Sistema guarda  │
│ datos en sesión │
│ (nombre, email) │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Asesor llena    │
│ Orden de Compra │
│ y selecciona    │
│ Centro de Costo │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Guarda Orden    │
│ Estado:PENDIENTE│
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│ 📧 Correo 1:            │
│ → Centro de Costo       │
│ (nvilca@...com)         │
│ Con link al panel       │
└────────┬────────────────┘
         │
         ▼
┌─────────────────┐
│ Responsable     │
│ abre link       │
│ y revisa orden  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Aprueba/Rechaza │
│ la orden        │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Actualiza BD    │
│ Estado:APROBADO │
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│ 📧 Correo 2:            │
│ → Asesor                │
│ (evegas@...com)         │
│ "Hola Eduardo..."       │
└─────────────────────────┘
```

---

## 🧪 Ejemplo Real

### **Caso: Eduardo Vegas García crea una orden**

1. **Login:**
   - Usuario: `evega`
   - Password: `73885481`
   - Sistema guarda: `$_SESSION['usuario_nombre_completo'] = "Eduardo Vegas García"`
   - Sistema guarda: `$_SESSION['usuario_email'] = "evegas@interamericananorte.com"`

2. **Llena orden:**
   - Agencia: `CHICLAYO`
   - Responsable: `NANCY VILCA BENAVIDES`
   - Centro de Costo: `02490`
   - Email guardado: `nvilca@interamericananorte.com`

3. **Guarda orden:**
   - Se crea expediente: `EXP-2025-001`
   - Se envía correo a: `nvilca@interamericananorte.com`

4. **Nancy Vilca aprueba:**
   - Abre link del correo
   - Revisa datos
   - Aprueba con observación: "Todo correcto"

5. **Eduardo recibe correo:**
   - Asunto: "Orden de Compra APROBADA - EXP-2025-001"
   - Contenido: "Hola Eduardo Vegas García, Tu orden de compra ha sido APROBADA"

---

## 🔧 Archivos Involucrados

1. **`app/models/Document.php`**
   - `verificarFirma()` → Guarda datos en sesión
   - `guardarOrdenCompra()` → Guarda orden y envía correo 1
   - `enviarCorreoAprobacion()` → Envía correo al centro de costo
   - `procesarAprobacion()` → Actualiza estado y envía correo 2
   - `enviarCorreoAsesor()` → Envía correo al asesor

2. **`app/controllers/AprobacionController.php`**
   - `panel()` → Muestra panel de aprobación
   - `procesar()` → Procesa aprobación/rechazo

3. **`app/views/aprobacion/panel.php`**
   - Vista del panel de aprobación

4. **`config/database.php`**
   - `getDocDigitalesConnection()` → Conexión a BD DOC_DIGITALES

---

## ✅ Resumen

**Correo 1:** Centro de Costo recibe notificación de orden pendiente
**Correo 2:** Asesor recibe notificación de aprobación/rechazo con su nombre completo

**Ambos correos usan la misma API:**
```
http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php
```

**Sin token** (como solicitaste)
