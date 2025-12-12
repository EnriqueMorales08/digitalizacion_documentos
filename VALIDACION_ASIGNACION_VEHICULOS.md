# 🚗 Validación de Asignación de Vehículos

## Fecha de Implementación
30 de Octubre de 2025

---

## 📋 Descripción

Se ha implementado un sistema de validación que verifica si el vehículo (chasis) que el asesor está intentando usar en una orden de compra está asignado a su nombre en la base de datos de Stock.

---

## 🎯 Objetivo

Evitar que un asesor cree órdenes de compra con vehículos que no le han sido asignados, asegurando que cada vehículo sea vendido por el asesor correcto.

---

## 🔄 Flujo de Funcionamiento

```
┌─────────────────────────────────────────────────────────────┐
│  1. Asesor ingresa el CHASIS del vehículo                   │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│  2. Sistema consulta tabla STOCK                             │
│     - Busca el campo STO_VENDEDOR                            │
│     - Compara con el nombre del usuario logueado             │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
                    ┌───────┴───────┐
                    │               │
                    ▼               ▼
        ┌───────────────────┐   ┌──────────────────┐
        │  COINCIDE         │   │  NO COINCIDE     │
        │  (Es su vehículo) │   │  (Otro asesor)   │
        └───────────────────┘   └──────────────────┘
                    │                       │
                    ▼                       ▼
        ┌───────────────────┐   ┌──────────────────────────┐
        │  ✅ Continuar     │   │  ⚠️ Mostrar mensaje:     │
        │  con el proceso   │   │  "Este vehículo está     │
        └───────────────────┘   │   asignado a: [NOMBRE]"  │
                                │  "¿Es usted?"            │
                                └──────────────────────────┘
                                            │
                                    ┌───────┴───────┐
                                    │               │
                                    ▼               ▼
                            ┌───────────┐   ┌──────────────┐
                            │  SÍ       │   │  NO          │
                            └───────────┘   └──────────────┘
                                    │               │
                                    ▼               ▼
                            ┌───────────┐   ┌──────────────────────┐
                            │ Continuar │   │ ❌ Bloquear proceso  │
                            └───────────┘   │ Mensaje: "Solicitar  │
                                            │ a cajera asignación" │
                                            └──────────────────────┘
```

---

## 🔧 Implementación Técnica

### Archivos Modificados

#### 1. `app/models/Document.php`

**Cambios:**
- Se agregó el campo `STO_VENDEDOR` a la consulta de búsqueda de vehículos
- Se creó la función `compararNombres()` para comparar nombres en diferentes formatos
- Se creó la función `validarAsignacionVehiculo()` para validar la asignación

**Funciones agregadas:**

```php
// Compara nombres manejando diferentes formatos
private function compararNombres($nombre1, $nombre2)

// Valida si el vehículo está asignado al asesor
public function validarAsignacionVehiculo($chasis)
```

#### 2. `app/controllers/DocumentController.php`

**Cambios:**
- Se agregó el método `validarAsignacionVehiculo()` como endpoint

#### 3. `config/routes.php`

**Cambios:**
- Se agregó la ruta `/documents/validar-asignacion-vehiculo`

#### 4. `app/views/documents/layouts/orden-compra.php`

**Cambios:**
- Se modificó la función `autocompletarVehiculo()` en JavaScript
- Se agregó validación antes de autocompletar los datos del vehículo

---

## 📊 Comparación de Nombres

### Problema
Los nombres en la tabla `STOCK` (campo `STO_VENDEDOR`) están en formato:
```
APELLIDOS NOMBRES
Ejemplo: ALVA FACHO JULIO JANFRANCO
```

Los nombres en la sesión están en formato:
```
NOMBRES APELLIDOS
Ejemplo: Julio Janfranco Alva Facho
```

### Solución
La función `compararNombres()` normaliza ambos nombres:
1. Convierte a mayúsculas
2. Divide en palabras
3. Ordena alfabéticamente
4. Compara las palabras ordenadas

**Ejemplo:**
```php
"ALVA FACHO JULIO JANFRANCO" → ["ALVA", "FACHO", "JANFRANCO", "JULIO"]
"Julio Janfranco Alva Facho" → ["ALVA", "FACHO", "JANFRANCO", "JULIO"]
                                 ✅ COINCIDEN
```

---

## 🎨 Interfaz de Usuario

### Escenario 1: Vehículo Asignado Correctamente
- El asesor ingresa un chasis que le pertenece
- El sistema autocompleta los datos normalmente
- No se muestra ningún mensaje adicional

### Escenario 2: Vehículo Asignado a Otro Asesor
1. **Primer mensaje (confirm):**
   ```
   ⚠️ Este vehículo está asignado a: ALVA FACHO JULIO JANFRANCO
   
   ¿Es usted esta persona?
   
   [Aceptar] [Cancelar]
   ```

2. **Si el usuario hace clic en "Aceptar":**
   - El sistema continúa con el autocompletado
   - Se asume que es un error de formato de nombre

3. **Si el usuario hace clic en "Cancelar":**
   - Se muestra un segundo mensaje (alert):
   ```
   ❌ No puede continuar con este vehículo.
   
   Por favor, solicite a la cajera la asignación del vehículo.
   ```
   - El campo de chasis se limpia
   - El cursor vuelve al campo de chasis

