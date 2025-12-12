-- Agregar columnas para recuperación de contraseña en la tabla firmas
-- Ejecutar este script en SQL Server Management Studio

USE DOC_DIGITALES;
GO

-- Agregar columna para el token de reseteo
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'firmas') AND name = 'reset_token')
BEGIN
    ALTER TABLE firmas ADD reset_token NVARCHAR(100) NULL;
    PRINT 'Columna reset_token agregada exitosamente';
END
ELSE
BEGIN
    PRINT 'Columna reset_token ya existe';
END
GO

-- Agregar columna para la fecha de expiración del token
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'firmas') AND name = 'reset_token_expira')
BEGIN
    ALTER TABLE firmas ADD reset_token_expira DATETIME NULL;
    PRINT 'Columna reset_token_expira agregada exitosamente';
END
ELSE
BEGIN
    PRINT 'Columna reset_token_expira ya existe';
END
GO

PRINT 'Script ejecutado correctamente';
