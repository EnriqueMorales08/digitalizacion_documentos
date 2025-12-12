-- =====================================================
-- SCRIPT PARA RECREAR TABLA DE AUDITORÍA
-- Base de datos: FACCARPRUEBA
-- Tabla: SIST_AUDIT_LOG
-- Este script ELIMINA y RECREA la tabla
-- =====================================================

USE FACCARPRUEBA;
GO

PRINT '🔧 Iniciando recreación de tabla SIST_AUDIT_LOG...';
PRINT '';

-- =====================================================
-- PASO 1: ELIMINAR TABLA SI EXISTE
-- =====================================================

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SIST_AUDIT_LOG]') AND type in (N'U'))
BEGIN
    PRINT '⚠️  Eliminando tabla SIST_AUDIT_LOG existente...';
    DROP TABLE SIST_AUDIT_LOG;
    PRINT '✅ Tabla eliminada exitosamente.';
    PRINT '';
END
ELSE
BEGIN
    PRINT '✅ La tabla SIST_AUDIT_LOG no existe.';
    PRINT '';
END

-- =====================================================
-- PASO 2: CREAR TABLA SIST_AUDIT_LOG
-- =====================================================

PRINT '📋 Creando tabla SIST_AUDIT_LOG...';

CREATE TABLE SIST_AUDIT_LOG (
    AUDIT_ID INT IDENTITY(1,1) PRIMARY KEY,
    AUDIT_TIMESTAMP DATETIME DEFAULT GETDATE(),
    
    -- Información del usuario que realizó el cambio
    AUDIT_USER_ID NVARCHAR(100),           -- Usuario que hizo el cambio
    AUDIT_USER_NAME NVARCHAR(200),         -- Nombre completo del usuario
    AUDIT_USER_EMAIL NVARCHAR(255),        -- Email del usuario
    AUDIT_USER_ROLE NVARCHAR(50),          -- Rol del usuario (USER, ADMIN)
    
    -- Información del documento modificado
    AUDIT_DOCUMENT_TYPE NVARCHAR(100),     -- Tipo de documento (ORDEN_COMPRA, ACTA, etc.)
    AUDIT_DOCUMENT_ID INT,                 -- ID del documento modificado
    AUDIT_ORDEN_ID INT,                    -- ID de la orden de compra relacionada
    AUDIT_NUMERO_EXPEDIENTE NVARCHAR(50),  -- Número de expediente
    
    -- Detalles del cambio
    AUDIT_ACTION NVARCHAR(50),             -- Acción: INSERT, UPDATE, DELETE
    AUDIT_FIELD_NAME NVARCHAR(200),        -- Nombre del campo modificado
    AUDIT_OLD_VALUE NVARCHAR(MAX),         -- Valor anterior
    AUDIT_NEW_VALUE NVARCHAR(MAX),         -- Valor nuevo
    
    -- Información adicional
    AUDIT_IP_ADDRESS NVARCHAR(50),         -- Dirección IP del usuario
    AUDIT_SESSION_ID NVARCHAR(100),        -- ID de sesión
    AUDIT_DESCRIPTION NVARCHAR(500)        -- Descripción adicional del cambio
);

PRINT '✅ Tabla SIST_AUDIT_LOG creada exitosamente.';
PRINT '';

-- =====================================================
-- PASO 3: CREAR ÍNDICES
-- =====================================================

PRINT '📊 Creando índices...';

-- Índice por timestamp (DESC para ver los más recientes primero)
CREATE INDEX IDX_AUDIT_TIMESTAMP ON SIST_AUDIT_LOG(AUDIT_TIMESTAMP DESC);
PRINT '   ✅ IDX_AUDIT_TIMESTAMP';

-- Índice por usuario
CREATE INDEX IDX_AUDIT_USER_ID ON SIST_AUDIT_LOG(AUDIT_USER_ID);
PRINT '   ✅ IDX_AUDIT_USER_ID';

-- Índice por orden ID
CREATE INDEX IDX_AUDIT_ORDEN_ID ON SIST_AUDIT_LOG(AUDIT_ORDEN_ID);
PRINT '   ✅ IDX_AUDIT_ORDEN_ID';

-- Índice por tipo de documento
CREATE INDEX IDX_AUDIT_DOCUMENT_TYPE ON SIST_AUDIT_LOG(AUDIT_DOCUMENT_TYPE);
PRINT '   ✅ IDX_AUDIT_DOCUMENT_TYPE';

-- Índice por número de expediente
CREATE INDEX IDX_AUDIT_NUMERO_EXPEDIENTE ON SIST_AUDIT_LOG(AUDIT_NUMERO_EXPEDIENTE);
PRINT '   ✅ IDX_AUDIT_NUMERO_EXPEDIENTE';

PRINT '';
PRINT '✅ Todos los índices creados.';
PRINT '';

-- =====================================================
-- PASO 4: VERIFICACIÓN
-- =====================================================

PRINT '🔍 Verificando instalación...';
PRINT '';

-- Verificar tabla
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SIST_AUDIT_LOG]') AND type in (N'U'))
BEGIN
    PRINT '   ✅ Tabla SIST_AUDIT_LOG: OK';
    
    -- Contar columnas
    DECLARE @ColumnCount INT;
    SELECT @ColumnCount = COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'SIST_AUDIT_LOG';
    
    PRINT '   ✅ Columnas creadas: ' + CAST(@ColumnCount AS NVARCHAR(10));
    
    -- Contar índices
    DECLARE @IndexCount INT;
    SELECT @IndexCount = COUNT(*) 
    FROM sys.indexes 
    WHERE object_id = OBJECT_ID('SIST_AUDIT_LOG') 
    AND name IS NOT NULL;
    
    PRINT '   ✅ Índices creados: ' + CAST(@IndexCount AS NVARCHAR(10));
    
    PRINT '';
    PRINT '📋 Estructura de la tabla:';
    PRINT '';
    
    -- Mostrar columnas
    SELECT 
        COLUMN_NAME AS Columna,
        DATA_TYPE AS Tipo,
        CHARACTER_MAXIMUM_LENGTH AS Longitud,
        IS_NULLABLE AS Permite_NULL
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'SIST_AUDIT_LOG'
    ORDER BY ORDINAL_POSITION;
    
END
ELSE
BEGIN
    PRINT '   ❌ ERROR: La tabla no se creó correctamente';
END

PRINT '';
PRINT '🎉 ¡RECREACIÓN COMPLETADA EXITOSAMENTE!';
PRINT '';
PRINT '📝 Prueba con esta consulta:';
PRINT 'SELECT * FROM SIST_AUDIT_LOG;';
PRINT '';

GO
