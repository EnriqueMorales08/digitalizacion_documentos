<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Compra - Interamericana</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --primary: #1e3a8a;
        --ink: #111827;
        --bg: #f8f9fa;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 10px;
        background-color: var(--bg);
        margin: 0;
        padding: 15px;
    }

    /* Bloque completo tipo carta */
    .page {
        width: 794px; /* Ancho A4 */
        margin: 0 auto;
        background: #fff;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
    }

    .form-container {
        width: 100%;
        max-width: 774px;
        border: 1px solid #000;
        background-color: white;
    }

    @media print {
        body {
            background: #fff;
        }

        .page {
            box-shadow: none;
        }
    }

    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px;
    }

    .header-left img {
        width: 210px;
    }

    .header-center {
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        flex: 1;
    }

    .header-right {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .header-field {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
    }

    .header-field label {
        margin-right: 5px;
    }

    .header-field input {
        border: 1px solid #000;
        background-color: #fdeee2;
        height: 16px;
        font-size: 10px;
        width: 100px;
    }

    input,
    select,
    textarea {
        border: none;
        background: #fdeee2;
        padding: 4px;
        font-size: 10px;
        width: 100%;
        box-sizing: border-box;
    }
    </style>
</head>

<body>
  <!-- Flecha de regreso -->
  <div style="position: fixed; top: 20px; left: 20px; z-index: 1000;">
    <a href="/digitalizacion-documentos/documents" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3); font-family: Arial, sans-serif; font-size: 14px; font-weight: 500; transition: all 0.3s ease;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Regresar
    </a>
  </div>

  <!-- Formulario que envuelve todo el documento -->
  <form method="POST" action="/digitalizacion-documentos/documents/procesar-orden-compra" style="margin: 0; padding: 0;" autocomplete="off" onsubmit="alert('Formulario enviado');">

    <div class="page">
        <div class="form-container" style="margin-bottom:5px">
        <!-- ENCABEZADO -->
        <div class="header">
            <div class="header-left">
                <img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" alt="Logo Interamericana" width="200">
            </div>
            <div class="header-center">
                SOLICITUD DE COMPRA
            </div>
            <div class="header-right">
                <div class="header-field">
                    <label>Nro. Expediente:</label>
                    <input type="text" name="OC_NUMERO_EXPEDIENTE">
                </div>
                <div class="header-field">
                    <label>Nro. Cotización:</label>
                    <input type="text" name="OC_NUMERO_COTIZACION">
                </div>
            </div>
        </div>

        <!-- FECHA / ASESOR -->
        <div style="display:flex; border-bottom:1px solid #000; justify-content:flex-end; align-items:center; gap:20px; margin-right:50px;">
            <label for="fecha_orden" style="background:#ffffff; font-weight:bold; padding:4px;">FECHA</label>
            <input type="date" id="fecha_orden" name="OC_FECHA_ORDEN" style="width:120px;">
            <div style="background:#ffffff; font-weight:bold; padding:4px;">ASESOR</div>
            <input type="text" id="asesor_venta" name="OC_ASESOR_VENTA" style="width:150px;">
        </div>
    </div>

    <!-- DATOS DEL CLIENTE -->
    <div style="display:flex; border:1px solid #000;width:774px;margin:0 auto;">
        <!-- Columna lateral -->
        <div
            style="writing-mode: vertical-lr; transform: rotate(180deg); background:#ffffff; font-weight:bold; text-align:center; padding:8px; border-left:1px solid #000;">
            Datos del Cliente
        </div>

        <!-- Sección derecha -->
        <div style="flex:1;">
            <!-- Fila 1 -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Comprador</div>
                <input type="text" name="OC_COMPRADOR_NOMBRE" style="width:150px;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">TIPO DOC</div>
                <select name="OC_COMPRADOR_TIPO_DOCUMENTO" style="width:100px;">
                    <option value="">-- Seleccione --</option>
                    <option value="dni">DNI</option>
                    <option value="pasaporte">PASAPORTE</option>
                    <option value="carnet">CARNET EXTRANJERIA</option>
                    <option value="ruc">RUC</option>
                </select>
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">NRO. DOC</div>
                <input type="text" name="OC_COMPRADOR_NUMERO_DOCUMENTO" style="width:150px;">
            </div>

            <!-- Fila 2 -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Tipo Doc. de venta</div>
                <select name="OC_TIPO_DOCUMENTO_VENTA" style="width:120px;">
                    <option value="">-- Seleccione --</option>
                    <option value="boleta">BOLETA DE VENTA</option>
                    <option value="factura">FACTURA DE VENTA</option>
                </select>
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; margin-left:80px;">Fuente
                    Contacto</div>
                <select name="OC_FUENTE_CONTACTO" style="width:160px;">
                    <option value="">-- Seleccione --</option>
                    <option value="digital_marca">Digital Marca</option>
                    <option value="digital_dealer">Digital Dealer</option>
                    <option value="trabajo_campo">Trabajo Campo / Campañas</option>
                    <option value="afluencia_piso">Afluencia Piso</option>
                    <option value="recomendado">Recomendado</option>
                    <option value="digital_agencias">Digital Agencias</option>
                    <option value="recurrente">Recurrente</option>
                </select>
            </div>

            <!-- Fila 3 -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Fecha de Nac.</div>
                <input type="date" name="OC_FECHA_NACIMIENTO" style="width:100px;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">Estado Civil</div>
                <select name="OC_ESTADO_CIVIL" style="width:100px;">
                    <option value="">-- Seleccione --</option>
                    <option value="soltero">SOLTERO</option>
                    <option value="casado">CASADO</option>
                    <option value="divorciado">DIVORCIADO</option>
                    <option value="separado">SEPARADO</option>
                    <option value="concubino">CONCUBINA(O)</option>
                    <option value="conviviente">CONVIVIENTE</option>
                </select>
                <div style="background:#ffffff; font-weight:bold; padding:4px; margin-left:5px;">Situación Laboral
                </div>
                <select name="OC_SITUACION_LABORAL" style="width:120px;">
                    <option value="">-- Seleccione --</option>
                    <option value="empleado">EMPLEADO</option>
                    <option value="independiente">INDEPENDIENTE</option>
                    <option value="negociante">NEGOCIANTE</option>
                    <option value="jubilado">JUBILADO</option>
                    <option value="dependiente">DEPENDIENTE</option>
                    <option value="desempleado">DESEMPLEADO</option>
                    <option value="otros">OTROS</option>
                </select>
            </div>

            <!-- Fila 4 -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Cónyuge</div>
                <input type="text" name="OC_CONYUGE_NOMBRE" style="width:130px;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">TIPO DOC</div>
                <select name="OC_CONYUGE_TIPO_DOCUMENTO" style="width:100px;">
                    <option value="">-- Seleccione --</option>
                    <option value="dni">DNI</option>
                    <option value="pasaporte">PASAPORTE</option>
                    <option value="carnet">CARNET EXTRANJERIA</option>
                </select>
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">NRO. DOC</div>
                <input type="text" name="OC_CONYUGE_NUMERO_DOCUMENTO" style="flex:1;">
            </div>

            <!-- Fila 5 -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Dirección</div>
                <input type="text" name="OC_DIRECCION_CLIENTE" style="width:385px;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px; margin-left:15px;">Teléfonos</div>
                <input type="text" name="OC_TELEFONOS_CLIENTE" style="width:130px;">
            </div>

            <!-- Fila 6 -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Email</div>
                <input type="email" name="OC_EMAIL_CLIENTE" style="width:470px;">
                <input type="text" name="OC_TELEFONO_ADICIONAL" style="width:150px;">
            </div>

            <!-- Fila 7 -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Ocupación</div>
                <input type="text" name="OC_OCUPACION_CLIENTE" style="flex:1;">
            </div>

            <!-- Hobbies y Fecha de Nac. -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Hobbies</div>
                <input type="text" name="OC_HOBBIES_CLIENTE" style="width:200px;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Fecha de Nac.</div>
                <input type="date" name="OC_FECHA_NACIMIENTO_NUEVO" style="width:150px;">
            </div>

            <!-- Tarjeta de propiedad -->
            <div style="display:flex; border-top:2px solid #000; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:180px;">Tarjeta de propiedad a
                    nombre de:</div>
                <div style="background:#fdeee2; flex:1; padding:4px; display:flex; gap:15px; align-items:center;">
                    <label><input type="radio" name="OC_TARJETA_PROPIEDAD" value="natural"> Persona natural</label>
                    <label><input type="radio" name="OC_TARJETA_PROPIEDAD" value="ruc"> P. Natural con RUC</label>
                    <label><input type="radio" name="OC_TARJETA_PROPIEDAD" value="juridica"> Persona Jurídica</label>
                </div>
            </div>

            <!-- Nombre / Razon Social -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Nombre / Razón Social</div>
                <input type="text" name="OC_PROPIETARIO_NOMBRE" class="no-adjust" style="width:280px;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:50px;">DNI</div>
                <input type="text" name="OC_PROPIETARIO_DNI" style="width:90px;">
            </div>

            <!-- Co-propietario -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Co-propietario / Cónyuge
                </div>
                <input type="text" name="OC_COPROPIETARIO_NOMBRE" class="no-adjust" style="width:280px;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:50px;">DNI</div>
                <input type="text" name="OC_COPROPIETARIO_DNI" style="width:90px;">
            </div>

            <!-- Representante legal -->
            <div style="display:flex;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Representante legal</div>
                <input type="text" name="OC_REPRESENTANTE_LEGAL" class="no-adjust" style="width:280px;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:50px;">DNI</div>
                <input type="text" name="OC_REPRESENTANTE_DNI" style="width:90px;">
            </div>
        </div>
    </div>

    <!-- VEHÍCULO -->
    <div style="display:flex; border:1px solid #000;width:774px;margin:10px auto 0;">
        <!-- Columna lateral -->
        <div
            style="writing-mode: vertical-lr; transform: rotate(180deg); background:#ffffff; font-weight:bold; text-align:center; padding:8px; border-left:1px solid #000;">
            Vehículo
        </div>

        <!-- Sección derecha -->
        <div style="flex:1;">
            <!-- Encabezados -->
            <div style="display:flex; border-bottom:1px solid #000; text-align:center; font-weight:bold;">
                <div style="flex:1; padding:4px;">Clase</div>
                <div style="flex:1; padding:4px;">Marca</div>
                <div style="flex:1; padding:4px;">Modelo</div>
                <div style="flex:1; padding:4px;">Versión</div>
                <div style="flex:1; padding:4px;">FSC / Código</div>
            </div>
            <!-- Inputs -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <input type="text" name="OC_VEHICULO_CLASE" style="flex:1;">
                <input type="text" name="OC_VEHICULO_MARCA" style="flex:1;">
                <input type="text" name="OC_VEHICULO_MODELO" style="flex:1;">
                <input type="text" name="OC_VEHICULO_VERSION" style="flex:1;">
                <input type="text" name="OC_VEHICULO_CODIGO_FSC" style="flex:1;">
            </div>

            <!-- Encabezados -->
            <div style="display:flex; border-bottom:1px solid #000; text-align:center; font-weight:bold;">
                <div style="flex:1; padding:4px;">Motor</div>
                <div style="flex:1; padding:4px;">Chasis</div>
                <div style="flex:1; padding:4px;">Color</div>
                <div style="flex:1; padding:4px;">Año Mod.</div>
            </div>
            <!-- Inputs -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <input type="text" name="OC_VEHICULO_MOTOR" style="flex:1;">
                <input type="text" name="OC_VEHICULO_CHASIS" style="flex:1;">
                <input type="text" name="OC_VEHICULO_COLOR" style="flex:1;">
                <input type="text" name="OC_VEHICULO_ANIO_MODELO" style="flex:1;">
            </div>

            <!-- Encabezados -->
            <div style="display:flex; border-bottom:1px solid #000; text-align:center; font-weight:bold;">
                <div style="flex:1; padding:4px;">Período Garantía</div>
                <div style="flex:1; padding:4px;">Periodicidad de mantenimientos</div>
                <div style="flex:1; padding:4px;">Primer mantenimiento</div>
            </div>
            <!-- Inputs -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <input type="text" name="OC_PERIODO_GARANTIA" style="flex:1;">
                <input type="text" name="OC_PERIODICIDAD_MANTENIMIENTO" style="flex:1;">
                <input type="text" name="OC_PRIMER_MANTENIMIENTO" style="flex:1;">
            </div>

            <!-- Tipo de uso -->
            <div style="display:flex; align-items:center; border-bottom:1px solid #000; padding:4px; font-weight:bold;">
                Tipo de uso del vehículo:
                <label style="margin-left:10px; font-weight:normal;">
                    <input type="radio" name="OC_USO_VEHICULO" value="particular"> Particular
                </label>
                <label style="margin-left:15px; font-weight:normal;">
                    <input type="radio" name="OC_USO_VEHICULO" value="servicio"> Servicio Público
                </label>
            </div>
        </div>
    </div>


    <!-- VALOR DE LA COMPRA / FORMA DE PAGO -->
    <!-- VALOR DE LA COMPRA / FORMA DE PAGO -->
    <div style="display:flex; border:1px solid #000; width:774px; margin:10px auto 0;">

        <!-- Columna izquierda -->
        <div style="width:50%; display:flex; border-right:1px solid #000;">
            <!-- Lateral -->
            <div
                style="writing-mode: vertical-lr; transform: rotate(180deg); background:#ffffff; font-weight:bold; text-align:center; padding:8px; border-left:1px solid #000;">
                Valor de la Compra
            </div>
            <!-- Contenido -->
            <div style="flex:1;">
                <!-- Forma de pago -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Forma de pago:</div>
                    <select name="OC_FORMA_PAGO" style="flex:1;">
                        <option>CONTADO</option>
                        <option>CRÉDITO</option>
                    </select>
                </div>

                <!-- Precio de venta -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Precio de venta</div>
                    <select name="OC_MONEDA_PRECIO_VENTA" style="width:70px; margin-right:5px;">
                        <option>US$</option>
                        <option>MN</option>
                    </select>
                    <input type="text" name="OC_PRECIO_VENTA" style="flex:1;">
                </div>

                <!-- Bono Financiamiento -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:110px;">Bono Financiamiento
                    </div>
                    <select name="OC_MONEDA_BONO_FINANCIAMIENTO" style="width:70px; margin-right:5px;">
                        <option>US$</option>
                        <option>MN</option>
                    </select>
                    <input type="text" name="OC_BONO_FINANCIAMIENTO" style="flex:1;">
                </div>

                <!-- Equipamiento adicional -->
                <div style="background:#ffffff; font-weight:bold; padding:4px;">Equipamiento adicional</div>
                <div style="border-bottom:1px solid #000; padding:4px;">
                    <div style="display:flex; margin-bottom:4px;">
                        <input type="text" name="OC_DESCRIPCION_EQUIPAMIENTO_1" placeholder="Descripción" style="flex:1; margin-right:5px;">
                        <select name="OC_MONEDA_EQUIPAMIENTO_1" style="width:70px; margin-right:5px;">
                            <option>US$</option>
                            <option>MN</option>
                        </select>
                        <input type="text" name="OC_EQUIPAMIENTO_ADICIONAL_1" style="flex:1;">
                    </div>
                    <div style="display:flex; margin-bottom:4px;">
                        <input type="text" name="OC_DESCRIPCION_EQUIPAMIENTO_2" placeholder="Descripción" style="flex:1; margin-right:5px;">
                        <select name="OC_MONEDA_EQUIPAMIENTO_2" style="width:70px; margin-right:5px;">
                            <option>US$</option>
                            <option>MN</option>
                        </select>
                        <input type="text" name="OC_EQUIPAMIENTO_ADICIONAL_2" style="flex:1;">
                    </div>
                    <div style="display:flex; margin-bottom:4px;">
                        <input type="text" name="OC_DESCRIPCION_EQUIPAMIENTO_3" placeholder="Descripción" style="flex:1; margin-right:5px;">
                        <select name="OC_MONEDA_EQUIPAMIENTO_3" style="width:70px; margin-right:5px;">
                            <option>US$</option>
                            <option>MN</option>
                        </select>
                        <input type="text" name="OC_EQUIPAMIENTO_ADICIONAL_3" style="flex:1;">
                    </div>
                    <div style="display:flex; margin-bottom:4px;">
                        <input type="text" name="OC_DESCRIPCION_EQUIPAMIENTO_4" placeholder="Descripción" style="flex:1; margin-right:5px;">
                        <select name="OC_MONEDA_EQUIPAMIENTO_4" style="width:70px; margin-right:5px;">
                            <option>US$</option>
                            <option>MN</option>
                        </select>
                        <input type="text" name="OC_EQUIPAMIENTO_ADICIONAL_4" style="flex:1;">
                    </div>
                    <div style="display:flex;">
                        <input type="text" name="OC_DESCRIPCION_EQUIPAMIENTO_5" placeholder="Descripción" style="flex:1; margin-right:5px;">
                        <select name="OC_MONEDA_EQUIPAMIENTO_5" style="width:70px; margin-right:5px;">
                            <option>US$</option>
                            <option>MN</option>
                        </select>
                        <input type="text" name="OC_EQUIPAMIENTO_ADICIONAL_5" style="flex:1;">
                    </div>
                </div>

                <!-- Total equipamiento -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Total Equipamiento
                    </div>
                    <input type="text" name="OC_TOTAL_EQUIPAMIENTO" style="flex:1;">
                </div>

                <!-- Precio compra total -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:130px;">Precio compra total
                    </div>
                    <select name="OC_MONEDA_PRECIO_TOTAL" style="width:70px; margin-right:5px;">
                        <option>US$</option>
                        <option>MN</option>
                    </select>
                    <input type="text" name="OC_PRECIO_TOTAL_COMPRA" style="flex:1;">
                </div>

                <!-- Tipo cambio -->
                <div style="display:flex; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:130px;">Tipo Cambio Ref. S/.
                    </div>
                    <input type="text" name="OC_TIPO_CAMBIO" style="width:80px; margin-right:5px;" placeholder="3.93">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:30px;">S/.</div>
                    <input type="text" name="OC_TIPO_CAMBIO_SOL" style="flex:1;" placeholder="76,635.00">
                </div>
            </div>
        </div>

        <!-- Columna derecha -->
        <div style="width:50%; display:flex;">
            <!-- Lateral -->
            <div
                style="writing-mode: vertical-lr; transform: rotate(180deg); background:#ffffff; font-weight:bold; text-align:center; padding:8px; border-left:1px solid #000;">
                Forma de Pago
            </div>
            <!-- Contenido -->
            <div style="flex:1;">
                <!-- Pago a cuenta -->
                <div style="display:flex;  align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Pago a cuenta</div>
                    <select name="OC_MONEDA_PAGO_CUENTA" style="width:70px; margin-right:5px;">
                        <option>US$</option>
                        <option>MN</option>
                    </select>
                    <input type="text" name="OC_PAGO_CUENTA" style="flex:1;border-bottom:1px solid #000;" autocomplete="off">
                </div>
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; height:20px;"></div>
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; height:20px;"></div>

                <!-- Nro. Operación -->
                <div style="display:flex; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Nro. Operación</div>
                    <input type="text" name="OC_NUMERO_OPERACION" style="flex:1;border-bottom:1px solid #000" autocomplete="off">

                </div>
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;"></div>
                    <input type="text" name="OC_NUMERO_OPERACION_2" style="flex:1;" autocomplete="off">

                </div>



                <!-- Entidad financiera -->
                <div style="display:flex; align-items:center;border-bottom:1px solid #000">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:130px;">Entidad financiera de
                        abono</div>
                    <input type="text" name="OC_ENTIDAD_FINANCIERA" style="flex:1;" placeholder="BCP" autocomplete="off">
                </div>

                <!-- Saldo -->
                <div style="display:flex; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Saldo (3-4)</div>
                    <select name="OC_MONEDA_SALDO" style="width:70px; margin-right:5px;">
                        <option>US$</option>
                        <option>MN</option>
                    </select>
                    <input type="text" name="OC_SALDO_PENDIENTE" style="flex:1; border-bottom:1px solid #000" autocomplete="off">
                </div>

                <!-- Banco -->
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; height:20px;"></div>
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; height:20px;"></div>


                <div style="display:flex; ">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">Banco:</div>
                    <input type="text" name="OC_BANCO_ABONO" style="flex:1;border-bottom:1px solid #000;" autocomplete="off">
                </div>

                <!-- Sectorista -->
                <div style="display:flex;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">Sectorista:</div>
                    <input type="text" name="OC_SECTORISTA_BANCO" style="flex:1;border-bottom:1px solid #000;" autocomplete="off">
                </div>

                <!-- Oficina -->
                <div style="display:flex;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">Oficina:</div>
                    <input type="text" name="OC_OFICINA_BANCO" style="flex:1;border-bottom:1px solid #000;" autocomplete="off">
                </div>

                <!-- Teléf. Sector -->
                <div style="display:flex;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:90px;">Teléf. Sector:</div>
                    <input type="text" name="OC_TELEFONO_SECTORISTA" style="flex:1;border-bottom:1px solid #000;" autocomplete="off">
                </div>
            </div>
        </div>
    </div>

    <!-- OBSEQUIOS / CORTESÍAS / CAMPAÑAS -->
 <div style="border:1px solid #000; width:774px; margin:10px auto 0;">
    <div style="background:#ffffff; font-weight:bold; padding:4px; border-bottom:1px solid #000; text-align:center;">
        Obsequios / Cortesías / Campañas
    </div>
    <textarea name="OC_OBSEQUIOS_CORTESIAS" style="width:100%; height:60px; border:none; background:#fdeee2;"></textarea>
