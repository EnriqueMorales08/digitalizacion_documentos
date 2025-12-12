-- =====================================================
-- CONSULTAS ÚTILES PARA GESTIÓN DE ROLES
-- Base de datos: DOC_DIGITALES y FACCARPRUEBA
-- Fecha: 30 de Octubre de 2025
-- =====================================================

-- =====================================================
-- GESTIÓN DE USUARIOS Y ROLES
-- =====================================================

-- 1. Ver todos los usuarios y sus roles
USE [DOC_DIGITALES]
GO

SELECT 
    usuario,
    firma_nombre,
    firma_apellido,
    firma_mail,
    ISNULL(rol, 'SIN ROL') as rol
FROM firmas
ORDER BY rol DESC, usuario
GO

-- 2. Asignar rol ADMIN a un usuario específico
/*
UPDATE firmas 
SET rol = 'ADMIN' 
WHERE usuario = 'nombre_del_usuario'
*/

-- 3. Asignar rol USER a un usuario específico
/*
UPDATE firmas 
SET rol = 'USER' 
WHERE usuario = 'nombre_del_usuario'
*/

-- 4. Asignar rol USER a todos los usuarios sin rol
/*
UPDATE firmas 
SET rol = 'USER' 
WHERE rol IS NULL OR rol = ''
*/

-- 5. Ver solo administradores
SELECT 
    usuario,
    firma_nombre + ' ' + firma_apellido as nombre_completo,
    firma_mail
FROM firmas
WHERE rol = 'ADMIN'
GO

-- 6. Ver solo usuarios/asesores
SELECT 
    usuario,
    firma_nombre + ' ' + firma_apellido as nombre_completo,
    firma_mail
FROM firmas
WHERE rol = 'USER'
GO

-- =====================================================
-- GESTIÓN DE ÓRDENES DE COMPRA
-- =====================================================

USE [FACCARPRUEBA]
GO

-- 7. Ver órdenes sin usuario asignado (solo visibles para ADMIN)
SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_ASESOR_VENTA,
    OC_FECHA_CREACION,
    OC_USUARIO_EMAIL,
    OC_USUARIO_NOMBRE
FROM SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL IS NULL OR OC_USUARIO_EMAIL = ''
ORDER BY OC_FECHA_CREACION DESC
GO

-- 8. Ver órdenes de un asesor específico
SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_FECHA_CREACION,
    OC_ESTADO_APROBACION
FROM SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL = 'email@ejemplo.com'
ORDER BY OC_FECHA_CREACION DESC
GO

-- 9. Contar órdenes por asesor
SELECT 
    ISNULL(OC_USUARIO_NOMBRE, 'SIN ASIGNAR') as Asesor,
    ISNULL(OC_USUARIO_EMAIL, 'N/A') as Email,
    COUNT(*) as Total_Ordenes,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'PENDIENTE' THEN 1 ELSE 0 END) as Pendientes,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'APROBADO' THEN 1 ELSE 0 END) as Aprobadas,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'RECHAZADO' THEN 1 ELSE 0 END) as Rechazadas
FROM SIST_ORDEN_COMPRA
GROUP BY OC_USUARIO_NOMBRE, OC_USUARIO_EMAIL
ORDER BY Total_Ordenes DESC
GO

-- 10. Asignar un usuario a una orden específica
/*
UPDATE SIST_ORDEN_COMPRA
SET OC_USUARIO_EMAIL = 'email@ejemplo.com',
    OC_USUARIO_NOMBRE = 'Nombre Completo'
WHERE OC_ID = 123
*/

-- 11. Asignar todas las órdenes huérfanas a un administrador
/*
UPDATE SIST_ORDEN_COMPRA
SET OC_USUARIO_EMAIL = 'admin@faccar.com',
    OC_USUARIO_NOMBRE = 'Administrador Sistema'
WHERE OC_USUARIO_EMAIL IS NULL OR OC_USUARIO_EMAIL = ''
*/

