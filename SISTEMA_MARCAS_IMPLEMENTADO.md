# Sistema de Control de Acceso por Marcas

## Fecha de Implementación
7 de Noviembre de 2025

## Descripción General
Se ha implementado un sistema de control de acceso basado en marcas de vehículos que complementa el sistema de roles existente (USER/ADMIN). Este sistema permite que usuarios específicos (jefes de marca) puedan visualizar todas las órdenes de compra de las marcas asignadas a ellos, sin poder editarlas.

## Roles y Permisos

### 1. Usuario ADMIN sin marcas
- **Acceso**: Ve TODAS las órdenes de compra del sistema
- **Permisos**: Puede editar, aprobar y rechazar cualquier orden

### 2. Usuario USER sin marcas (Asesor normal)
- **Acceso**: Ve solo las órdenes de compra que él mismo creó
- **Permisos**: Puede crear y editar sus propias órdenes

### 3. Usuario USER con marcas asignadas (Jefe de Marca)
- **Acceso**: Ve todas las órdenes de compra de las marcas asignadas (independientemente de quién las creó)
- **Permisos**: **SOLO VISUALIZACIÓN** - No puede editar, crear, aprobar ni rechazar órdenes
- **Ejemplo**: Un usuario con marca "FORD,SUBARU" verá todas las órdenes de Ford y Subaru

## Cambios Realizados

### 1. AuthController.php
**Archivo**: `app/controllers/AuthController.php`

**Cambios**:
- Se agregó la columna `marca` a la consulta SQL del login (línea 52)
- Se guarda las marcas del usuario en la sesión: `$_SESSION['usuario_marcas']` (línea 78)

**Código modificado**:
```php
$sql = "SELECT usuario, password, firma_nombre, firma_apellido, firma_mail, firma_data, rol, marca 
        FROM firmas 
        WHERE usuario = ? AND password = ?";

// ...

$_SESSION['usuario_marcas'] = $user['marca'] ?? ''; // Guardar las marcas del usuario
```

### 2. Document.php (Modelo)
**Archivo**: `app/models/Document.php`

#### a) Nueva función: `puedeEditar()` - Líneas 19-38
Función estática que verifica si el usuario actual tiene permisos de edición.

**Lógica**:
- Si es ADMIN: siempre puede editar
- Si es USER con marcas: NO puede editar (solo visualizar)
- Si es USER sin marcas: puede editar sus propias órdenes

```php
public static function puedeEditar() {
    // Si es ADMIN, siempre puede editar
    if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'ADMIN') {
        return true;
    }
    
    // Si es USER y tiene marcas asignadas, NO puede editar
    if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'USER') {
        if (isset($_SESSION['usuario_marcas']) && !empty(trim($_SESSION['usuario_marcas']))) {
            return false; // Usuario con marcas solo puede visualizar
        }
        return true; // Usuario sin marcas puede editar sus propias órdenes
    }
    
    return false; // Por defecto no puede editar
}
```

#### b) getOrdenCompra() - Modificada
Filtra el acceso a una orden específica por ID.

**Lógica de filtrado**:
- Si el usuario es USER con marcas: filtra por `OC_VEHICULO_MARCA` usando LIKE para cada marca
- Si el usuario es USER sin marcas: filtra por `OC_USUARIO_EMAIL`
- Si el usuario es ADMIN: sin filtro (ve todas)

```php
// 🔒 FILTRO POR ROL Y MARCAS
if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'USER') {
    // Si el usuario tiene marcas asignadas, filtrar por marcas
    if (isset($_SESSION['usuario_marcas']) && !empty(trim($_SESSION['usuario_marcas']))) {
        $marcasUsuario = array_map('trim', explode(',', $_SESSION['usuario_marcas']));
        $marcasConditions = [];
        foreach ($marcasUsuario as $marca) {
            $marcasConditions[] = "OC_VEHICULO_MARCA LIKE ?";
            $params[] = '%' . $marca . '%';
        }
        if (!empty($marcasConditions)) {
            $whereConditions[] = '(' . implode(' OR ', $marcasConditions) . ')';
        }
    } else {
        // Usuario USER sin marcas - solo ve sus propias órdenes
        if (isset($_SESSION['usuario_email'])) {
            $whereConditions[] = "OC_USUARIO_EMAIL = ?";
            $params[] = $_SESSION['usuario_email'];
        }
    }
}
```

#### c) buscarPorNumeroExpediente() - Modificada
Aplica la misma lógica de filtrado por marcas en la búsqueda por número de expediente.

#### d) listarOrdenesCompra() - Modificada
Aplica la misma lógica de filtrado por marcas en el listado paginado de órdenes.

### 3. DocumentController.php
**Archivo**: `app/controllers/DocumentController.php`

**Cambios**:
- Se agregó verificación de permisos en `procesarOrdenCompra()` (línea 127-131)
- Se agregó verificación de permisos en `guardarDocumento()` (línea 250-254)

**Código agregado**:
```php
// 🔒 Verificar permisos de edición
if (!Document::puedeEditar()) {
    header("Location: /digitalizacion-documentos/documents?error=" . urlencode('No tiene permisos para editar documentos'));
    exit;
}
```

