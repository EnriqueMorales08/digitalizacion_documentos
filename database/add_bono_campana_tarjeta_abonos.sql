-- =====================================================
-- MIGRACIÓN: Agregar campos de Bono de Campaña, 
-- Tarjeta a Nombre de y Sistema de Abonos Detallados
-- Fecha: 2025-10-16
-- =====================================================

USE FACCARPRUEBA;
GO

PRINT '🚀 Iniciando migración: Bono de Campaña, Tarjeta y Abonos Detallados...';

-- =====================================================
-- 1. AGREGAR CAMPO: BONO DE CAMPAÑA
-- =====================================================

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_BONO_CAMPANA')
BEGIN
    ALTER TABLE SIST_ORDEN_COMPRA ADD OC_BONO_CAMPANA DECIMAL(12,2);
    PRINT '✅ Campo OC_BONO_CAMPANA agregado.';
END
ELSE
BEGIN
    PRINT '⚠️ Campo OC_BONO_CAMPANA ya existe.';
END

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_MONEDA_BONO_CAMPANA')
BEGIN
    ALTER TABLE SIST_ORDEN_COMPRA ADD OC_MONEDA_BONO_CAMPANA NVARCHAR(5);
    PRINT '✅ Campo OC_MONEDA_BONO_CAMPANA agregado.';
END
ELSE
BEGIN
    PRINT '⚠️ Campo OC_MONEDA_BONO_CAMPANA ya existe.';
END

-- =====================================================
-- 2. AGREGAR CAMPO: TARJETA A NOMBRE DE
-- =====================================================

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_TARJETA_NOMBRE')
BEGIN
    ALTER TABLE SIST_ORDEN_COMPRA ADD OC_TARJETA_NOMBRE NVARCHAR(200);
    PRINT '✅ Campo OC_TARJETA_NOMBRE agregado.';
END
ELSE
BEGIN
    PRINT '⚠️ Campo OC_TARJETA_NOMBRE ya existe.';
END

-- =====================================================
-- 3. AGREGAR CAMPOS: SISTEMA DE ABONOS DETALLADOS
-- Cada abono tiene: Monto, Moneda, Nro Operación, 
-- Entidad Financiera y Archivo
-- =====================================================

PRINT '📋 Agregando campos de abonos detallados (1-10)...';

DECLARE @i INT = 1;
DECLARE @campo_monto NVARCHAR(50);
DECLARE @campo_moneda NVARCHAR(50);
DECLARE @campo_operacion NVARCHAR(50);
DECLARE @campo_entidad NVARCHAR(50);
DECLARE @campo_archivo NVARCHAR(50);
DECLARE @sql NVARCHAR(MAX);

WHILE @i <= 10
BEGIN
    SET @campo_monto = 'OC_ABONO_' + CAST(@i AS NVARCHAR) + '_MONTO';
    SET @campo_moneda = 'OC_ABONO_' + CAST(@i AS NVARCHAR) + '_MONEDA';
    SET @campo_operacion = 'OC_ABONO_' + CAST(@i AS NVARCHAR) + '_OPERACION';
    SET @campo_entidad = 'OC_ABONO_' + CAST(@i AS NVARCHAR) + '_ENTIDAD';
    SET @campo_archivo = 'OC_ABONO_' + CAST(@i AS NVARCHAR) + '_ARCHIVO';

    -- Agregar campo MONTO
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = @campo_monto)
    BEGIN
        SET @sql = 'ALTER TABLE SIST_ORDEN_COMPRA ADD ' + @campo_monto + ' DECIMAL(12,2)';
        EXEC sp_executesql @sql;
        PRINT '✅ Campo ' + @campo_monto + ' agregado.';
    END

    -- Agregar campo MONEDA
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = @campo_moneda)
    BEGIN
        SET @sql = 'ALTER TABLE SIST_ORDEN_COMPRA ADD ' + @campo_moneda + ' NVARCHAR(5)';
        EXEC sp_executesql @sql;
        PRINT '✅ Campo ' + @campo_moneda + ' agregado.';
    END

    -- Agregar campo OPERACION
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = @campo_operacion)
    BEGIN
        SET @sql = 'ALTER TABLE SIST_ORDEN_COMPRA ADD ' + @campo_operacion + ' NVARCHAR(50)';
        EXEC sp_executesql @sql;
        PRINT '✅ Campo ' + @campo_operacion + ' agregado.';
    END

    -- Agregar campo ENTIDAD
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = @campo_entidad)
    BEGIN
        SET @sql = 'ALTER TABLE SIST_ORDEN_COMPRA ADD ' + @campo_entidad + ' NVARCHAR(100)';
        EXEC sp_executesql @sql;
        PRINT '✅ Campo ' + @campo_entidad + ' agregado.';
    END

    -- Agregar campo ARCHIVO
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = @campo_archivo)
    BEGIN
        SET @sql = 'ALTER TABLE SIST_ORDEN_COMPRA ADD ' + @campo_archivo + ' NVARCHAR(500)';
        EXEC sp_executesql @sql;
        PRINT '✅ Campo ' + @campo_archivo + ' agregado.';
    END

    SET @i = @i + 1;
END

-- =====================================================
-- RESUMEN DE MIGRACIÓN
-- =====================================================

PRINT '';
PRINT '✅ MIGRACIÓN COMPLETADA EXITOSAMENTE';
PRINT '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━';
PRINT '📦 Campos agregados:';
PRINT '   • OC_BONO_CAMPANA (DECIMAL)';
PRINT '   • OC_MONEDA_BONO_CAMPANA (NVARCHAR)';
PRINT '   • OC_TARJETA_NOMBRE (NVARCHAR)';
PRINT '   • OC_ABONO_[1-10]_MONTO (DECIMAL)';
PRINT '   • OC_ABONO_[1-10]_MONEDA (NVARCHAR)';
PRINT '   • OC_ABONO_[1-10]_OPERACION (NVARCHAR)';
PRINT '   • OC_ABONO_[1-10]_ENTIDAD (NVARCHAR)';
PRINT '   • OC_ABONO_[1-10]_ARCHIVO (NVARCHAR)';
PRINT '';
PRINT '💡 Funcionalidades nuevas:';
PRINT '   ✓ Bono de Campaña con moneda seleccionable';
PRINT '   ✓ Tarjeta a Nombre de (auto-rellena con nombre comprador)';
PRINT '   ✓ Sistema de abonos detallados con:';
PRINT '     - Monto y moneda por abono';
PRINT '     - Número de operación específico';
PRINT '     - Entidad financiera asociada';
PRINT '     - Archivo voucher individual';
PRINT '';
PRINT '🎯 Ahora cada abono tiene su propia entidad financiera';
PRINT '   y número de operación, eliminando confusiones.';
PRINT '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━';

GO
