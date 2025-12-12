-- =====================================================
-- MIGRACIÓN: Agregar tabla SIST_CARTA_FELICITACIONES
-- Fecha: 2025-11-26
-- Descripción: Tabla faltante para el documento Carta de Felicitaciones
-- =====================================================

-- Verificar si la tabla ya existe antes de crearla
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[SIST_CARTA_FELICITACIONES]') AND type in (N'U'))
BEGIN
    CREATE TABLE SIST_CARTA_FELICITACIONES (
        CF_ID INT IDENTITY(1,1) PRIMARY KEY,
        CF_FECHA_CREACION DATETIME DEFAULT GETDATE(),
        CF_DOCUMENTO_VENTA_ID INT,
        
        -- Datos del cliente
        CF_CLIENTE_NOMBRE NVARCHAR(200),
        
        -- Datos del vehículo
        CF_VEHICULO_MARCA NVARCHAR(100),
        CF_VEHICULO_MODELO NVARCHAR(100),
        
        -- Datos del asesor
        CF_ASESOR_NOMBRE NVARCHAR(200),
        CF_ASESOR_CELULAR NVARCHAR(50),
        
        -- Aplicación
        CF_APLICACION_NOMBRE NVARCHAR(200),
        
        FOREIGN KEY (CF_DOCUMENTO_VENTA_ID) REFERENCES SIST_ORDEN_COMPRA(OC_ID)
    );
    
    PRINT '✅ Tabla SIST_CARTA_FELICITACIONES creada exitosamente';
END
ELSE
BEGIN
    PRINT '⚠️ La tabla SIST_CARTA_FELICITACIONES ya existe';
END
GO
