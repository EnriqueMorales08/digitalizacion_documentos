-- =====================================================
-- SCRIPT DE INSTALACIÓN: SISTEMA DE AUDITORÍA
-- Base de datos: FACCARPRUEBA
-- Tabla: SIST_AUDIT_LOG
-- Fecha: Noviembre 2024
-- =====================================================

USE FACCARPRUEBA;
GO

PRINT '🚀 Iniciando instalación del sistema de auditoría...';
PRINT '';

-- =====================================================
-- VERIFICAR SI LA TABLA YA EXISTE
-- =====================================================

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SIST_AUDIT_LOG]') AND type in (N'U'))
BEGIN
    PRINT '⚠️  La tabla SIST_AUDIT_LOG ya existe.';
    PRINT '   Si deseas recrearla, ejecuta primero:';
    PRINT '   DROP TABLE SIST_AUDIT_LOG;';
    PRINT '';
    PRINT '❌ Instalación cancelada para evitar pérdida de datos.';
END
ELSE
BEGIN
    PRINT '✅ La tabla SIST_AUDIT_LOG no existe. Procediendo con la creación...';
    PRINT '';

    -- =====================================================
    -- CREAR TABLA SIST_AUDIT_LOG
    -- =====================================================

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
    -- CREAR ÍNDICES PARA OPTIMIZAR CONSULTAS
    -- =====================================================

    PRINT '📊 Creando índices para optimizar rendimiento...';

    -- Índice por timestamp (DESC para ver los más recientes primero)
    CREATE INDEX IDX_AUDIT_TIMESTAMP ON SIST_AUDIT_LOG(AUDIT_TIMESTAMP DESC);
    PRINT '   ✅ Índice IDX_AUDIT_TIMESTAMP creado';

    -- Índice por usuario
    CREATE INDEX IDX_AUDIT_USER_ID ON SIST_AUDIT_LOG(AUDIT_USER_ID);
    PRINT '   ✅ Índice IDX_AUDIT_USER_ID creado';

    -- Índice por orden ID
    CREATE INDEX IDX_AUDIT_ORDEN_ID ON SIST_AUDIT_LOG(AUDIT_ORDEN_ID);
    PRINT '   ✅ Índice IDX_AUDIT_ORDEN_ID creado';

    -- Índice por tipo de documento
    CREATE INDEX IDX_AUDIT_DOCUMENT_TYPE ON SIST_AUDIT_LOG(AUDIT_DOCUMENT_TYPE);
    PRINT '   ✅ Índice IDX_AUDIT_DOCUMENT_TYPE creado';

    -- Índice por número de expediente
    CREATE INDEX IDX_AUDIT_NUMERO_EXPEDIENTE ON SIST_AUDIT_LOG(AUDIT_NUMERO_EXPEDIENTE);
    PRINT '   ✅ Índice IDX_AUDIT_NUMERO_EXPEDIENTE creado';

    PRINT '';
    PRINT '✅ Todos los índices creados exitosamente.';
    PRINT '';

    -- =====================================================
    -- VERIFICACIÓN FINAL
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
    END
    ELSE
    BEGIN
        PRINT '   ❌ ERROR: La tabla no se creó correctamente';
    END

    PRINT '';
    PRINT '🎉 ¡INSTALACIÓN COMPLETADA EXITOSAMENTE!';
    PRINT '';
    PRINT '📋 Próximos pasos:';
    PRINT '   1. Verificar que los archivos PHP estén en su lugar';
    PRINT '   2. Asignar rol ADMIN a usuarios que deben ver reportes';
    PRINT '   3. Acceder a /digitalizacion-documentos/audit';
    PRINT '';
    PRINT '📖 Documentación:';
    PRINT '   - INSTRUCCIONES_SISTEMA_AUDITORIA.md';
    PRINT '   - RESUMEN_SISTEMA_AUDITORIA.md';
    PRINT '';
END

GO

-- =====================================================
-- CONSULTAS ÚTILES PARA VERIFICACIÓN
-- =====================================================

PRINT '📊 Consultas útiles:';
PRINT '';
PRINT '-- Ver estructura de la tabla:';
PRINT 'EXEC sp_help ''SIST_AUDIT_LOG'';';
PRINT '';
PRINT '-- Ver todos los índices:';
PRINT 'EXEC sp_helpindex ''SIST_AUDIT_LOG'';';
PRINT '';
PRINT '-- Contar registros:';
PRINT 'SELECT COUNT(*) AS TotalRegistros FROM SIST_AUDIT_LOG;';
PRINT '';
PRINT '-- Ver últimos 10 cambios:';
PRINT 'SELECT TOP 10 * FROM SIST_AUDIT_LOG ORDER BY AUDIT_TIMESTAMP DESC;';
PRINT '';

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
