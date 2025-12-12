-- =====================================================
-- CONSULTAS ÚTILES PARA GESTIÓN DE JEFE DE MARCA
-- Base de datos: DOC_DIGITALES y FACCARPRUEBA
-- Fecha: 20 de Noviembre de 2025
-- =====================================================

-- =====================================================
-- GESTIÓN DE ROLES: JEFE DE MARCA
-- =====================================================

USE [DOC_DIGITALES]
GO

-- 1. Ver todos los usuarios con sus roles, marcas y tiendas
SELECT 
    usuario,
    firma_nombre + ' ' + firma_apellido as nombre_completo,
    firma_mail,
    ISNULL(rol, 'SIN ROL') as rol,
    ISNULL(marca, 'SIN MARCA') as marca,
    ISNULL(tienda, 'SIN TIENDA') as tienda
FROM firmas
ORDER BY 
    CASE rol 
        WHEN 'ADMIN' THEN 1 
        WHEN 'JEFE DE MARCA' THEN 2 
        WHEN 'USER' THEN 3 
        ELSE 4 
    END,
    usuario
GO

-- 2. Ver solo JEFES DE MARCA
SELECT 
    usuario,
    firma_nombre + ' ' + firma_apellido as nombre_completo,
    firma_mail,
    marca,
    tienda
FROM firmas
WHERE rol = 'JEFE DE MARCA'
ORDER BY usuario
GO

-- 3. Ver solo USERS (Asesores)
SELECT 
    usuario,
    firma_nombre + ' ' + firma_apellido as nombre_completo,
    firma_mail,
    marca,
    tienda
FROM firmas
WHERE rol = 'USER'
ORDER BY marca, tienda, usuario
GO

-- =====================================================
-- ASIGNAR ROLES Y PERMISOS
-- =====================================================

-- 4. Asignar rol JEFE DE MARCA a un usuario (con una marca y un centro de costo)
/*
UPDATE firmas 
SET rol = 'JEFE DE MARCA',
    marca = 'FORD',
    tienda = '01272'
WHERE usuario = 'nombre_del_usuario'
*/

-- 5. Asignar rol JEFE DE MARCA con múltiples marcas
/*
UPDATE firmas 
SET rol = 'JEFE DE MARCA',
    marca = 'FORD,KIA,PEUGEOT',
    tienda = '01272'
WHERE usuario = 'nombre_del_usuario'
*/

-- 6. Asignar rol JEFE DE MARCA con múltiples centros de costo
/*
UPDATE firmas 
SET rol = 'JEFE DE MARCA',
    marca = 'FORD',
    tienda = '01272,01271,01270'
WHERE usuario = 'nombre_del_usuario'
*/

-- 7. Asignar rol JEFE DE MARCA con múltiples marcas Y múltiples centros de costo
/*
UPDATE firmas 
SET rol = 'JEFE DE MARCA',
    marca = 'FORD,KIA',
    tienda = '01272,01271'
WHERE usuario = 'nombre_del_usuario'
*/

-- 8. Asignar rol USER (Asesor)
/*
UPDATE firmas 
SET rol = 'USER',
    marca = 'FORD',
    tienda = '01272'
WHERE usuario = 'nombre_del_usuario'
*/

-- 9. Asignar rol ADMIN
/*
UPDATE firmas 
SET rol = 'ADMIN',
    marca = NULL,
    tienda = NULL
WHERE usuario = 'nombre_del_usuario'
*/

-- =====================================================
-- CONSULTAS DE ÓRDENES DE COMPRA
-- =====================================================

USE [FACCARPRUEBA]
GO

-- 10. Ver órdenes de una marca específica y tienda específica (por código de centro de costo)
SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_VEHICULO_MARCA,
    OC_CENTRO_COSTO,
    OC_USUARIO_NOMBRE as Asesor,
    OC_FECHA_CREACION,
    OC_ESTADO_APROBACION
FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA LIKE '%FORD%'
  AND OC_CENTRO_COSTO LIKE '%01272%'
ORDER BY OC_FECHA_CREACION DESC
GO

-- 11. Ver órdenes de múltiples marcas en una tienda
SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_VEHICULO_MARCA,
    OC_CENTRO_COSTO,
    OC_USUARIO_NOMBRE as Asesor,
    OC_FECHA_CREACION,
    OC_ESTADO_APROBACION
FROM SIST_ORDEN_COMPRA
WHERE (OC_VEHICULO_MARCA LIKE '%FORD%' OR OC_VEHICULO_MARCA LIKE '%KIA%')
  AND OC_CENTRO_COSTO LIKE '%01272%'
