-- =====================================================
-- LIMPIAR REGISTROS INCORRECTOS DE AUDITORÍA
-- Elimina registros donde el valor anterior y nuevo son iguales
-- =====================================================

USE FACCARPRUEBA;
GO

PRINT '🧹 Limpiando registros incorrectos de SIST_AUDIT_LOG...';
PRINT '';

-- Ver cuántos registros hay antes
DECLARE @TotalAntes INT;
SELECT @TotalAntes = COUNT(*) FROM SIST_AUDIT_LOG;
PRINT '📊 Total de registros antes: ' + CAST(@TotalAntes AS NVARCHAR(10));
PRINT '';

-- Ver cuántos registros tienen valores iguales (falsos positivos)
DECLARE @FalsosPositivos INT;
SELECT @FalsosPositivos = COUNT(*) 
FROM SIST_AUDIT_LOG
WHERE AUDIT_OLD_VALUE = AUDIT_NEW_VALUE
   OR (
       -- Detectar fechas iguales con diferente formato
       -- Ej: "2025-11-04 00:00:00" vs "2025-11-04"
       AUDIT_FIELD_NAME LIKE '%FECHA%' 
       AND CONVERT(DATE, AUDIT_OLD_VALUE) = CONVERT(DATE, AUDIT_NEW_VALUE)
   );

PRINT '⚠️  Registros con valores iguales (falsos positivos): ' + CAST(@FalsosPositivos AS NVARCHAR(10));
PRINT '';

-- Mostrar algunos ejemplos antes de eliminar
IF @FalsosPositivos > 0
BEGIN
    PRINT '📋 Ejemplos de registros que se eliminarán:';
    PRINT '';
    
    SELECT TOP 10
        AUDIT_ID AS ID,
        AUDIT_FIELD_NAME AS Campo,
        AUDIT_OLD_VALUE AS [Valor Anterior],
        AUDIT_NEW_VALUE AS [Valor Nuevo],
        AUDIT_TIMESTAMP AS Fecha,
        CASE 
            WHEN AUDIT_OLD_VALUE = AUDIT_NEW_VALUE THEN 'Valores idénticos'
            WHEN AUDIT_FIELD_NAME LIKE '%FECHA%' THEN 'Fechas iguales (diferente formato)'
            ELSE 'Otro'
        END AS Motivo
    FROM SIST_AUDIT_LOG
    WHERE AUDIT_OLD_VALUE = AUDIT_NEW_VALUE
       OR (
           AUDIT_FIELD_NAME LIKE '%FECHA%' 
           AND CONVERT(DATE, AUDIT_OLD_VALUE) = CONVERT(DATE, AUDIT_NEW_VALUE)
       )
    ORDER BY AUDIT_TIMESTAMP DESC;
    
    PRINT '';
    PRINT '🗑️  Eliminando registros con valores iguales...';
    
    -- Eliminar registros donde el valor anterior y nuevo son iguales
    DELETE FROM SIST_AUDIT_LOG
    WHERE AUDIT_OLD_VALUE = AUDIT_NEW_VALUE
       OR (
           -- Eliminar fechas iguales con diferente formato
           AUDIT_FIELD_NAME LIKE '%FECHA%' 
           AND CONVERT(DATE, AUDIT_OLD_VALUE) = CONVERT(DATE, AUDIT_NEW_VALUE)
       );
    
    PRINT '✅ Registros eliminados: ' + CAST(@FalsosPositivos AS NVARCHAR(10));
END
ELSE
BEGIN
    PRINT '✅ No hay registros con valores iguales para eliminar.';
END

PRINT '';

-- Ver cuántos registros quedan después
DECLARE @TotalDespues INT;
SELECT @TotalDespues = COUNT(*) FROM SIST_AUDIT_LOG;
PRINT '📊 Total de registros después: ' + CAST(@TotalDespues AS NVARCHAR(10));
PRINT '';

-- Mostrar registros restantes
IF @TotalDespues > 0
BEGIN
    PRINT '📋 Registros válidos restantes:';
    PRINT '';
    
    SELECT 
        AUDIT_ID AS ID,
        AUDIT_TIMESTAMP AS [Fecha/Hora],
        AUDIT_USER_NAME AS Usuario,
        AUDIT_NUMERO_EXPEDIENTE AS Expediente,
        AUDIT_FIELD_NAME AS Campo,
        AUDIT_OLD_VALUE AS [Valor Anterior],
        AUDIT_NEW_VALUE AS [Valor Nuevo]
    FROM SIST_AUDIT_LOG
    ORDER BY AUDIT_TIMESTAMP DESC;
END
ELSE
BEGIN
    PRINT '📭 No quedan registros en la tabla de auditoría.';
END

PRINT '';
PRINT '✅ Limpieza completada.';
PRINT '';

GO
