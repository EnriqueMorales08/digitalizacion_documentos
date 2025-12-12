-- =====================================================
-- MIGRACIÓN: Agregar columna OC_COMENTARIO_VALIDACION
-- Fecha: 2025-11-28
-- Descripción: Agregar columna para guardar comentario cuando
--              no se cumplen validaciones de pago
-- =====================================================

USE FACCARPRUEBA;
GO

-- Verificar si la columna ya existe
IF NOT EXISTS (
    SELECT * FROM sys.columns 
    WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') 
    AND name = 'OC_COMENTARIO_VALIDACION'
)
BEGIN
    PRINT '✅ Agregando columna OC_COMENTARIO_VALIDACION...';
    
    ALTER TABLE SIST_ORDEN_COMPRA
    ADD OC_COMENTARIO_VALIDACION NVARCHAR(MAX);
    
    PRINT '✅ Columna OC_COMENTARIO_VALIDACION agregada exitosamente.';
END
ELSE
BEGIN
    PRINT '⚠️ La columna OC_COMENTARIO_VALIDACION ya existe.';
END
GO

-- Verificar el resultado
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'SIST_ORDEN_COMPRA'
AND COLUMN_NAME = 'OC_COMENTARIO_VALIDACION';
GO

PRINT '✅ Migración completada.';
GO
