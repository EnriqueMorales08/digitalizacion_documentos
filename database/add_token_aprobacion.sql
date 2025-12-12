-- Script para agregar campo de token de aprobación a la tabla SIST_ORDEN_COMPRA
-- Este token permite acceder al panel de aprobación sin necesidad de login

USE [INTERAMERICANA_DOCUMENTOS];
GO

-- Verificar si la columna ya existe antes de agregarla
IF NOT EXISTS (SELECT * FROM sys.columns 
               WHERE object_id = OBJECT_ID(N'[dbo].[SIST_ORDEN_COMPRA]') 
               AND name = 'OC_TOKEN_APROBACION')
BEGIN
    ALTER TABLE [dbo].[SIST_ORDEN_COMPRA]
    ADD OC_TOKEN_APROBACION NVARCHAR(64) NULL;
    
    PRINT 'Columna OC_TOKEN_APROBACION agregada exitosamente';
END
ELSE
BEGIN
    PRINT 'La columna OC_TOKEN_APROBACION ya existe';
END
GO

-- Crear índice para mejorar búsquedas por token
IF NOT EXISTS (SELECT * FROM sys.indexes 
               WHERE name = 'IX_SIST_ORDEN_COMPRA_TOKEN' 
               AND object_id = OBJECT_ID(N'[dbo].[SIST_ORDEN_COMPRA]'))
BEGIN
    CREATE INDEX IX_SIST_ORDEN_COMPRA_TOKEN 
    ON [dbo].[SIST_ORDEN_COMPRA](OC_TOKEN_APROBACION);
    
    PRINT 'Índice IX_SIST_ORDEN_COMPRA_TOKEN creado exitosamente';
END
ELSE
BEGIN
    PRINT 'El índice IX_SIST_ORDEN_COMPRA_TOKEN ya existe';
END
GO
