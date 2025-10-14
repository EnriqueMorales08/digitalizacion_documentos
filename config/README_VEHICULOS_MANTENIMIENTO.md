# Configuración de Datos de Mantenimiento de Vehículos

## Archivo: `vehiculos_mantenimiento.json`

Este archivo contiene los datos de garantía, periodicidad de mantenimiento y primer ingreso para cada modelo de vehículo.

## Estructura del JSON

```json
{
  "MARCA": "Nombre de la marca en mayúsculas",
  "MODELO": "Nombre del modelo",
  "GARANTIA": "Texto de garantía (ej: 5 años o 100 mil km)",
  "1 INGRESO": "Primer ingreso (ej: 5,000 o 6 meses)",
  "PERIODICIDAD": "Kilometraje de periodicidad (ej: 5,000)"
}
```

## Formateo Automático

El sistema formatea automáticamente los datos de la siguiente manera:

### 1. GARANTIA
- **Entrada:** `"5 años o 100 mil km"`
- **Salida en formulario:** `"5 años o 100 mil km, lo que pase primero"`

### 2. PRIMER INGRESO (1 INGRESO)
- **Entrada:** `"5,000 o 6 meses"`
- **Salida en formulario:** `"5,000 km o 6 meses"`
- Si solo es un número, se agrega " km" al final

### 3. PERIODICIDAD
- **Entrada:** `"5,000"`
- **Salida en formulario:** `"cada 5,000 km"`

## Cómo Agregar un Nuevo Vehículo

1. Abre el archivo `vehiculos_mantenimiento.json`
2. Agrega un nuevo objeto al array con la siguiente estructura:

```json
{
  "MARCA": "CHERY",
  "MODELO": "TIGGO 8 PRO",
  "GARANTIA": "7 años o 150 mil km",
  "1 INGRESO": "10,000 o 12 meses",
  "PERIODICIDAD": "10,000"
}
```

3. Asegúrate de:
   - Usar MAYÚSCULAS para la marca
   - Mantener el formato de comas en los números (5,000 no 5000)
   - No olvidar la coma al final del objeto (excepto el último)

## Ejemplo Completo

```json
[
  {
    "MARCA": "CHERY",
    "MODELO": "ARRIZO 5",
    "GARANTIA": "5 años o 100 mil km",
    "1 INGRESO": "5,000 o 6 meses",
    "PERIODICIDAD": "5,000"
  },
  {
    "MARCA": "TOYOTA",
    "MODELO": "COROLLA",
    "GARANTIA": "3 años o 100 mil km",
    "1 INGRESO": "10,000 o 12 meses",
    "PERIODICIDAD": "10,000"
  }
]
```

## Funcionamiento en el Formulario

Cuando el usuario:
1. Ingresa un **chasis** → Se autocompleta marca y modelo → Se buscan automáticamente los datos de mantenimiento
2. Ingresa **marca y modelo manualmente** → Al salir del campo (blur), se buscan los datos de mantenimiento

Los campos que se autocompletarán son:
- **Período Garantía**
- **Periodicidad de mantenimientos**
- **Primer mantenimiento**

## Pruebas

Puedes probar el sistema accediendo a:
- `/digitalizacion-documentos/test_mantenimiento.php` - Ver todos los datos formateados
- `/digitalizacion-documentos/documents/show?id=orden-compra` - Probar en el formulario real
