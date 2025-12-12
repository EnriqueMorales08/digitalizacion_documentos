-- =====================================================
-- SCRIPT DE ACTUALIZACIÓN: Agregar campo OC_COMPRADOR_APELLIDO
-- Fecha: 2025-10-30
-- Descripción: Agrega el campo OC_COMPRADOR_APELLIDO a la tabla SIST_ORDEN_COMPRA
-- =====================================================

USE FACCARPRUEBA;
GO

PRINT '🚀 Iniciando actualización de tabla SIST_ORDEN_COMPRA...'
GO

-- Verificar si la columna ya existe
IF NOT EXISTS (
    SELECT * FROM sys.columns 
    WHERE object_id = OBJECT_ID(N'[dbo].[SIST_ORDEN_COMPRA]') 
    AND name = 'OC_COMPRADOR_APELLIDO'
)
BEGIN
    PRINT '📝 Agregando columna OC_COMPRADOR_APELLIDO...'
    
    -- Agregar la columna después de OC_COMPRADOR_NOMBRE
    ALTER TABLE SIST_ORDEN_COMPRA
    ADD OC_COMPRADOR_APELLIDO NVARCHAR(200) NULL;
    
    PRINT '✅ Columna OC_COMPRADOR_APELLIDO agregada exitosamente'
END
ELSE
BEGIN
    PRINT '⚠️ La columna OC_COMPRADOR_APELLIDO ya existe. No se realizaron cambios.'
END
GO

PRINT '🎉 Actualización completada exitosamente!'
GO
