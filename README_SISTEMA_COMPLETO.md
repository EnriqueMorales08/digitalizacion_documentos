# 🚗 Sistema de Digitalización de Documentos - Interamericana Norte

## ✅ Sistema Completo Implementado

### 📋 Funcionalidades Principales

#### 1. **Sistema de Login** 🔐
- Login con credenciales de la tabla `firmas` en BD `DOC_DIGITALES`
- Sesión persistente con datos del usuario
- Saludo personalizado con nombre completo
- Botón de cerrar sesión
- Protección de todas las rutas (excepto login)

#### 2. **Orden de Compra con Aprobación** 📄
- Selección de Agencia, Responsable y Centro de Costo (datos desde Google Sheets API)
- Autocompletado de datos del vehículo por chasis
- Autocompletado de datos de mantenimiento
- Estado inicial: **PENDIENTE**

#### 3. **Sistema de Aprobación** ✅❌
- Correo automático al Centro de Costo al guardar orden
- Panel de aprobación con datos completos
- Botones: Aprobar / Rechazar
- Campo de observaciones
- Correo automático al Asesor con resultado

#### 4. **Bloqueo de Impresión** 🔒
- Solo se puede imprimir si el estado es **APROBADO**
- Indicador visual del estado en lista de expedientes
- Botón de impresión deshabilitado si no está aprobado
- Mensaje de error si se intenta imprimir sin aprobación

---

## 🔄 Flujo Completo del Sistema

### **Paso 1: Login**
```
http://localhost/digitalizacion-documentos/auth/login

Usuario: evega
Password: 73885481
```

Al hacer login:
- Se guardan datos en sesión: nombre, apellido, email, firma
- Redirige a página de bienvenida
- Muestra: "Bienvenido Eduardo Vegas García"

---

### **Paso 2: Crear Orden de Compra**

1. Click en **"Generar Orden de Compra"**
2. Seleccionar:
   - **Agencia:** CHICLAYO
   - **Responsable:** NANCY VILCA BENAVIDES
   - **Centro de Costo:** 02490 - VTA VEH EIA
3. Ingresar chasis (ej: `LGXX3267703X03407`)
   - Se autocompletan datos del vehículo
   - Se autocompletan datos de mantenimiento
4. Llenar demás campos
5. Click en **"Guardar Orden de Compra"**

**Resultado:**
- Se guarda con estado: `PENDIENTE`
- Se envía correo a: `nvilca@interamericananorte.com`

---

### **Paso 3: Aprobación (Centro de Costo)**

1. Nancy Vilca recibe correo:
```
Asunto: Orden de Compra Pendiente - EXP-2025-001

Hola,

Tienes una nueva orden de compra pendiente de aprobación:

- Número de Expediente: EXP-2025-001
- Cliente: Juan Pérez
- Vehículo: CHERY PLATEAU
- Asesor: Eduardo Vegas García
- Estado: Pendiente de aprobación

[Ver Orden Pendiente]
```

2. Click en **"Ver Orden Pendiente"**
3. Se abre panel con todos los datos
4. Escribe observaciones (opcional)
5. Click en **"Aprobar"** o **"Rechazar"**

**Resultado:**
- Estado cambia a: `APROBADO` o `RECHAZADO`
- Se envía correo a: `evegas@interamericananorte.com`

---

### **Paso 4: Notificación al Asesor**

Eduardo Vegas García recibe correo:
```
Asunto: Orden de Compra APROBADA - EXP-2025-001

Hola Eduardo Vegas García,

Tu orden de compra ha sido APROBADA

╔════════════════════════════════╗
║ Número de Expediente: EXP-001  ║
║ Cliente: Juan Pérez            ║
║ Vehículo: CHERY PLATEAU        ║
║ Estado: APROBADA ✓             ║
║ Observaciones: Todo correcto   ║
╚════════════════════════════════╝
```

---

### **Paso 5: Imprimir Documentos**

1. Ir a **"Gestionar Expedientes"**
2. Ver lista de expedientes con estado:
   - 🟢 **APROBADO** → Botón "Imprimir" habilitado
   - 🟡 **PENDIENTE** → Botón "Imprimir" deshabilitado
   - 🔴 **RECHAZADO** → Botón "Imprimir" deshabilitado

3. Si está **APROBADO**:
   - Click en "Imprimir Todo" → ✅ Funciona
   - Click en documento individual → ✅ Funciona

4. Si está **PENDIENTE** o **RECHAZADO**:
   - Click en "Imprimir" → ❌ Mensaje de error
   - "No se puede imprimir. La orden está PENDIENTE de aprobación."

---

## 🗄️ Estructura de Base de Datos

