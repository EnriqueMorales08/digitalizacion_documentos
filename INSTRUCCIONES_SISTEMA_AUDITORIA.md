# 📊 Sistema de Auditoría - Instrucciones de Uso

## 🎯 Descripción General

El sistema de auditoría permite a los **ADMINISTRADORES** monitorear todos los cambios que realizan los **ASESORES** (usuarios con rol USER) en los documentos del sistema. Cada vez que un asesor modifica una orden de compra, el sistema registra automáticamente:

- ✅ **Qué documento** se modificó
- ✅ **Qué campos específicos** cambiaron
- ✅ **Valor anterior** y **valor nuevo** de cada campo
- ✅ **Quién** hizo el cambio (usuario, nombre, email, rol)
- ✅ **Cuándo** se hizo el cambio (fecha y hora exacta)
- ✅ **Desde dónde** se hizo (dirección IP)

---

## 📋 Tabla de Base de Datos

### Nombre de la tabla: `SIST_AUDIT_LOG`

### Campos principales:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `AUDIT_ID` | INT | ID único del registro de auditoría |
| `AUDIT_TIMESTAMP` | DATETIME | Fecha y hora del cambio |
| `AUDIT_USER_ID` | NVARCHAR(100) | Usuario que realizó el cambio |
| `AUDIT_USER_NAME` | NVARCHAR(200) | Nombre completo del usuario |
| `AUDIT_USER_EMAIL` | NVARCHAR(255) | Email del usuario |
| `AUDIT_USER_ROLE` | NVARCHAR(50) | Rol del usuario (USER/ADMIN) |
| `AUDIT_DOCUMENT_TYPE` | NVARCHAR(100) | Tipo de documento (ORDEN_COMPRA, ACTA, etc.) |
| `AUDIT_DOCUMENT_ID` | INT | ID del documento modificado |
| `AUDIT_ORDEN_ID` | INT | ID de la orden de compra relacionada |
| `AUDIT_NUMERO_EXPEDIENTE` | NVARCHAR(50) | Número de expediente |
| `AUDIT_ACTION` | NVARCHAR(50) | Acción realizada (INSERT, UPDATE, DELETE) |
| `AUDIT_FIELD_NAME` | NVARCHAR(200) | Nombre del campo modificado |
| `AUDIT_OLD_VALUE` | NVARCHAR(MAX) | Valor anterior del campo |
| `AUDIT_NEW_VALUE` | NVARCHAR(MAX) | Valor nuevo del campo |
| `AUDIT_IP_ADDRESS` | NVARCHAR(50) | Dirección IP del usuario |
| `AUDIT_SESSION_ID` | NVARCHAR(100) | ID de sesión |
| `AUDIT_DESCRIPTION` | NVARCHAR(500) | Descripción adicional |

---

## 🔧 Instalación

### 1. Crear la tabla en SQL Server

Ejecuta el script actualizado `database/schema_sist.sql` en tu base de datos SQL Server. El script incluye:

```sql
CREATE TABLE SIST_AUDIT_LOG (
    AUDIT_ID INT IDENTITY(1,1) PRIMARY KEY,
    AUDIT_TIMESTAMP DATETIME DEFAULT GETDATE(),
    -- ... resto de campos
);

-- Índices para mejorar rendimiento
CREATE INDEX IDX_AUDIT_TIMESTAMP ON SIST_AUDIT_LOG(AUDIT_TIMESTAMP DESC);
CREATE INDEX IDX_AUDIT_USER_ID ON SIST_AUDIT_LOG(AUDIT_USER_ID);
CREATE INDEX IDX_AUDIT_ORDEN_ID ON SIST_AUDIT_LOG(AUDIT_ORDEN_ID);
-- ... más índices
```

### 2. Verificar archivos creados

Asegúrate de que existan estos archivos:

- ✅ `app/models/AuditLog.php` - Modelo para gestionar auditoría
- ✅ `app/controllers/AuditController.php` - Controlador para reportes
- ✅ `app/views/audit/index.php` - Vista del reporte
- ✅ Rutas agregadas en `config/routes.php`

---

## 🚀 Cómo Usar el Sistema

### Para Administradores

#### 1. Acceder al Reporte de Auditoría

**URL:** `http://tu-servidor/digitalizacion-documentos/audit`

**Requisitos:**
- Debes estar logueado
- Tu usuario debe tener rol `ADMIN` en la tabla `firmas` de la BD `DOC_DIGITALES`

Si intentas acceder sin ser admin, serás redirigido al inicio con un mensaje de error.

#### 2. Usar los Filtros de Búsqueda

El reporte incluye varios filtros para encontrar cambios específicos:

- **Fecha Desde / Fecha Hasta**: Buscar cambios en un rango de fechas
- **Usuario**: Filtrar por un asesor específico
- **Nº Expediente**: Buscar cambios en un expediente específico
- **ID Orden**: Buscar por ID de orden de compra
- **Tipo Documento**: Filtrar por tipo (Orden de Compra, Acta, Carta)

**Ejemplo de uso:**
```
Fecha Desde: 2024-11-01
Fecha Hasta: 2024-11-04
Usuario: [Seleccionar asesor]
```

Haz clic en **"Buscar"** para aplicar los filtros.

#### 3. Ver los Resultados

La tabla muestra:
- **Fecha/Hora**: Cuándo se hizo el cambio
- **Usuario**: Quién lo hizo (nombre y usuario)
- **Rol**: Si era USER o ADMIN
- **Nº Expediente**: Expediente modificado
- **Acción**: UPDATE, INSERT o DELETE
- **Campo**: Qué campo se modificó (ej: `OC_PRECIO_VENTA`)
- **Valor Anterior**: El valor que tenía antes (en rojo, tachado)
- **Valor Nuevo**: El nuevo valor (en verde, negrita)
- **IP**: Dirección IP desde donde se hizo el cambio

