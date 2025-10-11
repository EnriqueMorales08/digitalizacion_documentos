-- =====================================================
-- SISTEMA DE NÚMERO DE EXPEDIENTE AUTO-GENERADO
-- Formato: YYYYMM0001 (se reinicia cada mes)
-- =====================================================

PRINT '🚀 Configurando sistema de número de expediente...';

-- 1. Agregar índice único a OC_NUMERO_EXPEDIENTE si no existe
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'UQ_OC_NUMERO_EXPEDIENTE' AND object_id = OBJECT_ID('SIST_ORDEN_COMPRA'))
BEGIN
    CREATE UNIQUE INDEX UQ_OC_NUMERO_EXPEDIENTE ON SIST_ORDEN_COMPRA(OC_NUMERO_EXPEDIENTE) WHERE OC_NUMERO_EXPEDIENTE IS NOT NULL;
    PRINT '✅ Índice único creado en OC_NUMERO_EXPEDIENTE';
END
ELSE
BEGIN
    PRINT '✅ Índice único ya existe en OC_NUMERO_EXPEDIENTE';
END

-- 2. Crear función para generar el siguiente número de expediente
IF OBJECT_ID('fn_GenerarNumeroExpediente', 'FN') IS NOT NULL
    DROP FUNCTION fn_GenerarNumeroExpediente;

CREATE FUNCTION fn_GenerarNumeroExpediente()
RETURNS NVARCHAR(50)
AS
BEGIN
    DECLARE @nuevoNumero NVARCHAR(50);
    DECLARE @anioMes NVARCHAR(6);
    DECLARE @ultimoNumero INT;
    DECLARE @siguienteNumero INT;
    
    -- Obtener año y mes actual (YYYYMM)
    SET @anioMes = FORMAT(GETDATE(), 'yyyyMM');
    
    -- Buscar el último número del mes actual
    SELECT @ultimoNumero = MAX(CAST(RIGHT(OC_NUMERO_EXPEDIENTE, 4) AS INT))
    FROM SIST_ORDEN_COMPRA
    WHERE OC_NUMERO_EXPEDIENTE LIKE @anioMes + '%'
    AND LEN(OC_NUMERO_EXPEDIENTE) = 10
    AND ISNUMERIC(OC_NUMERO_EXPEDIENTE) = 1;
    
    -- Si no hay registros este mes, empezar desde 1
    IF @ultimoNumero IS NULL
        SET @siguienteNumero = 1;
    ELSE
        SET @siguienteNumero = @ultimoNumero + 1;
    
    -- Formatear con ceros a la izquierda (4 dígitos)
    SET @nuevoNumero = @anioMes + RIGHT('0000' + CAST(@siguienteNumero AS NVARCHAR(4)), 4);
    
    RETURN @nuevoNumero;
END

PRINT '✅ Función fn_GenerarNumeroExpediente creada';

-- 3. Crear procedimiento almacenado para generar número de expediente con manejo de concurrencia
IF OBJECT_ID('sp_GenerarNumeroExpediente', 'P') IS NOT NULL
    DROP PROCEDURE sp_GenerarNumeroExpediente;

CREATE PROCEDURE sp_GenerarNumeroExpediente
    @NumeroExpediente NVARCHAR(50) OUTPUT
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @anioMes NVARCHAR(6);
    DECLARE @ultimoNumero INT;
    DECLARE @siguienteNumero INT;
    DECLARE @intentos INT = 0;
    DECLARE @maxIntentos INT = 10;
    DECLARE @exito BIT = 0;
    
    WHILE @intentos < @maxIntentos AND @exito = 0
    BEGIN
        BEGIN TRY
            -- Obtener año y mes actual (YYYYMM)
            SET @anioMes = FORMAT(GETDATE(), 'yyyyMM');
            
            -- Usar UPDLOCK y HOLDLOCK para evitar condiciones de carrera
            SELECT @ultimoNumero = MAX(CAST(RIGHT(OC_NUMERO_EXPEDIENTE, 4) AS INT))
            FROM SIST_ORDEN_COMPRA WITH (UPDLOCK, HOLDLOCK)
            WHERE OC_NUMERO_EXPEDIENTE LIKE @anioMes + '%'
            AND LEN(OC_NUMERO_EXPEDIENTE) = 10
            AND ISNUMERIC(OC_NUMERO_EXPEDIENTE) = 1;
            
            -- Si no hay registros este mes, empezar desde 1
            IF @ultimoNumero IS NULL
                SET @siguienteNumero = 1;
            ELSE
                SET @siguienteNumero = @ultimoNumero + 1;
            
            -- Formatear con ceros a la izquierda (4 dígitos)
            SET @NumeroExpediente = @anioMes + RIGHT('0000' + CAST(@siguienteNumero AS NVARCHAR(4)), 4);
            
            SET @exito = 1;
        END TRY
        BEGIN CATCH
            SET @intentos = @intentos + 1;
            IF @intentos >= @maxIntentos
            BEGIN
                -- Si falló después de varios intentos, lanzar error
                THROW;
            END
            ELSE
            BEGIN
                -- Esperar un poco antes de reintentar
                WAITFOR DELAY '00:00:00.100';
            END
        END CATCH
    END
    
    RETURN 0;
END

PRINT '✅ Procedimiento sp_GenerarNumeroExpediente creado';

-- 4. Verificar que el campo OC_NUMERO_EXPEDIENTE existe
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_NUMERO_EXPEDIENTE')
BEGIN
    ALTER TABLE SIST_ORDEN_COMPRA ADD OC_NUMERO_EXPEDIENTE NVARCHAR(50);
    PRINT '✅ Campo OC_NUMERO_EXPEDIENTE agregado';
END
ELSE
BEGIN
    PRINT '✅ Campo OC_NUMERO_EXPEDIENTE ya existe';
END

-- 5. Crear índice para búsquedas rápidas por número de expediente
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IDX_OC_NUMERO_EXPEDIENTE' AND object_id = OBJECT_ID('SIST_ORDEN_COMPRA'))
BEGIN
    CREATE INDEX IDX_OC_NUMERO_EXPEDIENTE ON SIST_ORDEN_COMPRA(OC_NUMERO_EXPEDIENTE);
    PRINT '✅ Índice de búsqueda creado en OC_NUMERO_EXPEDIENTE';
END
ELSE
BEGIN
    PRINT '✅ Índice de búsqueda ya existe en OC_NUMERO_EXPEDIENTE';
END

PRINT '🎉 Sistema de número de expediente configurado correctamente';
PRINT '📋 Formato: YYYYMM0001 (ejemplo: 2025100001)';
PRINT '🔄 Se reinicia automáticamente cada mes';