### **BD: FACCARPRUEBA**
```sql
-- Tabla principal
SIST_ORDEN_COMPRA
  - OC_ID (PK)
  - OC_NUMERO_EXPEDIENTE
  - OC_AGENCIA
  - OC_NOMBRE_RESPONSABLE
  - OC_CENTRO_COSTO
  - OC_EMAIL_CENTRO_COSTO
  - OC_ESTADO_APROBACION (PENDIENTE/APROBADO/RECHAZADO)
  - OC_FECHA_APROBACION
  - OC_OBSERVACIONES_APROBACION
  - ... (demás campos)
```

### **BD: DOC_DIGITALES**
```sql
-- Tabla de usuarios
firmas
  - usuario (PK)
  - password
  - firma_nombre
  - firma_apellido
  - firma_mail
  - firma_data
```

---

## 📧 API de Correos

**Endpoint:**
```
POST http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php
```

**Headers:**
```json
{
  "Content-Type": "application/json"
}
```

**Body:**
```json
{
  "to": "destinatario@example.com",
  "subject": "Asunto del correo",
  "html": "<html>...</html>",
  "from": "noreply@interamericananorte.com",
  "from_name": "Sistema de Digitalización"
}
```

**Sin token** (como solicitaste)

---

## 🔑 Usuarios de Prueba

| Usuario | Password  | Nombre Completo           | Email                              |
|---------|-----------|---------------------------|------------------------------------|
| evega   | 73885481  | Eduardo Vegas García      | evegas@interamericananorte.com     |
| cmond   | 40753711  | Cecilia Rosemary Mondragon| cmondragon@interamericananorte.com |
| EMORA   | 73474990  | Enrique Javier Morales    | -                                  |

---

## 📁 Archivos Principales

### **Autenticación:**
- `app/controllers/AuthController.php` - Controlador de login/logout
- `app/views/auth/login.php` - Página de login
- `config/routes.php` - Rutas protegidas

### **Aprobación:**
- `app/controllers/AprobacionController.php` - Controlador de aprobación
- `app/views/aprobacion/panel.php` - Panel de aprobación
- `app/models/Document.php` - Métodos de aprobación y correos

### **Orden de Compra:**
- `app/views/documents/layouts/orden-compra.php` - Formulario con 3 selects
- `app/models/Document.php` - Métodos de centros de costo

### **Expedientes:**
- `app/controllers/ExpedienteController.php` - Bloqueo de impresión
- `app/views/expedientes/index.php` - Lista con estados

---

## 🧪 Cómo Probar

### **1. Ejecutar SQL:**
```sql
-- Ejecutar: database/alter_add_aprobacion.sql
USE FACCARPRUEBA;
GO

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_AGENCIA NVARCHAR(100);
ALTER TABLE SIST_ORDEN_COMPRA ADD OC_NOMBRE_RESPONSABLE NVARCHAR(200);
ALTER TABLE SIST_ORDEN_COMPRA ADD OC_CENTRO_COSTO NVARCHAR(50);
ALTER TABLE SIST_ORDEN_COMPRA ADD OC_EMAIL_CENTRO_COSTO NVARCHAR(150);
ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ESTADO_APROBACION NVARCHAR(20) DEFAULT 'PENDIENTE';
ALTER TABLE SIST_ORDEN_COMPRA ADD OC_FECHA_APROBACION DATETIME;
ALTER TABLE SIST_ORDEN_COMPRA ADD OC_OBSERVACIONES_APROBACION NVARCHAR(500);
GO
```

### **2. Acceder al sistema:**
```
http://localhost/digitalizacion-documentos/
```

### **3. Hacer login:**
```
Usuario: evega
Password: 73885481
```

### **4. Crear orden de compra:**
- Seleccionar centro de costo
- Llenar datos
- Guardar

### **5. Revisar correo del centro de costo:**
- Buscar en bandeja de entrada: `nvilca@interamericananorte.com`
- Abrir link del correo

### **6. Aprobar/Rechazar:**
- Revisar datos
- Aprobar o rechazar

### **7. Revisar correo del asesor:**
- Buscar en bandeja de entrada: `evegas@interamericananorte.com`
- Ver notificación

### **8. Intentar imprimir:**
- Ir a expedientes
- Ver estado
- Intentar imprimir

---

## ✅ Checklist de Funcionalidades

- [x] Sistema de login con BD DOC_DIGITALES
- [x] Saludo personalizado con nombre + apellido
- [x] Botón de cerrar sesión
- [x] Protección de rutas
- [x] 3 selects en cascada (Agencia → Responsable → Centro de Costo)
- [x] Datos desde Google Sheets API
- [x] Correo al centro de costo al guardar
- [x] Panel de aprobación/rechazo
- [x] Correo al asesor con resultado
- [x] Bloqueo de impresión si no está aprobado
- [x] Indicador visual de estado en expedientes
- [x] Botón de impresión deshabilitado
- [x] Mensaje de error al intentar imprimir sin aprobación

---

## 🎉 ¡Sistema Completo y Listo para Usar!

Todas las funcionalidades solicitadas han sido implementadas y probadas.
