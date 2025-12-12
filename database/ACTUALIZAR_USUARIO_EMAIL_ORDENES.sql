-- =====================================================
-- Script para actualizar OC_USUARIO_EMAIL en órdenes existentes
-- Base de datos: FACCARPRUEBA
-- Tabla: SIST_ORDEN_COMPRA
-- Fecha: 30 de Octubre de 2025
-- =====================================================

USE [FACCARPRUEBA]
GO

PRINT '🔍 Verificando órdenes sin OC_USUARIO_EMAIL...'
GO

-- Contar órdenes sin email de usuario
DECLARE @OrdenesVacias INT
SELECT @OrdenesVacias = COUNT(*)
FROM SIST_ORDEN_COMPRA
WHERE OC_USUARIO_EMAIL IS NULL OR OC_USUARIO_EMAIL = ''

PRINT '📊 Órdenes sin OC_USUARIO_EMAIL: ' + CAST(@OrdenesVacias AS VARCHAR(10))
GO

-- Mostrar órdenes sin email
PRINT ''
PRINT '📋 Órdenes de compra sin email de usuario:'
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

PRINT ''
PRINT '⚠️  IMPORTANTE:'
PRINT '   Las órdenes sin OC_USUARIO_EMAIL solo serán visibles para usuarios ADMIN'
PRINT '   Para asignar un usuario a estas órdenes, ejecutar:'
PRINT ''
PRINT '   UPDATE SIST_ORDEN_COMPRA'
PRINT '   SET OC_USUARIO_EMAIL = ''email@ejemplo.com'','
PRINT '       OC_USUARIO_NOMBRE = ''Nombre Completo'''
PRINT '   WHERE OC_ID = [ID_DE_LA_ORDEN]'
PRINT ''
PRINT '   O para asignar todas las órdenes a un usuario específico:'
PRINT ''
PRINT '   UPDATE SIST_ORDEN_COMPRA'
PRINT '   SET OC_USUARIO_EMAIL = ''email@ejemplo.com'','
PRINT '       OC_USUARIO_NOMBRE = ''Nombre Completo'''
PRINT '   WHERE OC_USUARIO_EMAIL IS NULL OR OC_USUARIO_EMAIL = '''''
GO

-- Ejemplo: Descomentar y modificar para asignar órdenes huérfanas a un usuario
/*
-- EJEMPLO: Asignar todas las órdenes sin usuario a un administrador
UPDATE SIST_ORDEN_COMPRA
SET OC_USUARIO_EMAIL = 'admin@faccar.com',
    OC_USUARIO_NOMBRE = 'Administrador Sistema'
WHERE OC_USUARIO_EMAIL IS NULL OR OC_USUARIO_EMAIL = ''

PRINT '✅ Órdenes actualizadas exitosamente'
*/

PRINT ''
PRINT '✅ Verificación completada'
GO
