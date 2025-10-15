# 📧 Flujo de Correos - Sistema de Aprobación

## Configuración de Correos

**Todos los correos se envían desde:**
- **From:** `comunica@interamericana.shop`
- **From Name:** `Sistema de Digitalización Interamericana`
- **API:** `http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php`

---

## 📨 Flujo Completo

### 1️⃣ Creación de Orden de Compra

**Quién crea:** Usuario logueado (Asesor)

**Qué se guarda en la BD:**
- Todos los datos de la orden
- `OC_EMAIL_CENTRO_COSTO` → Email del responsable (del JSON `centros_costo_backup.json`)
- `OC_NOMBRE_RESPONSABLE` → Nombre del responsable

**Correo enviado a:** Responsable (email del JSON)

**Ejemplo:**
- Agencia: LIMA
- Responsable: ENZO VEGAS
- Email destino: `moralesagurto.0803@gmail.com`

**Contenido del correo:**
```
Asunto: 📬 Orden de Compra Pendiente de Aprobación - [NUMERO_EXPEDIENTE]

Contenido:
- Número de Expediente
- Cliente
- Vehículo (Marca + Modelo)
- Chasis
- Precio
- Asesor
- Estado: ⏳ Pendiente de aprobación
- Botón: 👁️ Ver y Aprobar Orden
```

**Link en el correo:**
```
http://190.238.78.104:3800/digitalizacion-documentos/aprobacion/panel?id=[ORDEN_ID]
```

---

### 2️⃣ Panel de Aprobación

**Quién accede:** Responsable (hace clic en el link del correo)

**Qué ve:**
- Número de Expediente
- Estado (PENDIENTE/APROBADO/RECHAZADO)
- Cliente
- Asesor de Venta
- Marca del Vehículo
- Modelo del Vehículo
- Chasis
- Precio de Venta
- Fecha de Aprobación
- Campo de Observaciones (opcional)

**Acciones disponibles:**
- ✓ Aprobar Orden
- ✗ Rechazar Orden

---

### 3️⃣ Aprobación/Rechazo de Orden

**Quién aprueba:** Responsable

**Qué se actualiza en la BD:**
- `OC_ESTADO_APROBACION` → 'APROBADO' o 'RECHAZADO'
- `OC_FECHA_APROBACION` → Fecha y hora actual
- `OC_OBSERVACIONES_APROBACION` → Observaciones (si las hay)

**Correo enviado a:** Usuario que creó la orden (Asesor logueado)

**De dónde se obtiene el email:**
- Tabla: `firmas`
- Columna: `firma_mail`
- Se obtiene de la sesión: `$_SESSION['usuario_email']`

**Contenido del correo:**
```
Asunto: 📬 Orden de Compra APROBADA/RECHAZADA - [NUMERO_EXPEDIENTE]

Contenido:
Hola [NOMBRE_ASESOR],

Tu orden de compra ha sido APROBADA/RECHAZADA

- Número de Expediente
- Cliente
- Vehículo
- Estado: ✅ APROBADA / ❌ RECHAZADA
- Observaciones (si las hay)
```

---

### 4️⃣ Después de Aprobar

**En el panel de aprobación:**
- El badge cambia a: ✅ APROBADO
- Aparece el botón: 🖨️ Imprimir Documentos
- Al hacer clic, redirige a: `/digitalizacion-documentos/expedientes/ver?id=[ORDEN_ID]`

---

## 🔍 Resumen del Flujo

```
1. ASESOR crea orden
   ↓
2. Se envía correo a RESPONSABLE (email del JSON centros_costo_backup.json)
   ↓
3. RESPONSABLE hace clic en el link
   ↓
4. RESPONSABLE ve el panel con todos los datos
   ↓
5. RESPONSABLE aprueba/rechaza
   ↓
6. Se envía correo a ASESOR (email de tabla firmas.firma_mail)
   ↓
7. RESPONSABLE ve botón de imprimir (si aprobó)
```

---

## 📋 Ejemplo Completo

### Escenario:
- **Asesor logueado:** Juan Pérez (email: `juan.perez@interamericana.shop`)
- **Agencia seleccionada:** LIMA
- **Responsable seleccionado:** ENZO VEGAS
- **Email responsable (del JSON):** `moralesagurto.0803@gmail.com`

### Paso 1: Crear Orden
```json
{
  "to": "moralesagurto.0803@gmail.com",
  "subject": "📬 Orden de Compra Pendiente de Aprobación - 2025100001",
  "html": "...",
  "from": "comunica@interamericana.shop",
  "from_name": "Sistema de Digitalización Interamericana"
}
```

### Paso 2: Aprobar Orden
```json
{
  "to": "juan.perez@interamericana.shop",
  "subject": "📬 Orden de Compra APROBADA - 2025100001",
  "html": "...",
  "from": "comunica@interamericana.shop",
  "from_name": "Sistema de Digitalización Interamericana"
}
```

---

## ⚙️ Configuración Técnica

### Variables de Sesión Necesarias

**Al hacer login (tabla firmas):**
```php
$_SESSION['usuario_email'] = $row['firma_mail'];
$_SESSION['usuario_nombre'] = $row['firma_nombre'];
$_SESSION['usuario_apellido'] = $row['firma_apellido'];
$_SESSION['usuario_nombre_completo'] = trim($row['firma_nombre'] . ' ' . $row['firma_apellido']);
```

### Campos en la BD (SIST_ORDEN_COMPRA)

**Para el correo al responsable:**
- `OC_EMAIL_CENTRO_COSTO` → Email del responsable (del JSON)
- `OC_NOMBRE_RESPONSABLE` → Nombre del responsable

**Para el correo al asesor:**
- Se usa `$_SESSION['usuario_email']` (NO se guarda en la BD)

---

## ✅ Verificación

Para verificar que todo funciona:

1. ✅ Login como asesor
2. ✅ Crear orden seleccionando LIMA → ENZO VEGAS
3. ✅ Verificar que llegue correo a `moralesagurto.0803@gmail.com`
4. ✅ Hacer clic en el link del correo
5. ✅ Verificar que se vea el panel con todos los datos (chasis, precio, etc.)
6. ✅ Aprobar la orden
7. ✅ Verificar que llegue correo al email del asesor logueado
8. ✅ Verificar que el badge cambie a APROBADO
9. ✅ Verificar que aparezca el botón de imprimir

---

## 🐛 Debugging

**Ver logs de correo:**
```php
error_log("=== ENVIANDO CORREO ===");
error_log("TO: " . $emailDestino);
error_log("FROM: comunica@interamericana.shop");
error_log("SUBJECT: " . $subject);
```

**Ubicación de logs:**
- PHP error_log (configurado en php.ini)
- Verificar con: `tail -f /path/to/error.log`

---

**Última actualización:** Octubre 2025  
**Versión:** 2.0
