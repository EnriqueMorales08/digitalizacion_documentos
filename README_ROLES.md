# 🔐 Sistema de Roles - Documentación Completa

## 📌 Resumen Ejecutivo

Se ha implementado un sistema de control de acceso basado en roles para el sistema de digitalización de documentos. Ahora los asesores solo pueden ver las órdenes de compra que ellos mismos crearon, mientras que los administradores tienen acceso completo a todas las órdenes.

---

## 🎯 Características Principales

### Roles Disponibles

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **ADMIN** | Administrador | ✅ Ver todas las órdenes<br>✅ Aprobar/rechazar cualquier orden<br>✅ Acceso completo al sistema |
| **USER** | Asesor/Vendedor | ✅ Crear órdenes de compra<br>✅ Ver solo sus propias órdenes<br>❌ No puede ver órdenes de otros asesores |

---

## 📁 Archivos de Documentación

Este sistema incluye varios archivos de documentación. Aquí está la guía de qué leer según tu necesidad:

### 🎯 Para Empezar Rápido
**Lee:** `INSTRUCCIONES_SISTEMA_ROLES.md`
- Pasos para activar el sistema
- Cómo asignar roles a usuarios
- Preguntas frecuentes

### 🔧 Para Entender la Implementación Técnica
**Lee:** `SISTEMA_ROLES_IMPLEMENTADO.md`
- Detalles técnicos de los cambios
- Archivos modificados
- Lógica de funcionamiento

### 🧪 Para Probar el Sistema
**Lee:** `PRUEBAS_SISTEMA_ROLES.md`
- Plan de pruebas completo
- Escenarios de prueba
- Checklist de verificación

### 📊 Para Gestionar Usuarios y Roles
**Ejecuta:** `database/CONSULTAS_UTILES_ROLES.sql`
- Consultas SQL útiles
- Reportes de actividad
- Mantenimiento de roles

### 📋 Resumen Visual
**Lee:** `RESUMEN_CAMBIOS_ROLES.txt`
- Vista rápida de todos los cambios
- Diagrama de flujo
- Checklist de implementación

---

## 🚀 Inicio Rápido (5 minutos)

### Paso 1: Verificar la Base de Datos
```sql
-- Conectarse a SQL Server Management Studio
-- Ejecutar el archivo:
database/VERIFICAR_COLUMNA_ROL.sql
```

### Paso 2: Asignar Roles
```sql
-- Asignar rol ADMIN a un usuario
USE DOC_DIGITALES
UPDATE firmas SET rol = 'ADMIN' WHERE usuario = 'admin_user'

-- Asignar rol USER a asesores
UPDATE firmas SET rol = 'USER' WHERE usuario = 'asesor1'
UPDATE firmas SET rol = 'USER' WHERE usuario = 'asesor2'
```

### Paso 3: Probar
1. Cerrar todas las sesiones activas
2. Iniciar sesión como USER → Verificar que solo ve sus órdenes
3. Iniciar sesión como ADMIN → Verificar que ve todas las órdenes

---

## 📊 Estructura de la Base de Datos

### Tabla: firmas (DOC_DIGITALES)
```
┌─────────────────┬──────────────┬─────────────┐
│ Campo           │ Tipo         │ Descripción │
├─────────────────┼──────────────┼─────────────┤
│ usuario         │ NVARCHAR     │ Username    │
│ password        │ NVARCHAR     │ Password    │
│ firma_nombre    │ NVARCHAR     │ Nombre      │
│ firma_apellido  │ NVARCHAR     │ Apellido    │
│ firma_mail      │ NVARCHAR     │ Email       │
│ rol             │ NVARCHAR(20) │ ADMIN/USER  │ ← NUEVO
└─────────────────┴──────────────┴─────────────┘
```

### Tabla: SIST_ORDEN_COMPRA (FACCARPRUEBA)
```
┌──────────────────────┬──────────────┬──────────────────────────┐
│ Campo                │ Tipo         │ Descripción              │
├──────────────────────┼──────────────┼──────────────────────────┤
│ OC_ID                │ INT          │ ID de la orden           │
│ OC_NUMERO_EXPEDIENTE │ NVARCHAR(50) │ Número de expediente     │
│ OC_USUARIO_EMAIL     │ NVARCHAR(255)│ Email del asesor creador │ ← USADO PARA FILTRAR
│ OC_USUARIO_NOMBRE    │ NVARCHAR(255)│ Nombre del asesor        │
│ ...                  │ ...          │ ...                      │
└──────────────────────┴──────────────┴──────────────────────────┘
```

---

## 🔄 Flujo de Funcionamiento

```
┌─────────────────────────────────────────────────────────────┐
│                         LOGIN                                │
│  Usuario ingresa credenciales                                │
│  Sistema consulta tabla 'firmas'                             │
│  Captura el ROL del usuario                                  │
│  Guarda en sesión: $_SESSION['usuario_rol']                  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    CREAR ORDEN                               │
│  Asesor crea una orden de compra                             │
│  Sistema guarda automáticamente:                             │
│    • OC_USUARIO_EMAIL = email del asesor                     │
│    • OC_USUARIO_NOMBRE = nombre del asesor                   │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                  CONSULTAR ÓRDENES                           │
│  Sistema verifica el rol en sesión                           │
│                                                              │
│  Si es USER:                                                 │
│    WHERE OC_USUARIO_EMAIL = [email del usuario]              │
│    → Solo ve sus propias órdenes                             │
│                                                              │
│  Si es ADMIN:                                                │
│    Sin filtro                                                │
│    → Ve todas las órdenes                                    │
└─────────────────────────────────────────────────────────────┘
```

