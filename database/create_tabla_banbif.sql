-- =====================================================
-- Script para crear tabla SIST_CARTA_CARACTERISTICAS_BANBIF
-- Ejecutar este script en la base de datos FACCARPRUEBA
-- =====================================================

USE FACCARPRUEBA;
GO

PRINT '🚀 Creando tabla SIST_CARTA_CARACTERISTICAS_BANBIF...';
PRINT '';

-- Verificar si la tabla ya existe
IF EXISTS (SELECT * FROM sys.tables WHERE name = 'SIST_CARTA_CARACTERISTICAS_BANBIF')
BEGIN
    PRINT '⚠️ La tabla SIST_CARTA_CARACTERISTICAS_BANBIF ya existe.';
    PRINT '   Si desea recrearla, elimínela manualmente primero.';
END
ELSE
BEGIN
    -- Crear la tabla
    CREATE TABLE SIST_CARTA_CARACTERISTICAS_BANBIF (
        CCB_ID INT IDENTITY(1,1) PRIMARY KEY,
        CCB_FECHA_CREACION DATETIME DEFAULT GETDATE(),
        CCB_DOCUMENTO_VENTA_ID INT,

        -- Encabezado
        CCB_FECHA_CARTA NVARCHAR(200),
        CCB_NOMBRE_CONCESIONARIO NVARCHAR(200),
        CCB_RUC_CONCESIONARIO NVARCHAR(50),

        -- Datos del cliente
        CCB_CLIENTE_NOMBRE NVARCHAR(200),
        CCB_PROPIETARIO_TARJETA NVARCHAR(200),

        -- Datos del vehículo
        CCB_VEHICULO_MARCA NVARCHAR(100),
        CCB_VEHICULO_MODELO NVARCHAR(100),
        CCB_VEHICULO_ANIO_MODELO NVARCHAR(10),
        CCB_VEHICULO_COLOR NVARCHAR(50),
        CCB_VEHICULO_CLASE NVARCHAR(100),
        CCB_VEHICULO_MOTOR NVARCHAR(100),
        CCB_VEHICULO_CARROCERIA NVARCHAR(50),
        CCB_VEHICULO_CHASIS NVARCHAR(50),

        -- Información financiera
        CCB_PRECIO_VENTA_USD DECIMAL(12,2),
        CCB_PRECIO_VENTA_PEN DECIMAL(12,2),
        CCB_CUOTA_INICIAL_USD DECIMAL(12,2),
        CCB_CUOTA_INICIAL_PEN DECIMAL(12,2),
        CCB_SALDO_PRECIO_USD DECIMAL(12,2),
        CCB_SALDO_PRECIO_PEN DECIMAL(12,2),
        CCB_BENEFICIO_BANBIF_USD DECIMAL(12,2),
        CCB_BENEFICIO_BANBIF_PEN DECIMAL(12,2),

        -- Firma
        CCB_FIRMA_REPRESENTANTE NVARCHAR(500),

        FOREIGN KEY (CCB_DOCUMENTO_VENTA_ID) REFERENCES SIST_ORDEN_COMPRA(OC_ID)
    );

    PRINT '✅ Tabla SIST_CARTA_CARACTERISTICAS_BANBIF creada exitosamente';
    PRINT '';
    PRINT '📋 Estructura de la tabla:';
    PRINT '   - CCB_ID (PK)';
    PRINT '   - CCB_FECHA_CREACION';
    PRINT '   - CCB_DOCUMENTO_VENTA_ID (FK)';
    PRINT '   - Campos de encabezado (3)';
    PRINT '   - Campos de cliente (2)';
    PRINT '   - Campos de vehículo (8)';
    PRINT '   - Campos financieros (8)';
    PRINT '   - Campo de firma (1)';
    PRINT '';
    PRINT '✅ Total: 23 campos';
END
GO

PRINT '';
PRINT '✅ Script completado';
