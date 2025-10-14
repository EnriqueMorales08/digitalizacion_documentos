<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Carta de Conocimiento y Aceptación - Interamericana Norte</title>
<style>
 :root {
   --primary: #1e3a8a;
   --ink: #111827;
   --bg: #f8f9fa;
 }

 @page { size: A4; margin: 25mm; }
 body{
   font-family:"Times New Roman", Times, serif;
   background-color: var(--bg);
   margin: 0;
   padding: 20px;
   font-size: 12pt;
 }

 /* Bloque completo tipo carta */
 .page {
   width: 794px;
   margin: 0 auto;
   background: #fff;
   padding: 20px;
   border-radius: 10px;
   box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
   line-height: 1.5;
   color: #000;
 }

  .header{ text-align:center; margin: 6px 0 14px; }
  .company{ font-weight:700; font-size:16pt; margin-bottom:2px; }
  .title{ font-weight:700; font-size:13.5pt; }

  /* NO justificar por defecto para evitar estiramientos raros */
  p{ margin: 0 0 10px; }
  /* Solo justificar donde sí corresponde (párrafo largo) */
  .justify{ text-align: justify; }

  /* Las 3 líneas iniciales van sin justificado */
  .intro p{ text-align: left; margin: 0 0 6px; }

  ol{ margin: 14px 0 0 18px; }
  ol li{ margin: 8px 0; }

  .line{
    display:inline-block;
    border-bottom:1px solid #000;
    padding: 0 6px 2px;
    line-height:1.2;
    min-width: 40px;
  }
  .w-330{ min-width:330px; }
  .w-300{ min-width:350px; }
  .w-260{ min-width:260px; }
  .w-220{ min-width:220px; }
  .w-180{ min-width:180px; }
  .w-140{ min-width:140px; }
  .w-120{ min-width:120px; }
  .w-100{ min-width:100px; }

  .bold{ font-weight:700; }
  .lower{ text-transform: lowercase; }

  .divider{ margin: 22px 0 14px; border:0; border-top:1px solid #000; }
  .row{ margin: 0 0 8px; }
  .label{ display:inline-block; width:145px; font-weight:700; vertical-align:top; }
  .u{ display:inline-block; border-bottom:1px solid #000; min-width:260px; padding-bottom:2px; }

  @media print{
    body{ width:auto; }
    .line,.u{ border-bottom:1px solid #000 !important; }
  }
</style>
</head>
<body>
  <form method="POST" action="/digitalizacion-documentos/documents/guardar-documento">
  <!-- Flecha de regreso -->
  <?php
  $urlRegreso = '/digitalizacion-documentos/documents';
  if (isset($_SESSION['orden_id']) && $_SESSION['orden_id']) {
      $urlRegreso = '/digitalizacion-documentos/expedientes/ver?id=' . $_SESSION['orden_id'];
  }
  ?>
  <div style="position: fixed; top: 20px; left: 20px; z-index: 1000;">
    <a href="<?= $urlRegreso ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3); font-family: Arial, sans-serif; font-size: 14px; font-weight: 500; transition: all 0.3s ease;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Regresar
    </a>
  </div>

  <div class="page">
    <div class="header">
      <div class="company">INTERAMERICANA NORTE</div>
      <div class="title">Carta de Conocimiento y Aceptación</div>
    </div>

  <!-- BLOQUE INICIAL SIN JUSTIFICAR -->
  <div class="intro" spellcheck="false">
    <p>
      Yo, <span class="line w-300" contenteditable="true" id="cliente_nombre_completo"><?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?></span>, identificado con
      <span class="line w-220" contenteditable="true" id="cliente_documento"><?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?></span>, en calidad de comprador del
    </p>
    <p>
      vehículo <span class="line w-140" contenteditable="true" id="vehiculo_marca"><?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?></span>, modelo
      <span class="line w-180" contenteditable="true" id="vehiculo_modelo"><?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_MODELO'] ?? ''); ?></span>, año
      <span class="line w-100" contenteditable="true" id="vehiculo_anio"><?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_ANIO_MODELO'] ?? ''); ?></span>,
    </p>
    <p>
      <span class="lower">vin</span> <span class="line w-330" contenteditable="true" id="vehiculo_vin"><?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_CHASIS'] ?? ''); ?></span>, declaro lo siguiente:
    </p>
  </div>

  <ol>
    <li><span class="bold">Condición de registro:</span> Se me ha informado que, para efectos de realizar el trámite de inmatriculación y emisión de placas ante Registros Públicos, el tramitador designado registrará la operación bajo la modalidad de <span class="bold">“condición de compra al crédito”</span>, aun cuando mi adquisición haya sido realizada <span class="bold">al contado</span>.</li>
    <li><span class="bold">Finalidad de la medida:</span> Esta condición <span class="bold">no altera mi forma de pago</span>, sino que se aplica exclusivamente como requisito administrativo para <span class="bold">agilizar y facilitar la gestión del trámite registral</span>. Con ello se evita la demora que implicaría presentar los comprobantes de pago (vouchers) de manera individual, lo que podría retrasar la entrega de mis placas.</li>
    <li><span class="bold">Exoneración de responsabilidad:</span> Entiendo y acepto que <span class="bold">Interamericana Norte</span> y el tramitador no asumen responsabilidad alguna por los plazos adicionales que pudieran originarse si, por motivos ajenos a su gestión, los registros públicos requieren documentación complementaria.</li>
  </ol>

  <p class="justify">En consecuencia, con mi firma dejo constancia de que he sido informado y <span class="bold">acepto expresamente esta modalidad de registro</span>, comprendiendo que tiene como único propósito optimizar el tiempo de entrega de mis placas de rodaje.</p>

  <hr class="divider"/>

  <div class="row"><span class="label">Firma del cliente:</span><span class="u w-300" contenteditable="true" spellcheck="false" id="firma_cliente"><?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?><img src="<?php echo htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']); ?>" style="max-width:100%; max-height:50px;"><?php else: ?>Firma<?php endif; ?></span></div>
  <div class="row"><span class="label">Nombre:</span><span class="u w-300" contenteditable="true" spellcheck="false" id="nombre_firma"><?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?></span></div>
  <div class="row"><span class="label">DNI/CE:</span><span class="u w-220" contenteditable="true" spellcheck="false" id="documento_firma"><?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?></span></div>
  <div class="row"><span class="label">Fecha:</span><span class="u w-140" contenteditable="true" spellcheck="false" id="fecha_firma"><?php echo date('d/m/Y'); ?></span></div>
 </div>


  <script>
    // Función para ajustar el ancho del span según el texto
    function adjustSpanWidth(span) {
      const canvas = document.createElement('canvas');
      const context = canvas.getContext('2d');
      context.font = getComputedStyle(span).font;
      const textWidth = context.measureText(span.textContent || ' ').width;
      span.style.minWidth = Math.max(textWidth + 20, 50) + 'px';
    }

    // Ajustar al cargar
    document.addEventListener('DOMContentLoaded', function() {
      const spans = document.querySelectorAll('span[contenteditable="true"]');
      spans.forEach(span => {
        adjustSpanWidth(span);
        span.addEventListener('input', function() {
          adjustSpanWidth(this);
        });
      });
    });
  </script>

  <!-- Campos ocultos para guardar datos -->
  <input type="hidden" name="CCA_CLIENTE_NOMBRE_COMPLETO" id="hidden_cliente_nombre_completo">
  <input type="hidden" name="CCA_CLIENTE_DOCUMENTO" id="hidden_cliente_documento">
  <input type="hidden" name="CCA_VEHICULO_MARCA" id="hidden_vehiculo_marca">
  <input type="hidden" name="CCA_VEHICULO_MODELO" id="hidden_vehiculo_modelo">
  <input type="hidden" name="CCA_VEHICULO_ANIO" id="hidden_vehiculo_anio">
  <input type="hidden" name="CCA_VEHICULO_VIN" id="hidden_vehiculo_vin">
  <input type="hidden" name="CCA_FIRMA_CLIENTE" id="hidden_firma_cliente">
  <input type="hidden" name="CCA_NOMBRE_FIRMA" id="hidden_nombre_firma">
  <input type="hidden" name="CCA_DOCUMENTO_FIRMA" id="hidden_documento_firma">
  <input type="hidden" name="CCA_FECHA_FIRMA" id="hidden_fecha_firma">

  <!-- Botón de guardar -->
  <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
    <input type="hidden" name="document_type" value="carta_conocimiento_aceptacion">
    <button type="submit" onclick="return copiarDatosAntesDeGuardar(event)" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
        💾 GUARDAR
    </button>
  </div>
  
  <script>
    function copiarDatosAntesDeGuardar(event) {
      // Copiar valores de contenteditable a campos ocultos
      document.getElementById('hidden_cliente_nombre_completo').value = document.getElementById('cliente_nombre_completo').textContent;
      document.getElementById('hidden_cliente_documento').value = document.getElementById('cliente_documento').textContent;
      document.getElementById('hidden_vehiculo_marca').value = document.getElementById('vehiculo_marca').textContent;
      document.getElementById('hidden_vehiculo_modelo').value = document.getElementById('vehiculo_modelo').textContent;
      document.getElementById('hidden_vehiculo_anio').value = document.getElementById('vehiculo_anio').textContent;
      document.getElementById('hidden_vehiculo_vin').value = document.getElementById('vehiculo_vin').textContent;
      document.getElementById('hidden_firma_cliente').value = document.getElementById('firma_cliente').innerHTML;
      document.getElementById('hidden_nombre_firma').value = document.getElementById('nombre_firma').textContent;
      document.getElementById('hidden_documento_firma').value = document.getElementById('documento_firma').textContent;
      document.getElementById('hidden_fecha_firma').value = document.getElementById('fecha_firma').textContent;
      
      // Permitir que el formulario se envíe
      return true;
    }
  </script>
  </form>
  <style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
  </style>
</body>
</html>