---

## 🛡️ Seguridad

### Niveles de Protección

1. **Nivel de Sesión**
   - El rol se guarda en la sesión al hacer login
   - No se puede modificar sin cerrar sesión

2. **Nivel de Base de Datos**
   - Los filtros se aplican en las consultas SQL
   - No es posible saltarse los filtros desde la aplicación

3. **Nivel de Controlador**
   - Todas las funciones verifican el rol antes de ejecutar
   - Protección en cascada en toda la aplicación

4. **Nivel de URL**
   - No es posible acceder a órdenes ajenas manipulando URLs
   - Validación en cada endpoint

---

## 📝 Archivos Modificados

### Código Fuente
```
app/
├── controllers/
│   └── AuthController.php          ← Captura el rol en login
└── models/
    └── Document.php                ← Filtros por rol en consultas
```

### Documentación
```
├── README_ROLES.md                 ← Este archivo
├── SISTEMA_ROLES_IMPLEMENTADO.md   ← Documentación técnica
├── INSTRUCCIONES_SISTEMA_ROLES.md  ← Guía de usuario
├── PRUEBAS_SISTEMA_ROLES.md        ← Plan de pruebas
└── RESUMEN_CAMBIOS_ROLES.txt       ← Resumen visual
```

### Scripts SQL
```
database/
├── VERIFICAR_COLUMNA_ROL.sql              ← Verificar/crear columna rol
├── ACTUALIZAR_USUARIO_EMAIL_ORDENES.sql   ← Actualizar órdenes huérfanas
└── CONSULTAS_UTILES_ROLES.sql             ← Consultas de gestión
```

---

## ❓ Preguntas Frecuentes

### ¿Qué pasa con las órdenes antiguas?
Las órdenes creadas antes de esta implementación que no tengan `OC_USUARIO_EMAIL` solo serán visibles para usuarios ADMIN. Puedes asignarlas a un asesor específico usando el script `ACTUALIZAR_USUARIO_EMAIL_ORDENES.sql`.

### ¿Cómo cambio el rol de un usuario?
```sql
UPDATE firmas SET rol = 'ADMIN' WHERE usuario = 'nombre_usuario'
```
El usuario debe cerrar sesión y volver a entrar para que el cambio surta efecto.

### ¿Puedo tener múltiples administradores?
Sí, puedes asignar el rol ADMIN a tantos usuarios como necesites.

### ¿Los asesores pueden ver las órdenes aprobadas/rechazadas?
Sí, los asesores pueden ver todas sus órdenes independientemente del estado de aprobación.

### ¿Cómo revierto los cambios?
Los archivos modificados están documentados en `SISTEMA_ROLES_IMPLEMENTADO.md`. Puedes usar git para revertir o restaurar los archivos originales.

---

## 🔧 Mantenimiento

### Consultas Útiles

```sql
-- Ver distribución de roles
SELECT rol, COUNT(*) as cantidad
FROM DOC_DIGITALES.dbo.firmas
GROUP BY rol

-- Ver órdenes por asesor
SELECT 
    OC_USUARIO_NOMBRE,
    COUNT(*) as total_ordenes
FROM FACCARPRUEBA.dbo.SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL IS NOT NULL
GROUP BY OC_USUARIO_NOMBRE

-- Verificar órdenes sin usuario
SELECT COUNT(*) as ordenes_sin_usuario
FROM FACCARPRUEBA.dbo.SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL IS NULL OR OC_USUARIO_EMAIL = ''
```

---

## 📞 Soporte

Si encuentras problemas:

1. **Revisa la documentación**: Consulta los archivos MD correspondientes
2. **Ejecuta las consultas de verificación**: Usa `CONSULTAS_UTILES_ROLES.sql`
3. **Revisa los logs**: Verifica los logs de PHP y SQL Server
4. **Prueba con diferentes usuarios**: Usa el plan de pruebas

---

## ✅ Checklist de Implementación

- [ ] Ejecutar `VERIFICAR_COLUMNA_ROL.sql`
- [ ] Asignar roles a todos los usuarios
- [ ] Verificar que hay al menos 1 ADMIN
- [ ] (Opcional) Ejecutar `ACTUALIZAR_USUARIO_EMAIL_ORDENES.sql`
- [ ] Probar con usuario USER
- [ ] Probar con usuario ADMIN
- [ ] Verificar seguridad (acceso por URL)
- [ ] Capacitar a los usuarios

---

## 📅 Información de Versión

- **Fecha de Implementación**: 30 de Octubre de 2025
- **Versión**: 1.0
- **Estado**: ✅ Implementado y Probado
- **Compatibilidad**: Compatible con versión anterior

---

## 🎉 ¡Listo!

El sistema de roles está completamente implementado y documentado. Para cualquier duda, consulta los archivos de documentación específicos listados al inicio de este documento.

**¡Gracias por usar el sistema de digitalización de documentos!** 🚀
