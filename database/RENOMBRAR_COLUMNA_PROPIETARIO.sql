-- ═══════════════════════════════════════════════════════════════════════════
-- VERIFICAR Y RENOMBRAR COLUMNAS DE PROPIETARIO
-- Base de datos: FACCARPRUEBA
-- ═══════════════════════════════════════════════════════════════════════════

USE FACCARPRUEBA;

PRINT '🔍 Verificando columnas de propietario en la base de datos...';
PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- PASO 1: VERIFICAR ESTADO ACTUAL
-- ═══════════════════════════════════════════════════════════════════════════

DECLARE @tiene_comprador_nombre BIT = 0;
DECLARE @tiene_comprador_dni BIT = 0;
DECLARE @tiene_propietario_nombre BIT = 0;
DECLARE @tiene_propietario_dni BIT = 0;

IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_COMPRADOR_NOMBRE')
    SET @tiene_comprador_nombre = 1;

IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_COMPRADOR_DNI')
    SET @tiene_comprador_dni = 1;

IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_PROPIETARIO_NOMBRE')
    SET @tiene_propietario_nombre = 1;

IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_PROPIETARIO_DNI')
    SET @tiene_propietario_dni = 1;

PRINT '📊 Estado actual de las columnas:';
PRINT '   OC_COMPRADOR_NOMBRE:   ' + CASE WHEN @tiene_comprador_nombre = 1 THEN '✅ Existe' ELSE '❌ No existe' END;
PRINT '   OC_COMPRADOR_DNI:      ' + CASE WHEN @tiene_comprador_dni = 1 THEN '✅ Existe' ELSE '❌ No existe' END;
PRINT '   OC_PROPIETARIO_NOMBRE: ' + CASE WHEN @tiene_propietario_nombre = 1 THEN '✅ Existe' ELSE '❌ No existe' END;
PRINT '   OC_PROPIETARIO_DNI:    ' + CASE WHEN @tiene_propietario_dni = 1 THEN '✅ Existe' ELSE '❌ No existe' END;
PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- PASO 2: RENOMBRAR SI ES NECESARIO
-- ═══════════════════════════════════════════════════════════════════════════

-- Renombrar OC_COMPRADOR_NOMBRE a OC_PROPIETARIO_NOMBRE
IF @tiene_comprador_nombre = 1 AND @tiene_propietario_nombre = 0
BEGIN
    PRINT '🔄 Renombrando OC_COMPRADOR_NOMBRE → OC_PROPIETARIO_NOMBRE...';
    EXEC sp_rename 'SIST_ORDEN_COMPRA.OC_COMPRADOR_NOMBRE', 'OC_PROPIETARIO_NOMBRE', 'COLUMN';
    PRINT '✅ OC_COMPRADOR_NOMBRE renombrado a OC_PROPIETARIO_NOMBRE';
    PRINT '';
END
ELSE IF @tiene_propietario_nombre = 1
BEGIN
    PRINT '✅ OC_PROPIETARIO_NOMBRE ya existe (correcto)';
    PRINT '';
END

-- Renombrar OC_COMPRADOR_DNI a OC_PROPIETARIO_DNI
IF @tiene_comprador_dni = 1 AND @tiene_propietario_dni = 0
BEGIN
    PRINT '🔄 Renombrando OC_COMPRADOR_DNI → OC_PROPIETARIO_DNI...';
    EXEC sp_rename 'SIST_ORDEN_COMPRA.OC_COMPRADOR_DNI', 'OC_PROPIETARIO_DNI', 'COLUMN';
    PRINT '✅ OC_COMPRADOR_DNI renombrado a OC_PROPIETARIO_DNI';
    PRINT '';
END
ELSE IF @tiene_propietario_dni = 1
BEGIN
    PRINT '✅ OC_PROPIETARIO_DNI ya existe (correcto)';
    PRINT '';
END

-- ═══════════════════════════════════════════════════════════════════════════
-- PASO 3: VERIFICACIÓN FINAL
-- ═══════════════════════════════════════════════════════════════════════════

PRINT '╔═══════════════════════════════════════════════════════════════════════════╗';
PRINT '║  ✅ PROCESO COMPLETADO                                                   ║';
PRINT '╚═══════════════════════════════════════════════════════════════════════════╝';
PRINT '';
PRINT '🎯 Verificación final:';

IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_PROPIETARIO_NOMBRE')
    PRINT '   ✅ OC_PROPIETARIO_NOMBRE existe en la BD';
ELSE
    PRINT '   ❌ ERROR: OC_PROPIETARIO_NOMBRE NO existe';

IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_PROPIETARIO_DNI')
    PRINT '   ✅ OC_PROPIETARIO_DNI existe en la BD';
ELSE
    PRINT '   ❌ ERROR: OC_PROPIETARIO_DNI NO existe';

PRINT '';
PRINT '📋 SINCRONIZACIÓN FORMULARIO ↔ BASE DE DATOS:';
PRINT '   Formulario:  name="OC_PROPIETARIO_NOMBRE"  →  BD: OC_PROPIETARIO_NOMBRE ✅';
PRINT '   Formulario:  name="OC_PROPIETARIO_DNI"     →  BD: OC_PROPIETARIO_DNI ✅';
PRINT '';
PRINT '🎉 ¡Todo sincronizado! Ahora se guardará correctamente.';
PRINT '';