### 4. AprobacionController.php
**Archivo**: `app/controllers/AprobacionController.php`

**Cambios**:
- Se agregó verificación de permisos en `procesar()` (línea 38-43)
- Los usuarios con marcas no pueden aprobar ni rechazar órdenes

## Tabla de Base de Datos

### Tabla: firmas (Base de datos: DOC_DIGITALES)
**Nueva columna**: `marca`

**Formato de datos**:
- Vacío: Usuario normal sin acceso por marcas
- Una marca: `FORD`
- Múltiples marcas: `FORD,SUBARU,TOYOTA` (separadas por comas)

### Tabla: SIST_ORDEN_COMPRA
**Campo utilizado**: `OC_VEHICULO_MARCA`

Este campo se compara con las marcas del usuario para determinar el acceso.

## Cómo Funciona

### Escenario 1: Usuario USER sin marcas (Asesor)
1. Inicia sesión → `$_SESSION['usuario_marcas']` está vacío
2. Crea órdenes de compra → Se guarda su email en `OC_USUARIO_EMAIL`
3. Ve solo sus propias órdenes
4. Puede editar sus propias órdenes

### Escenario 2: Usuario USER con marcas (Jefe de Marca)
1. Inicia sesión → `$_SESSION['usuario_marcas']` = "FORD,SUBARU"
2. El sistema separa las marcas: ["FORD", "SUBARU"]
3. Ve todas las órdenes donde `OC_VEHICULO_MARCA` contenga "FORD" o "SUBARU"
4. **NO puede editar, crear, aprobar ni rechazar** ninguna orden
5. Solo puede visualizar las órdenes

### Escenario 3: Usuario ADMIN
1. Inicia sesión → Rol = "ADMIN"
2. Ve todas las órdenes sin restricción
3. Puede editar, aprobar y rechazar cualquier orden

## Seguridad

✅ **Filtrado a nivel de base de datos**: Los filtros se aplican en las consultas SQL
✅ **Restricción de edición**: Usuarios con marcas no pueden modificar datos
✅ **Restricción de aprobación**: Usuarios con marcas no pueden aprobar/rechazar
✅ **Búsquedas filtradas**: Las búsquedas respetan los permisos por marca
✅ **Compatibilidad**: El sistema anterior (USER/ADMIN sin marcas) sigue funcionando igual

## Pruebas Recomendadas

### Como Usuario USER sin marcas (Asesor)
1. Crear una orden de compra de marca FORD
2. Verificar que solo aparece en su listado
3. Editar la orden (debe funcionar)
4. Intentar ver orden de otro usuario (no debe aparecer)

### Como Usuario USER con marca FORD (Jefe de Marca)
1. Verificar que aparecen todas las órdenes de FORD en el listado
2. Intentar editar una orden (debe mostrar error de permisos)
3. Intentar crear una orden (debe mostrar error de permisos)
4. Intentar aprobar/rechazar (debe mostrar error de permisos)
5. Verificar que NO aparecen órdenes de otras marcas (ej. SUBARU)

### Como Usuario USER con múltiples marcas "FORD,SUBARU"
1. Verificar que aparecen órdenes de FORD y SUBARU
2. Verificar que NO aparecen órdenes de otras marcas (ej. TOYOTA)
3. Confirmar que no puede editar ninguna orden

### Como Usuario ADMIN
1. Verificar que ve todas las órdenes de todas las marcas
2. Editar cualquier orden (debe funcionar)
3. Aprobar/rechazar cualquier orden (debe funcionar)

## Configuración de Usuarios

Para asignar marcas a un usuario, ejecutar en SQL Server:

```sql
-- Asignar una marca
UPDATE firmas 
SET marca = 'FORD' 
WHERE usuario = 'jefe_ford';

-- Asignar múltiples marcas
UPDATE firmas 
SET marca = 'FORD,SUBARU,TOYOTA' 
WHERE usuario = 'jefe_multimarca';

-- Quitar marcas (volver a usuario normal)
UPDATE firmas 
SET marca = NULL 
WHERE usuario = 'asesor_normal';

-- Ver usuarios y sus marcas
SELECT usuario, firma_nombre, firma_apellido, rol, marca 
FROM firmas 
ORDER BY rol, marca;
```

## Notas Importantes

- Las marcas en la columna `marca` deben coincidir con los valores en `OC_VEHICULO_MARCA`
- Las marcas son case-sensitive (distinguen mayúsculas/minúsculas)
- Se usa LIKE para permitir coincidencias parciales (ej. "FORD" coincide con "FORD RANGER")
- Los usuarios con marcas pierden todos los permisos de edición, incluso si son USER
- El sistema mantiene compatibilidad total con el sistema de roles anterior

## Reversión de Cambios

Si necesitas revertir estos cambios, los archivos modificados son:
1. `app/controllers/AuthController.php`
2. `app/models/Document.php`
3. `app/controllers/DocumentController.php`
4. `app/controllers/AprobacionController.php`

Puedes usar el control de versiones (git) para volver a la versión anterior.
