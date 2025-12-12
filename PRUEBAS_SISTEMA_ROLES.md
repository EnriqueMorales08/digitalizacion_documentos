# 🧪 Plan de Pruebas - Sistema de Roles

## Objetivo
Verificar que el sistema de roles funciona correctamente y que los asesores (USER) solo pueden ver sus propias órdenes mientras que los administradores (ADMIN) pueden ver todas.

---

## Pre-requisitos

Antes de comenzar las pruebas, asegúrate de:

1. ✅ Haber ejecutado el script `database/VERIFICAR_COLUMNA_ROL.sql`
2. ✅ Tener al menos 2 usuarios con rol USER (asesores)
3. ✅ Tener al menos 1 usuario con rol ADMIN
4. ✅ Tener órdenes de compra creadas por diferentes asesores

---

## Escenario 1: Pruebas como Usuario (Asesor)

### Preparación
1. Identifica un usuario con rol `USER` en la base de datos
2. Asegúrate de que este usuario tenga al menos 1 orden de compra creada

### Pruebas a Realizar

#### ✅ Prueba 1.1: Login y Sesión
**Pasos:**
1. Ir a `/digitalizacion-documentos/auth/login`
2. Iniciar sesión con un usuario USER
3. Verificar que se redirige correctamente al panel principal

**Resultado Esperado:**
- Login exitoso
- Sesión iniciada correctamente

---

#### ✅ Prueba 1.2: Ver Listado de Expedientes
**Pasos:**
1. Ir a `/digitalizacion-documentos/expedientes`
2. Observar el listado de órdenes de compra

**Resultado Esperado:**
- Solo se muestran las órdenes creadas por el usuario logueado
- No aparecen órdenes de otros asesores
- El contador de registros muestra solo las órdenes propias

**Verificación SQL:**
```sql
-- Ejecutar en FACCARPRUEBA
SELECT COUNT(*) as Mis_Ordenes
FROM SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL = 'email_del_usuario_logueado'
```

---

#### ✅ Prueba 1.3: Buscar Expediente Propio
**Pasos:**
1. En `/digitalizacion-documentos/expedientes`
2. Buscar un número de expediente que pertenezca al usuario logueado
3. Hacer clic en "Ver"

**Resultado Esperado:**
- La búsqueda encuentra el expediente
- Se puede acceder a ver los detalles
- Se muestran todos los documentos asociados

---

#### ✅ Prueba 1.4: Intentar Acceder a Expediente de Otro Asesor
**Pasos:**
1. Obtener el ID de una orden de otro asesor (desde la BD)
2. Intentar acceder directamente con la URL:
   `/digitalizacion-documentos/expedientes/ver?id=XXX`

**Resultado Esperado:**
- ❌ No se muestra la orden
- Redirige a la lista con mensaje de error "Expediente no encontrado"
- El usuario NO puede ver órdenes ajenas

**Verificación:**
```sql
-- Obtener ID de orden de otro usuario
SELECT TOP 1 OC_ID, OC_NUMERO_EXPEDIENTE, OC_USUARIO_EMAIL
FROM SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL != 'email_del_usuario_logueado'
```

---

#### ✅ Prueba 1.5: Buscar Expediente de Otro Asesor
**Pasos:**
1. En `/digitalizacion-documentos/expedientes`
2. Buscar un número de expediente que NO pertenezca al usuario logueado

**Resultado Esperado:**
- ❌ La búsqueda no encuentra el expediente
- Mensaje: "Expediente no encontrado"

---

#### ✅ Prueba 1.6: Crear Nueva Orden
**Pasos:**
1. Crear una nueva orden de compra
2. Guardar la orden
3. Verificar que aparece en el listado

**Resultado Esperado:**
- La orden se crea correctamente
- Aparece inmediatamente en el listado del usuario
- Los campos OC_USUARIO_EMAIL y OC_USUARIO_NOMBRE se guardan automáticamente

**Verificación SQL:**
```sql
-- Verificar última orden creada
SELECT TOP 1 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_USUARIO_EMAIL,
    OC_USUARIO_NOMBRE,
    OC_FECHA_CREACION
FROM SIST_ORDEN_COMPRA
ORDER BY OC_FECHA_CREACION DESC
```

---

## Escenario 2: Pruebas como Administrador

### Preparación
1. Identifica un usuario con rol `ADMIN` en la base de datos
2. Asegúrate de que existan órdenes de diferentes asesores

### Pruebas a Realizar

#### ✅ Prueba 2.1: Login como Admin
**Pasos:**
1. Cerrar sesión del usuario anterior
2. Ir a `/digitalizacion-documentos/auth/login`
3. Iniciar sesión con un usuario ADMIN

**Resultado Esperado:**
- Login exitoso
- Sesión iniciada correctamente

---

#### ✅ Prueba 2.2: Ver Listado Completo
**Pasos:**
1. Ir a `/digitalizacion-documentos/expedientes`
2. Observar el listado de órdenes de compra

**Resultado Esperado:**
- ✅ Se muestran TODAS las órdenes de compra del sistema
- Se ven órdenes de todos los asesores
- El contador muestra el total real de órdenes

**Verificación SQL:**
```sql
-- Total de órdenes en el sistema
SELECT COUNT(*) as Total_Ordenes
FROM SIST_ORDEN_COMPRA

-- Órdenes por asesor
SELECT 
    OC_USUARIO_NOMBRE,
    COUNT(*) as Cantidad
FROM SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL IS NOT NULL
GROUP BY OC_USUARIO_NOMBRE
```