</div>

<!-- FIRMAS -->
<div style="display:flex; justify-content:space-between; width:774px; margin:15px auto 5px;">
    <!-- Asesor de venta -->
    <div style="flex:1; border:1px solid #000; margin-right:5px; display:flex; flex-direction:column; justify-content:space-between; height:70px;">
        <div></div>
        <div style="background:#ccc; text-align:center; font-weight:bold; padding:2px; font-size:10px;">ASESOR DE VENTA</div>
    </div>

    <!-- Firma cliente + Huella digital -->
    <div style="flex:2.8; border:1px solid #000; margin-right:5px; display:flex; flex-direction:column; justify-content:space-between; height:70px;">
        <div style="flex:1;"></div>
        <div style="display:flex;">
            <div style="flex:1; background:#ccc; text-align:center; font-weight:bold; padding:2px; font-size:10px; border-right:1px solid #000;">FIRMA CLIENTE</div>
            <div style="width:150px; background:#ccc; text-align:center; font-weight:bold; padding:1px; font-size:10px;">HUELLA DIGITAL</div>
        </div>
    </div>
</div>

<div style="display:flex; justify-content:space-between; width:774px; margin:0 auto 10px;">
    <!-- Jefe de tienda -->
    <div style="flex:1; border:1px solid #000; margin-right:5px; display:flex; flex-direction:column; justify-content:space-between; height:70px;">
        <div></div>
        <div style="background:#ccc; text-align:center; font-weight:bold; padding:2px; font-size:10px;">JEFE DE TIENDA</div>
    </div>

    <!-- Visto ADV -->
    <div style="flex:1; border:1px solid #000; display:flex; flex-direction:column; justify-content:space-between; height:70px;">
        <div></div>
        <div style="background:#ccc; text-align:center; font-weight:bold; padding:2px; font-size:10px;">VISTO ADV°</div>
    </div>
