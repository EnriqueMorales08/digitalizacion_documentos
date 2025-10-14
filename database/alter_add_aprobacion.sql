-- =====================================================
-- Agregar columnas para sistema de aprobación
-- =====================================================

USE FACCARPRUEBA;
GO

-- Agregar columnas de centro de costo
ALTER TABLE SIST_ORDEN_COMPRA
ADD OC_AGENCIA NVARCHAR(100);
GO

ALTER TABLE SIST_ORDEN_COMPRA
ADD OC_NOMBRE_RESPONSABLE NVARCHAR(200);
GO

ALTER TABLE SIST_ORDEN_COMPRA
ADD OC_CENTRO_COSTO NVARCHAR(50);
GO

ALTER TABLE SIST_ORDEN_COMPRA
ADD OC_EMAIL_CENTRO_COSTO NVARCHAR(150);
GO

-- Agregar columna de estado de aprobación
ALTER TABLE SIST_ORDEN_COMPRA
ADD OC_ESTADO_APROBACION NVARCHAR(20) DEFAULT 'PENDIENTE';
GO
-- Valores posibles: PENDIENTE, APROBADO, RECHAZADO

-- Agregar columna de fecha de aprobación
ALTER TABLE SIST_ORDEN_COMPRA
ADD OC_FECHA_APROBACION DATETIME;
GO

-- Agregar columna de observaciones de aprobación
ALTER TABLE SIST_ORDEN_COMPRA
ADD OC_OBSERVACIONES_APROBACION NVARCHAR(500);
GO

PRINT '✅ Columnas de aprobación agregadas exitosamente';
GO
