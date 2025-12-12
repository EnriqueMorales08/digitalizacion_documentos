# 🔐 Instrucciones - Sistema de Roles

## ¿Qué se implementó?

Se agregó un sistema de roles para controlar quién puede ver qué órdenes de compra:

- **Asesores (USER)**: Solo ven las órdenes de compra que ellos mismos crearon
- **Administradores (ADMIN)**: Ven todas las órdenes de compra del sistema

## 📋 Pasos para Activar el Sistema

### 1. Verificar la columna ROL en la base de datos

Ejecuta este script en SQL Server Management Studio:

```sql
-- Conectarse a la base de datos DOC_DIGITALES
USE DOC_DIGITALES
GO

-- Ver la tabla de usuarios y sus roles
SELECT usuario, firma_nombre, firma_apellido, rol 
FROM firmas
ORDER BY usuario
```

Si la columna `rol` no existe o está vacía, ejecuta el script:
```
database/VERIFICAR_COLUMNA_ROL.sql
```

### 2. Asignar roles a los usuarios

Para asignar el rol ADMIN a un usuario:
```sql
USE DOC_DIGITALES
GO

UPDATE firmas 
SET rol = 'ADMIN' 
WHERE usuario = 'nombre_del_admin'
```

Para asignar el rol USER a un asesor:
```sql
USE DOC_DIGITALES
GO

UPDATE firmas 
SET rol = 'USER' 
WHERE usuario = 'nombre_del_asesor'
```

### 3. Verificar órdenes existentes (Opcional)

Si tienes órdenes de compra creadas antes de esta implementación, ejecuta:
```
database/ACTUALIZAR_USUARIO_EMAIL_ORDENES.sql
```

Este script te mostrará qué órdenes no tienen un usuario asignado. Estas órdenes solo serán visibles para usuarios ADMIN.

## 🧪 Cómo Probar

### Como Usuario (Asesor)
1. Inicia sesión con un usuario que tenga rol `USER`
2. Ve a "Expedientes" o "Listado de Órdenes"
3. Solo deberías ver las órdenes que TÚ creaste
4. Si intentas acceder a una orden de otro asesor (por URL directa), no podrás verla

### Como Administrador
1. Inicia sesión con un usuario que tenga rol `ADMIN`
2. Ve a "Expedientes" o "Listado de Órdenes"
3. Deberías ver TODAS las órdenes de compra de todos los asesores
4. Puedes aprobar/rechazar cualquier orden

## ❓ Preguntas Frecuentes

**P: ¿Qué pasa si un asesor intenta acceder a una orden de otro asesor?**
R: El sistema no le mostrará la orden. Es como si no existiera para ese usuario.

**P: ¿Los administradores pueden ver las órdenes de los asesores?**
R: Sí, los usuarios con rol ADMIN ven todas las órdenes sin restricción.

**P: ¿Qué pasa con las órdenes antiguas que no tienen usuario asignado?**
R: Solo los usuarios ADMIN podrán verlas. Si quieres asignarlas a un asesor específico, usa el script de actualización.

**P: ¿Cómo cambio el rol de un usuario?**
R: Ejecuta el UPDATE en la tabla `firmas` de la base de datos `DOC_DIGITALES`:
```sql
UPDATE firmas SET rol = 'ADMIN' WHERE usuario = 'nombre_usuario'
```

**P: ¿Puedo tener más de un administrador?**
R: Sí, puedes asignar el rol ADMIN a tantos usuarios como necesites.

## 🔄 Cómo Revertir los Cambios

Si necesitas volver a la versión anterior sin roles:

1. Los archivos modificados están documentados en `SISTEMA_ROLES_IMPLEMENTADO.md`
2. Puedes restaurar desde el control de versiones (git) si lo tienes configurado
3. O contacta al desarrollador para obtener los archivos originales

## 📞 Soporte

Si tienes problemas o preguntas:
1. Revisa el archivo `SISTEMA_ROLES_IMPLEMENTADO.md` para detalles técnicos
2. Verifica que los roles estén correctamente asignados en la base de datos
3. Asegúrate de cerrar sesión y volver a iniciar después de cambiar roles

## ✅ Checklist de Implementación

- [ ] Ejecutar script `VERIFICAR_COLUMNA_ROL.sql`
- [ ] Asignar rol ADMIN a los administradores
- [ ] Asignar rol USER a los asesores
- [ ] (Opcional) Ejecutar script `ACTUALIZAR_USUARIO_EMAIL_ORDENES.sql`
- [ ] Probar con un usuario USER (debe ver solo sus órdenes)
- [ ] Probar con un usuario ADMIN (debe ver todas las órdenes)
- [ ] Verificar que las nuevas órdenes se crean correctamente

---

**Fecha de implementación**: 30 de Octubre de 2025
**Versión**: 1.0
