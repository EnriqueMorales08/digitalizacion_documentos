-- =====================================================
-- SCRIPT DE MIGRACIÓN: Agregar tabla SIST_CONFIRMACIONES_CAJERA
-- Fecha: 2025-11-21
-- Descripción: Tabla para registrar confirmaciones de cajera
--              con firma digital y aprobación/rechazo
-- =====================================================

USE FACCARPRUEBA;
GO

-- Verificar si la tabla ya existe
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SIST_CONFIRMACIONES_CAJERA]') AND type in (N'U'))
BEGIN
    PRINT '🔧 Creando tabla SIST_CONFIRMACIONES_CAJERA...'
    
    CREATE TABLE SIST_CONFIRMACIONES_CAJERA (
        CAJERA_ID INT IDENTITY(1,1) PRIMARY KEY,
        CAJERA_NUMERO_EXPEDIENTE NVARCHAR(50) NOT NULL,
        CAJERA_EMAIL NVARCHAR(255) NOT NULL,
        CAJERA_TOKEN NVARCHAR(64) NOT NULL UNIQUE,
        CAJERA_ESTADO NVARCHAR(20) DEFAULT 'PENDIENTE', -- PENDIENTE, APROBADO, RECHAZADO
        CAJERA_FECHA_ENVIO DATETIME DEFAULT GETDATE(),
        CAJERA_FECHA_RESPUESTA DATETIME NULL,
        CAJERA_IP NVARCHAR(50) NULL,
        CAJERA_OBSERVACIONES NVARCHAR(MAX) NULL,
        CAJERA_FIRMA NVARCHAR(500) NULL -- URL de la firma (ejemplo: http://190.238.78.104:3800/robot-sdg-ford/firmas/cajera.png)
    );
    
    -- Crear índices para mejorar el rendimiento
    CREATE INDEX IDX_CAJERA_EXPEDIENTE ON SIST_CONFIRMACIONES_CAJERA(CAJERA_NUMERO_EXPEDIENTE);
    CREATE INDEX IDX_CAJERA_TOKEN ON SIST_CONFIRMACIONES_CAJERA(CAJERA_TOKEN);
    CREATE INDEX IDX_CAJERA_ESTADO ON SIST_CONFIRMACIONES_CAJERA(CAJERA_ESTADO);
    CREATE INDEX IDX_CAJERA_EMAIL ON SIST_CONFIRMACIONES_CAJERA(CAJERA_EMAIL);
    
    PRINT '✅ Tabla SIST_CONFIRMACIONES_CAJERA creada exitosamente'
    PRINT '📧 Sistema de confirmación de cajera listo'
    PRINT ''
    PRINT '📋 Estructura de la tabla:'
    PRINT '   - CAJERA_ID: ID único autoincremental'
    PRINT '   - CAJERA_NUMERO_EXPEDIENTE: Número del expediente'
    PRINT '   - CAJERA_EMAIL: Email de la cajera'
    PRINT '   - CAJERA_TOKEN: Token único para confirmación (64 caracteres)'
    PRINT '   - CAJERA_ESTADO: PENDIENTE | APROBADO | RECHAZADO'
    PRINT '   - CAJERA_FECHA_ENVIO: Fecha de envío del correo'
    PRINT '   - CAJERA_FECHA_RESPUESTA: Fecha de respuesta'
    PRINT '   - CAJERA_IP: IP desde donde confirmó'
    PRINT '   - CAJERA_OBSERVACIONES: Comentarios de la cajera'
    PRINT '   - CAJERA_FIRMA: URL de la firma (NVARCHAR(500))'
    PRINT ''
    PRINT '💡 NOTA: La firma se guarda en OC_VISTO_ADV de SIST_ORDEN_COMPRA'
END
ELSE
BEGIN
    PRINT '⚠️  La tabla SIST_CONFIRMACIONES_CAJERA ya existe'
    PRINT '    No se realizaron cambios'
END
GO

PRINT ''
PRINT '🎉 Script ejecutado correctamente'
GO
