-- Script para actualizar el campo OC_CLIENTE_HUELLA
-- Este campo almacenará la URL del archivo de la huella digital (igual que los otros archivos)

USE FACCARPRUEBA;
GO

-- Verificar si la columna existe y modificarla
IF EXISTS (SELECT * FROM sys.columns 
           WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') 
           AND name = 'OC_CLIENTE_HUELLA')
BEGIN
    -- Cambiar a VARCHAR(500) para almacenar URLs de archivos (igual que OC_ARCHIVO_DNI, etc.)
    ALTER TABLE SIST_ORDEN_COMPRA
    ALTER COLUMN OC_CLIENTE_HUELLA VARCHAR(500);
    
    PRINT '✅ Campo OC_CLIENTE_HUELLA actualizado correctamente a VARCHAR(500)';
END
ELSE
BEGIN
    PRINT '❌ El campo OC_CLIENTE_HUELLA no existe en la tabla SIST_ORDEN_COMPRA';
END
GO
