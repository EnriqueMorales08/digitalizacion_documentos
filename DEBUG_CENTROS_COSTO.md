# 🔍 Guía de Depuración - Centros de Costo

## Problema
Los selects de Agencia, Responsable y Centro de Costo no cargan datos.

---

## 🧪 Archivos de Prueba Creados

### 1. **test_api.php**
Prueba directa de la API de Google Sheets.

**Ejecutar:**
```
http://localhost/digitalizacion-documentos/test_api.php
```

**Qué verifica:**
- ✅ Conexión a la API de Google Sheets
- ✅ Decodificación del JSON
- ✅ Extracción de agencias únicas
- ✅ Muestra los primeros 3 registros

**Resultado esperado:**
```json
[
    "CHICLAYO",
    "PIURA",
    "TRUJILLO"
]
```

---

### 2. **test_endpoint.php**
Prueba del modelo y métodos de PHP.

**Ejecutar:**
```
http://localhost/digitalizacion-documentos/test_endpoint.php
```

**Qué verifica:**
- ✅ Método `getCentrosCosto()`
- ✅ Método `getAgencias()`
- ✅ Método `getNombresPorAgencia()`
- ✅ Muestra lista completa de agencias y nombres

---

## 🔧 Pasos de Depuración

### **Paso 1: Verificar API de Google Sheets**

1. Abre en el navegador:
```
http://localhost/digitalizacion-documentos/test_api.php
```

2. **Si ves error "No se pudo obtener datos":**
   - El servidor no tiene acceso a internet
   - Verifica firewall o proxy
   - Prueba abrir la URL directamente en el navegador:
     ```
     https://opensheet.elk.sh/155IT8et2XYhMK6bkr7OJBtCziHS6X9Ia_6Q99Gm0WAk/Hoja%201
     ```

3. **Si ves datos correctamente:**
   - ✅ La API funciona
   - Continúa al Paso 2

---

### **Paso 2: Verificar Endpoints PHP**

1. Abre en el navegador:
```
http://localhost/digitalizacion-documentos/test_endpoint.php
```

2. **Si ves error:**
   - Revisa los logs de PHP
   - Verifica que la clase `Document` esté correctamente cargada

3. **Si ves las agencias:**
   - ✅ El modelo funciona
   - Continúa al Paso 3

---

### **Paso 3: Verificar Ruta del Controlador**

1. Abre en el navegador:
```
http://localhost/digitalizacion-documentos/documents/get-agencias
```

2. **Resultado esperado:**
```json
["CHICLAYO","PIURA","TRUJILLO"]
```

3. **Si ves error 404:**
   - La ruta no está registrada correctamente
   - Verifica `config/routes.php`

4. **Si ves error de sesión:**
   - Primero haz login en:
     ```
     http://localhost/digitalizacion-documentos/auth/login
     ```
   - Usuario: `evega`
   - Password: `73885481`
   - Luego vuelve a probar el endpoint

---

### **Paso 4: Verificar JavaScript en el Navegador**

1. Abre la orden de compra:
```
http://localhost/digitalizacion-documentos/documents/show?id=orden-compra
```

2. Abre la **Consola del Navegador** (F12)

3. Busca estos mensajes:
```
🔄 Inicializando centros de costo...
📡 Llamando a: /digitalizacion-documentos/documents/get-agencias
📥 Respuesta recibida: 200
✅ Agencias recibidas: [...]
✅ Agencias cargadas en el select
```

4. **Si ves error:**
   - Anota el mensaje de error
   - Verifica la URL que se está llamando
   - Verifica que el endpoint responda correctamente

---

## 🐛 Errores Comunes y Soluciones

### **Error: "Failed to fetch"**
**Causa:** El endpoint no responde o hay error de CORS.

**Solución:**
1. Verifica que el servidor Apache esté corriendo
2. Prueba el endpoint directamente en el navegador
3. Revisa los logs de Apache

---

### **Error: "HTTP error! status: 302"**
**Causa:** El sistema está redirigiendo (probablemente por falta de login).

**Solución:**
1. Haz login primero:
   ```
   http://localhost/digitalizacion-documentos/auth/login
   ```
2. Luego vuelve a la orden de compra

---

### **Error: "No se recibieron agencias"**
**Causa:** La API de Google Sheets no devuelve datos.

**Solución:**
1. Ejecuta `test_api.php` para verificar
2. Si la API no funciona, verifica:
   - Conexión a internet del servidor
   - Firewall
   - URL de la API

---

### **Error: Array vacío []**
**Causa:** Los datos de la API no tienen la estructura esperada.

**Solución:**
1. Ejecuta `test_api.php` y revisa la estructura
2. Verifica que las columnas se llamen exactamente:
   - `AGENCIA`
   - `NOMBRE`
   - `CENTRO DE COSTO`
   - `NOMBRE CC`
   - `EMAIL`

---

## 📊 Estructura Esperada de la API

```json
[
  {
    "AGENCIA": "CHICLAYO",
    "NOMBRE": "NANCY VILCA BENAVIDES",
    "CENTRO DE COSTO": "02490",
    "NOMBRE CC": "VTA VEH EIA",
    "EMAIL": "nvilca@interamericananorte.com"
  },
  {
    "AGENCIA": "PIURA",
    "NOMBRE": "JUAN PEREZ",
    "CENTRO DE COSTO": "02491",
    "NOMBRE CC": "VTA VEH PIURA",
    "EMAIL": "jperez@interamericananorte.com"
  }
]
```

---

## ✅ Checklist de Verificación

- [ ] Apache está corriendo
- [ ] PHP está funcionando
- [ ] El servidor tiene acceso a internet
- [ ] La URL de la API de Google Sheets es correcta
- [ ] El usuario está logueado
- [ ] La ruta `/documents/get-agencias` responde
- [ ] La consola del navegador muestra los logs
- [ ] Los selects tienen los IDs correctos: `agencia`, `nombre_responsable`, `centro_costo`

---

## 🆘 Si Nada Funciona

1. **Verifica los logs de PHP:**
   ```
   C:\xampp\apache\logs\error.log
   ```

2. **Verifica los logs de Apache:**
   ```
   C:\xampp\apache\logs\access.log
   ```

3. **Habilita errores de PHP:**
   En `php.ini`:
   ```ini
   display_errors = On
   error_reporting = E_ALL
   ```

4. **Reinicia Apache:**
   ```
   Abre XAMPP Control Panel
   Stop Apache
   Start Apache
   ```

---

## 📞 Información de Contacto

Si el problema persiste, proporciona:
1. Resultado de `test_api.php`
2. Resultado de `test_endpoint.php`
3. Captura de la consola del navegador (F12)
4. Logs de error de PHP/Apache
