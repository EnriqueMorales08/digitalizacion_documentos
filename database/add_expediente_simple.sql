-- =====================================================
-- SISTEMA DE NUMERO DE EXPEDIENTE - VERSION SIMPLE
-- Solo crea indices, el numero se genera en PHP
-- =====================================================

-- 1. Verificar que el campo OC_NUMERO_EXPEDIENTE existe
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_NUMERO_EXPEDIENTE')
BEGIN
    ALTER TABLE SIST_ORDEN_COMPRA ADD OC_NUMERO_EXPEDIENTE NVARCHAR(50);
    PRINT 'Campo OC_NUMERO_EXPEDIENTE agregado';
END
ELSE
BEGIN
    PRINT 'Campo OC_NUMERO_EXPEDIENTE ya existe';
END

-- 2. Crear indice unico para evitar duplicados
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'UQ_OC_NUMERO_EXPEDIENTE' AND object_id = OBJECT_ID('SIST_ORDEN_COMPRA'))
BEGIN
    CREATE UNIQUE INDEX UQ_OC_NUMERO_EXPEDIENTE ON SIST_ORDEN_COMPRA(OC_NUMERO_EXPEDIENTE) WHERE OC_NUMERO_EXPEDIENTE IS NOT NULL;
    PRINT 'Indice unico creado en OC_NUMERO_EXPEDIENTE';
END
ELSE
BEGIN
    PRINT 'Indice unico ya existe en OC_NUMERO_EXPEDIENTE';
END

-- 3. Crear indice para busquedas rapidas
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IDX_OC_NUMERO_EXPEDIENTE' AND object_id = OBJECT_ID('SIST_ORDEN_COMPRA'))
BEGIN
    CREATE INDEX IDX_OC_NUMERO_EXPEDIENTE ON SIST_ORDEN_COMPRA(OC_NUMERO_EXPEDIENTE);
    PRINT 'Indice de busqueda creado en OC_NUMERO_EXPEDIENTE';
END
ELSE
BEGIN
    PRINT 'Indice de busqueda ya existe en OC_NUMERO_EXPEDIENTE';
END

PRINT 'Sistema de numero de expediente configurado correctamente';
PRINT 'El numero se genera automaticamente en PHP al guardar la orden';
PRINT 'Formato: YYYYMM0001 (ejemplo: 2025100001)';
