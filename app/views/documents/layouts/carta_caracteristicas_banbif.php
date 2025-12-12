<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento BanBif - Financiamiento Vehicular</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 10px;
        }
        
        .document {
            max-width: 794px;
            margin: 0 auto;
            background-color: white;
            padding: 20px 40px;
        }
        
        .header-field {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .header-field label {
            font-size: 11px;
            font-weight: normal;
        }
        
        .header-field input {
            border: 1px solid #333;
            padding: 2px 4px;
            font-size: 11px;
        }
        
        .date-input {
            width: 50px;
        }
        
        .month-input {
            width: 120px;
        }
        
        .year-input {
            width: 150px;
        }
        
        .name-input {
            flex: 1;
            width: 100%;
        }
        
        .ruc-input {
            width: 350px;
        }
        
        .salutation {
            margin-top: 15px;
            margin-bottom: 5px;
        }
        
        .salutation p {
            font-size: 11px;
            line-height: 1.3;
            margin-bottom: 2px;
        }
        
        .salutation .company {
            font-weight: bold;
        }
        
        .content {
            margin-top: 10px;
        }
        
        .content p {
            font-size: 11px;
            line-height: 1.4;
            margin-bottom: 6px;
            text-align: justify;
        }
        
        .financing-box {
            border: 1px solid #333;
            padding: 6px;
            margin: 6px 0 10px 0;
            min-height: 20px;
            font-size: 11px;
        }
        
        .list-items {
            margin: 10px 0;
            padding-left: 30px;
        }
        
        .list-items p {
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .recipient-box {
            border: 1px solid #333;
            padding: 6px;
            margin: 6px 0 10px 0;
            min-height: 20px;
            font-size: 11px;
        }
        
        .vehicle-table {
            width: 100%;
            margin: 15px 0;
        }
        
        .vehicle-table td {
            padding: 3px;
            font-size: 11px;
        }
        
        .vehicle-table input {
            width: 100%;
            border: 1px solid #666;
            padding: 2px 6px;
            font-size: 11px;
        }
        
        .vehicle-table .label-cell {
            width: 140px;
            text-align: left;
        }
        
        .vehicle-table .input-cell {
            width: 210px;
        }
        
        .financial-info {
            margin: 10px 0;
            padding-left: 30px;
        }
        
        .financial-row {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .financial-row .bullet {
            width: 8px;
            height: 8px;
            background-color: black;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .financial-row label {
            min-width: 280px;
        }
        
        .financial-row .currency {
            margin: 0 10px;
        }
        
        .financial-row input {
            border: 1px solid #666;
            padding: 2px 6px;
            font-size: 11px;
        }

        .financial-row .price-input {
            width: 200px;
        }

        .financial-row .currency-label {
            margin-left: 10px;
            margin-right: 10px;
        }

        .financial-row .amount-input {
            width: 200px;
        }
        
        .benefit-row {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            margin-top: 5px;
            font-size: 11px;
            padding-left: 30px;
        }
        
        .benefit-row .bullet {
            width: 8px;
            height: 8px;
            background-color: black;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .benefit-row label {
            min-width: 280px;
        }
        
        .benefit-row .currency {
            margin: 0 10px;
        }
        
        .benefit-row input {
            border: 1px solid #000;
            border-width: 2px;
            padding: 4px 8px;
            font-size: 13px;
            width: 150px;
        }
        
        .benefit-row .currency-label {
            margin-left: 10px;
            margin-right: 10px;
        }
        
        .benefit-row .amount-input {
            width: 163px;
        }
        
        .footnote {
            font-size: 12px;
            margin-top: 20px;
            font-style: italic;
        }
        
        .signature-section {
            margin-top: 80px;
            text-align: right;
        }
        
        .signature-line {
            border-top: 2px solid black;
            width: 300px;
            margin-left: auto;
            padding-top: 5px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Flecha de regreso -->
    <?php
    $urlRegreso = '/digitalizacion-documentos/documents';
    if (isset($_SESSION['orden_id']) && $_SESSION['orden_id']) {
        $urlRegreso = '/digitalizacion-documentos/expedientes/ver?id=' . $_SESSION['orden_id'];
    }
    ?>
    <div style="position: fixed; top: 20px; left: 20px; z-index: 1000;" class="no-print">
        <a href="<?= $urlRegreso ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3); font-family: Arial, sans-serif; font-size: 14px; font-weight: 500; transition: all 0.3s ease;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Regresar
        </a>
    </div>

    <form method="POST" action="/digitalizacion-documentos/documents/guardar-documento" style="margin: 0; padding: 0;">
    <div class="document">
        <?php
        // Formatear fecha para visualización y para BD
        $fechaBD = $documentData['CCB_FECHA_CARTA'] ?? $ordenCompraData['OC_FECHA_ORDEN'] ?? date('Y-m-d');
        
        // Convertir a DateTime si es necesario
        if ($fechaBD instanceof DateTime) {
            $date = $fechaBD;
        } elseif (is_string($fechaBD)) {
            $date = new DateTime($fechaBD);
        } else {
            $date = new DateTime();
        }
        
        // Formato para visualización
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $dia = $date->format('d');
        $mes = $meses[(int)$date->format('m')];
        $anio = $date->format('Y');
        $formatted = "Piura, $dia de $mes de $anio";
        
        // Formato para BD (Y-m-d)
        $fechaFormatoBD = $date->format('Y-m-d');
        ?>
        <div class="header-field">
            <label>Fecha:</label>
            <input type="text" id="fecha_carta_display" value="<?php echo htmlspecialchars($formatted); ?>" style="width: 300px;" readonly>
            <input type="hidden" name="CCB_FECHA_CARTA" value="<?php echo htmlspecialchars($fechaFormatoBD); ?>">
        </div>
        
        <div class="header-field">
            <label>Nombre del Concesionario:</label>
            <input type="text" name="CCB_NOMBRE_CONCESIONARIO" class="name-input" value="<?php echo htmlspecialchars($documentData['CCB_NOMBRE_CONCESIONARIO'] ?? 'INTERAMERICANA NORTE S.A.C.'); ?>">
        </div>
        
        <div class="header-field">
            <label>N° del RUC:</label>
            <input type="text" name="CCB_RUC_CONCESIONARIO" class="ruc-input" value="<?php echo htmlspecialchars($documentData['CCB_RUC_CONCESIONARIO'] ?? '20483998270'); ?>">
        </div>
        
        <div class="salutation">
            <p>Señores</p>
            <p class="company">BanBif – Banco Interamericano de Finanzas</p>
            <p>Presente.-</p>
        </div>
        
        <div class="content">
            <p>De nuestra consideración:</p>
            <p>Por medio de la presente, en virtud del financiamiento otorgado a:</p>
            <input type="text" name="CCB_CLIENTE_NOMBRE" value="<?php echo htmlspecialchars($documentData['CCB_CLIENTE_NOMBRE'] ?? (trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))) ?? ''); ?>" style="width: 100%; border: 1px solid #333; padding: 8px; margin: 10px 0 20px 0; font-size: 14px;">

            <p>para la adquisición del vehículo que se describe en el presente documento, dejamos constancia expresa de nuestro compromiso irrevocable e incondicional de poder entregar en un plazo no mayor de 20 días útiles de haber cancelado el precio de venta, los siguientes documentos a sola solicitud de BanBif. Además, informaré oportunamente a BanBif cualquier suceso que pueda demorar la entrega.</p>

            <div class="list-items">
                <p>a) Copia de la factura cancelada,</p>
                <p>b) Copia de la tarjeta de propiedad del vehículo materia de compra/venta.</p>
            </div>

            <p>De acuerdo a lo coordinado con nuestro cliente y BanBif, les confirmamos que la factura y la tarjeta de propiedad serán emitidas a nombre de:</p>
            <input type="text" name="CCB_PROPIETARIO_TARJETA" value="<?php echo htmlspecialchars($documentData['CCB_PROPIETARIO_TARJETA'] ?? $ordenCompraData['OC_PROPIETARIO_NOMBRE'] ?? ''); ?>" style="width: 100%; border: 1px solid #333; padding: 8px; margin: 10px 0 20px 0; font-size: 14px;">
            
            <p>Asimismo, en caso de que luego de la inmatriculación se den variaciones en los titulares registrales del vehículo, nos comprometemos a regularizar según como se indica en la presente carta o dar aviso a BanBif y al cliente para las regularizaciones correspondientes.</p>
            
            <p>Al momento de emitir esta carta, confirmamos haber recibido de nuestro cliente el abono total de la cuota inicial. Los datos del vehículo son:</p>
        </div>
        
        <table class="vehicle-table">
            <tr>
                <td class="label-cell">Marca:</td>
                <td class="input-cell"><input type="text" name="CCB_VEHICULO_MARCA" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_MARCA'] ?? $ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?>"></td>
                <td class="label-cell">Color:</td>
                <td class="input-cell"><input type="text" name="CCB_VEHICULO_COLOR" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_COLOR'] ?? $ordenCompraData['OC_VEHICULO_COLOR'] ?? ''); ?>"></td>
            </tr>
            <tr>
                <td class="label-cell">Modelo:</td>
                <td class="input-cell"><input type="text" name="CCB_VEHICULO_MODELO" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_MODELO'] ?? $ordenCompraData['OC_VEHICULO_MODELO'] ?? ''); ?>"></td>
                <td class="label-cell">Clase:</td>
                <td class="input-cell"><input type="text" name="CCB_VEHICULO_CLASE" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_CLASE'] ?? $ordenCompraData['OC_VEHICULO_CLASE'] ?? ''); ?>"></td>
            </tr>
            <tr>
                <td class="label-cell">Año de modelo:</td>
                <td class="input-cell"><input type="text" name="CCB_VEHICULO_ANIO_MODELO" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_ANIO_MODELO'] ?? $ordenCompraData['OC_VEHICULO_ANIO_MODELO'] ?? ''); ?>"></td>
                <td class="label-cell">N°. De motor:</td>
                <td class="input-cell"><input type="text" name="CCB_VEHICULO_MOTOR" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_MOTOR'] ?? $ordenCompraData['OC_VEHICULO_MOTOR'] ?? ''); ?>"></td>
            </tr>
            <tr>
                <td class="label-cell">Tipo de carrocería:</td>
                <td class="input-cell"><input type="text" name="CCB_VEHICULO_CARROCERIA" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_CARROCERIA'] ?? ''); ?>"></td>
                <td class="label-cell">N°. De serie:</td>
                <td class="input-cell"><input type="text" name="CCB_VEHICULO_CHASIS" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_CHASIS'] ?? $ordenCompraData['OC_VEHICULO_CHASIS'] ?? ''); ?>"></td>
            </tr>
        </table>
        
        <div class="financial-info">
            <div class="financial-row">
                <div class="bullet"></div>
                <label>Precio de Venta (incluido I.G.V.)</label>
                <span class="currency">$</span>
                <input type="text" name="CCB_PRECIO_VENTA_USD" class="price-input" value="<?php echo htmlspecialchars(
                    $documentData['CCB_PRECIO_VENTA_USD']
                        ?? ((($ordenCompraData['OC_MONEDA_PRECIO_VENTA'] ?? '') === 'US$')
                            ? ($ordenCompraData['OC_PRECIO_VENTA'] ?? '')
                            : '')
                ); ?>">
                <span class="currency-label">S/</span>
                <input type="text" name="CCB_PRECIO_VENTA_PEN" class="amount-input" value="<?php echo htmlspecialchars(
                    $documentData['CCB_PRECIO_VENTA_PEN']
                        ?? ((($ordenCompraData['OC_MONEDA_PRECIO_VENTA'] ?? '') === 'MN')
                            ? ($ordenCompraData['OC_PRECIO_VENTA'] ?? '')
                            : '')
                ); ?>">
            </div>

            <div class="financial-row">
                <div class="bullet"></div>
                <label>Cuota inicial</label>
                <span class="currency">$</span>
                <input type="text" name="CCB_CUOTA_INICIAL_USD" class="price-input" value="<?php echo htmlspecialchars(
                    $documentData['CCB_CUOTA_INICIAL_USD']
                        ?? ((($ordenCompraData['OC_MONEDA_CUOTA_INICIAL'] ?? '') === 'US$')
                            ? ($ordenCompraData['OC_CUOTA_INICIAL'] ?? '')
                            : '')
                ); ?>">
                <span class="currency-label">S/</span>
                <input type="text" name="CCB_CUOTA_INICIAL_PEN" class="amount-input" value="<?php echo htmlspecialchars(
                    $documentData['CCB_CUOTA_INICIAL_PEN']
                        ?? ((($ordenCompraData['OC_MONEDA_CUOTA_INICIAL'] ?? '') === 'MN')
                            ? ($ordenCompraData['OC_CUOTA_INICIAL'] ?? '')
                            : '')
                ); ?>">
            </div>

            <div class="financial-row">
                <div class="bullet"></div>
                <label>Saldo de Precio (Desembolso)</label>
                <span class="currency">$</span>
                <input type="text" name="CCB_SALDO_PRECIO_USD" class="price-input" value="<?php echo htmlspecialchars($documentData['CCB_SALDO_PRECIO_USD'] ?? ''); ?>">
                <span class="currency-label">S/</span>
                <input type="text" name="CCB_SALDO_PRECIO_PEN" class="amount-input" value="<?php echo htmlspecialchars($documentData['CCB_SALDO_PRECIO_PEN'] ?? ''); ?>">
            </div>
        </div>
        
        <div class="benefit-row">
            <div class="bullet"></div>
            <label>Beneficio de Fidelización BanBif*</label>
            <span class="currency">$</span>
            <input type="text" name="CCB_BENEFICIO_BANBIF_USD" class="amount-input" value="<?php echo htmlspecialchars($documentData['CCB_BENEFICIO_BANBIF_USD'] ?? ''); ?>">
            <span class="currency-label">S/</span>
            <input type="text" name="CCB_BENEFICIO_BANBIF_PEN" class="amount-input" value="<?php echo htmlspecialchars($documentData['CCB_BENEFICIO_BANBIF_PEN'] ?? ''); ?>">
        </div>
        
        <div class="content">
            <p>Cualquier variación en las condiciones y/o términos de la operación de venta que se realice antes o después del desembolso del saldo de precio, deberá ser informada a BanBif con anticipación, caso contrario la venta no tendrá efecto y aceptamos devolver dentro de los 2 días útiles siguientes de la sola solicitud de BanBif, cualquier monto que nos hubiera entregado.</p>
            
            <p>Solicitamos que el desembolso se efectúe según los montos indicados en la presente carta en soles o dólares; y que se abone en la cuenta corriente que tenemos en BanBif, o en su defecto, se emita un cheque de gerencia.</p>
            
            <p class="footnote">*Descuento aplicado al precio de vehículo</p>
        </div>
        
        <div class="signature-section">
            <div class="signature-line">
                Firma del representante
            </div>
        </div>
    </div>

    <!-- Botón de guardar -->
    <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
        <input type="hidden" name="document_type" value="carta_caracteristicas_banbif">
        <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
            💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
        </button>
    </div>
    <?php endif; ?>
    <?php if (isset($modoImpresion) && $modoImpresion): ?>
    <div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
      <a href="/digitalizacion-documentos/documents/show?id=carta_caracteristicas_banbif&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
         style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4); font-family: Arial, sans-serif; font-size: 14px; font-weight: 600; transition: all 0.3s ease;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        ✏️ EDITAR
      </a>
    </div>
    <?php endif; ?>
    <script>
    <?php if (isset($modoImpresion) && $modoImpresion): ?>
    document.addEventListener('DOMContentLoaded', function() {
      const inputs = document.querySelectorAll('input:not([type="hidden"]), select, textarea');
      inputs.forEach(el => { el.setAttribute('readonly', 'readonly'); el.setAttribute('disabled', 'disabled'); el.style.cursor = 'default'; el.style.pointerEvents = 'none'; });
    });
    <?php endif; ?>
    </script>

    </form>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                padding: 0 !important;
                background-color: white !important;
            }
            
            .document {
                box-shadow: none !important;
                padding: 8mm !important;
                max-width: 100% !important;
            }
            .benefit-row .currency{
                    margin: 0 8px;
            }
                .benefit-row .currency-label {
                 margin-left: 6px;
                 margin-right: 6px;
             }
            
        }
    </style>

    <!-- Preview desde localStorage -->
    <script>
      const urlParams = new URLSearchParams(window.location.search);
      const esPreview = urlParams.get('preview') === '1';
      const tieneOrdenId = urlParams.get('orden_id') !== null;
      
      if (esPreview && !tieneOrdenId) {
          console.log('👁️ Modo PREVIEW - Carta Características Banbif');
          const datosStr = localStorage.getItem('preview_orden_compra');
          
          if (datosStr) {
              try {
                  const datos = JSON.parse(datosStr);
                  console.log('📦 Datos cargados:', datos);
                  
                  const nombreCompleto = (datos.comprador_nombre + ' ' + datos.comprador_apellido).trim();
                  
                  document.querySelector('[name="CCB_CLIENTE_NOMBRE"]').value = nombreCompleto;
                  document.querySelector('[name="CCB_VEHICULO_MARCA"]').value = datos.vehiculo_marca || '';
                  document.querySelector('[name="CCB_VEHICULO_MODELO"]').value = datos.vehiculo_modelo || '';
                  const campoAnio = document.querySelector('[name="CCB_VEHICULO_ANIO_MODELO"]') || document.querySelector('[name="CCB_VEHICULO_ANIO"]');
                  if (campoAnio) {
                      campoAnio.value = datos.vehiculo_anio || '';
                  }
                  const campoClase = document.querySelector('[name="CCB_VEHICULO_CLASE"]');
                  if (campoClase) {
                      campoClase.value = datos.vehiculo_clase || '';
                  }
                  document.querySelector('[name="CCB_VEHICULO_CHASIS"]').value = datos.vehiculo_chasis || '';
                  document.querySelector('[name="CCB_VEHICULO_MOTOR"]').value = datos.vehiculo_motor || '';
                  document.querySelector('[name="CCB_VEHICULO_COLOR"]').value = datos.vehiculo_color || '';

                  // Precio de venta y cuota inicial desde la OC en tiempo real (solo montos)
                  if (datos.precio_venta) {
                      const campoUsd = document.querySelector('[name="CCB_PRECIO_VENTA_USD"]');
                      const campoPen = document.querySelector('[name="CCB_PRECIO_VENTA_PEN"]');
                      const valor = (datos.precio_venta || '').toString().trim();
                      if (campoUsd && !campoUsd.value) campoUsd.value = valor;
                      if (campoPen && !campoPen.value) campoPen.value = valor;
                  }

                  if (datos.cuota_inicial) {
                      const campoUsd = document.querySelector('[name="CCB_CUOTA_INICIAL_USD"]');
                      const campoPen = document.querySelector('[name="CCB_CUOTA_INICIAL_PEN"]');
                      const valor = (datos.cuota_inicial || '').toString().trim();
                      if (campoUsd && !campoUsd.value) campoUsd.value = valor;
                      if (campoPen && !campoPen.value) campoPen.value = valor;
                  }
                  
                  console.log('✅ Preview cargado - Carta Características Banbif');
              } catch (e) {
                  console.error('❌ Error:', e);
              }
          }
      }
    </script>
</body>
</html>