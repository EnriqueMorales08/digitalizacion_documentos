# Instrucciones de Actualización - Sistema de Aprobación

## Cambios Realizados

Se han corregido **3 problemas críticos** en el sistema de aprobación:

### 1. ✅ URL del Panel de Aprobación (404)
**Problema:** El link en el correo llevaba a una página 404.
**Solución:** Se agregó la ruta `/aprobacion/panel` en `config/routes.php`.

### 2. ✅ Envío de Correo al Asesor
**Problema:** No se enviaba correo al asesor cuando se aprobaba/rechazaba la orden.
**Solución:** 
- El correo se envía automáticamente al asesor usando el email de la sesión (`$_SESSION['usuario_email']`)
- El email se obtiene de la tabla `firmas.firma_mail` al hacer login

### 3. ✅ Badge y Botón Imprimir
**Problema:** El badge no cambiaba de PENDIENTE a APROBADO y el botón imprimir no se habilitaba.
**Solución:**
- El badge ahora muestra correctamente el estado (PENDIENTE/APROBADO/RECHAZADO)
- Cuando está APROBADO, se muestra el botón "🖨️ Imprimir Documentos"
- El botón redirige a la página de expedientes para imprimir

### 4. ✅ Datos Completos en Correos y Panel
**Mejora:** Se agregaron más datos en los correos y en el panel de aprobación:
- Chasis del vehículo
- Precio de venta
- Marca y modelo separados
- Mejor formato visual

---

## Archivos Modificados

1. **config/routes.php**
   - Agregado `require_once` para AprobacionController
   - Agregada ruta GET `/aprobacion/panel`

2. **app/views/aprobacion/panel.php**
   - Agregados estilos para botón imprimir
   - Agregada lógica para mostrar botón imprimir cuando está APROBADO
   - Agregados campos: Chasis y Precio de Venta
   - Corregida función `imprimirDocumentos()` para redirigir correctamente

3. **app/models/Document.php**
   - Modificado `enviarCorreoResponsable()` para incluir más datos (chasis, precio)
   - Modificado `enviarCorreoAsesor()` para usar email de la sesión
   - Mejorado formato HTML de los correos

4. **FLUJO_CORREOS_APROBACION.md** (NUEVO)
   - Documentación completa del flujo de correos

---

## Flujo de Trabajo Actualizado

### 1. Creación de Orden
- El asesor crea una orden de compra
- Se guarda el email del responsable en `OC_EMAIL_CENTRO_COSTO` (del JSON `centros_costo_backup.json`)
- **Se envía correo al RESPONSABLE** con:
  - Número de expediente
  - Cliente
  - Vehículo (marca, modelo, chasis)
  - Precio
  - Link al panel de aprobación

### 2. Aprobación/Rechazo
- El responsable recibe el correo y hace clic en el link
- Accede al panel de aprobación (ahora funciona correctamente)
- Ve todos los datos: expediente, cliente, vehículo, chasis, precio, etc.
- Aprueba o rechaza la orden
- **Automáticamente se envía correo al ASESOR** (email de `$_SESSION['usuario_email']`)

### 3. Después de Aprobar
- El badge cambia a "✅ APROBADO"
- Aparece el botón "🖨️ Imprimir Documentos"
- Al hacer clic, redirige a la página de expedientes

---

## Verificación

Para verificar que todo funciona correctamente:

1. ✅ Login como asesor
2. ✅ Crear una orden de compra nueva (seleccionar agencia y responsable)
3. ✅ Verificar que llegue el correo al responsable (email del JSON)
4. ✅ Hacer clic en el link del correo (debe abrir el panel, no 404)
5. ✅ Verificar que se vean todos los datos (chasis, precio, etc.)
6. ✅ Aprobar la orden
7. ✅ Verificar que el badge cambie a APROBADO
8. ✅ Verificar que aparezca el botón de imprimir
9. ✅ Verificar que llegue correo al asesor logueado

---

## Configuración de Correos

**TODOS los correos se envían desde:**
- **From:** `comunica@interamericana.shop`
- **From Name:** `Sistema de Digitalización Interamericana`
- **API:** `http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php`

**Correo al Responsable:**
- Email obtenido del JSON `centros_costo_backup.json`
- Se guarda en `OC_EMAIL_CENTRO_COSTO`

**Correo al Asesor:**
- Email obtenido de la sesión: `$_SESSION['usuario_email']`
- Proviene de la tabla `firmas.firma_mail`

---

## Notas Técnicas

- Los logs de correo se guardan en el error_log de PHP
- Revisar documentación completa en: `FLUJO_CORREOS_APROBACION.md`
- No se requiere ejecutar ningún script SQL adicional

---

## Soporte

Si tienes problemas:
1. Revisa los logs de PHP (`error_log`)
2. Verifica que el servidor de correo esté funcionando
3. Asegúrate de que la sesión del usuario tenga `usuario_email` guardado
4. Verifica que el JSON `centros_costo_backup.json` tenga los emails correctos

---

**Fecha de Actualización:** Octubre 2025
**Versión:** 1.1
