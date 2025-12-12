-- =====================================================
-- INSERTAR REGISTRO DE PRUEBA EN AUDITORÍA
-- Para verificar que la tabla funciona correctamente
-- =====================================================

USE FACCARPRUEBA;
GO

PRINT '📝 Insertando registro de prueba en SIST_AUDIT_LOG...';
PRINT '';

-- Insertar un registro de ejemplo
INSERT INTO SIST_AUDIT_LOG (
    AUDIT_USER_ID,
    AUDIT_USER_NAME,
    AUDIT_USER_EMAIL,
    AUDIT_USER_ROLE,
    AUDIT_DOCUMENT_TYPE,
    AUDIT_DOCUMENT_ID,
    AUDIT_ORDEN_ID,
    AUDIT_NUMERO_EXPEDIENTE,
    AUDIT_ACTION,
    AUDIT_FIELD_NAME,
    AUDIT_OLD_VALUE,
    AUDIT_NEW_VALUE,
    AUDIT_IP_ADDRESS,
    AUDIT_SESSION_ID,
    AUDIT_DESCRIPTION
) VALUES (
    'usuario_prueba',                           -- Usuario
    'Usuario de Prueba',                        -- Nombre completo
    'prueba@ejemplo.com',                       -- Email
    'USER',                                     -- Rol
    'ORDEN_COMPRA',                             -- Tipo de documento
    1,                                          -- ID del documento
    1,                                          -- ID de orden
    '2024110001',                               -- Número de expediente
    'UPDATE',                                   -- Acción
    'OC_PRECIO_VENTA',                         -- Campo modificado
    '50000.00',                                 -- Valor anterior
    '55000.00',                                 -- Valor nuevo
    '192.168.1.100',                           -- IP
    'test_session_123',                        -- ID de sesión
    'Registro de prueba del sistema de auditoría' -- Descripción
);

PRINT '✅ Registro de prueba insertado exitosamente.';
PRINT '';

-- Verificar el registro
PRINT '🔍 Verificando el registro insertado:';
PRINT '';

SELECT 
    AUDIT_ID AS ID,
    AUDIT_TIMESTAMP AS [Fecha/Hora],
    AUDIT_USER_NAME AS Usuario,
    AUDIT_NUMERO_EXPEDIENTE AS Expediente,
    AUDIT_FIELD_NAME AS Campo,
    AUDIT_OLD_VALUE AS [Valor Anterior],
    AUDIT_NEW_VALUE AS [Valor Nuevo]
FROM SIST_AUDIT_LOG
ORDER BY AUDIT_TIMESTAMP DESC;

PRINT '';
PRINT '✅ Ahora puedes acceder a /digitalizacion-documentos/audit para ver el reporte';
PRINT '';

GO
