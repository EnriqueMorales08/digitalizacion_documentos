-- =====================================================
-- MIGRACIÓN: Renombrar columna OC_CLIENTE_HUELLA a OC_CONFIRMACION_SANTANDER
-- Fecha: 2025-11-28
-- Descripción: Cambiar el nombre de la columna que almacena
--              el archivo de huella digital del cliente por
--              Confirmación Santander
-- =====================================================

USE FACCARPRUEBA;
GO

-- Verificar si la columna existe antes de renombrar
IF EXISTS (
    SELECT * FROM sys.columns 
    WHERE object_id = OBJECT_ID('ORDENES_COMPRA') 
    AND name = 'OC_CLIENTE_HUELLA'
)
BEGIN
    PRINT '✅ Renombrando columna OC_CLIENTE_HUELLA a OC_CONFIRMACION_SANTANDER...';
    
    EXEC sp_rename 
        'ORDENES_COMPRA.OC_CLIENTE_HUELLA', 
        'OC_CONFIRMACION_SANTANDER', 
        'COLUMN';
    
    PRINT '✅ Columna renombrada exitosamente.';
END
ELSE
BEGIN
    PRINT '⚠️ La columna OC_CLIENTE_HUELLA no existe. Verificando si ya fue renombrada...';
    
    IF EXISTS (
        SELECT * FROM sys.columns 
        WHERE object_id = OBJECT_ID('ORDENES_COMPRA') 
        AND name = 'OC_CONFIRMACION_SANTANDER'
    )
    BEGIN
        PRINT '✅ La columna OC_CONFIRMACION_SANTANDER ya existe. No se requiere acción.';
    END
    ELSE
    BEGIN
        PRINT '❌ ERROR: No se encontró ninguna de las dos columnas.';
    END
END
GO

-- Verificar el resultado
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    CHARACTER_MAXIMUM_LENGTH,
    IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'ORDENES_COMPRA'
AND COLUMN_NAME IN ('OC_CLIENTE_HUELLA', 'OC_CONFIRMACION_SANTANDER');
GO

PRINT '✅ Migración completada.';
GO
