-- =====================================================
-- MIGRACIÓN: Agregar campo CF_VEHICULO_MODELO
-- Fecha: 2025-11-26
-- Descripción: Campo faltante en SIST_CARTA_FELICITACIONES
-- =====================================================

-- Verificar si el campo ya existe antes de agregarlo
IF NOT EXISTS (
    SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'SIST_CARTA_FELICITACIONES' 
    AND COLUMN_NAME = 'CF_VEHICULO_MODELO'
)
BEGIN
    ALTER TABLE SIST_CARTA_FELICITACIONES
    ADD CF_VEHICULO_MODELO NVARCHAR(100);
    
    PRINT '✅ Campo CF_VEHICULO_MODELO agregado exitosamente';
END
ELSE
BEGIN
    PRINT '⚠️ El campo CF_VEHICULO_MODELO ya existe';
END
GO
