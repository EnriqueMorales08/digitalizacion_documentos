# 📋 Sistema de Número de Expediente - Instrucciones de Instalación

## 🎯 Descripción del Sistema

El sistema de número de expediente genera automáticamente un identificador único para cada orden de compra con el formato **YYYYMM0001**, donde:
- **YYYY** = Año actual (4 dígitos)
- **MM** = Mes actual (2 dígitos)
- **0001** = Número secuencial que se reinicia cada mes (4 dígitos con ceros a la izquierda)

**Ejemplos:**
- Primera orden de octubre 2025: `2025100001`
- Segunda orden de octubre 2025: `2025100002`
- Primera orden de noviembre 2025: `2025110001`

## 📦 Estructura del Sistema

### Base de Datos
- **Tabla principal:** `SIST_ORDEN_COMPRA` con campo `OC_NUMERO_EXPEDIENTE`
- **Tablas relacionadas:** Todas las tablas de documentos se relacionan mediante `*_DOCUMENTO_VENTA_ID` (clave foránea a `OC_ID`)
- **Procedimiento almacenado:** `sp_GenerarNumeroExpediente` - Genera números de forma segura con manejo de concurrencia
- **Función:** `fn_GenerarNumeroExpediente` - Función auxiliar para generar números

### Archivos Creados/Modificados

#### Nuevos Archivos:
1. **`database/add_expediente_system.sql`** - Script SQL para crear el sistema de expedientes
2. **`app/controllers/ExpedienteController.php`** - Controlador para gestión de expedientes
3. **`app/views/expedientes/index.php`** - Vista principal: listar expedientes
4. **`app/views/expedientes/ver.php`** - Vista: ver documentos de un expediente
5. **`app/views/expedientes/imprimir_todos.php`** - Vista: imprimir todos los documentos

#### Archivos Modificados:
1. **`app/models/Document.php`** - Agregados métodos:
   - `generarNumeroExpediente()` - Genera el número automáticamente
   - `buscarPorNumeroExpediente()` - Busca orden por número
   - `listarOrdenesCompra()` - Lista todas las órdenes con paginación
   - `getDocumentosPorOrden()` - Obtiene todos los documentos de una orden
   - `guardarOrdenCompra()` - Modificado para auto-generar número

2. **`config/routes.php`** - Agregadas rutas:
   - `/expedientes` - Listar expedientes
   - `/expedientes/ver` - Ver expediente específico
   - `/expedientes/imprimir-todos` - Imprimir todos los documentos
   - `/expedientes/imprimir-documento` - Imprimir documento individual
   - `/expedientes/buscar` - API para buscar expedientes

3. **`app/views/documents/index.php`** - Agregado botón "Gestionar Expedientes"

## 🚀 Instalación

### Paso 1: Ejecutar el Script SQL

Ejecuta el script SQL en SQL Server Management Studio o mediante sqlcmd:

```bash
sqlcmd -S 192.168.10.10 -U sa -P sistemasi -d FACCARPRUEBA -i "database/add_expediente_system.sql"
```

O desde SSMS:
1. Abre SQL Server Management Studio
2. Conéctate al servidor `192.168.10.10`
3. Abre el archivo `database/add_expediente_system.sql`
4. Ejecuta el script (F5)

### Paso 2: Verificar la Instalación

Ejecuta estas consultas para verificar:

```sql
-- Verificar que el campo existe
SELECT name FROM sys.columns 
WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') 
AND name = 'OC_NUMERO_EXPEDIENTE';

-- Verificar que el procedimiento existe
SELECT name FROM sys.procedures 
WHERE name = 'sp_GenerarNumeroExpediente';

-- Verificar que la función existe
SELECT name FROM sys.objects 
WHERE name = 'fn_GenerarNumeroExpediente' AND type = 'FN';

-- Verificar índices
SELECT name FROM sys.indexes 
WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') 
AND name IN ('UQ_OC_NUMERO_EXPEDIENTE', 'IDX_OC_NUMERO_EXPEDIENTE');
```

### Paso 3: Probar el Sistema

1. **Crear una nueva orden de compra:**
   - Ve a: `http://localhost/digitalizacion-documentos/documents`
   - Llena el formulario de Orden de Compra
   - Guarda la orden
   - El sistema generará automáticamente el número de expediente

2. **Ver expedientes:**
   - Ve a: `http://localhost/digitalizacion-documentos/expedientes`
   - Verás la lista de todos los expedientes
   - Puedes buscar por número de expediente, nombre o DNI

3. **Ver documentos de un expediente:**
   - Haz clic en "Ver Documentos" en cualquier expediente
   - Verás todos los documentos asociados
   - Puedes imprimir documentos individuales o todos juntos

## 🔧 Funcionalidades

### 1. Generación Automática de Número de Expediente
- Se genera automáticamente al guardar una orden de compra
- No requiere intervención manual
- Maneja concurrencia (múltiples usuarios creando órdenes simultáneamente)
- Se reinicia automáticamente cada mes