</div>


<!-- IMPORTANTE -->
<div style="width:774px; margin:0 auto; padding:8px; font-size:10px; line-height:1.4; background:#ffffff;">
    <p style="font-weight:bold;">IMPORTANTE:</p>
    <ol>
        <li>Esta solicitud está sujeta a la aprobación de INTERAMERICANA NORTE SAC.</li>
        <li>Cualquier pedido de equipamiento adicional a las características de la presente solicitud será por cuenta y costo del cliente.</li>
        <li>Luego de la entrega de los pagos a cuenta cualquier devolución estará afecta al 7% o $100 como mínimo de gastos administrativos. El monto abonado por el cliente será entregado en los quince (15) días útiles después de presentada la Solicitud de Devolución y en cheque no negociable a nombre del titular de la reserva.</li>
        <li>El trámite de placas de rodaje y tarjeta de propiedad es una cortesía que otorgamos a nuestros clientes. Dicho trámite se encuentra sujeto a los criterios de calificación autónomos de cada registrador, por lo que nuestra empresa no se hace responsable por las demoras ocasionadas como consecuencia de la aplicación de criterios registrales empleados por SUNARP.</li>
        <li>El solicitante acepta formalmente todas las características del vehículo descritos en el presente documento.</li>
        <li>El Cliente declara conocer que en caso el vehículo no se encuentre en stock, libera a la empresa de cualquier responsabilidad relacionada con los plazos de entrega. Las fechas de entrega son variables y están sujetas a cambio, con previa comunicación al cliente.</li>
        <li>Manifiesto que los datos consignados son exactos y se ajustan fielmente a la realidad.</li>
        <li>El tipo de cambio es referencial, cualquier variación que ocurra al momento de la cancelación del vehículo será asumido por el cliente.</li>
        <li>El cliente ha sido informado que podrían presentarse ciertas características audibles y/o perceptibles propias del funcionamiento, accionamiento o desempeño de los componentes y/o elementos del vehículo (Motor, sistema de frenos, transmisión, aire acondicionado, suspensión, eléctrico, refrigeración entre otros) y en mayor medida en determinadas condiciones climáticas o de exigencia en la conducción.</li>
        <li>El cliente declara que se le ha informado desde la fase de oferta y exhibición de los modelos Tiggo 7 Pro y Tiggo 8, que CHERY importa y comercializa algunos vehículos que vienen con lunas oscurecidas/polarizadas de fábrica y que conforme el D.S. N° 004-2019-IN y D.S. N° 058-2003-MTC, EL CLIENTE deberá tramitar bajo su costo y cargo el permiso de lunas polarizadas ante la autoridad competente en caso fuese aplicable.</li>
        <li>Toda pago y obligaciones tributarias dependen directamente del adquiriente (Impuesto Vehicular).</li>
    </ol>