ORDER BY OC_FECHA_CREACION DESC
GO

-- 12. Ver órdenes de una marca en múltiples tiendas
SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_VEHICULO_MARCA,
    OC_CENTRO_COSTO,
    OC_USUARIO_NOMBRE as Asesor,
    OC_FECHA_CREACION,
    OC_ESTADO_APROBACION
FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA LIKE '%FORD%'
  AND (OC_CENTRO_COSTO LIKE '%01272%' 
       OR OC_CENTRO_COSTO LIKE '%01271%'
       OR OC_CENTRO_COSTO LIKE '%01270%')
ORDER BY OC_FECHA_CREACION DESC
GO

-- 13. Contar órdenes por marca y tienda (código de centro de costo)
SELECT 
    OC_VEHICULO_MARCA as Marca,
    OC_CENTRO_COSTO as Codigo_Centro_Costo,
    COUNT(*) as Total_Ordenes,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'PENDIENTE' THEN 1 ELSE 0 END) as Pendientes,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'APROBADO' THEN 1 ELSE 0 END) as Aprobadas,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'RECHAZADO' THEN 1 ELSE 0 END) as Rechazadas
FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA IS NOT NULL 
  AND OC_CENTRO_COSTO IS NOT NULL
GROUP BY OC_VEHICULO_MARCA, OC_CENTRO_COSTO
ORDER BY Marca, Codigo_Centro_Costo
GO

-- 14. Ver órdenes de un asesor específico
SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_VEHICULO_MARCA,
    OC_CENTRO_COSTO,
    OC_FECHA_CREACION,
    OC_ESTADO_APROBACION
FROM SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL = 'email_del_asesor@ejemplo.com'
ORDER BY OC_FECHA_CREACION DESC
GO

-- 15. Contar órdenes por asesor (para un jefe de marca)
SELECT 
    OC_USUARIO_NOMBRE as Asesor,
    OC_USUARIO_EMAIL as Email,
    OC_VEHICULO_MARCA as Marca,
    OC_CENTRO_COSTO as Codigo_Centro_Costo,
    COUNT(*) as Total_Ordenes,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'PENDIENTE' THEN 1 ELSE 0 END) as Pendientes,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'APROBADO' THEN 1 ELSE 0 END) as Aprobadas,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'RECHAZADO' THEN 1 ELSE 0 END) as Rechazadas
FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA LIKE '%FORD%'
  AND OC_CENTRO_COSTO LIKE '%01272%'
  AND OC_USUARIO_EMAIL IS NOT NULL
GROUP BY OC_USUARIO_NOMBRE, OC_USUARIO_EMAIL, OC_VEHICULO_MARCA, OC_CENTRO_COSTO
ORDER BY Total_Ordenes DESC
GO

-- =====================================================
-- REPORTES Y ESTADÍSTICAS
-- =====================================================

-- 16. Reporte de actividad por marca y tienda (último mes)
SELECT 
    OC_VEHICULO_MARCA as Marca,
    OC_CENTRO_COSTO as Codigo_Centro_Costo,
    COUNT(*) as Ordenes_Creadas,
    MIN(OC_FECHA_CREACION) as Primera_Orden,
    MAX(OC_FECHA_CREACION) as Ultima_Orden
FROM SIST_ORDEN_COMPRA
WHERE OC_FECHA_CREACION >= DATEADD(MONTH, -1, GETDATE())
GROUP BY OC_VEHICULO_MARCA, OC_CENTRO_COSTO
ORDER BY Ordenes_Creadas DESC
GO

-- 17. Ver órdenes pendientes por marca y tienda (código de centro de costo)
SELECT 
    OC_VEHICULO_MARCA as Marca,
    OC_CENTRO_COSTO as Codigo_Centro_Costo,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_USUARIO_NOMBRE as Asesor,
    OC_FECHA_CREACION,
    DATEDIFF(DAY, OC_FECHA_CREACION, GETDATE()) as Dias_Pendiente
FROM SIST_ORDEN_COMPRA
WHERE OC_ESTADO_APROBACION = 'PENDIENTE'
  AND OC_VEHICULO_MARCA LIKE '%FORD%'
  AND OC_CENTRO_COSTO LIKE '%01272%'
ORDER BY Dias_Pendiente DESC
GO

