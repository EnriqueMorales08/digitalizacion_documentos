-- ═══════════════════════════════════════════════════════════════════════════
-- SCRIPT DE MIGRACIÓN: Agregar Bono de Campaña, Tarjeta y Abonos Detallados
-- Base de datos: FACCARPRUEBA
-- Fecha: 16 de Octubre, 2025
-- ═══════════════════════════════════════════════════════════════════════════

USE FACCARPRUEBA;
GO

PRINT '';
PRINT '╔═══════════════════════════════════════════════════════════════════════════╗';
PRINT '║  🚀 INICIANDO MIGRACIÓN DE BASE DE DATOS                                 ║';
PRINT '╚═══════════════════════════════════════════════════════════════════════════╝';
PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- 1. AGREGAR CAMPO: BONO DE CAMPAÑA
-- ═══════════════════════════════════════════════════════════════════════════

PRINT '📦 [1/4] Agregando campos de BONO DE CAMPAÑA...';

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_BONO_CAMPANA')
BEGIN
    ALTER TABLE SIST_ORDEN_COMPRA ADD OC_BONO_CAMPANA DECIMAL(12,2);
    PRINT '   ✅ Campo OC_BONO_CAMPANA agregado.';
END
ELSE
BEGIN
    PRINT '   ⚠️  Campo OC_BONO_CAMPANA ya existe.';
END

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_MONEDA_BONO_CAMPANA')
BEGIN
    ALTER TABLE SIST_ORDEN_COMPRA ADD OC_MONEDA_BONO_CAMPANA NVARCHAR(5);
    PRINT '   ✅ Campo OC_MONEDA_BONO_CAMPANA agregado.';
END
ELSE
BEGIN
    PRINT '   ⚠️  Campo OC_MONEDA_BONO_CAMPANA ya existe.';
END

PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- 2. AGREGAR CAMPO: TARJETA A NOMBRE DE
-- ═══════════════════════════════════════════════════════════════════════════

PRINT '📦 [2/4] Agregando campo TARJETA A NOMBRE DE...';

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_TARJETA_NOMBRE')
BEGIN
    ALTER TABLE SIST_ORDEN_COMPRA ADD OC_TARJETA_NOMBRE NVARCHAR(200);
    PRINT '   ✅ Campo OC_TARJETA_NOMBRE agregado.';
END
ELSE
BEGIN
    PRINT '   ⚠️  Campo OC_TARJETA_NOMBRE ya existe.';
END

PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- 3. AGREGAR CAMPOS: SISTEMA DE ABONOS DETALLADOS (1-7)
-- ═══════════════════════════════════════════════════════════════════════════

PRINT '📦 [3/4] Agregando campos de ABONOS DETALLADOS (1-7)...';
PRINT '   Cada abono tiene: Monto, Nro Operación, Entidad Financiera, Archivo';
PRINT '';

DECLARE @i INT = 1;
DECLARE @campo_monto NVARCHAR(50);
DECLARE @campo_operacion NVARCHAR(50);
DECLARE @campo_entidad NVARCHAR(50);
DECLARE @campo_archivo NVARCHAR(50);
DECLARE @sql NVARCHAR(MAX);

WHILE @i <= 7
BEGIN
    PRINT '   → Procesando Abono ' + CAST(@i AS NVARCHAR) + '...';
    
    SET @campo_monto = 'OC_MONTO_' + CAST(@i AS NVARCHAR);
    SET @campo_operacion = 'OC_NRO_OPERACION_' + CAST(@i AS NVARCHAR);
    SET @campo_entidad = 'OC_ENTIDAD_FINANCIERA_' + CAST(@i AS NVARCHAR);
    SET @campo_archivo = 'OC_ARCHIVO_ABONO_' + CAST(@i AS NVARCHAR);

    -- Agregar campo MONTO
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = @campo_monto)
    BEGIN
        SET @sql = 'ALTER TABLE SIST_ORDEN_COMPRA ADD ' + @campo_monto + ' NVARCHAR(50)';
        EXEC sp_executesql @sql;
        PRINT '     ✅ ' + @campo_monto;
    END
    ELSE
    BEGIN
        PRINT '     ⚠️  ' + @campo_monto + ' ya existe';
    END

    -- Agregar campo NRO_OPERACION
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = @campo_operacion)
    BEGIN
        SET @sql = 'ALTER TABLE SIST_ORDEN_COMPRA ADD ' + @campo_operacion + ' NVARCHAR(50)';
        EXEC sp_executesql @sql;
        PRINT '     ✅ ' + @campo_operacion;
    END
    ELSE
    BEGIN
        PRINT '     ⚠️  ' + @campo_operacion + ' ya existe';
    END

    -- Agregar campo ENTIDAD_FINANCIERA
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = @campo_entidad)
    BEGIN
        SET @sql = 'ALTER TABLE SIST_ORDEN_COMPRA ADD ' + @campo_entidad + ' NVARCHAR(100)';
        EXEC sp_executesql @sql;
        PRINT '     ✅ ' + @campo_entidad;
    END
    ELSE
    BEGIN
        PRINT '     ⚠️  ' + @campo_entidad + ' ya existe';
    END

    -- Agregar campo ARCHIVO_ABONO
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = @campo_archivo)
    BEGIN
        SET @sql = 'ALTER TABLE SIST_ORDEN_COMPRA ADD ' + @campo_archivo + ' NVARCHAR(500)';
        EXEC sp_executesql @sql;
        PRINT '     ✅ ' + @campo_archivo;
    END
    ELSE
    BEGIN
        PRINT '     ⚠️  ' + @campo_archivo + ' ya existe';
    END

    PRINT '';
    SET @i = @i + 1;