### Escenario 3: Vehículo Sin Asignación
- Si el campo `STO_VENDEDOR` está vacío
- El sistema permite continuar sin restricciones
- Cualquier asesor puede usar ese vehículo

---

## 🔒 Seguridad

✅ **Validación en el servidor**: La validación se hace en PHP, no solo en JavaScript  
✅ **Comparación inteligente**: Maneja diferentes formatos de nombres  
✅ **No bloquea completamente**: Permite continuar si el asesor confirma  
✅ **Mensaje claro**: Indica exactamente a quién está asignado el vehículo  

---

## 🧪 Pruebas

### Caso 1: Vehículo Propio
**Pasos:**
1. Iniciar sesión como asesor
2. Ingresar un chasis asignado a ese asesor
3. Salir del campo (blur)

**Resultado Esperado:**
- ✅ Autocompleta sin mensajes
- ✅ Todos los campos se llenan correctamente

### Caso 2: Vehículo de Otro Asesor (Confirmar SÍ)
**Pasos:**
1. Iniciar sesión como asesor A
2. Ingresar un chasis asignado al asesor B
3. Salir del campo (blur)
4. Hacer clic en "Aceptar" en el mensaje de confirmación

**Resultado Esperado:**
- ⚠️ Muestra mensaje con nombre del asesor B
- ✅ Al confirmar, autocompleta normalmente

### Caso 3: Vehículo de Otro Asesor (Confirmar NO)
**Pasos:**
1. Iniciar sesión como asesor A
2. Ingresar un chasis asignado al asesor B
3. Salir del campo (blur)
4. Hacer clic en "Cancelar" en el mensaje de confirmación

**Resultado Esperado:**
- ⚠️ Muestra mensaje con nombre del asesor B
- ❌ Muestra mensaje de bloqueo
- 🔄 Limpia el campo de chasis
- 🎯 Vuelve el foco al campo de chasis

### Caso 4: Vehículo Sin Asignación
**Pasos:**
1. Iniciar sesión como cualquier asesor
2. Ingresar un chasis sin vendedor asignado (STO_VENDEDOR vacío)
3. Salir del campo (blur)

**Resultado Esperado:**
- ✅ Autocompleta sin mensajes
- ✅ Permite continuar normalmente

---

## 📝 Consultas SQL Útiles

### Ver vehículos y sus asignaciones
```sql
USE stock
GO

SELECT 
    STO_CHASIS,
    STO_MARCA,
    STO_MODELO,
    STO_VENDEDOR,
    STO_AFAB
FROM STOCK
WHERE STO_VENDEDOR IS NOT NULL
ORDER BY STO_VENDEDOR
```

### Ver vehículos sin asignación
```sql
USE stock
GO

SELECT 
    STO_CHASIS,
    STO_MARCA,
    STO_MODELO
FROM STOCK
WHERE STO_VENDEDOR IS NULL OR STO_VENDEDOR = ''
```

### Asignar un vehículo a un asesor
```sql
USE stock
GO

UPDATE STOCK
SET STO_VENDEDOR = 'APELLIDOS NOMBRES'
WHERE STO_CHASIS = 'CHASIS_DEL_VEHICULO'
```

---

## ⚙️ Configuración

### Requisitos
1. La tabla `STOCK` debe tener el campo `STO_VENDEDOR`
2. Los nombres en `STO_VENDEDOR` deben estar en formato: APELLIDOS NOMBRES
3. El usuario debe tener `usuario_nombre_completo` en la sesión

### Variables de Sesión Requeridas
```php
$_SESSION['usuario_nombre_completo'] // Ejemplo: "Julio Janfranco Alva Facho"
```

---

## 🔄 Integración con Sistema de Roles

Esta validación funciona en conjunto con el sistema de roles:
- **Usuarios (USER)**: Validación activa, solo pueden usar vehículos asignados a ellos
- **Administradores (ADMIN)**: Validación activa, pero pueden confirmar y continuar con cualquier vehículo

---

## 📞 Soporte

### Problemas Comunes

**P: El sistema no reconoce mi nombre**
R: Verifica que el formato en `STO_VENDEDOR` coincida con tu nombre completo. La comparación es inteligente pero requiere que todas las palabras estén presentes.

**P: ¿Puedo usar un vehículo sin asignación?**
R: Sí, los vehículos sin asignación pueden ser usados por cualquier asesor.

**P: ¿Qué hago si necesito usar un vehículo de otro asesor?**
R: Solicita a la cajera que reasigne el vehículo a tu nombre en la base de datos Stock.

**P: El mensaje aparece pero soy yo el asignado**
R: Puede ser un problema de formato de nombre. Haz clic en "Aceptar" para continuar. Reporta el caso para revisar el formato en la base de datos.

---

## 📚 Archivos Relacionados

- `SISTEMA_ROLES_IMPLEMENTADO.md` - Sistema de roles
- `README_ROLES.md` - Documentación general de roles

---

**Versión**: 1.0  
**Fecha**: 30 de Octubre de 2025  
**Estado**: ✅ Implementado y Funcional
