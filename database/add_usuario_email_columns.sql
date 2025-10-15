-- Script para agregar columnas de email y nombre del usuario creador de la orden
-- Esto permite enviar notificaciones al asesor que creó la orden cuando sea aprobada/rechazada

USE [DOC_DIGITALES]
GO

-- Verificar si las columnas ya existen antes de agregarlas
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'[dbo].[SIST_ORDEN_COMPRA]') AND name = 'OC_USUARIO_EMAIL')
BEGIN
    ALTER TABLE [dbo].[SIST_ORDEN_COMPRA]
    ADD [OC_USUARIO_EMAIL] NVARCHAR(255) NULL;
    PRINT 'Columna OC_USUARIO_EMAIL agregada exitosamente';
END
ELSE
BEGIN
    PRINT 'La columna OC_USUARIO_EMAIL ya existe';
END
GO

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'[dbo].[SIST_ORDEN_COMPRA]') AND name = 'OC_USUARIO_NOMBRE')
BEGIN
    ALTER TABLE [dbo].[SIST_ORDEN_COMPRA]
    ADD [OC_USUARIO_NOMBRE] NVARCHAR(255) NULL;
    PRINT 'Columna OC_USUARIO_NOMBRE agregada exitosamente';
END
ELSE
BEGIN
    PRINT 'La columna OC_USUARIO_NOMBRE ya existe';
END
GO

PRINT 'Script completado exitosamente';
GO
