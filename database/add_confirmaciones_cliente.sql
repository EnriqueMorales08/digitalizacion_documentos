-- =====================================================
-- SCRIPT DE MIGRACIÓN: Agregar tabla SIST_CONFIRMACIONES_CLIENTE
-- Fecha: 2025-11-21
-- Descripción: Tabla para registrar confirmaciones de clientes
--              antes de enviar documentos a cajera
-- =====================================================

USE FACCARPRUEBA;
GO

-- Verificar si la tabla ya existe
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SIST_CONFIRMACIONES_CLIENTE]') AND type in (N'U'))
BEGIN
    PRINT '🔧 Creando tabla SIST_CONFIRMACIONES_CLIENTE...'
    
    CREATE TABLE SIST_CONFIRMACIONES_CLIENTE (
        CONF_ID INT IDENTITY(1,1) PRIMARY KEY,
        CONF_NUMERO_EXPEDIENTE NVARCHAR(50) NOT NULL,
        CONF_EMAIL_CLIENTE NVARCHAR(255) NOT NULL,
        CONF_TOKEN_CONFIRMACION NVARCHAR(100) NOT NULL UNIQUE,
        CONF_ESTADO NVARCHAR(20) DEFAULT 'PENDIENTE', -- PENDIENTE, ACEPTADO, RECHAZADO
        CONF_FECHA_ENVIO DATETIME NOT NULL,
        CONF_FECHA_RESPUESTA DATETIME NULL,
        CONF_IP_CLIENTE NVARCHAR(45) NULL,
        CONF_OBSERVACIONES NVARCHAR(MAX) NULL,
        CONF_ENVIADO_CAJERA BIT DEFAULT 0,
        CONF_FECHA_ENVIO_CAJERA DATETIME NULL,
        CONF_CREATED_AT DATETIME DEFAULT GETDATE(),
        CONF_UPDATED_AT DATETIME DEFAULT GETDATE()
    );
    
    -- Crear índices para mejorar el rendimiento
    CREATE INDEX IDX_CONF_EXPEDIENTE ON SIST_CONFIRMACIONES_CLIENTE(CONF_NUMERO_EXPEDIENTE);
    CREATE INDEX IDX_CONF_TOKEN ON SIST_CONFIRMACIONES_CLIENTE(CONF_TOKEN_CONFIRMACION);
    CREATE INDEX IDX_CONF_ESTADO ON SIST_CONFIRMACIONES_CLIENTE(CONF_ESTADO);
    CREATE INDEX IDX_CONF_EMAIL ON SIST_CONFIRMACIONES_CLIENTE(CONF_EMAIL_CLIENTE);
    
    PRINT '✅ Tabla SIST_CONFIRMACIONES_CLIENTE creada exitosamente'
    PRINT '📧 Sistema de confirmación de cliente listo'
    PRINT ''
    PRINT '📋 Estructura de la tabla:'
    PRINT '   - CONF_ID: ID único autoincremental'
    PRINT '   - CONF_NUMERO_EXPEDIENTE: Número del expediente'
    PRINT '   - CONF_EMAIL_CLIENTE: Email del cliente'
    PRINT '   - CONF_TOKEN_CONFIRMACION: Token único para confirmación'
    PRINT '   - CONF_ESTADO: PENDIENTE | ACEPTADO | RECHAZADO'
    PRINT '   - CONF_FECHA_ENVIO: Fecha de envío del correo'
    PRINT '   - CONF_FECHA_RESPUESTA: Fecha de respuesta del cliente'
    PRINT '   - CONF_IP_CLIENTE: IP desde donde confirmó'
    PRINT '   - CONF_OBSERVACIONES: Comentarios del cliente'
    PRINT '   - CONF_ENVIADO_CAJERA: 0=No enviado, 1=Enviado'
    PRINT '   - CONF_FECHA_ENVIO_CAJERA: Fecha de envío a cajera'
END
ELSE
BEGIN
    PRINT '⚠️  La tabla SIST_CONFIRMACIONES_CLIENTE ya existe'
    PRINT '    No se realizaron cambios'
END
GO

PRINT ''
PRINT '🎉 Script ejecutado correctamente'
GO
