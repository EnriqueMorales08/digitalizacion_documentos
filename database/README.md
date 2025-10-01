# 🗄️ CONFIGURACIÓN DE BASE DE DATOS - SQL SERVER

## 📋 PATRÓN DE NOMENCLATURA SIST_

### **🗂️ Estructura de Tablas:**
- **Prefijo:** `SIST_`
- **Nombre:** En mayúsculas
- **Ejemplo:** `SIST_ORDEN_COMPRA`, `SIST_CARTA_CARACTERISTICAS`

### **🏷️ Estructura de Campos:**
- **Prefijo:** Primeras letras de la tabla
- **Formato:** `PREFIJO_NOMBRE_DEL_CAMPO`
- **Ejemplos:**
  - `SIST_ORDEN_COMPRA` → `OC_NUMERO_EXPEDIENTE`, `OC_COMPRADOR_NOMBRE`
  - `SIST_CARTA_CARACTERISTICAS` → `CC_FECHA_CARTA`, `CC_CLIENTE_NOMBRE`

### **📊 Tablas Disponibles:**
| Tabla | Prefijo | Descripción |
|-------|---------|-------------|
| `SIST_ORDEN_COMPRA` | `OC_` | Orden de compra completa |
| `SIST_ACTA_CONOCIMIENTO_CONFORMIDAD` | `ACC_` | Acta GLP |
| `SIST_AUTORIZACION_DATOS_PERSONALES` | `ADP_` | Autorización de datos |
| `SIST_CARTA_CONOCIMIENTO_ACEPTACION` | `CCA_` | Carta de aceptación |
| `SIST_CARTA_RECEPCION` | `CR_` | Carta de recepción |
| `SIST_CARTA_CARACTERISTICAS` | `CC_` | Carta de características |
| `SIST_CARTA_FELICITACIONES` | `CF_` | Carta de bienvenida |

## 📋 INSTRUCCIONES PASO A PASO
Si no tienes SQL Server instalado:

1. **Descarga SQL Server Express** desde: https://www.microsoft.com/en-us/sql-server/sql-server-downloads
2. **Ejecuta el instalador** y selecciona "Basic Installation"
3. **Configura autenticación mixta** (Windows + SQL Server Authentication)
4. **Establece una contraseña** para el usuario 'sa'

### **PASO 2: Instalar SQL Server Management Studio (SSMS)**
1. **Descarga SSMS** desde: https://docs.microsoft.com/en-us/sql/ssms/download-sql-server-management-studio-ssms
2. **Instala SSMS** siguiendo el asistente

### **PASO 3: Crear la Base de Datos**

#### **Opción A: Usando SSMS (Recomendado)**
1. **Abre SQL Server Management Studio**
2. **Conéctate a tu servidor** (normalmente: `localhost\SQLEXPRESS` o `localhost`)
3. **Clic derecho en "Databases"** → **"New Database"**
4. **Nombre:** `documentos_db`
5. **Clic en "OK"**

#### **Opción B: Usando SQL Script (Recomendado para empezar)**
1. **Abre SQL Server Management Studio**
2. **Conéctate a tu servidor**
3. **Abre el archivo `database/schema_basic.sql`** (versión simplificada sin procedimientos)
4. **Ejecuta el script** (F5 o clic en "Execute")

#### **Opción C: Script Completo (Avanzado)**
Si quieres los procedimientos almacenados:
1. **Ejecuta primero** `database/schema_basic.sql`
2. **Después ejecuta** los procedimientos de `database/schema.sql` por separado

### **PASO 4: Verificar la Instalación**
Ejecuta esta consulta para verificar que las tablas se crearon:
```sql
USE documentos_db;
SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE';
```

Deberías ver estas tablas:
- SIST_ORDEN_COMPRA
- SIST_ACTA_CONOCIMIENTO_CONFORMIDAD
- SIST_AUTORIZACION_DATOS_PERSONALES
- SIST_CARTA_CONOCIMIENTO_ACEPTACION
- SIST_CARTA_RECEPCION
- SIST_CARTA_CARACTERISTICAS
- SIST_CARTA_FELICITACIONES

### **PASO 5: Configurar la Conexión en PHP**

#### **Editar `config/database.php`**
Cambia estas líneas con tus datos reales:
```php
$serverName = "localhost\\SQLEXPRESS"; // O "localhost" si no usas instancia nombrada
$database = "interamericana_db";
$username = "sa"; // Tu usuario de SQL Server
$password = "tu_password_real"; // Tu contraseña real
```

### **PASO 6: Probar la Conexión**

Crea un archivo `test_connection.php` en la raíz del proyecto:
```php
<?php
require_once 'config/database.php';

try {
    $db = getDB();
    echo "✅ Conexión exitosa a SQL Server";

    // Probar consulta
    $stmt = $db->query("SELECT TOP 1 TABLE_NAME FROM INFORMATION_SCHEMA.TABLES");
    $result = $stmt->fetch();
    echo "<br>✅ Primera tabla encontrada: " . $result['TABLE_NAME'];

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
```

### **PASO 7: Configurar IIS o Apache**
Asegúrate de que tu servidor web tenga habilitada la extensión `pdo_sqlsrv` para PHP.

#### **Para XAMPP/WAMP:**
1. Descarga los drivers de SQL Server para PHP desde: https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server
2. Copia los archivos .dll a la carpeta `ext` de PHP
3. Habilita las extensiones en `php.ini`:
   ```
   extension=php_pdo_sqlsrv.dll
   extension=php_sqlsrv.dll
   ```

## 🔧 ESTRUCTURA DE LAS TABLAS