</div>

<!-- Botón de envío -->
<div style="width:774px; margin:20px auto; text-align:center;">
    <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.3s ease;">
        💾 GUARDAR ORDEN DE COMPRA
    </button>
</div>


    <script>
        // Función para ajustar el ancho del input según el texto
        function adjustInputWidth(input) {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            context.font = getComputedStyle(input).font;
            const textWidth = context.measureText(input.value || ' ').width;
            let minWidth = 50; // mínimo por defecto
            if (input.name === 'OC_ASESOR_VENTA' || input.name === 'OC_COMPRADOR_NOMBRE') {
                minWidth = 150; // mínimo más largo para asesor y comprador
            }

            // Obtener el ancho original definido en el CSS inline
            const originalWidth = input.style.width;
            const originalWidthValue = originalWidth ? parseInt(originalWidth.replace('px', '')) : 0;

            // Usar el mayor entre el ancho calculado y el ancho original
            const newWidth = Math.max(textWidth + 20, minWidth, originalWidthValue);
            input.style.width = newWidth + 'px';
        }

        // Ajustar al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="text"]:not(.no-adjust), input[type="email"]:not(.no-adjust)');
            inputs.forEach(input => {
                adjustInputWidth(input);
                input.addEventListener('input', function() {
                    adjustInputWidth(this);
                });
            });
        });
    </script>
        </div>
    <script>
        // Función para calcular el tipo de cambio en soles
        function calcularTipoCambio() {
            const precioTotal = parseFloat(document.getElementsByName('OC_PRECIO_TOTAL_COMPRA')[0].value) || 0;
            const tipoCambio = parseFloat(document.getElementsByName('OC_TIPO_CAMBIO')[0].value) || 0;
            const moneda = document.getElementsByName('OC_MONEDA_PRECIO_TOTAL')[0].value;
            let resultado;
            if (moneda === 'US$') {
                resultado = precioTotal * tipoCambio;
            } else {
                resultado = precioTotal;
            }
            document.getElementsByName('OC_TIPO_CAMBIO_SOL')[0].value = resultado.toFixed(2);
        }

        // Función para autocompletar fecha de nacimiento
        function autocompletarFechaNacimiento() {
            const primeraFechaNacimiento = document.getElementsByName('OC_FECHA_NACIMIENTO')[0];
            const segundaFechaNacimiento = document.getElementsByName('OC_FECHA_NACIMIENTO_NUEVO')[0];

            if (primeraFechaNacimiento && segundaFechaNacimiento) {
                primeraFechaNacimiento.addEventListener('change', function() {
                    if (this.value && !segundaFechaNacimiento.value) {
                        segundaFechaNacimiento.value = this.value;
                    }
                });
            }
        }

        // Función para manejar bloqueo de bono de financiamiento
        function manejarBonoFinanciamiento() {
            const formaPago = document.getElementsByName('OC_FORMA_PAGO')[0].value;
            const bonoMoneda = document.getElementsByName('OC_MONEDA_BONO_FINANCIAMIENTO')[0];
            const bonoInput = document.getElementsByName('OC_BONO_FINANCIAMIENTO')[0];

            if (formaPago === 'CONTADO') {
                bonoMoneda.disabled = true;
                bonoInput.disabled = true;
                bonoInput.value = '';
            } else {
                bonoMoneda.disabled = false;
                bonoInput.disabled = false;
            }
        }

        // Función para calcular total de equipamiento
        function calcularTotalEquipamiento() {
            let total = 0;
            for (let i = 1; i <= 5; i++) {
                const valor = parseFloat(document.getElementsByName('OC_EQUIPAMIENTO_ADICIONAL_' + i)[0].value) || 0;
                total += valor;
            }
            document.getElementsByName('OC_TOTAL_EQUIPAMIENTO')[0].value = total.toFixed(2);
            calcularPrecioTotalCompra();
        }

        // Función para calcular precio total de compra
        function calcularPrecioTotalCompra() {
            const precioVenta = parseFloat(document.getElementsByName('OC_PRECIO_VENTA')[0].value) || 0;
            const totalEquipamiento = parseFloat(document.getElementsByName('OC_TOTAL_EQUIPAMIENTO')[0].value) || 0;
            const precioTotal = precioVenta + totalEquipamiento;
            document.getElementsByName('OC_PRECIO_TOTAL_COMPRA')[0].value = precioTotal.toFixed(2);
            calcularTipoCambio();
        }

        // Agregar event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const precioTotalInput = document.getElementsByName('OC_PRECIO_TOTAL_COMPRA')[0];
            const tipoCambioInput = document.getElementsByName('OC_TIPO_CAMBIO')[0];
            const monedaSelect = document.getElementsByName('OC_MONEDA_PRECIO_TOTAL')[0];
            const formaPagoSelect = document.getElementsByName('OC_FORMA_PAGO')[0];
            const precioVentaInput = document.getElementsByName('OC_PRECIO_VENTA')[0];

            if (precioTotalInput && tipoCambioInput) {
                precioTotalInput.addEventListener('input', calcularTipoCambio);
                tipoCambioInput.addEventListener('input', calcularTipoCambio);
            }

            if (monedaSelect) {
                monedaSelect.addEventListener('change', calcularTipoCambio);
            }

            // Event listener para forma de pago
            if (formaPagoSelect) {
                formaPagoSelect.addEventListener('change', manejarBonoFinanciamiento);
            }

            // Event listeners para equipamientos
            for (let i = 1; i <= 5; i++) {
                const equipInput = document.getElementsByName('OC_EQUIPAMIENTO_ADICIONAL_' + i)[0];
                if (equipInput) {
                    equipInput.addEventListener('input', calcularTotalEquipamiento);
                }
            }

            // Event listener para precio de venta
            if (precioVentaInput) {
                precioVentaInput.addEventListener('input', calcularPrecioTotalCompra);
            }

            // Inicializar autocompletado de fecha
            autocompletarFechaNacimiento();

            // Inicializar cálculos
            manejarBonoFinanciamiento();
            calcularTotalEquipamiento();
        });
    </script>
    </form>
</body>