#### 4. Exportar a CSV

Haz clic en el botón **"Exportar CSV"** para descargar todos los registros filtrados en formato Excel/CSV.

El archivo incluirá:
- Todos los campos de auditoría
- Nombre del archivo: `auditoria_YYYY-MM-DD_HHMMSS.csv`
- Compatible con Excel (UTF-8 con BOM)

#### 5. Paginación

- El reporte muestra **50 registros por página**
- Usa los botones de navegación en la parte inferior para moverte entre páginas
- Las estadísticas en la parte superior muestran el total de registros

---

## 🔍 Ejemplos de Uso

### Ejemplo 1: Ver qué cambió un asesor hoy

1. Accede a `/digitalizacion-documentos/audit`
2. En **"Fecha Desde"** selecciona la fecha de hoy
3. En **"Usuario"** selecciona el asesor
4. Haz clic en **"Buscar"**

Verás todos los cambios que ese asesor hizo hoy.

### Ejemplo 2: Auditar un expediente específico

1. Accede a `/digitalizacion-documentos/audit`
2. En **"Nº Expediente"** ingresa el número (ej: `2024110001`)
3. Haz clic en **"Buscar"**

Verás el historial completo de cambios de ese expediente.

### Ejemplo 3: Ver cambios en un rango de fechas

1. Accede a `/digitalizacion-documentos/audit`
2. **"Fecha Desde"**: `2024-11-01`
3. **"Fecha Hasta"**: `2024-11-30`
4. Haz clic en **"Buscar"**

Verás todos los cambios del mes de noviembre.

---

## 🔒 Seguridad

### Restricciones de Acceso

- ✅ **Solo ADMIN** puede ver los reportes de auditoría
- ✅ Los asesores (USER) **NO** pueden ver ni acceder a `/audit`
- ✅ Si un USER intenta acceder, será redirigido automáticamente
- ✅ La verificación se hace en el constructor del `AuditController`

### Qué se Registra Automáticamente

El sistema registra cambios **solo cuando se actualiza** una orden de compra existente:

- ✅ Se comparan los valores anteriores con los nuevos
- ✅ Solo se registran los campos que **realmente cambiaron**
- ✅ Los campos excluidos (timestamps, IDs auto-generados) NO se auditan
- ✅ Si la auditoría falla, **NO afecta** la operación principal

### Campos Excluidos de Auditoría

Por defecto, estos campos NO se auditan (para evitar ruido):
- `OC_FECHA_CREACION`
- `OC_FECHA_APROBACION`
- `ACC_FECHA_CREACION`
- Y otros timestamps automáticos

---

## 📊 Estadísticas en el Dashboard

En la parte superior del reporte verás 4 tarjetas con estadísticas:

1. **Total de Registros**: Cantidad total de cambios registrados (con filtros aplicados)
2. **Usuarios Activos**: Cantidad de usuarios que han hecho cambios
3. **Páginas**: Número total de páginas de resultados
4. **Página Actual**: En qué página estás navegando

---

## 🛠️ Mantenimiento

### Limpiar Logs Antiguos

Si la tabla crece mucho, puedes limpiar logs antiguos con este query:

```sql
-- Eliminar logs de más de 1 año
DELETE FROM SIST_AUDIT_LOG 
WHERE AUDIT_TIMESTAMP < DATEADD(YEAR, -1, GETDATE());
```

**Recomendación:** Ejecutar esto cada 6 meses o cuando la tabla supere 100,000 registros.

### Optimizar Rendimiento

Los índices ya están creados para optimizar las consultas más comunes:
- Por fecha (DESC para ver los más recientes primero)
- Por usuario
- Por orden ID
- Por tipo de documento
- Por número de expediente

---

## 🐛 Solución de Problemas

### Problema: No veo el botón de Auditoría

**Solución:** Verifica que tu usuario tenga rol `ADMIN` en la tabla `firmas`:

```sql
SELECT usuario, rol FROM firmas WHERE usuario = 'tu_usuario';
```

Si dice `USER`, cámbialo a `ADMIN`:

```sql
UPDATE firmas SET rol = 'ADMIN' WHERE usuario = 'tu_usuario';
```

### Problema: No se registran cambios

**Verifica:**
1. Que la tabla `SIST_AUDIT_LOG` exista
2. Revisa los logs de PHP (error_log) para ver si hay errores
3. Asegúrate de que estás **editando** una orden existente (no creando una nueva)

### Problema: Error al exportar CSV

**Solución:** Verifica que PHP tenga permisos de escritura y que la función `fputcsv` esté habilitada.

---

## 📞 Soporte

Si tienes dudas o problemas:
1. Revisa los logs de PHP en tu servidor
2. Verifica que todos los archivos estén en su lugar
3. Asegúrate de que la tabla `SIST_AUDIT_LOG` exista en SQL Server

---

## ✅ Checklist de Implementación

- [ ] Ejecutar `schema_sist.sql` para crear la tabla `SIST_AUDIT_LOG`
- [ ] Verificar que existan los archivos:
  - [ ] `app/models/AuditLog.php`
  - [ ] `app/controllers/AuditController.php`
  - [ ] `app/views/audit/index.php`
- [ ] Verificar que las rutas estén en `config/routes.php`
- [ ] Asignar rol `ADMIN` a los usuarios que deben ver reportes
- [ ] Probar acceso a `/digitalizacion-documentos/audit`
- [ ] Hacer una prueba editando una orden y verificar que se registre

---

## 🎉 ¡Listo!

El sistema de auditoría está completamente funcional. Los administradores ahora pueden monitorear todas las acciones de los asesores con fecha, hora y detalles completos de cada cambio.
