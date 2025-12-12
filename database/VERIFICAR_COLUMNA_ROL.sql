-- =====================================================
-- Script para verificar y configurar la columna ROL
-- Base de datos: DOC_DIGITALES
-- Tabla: firmas
-- Fecha: 30 de Octubre de 2025
-- =====================================================

USE [DOC_DIGITALES]
GO

PRINT '🔍 Verificando columna ROL en tabla firmas...'
GO

-- Verificar si la columna ROL existe
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'[dbo].[firmas]') AND name = 'rol')
BEGIN
    PRINT '❌ La columna ROL no existe. Creándola...'
    
    ALTER TABLE [dbo].[firmas]
    ADD [rol] NVARCHAR(20) NULL;
    
    PRINT '✅ Columna ROL creada exitosamente'
    
    -- Establecer valor por defecto USER para registros existentes
    UPDATE [dbo].[firmas]
    SET [rol] = 'USER'
    WHERE [rol] IS NULL;
    
    PRINT '✅ Registros existentes configurados con rol USER'
END
ELSE
BEGIN
    PRINT '✅ La columna ROL ya existe'
END
GO

-- Mostrar información de la columna
PRINT ''
PRINT '📋 Información de la columna ROL:'
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    CHARACTER_MAXIMUM_LENGTH,
    IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'firmas' AND COLUMN_NAME = 'rol'
GO

-- Mostrar distribución de roles
PRINT ''
PRINT '📊 Distribución actual de roles:'
SELECT 
    ISNULL(rol, 'NULL') as Rol,
    COUNT(*) as Cantidad
FROM [dbo].[firmas]
GROUP BY rol
ORDER BY COUNT(*) DESC
GO

-- Mostrar usuarios y sus roles
PRINT ''
PRINT '👥 Lista de usuarios y roles:'
SELECT 
    usuario,
    firma_nombre,
    firma_apellido,
    firma_mail,
    ISNULL(rol, 'SIN ROL') as rol
FROM [dbo].[firmas]
ORDER BY rol, usuario
GO

PRINT ''
PRINT '✅ Verificación completada'
PRINT ''
PRINT '📝 NOTAS IMPORTANTES:'
PRINT '   - Roles válidos: ADMIN, USER'
PRINT '   - ADMIN: Acceso completo a todas las órdenes'
PRINT '   - USER: Solo ve sus propias órdenes de compra'
PRINT '   - Para cambiar el rol de un usuario, ejecutar:'
PRINT '     UPDATE firmas SET rol = ''ADMIN'' WHERE usuario = ''nombre_usuario'''
GO
