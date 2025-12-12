# Instrucciones para Habilitar Acceso sin Login

## Problema Resuelto
Anteriormente, la cajera necesitaba estar logueada en el sistema para aprobar órdenes de compra. Ahora puede acceder directamente desde el enlace del correo sin necesidad de login.

## Pasos de Implementación

### 1. Ejecutar Script SQL
Debes ejecutar el script `add_token_aprobacion.sql` en SQL Server Management Studio:

```sql
-- Abrir SQL Server Management Studio
-- Conectarse a la base de datos INTERAMERICANA_DOCUMENTOS
-- Abrir el archivo: add_token_aprobacion.sql
-- Ejecutar el script (F5)
```

Este script:
- Agrega el campo `OC_TOKEN_APROBACION` a la tabla `SIST_ORDEN_COMPRA`
- Crea un índice para mejorar el rendimiento de las búsquedas por token

### 2. Verificar la Implementación
Una vez ejecutado el script, el sistema funcionará de la siguiente manera:

1. **Al crear una nueva orden de compra:**
   - Se genera automáticamente un token único de 64 caracteres
   - Este token se guarda en el campo `OC_TOKEN_APROBACION`

2. **Al enviar el correo a la cajera:**
   - El enlace incluye el token: `...aprobacion/panel?id=123&token=abc123...`
   - Este enlace permite acceso directo sin login

3. **Al acceder desde el correo:**
   - La cajera hace clic en el enlace
   - El sistema valida el token automáticamente
   - Si el token es válido, muestra el panel de aprobación
   - La cajera puede aprobar o rechazar sin necesidad de login

### 3. Seguridad
- Cada token es único y aleatorio (64 caracteres hexadecimales)
- El token solo funciona para la orden específica
- No se puede reutilizar para otras órdenes
- Los tokens no expiran (puedes agregar expiración si lo deseas)

### 4. Compatibilidad
- Las órdenes antiguas (sin token) seguirán requiriendo login
- Las nuevas órdenes (con token) permitirán acceso directo
- El sistema soporta ambos métodos de acceso

## Notas Importantes
- **IMPORTANTE:** Debes ejecutar el script SQL antes de crear nuevas órdenes
- Las órdenes creadas antes de ejecutar el script no tendrán token
- Para agregar tokens a órdenes existentes, puedes ejecutar un UPDATE manual

## Ejemplo de UPDATE para Órdenes Existentes (Opcional)
Si deseas agregar tokens a órdenes existentes que están PENDIENTES:

```sql
UPDATE SIST_ORDEN_COMPRA
SET OC_TOKEN_APROBACION = CONVERT(VARCHAR(64), HASHBYTES('SHA2_256', CAST(OC_ID AS VARCHAR) + CAST(GETDATE() AS VARCHAR)), 2)
WHERE OC_ESTADO_APROBACION = 'PENDIENTE' 
  AND OC_TOKEN_APROBACION IS NULL;
```

## Soporte
Si tienes algún problema con la implementación, verifica:
1. Que el script SQL se ejecutó correctamente
2. Que el campo `OC_TOKEN_APROBACION` existe en la tabla
3. Que las nuevas órdenes tienen token generado
4. Que el enlace del correo incluye el parámetro `&token=...`