-- 18. Top 10 asesores con más órdenes (por marca y tienda)
SELECT TOP 10
    OC_USUARIO_NOMBRE as Asesor,
    OC_USUARIO_EMAIL as Email,
    COUNT(*) as Total_Ordenes,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'APROBADO' THEN 1 ELSE 0 END) as Aprobadas,
    CAST(SUM(CASE WHEN OC_ESTADO_APROBACION = 'APROBADO' THEN 1 ELSE 0 END) * 100.0 / COUNT(*) AS DECIMAL(5,2)) as Porcentaje_Aprobacion
FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA LIKE '%FORD%'
  AND OC_CENTRO_COSTO LIKE '%01272%'
  AND OC_USUARIO_EMAIL IS NOT NULL
GROUP BY OC_USUARIO_NOMBRE, OC_USUARIO_EMAIL
ORDER BY Total_Ordenes DESC
GO

-- =====================================================
-- VERIFICACIÓN Y VALIDACIÓN
-- =====================================================

-- 19. Verificar que todos los usuarios tienen marca y tienda asignada
USE [DOC_DIGITALES]
GO

SELECT 
    usuario,
    firma_nombre + ' ' + firma_apellido as nombre_completo,
    rol,
    CASE 
        WHEN marca IS NULL OR marca = '' THEN '❌ SIN MARCA'
        ELSE '✅ ' + marca
    END as marca,
    CASE 
        WHEN tienda IS NULL OR tienda = '' THEN '❌ SIN TIENDA'
        ELSE '✅ ' + tienda
    END as tienda
FROM firmas
WHERE rol IN ('USER', 'JEFE DE MARCA')
ORDER BY rol, usuario
GO

-- 20. Verificar distribución de roles
SELECT 
    ISNULL(rol, 'SIN ROL') as Rol,
    COUNT(*) as Total_Usuarios
FROM firmas
GROUP BY rol
ORDER BY 
    CASE rol 
        WHEN 'ADMIN' THEN 1 
        WHEN 'JEFE DE MARCA' THEN 2 
        WHEN 'USER' THEN 3 
        ELSE 4 
    END
GO

-- 21. Ver órdenes sin marca o centro de costo asignado (posibles problemas)
USE [FACCARPRUEBA]
GO

SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    CASE 
        WHEN OC_VEHICULO_MARCA IS NULL OR OC_VEHICULO_MARCA = '' THEN '❌ SIN MARCA'
        ELSE OC_VEHICULO_MARCA
    END as Marca,
    CASE 
        WHEN OC_CENTRO_COSTO IS NULL OR OC_CENTRO_COSTO = '' THEN '❌ SIN CENTRO COSTO'
        ELSE OC_CENTRO_COSTO
    END as Centro_Costo,
    OC_FECHA_CREACION
FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA IS NULL 
   OR OC_VEHICULO_MARCA = ''
   OR OC_CENTRO_COSTO IS NULL
   OR OC_CENTRO_COSTO = ''
ORDER BY OC_FECHA_CREACION DESC
GO

-- =====================================================
-- EJEMPLOS DE CASOS DE USO REALES
-- =====================================================

-- EJEMPLO 1: Crear un Jefe de Marca FORD para un centro de costo (01272)
/*
USE [DOC_DIGITALES]
GO

UPDATE firmas 
SET rol = 'JEFE DE MARCA',
    marca = 'FORD',
    tienda = '01272'
WHERE usuario = 'jgarcia'
GO
*/

-- EJEMPLO 2: Crear un Jefe de Marca FORD para múltiples centros de costo (01272, 01271, 01270)
/*
USE [DOC_DIGITALES]
GO

UPDATE firmas 
SET rol = 'JEFE DE MARCA',
    marca = 'FORD',
    tienda = '01272,01271,01270'
WHERE usuario = 'mlopez'
GO
*/

-- EJEMPLO 3: Crear un Jefe de Marca de múltiples marcas (FORD, KIA) en un centro de costo (01272)
/*
USE [DOC_DIGITALES]
GO

UPDATE firmas 
SET rol = 'JEFE DE MARCA',
    marca = 'FORD,KIA',
    tienda = '01272'
WHERE usuario = 'rsanchez'
GO
*/

-- EJEMPLO 4: Crear un asesor USER de FORD en un centro de costo (01272)
/*
USE [DOC_DIGITALES]
GO

UPDATE firmas 
SET rol = 'USER',
    marca = 'FORD',
    tienda = '01272'
WHERE usuario = 'adiaz'
GO
*/

-- =====================================================
-- FIN DE CONSULTAS ÚTILES
-- =====================================================
