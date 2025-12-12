-- =====================================================
-- Script para corregir fechas existentes en SIST_CARTA_RECEPCION
-- Separa las fechas completas guardadas en CR_FECHA_ANIO
-- en los campos CR_FECHA_DIA, CR_FECHA_MES y CR_FECHA_ANIO
-- =====================================================

USE FACCARPRUEBA;
GO

PRINT '🔧 Corrigiendo fechas existentes en SIST_CARTA_RECEPCION...';
PRINT '';

-- Actualizar registros donde CR_FECHA_ANIO contiene una fecha completa (formato YYYY-MM-DD)
-- y CR_FECHA_DIA y CR_FECHA_MES están vacíos
UPDATE SIST_CARTA_RECEPCION
SET 
    CR_FECHA_DIA = DAY(CAST(CR_FECHA_ANIO AS DATE)),
    CR_FECHA_MES = MONTH(CAST(CR_FECHA_ANIO AS DATE)),
    CR_FECHA_ANIO = YEAR(CAST(CR_FECHA_ANIO AS DATE))
WHERE 
    -- Solo actualizar si CR_FECHA_ANIO tiene formato de fecha (contiene guiones)
    CR_FECHA_ANIO LIKE '%-%-%'
    -- Y los otros campos están vacíos o son NULL
    AND (CR_FECHA_DIA IS NULL OR CR_FECHA_DIA = '')
    AND (CR_FECHA_MES IS NULL OR CR_FECHA_MES = '')
    -- Verificar que se puede convertir a fecha
    AND ISDATE(CR_FECHA_ANIO) = 1;

PRINT '✅ Fechas corregidas exitosamente';
PRINT '';

-- Mostrar resumen de registros corregidos
SELECT 
    CR_ID,
    CR_FECHA_DIA as 'Día',
    CR_FECHA_MES as 'Mes',
    CR_FECHA_ANIO as 'Año',
    CR_FECHA_CREACION as 'Fecha Creación'
FROM SIST_CARTA_RECEPCION
ORDER BY CR_FECHA_CREACION DESC;

PRINT '';
PRINT '📊 Resumen mostrado arriba';
PRINT '✅ Script completado';
