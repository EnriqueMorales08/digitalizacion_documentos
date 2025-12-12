-- ═══════════════════════════════════════════════════════════════════════════
-- RENOMBRAR: OC_PROPIETARIO_DNI → OC_PROPIETARIO_RUC
-- Base de datos: FACCARPRUEBA
-- ═══════════════════════════════════════════════════════════════════════════

USE FACCARPRUEBA;

PRINT '🔄 Renombrando columna OC_PROPIETARIO_DNI a OC_PROPIETARIO_RUC...';
PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- VERIFICAR ESTADO ACTUAL
-- ═══════════════════════════════════════════════════════════════════════════

DECLARE @tiene_dni BIT = 0;
DECLARE @tiene_ruc BIT = 0;

IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_PROPIETARIO_DNI')
    SET @tiene_dni = 1;

IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_PROPIETARIO_RUC')
    SET @tiene_ruc = 1;

PRINT '📊 Estado actual:';
PRINT '   OC_PROPIETARIO_DNI: ' + CASE WHEN @tiene_dni = 1 THEN '✅ Existe' ELSE '❌ No existe' END;
PRINT '   OC_PROPIETARIO_RUC: ' + CASE WHEN @tiene_ruc = 1 THEN '✅ Existe' ELSE '❌ No existe' END;
PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- RENOMBRAR COLUMNA
-- ═══════════════════════════════════════════════════════════════════════════

IF @tiene_dni = 1 AND @tiene_ruc = 0
BEGIN
    PRINT '🔄 Renombrando OC_PROPIETARIO_DNI → OC_PROPIETARIO_RUC...';
    EXEC sp_rename 'SIST_ORDEN_COMPRA.OC_PROPIETARIO_DNI', 'OC_PROPIETARIO_RUC', 'COLUMN';
    PRINT '✅ Columna renombrada exitosamente';
    PRINT '';
END
ELSE IF @tiene_ruc = 1
BEGIN
    PRINT '✅ OC_PROPIETARIO_RUC ya existe (correcto)';
    PRINT '   No es necesario renombrar.';
    PRINT '';
END
ELSE
BEGIN
    PRINT '❌ ERROR: No se encontró la columna OC_PROPIETARIO_DNI';
    PRINT '   Verifica el nombre de la columna en la base de datos.';
    PRINT '';
END

-- ═══════════════════════════════════════════════════════════════════════════
-- VERIFICACIÓN FINAL
-- ═══════════════════════════════════════════════════════════════════════════

PRINT '╔═══════════════════════════════════════════════════════════════════════════╗';
PRINT '║  ✅ PROCESO COMPLETADO                                                   ║';
PRINT '╚═══════════════════════════════════════════════════════════════════════════╝';
PRINT '';

IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_PROPIETARIO_RUC')
BEGIN
    PRINT '🎯 Verificación final:';
    PRINT '   ✅ OC_PROPIETARIO_RUC existe en la BD';
    PRINT '';
    PRINT '📋 SINCRONIZACIÓN FORMULARIO ↔ BASE DE DATOS:';
    PRINT '   Formulario:  name="OC_PROPIETARIO_RUC"  →  BD: OC_PROPIETARIO_RUC ✅';
    PRINT '';
    PRINT '🎉 ¡Todo sincronizado! Ahora se guardará correctamente.';
END
ELSE
BEGIN
    PRINT '❌ ERROR: La columna OC_PROPIETARIO_RUC NO existe después del proceso.';
    PRINT '   Contacta al administrador del sistema.';
END

PRINT '';
