-- =====================================================
-- SISTEMA DE NUMERO DE EXPEDIENTE AUTO-GENERADO
-- Formato: YYYYMM0001 (se reinicia cada mes)
-- =====================================================

PRINT 'Configurando sistema de numero de expediente...';

-- 1. Agregar indice unico a OC_NUMERO_EXPEDIENTE si no existe
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'UQ_OC_NUMERO_EXPEDIENTE' AND object_id = OBJECT_ID('SIST_ORDEN_COMPRA'))
BEGIN
    CREATE UNIQUE INDEX UQ_OC_NUMERO_EXPEDIENTE ON SIST_ORDEN_COMPRA(OC_NUMERO_EXPEDIENTE) WHERE OC_NUMERO_EXPEDIENTE IS NOT NULL;
    PRINT 'Indice unico creado en OC_NUMERO_EXPEDIENTE';
END
ELSE
BEGIN
    PRINT 'Indice unico ya existe en OC_NUMERO_EXPEDIENTE';
END

-- 2. Verificar que el campo OC_NUMERO_EXPEDIENTE existe
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SIST_ORDEN_COMPRA') AND name = 'OC_NUMERO_EXPEDIENTE')
BEGIN
    ALTER TABLE SIST_ORDEN_COMPRA ADD OC_NUMERO_EXPEDIENTE NVARCHAR(50);
    PRINT 'Campo OC_NUMERO_EXPEDIENTE agregado';
END
ELSE
BEGIN
    PRINT 'Campo OC_NUMERO_EXPEDIENTE ya existe';
END

-- 3. Crear indice para busquedas rapidas por numero de expediente
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IDX_OC_NUMERO_EXPEDIENTE' AND object_id = OBJECT_ID('SIST_ORDEN_COMPRA'))
BEGIN
    CREATE INDEX IDX_OC_NUMERO_EXPEDIENTE ON SIST_ORDEN_COMPRA(OC_NUMERO_EXPEDIENTE);
    PRINT 'Indice de busqueda creado en OC_NUMERO_EXPEDIENTE';
END
ELSE
BEGIN
    PRINT 'Indice de busqueda ya existe en OC_NUMERO_EXPEDIENTE';
END

-- 4. Eliminar procedimiento si existe
IF OBJECT_ID('sp_GenerarNumeroExpediente', 'P') IS NOT NULL
BEGIN
    DROP PROCEDURE sp_GenerarNumeroExpediente;
    PRINT 'Procedimiento anterior eliminado';
END
GO

-- 5. Crear procedimiento almacenado para generar numero de expediente
CREATE PROCEDURE sp_GenerarNumeroExpediente
    @NumeroExpediente NVARCHAR(50) OUTPUT
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @anioMes NVARCHAR(6);
    DECLARE @ultimoNumero INT;
    DECLARE @siguienteNumero INT;
    DECLARE @intentos INT;
    DECLARE @maxIntentos INT;
    DECLARE @exito BIT;
    
    SET @intentos = 0;
    SET @maxIntentos = 10;
    SET @exito = 0;
    
    WHILE @intentos < @maxIntentos AND @exito = 0
    BEGIN
        BEGIN TRY
            -- Obtener anio y mes actual (YYYYMM)
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
            
            -- Formatear con ceros a la izquierda (4 digitos)
            SET @NumeroExpediente = @anioMes + RIGHT('0000' + CAST(@siguienteNumero AS NVARCHAR(4)), 4);
            
            SET @exito = 1;
        END TRY
        BEGIN CATCH
            SET @intentos = @intentos + 1;
            IF @intentos >= @maxIntentos
            BEGIN
                -- Si fallo despues de varios intentos, lanzar error
                DECLARE @ErrorMessage NVARCHAR(4000) = ERROR_MESSAGE();
                DECLARE @ErrorSeverity INT = ERROR_SEVERITY();
                DECLARE @ErrorState INT = ERROR_STATE();
                RAISERROR(@ErrorMessage, @ErrorSeverity, @ErrorState);
            END
            ELSE
            BEGIN
                -- Esperar un poco antes de reintentar
                WAITFOR DELAY '00:00:00.100';
            END
        END CATCH
    END
END
GO

PRINT 'Procedimiento sp_GenerarNumeroExpediente creado correctamente';
PRINT 'Sistema de numero de expediente configurado';
PRINT 'Formato: YYYYMM0001 (ejemplo: 2025100001)';
PRINT 'Se reinicia automaticamente cada mes';
