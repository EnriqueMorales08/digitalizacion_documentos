-- =====================================================
-- MIGRACIÓN: Agregar campos de Cuota Inicial
-- Fecha: 2025-11-28
-- Descripción: Agregar columnas OC_MONEDA_CUOTA_INICIAL y OC_CUOTA_INICIAL
--              a la tabla ORDENES_COMPRA
-- =====================================================

USE FACCARPRUEBA;
GO

-- Verificar si las columnas ya existen antes de agregarlas
IF NOT EXISTS (
    SELECT * FROM sys.columns 
    WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') 
    AND name = 'OC_MONEDA_CUOTA_INICIAL'
)
BEGIN
    PRINT '✅ Agregando columna OC_MONEDA_CUOTA_INICIAL...';
    
    ALTER TABLE SIST_ORDEN_COMPRA
    ADD OC_MONEDA_CUOTA_INICIAL NVARCHAR(5);
    
    PRINT '✅ Columna OC_MONEDA_CUOTA_INICIAL agregada exitosamente.';
END
ELSE
BEGIN
    PRINT '⚠️ La columna OC_MONEDA_CUOTA_INICIAL ya existe.';
END
GO

IF NOT EXISTS (
    SELECT * FROM sys.columns 
    WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') 
    AND name = 'OC_CUOTA_INICIAL'
)
BEGIN
    PRINT '✅ Agregando columna OC_CUOTA_INICIAL...';
    
    ALTER TABLE SIST_ORDEN_COMPRA
    ADD OC_CUOTA_INICIAL DECIMAL(12,2);
    
    PRINT '✅ Columna OC_CUOTA_INICIAL agregada exitosamente.';
END
ELSE
BEGIN
    PRINT '⚠️ La columna OC_CUOTA_INICIAL ya existe.';
END
GO

-- Verificar el resultado
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    CHARACTER_MAXIMUM_LENGTH,
    NUMERIC_PRECISION,
    NUMERIC_SCALE,
    IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'SIST_ORDEN_COMPRA'
AND COLUMN_NAME IN ('OC_MONEDA_CUOTA_INICIAL', 'OC_CUOTA_INICIAL')
ORDER BY COLUMN_NAME;
GO

PRINT '✅ Migración completada.';
GO
