-- ============================================
-- CONSULTAS ÚTILES PARA SISTEMA DE MARCAS
-- Base de datos: DOC_DIGITALES
-- ============================================

USE DOC_DIGITALES
GO

-- ============================================
-- 1. CONSULTAS DE VERIFICACIÓN
-- ============================================

-- Ver todos los usuarios con sus roles y marcas
SELECT 
    usuario,
    firma_nombre + ' ' + firma_apellido as nombre_completo,
    firma_mail,
    rol,
    ISNULL(marca, 'SIN MARCA') as marcas_asignadas
FROM firmas
ORDER BY rol DESC, marca
GO

-- Ver solo usuarios con marcas asignadas (Jefes de Marca)
SELECT 
    usuario,
    firma_nombre + ' ' + firma_apellido as nombre_completo,
    rol,
    marca as marcas_asignadas
FROM firmas
WHERE marca IS NOT NULL AND marca <> ''
ORDER BY marca
GO

-- Ver usuarios sin marcas (Asesores normales y Admins)
SELECT 
    usuario,
    firma_nombre + ' ' + firma_apellido as nombre_completo,
    rol,
    'SIN MARCA' as marcas_asignadas
FROM firmas
WHERE marca IS NULL OR marca = ''
ORDER BY rol DESC
GO

-- ============================================
-- 2. ASIGNAR MARCAS A USUARIOS
-- ============================================

-- Asignar una sola marca a un usuario
-- Ejemplo: Asignar FORD a un jefe de marca
UPDATE firmas 
SET marca = 'FORD' 
WHERE usuario = 'jefe_ford'
GO

-- Asignar múltiples marcas a un usuario (separadas por comas)
-- Ejemplo: Asignar FORD, SUBARU y TOYOTA
UPDATE firmas 
SET marca = 'FORD,SUBARU,TOYOTA' 
WHERE usuario = 'jefe_multimarca'
GO

-- Quitar marcas a un usuario (convertirlo en usuario normal)
UPDATE firmas 
SET marca = NULL 
WHERE usuario = 'usuario_ejemplo'
GO

-- ============================================
-- 3. CONSULTAS DE ÓRDENES POR MARCA
-- ============================================

-- Ver todas las órdenes de una marca específica
SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_VEHICULO_MARCA,
    OC_VEHICULO_MODELO,
    OC_FECHA_CREACION,
    OC_ESTADO_APROBACION,
    OC_USUARIO_EMAIL
FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA LIKE '%FORD%'
ORDER BY OC_FECHA_CREACION DESC
GO

-- Contar órdenes por marca
SELECT 
    OC_VEHICULO_MARCA as Marca,
    COUNT(*) as Total_Ordenes,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'APROBADO' THEN 1 ELSE 0 END) as Aprobadas,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'PENDIENTE' THEN 1 ELSE 0 END) as Pendientes,
    SUM(CASE WHEN OC_ESTADO_APROBACION = 'RECHAZADO' THEN 1 ELSE 0 END) as Rechazadas
FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA IS NOT NULL
GROUP BY OC_VEHICULO_MARCA
ORDER BY Total_Ordenes DESC
GO

-- Ver órdenes que vería un jefe de marca específico
-- Ejemplo: Ver lo que vería un jefe con marcas FORD,SUBARU
DECLARE @marcas VARCHAR(200) = 'FORD,SUBARU'

SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_VEHICULO_MARCA,
    OC_VEHICULO_MODELO,
    OC_FECHA_CREACION,
    OC_ESTADO_APROBACION
FROM SIST_ORDEN_COMPRA
WHERE 
    OC_VEHICULO_MARCA LIKE '%FORD%' 
    OR OC_VEHICULO_MARCA LIKE '%SUBARU%'
ORDER BY OC_FECHA_CREACION DESC
GO

-- ============================================
-- 4. ESTADÍSTICAS POR MARCA
-- ============================================

-- Resumen de usuarios por tipo
SELECT 
    'Usuarios con marcas (Jefes de Marca)' as Tipo,
    COUNT(*) as Cantidad
FROM firmas
WHERE marca IS NOT NULL AND marca <> ''
UNION ALL
SELECT 
    'Usuarios sin marcas (Asesores/Admins)' as Tipo,
    COUNT(*) as Cantidad
FROM firmas
WHERE marca IS NULL OR marca = ''
GO

-- Ver todas las marcas únicas en el sistema
SELECT DISTINCT 
    OC_VEHICULO_MARCA as Marca_Vehiculo
FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA IS NOT NULL AND OC_VEHICULO_MARCA <> ''
ORDER BY OC_VEHICULO_MARCA
GO

-- ============================================
-- 5. VERIFICACIÓN DE PERMISOS
-- ============================================

-- Verificar qué puede hacer un usuario específico
DECLARE @usuario VARCHAR(50) = 'usuario_ejemplo'

