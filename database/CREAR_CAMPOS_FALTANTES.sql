-- ═══════════════════════════════════════════════════════════════════════════
-- CREAR CAMPOS FALTANTES - ABONOS 2 AL 7
-- Base de datos: FACCARPRUEBA
-- ═══════════════════════════════════════════════════════════════════════════

USE FACCARPRUEBA;

PRINT '🚀 Creando campos faltantes de abonos...';
PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- ABONO 2
-- ═══════════════════════════════════════════════════════════════════════════
ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ARCHIVO_ABONO1 NVARCHAR(500);

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_MONTO_2 NVARCHAR(50);
PRINT '✅ OC_MONTO_2 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_NRO_OPERACION_2 NVARCHAR(50);
PRINT '✅ OC_NRO_OPERACION_2 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ENTIDAD_FINANCIERA_2 NVARCHAR(100);
PRINT '✅ OC_ENTIDAD_FINANCIERA_2 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ARCHIVO_ABONO2 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO_2 creado';

PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- ABONO 3
-- ═══════════════════════════════════════════════════════════════════════════

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_MONTO_3 NVARCHAR(50);
PRINT '✅ OC_MONTO_3 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_NRO_OPERACION_3 NVARCHAR(50);
PRINT '✅ OC_NRO_OPERACION_3 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ENTIDAD_FINANCIERA_3 NVARCHAR(100);
PRINT '✅ OC_ENTIDAD_FINANCIERA_3 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ARCHIVO_ABONO3 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO_3 creado';

PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- ABONO 4
-- ═══════════════════════════════════════════════════════════════════════════

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_MONTO_4 NVARCHAR(50);
PRINT '✅ OC_MONTO_4 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_NRO_OPERACION_4 NVARCHAR(50);
PRINT '✅ OC_NRO_OPERACION_4 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ENTIDAD_FINANCIERA_4 NVARCHAR(100);
PRINT '✅ OC_ENTIDAD_FINANCIERA_4 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ARCHIVO_ABONO4 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO_4 creado';

PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- ABONO 5
-- ═══════════════════════════════════════════════════════════════════════════

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_MONTO_5 NVARCHAR(50);
PRINT '✅ OC_MONTO_5 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_NRO_OPERACION_5 NVARCHAR(50);
PRINT '✅ OC_NRO_OPERACION_5 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ENTIDAD_FINANCIERA_5 NVARCHAR(100);
PRINT '✅ OC_ENTIDAD_FINANCIERA_5 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ARCHIVO_ABONO5 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO_5 creado';

PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- ABONO 6
-- ═══════════════════════════════════════════════════════════════════════════

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_MONTO_6 NVARCHAR(50);
PRINT '✅ OC_MONTO_6 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_NRO_OPERACION_6 NVARCHAR(50);
PRINT '✅ OC_NRO_OPERACION_6 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ENTIDAD_FINANCIERA_6 NVARCHAR(100);
PRINT '✅ OC_ENTIDAD_FINANCIERA_6 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ARCHIVO_ABONO6 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO_6 creado';

PRINT '';

-- ═══════════════════════════════════════════════════════════════════════════
-- ABONO 7
-- ═══════════════════════════════════════════════════════════════════════════

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_MONTO_7 NVARCHAR(50);
PRINT '✅ OC_MONTO_7 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_NRO_OPERACION_7 NVARCHAR(50);
PRINT '✅ OC_NRO_OPERACION_7 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ENTIDAD_FINANCIERA_7 NVARCHAR(100);
PRINT '✅ OC_ENTIDAD_FINANCIERA_7 creado';

ALTER TABLE SIST_ORDEN_COMPRA ADD OC_ARCHIVO_ABONO7 NVARCHAR(500);
PRINT '✅ OC_ARCHIVO_ABONO_7 creado';

PRINT '';
PRINT '╔═══════════════════════════════════════════════════════════════════════════╗';
PRINT '║  ✅ TODOS LOS CAMPOS CREADOS EXITOSAMENTE                                ║';
PRINT '╚═══════════════════════════════════════════════════════════════════════════╝';
PRINT '';
PRINT '📊 Total de campos creados: 24 campos (abonos 2-7)';
PRINT '';
PRINT '   Abono 2: OC_MONTO_2, OC_NRO_OPERACION_2, OC_ENTIDAD_FINANCIERA_2, OC_ARCHIVO_ABONO_2';
PRINT '   Abono 3: OC_MONTO_3, OC_NRO_OPERACION_3, OC_ENTIDAD_FINANCIERA_3, OC_ARCHIVO_ABONO_3';
PRINT '   Abono 4: OC_MONTO_4, OC_NRO_OPERACION_4, OC_ENTIDAD_FINANCIERA_4, OC_ARCHIVO_ABONO_4';
PRINT '   Abono 5: OC_MONTO_5, OC_NRO_OPERACION_5, OC_ENTIDAD_FINANCIERA_5, OC_ARCHIVO_ABONO_5';
PRINT '   Abono 6: OC_MONTO_6, OC_NRO_OPERACION_6, OC_ENTIDAD_FINANCIERA_6, OC_ARCHIVO_ABONO_6';
PRINT '   Abono 7: OC_MONTO_7, OC_NRO_OPERACION_7, OC_ENTIDAD_FINANCIERA_7, OC_ARCHIVO_ABONO_7';
PRINT '';
PRINT '🎉 ¡Listo! Ahora tienes todos los campos para 7 abonos.';
