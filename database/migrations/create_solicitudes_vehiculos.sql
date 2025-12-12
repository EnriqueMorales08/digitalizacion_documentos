-- =====================================================
-- TABLA: SIST_SOLICITUDES_VEHICULOS
-- Almacena las solicitudes de asignación de vehículos
-- entre asesores
-- =====================================================

-- Eliminar tabla si existe (solo para desarrollo)
IF OBJECT_ID('SIST_SOLICITUDES_VEHICULOS', 'U') IS NOT NULL
    DROP TABLE SIST_SOLICITUDES_VEHICULOS;
GO

CREATE TABLE SIST_SOLICITUDES_VEHICULOS (
    SOL_ID INT IDENTITY(1,1) PRIMARY KEY,
    
    -- Datos del vehículo
    SOL_CHASIS NVARCHAR(100) NOT NULL,
    SOL_MARCA NVARCHAR(100),
    SOL_UBICACION NVARCHAR(100),
    
    -- Tipo de solicitud
    SOL_TIPO NVARCHAR(20) NOT NULL, -- 'LIBRE' o 'REASIGNACION'
    
    -- Asesor solicitante (quien quiere el vehículo)
    SOL_ASESOR_SOLICITANTE_NOMBRE NVARCHAR(200) NOT NULL,
    SOL_ASESOR_SOLICITANTE_EMAIL NVARCHAR(150) NOT NULL,
    
    -- Asesor dueño (quien tiene el vehículo asignado actualmente)
    -- Solo aplica para tipo 'REASIGNACION'
    SOL_ASESOR_DUENO_NOMBRE NVARCHAR(200),
    SOL_ASESOR_DUENO_EMAIL NVARCHAR(150),
    
    -- Estado de la solicitud
    SOL_ESTADO NVARCHAR(20) DEFAULT 'PENDIENTE', -- PENDIENTE, ACEPTADO, RECHAZADO, CANCELADO
    
    -- Token único para validar la respuesta
    SOL_TOKEN NVARCHAR(100) UNIQUE NOT NULL,
    
    -- Fechas
    SOL_FECHA_SOLICITUD DATETIME DEFAULT GETDATE(),
    SOL_FECHA_RESPUESTA DATETIME,
    
    -- Observaciones
    SOL_OBSERVACIONES NVARCHAR(MAX),
    
    -- IP del que responde (para auditoría)
    SOL_IP_RESPUESTA NVARCHAR(50),
    
    -- Timestamps
    SOL_CREATED_AT DATETIME DEFAULT GETDATE(),
    SOL_UPDATED_AT DATETIME DEFAULT GETDATE()
);
GO

-- Índices para mejorar rendimiento
CREATE INDEX IDX_SOL_CHASIS ON SIST_SOLICITUDES_VEHICULOS(SOL_CHASIS);
CREATE INDEX IDX_SOL_TOKEN ON SIST_SOLICITUDES_VEHICULOS(SOL_TOKEN);
CREATE INDEX IDX_SOL_ESTADO ON SIST_SOLICITUDES_VEHICULOS(SOL_ESTADO);
CREATE INDEX IDX_SOL_FECHA_SOLICITUD ON SIST_SOLICITUDES_VEHICULOS(SOL_FECHA_SOLICITUD);
GO

PRINT '✅ Tabla SIST_SOLICITUDES_VEHICULOS creada exitosamente';
GO