SELECT 
    usuario,
    firma_nombre + ' ' + firma_apellido as nombre_completo,
    rol,
    marca,
    CASE 
        WHEN rol = 'ADMIN' THEN 'Puede ver TODAS las órdenes y EDITAR/APROBAR'
        WHEN rol = 'USER' AND (marca IS NULL OR marca = '') THEN 'Puede ver solo SUS órdenes y EDITARLAS'
        WHEN rol = 'USER' AND marca IS NOT NULL AND marca <> '' THEN 'Puede ver órdenes de marcas: ' + marca + ' (SOLO VISUALIZACIÓN)'
        ELSE 'Sin permisos'
    END as Permisos
FROM firmas
WHERE usuario = @usuario
GO

-- ============================================
-- 6. MANTENIMIENTO Y LIMPIEZA
-- ============================================

-- Ver usuarios con marcas que no existen en órdenes
SELECT DISTINCT
    f.usuario,
    f.marca as marca_asignada,
    'NO EXISTE EN ÓRDENES' as estado
FROM firmas f
WHERE f.marca IS NOT NULL AND f.marca <> ''
AND NOT EXISTS (
    SELECT 1 
    FROM SIST_ORDEN_COMPRA oc 
    WHERE oc.OC_VEHICULO_MARCA LIKE '%' + f.marca + '%'
)
GO

-- Ver órdenes sin marca asignada
SELECT 
    OC_ID,
    OC_NUMERO_EXPEDIENTE,
    OC_COMPRADOR_NOMBRE,
    OC_VEHICULO_MARCA,
    OC_FECHA_CREACION
FROM SIST_ORDEN_COMPRA
WHERE OC_VEHICULO_MARCA IS NULL OR OC_VEHICULO_MARCA = ''
ORDER BY OC_FECHA_CREACION DESC
GO

-- ============================================
-- 7. EJEMPLOS DE CONFIGURACIÓN COMÚN
-- ============================================

-- Crear un jefe de marca FORD
UPDATE firmas 
SET marca = 'FORD', rol = 'USER'
WHERE usuario = 'jefe_ford'
GO

-- Crear un jefe de múltiples marcas
UPDATE firmas 
SET marca = 'FORD,SUBARU,TOYOTA', rol = 'USER'
WHERE usuario = 'jefe_multimarca'
GO

-- Crear un asesor normal (sin marcas)
UPDATE firmas 
SET marca = NULL, rol = 'USER'
WHERE usuario = 'asesor_ventas'
GO

-- Crear un administrador
UPDATE firmas 
SET marca = NULL, rol = 'ADMIN'
WHERE usuario = 'admin_sistema'
GO

-- ============================================
-- 8. AUDITORÍA Y MONITOREO
-- ============================================

-- Ver actividad reciente por marca
SELECT 
    OC_VEHICULO_MARCA as Marca,
    COUNT(*) as Ordenes_Ultimos_30_Dias,
    MAX(OC_FECHA_CREACION) as Ultima_Orden
FROM SIST_ORDEN_COMPRA
WHERE OC_FECHA_CREACION >= DATEADD(DAY, -30, GETDATE())
GROUP BY OC_VEHICULO_MARCA
ORDER BY Ordenes_Ultimos_30_Dias DESC
GO

-- Ver qué jefes de marca tienen acceso a cada orden
SELECT 
    oc.OC_ID,
    oc.OC_NUMERO_EXPEDIENTE,
    oc.OC_VEHICULO_MARCA,
    f.usuario as Jefe_Marca_Con_Acceso,
    f.firma_nombre + ' ' + f.firma_apellido as Nombre_Jefe
FROM SIST_ORDEN_COMPRA oc
CROSS JOIN firmas f
WHERE 
    f.marca IS NOT NULL 
    AND f.marca <> ''
    AND oc.OC_VEHICULO_MARCA LIKE '%' + f.marca + '%'
ORDER BY oc.OC_NUMERO_EXPEDIENTE, f.usuario
GO

-- ============================================
-- 9. VALIDACIONES
-- ============================================

-- Verificar que la columna marca existe
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    CHARACTER_MAXIMUM_LENGTH,
    IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'firmas' AND COLUMN_NAME = 'marca'
GO

-- Si la columna no existe, crearla (ejecutar solo si es necesario)
-- ALTER TABLE firmas ADD marca VARCHAR(200) NULL
-- GO

-- ============================================
-- 10. REPORTES ÚTILES
-- ============================================

-- Reporte completo de accesos por usuario
SELECT 
    f.usuario,
    f.firma_nombre + ' ' + f.firma_apellido as nombre_completo,
    f.rol,
    f.marca,
    CASE 
        WHEN f.rol = 'ADMIN' THEN (SELECT COUNT(*) FROM SIST_ORDEN_COMPRA)
        WHEN f.rol = 'USER' AND (f.marca IS NULL OR f.marca = '') THEN 
            (SELECT COUNT(*) FROM SIST_ORDEN_COMPRA WHERE OC_USUARIO_EMAIL = f.firma_mail)
        WHEN f.rol = 'USER' AND f.marca IS NOT NULL THEN
            (SELECT COUNT(*) FROM SIST_ORDEN_COMPRA WHERE OC_VEHICULO_MARCA LIKE '%' + f.marca + '%')
        ELSE 0
    END as Ordenes_Con_Acceso
FROM firmas f
ORDER BY f.rol DESC, Ordenes_Con_Acceso DESC
GO
