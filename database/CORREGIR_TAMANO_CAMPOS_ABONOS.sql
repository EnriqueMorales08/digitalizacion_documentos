-- ═══════════════════════════════════════════════════════════════════════════
-- CORREGIR TAMAÑO DE CAMPOS DE ARCHIVOS DE ABONOS
-- Cambiar de NVARCHAR(50) a NVARCHAR(500)
-- Base de datos: FACCARPRUEBA
-- ═══════════════════════════════════════════════════════════════════════════

USE FACCARPRUEBA;

PRINT '🔧 Corrigiendo tamaño de campos de archivos de abonos...';
PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- CAMBIAR TAMAÑO DE CAMPOS OC_ARCHIVO_ABONO1 hasta OC_ARCHIVO_ABONO7
-- De NVARCHAR(50) a NVARCHAR(500) para soportar rutas largas
-- ═══════════════════════════════════════════════════════════════════════════

-- ABONO 1
ALTER TABLE SIST_ORDEN_COMPRA ALTER COLUMN OC_ARCHIVO_ABONO1 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO1 cambiado a NVARCHAR(500)';

-- ABONO 2
ALTER TABLE SIST_ORDEN_COMPRA ALTER COLUMN OC_ARCHIVO_ABONO2 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO2 cambiado a NVARCHAR(500)';

-- ABONO 3
ALTER TABLE SIST_ORDEN_COMPRA ALTER COLUMN OC_ARCHIVO_ABONO3 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO3 cambiado a NVARCHAR(500)';

-- ABONO 4
ALTER TABLE SIST_ORDEN_COMPRA ALTER COLUMN OC_ARCHIVO_ABONO4 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO4 cambiado a NVARCHAR(500)';

-- ABONO 5
ALTER TABLE SIST_ORDEN_COMPRA ALTER COLUMN OC_ARCHIVO_ABONO5 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO5 cambiado a NVARCHAR(500)';

-- ABONO 6
ALTER TABLE SIST_ORDEN_COMPRA ALTER COLUMN OC_ARCHIVO_ABONO6 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO6 cambiado a NVARCHAR(500)';

-- ABONO 7
ALTER TABLE SIST_ORDEN_COMPRA ALTER COLUMN OC_ARCHIVO_ABONO7 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO7 cambiado a NVARCHAR(500)';

PRINT '';
PRINT '╔═══════════════════════════════════════════════════════════════════════════╗';
PRINT '║  ✅ CORRECCIÓN COMPLETADA                                                ║';
PRINT '╚═══════════════════════════════════════════════════════════════════════════╝';
PRINT '';
PRINT '📊 Todos los campos de archivos de abonos ahora son NVARCHAR(500)';
PRINT '   Esto permite guardar rutas de archivos largas sin problemas.';
PRINT '';
PRINT '🎯 Ahora puedes guardar la orden de compra sin errores.';
PRINT '';