---

#### ✅ Prueba 2.3: Acceder a Cualquier Expediente
**Pasos:**
1. Seleccionar una orden de cualquier asesor
2. Hacer clic en "Ver"
3. Verificar acceso completo

**Resultado Esperado:**
- ✅ Se puede acceder a la orden sin restricciones
- Se muestran todos los detalles
- Se pueden ver todos los documentos asociados

---

#### ✅ Prueba 2.4: Buscar Cualquier Expediente
**Pasos:**
1. Buscar un expediente de cualquier asesor
2. Verificar que se encuentra

**Resultado Esperado:**
- ✅ La búsqueda encuentra cualquier expediente
- No hay restricciones de acceso

---

#### ✅ Prueba 2.5: Aprobar/Rechazar Órdenes
**Pasos:**
1. Ir al panel de aprobación de una orden
2. Aprobar o rechazar la orden

**Resultado Esperado:**
- ✅ Se puede aprobar/rechazar cualquier orden
- El estado se actualiza correctamente
- Se envía notificación al asesor que creó la orden

---

## Escenario 3: Pruebas de Seguridad

#### ✅ Prueba 3.1: Cambio de Rol en Tiempo Real
**Pasos:**
1. Iniciar sesión como USER
2. Mientras la sesión está activa, cambiar el rol a ADMIN en la BD
3. Refrescar la página

**Resultado Esperado:**
- ⚠️ El cambio NO se refleja hasta cerrar sesión
- El rol se guarda en la sesión al hacer login
- Debe cerrar sesión y volver a entrar para ver el cambio

---

#### ✅ Prueba 3.2: Acceso Directo por URL
**Pasos:**
1. Como USER, copiar la URL de una orden ajena
2. Cerrar sesión
3. Iniciar sesión con otro USER
4. Pegar la URL copiada

**Resultado Esperado:**
- ❌ No se puede acceder a la orden
- Mensaje de error o redirección

---

#### ✅ Prueba 3.3: Manipulación de Parámetros
**Pasos:**
1. Como USER, intentar modificar parámetros en la URL
2. Probar con diferentes IDs de órdenes

**Resultado Esperado:**
- ❌ Solo se puede acceder a órdenes propias
- Cualquier intento de acceder a órdenes ajenas falla

---

## Escenario 4: Pruebas de Búsqueda y Filtros

#### ✅ Prueba 4.1: Búsqueda como USER
**Pasos:**
1. Como USER, buscar por nombre de comprador
2. Buscar por número de documento
3. Buscar por número de expediente

**Resultado Esperado:**
- Solo se encuentran resultados de órdenes propias
- No aparecen resultados de otros asesores

---

#### ✅ Prueba 4.2: Búsqueda como ADMIN
**Pasos:**
1. Como ADMIN, realizar las mismas búsquedas
2. Verificar resultados

**Resultado Esperado:**
- Se encuentran resultados de todos los asesores
- Búsqueda sin restricciones

---

## Checklist de Verificación Final

Marca cada item cuando esté verificado:

### Configuración Inicial
- [ ] Columna `rol` existe en tabla `firmas`
- [ ] Todos los usuarios tienen un rol asignado
- [ ] Hay al menos 1 usuario ADMIN
- [ ] Hay al menos 2 usuarios USER

### Funcionalidad USER
- [ ] USER solo ve sus propias órdenes en el listado
- [ ] USER puede crear nuevas órdenes
- [ ] USER NO puede acceder a órdenes ajenas por URL directa
- [ ] USER NO puede buscar expedientes ajenos
- [ ] USER puede ver y editar sus propias órdenes

### Funcionalidad ADMIN
- [ ] ADMIN ve todas las órdenes en el listado
- [ ] ADMIN puede acceder a cualquier orden
- [ ] ADMIN puede buscar cualquier expediente
- [ ] ADMIN puede aprobar/rechazar cualquier orden
- [ ] ADMIN puede imprimir cualquier documento

### Seguridad
- [ ] No es posible acceder a órdenes ajenas manipulando URLs
- [ ] Las búsquedas respetan los permisos por rol
- [ ] Los filtros se aplican a nivel de base de datos
- [ ] El cambio de rol requiere cerrar sesión

---

## Reporte de Problemas

Si encuentras algún problema durante las pruebas:

1. **Anota el problema**: Describe qué esperabas vs qué obtuviste
2. **Captura pantalla**: Si es posible, toma una captura
3. **Verifica logs**: Revisa los logs de PHP y SQL Server
4. **Consulta SQL**: Ejecuta las consultas de verificación

---

## Consultas SQL de Ayuda

```sql
-- Ver sesión actual (simular)
SELECT 
    'email@ejemplo.com' as usuario_email,
    'USER' as usuario_rol

-- Ver qué vería un USER específico
SELECT OC_ID, OC_NUMERO_EXPEDIENTE, OC_COMPRADOR_NOMBRE
FROM SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL = 'email@ejemplo.com'

-- Ver qué vería un ADMIN (todo)
SELECT OC_ID, OC_NUMERO_EXPEDIENTE, OC_COMPRADOR_NOMBRE, OC_USUARIO_EMAIL
FROM SIST_ORDEN_COMPRA
```

---

**Fecha de creación**: 30 de Octubre de 2025
**Versión**: 1.0