-- 12. Ver últimas 10 órdenes creadas con información del asesor
SELECT TOP 10
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_USUARIO_NOMBRE as Asesor,
    OC_USUARIO_EMAIL as Email_Asesor,
    OC_FECHA_CREACION,
    OC_ESTADO_APROBACION
FROM SIST_ORDEN_COMPRA
ORDER BY OC_FECHA_CREACION DESC
GO

-- =====================================================
-- AUDITORÍA Y REPORTES
-- =====================================================

-- 13. Reporte de actividad por asesor (último mes)
SELECT 
    OC_USUARIO_NOMBRE as Asesor,
    COUNT(*) as Ordenes_Creadas,
    MIN(OC_FECHA_CREACION) as Primera_Orden,
    MAX(OC_FECHA_CREACION) as Ultima_Orden
FROM SIST_ORDEN_COMPRA
WHERE OC_FECHA_CREACION >= DATEADD(MONTH, -1, GETDATE())
    AND OC_USUARIO_EMAIL IS NOT NULL
GROUP BY OC_USUARIO_NOMBRE
ORDER BY Ordenes_Creadas DESC
GO

-- 14. Ver órdenes pendientes por asesor
SELECT 
    OC_USUARIO_NOMBRE as Asesor,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_FECHA_CREACION,
    DATEDIFF(DAY, OC_FECHA_CREACION, GETDATE()) as Dias_Pendiente
FROM SIST_ORDEN_COMPRA
WHERE OC_ESTADO_APROBACION = 'PENDIENTE'
    AND OC_USUARIO_EMAIL IS NOT NULL
ORDER BY OC_USUARIO_NOMBRE, OC_FECHA_CREACION
GO

-- 15. Verificar integridad: Órdenes con email pero sin nombre
SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_USUARIO_EMAIL,
    OC_USUARIO_NOMBRE
FROM SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL IS NOT NULL 
    AND (OC_USUARIO_NOMBRE IS NULL OR OC_USUARIO_NOMBRE = '')
GO

-- =====================================================
-- MANTENIMIENTO
-- =====================================================

-- 16. Sincronizar nombres de usuario desde tabla firmas
/*
UPDATE oc
SET oc.OC_USUARIO_NOMBRE = f.firma_nombre + ' ' + f.firma_apellido
FROM SIST_ORDEN_COMPRA oc
INNER JOIN DOC_DIGITALES.dbo.firmas f ON oc.OC_USUARIO_EMAIL = f.firma_mail
WHERE oc.OC_USUARIO_NOMBRE IS NULL OR oc.OC_USUARIO_NOMBRE = ''
*/

-- 17. Limpiar roles inválidos
/*
UPDATE firmas
SET rol = 'USER'
WHERE rol NOT IN ('ADMIN', 'USER')
    AND rol IS NOT NULL
*/

-- =====================================================
-- CONSULTAS DE VERIFICACIÓN POST-IMPLEMENTACIÓN
-- =====================================================

-- 18. Verificar que todos los usuarios tienen rol asignado
USE [DOC_DIGITALES]
GO

SELECT 
    COUNT(*) as Total_Usuarios,
    SUM(CASE WHEN rol = 'ADMIN' THEN 1 ELSE 0 END) as Admins,
    SUM(CASE WHEN rol = 'USER' THEN 1 ELSE 0 END) as Users,
    SUM(CASE WHEN rol IS NULL OR rol = '' THEN 1 ELSE 0 END) as Sin_Rol
FROM firmas
GO

-- 19. Verificar que todas las órdenes tienen usuario asignado
USE [FACCARPRUEBA]
GO

SELECT 
    COUNT(*) as Total_Ordenes,
    SUM(CASE WHEN OC_USUARIO_EMAIL IS NOT NULL AND OC_USUARIO_EMAIL != '' THEN 1 ELSE 0 END) as Con_Usuario,
    SUM(CASE WHEN OC_USUARIO_EMAIL IS NULL OR OC_USUARIO_EMAIL = '' THEN 1 ELSE 0 END) as Sin_Usuario
FROM SIST_ORDEN_COMPRA
GO

-- =====================================================
-- FIN DE CONSULTAS ÚTILES
-- =====================================================
