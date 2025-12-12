<?php
date_default_timezone_set('America/Lima');
?>
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
   border-radius: 0;
   box-shadow: none;
   line-height: 1.5;
   color: #000;
 }

  .header{ text-align:center; margin: 6px 0 14px; }
  .company{ font-weight:700; font-size:16pt; margin-bottom:2px; }
  .title{ font-weight:700; font-size:13.5pt; }

  /* NO justificar por defecto para evitar estiramientos raros */
  p{ margin: 0 25px 0 10px; }
  /* Solo justificar donde sí corresponde (párrafo largo) */
  .justify{ text-align: justify; }

  /* Las 3 líneas iniciales van sin justificado */
  .intro p{ text-align: left; margin: 0 0 6px; }

  ol{ margin: 14px 25px 0 18px; }
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

  .divider{ margin: 30px 25px 0 14px; border:0; border-top:1px solid #000; }
  .row{ margin: 15px 0 8px; }
  .label{ display:inline-block; width:145px; font-weight:700; vertical-align:center; }
  .u{ display:inline-block; border-bottom:1px solid #000; min-width:260px; padding-bottom:2px; }

  @media print{
    @page{
      margin: 8mm 8mm 8mm 3mm;
    }
   
    body{ width:auto; font-size: 16.5px; margin: 0; padding: 10mm; }
    html { margin: 0; padding: 0; }
    .page{ border-radius: 0; box-shadow: none; }
    .line,.u{ border-bottom:1px solid #000 !important; }
    p, li, span, .line, .u{ font-size: 16.5px !important; }
    .no-print{ display: none !important; }

    /* Ocultar bordes y placeholder de firma en impresión */
    #firma-cliente-preview {
      border: none !important;
      min-height: auto !important;
    }
    
    #firma-cliente-preview span {
      display: none !important;
    }
  }
</style>
</head>
<body>
  <form method="POST" action="/digitalizacion-documentos/documents/guardar-documento">
  <!-- Flecha de regreso -->
  <?php
  // Determinar URL de regreso
  $esCajera = isset($_GET['cajera']) && $_GET['cajera'] === '1';
  $tokenCajera = $_GET['token'] ?? '';
  
  if ($esCajera && $tokenCajera) {
      $urlRegreso = '/digitalizacion-documentos/cajera/ver?token=' . urlencode($tokenCajera);
  } elseif (isset($_SESSION['orden_id']) && $_SESSION['orden_id']) {
      $urlRegreso = '/digitalizacion-documentos/expedientes/ver?id=' . $_SESSION['orden_id'];
  } else {
      $urlRegreso = '/digitalizacion-documentos/documents';
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

  <div class="page">
    <div class="header">
      <div class="company">INTERAMERICANA NORTE</div>
      <div class="title">Carta de Conocimiento y Aceptación</div>
    </div>

  <!-- BLOQUE INICIAL SIN JUSTIFICAR -->
  <div class="intro" spellcheck="false">
    <p>
      Yo, <span class="line w-300" contenteditable="true" id="cliente_nombre_completo"><?php echo htmlspecialchars($documentData['CCA_CLIENTE_NOMBRE_COMPLETO'] ?? (trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))) ?? ''); ?></span>, identificado con
      <span class="line w-220" contenteditable="true" id="cliente_documento"><?php echo htmlspecialchars($documentData['CCA_CLIENTE_DOCUMENTO'] ?? $ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?></span>, en calidad de comprador del
    </p>
    <p>
      vehículo <span class="line w-140" contenteditable="true" id="vehiculo_marca"><?php echo htmlspecialchars($documentData['CCA_VEHICULO_MARCA'] ?? $ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?></span>, modelo
      <span class="line w-180" contenteditable="true" id="vehiculo_modelo"><?php echo htmlspecialchars($documentData['CCA_VEHICULO_MODELO'] ?? $ordenCompraData['OC_VEHICULO_MODELO'] ?? ''); ?></span>, año
      <span class="line w-100" contenteditable="true" id="vehiculo_anio"><?php echo htmlspecialchars($documentData['CCA_VEHICULO_ANIO'] ?? $ordenCompraData['OC_VEHICULO_ANIO_MODELO'] ?? ''); ?></span>,
    </p>
    <p>
      <span class="lower">vin</span> <span class="line w-330" contenteditable="true" id="vehiculo_vin"><?php echo htmlspecialchars($documentData['CCA_VEHICULO_VIN'] ?? $ordenCompraData['OC_VEHICULO_CHASIS'] ?? ''); ?></span>, declaro lo siguiente:
    </p>
  </div>

  <ol>
    <li><span class="bold">Condición de registro:</span> Se me ha informado que, para efectos de realizar el trámite de inmatriculación y emisión de placas ante Registros Públicos, el tramitador designado registrará la operación bajo la modalidad de <span class="bold">“condición de compra al crédito”</span>, aun cuando mi adquisición haya sido realizada <span class="bold">al contado</span>.</li>
    <li><span class="bold">Finalidad de la medida:</span> Esta condición <span class="bold">no altera mi forma de pago</span>, sino que se aplica exclusivamente como requisito administrativo para <span class="bold">agilizar y facilitar la gestión del trámite registral</span>. Con ello se evita la demora que implicaría presentar los comprobantes de pago (vouchers) de manera individual, lo que podría retrasar la entrega de mis placas.</li>
    <li><span class="bold">Exoneración de responsabilidad:</span> Entiendo y acepto que <span class="bold">Interamericana Norte</span> y el tramitador no asumen responsabilidad alguna por los plazos adicionales que pudieran originarse si, por motivos ajenos a su gestión, los registros públicos requieren documentación complementaria.</li>
  </ol>

  <p class="justify">En consecuencia, con mi firma dejo constancia de que he sido informado y <span class="bold">acepto expresamente esta modalidad de registro</span>, comprendiendo que tiene como único propósito optimizar el tiempo de entrega de mis placas de rodaje.</p>

  <hr class="divider"/>

  <div class="row">
    <span class="label">Firma del cliente:</span>
    <div id="firma-cliente-preview" style="display:inline-block; min-width:260px; border-bottom:1px solid #000; padding-bottom:2px; position:relative; min-height:50px; vertical-align:bottom; cursor:pointer;" onclick="abrirCapturadorFirmaCCA()">
      <?php if (!empty($documentData['CCA_FIRMA_CLIENTE'])): ?>
        <img src="<?php echo htmlspecialchars($documentData['CCA_FIRMA_CLIENTE']); ?>" style="max-width:100%; max-height:50px; display:block;" alt="Firma del cliente">
      <?php else: ?>
        <span style="color:#999; font-size:11px;">Haga clic aquí para firmar</span>
      <?php endif; ?>
    </div>
  </div>
  <div class="row"><span class="label">Nombre:</span><span class="u w-300" contenteditable="true" spellcheck="false" id="nombre_firma"><?php echo htmlspecialchars($documentData['CCA_NOMBRE_FIRMA'] ?? (trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))) ?? ''); ?></span></div>
  <div class="row"><span class="label">DNI/CE:</span><span class="u w-220" contenteditable="true" spellcheck="false" id="documento_firma"><?php echo htmlspecialchars($documentData['CCA_DOCUMENTO_FIRMA'] ?? $ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?></span></div>
  <div class="row"><span class="label">Fecha:</span><span class="u w-140" contenteditable="true" spellcheck="false" id="fecha_firma"><?php 
    if (!empty($documentData['CCA_FECHA_FIRMA'])) {
      $fechaFirma = $documentData['CCA_FECHA_FIRMA'];
      if ($fechaFirma instanceof DateTime) { echo $fechaFirma->format('d/m/Y'); } else { echo date('d/m/Y', strtotime($fechaFirma)); }
    } else {
      $fechaOrden = $ordenCompraData['OC_FECHA_ORDEN'] ?? date('Y-m-d'); 
      if ($fechaOrden instanceof DateTime) { $fechaOrden = $fechaOrden->format('Y-m-d'); } 
      echo date('d/m/Y', strtotime($fechaOrden));
    }
  ?></span></div>
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
  <input type="hidden" name="CCA_FIRMA_CLIENTE" id="firma_cliente_ruta">
  <input type="hidden" name="CCA_NOMBRE_FIRMA" id="hidden_nombre_firma">
  <input type="hidden" name="CCA_DOCUMENTO_FIRMA" id="hidden_documento_firma">
  <input type="hidden" name="CCA_FECHA_FIRMA" id="hidden_fecha_firma">

  <!-- Botón de guardar -->
  <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
  <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
    <input type="hidden" name="document_type" value="carta_conocimiento_aceptacion">
    <button type="submit" onclick="return copiarDatosAntesDeGuardar(event)" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
        💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
    </button>
  </div>
  <?php endif; ?>
  
  <!-- Botón de EDITAR cuando está en modo visualización -->
  <?php if (isset($modoImpresion) && $modoImpresion): ?>
  <div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
    <a href="/digitalizacion-documentos/documents/show?id=carta_conocimiento_aceptacion&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
       style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4); font-family: Arial, sans-serif; font-size: 14px; font-weight: 600; transition: all 0.3s ease;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      ✏️ EDITAR
    </a>
  </div>
  <?php endif; ?>
  
  <script>
    function copiarDatosAntesDeGuardar(event) {
      // Copiar valores de contenteditable a campos ocultos
      document.getElementById('hidden_cliente_nombre_completo').value = document.getElementById('cliente_nombre_completo').textContent;
      document.getElementById('hidden_cliente_documento').value = document.getElementById('cliente_documento').textContent;
      document.getElementById('hidden_vehiculo_marca').value = document.getElementById('vehiculo_marca').textContent;
      document.getElementById('hidden_vehiculo_modelo').value = document.getElementById('vehiculo_modelo').textContent;
      document.getElementById('hidden_vehiculo_anio').value = document.getElementById('vehiculo_anio').textContent;
      document.getElementById('hidden_vehiculo_vin').value = document.getElementById('vehiculo_vin').textContent;
      // La firma del cliente ya está en firma_cliente_ruta, no necesita copiarse
      document.getElementById('hidden_nombre_firma').value = document.getElementById('nombre_firma').textContent;
      document.getElementById('hidden_documento_firma').value = document.getElementById('documento_firma').textContent;
      document.getElementById('hidden_fecha_firma').value = document.getElementById('fecha_firma').textContent;
      
      // Permitir que el formulario se envíe
      return true;
    }
    
    // Deshabilitar edición en modo visualización
    <?php if (isset($modoImpresion) && $modoImpresion): ?>
    document.addEventListener('DOMContentLoaded', function() {
      // Deshabilitar todos los contenteditable
      const editables = document.querySelectorAll('[contenteditable="true"]');
      editables.forEach(function(el) {
        el.setAttribute('contenteditable', 'false');
        el.style.cursor = 'default';
      });
      
      // Deshabilitar el click en la firma
      const firmaPreview = document.getElementById('firma-cliente-preview');
      if (firmaPreview) {
        firmaPreview.onclick = null;
        firmaPreview.style.cursor = 'default';
      }
    });
    <?php endif; ?>
  </script>

  <!-- Modal para capturar firma del cliente -->
  <div id="modalFirmaClienteCCA" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:10000; justify-content:center; align-items:center;">
    <div style="background:white; padding:30px; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,0.3); max-width:600px; width:90%;">
      <h4 style="margin:0 0 20px 0; color:#333; text-align:center;">Firma del Cliente</h4>
      <div style="border:2px solid #667eea; border-radius:10px; overflow:hidden; margin-bottom:20px;">
        <canvas id="canvasFirmaClienteCCA" width="540" height="200" style="display:block; cursor:crosshair; touch-action:none;"></canvas>
      </div>
      <p style="text-align:center; color:#666; font-size:14px; margin-bottom:20px;">Dibuje su firma usando el mouse o el dedo (en pantallas táctiles)</p>
      <div style="display:flex; gap:10px; justify-content:center;">
        <button type="button" onclick="limpiarFirmaCCA()" style="background:#dc3545; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Limpiar</button>
        <button type="button" onclick="guardarFirmaClienteCCA()" style="background:#28a745; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Guardar Firma</button>
        <button type="button" onclick="cerrarModalFirmaCCA()" style="background:#6c757d; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Cancelar</button>
      </div>
    </div>
  </div>

  <script>
    let canvasCCA, ctxCCA, dibujandoCCA = false;
    let ultimoXCCA = 0, ultimoYCCA = 0;

    function abrirCapturadorFirmaCCA() {
      const modal = document.getElementById('modalFirmaClienteCCA');
      modal.style.display = 'flex';
      canvasCCA = document.getElementById('canvasFirmaClienteCCA');
      ctxCCA = canvasCCA.getContext('2d');
      ctxCCA.strokeStyle = '#000';
      ctxCCA.lineWidth = 2;
      ctxCCA.lineCap = 'round';
      ctxCCA.lineJoin = 'round';
      ctxCCA.fillStyle = '#fff';
      ctxCCA.fillRect(0, 0, canvasCCA.width, canvasCCA.height);
      canvasCCA.addEventListener('mousedown', iniciarDibujoCCA);
      canvasCCA.addEventListener('mousemove', dibujarCCA);
      canvasCCA.addEventListener('mouseup', detenerDibujoCCA);
      canvasCCA.addEventListener('mouseout', detenerDibujoCCA);
      canvasCCA.addEventListener('touchstart', iniciarDibujoTouchCCA);
      canvasCCA.addEventListener('touchmove', dibujarTouchCCA);
      canvasCCA.addEventListener('touchend', detenerDibujoCCA);
    }

    function iniciarDibujoCCA(e) {
      dibujandoCCA = true;
      const rect = canvasCCA.getBoundingClientRect();
      ultimoXCCA = e.clientX - rect.left;
      ultimoYCCA = e.clientY - rect.top;
    }

    function dibujarCCA(e) {
      if (!dibujandoCCA) return;
      const rect = canvasCCA.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      ctxCCA.beginPath();
      ctxCCA.moveTo(ultimoXCCA, ultimoYCCA);
      ctxCCA.lineTo(x, y);
      ctxCCA.stroke();
      ultimoXCCA = x;
      ultimoYCCA = y;
    }

    function detenerDibujoCCA() {
      dibujandoCCA = false;
    }

    function iniciarDibujoTouchCCA(e) {
      e.preventDefault();
      dibujandoCCA = true;
      const rect = canvasCCA.getBoundingClientRect();
      const touch = e.touches[0];
      ultimoXCCA = touch.clientX - rect.left;
      ultimoYCCA = touch.clientY - rect.top;
    }

    function dibujarTouchCCA(e) {
      if (!dibujandoCCA) return;
      e.preventDefault();
      const rect = canvasCCA.getBoundingClientRect();
      const touch = e.touches[0];
      const x = touch.clientX - rect.left;
      const y = touch.clientY - rect.top;
      ctxCCA.beginPath();
      ctxCCA.moveTo(ultimoXCCA, ultimoYCCA);
      ctxCCA.lineTo(x, y);
      ctxCCA.stroke();
      ultimoXCCA = x;
      ultimoYCCA = y;
    }

    function limpiarFirmaCCA() {
      ctxCCA.fillStyle = '#fff';
      ctxCCA.fillRect(0, 0, canvasCCA.width, canvasCCA.height);
    }

    function guardarFirmaClienteCCA() {
      const imageData = ctxCCA.getImageData(0, 0, canvasCCA.width, canvasCCA.height);
      const pixels = imageData.data;
      let hayDibujo = false;
      for (let i = 0; i < pixels.length; i += 4) {
        if (pixels[i] !== 255 || pixels[i+1] !== 255 || pixels[i+2] !== 255) {
          hayDibujo = true;
          break;
        }
      }
      if (!hayDibujo) {
        alert('Por favor, dibuje su firma antes de guardar.');
        return;
      }
      const firmaDataURL = canvasCCA.toDataURL('image/png');
      const btnGuardar = event.target;
      const textoOriginal = btnGuardar.innerHTML;
      btnGuardar.innerHTML = 'Guardando...';
      btnGuardar.disabled = true;
      fetch('/digitalizacion-documentos/documents/guardar-firma-cliente', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'firma_base64=' + encodeURIComponent(firmaDataURL)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('firma_cliente_ruta').value = data.ruta;
          const preview = document.getElementById('firma-cliente-preview');
          preview.innerHTML = '<img src="' + data.ruta + '" style="max-width:100%; max-height:50px; display:block;">' +
                             '<img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">';
          cerrarModalFirmaCCA();
          console.log('✅ Firma guardada en:', data.ruta);
        } else {
          alert('Error al guardar la firma: ' + data.error);
          btnGuardar.innerHTML = textoOriginal;
          btnGuardar.disabled = false;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar la firma.');
        btnGuardar.innerHTML = textoOriginal;
        btnGuardar.disabled = false;
      });
    }

    function cerrarModalFirmaCCA() {
      document.getElementById('modalFirmaClienteCCA').style.display = 'none';
    }

    // Cargar firma guardada al cargar el documento
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (isset($documentData) && !empty($documentData['CCA_FIRMA_CLIENTE'])): ?>
        const firmaRuta = '<?php echo htmlspecialchars($documentData['CCA_FIRMA_CLIENTE']); ?>';
        document.getElementById('firma_cliente_ruta').value = firmaRuta;
        const preview = document.getElementById('firma-cliente-preview');
        preview.innerHTML = '<img src="' + firmaRuta + '" style="max-width:100%; max-height:50px; display:block;">' +
                           '<img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">';
        console.log('✅ Firma cargada desde BD:', firmaRuta);
      <?php else: ?>
        console.log('⚠️ No hay firma guardada en BD');
      <?php endif; ?>
    });
  </script>
  </form>
  <style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
  </style>

  <!-- Script para cargar datos de preview desde localStorage -->
  <script>
    // Verificar si es modo preview
    const urlParams = new URLSearchParams(window.location.search);
    const esPreview = urlParams.get('preview') === '1';
    const tieneOrdenId = urlParams.get('orden_id') !== null;
    
    // Si tiene orden_id, viene del expediente (usa BD, no preview)
    if (esPreview && !tieneOrdenId) {
        console.log('👁️ Modo PREVIEW activado - Carta Conocimiento');
        
        // Intentar cargar datos de localStorage
        const datosPreviewStr = localStorage.getItem('preview_orden_compra');
        
        if (datosPreviewStr) {
            try {
                const datosPreview = JSON.parse(datosPreviewStr);
                console.log('📦 Datos de preview cargados:', datosPreview);
                
                // Llenar campos con datos de preview
                if (datosPreview.comprador_nombre || datosPreview.comprador_apellido) {
                    const nombreCompleto = (datosPreview.comprador_nombre + ' ' + datosPreview.comprador_apellido).trim();
                    const elemNombre = document.getElementById('cliente_nombre_completo');
                    if (elemNombre && nombreCompleto) {
                        elemNombre.textContent = nombreCompleto;
                        console.log('✅ Nombre completo:', nombreCompleto);
                    }
                }
                
                if (datosPreview.comprador_numero_doc) {
                    const elemDoc = document.getElementById('cliente_documento');
                    if (elemDoc) {
                        elemDoc.textContent = datosPreview.comprador_numero_doc;
                        console.log('✅ Documento:', datosPreview.comprador_numero_doc);
                    }
                }
                
                if (datosPreview.vehiculo_marca) {
                    const elemMarca = document.getElementById('vehiculo_marca');
                    if (elemMarca) {
                        elemMarca.textContent = datosPreview.vehiculo_marca;
                        console.log('✅ Marca:', datosPreview.vehiculo_marca);
                    }
                }
                
                if (datosPreview.vehiculo_modelo) {
                    const elemModelo = document.getElementById('vehiculo_modelo');
                    if (elemModelo) {
                        elemModelo.textContent = datosPreview.vehiculo_modelo;
                        console.log('✅ Modelo:', datosPreview.vehiculo_modelo);
                    }
                }
                
                if (datosPreview.vehiculo_anio) {
                    const elemAnio = document.getElementById('vehiculo_anio');
                    if (elemAnio) {
                        elemAnio.textContent = datosPreview.vehiculo_anio;
                        console.log('✅ Año:', datosPreview.vehiculo_anio);
                    }
                }
                
                if (datosPreview.vehiculo_chasis) {
                    const elemVin = document.getElementById('vehiculo_vin');
                    if (elemVin) {
                        elemVin.textContent = datosPreview.vehiculo_chasis;
                        console.log('✅ VIN:', datosPreview.vehiculo_chasis);
                    }
                }
                
                // Llenar campos de firma
                if (datosPreview.comprador_nombre || datosPreview.comprador_apellido) {
                    const nombreFirma = (datosPreview.comprador_nombre + ' ' + datosPreview.comprador_apellido).trim();
                    const elemNombreFirma = document.getElementById('nombre_firma');
                    if (elemNombreFirma && nombreFirma) {
                        elemNombreFirma.textContent = nombreFirma;
                    }
                }
                
                if (datosPreview.comprador_numero_doc) {
                    const elemDocFirma = document.getElementById('documento_firma');
                    if (elemDocFirma) {
                        elemDocFirma.textContent = datosPreview.comprador_numero_doc;
                    }
                }
                
                // Mostrar firma del cliente si existe
                if (datosPreview.firma_cliente) {
                    const firmaPreview = document.getElementById('firma-cliente-preview');
                    if (firmaPreview) {
                        firmaPreview.innerHTML = '<img src="' + datosPreview.firma_cliente + '" style="max-width:100%; max-height:50px; display:block;" alt="Firma del cliente">';
                        console.log('✅ Firma del cliente cargada');
                    }
                }
                
                console.log('✅ Preview cargado exitosamente');
                
            } catch (e) {
                console.error('❌ Error al parsear datos de preview:', e);
            }
        } else {
            console.warn('⚠️ No hay datos de preview en localStorage');
        }
    }
  </script>
</body>
</html>