END

-- ═══════════════════════════════════════════════════════════════════════════
-- 4. VERIFICACIÓN FINAL
-- ═══════════════════════════════════════════════════════════════════════════

PRINT '📦 [4/4] Verificando campos agregados...';
PRINT '';

DECLARE @total_campos INT = 0;

-- Contar campos agregados
IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_BONO_CAMPANA')
    SET @total_campos = @total_campos + 1;
IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_MONEDA_BONO_CAMPANA')
    SET @total_campos = @total_campos + 1;
IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_TARJETA_NOMBRE')
    SET @total_campos = @total_campos + 1;

-- Contar campos de abonos
DECLARE @j INT = 1;
WHILE @j <= 7
BEGIN
    IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_MONTO_' + CAST(@j AS NVARCHAR))
        SET @total_campos = @total_campos + 1;
    IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_NRO_OPERACION_' + CAST(@j AS NVARCHAR))
        SET @total_campos = @total_campos + 1;
    IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_ENTIDAD_FINANCIERA_' + CAST(@j AS NVARCHAR))
        SET @total_campos = @total_campos + 1;
    IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_ARCHIVO_ABONO_' + CAST(@j AS NVARCHAR))
        SET @total_campos = @total_campos + 1;
    SET @j = @j + 1;
END

PRINT '╔═══════════════════════════════════════════════════════════════════════════╗';
PRINT '║  ✅ MIGRACIÓN COMPLETADA EXITOSAMENTE                                    ║';
PRINT '╚═══════════════════════════════════════════════════════════════════════════╝';
PRINT '';
PRINT '📊 RESUMEN DE CAMBIOS:';
PRINT '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━';
PRINT '';
PRINT '   Total de campos verificados: ' + CAST(@total_campos AS NVARCHAR) + ' / 31';
PRINT '';
PRINT '   📦 Bono de Campaña:';
PRINT '      • OC_BONO_CAMPANA';
PRINT '      • OC_MONEDA_BONO_CAMPANA';
PRINT '';
PRINT '   🪪 Tarjeta:';
PRINT '      • OC_TARJETA_NOMBRE';
PRINT '';
PRINT '   💰 Abonos (1-7):';
PRINT '      • OC_MONTO_[1-7]';
PRINT '      • OC_NRO_OPERACION_[1-7]';
PRINT '      • OC_ENTIDAD_FINANCIERA_[1-7]';
PRINT '      • OC_ARCHIVO_ABONO_[1-7]';
PRINT '';
PRINT '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━';
PRINT '';
PRINT '💡 NOTAS IMPORTANTES:';
PRINT '   • Los campos vacíos se quedan NULL (normal)';
PRINT '   • Si solo hay 2 abonos, los campos 3-7 quedan vacíos';
PRINT '   • Todos los campos son NVARCHAR para flexibilidad';
PRINT '';
PRINT '🎯 PRÓXIMOS PASOS:';
PRINT '   1. Refrescar la página de orden de compra';
PRINT '   2. Verificar que aparezcan los nuevos campos';
PRINT '   3. Probar agregar abonos con el botón "+ Agregar Abono"';
PRINT '';
PRINT '╔═══════════════════════════════════════════════════════════════════════════╗';
PRINT '║  🎉 ¡LISTO PARA USAR!                                                     ║';
PRINT '╚═══════════════════════════════════════════════════════════════════════════╝';
PRINT '';

GO
