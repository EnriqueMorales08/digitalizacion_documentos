-- =====================================================
-- AMPLIAR COLUMNA ACC_FIRMA_CLIENTE
-- =====================================================
-- La columna ACC_FIRMA_CLIENTE es demasiado pequeña (200 caracteres)
-- para almacenar rutas completas de firmas.
-- Se amplía a 500 caracteres para coincidir con las otras tablas.

ALTER TABLE SIST_ACTA_CONOCIMIENTO_CONFORMIDAD
ALTER COLUMN ACC_FIRMA_CLIENTE NVARCHAR(500);

-- Mensaje de confirmación
PRINT '✅ Columna ACC_FIRMA_CLIENTE ampliada a NVARCHAR(500)';
