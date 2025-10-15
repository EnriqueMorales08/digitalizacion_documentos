# 🔧 Solución: Sesión se Cambia al Usar Firmas

## 🐛 Problema Identificado

Cuando el usuario logueado ingresaba credenciales para las **firmas digitales** (asesor, cliente, jefe), **la sesión se sobrescribía** con los datos de la persona que firmaba, cerrando efectivamente la sesión del usuario original.

### Escenario del Problema

```
1. Usuario A hace login → Sesión: usuario_email = "usuarioA@test.com"
   ↓
2. Usuario A crea orden de compra
   ↓
3. Usuario A ingresa credenciales de Usuario B para firma del asesor
   ↓
4. Sistema verifica firma de Usuario B
   ↓
5. ❌ Sistema SOBRESCRIBE la sesión → usuario_email = "usuarioB@test.com"
   ↓
6. ❌ Usuario A pierde su sesión y es redirigido al login
```

---

## 🔍 Causa Raíz

En el método `verificarFirma()` del archivo `Document.php`, después de verificar las credenciales de la firma, el código estaba **guardando los datos en la sesión**:

```php
// ❌ CÓDIGO INCORRECTO (líneas 504-508)
if ($row) {
    // Guardar datos del usuario en sesión
    $_SESSION['usuario_email'] = $row['firma_mail'];
    $_SESSION['usuario_nombre'] = $row['firma_nombre'];
    $_SESSION['usuario_apellido'] = $row['firma_apellido'];
    $_SESSION['usuario_nombre_completo'] = trim($row['firma_nombre'] . ' ' . $row['firma_apellido']);
    return 'http://190.238.78.104:3800' . $row['firma_data'];
}
```

Esto **sobrescribía la sesión del usuario logueado** con los datos de la persona que firmaba.

---

## ✅ Solución Implementada

**Eliminé las líneas que guardaban datos en la sesión**. El método `verificarFirma()` ahora **SOLO retorna la imagen de la firma**, sin modificar la sesión.

```php
// ✅ CÓDIGO CORRECTO
if ($row) {
    // NO guardar en sesión - solo retornar la firma
    // La sesión debe mantenerse con el usuario que hizo login
    return 'http://190.238.78.104:3800' . $row['firma_data'];
}
```

---

## 📋 Diferencia entre Login y Firmas

### Login (AuthController.php)
**Propósito:** Iniciar sesión del usuario en el sistema

**Qué hace:**
- Verifica credenciales
- **SÍ guarda en sesión** (correcto)
- Redirige al dashboard

```php
// ✅ CORRECTO - Guardar en sesión al hacer LOGIN
$_SESSION['usuario_email'] = $user['firma_mail'];
$_SESSION['usuario_nombre'] = $user['firma_nombre'];
$_SESSION['usuario_apellido'] = $user['firma_apellido'];
$_SESSION['usuario_nombre_completo'] = trim($user['firma_nombre'] . ' ' . $user['firma_apellido']);
```

### Firmas (Document.php → verificarFirma())
**Propósito:** Obtener imagen de firma digital para documentos

**Qué hace:**
- Verifica credenciales
- **NO guarda en sesión** (correcto)
- Solo retorna URL de la imagen de firma

```php
// ✅ CORRECTO - NO guardar en sesión al verificar FIRMAS
return 'http://190.238.78.104:3800' . $row['firma_data'];
```

---

## 🎯 Flujo Correcto Ahora

```
1. Usuario A hace login
   ↓
   Sesión: usuario_email = "usuarioA@test.com" ✅
   ↓
2. Usuario A crea orden de compra
   ↓
3. Usuario A ingresa credenciales de Usuario B para firma del asesor
   ↓
4. Sistema verifica firma de Usuario B
   ↓
5. ✅ Sistema RETORNA solo la imagen de firma
   ↓
6. ✅ Sesión se mantiene: usuario_email = "usuarioA@test.com"
   ↓
7. ✅ Usuario A continúa trabajando sin perder su sesión
```

---

## 📁 Archivo Modificado

**Archivo:** `app/models/Document.php`
**Método:** `verificarFirma()`
**Líneas modificadas:** 503-508

**Cambio:**
- ❌ Antes: Guardaba datos en `$_SESSION`
- ✅ Ahora: Solo retorna la URL de la firma

---

## 🔍 Cómo Verificar

### Prueba 1: Crear Orden con Firmas
1. Login con Usuario A (ej: `usuarioA@test.com`)
2. Crear orden de compra
3. Ingresar credenciales de Usuario B para firma del asesor
4. Ingresar credenciales de Usuario C para firma del cliente
5. Ingresar credenciales de Usuario D para firma del jefe
6. Guardar orden

**Resultado esperado:**
- ✅ Todas las firmas se guardan correctamente
- ✅ La sesión sigue siendo de Usuario A
- ✅ NO se cierra la sesión
- ✅ NO se redirige al login

### Prueba 2: Verificar Email en Orden
1. Después de crear la orden, verificar en la base de datos:

```sql
SELECT OC_USUARIO_EMAIL, OC_USUARIO_NOMBRE
FROM SIST_ORDEN_COMPRA
WHERE OC_ID = [ID_DE_LA_ORDEN];
```

**Resultado esperado:**
- ✅ `OC_USUARIO_EMAIL` = Email del Usuario A (quien hizo login)
- ✅ `OC_USUARIO_NOMBRE` = Nombre del Usuario A (quien hizo login)
- ❌ NO debe ser el email de Usuario B, C o D (quienes firmaron)

---

## ⚠️ Importante

**Las firmas digitales son SOLO para obtener imágenes de firma**, NO para cambiar la sesión del usuario.

**La sesión debe mantenerse SIEMPRE con el usuario que hizo login**, independientemente de quién firme los documentos.

---

## 🎯 Resumen

| Concepto | Login | Firmas |
|----------|-------|--------|
| **Propósito** | Iniciar sesión | Obtener imagen de firma |
| **Guardar en sesión** | ✅ SÍ | ❌ NO |
| **Archivo** | `AuthController.php` | `Document.php` |
| **Método** | `login()` | `verificarFirma()` |
| **Afecta sesión** | ✅ SÍ (correcto) | ❌ NO (correcto) |

---

**Fecha:** Octubre 2025  
**Versión:** 4.0 (Final)