### **📊 Tabla Principal: `SIST_ORDEN_COMPRA`**
Contiene todos los campos de la orden de compra (85+ campos mapeados con prefijo `OC_`).

### **📄 Tablas Específicas:**
- `SIST_ACTA_CONOCIMIENTO_CONFORMIDAD` - Datos del acta GLP (prefijo `ACC_`)
- `SIST_AUTORIZACION_DATOS_PERSONALES` - Autorizaciones de uso de imagen (prefijo `ADP_`)
- `SIST_CARTA_CONOCIMIENTO_ACEPTACION` - Cartas de aceptación (prefijo `CCA_`)
- `SIST_CARTA_RECEPCION` - Cartas de recepción de merchandising (prefijo `CR_`)
- `SIST_CARTA_CARACTERISTICAS` - Cartas de características del vehículo (prefijo `CC_`)
- `SIST_CARTA_FELICITACIONES` - Cartas de bienvenida (prefijo `CF_`)

## 🚀 PROCEDIMIENTOS ALMACENADOS

### **sp_insertar_documento_venta**
Para insertar nuevos documentos de venta.

### **sp_obtener_documentos_por_fecha**
Para consultar documentos por rango de fechas.

### **sp_obtener_documentos_por_asesor**
Para consultar documentos por asesor de ventas.

## 🎉 FUNCIONALIDADES DE LA APLICACIÓN

### **📋 Formularios Funcionales**
- ✅ **Orden de Compra** - 85+ campos mapeados y funcionales
- ✅ **Acta de Conformidad** - Campos mapeados para instalación GLP
- ✅ **Otros documentos** - Listos para implementación

### **💾 Sistema de Guardado**
- ✅ **Inserción automática** en base de datos
- ✅ **Página de éxito** dedicada con diseño moderno
- ✅ **Mensajes de confirmación** atractivos
- ✅ **Redirección inteligente** después del guardado

### **🎯 Página de Éxito**
Cuando guardas un documento, verás una página dedicada con:
- ✅ **Mensaje personalizado** según el tipo de documento
- ✅ **ID del documento** guardado
- ✅ **Botones de navegación** (Ver documentos / Crear nuevo)
- ✅ **Diseño moderno** con gradientes y animaciones

## 📝 EJEMPLO DE USO EN PHP

```php
<?php
require_once 'config/database.php';

// Insertar un nuevo documento con patrón SIST_
$db = getDB();
$stmt = $db->prepare("
    INSERT INTO SIST_ORDEN_COMPRA (
        OC_NUMERO_EXPEDIENTE, OC_FECHA_ORDEN, OC_ASESOR_VENTA,
        OC_COMPRADOR_NOMBRE, OC_COMPRADOR_NUMERO_DOCUMENTO
    ) VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    'EXP-001',
    '2025-01-15',
    'Juan Pérez',
    'Roberto López',
    '12345678'
]);

$nuevo_id = $db->lastInsertId();
echo "Documento insertado con ID: " . $nuevo_id;

// Consultar documentos por asesor
$stmt = $db->prepare("
    SELECT * FROM SIST_ORDEN_COMPRA
    WHERE OC_ASESOR_VENTA = ?
    ORDER BY OC_FECHA_ORDEN DESC
");
$stmt->execute(['Juan Pérez']);
$documentos = $stmt->fetchAll();
```

## ⚠️ **PROBLEMA CON EL SCRIPT COMPLETO**

Si encuentras el error:
```
'CREATE/ALTER PROCEDURE' must be the first statement in a query batch.
```

**Solución:** Usa el script básico primero:
1. **Ejecuta** `database/schema_basic.sql` (solo tablas)
2. **Después** ejecuta los procedimientos por separado si los necesitas

El script `schema_basic.sql` contiene solo las tablas esenciales y funciona sin problemas.

## 🔍 CONSULTAS ÚTILES

```sql
-- Documentos del mes actual
SELECT * FROM SIST_ORDEN_COMPRA
WHERE MONTH(OC_FECHA_ORDEN) = MONTH(GETDATE())
AND YEAR(OC_FECHA_ORDEN) = YEAR(GETDATE());

-- Documentos por asesor
SELECT OC_ASESOR_VENTA, COUNT(*) as total
FROM SIST_ORDEN_COMPRA
GROUP BY OC_ASESOR_VENTA
ORDER BY total DESC;

-- Documentos con vehículos de una marca específica
SELECT * FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA = 'CHERY';

-- Actas de conformidad por fecha
SELECT * FROM SIST_ACTA_CONOCIMIENTO_CONFORMIDAD
WHERE ACC_FECHA_ACTA >= '2025-01-01'
ORDER BY ACC_FECHA_ACTA DESC;
```

## ⚠️ NOTAS IMPORTANTES

1. **Backup regular:** Realiza backups periódicos de la base de datos
2. **Permisos:** Asegúrate de que el usuario de la aplicación tenga permisos adecuados
3. **Transacciones:** Usa transacciones para operaciones críticas
4. **Validación:** Valida los datos en PHP antes de insertar en la BD
5. **Seguridad:** Nunca expongas credenciales de BD en código público

## 🆘 SOLUCIÓN DE PROBLEMAS

### **Error de conexión**
- Verifica que SQL Server esté ejecutándose
- Confirma las credenciales en `database.php`
- Asegúrate de que el firewall permita conexiones

### **Error de driver PHP**
- Verifica que `pdo_sqlsrv` esté habilitado en `php.ini`
- Confirma que los archivos .dll estén en la carpeta correcta
- Reinicia el servidor web después de cambios

### **Error de permisos**
- Otorga permisos al usuario en SQL Server Management Studio
- Usa: `GRANT ALL PRIVILEGES ON interamericana_db.* TO 'usuario'@'localhost';`