### 2. Búsqueda de Expedientes
- Buscar por número de expediente
- Buscar por nombre del cliente
- Buscar por número de documento (DNI/RUC)
- Paginación automática (20 registros por página)

### 3. Visualización de Documentos
- Ver todos los documentos asociados a un expediente
- Identificar documentos generados y pendientes
- Acceso rápido a cada documento

### 4. Impresión
- **Imprimir todos los documentos:** Genera un PDF con todos los documentos del expediente
- **Imprimir documento individual:** Imprime solo el documento seleccionado
- Formato optimizado para impresión

## 📊 Estructura de Datos

### Relación entre Tablas

```
SIST_ORDEN_COMPRA (OC_ID, OC_NUMERO_EXPEDIENTE)
    ↓ (FK: *_DOCUMENTO_VENTA_ID)
    ├── SIST_ACTA_CONOCIMIENTO_CONFORMIDAD
    ├── SIST_AUTORIZACION_DATOS_PERSONALES
    ├── SIST_CARTA_CONOCIMIENTO_ACEPTACION
    ├── SIST_CARTA_RECEPCION
    ├── SIST_CARTA_CARACTERISTICAS
    ├── SIST_CARTA_CARACTERISTICAS_BANBIF
    ├── SIST_CARTA_FELICITACIONES
    ├── SIST_CARTA_OBSEQUIOS
    └── SIST_POLITICA_PROTECCION_DATOS
```

**Importante:** El número de expediente se almacena **solo en `SIST_ORDEN_COMPRA`**. Los demás documentos se relacionan mediante el campo `OC_ID` (clave foránea).

## 🔍 Consultas Útiles

### Ver todos los expedientes del mes actual
```sql
SELECT OC_NUMERO_EXPEDIENTE, OC_COMPRADOR_NOMBRE, OC_FECHA_CREACION
FROM SIST_ORDEN_COMPRA
WHERE OC_NUMERO_EXPEDIENTE LIKE FORMAT(GETDATE(), 'yyyyMM') + '%'
ORDER BY OC_NUMERO_EXPEDIENTE DESC;
```

### Ver documentos de un expediente específico
```sql
DECLARE @NumeroExpediente NVARCHAR(50) = '2025100001';
DECLARE @OC_ID INT;

SELECT @OC_ID = OC_ID FROM SIST_ORDEN_COMPRA WHERE OC_NUMERO_EXPEDIENTE = @NumeroExpediente;

-- Ver todos los documentos asociados
SELECT 'Acta Conformidad' AS Documento, COUNT(*) AS Cantidad 
FROM SIST_ACTA_CONOCIMIENTO_CONFORMIDAD WHERE ACC_DOCUMENTO_VENTA_ID = @OC_ID
UNION ALL
SELECT 'Autorización Datos', COUNT(*) 
FROM SIST_AUTORIZACION_DATOS_PERSONALES WHERE ADP_DOCUMENTO_VENTA_ID = @OC_ID
-- ... agregar más según necesidad
```

### Estadísticas mensuales
```sql
SELECT 
    FORMAT(OC_FECHA_CREACION, 'yyyy-MM') AS Mes,
    COUNT(*) AS TotalExpedientes
FROM SIST_ORDEN_COMPRA
GROUP BY FORMAT(OC_FECHA_CREACION, 'yyyy-MM')
ORDER BY Mes DESC;
```

## ⚠️ Consideraciones Importantes

1. **No modificar manualmente el número de expediente:** El sistema lo genera automáticamente
2. **Backup de base de datos:** Realiza backups regulares antes de hacer cambios
3. **Concurrencia:** El procedimiento almacenado maneja múltiples usuarios simultáneos
4. **Reinicio mensual:** Los números se reinician automáticamente cada mes
5. **Integridad referencial:** No eliminar registros de `SIST_ORDEN_COMPRA` si tienen documentos asociados

## 🐛 Solución de Problemas

### Error: "Número de expediente duplicado"
- El sistema maneja esto automáticamente con reintentos
- Si persiste, verifica que el índice único esté creado correctamente

### No se genera el número de expediente
- Verifica que el procedimiento `sp_GenerarNumeroExpediente` exista
- Revisa los logs de PHP en `error_log`
- Verifica permisos de ejecución en SQL Server

### No aparecen los expedientes en la lista
- Verifica que existan registros en `SIST_ORDEN_COMPRA`
- Verifica que el campo `OC_NUMERO_EXPEDIENTE` no sea NULL
- Revisa la conexión a la base de datos

## 📞 Soporte

Para problemas o consultas adicionales, revisa:
- Logs de PHP: `php_error.log`
- Logs de SQL Server: SQL Server Management Studio → Management → SQL Server Logs
- Código fuente: Todos los archivos están comentados para facilitar el mantenimiento

---

**Versión:** 1.0  
**Fecha:** Octubre 2025  
**Sistema:** Digitalización de Documentos - FACCAR
