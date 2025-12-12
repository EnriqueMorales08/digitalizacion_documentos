# 📋 Instrucciones de Actualización - 30 de Octubre 2025

## 🎯 Cambios Implementados

### 1. **Separación de Nombre y Apellido del Comprador**
   - Se agregó el campo `OC_COMPRADOR_APELLIDO` en la base de datos
   - El formulario ahora tiene dos campos separados: "Nombre" y "Apellido"
   - Se ajustaron los anchos de los campos para que todo entre en la misma fila

### 2. **Auto-llenado de "Tarjeta a Nombre de"**
   - Ahora combina automáticamente el nombre y apellido del comprador
   - Se actualiza en tiempo real mientras se escribe

### 3. **Auto-selección de Tipo de Documento de Venta**
   - **Persona natural** → Selecciona automáticamente "BOLETA DE VENTA"
   - **P. Natural con RUC** → Selecciona automáticamente "FACTURA DE VENTA"
   - **Persona Jurídica** → Selecciona automáticamente "FACTURA DE VENTA"

---

## 🔧 Pasos para Actualizar la Base de Datos

### Opción 1: Ejecutar desde SQL Server Management Studio (SSMS)

1. Abre **SQL Server Management Studio**
2. Conéctate a tu servidor de base de datos
3. Abre el archivo: `AGREGAR_APELLIDO_COMPRADOR.sql`
4. Ejecuta el script (presiona F5 o haz clic en "Execute")
5. Verifica que aparezca el mensaje: ✅ "Columna OC_COMPRADOR_APELLIDO agregada exitosamente"

### Opción 2: Ejecutar desde línea de comandos

```bash
sqlcmd -S tu_servidor -d FACCARPRUEBA -i "AGREGAR_APELLIDO_COMPRADOR.sql"
```

---

## ✅ Verificación de la Actualización

Ejecuta la siguiente consulta para verificar que el campo se agregó correctamente:

```sql
USE FACCARPRUEBA;
GO

SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'SIST_ORDEN_COMPRA' 
AND COLUMN_NAME = 'OC_COMPRADOR_APELLIDO';
GO
```

**Resultado esperado:**
```
COLUMN_NAME              DATA_TYPE    CHARACTER_MAXIMUM_LENGTH
OC_COMPRADOR_APELLIDO    nvarchar     200
```

---

## 📝 Archivos Modificados

1. **`database/schema_sist.sql`**
   - Agregado campo `OC_COMPRADOR_APELLIDO NVARCHAR(200)`

2. **`app/views/documents/layouts/orden-compra.php`**
   - Separado campo de nombre en dos inputs: nombre y apellido
   - Actualizada función `autoRellenarTarjetaNombre()` para combinar nombre y apellido
   - Agregada función `autoSeleccionarTipoDocumento()` para auto-selección

3. **`database/AGREGAR_APELLIDO_COMPRADOR.sql`** (NUEVO)
   - Script de actualización para agregar el nuevo campo

---

## 🚨 Importante

- **NO se afectan los datos existentes**: El campo se agrega como NULL
- **Compatibilidad hacia atrás**: Los registros antiguos seguirán funcionando
- **El modelo `Document.php` no requiere cambios**: Maneja campos dinámicamente

---

## 🧪 Pruebas Recomendadas

1. ✅ Crear una nueva orden de compra
2. ✅ Verificar que el nombre y apellido se separan correctamente
3. ✅ Verificar que "Tarjeta a Nombre de" se auto-llena con nombre + apellido
4. ✅ Seleccionar "Persona natural" y verificar que se selecciona "BOLETA DE VENTA"
5. ✅ Seleccionar "P. Natural con RUC" y verificar que se selecciona "FACTURA DE VENTA"
6. ✅ Seleccionar "Persona Jurídica" y verificar que se selecciona "FACTURA DE VENTA"
7. ✅ Editar una orden existente y verificar que funciona correctamente

---

## 📞 Soporte

Si encuentras algún problema durante la actualización, verifica:
- Que tienes permisos de ALTER TABLE en la base de datos
- Que la base de datos FACCARPRUEBA existe y está accesible
- Que no hay otras sesiones bloqueando la tabla SIST_ORDEN_COMPRA

---

**Fecha de actualización:** 30 de Octubre de 2025  
**Versión:** 1.0
