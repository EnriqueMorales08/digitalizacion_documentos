<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta de Características - Interamericana</title>
    <style>
        :root {
            --primary: #1e3a8a;
            --ink: #111827;
            --bg: #f8f9fa;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg);
            margin: 0;
            padding: 20px;
            line-height: 1.1;
            color: #333;
        }

        /* Bloque completo tipo carta */
        .page {
            width: 794px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
        }

        @media print {
            @page { 
                margin: 0; 
                size: auto;
            }
            body {
                background: #fff;
                margin: 0;
                padding: 10mm;
            }
            html {
                margin: 0;
                padding: 0;
            }

            .page {
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .logo-section img {
            height: 60px;
            width: auto;
        }
        
        .contact-info {
            text-align: right;
            font-size: 12px;
            line-height: 1.3;
        }
        
        .title {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            margin: 30px 0;
            text-transform: uppercase;
        }
        
        .date {
            text-align: right;
            margin-bottom: 30px;
            font-size: 13.5px;
        }
        
        .recipient {
            margin-bottom: 30px;
        }
        
        .recipient h3 {
            margin: 0 0 5px 0;
            font-size: 15px;
        }
        
        .content {
            margin-bottom: 30px;
            text-align: justify;
        }
        
        .vehicle-details {
            margin: 20px 0;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .detail-label {
            width: 200px;
            font-weight: bold;
        }
        
        .detail-value {
            flex: 1;
        }
        
        .values-section {
            margin: 30px 0;
        }
        
        .commitment {
            text-align: center;
            margin: 40px 0;
            font-weight: bold;
        }
        
        .closing {
            margin-top: 40px;
        }
        
        .footer-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 10px;
        }

        /* === Inputs sin líneas ni bordes visibles === */
        input {
            border: none;
            outline: none;
            font-family: inherit;
            font-size: inherit;
            background: transparent;
            width: auto;
            min-width: 100px;
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
  <div class="no-print" style="position: fixed; top: 20px; left: 20px; z-index: 1000;">
    <a href="<?= $urlRegreso ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3); font-family: Arial, sans-serif; font-size: 14px; font-weight: 500; transition: all 0.3s ease;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Regresar
    </a>
  </div>

    <form method="POST" action="/digitalizacion-documentos/documents/guardar-documento" style="margin: 0; padding: 0;">
    <div class="page">
        <div class="header">
        <div class="logo-section">
           <img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" alt="Logo Interamericana" width="200">
        </div>
        <div class="contact-info">
            Principal: Av. Sanchez Cerro Mz. 240 Lt. 02 Zona Industrial – Piura<br>
            Telef.: 073-325352<br>
            Tienda: José Leonardo Ortiz Nro. 450 – Chiclayo Web: www.interamericananorte.com
        </div>
    </div>

    <div class="title">CARTA DE CARACTERÍSTICAS</div>

    <div class="date">
        <?php
        $fecha = $ordenCompraData['OC_FECHA_ORDEN'] ?? '';
        if ($fecha) {
            if ($fecha instanceof DateTime) {
                $date = $fecha;
            } else {
                $date = new DateTime($fecha);
            }
            $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            $dia = $date->format('d');
            $mes = $meses[(int)$date->format('m')];
            $anio = $date->format('Y');
            $formatted = "Piura, $dia de $mes de $anio";
        } else {
            $formatted = "Piura, 28 de Agosto de 2025";
        }
        ?>
        <input type="text" name="CC_FECHA_CARTA" value="<?php echo htmlspecialchars($formatted); ?>" style="width: 300px;">
    </div>

    <div class="recipient">
        <h3>Señores:</h3>
        <h2><input type="text" name="CC_EMPRESA_DESTINO" value="AUTOPLAN EAFC S.A." style="width: 400px;"></h2>
        <p><strong>Presente. -</strong></p>
    </div>

    <div class="content">
        <p><strong>Estimados Señores:</strong></p>

        <p>Mediante la presente, les informamos que nuestro mutuo cliente
        <strong><input type="text" name="CC_CLIENTE_NOMBRE" value="<?php echo htmlspecialchars($documentData['CC_CLIENTE_NOMBRE'] ?? (trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))) ?? ''); ?>" style="width: 350px;"></strong>
        con <strong>DNI. <input type="text" name="CC_CLIENTE_DNI" value="<?php echo htmlspecialchars($documentData['CC_CLIENTE_DNI'] ?? $ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?>" style="width: 100px;"></strong>
        ha obtenido el crédito vehicular por la siguiente unidad:</p>
    </div>

    <div class="vehicle-details">
        <div class="detail-row">
            <div class="detail-label">Marca</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_MARCA" value="<?php echo htmlspecialchars($documentData['CC_VEHICULO_MARCA'] ?? $ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?>"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Modelo</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_MODELO" value="<?php echo htmlspecialchars($documentData['CC_VEHICULO_MODELO'] ?? $ordenCompraData['OC_VEHICULO_MODELO'] ?? ''); ?>" style="width: 250px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Año de Modelo</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_ANIO_MODELO" value="<?php echo htmlspecialchars($documentData['CC_VEHICULO_ANIO_MODELO'] ?? $ordenCompraData['OC_VEHICULO_ANIO_MODELO'] ?? ''); ?>" style="width: 100px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Número de Chasis</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_CHASIS" value="<?php echo htmlspecialchars($documentData['CC_VEHICULO_CHASIS'] ?? $ordenCompraData['OC_VEHICULO_CHASIS'] ?? ''); ?>" style="width: 250px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Número de Motor</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_MOTOR" value="<?php echo htmlspecialchars($documentData['CC_VEHICULO_MOTOR'] ?? $ordenCompraData['OC_VEHICULO_MOTOR'] ?? ''); ?>" style="width: 200px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Carrocería</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_CARROCERIA" value="SEDAN" style="width: 150px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Color</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_COLOR" value="<?php echo htmlspecialchars($documentData['CC_VEHICULO_COLOR'] ?? $ordenCompraData['OC_VEHICULO_COLOR'] ?? ''); ?>" style="width: 200px;"></div>
        </div>
    </div>

    <div class="values-section">
        <h4>Valores:</h4>
        <div class="detail-row">
            <div class="detail-label">Precio del vehículo</div>
            <div class="detail-value">: <input type="text" name="CC_PRECIO_VEHICULO" value="<?php echo htmlspecialchars($documentData['CC_PRECIO_VEHICULO'] ?? trim(($ordenCompraData['OC_MONEDA_PRECIO_VENTA'] ?? '') . ' ' . ($ordenCompraData['OC_PRECIO_VENTA'] ?? ''))); ?>" style="width: 150px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Cuota inicial</div>
            <div class="detail-value">: <input type="text" name="CC_CUOTA_INICIAL" value="<?php echo htmlspecialchars($documentData['CC_CUOTA_INICIAL'] ?? trim(($ordenCompraData['OC_MONEDA_CUOTA_INICIAL'] ?? '') . ' ' . ($ordenCompraData['OC_CUOTA_INICIAL'] ?? ''))); ?>" style="width: 150px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Monto Aprobado Neto</div>
            <div class="detail-value">: <input type="text" name="CC_MONTO_APROBADO_NETO" style="width: 150px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Campaña Vehículo</div>
            <div class="detail-value">: <input type="text" name="CC_CAMPANA_VEHICULO" style="width: 150px;"></div>
        </div>
    </div>

    <div class="commitment">
        Nos comprometemos a gestionar la tarjeta de Propiedad del vehículo a nombre de:
        <strong><input type="text" name="CC_PROPIETARIO_TARJETA" value="<?php echo htmlspecialchars($documentData['CC_PROPIETARIO_TARJETA'] ?? $ordenCompraData['OC_TARJETA_NOMBRE'] ?? ''); ?>" style="width: 350px;"></strong>
    </div>

    <div class="content">
        <p>A la espera del desembolso correspondiente, quedamos de ustedes.</p>
    </div>

    <div class="closing">
        <p>Atentamente,</p>
    </div>

    <div class="footer-line"></div>
    </div>

    <!-- Botón de guardar -->
    <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
        <input type="hidden" name="document_type" value="carta-caracteristicas">
        <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
            💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
        </button>
    </div>
    <?php endif; ?>
    <?php if (isset($modoImpresion) && $modoImpresion): ?>
    <div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
      <a href="/digitalizacion-documentos/documents/show?id=carta-caracteristicas&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
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

    <!-- Preview desde localStorage -->
    <script>
      const urlParams = new URLSearchParams(window.location.search);
      const esPreview = urlParams.get('preview') === '1';
      const tieneOrdenId = urlParams.get('orden_id') !== null;
      
      if (esPreview && !tieneOrdenId) {
          console.log('👁️ Modo PREVIEW - Carta Características');
          const datosStr = localStorage.getItem('preview_orden_compra');
          
          if (datosStr) {
              try {
                  const datos = JSON.parse(datosStr);
                  console.log('📦 Datos cargados:', datos);
                  
                  const nombreCompleto = (datos.comprador_nombre + ' ' + datos.comprador_apellido).trim();
                  
                  document.querySelector('[name="CC_CLIENTE_NOMBRE"]').value = nombreCompleto;
                  document.querySelector('[name="CC_CLIENTE_DNI"]').value = datos.comprador_numero_doc || '';
                  document.querySelector('[name="CC_VEHICULO_MARCA"]').value = datos.vehiculo_marca || '';
                  document.querySelector('[name="CC_VEHICULO_MODELO"]').value = datos.vehiculo_modelo || '';
                  document.querySelector('[name="CC_VEHICULO_ANIO_MODELO"]').value = datos.vehiculo_anio || '';
                  document.querySelector('[name="CC_VEHICULO_CHASIS"]').value = datos.vehiculo_chasis || '';
                  document.querySelector('[name="CC_VEHICULO_MOTOR"]').value = datos.vehiculo_motor || '';
                  document.querySelector('[name="CC_VEHICULO_COLOR"]').value = datos.vehiculo_color || '';
                  
                  // Precio del vehículo y cuota inicial desde la OC en tiempo real (solo monto, sin moneda)
                  if (datos.precio_venta) {
                      const campoPrecio = document.querySelector('[name="CC_PRECIO_VEHICULO"]');
                      if (campoPrecio && !campoPrecio.value) {
                          campoPrecio.value = (datos.precio_venta || '').toString().trim();
                      }
                  }

                  if (datos.cuota_inicial) {
                      const campoCuota = document.querySelector('[name="CC_CUOTA_INICIAL"]');
                      if (campoCuota && !campoCuota.value) {
                          campoCuota.value = (datos.cuota_inicial || '').toString().trim();
                      }
                  }
                  
                  if (datos.banco_abono) {
                      document.querySelector('[name="CC_EMPRESA_DESTINO"]').value = datos.banco_abono;
                  }
                  
                  console.log('✅ Preview cargado - Carta Características');
              } catch (e) {
                  console.error('❌ Error:', e);
              }
          }
      }
    </script>
</body>
</html>
