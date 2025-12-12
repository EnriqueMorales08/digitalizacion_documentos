# 🔑 Sistema de Recuperación de Contraseña

## ✅ IMPLEMENTACIÓN COMPLETA

Se ha implementado un sistema completo de recuperación de contraseña por email para el Sistema de Digitalización.

---

## 📋 PASO 1: EJECUTAR SCRIPT SQL

**IMPORTANTE:** Antes de usar el sistema, debes ejecutar el script SQL para agregar las columnas necesarias.

1. Abre **SQL Server Management Studio**
2. Conéctate a tu servidor de base de datos
3. Abre el archivo: `database/alter_firmas_reset_password.sql`
4. Ejecuta el script (F5)

Este script agregará 2 columnas a la tabla `firmas`:
- `reset_token` (NVARCHAR(100)) - Token único para recuperación
- `reset_token_expira` (DATETIME) - Fecha de expiración del token

---

## 🚀 CÓMO FUNCIONA

### **PASO 1: Usuario olvida su contraseña**
1. En el login, hace clic en **"¿Olvidaste tu usuario o contraseña?"**
2. Ingresa su **email registrado**
3. Hace clic en "Enviar Correo de Recuperación"

### **PASO 2: Sistema envía email**
- El sistema genera un **token único** de 64 caracteres
- Guarda el token en la tabla `firmas` con expiración de 1 hora
- Envía un correo al usuario con:
  - Su **nombre de usuario** (por si lo olvidó)
  - Un **enlace único** para restablecer la contraseña

### **PASO 3: Usuario recibe el correo**
El correo contiene:
```
Hola [Nombre],

Tu usuario es: [usuario]

[Botón: Restablecer Contraseña]

⚠️ Este enlace expirará en 1 hora
```

### **PASO 4: Usuario crea nueva contraseña**
1. Hace clic en el enlace del correo
2. Ingresa su **nueva contraseña** (mínimo 6 caracteres)
3. Confirma la contraseña
4. El sistema actualiza la contraseña en la tabla `firmas`
5. El token se elimina (ya no se puede usar)

### **PASO 5: Usuario inicia sesión**
- Regresa al login
- Ingresa con su nueva contraseña
- ¡Listo! ✅

---

## 📁 ARCHIVOS CREADOS/MODIFICADOS

### **Archivos Nuevos:**
1. `database/alter_firmas_reset_password.sql` - Script SQL
2. `app/views/auth/forgot-password.php` - Formulario de recuperación
3. `app/views/auth/reset-password.php` - Formulario de nueva contraseña

### **Archivos Modificados:**
1. `app/views/auth/login.php` - Agregado link "¿Olvidaste tu usuario o contraseña?"
2. `app/controllers/AuthController.php` - Agregados 5 métodos nuevos:
   - `showForgotPassword()` - Mostrar formulario de recuperación
   - `requestReset()` - Procesar solicitud y enviar email
   - `enviarCorreoRecuperacion()` - Enviar correo con token
   - `showResetPassword()` - Mostrar formulario de nueva contraseña
   - `resetPassword()` - Actualizar contraseña en BD
3. `config/routes.php` - Agregadas 4 rutas nuevas

---

## 🔒 SEGURIDAD

✅ **Token único:** Cada solicitud genera un token aleatorio de 64 caracteres
✅ **Expiración:** Los tokens expiran en 1 hora automáticamente
✅ **Un solo uso:** El token se elimina después de usarse
✅ **Validación de email:** Solo se envía si el email existe en la BD
✅ **Contraseña mínima:** Requiere al menos 6 caracteres

---

## 📧 CONFIGURACIÓN DE EMAIL

El sistema usa la misma API de correo que ya tienes configurada:
- **URL:** `http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php`
- **Método:** POST con JSON
- **Campos:** email, subject, body

No necesitas configurar nada adicional. ✅

---

## 🧪 PRUEBAS

### **Probar el flujo completo:**

1. **Ir al login:**
   - URL: `http://190.238.78.104:3800/digitalizacion-documentos/auth/login`

2. **Hacer clic en "¿Olvidaste tu usuario o contraseña?"**

3. **Ingresar un email registrado:**
   - Ejemplo: `evegas@interamericananorte.com`

4. **Revisar el correo:**
   - Verificar que llegó el email
   - Verificar que contiene el usuario
   - Verificar que el link funciona

5. **Hacer clic en el link del correo:**
   - Debe abrir la página de reseteo

6. **Crear nueva contraseña:**
   - Ingresar contraseña nueva (mínimo 6 caracteres)
   - Confirmar contraseña
   - Hacer clic en "Restablecer Contraseña"

7. **Iniciar sesión:**
   - Regresar al login
   - Ingresar con la nueva contraseña
   - Verificar que funciona ✅

---

## ⚠️ NOTAS IMPORTANTES

1. **Ejecuta el script SQL primero** - Sin las columnas `reset_token` y `reset_token_expira`, el sistema no funcionará.

2. **Los tokens expiran en 1 hora** - Si el usuario no usa el link en 1 hora, debe solicitar uno nuevo.

3. **Un token por usuario** - Si el usuario solicita recuperación varias veces, solo el último token será válido.

4. **Email debe estar registrado** - El email debe existir en la columna `firma_mail` de la tabla `firmas`.

5. **Contraseñas sin encriptar** - Actualmente las contraseñas se guardan en texto plano. Se recomienda implementar encriptación en el futuro.

---

## 🎨 DISEÑO

El sistema tiene un diseño moderno y profesional:
- ✅ Responsive (funciona en móviles)
- ✅ Animaciones suaves
- ✅ Mensajes de error/éxito claros
- ✅ Loading spinners
- ✅ Validaciones en tiempo real

---

## 📞 SOPORTE

Si tienes algún problema:
1. Verifica que ejecutaste el script SQL
2. Verifica que el email existe en la tabla `firmas`
3. Revisa los logs de PHP para errores
4. Verifica que la API de correo esté funcionando

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [x] Script SQL creado
- [x] Formulario de recuperación creado
- [x] Formulario de reseteo creado
- [x] Controlador actualizado
- [x] Rutas agregadas
- [x] Login actualizado con link
- [x] Sistema de envío de correos integrado
- [x] Validaciones implementadas
- [x] Seguridad implementada

---

**¡Sistema listo para usar!** 🎉

Fecha de implementación: 19 de Noviembre, 2025
