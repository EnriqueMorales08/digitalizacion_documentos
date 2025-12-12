# Sistema de Roles Implementado

## Fecha de Implementación
30 de Octubre de 2025

## Descripción General
Se ha implementado un sistema de control de acceso basado en roles para que los asesores (USER) solo puedan ver las órdenes de compra que ellos mismos crearon, mientras que los administradores (ADMIN) pueden ver todas las órdenes del sistema.

## Roles Disponibles
- **ADMIN**: Acceso completo a todas las órdenes de compra y documentos del sistema
- **USER**: Acceso limitado solo a las órdenes de compra que el usuario creó

## Cambios Realizados

### 1. AuthController.php
**Archivo**: `app/controllers/AuthController.php`

**Cambios**:
- Se agregó la columna `rol` a la consulta SQL del login (línea 52)
- Se guarda el rol del usuario en la sesión: `$_SESSION['usuario_rol']` (línea 77)

**Código modificado**:
```php
$sql = "SELECT usuario, password, firma_nombre, firma_apellido, firma_mail, firma_data, rol 
        FROM firmas 
        WHERE usuario = ? AND password = ?";

// ...

$_SESSION['usuario_rol'] = $user['rol']; // Guardar el rol del usuario
```

### 2. Document.php (Modelo)
**Archivo**: `app/models/Document.php`

Se agregaron filtros de seguridad en 3 funciones críticas:

#### a) getOrdenCompra() - Líneas 353-379
Filtra el acceso a una orden específica por ID.
- Si el usuario es USER: solo puede acceder a órdenes donde `OC_USUARIO_EMAIL` coincida con su email
- Si el usuario es ADMIN: puede acceder a cualquier orden

#### b) buscarPorNumeroExpediente() - Líneas 936-968
Filtra la búsqueda de órdenes por número de expediente.
- Si el usuario es USER: solo puede buscar sus propias órdenes
- Si el usuario es ADMIN: puede buscar cualquier orden

#### c) listarOrdenesCompra() - Líneas 957-1013
Filtra el listado de todas las órdenes con paginación.
- Si el usuario es USER: solo ve órdenes donde `OC_USUARIO_EMAIL` coincida con su email
- Si el usuario es ADMIN: ve todas las órdenes sin restricción

**Lógica de filtrado**:
```php
// 🔒 FILTRO POR ROL: Si el usuario es USER, solo ver sus propias órdenes
if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'USER') {
    if (isset($_SESSION['usuario_email'])) {
        $whereConditions[] = "OC_USUARIO_EMAIL = ?";
        $params[] = $_SESSION['usuario_email'];
    }
}
// Si es ADMIN, no se agrega filtro (ve todas las órdenes)
```

## Controladores Protegidos Automáticamente

Los siguientes controladores están protegidos automáticamente porque usan las funciones del modelo que ya tienen los filtros:

1. **DocumentController.php**: Usa `getOrdenCompra()` para mostrar documentos
2. **AprobacionController.php**: Usa `getOrdenCompra()` para el panel de aprobación
3. **ExpedienteController.php**: Usa `listarOrdenesCompra()`, `getOrdenCompra()` y `buscarPorNumeroExpediente()`

## Tabla de Base de Datos Utilizada

**Base de datos**: `DOC_DIGITALES`
**Tabla**: `firmas`
**Columna de roles**: `rol`

**Valores válidos**:
- `ADMIN` - Administrador con acceso completo
- `USER` - Usuario/Asesor con acceso limitado

## Cómo Funciona

1. **Login**: Cuando un usuario inicia sesión, el sistema captura su rol de la tabla `firmas` y lo guarda en `$_SESSION['usuario_rol']`

2. **Creación de Órdenes**: Cuando un asesor crea una orden de compra, el sistema guarda automáticamente su email en el campo `OC_USUARIO_EMAIL` de la tabla `SIST_ORDEN_COMPRA`

3. **Consultas Filtradas**: Todas las consultas a la base de datos verifican el rol del usuario:
   - Si es USER: se agrega `WHERE OC_USUARIO_EMAIL = [email del usuario]`
   - Si es ADMIN: no se agrega filtro adicional

4. **Protección en Cascada**: Como todos los controladores usan las funciones del modelo, la protección se aplica automáticamente en toda la aplicación

## Seguridad

✅ **Protección a nivel de base de datos**: Los filtros se aplican en las consultas SQL, no solo en la interfaz
✅ **Sin acceso directo**: Un usuario USER no puede acceder a órdenes de otros usuarios ni siquiera conociendo el ID
✅ **Búsquedas filtradas**: Las búsquedas por número de expediente también respetan los permisos
✅ **Listados seguros**: Los listados solo muestran órdenes permitidas según el rol

## Pruebas Recomendadas

1. **Como USER**:
   - Crear una orden de compra
   - Verificar que solo aparece en el listado de expedientes
   - Intentar acceder a una orden de otro usuario (debe fallar)
   - Buscar por número de expediente propio (debe funcionar)
   - Buscar por número de expediente de otro usuario (no debe encontrar)

2. **Como ADMIN**:
   - Ver el listado completo de todas las órdenes
   - Acceder a cualquier orden por ID
   - Buscar cualquier expediente por número
   - Aprobar/rechazar órdenes de cualquier usuario

## Notas Importantes

- Los roles deben estar configurados correctamente en la tabla `firmas` de la base de datos `DOC_DIGITALES`
- El campo `OC_USUARIO_EMAIL` debe estar poblado en todas las órdenes existentes para que el filtro funcione correctamente
- Si una orden no tiene `OC_USUARIO_EMAIL`, solo será visible para usuarios ADMIN

## Reversión de Cambios

Si necesitas revertir estos cambios, los archivos modificados son:
1. `app/controllers/AuthController.php`
2. `app/models/Document.php`

Puedes usar el control de versiones (git) para volver a la versión anterior si es necesario.
