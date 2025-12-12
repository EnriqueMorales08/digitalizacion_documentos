<?php
date_default_timezone_set('America/Lima');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acta de Conocimiento y Conformidad</title>
  <style>
    :root {
      --primary: #1e3a8a;
      --ink: #111827;
      --bg: #f8f9fa;
    }

    body {
      font-family: "Times New Roman", serif;
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
      line-height: 1.6;
      color: #000;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
    }

    .title {
      font-size: 20pt;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 30px;
    }

    .location-date {
      font-weight: bold;
      margin-bottom: 25px;
    }

    .content {
      text-align: justify;
      margin-bottom: 20px;
    }

    .declaration {
      margin: 30px 0;
    }

    .declaration-title {
      font-weight: bold;
      margin-bottom: 15px;
    }

    .declaration-list {
      padding-left: 20px;
    }

    .declaration-list li {
      margin-bottom: 15px;
      text-align: justify;
    }

    .vehicle-data {
      margin: 30px 0;
    }

    .vehicle-data-title {
      font-weight: bold;
      margin-bottom: 15px;
    }

    .vehicle-list {
      list-style: disc;
      padding-left: 20px;
    }

    .vehicle-list li {
      margin-bottom: 10px;
    }

    input[type="text"], input[type="date"] {
      border: none;
      border-bottom: 1px solid #000;
      background: transparent;
      font-family: inherit;
      font-size: 12pt;
      padding: 2px 4px;
      min-width: 150px;
    }

    input:focus {
      outline: none;
      border-bottom: 2px solid #000;
    }

    .short { width: 100px; }
    .medium { width: 200px; }
    .long { width: 350px; }

    .signature-section {
      margin-top: 60px;
      font-size: 12pt;
    }

    .signature-section p {
      margin-bottom: 25px;
    }

    @media print {
      @page { 
        margin: 8mm 8mm 8mm 3mm;
        size: auto;
      }
      body {
        background: #fff;
        font-size: 15px;
        margin: 0;
        padding: 10mm;
      }
      html {
        margin: 0;
        padding: 0;
      }

      .page {
        box-shadow: none;
        border-radius: 0;
        padding: 15px !important;
      }

      .no-print {
        display: none !important;
      }

      .header {
        margin-bottom: 10px !important;
      }

      .header img {
        height: 50px !important;
      }

      h2 {
        font-size: 15pt !important;
        margin: 10px 0 !important;
      }

      p, li {
        font-size: 15px !important;
        line-height: 1.35 !important;
        margin: 3px 0 !important;
      }

      .firma-section {
        margin-top: 15px !important;
      }

      .firma-box {
        height: 50px !important;
      }

      ul, ol {
        margin: 5px 0 !important;
        padding-left: 15px !important;
      }

      /* Ocultar bordes y placeholder de firma en impresión */
      #firma-cliente-preview-acc {
        border: none !important;
        min-height: auto !important;
      }
      
      #firma-cliente-preview-acc span {
        display: none !important;
      }
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
  <div style="position: fixed; top: 20px; left: 20px; z-index: 1000;" class="no-print">
    <a href="<?= $urlRegreso ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3); font-family: Arial, sans-serif; font-size: 14px; font-weight: 500; transition: all 0.3s ease;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Regresar
    </a>
  </div>

  <div class="page">
    <div class="header">
      <div class="title">ACTA DE CONOCIMIENTO Y CONFORMIDAD</div>
    </div>

  <div class="location-date">
    <strong>Lugar y Fecha:</strong> Piura, Perú; <input type="date" name="ACC_FECHA_ACTA" class="short" value="<?php $fechaOrden = $documentData['ACC_FECHA_ACTA'] ?? ($ordenCompraData['OC_FECHA_ORDEN'] ?? date('Y-m-d')); if ($fechaOrden instanceof DateTime) { $fechaOrden = $fechaOrden->format('Y-m-d'); } echo htmlspecialchars($fechaOrden); ?>">
  </div>

  <div class="content">
    Yo, <input type="text" name="ACC_NOMBRE_CLIENTE" class="long" value="<?php echo htmlspecialchars($documentData['ACC_NOMBRE_CLIENTE'] ?? (trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))) ?? ''); ?>" placeholder="Nombre Completo del Cliente">,
    identificado con DNI N.º <input type="text" name="ACC_DNI_CLIENTE" class="medium" value="<?php echo htmlspecialchars($documentData['ACC_DNI_CLIENTE'] ?? $ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?>" placeholder="Número de DNI">,
    en calidad de propietario del vehículo detallado más adelante, declaro haber sido informado y comprender plenamente las implicancias de la instalación de un sistema de Gas Licuado de Petróleo (GLP) de manera local en mi unidad vehicular.
  </div>

  <div class="declaration">
    <div class="declaration-title">Declaro lo siguiente:</div>
    <ol class="declaration-list">
      <li>He sido informado de que la instalación de un sistema GLP no autorizada por el fabricante o fuera de los centros oficiales de servicio implica la pérdida total de la garantía otorgada por la marca del vehículo.</li>
      <li>Entiendo que, a partir de la instalación del sistema GLP, ni el fabricante ni Interamericana Norte se hacen responsables por cualquier falla, desperfecto o daño que pueda presentarse en el vehículo, ya sea en componentes mecánicos, eléctricos, electrónicos o de cualquier otra índole.</li>
      <li>Reconozco que la instalación del sistema GLP se realiza bajo mi entera responsabilidad y que he sido debidamente informado sobre los riesgos y consecuencias que esta modificación puede acarrear la pérdida de LA GARANTÍA DE FÁBRICA.</li>
      <li>Acepto que cualquier reclamo relacionado con el funcionamiento del vehículo posterior a la instalación del sistema GLP será de mi exclusiva responsabilidad, eximiendo de toda obligación o compromiso a la marca fabricante y a <input type="text" name="ACC_EMPRESA_INSTALADORA" class="medium" value="Interamericana Norte SAC" placeholder="Nombre de la Empresa Instaladora">.</li>
    </ol>
  </div>

  <div class="vehicle-data">
    <div class="vehicle-data-title">Datos del Vehículo:</div>
    <ul class="vehicle-list">
      <li><strong>Boleta/Factura de Venta N.º:</strong> <input type="text" name="ACC_BOLETA_FACTURA_NUMERO" class="medium" value="<?php echo htmlspecialchars($documentData['ACC_BOLETA_FACTURA_NUMERO'] ?? $ordenCompraData['OC_ID'] ?? ''); ?>" placeholder="N.º Boleta o Factura"></li>
      <li><strong>Nombre del Cliente:</strong> <input type="text" name="ACC_CLIENTE_VEHICULO" class="long" value="<?php echo htmlspecialchars($documentData['ACC_CLIENTE_VEHICULO'] ?? trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))); ?>" placeholder="Nombre Completo"></li>
      <li><strong>Fecha de Venta:</strong> <input type="date" name="ACC_FECHA_VENTA" class="medium" value="<?php echo htmlspecialchars($documentData['ACC_FECHA_VENTA'] instanceof DateTime ? $documentData['ACC_FECHA_VENTA']->format('Y-m-d') : ($documentData['ACC_FECHA_VENTA'] ?? $ordenCompraData['OC_FECHA_ORDEN'] ?? '')); ?>"></li>
      <li><strong>Marca:</strong> <input type="text" name="ACC_MARCA_VEHICULO" class="medium" value="<?php echo htmlspecialchars($documentData['ACC_MARCA_VEHICULO'] ?? $ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?>" placeholder="Marca"></li>
      <li><strong>Modelo:</strong> <input type="text" name="ACC_MODELO_VEHICULO" class="medium" value="<?php echo htmlspecialchars($documentData['ACC_MODELO_VEHICULO'] ?? $ordenCompraData['OC_VEHICULO_MODELO'] ?? ''); ?>" placeholder="Modelo"></li>
      <li><strong>Año:</strong> <input type="text" name="ACC_ANIO_VEHICULO" class="short" value="<?php echo htmlspecialchars($documentData['ACC_ANIO_VEHICULO'] ?? $ordenCompraData['OC_VEHICULO_ANIO_MODELO'] ?? ''); ?>" placeholder="Año"></li>
      <li><strong>VIN (Número de Identificación Vehicular):</strong> <input type="text" name="ACC_VIN_VEHICULO" class="long" value="<?php echo htmlspecialchars($documentData['ACC_VIN_VEHICULO'] ?? $ordenCompraData['OC_VEHICULO_CHASIS'] ?? ''); ?>" placeholder="N.º VIN"></li>
      <li><strong>Color:</strong> <input type="text" name="ACC_COLOR_VEHICULO" class="medium" value="<?php echo htmlspecialchars($documentData['ACC_COLOR_VEHICULO'] ?? $ordenCompraData['OC_VEHICULO_COLOR'] ?? ''); ?>" placeholder="Color"></li>
    </ul>
  </div>

  <div class="content">
    Con mi firma, dejo constancia de haber leído, comprendido y aceptado en su totalidad los términos expuestos en la presente acta.
  </div>

  <div class="signature-section">
    <p>
      <strong>Firma del Cliente:</strong> 
      <span id="firma-cliente-preview-acc" style="display:inline-block; min-width:200px; min-height:50px; border:1px solid #ccc; padding:5px; position:relative; cursor:pointer; vertical-align:middle; margin-left:10px;" onclick="abrirCapturadorFirmaACC()">
        <span class="firma-placeholder" style="color:#999; font-size:11px;">Haga clic aquí para firmar</span>
      </span>
    </p>
    <p><strong>Nombre del Cliente:</strong> <input type="text" name="ACC_NOMBRE_FIRMA" class="long" value="<?php echo htmlspecialchars($documentData['ACC_NOMBRE_FIRMA'] ?? trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))); ?>"></p>
    <p><strong>DNI:</strong> <input type="text" name="ACC_DNI_FIRMA" class="medium" value="<?php echo htmlspecialchars($documentData['ACC_DNI_FIRMA'] ?? $ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?>"></p>
  </div>

  <!-- Campo oculto para firma (siempre presente) -->
  <input type="hidden" name="ACC_FIRMA_CLIENTE" id="firma_cliente_ruta_acc">

  <!-- Botón de guardar -->
  <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
  <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
    <input type="hidden" name="document_type" value="acta-conocimiento-conformidad">
    <button type="submit" onclick="return copiarFirmaACC()" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
        💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
    </button>
  </div>
  <?php endif; ?>
  
  <!-- Botón de EDITAR cuando está en modo visualización -->
  <?php if (isset($modoImpresion) && $modoImpresion): ?>
  <div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
    <a href="/digitalizacion-documentos/documents/show?id=acta-conocimiento-conformidad&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
       style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4); font-family: Arial, sans-serif; font-size: 14px; font-weight: 600; transition: all 0.3s ease;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      ✏️ EDITAR
    </a>
  </div>
  <?php endif; ?>
  </div>

  <script>
    function copiarFirmaACC() {
      // Copiar ruta de firma al campo oculto
      const firmaRuta = document.getElementById('firma_cliente_ruta_acc').value;
      if (!firmaRuta) {
        alert('Por favor, agregue la firma del cliente antes de guardar.');
        return false;
      }
      return true;
    }
  </script>

  <!-- Modal para capturar firma del cliente -->
  <div id="modalFirmaClienteACC" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:10000; justify-content:center; align-items:center;">
    <div style="background:white; padding:30px; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,0.3); max-width:600px; width:90%;">
      <h4 style="margin:0 0 20px 0; color:#333; text-align:center;">Firma del Cliente</h4>
      <div style="border:2px solid #667eea; border-radius:10px; overflow:hidden; margin-bottom:20px;">
        <canvas id="canvasFirmaClienteACC" width="540" height="200" style="display:block; cursor:crosshair; touch-action:none;"></canvas>
      </div>
      <p style="text-align:center; color:#666; font-size:14px; margin-bottom:20px;">Dibuje su firma usando el mouse o el dedo (en pantallas táctiles)</p>
      <div style="display:flex; gap:10px; justify-content:center;">
        <button type="button" onclick="limpiarFirmaACC()" style="background:#dc3545; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Limpiar</button>
        <button type="button" onclick="guardarFirmaClienteACC()" style="background:#28a745; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Guardar Firma</button>
        <button type="button" onclick="cerrarModalFirmaACC()" style="background:#6c757d; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Cancelar</button>
      </div>
    </div>
  </div>

  <script>
    let canvasACC, ctxACC, dibujandoACC = false;
    let ultimoXACC = 0, ultimoYACC = 0;

    function abrirCapturadorFirmaACC() {
      const modal = document.getElementById('modalFirmaClienteACC');
      modal.style.display = 'flex';
      canvasACC = document.getElementById('canvasFirmaClienteACC');
      ctxACC = canvasACC.getContext('2d');
      ctxACC.strokeStyle = '#000';
      ctxACC.lineWidth = 2;
      ctxACC.lineCap = 'round';
      ctxACC.lineJoin = 'round';
      ctxACC.fillStyle = '#fff';
      ctxACC.fillRect(0, 0, canvasACC.width, canvasACC.height);
      canvasACC.addEventListener('mousedown', iniciarDibujoACC);
      canvasACC.addEventListener('mousemove', dibujarACC);
      canvasACC.addEventListener('mouseup', detenerDibujoACC);
      canvasACC.addEventListener('mouseout', detenerDibujoACC);
      canvasACC.addEventListener('touchstart', iniciarDibujoTouchACC);
      canvasACC.addEventListener('touchmove', dibujarTouchACC);
      canvasACC.addEventListener('touchend', detenerDibujoACC);
    }

    function iniciarDibujoACC(e) {
      dibujandoACC = true;
      const rect = canvasACC.getBoundingClientRect();
      ultimoXACC = e.clientX - rect.left;
      ultimoYACC = e.clientY - rect.top;
    }

    function dibujarACC(e) {
      if (!dibujandoACC) return;
      const rect = canvasACC.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      ctxACC.beginPath();
      ctxACC.moveTo(ultimoXACC, ultimoYACC);
      ctxACC.lineTo(x, y);
      ctxACC.stroke();
      ultimoXACC = x;
      ultimoYACC = y;
    }

    function detenerDibujoACC() {
      dibujandoACC = false;
    }

    function iniciarDibujoTouchACC(e) {
      e.preventDefault();
      dibujandoACC = true;
      const rect = canvasACC.getBoundingClientRect();
      const touch = e.touches[0];
      ultimoXACC = touch.clientX - rect.left;
      ultimoYACC = touch.clientY - rect.top;
    }

    function dibujarTouchACC(e) {
      if (!dibujandoACC) return;
      e.preventDefault();
      const rect = canvasACC.getBoundingClientRect();
      const touch = e.touches[0];
      const x = touch.clientX - rect.left;
      const y = touch.clientY - rect.top;
      ctxACC.beginPath();
      ctxACC.moveTo(ultimoXACC, ultimoYACC);
      ctxACC.lineTo(x, y);
      ctxACC.stroke();
      ultimoXACC = x;
      ultimoYACC = y;
    }

    function limpiarFirmaACC() {
      ctxACC.fillStyle = '#fff';
      ctxACC.fillRect(0, 0, canvasACC.width, canvasACC.height);
    }

    function guardarFirmaClienteACC() {
      const imageData = ctxACC.getImageData(0, 0, canvasACC.width, canvasACC.height);
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
      const firmaDataURL = canvasACC.toDataURL('image/png');
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
          document.getElementById('firma_cliente_ruta_acc').value = data.ruta;
          const preview = document.getElementById('firma-cliente-preview-acc');
          preview.innerHTML = '<img src="' + data.ruta + '" style="max-width:100%; max-height:50px; display:block;">' +
                             '<img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">';
          cerrarModalFirmaACC();
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

    function cerrarModalFirmaACC() {
      document.getElementById('modalFirmaClienteACC').style.display = 'none';
    }

    // Cargar firma guardada al cargar el documento
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (isset($documentData) && !empty($documentData['ACC_FIRMA_CLIENTE'])): ?>
        const firmaRuta = '<?php echo htmlspecialchars($documentData['ACC_FIRMA_CLIENTE']); ?>';
        document.getElementById('firma_cliente_ruta_acc').value = firmaRuta;
        const preview = document.getElementById('firma-cliente-preview-acc');
        preview.innerHTML = '<img src="' + firmaRuta + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">' +
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

  <!-- Preview desde localStorage -->
  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const esPreview = urlParams.get('preview') === '1';
    const tieneOrdenId = urlParams.get('orden_id') !== null;
    
    if (esPreview && !tieneOrdenId) {
        console.log('👁️ Modo PREVIEW - Acta Conocimiento Conformidad');
        const datosStr = localStorage.getItem('preview_orden_compra');
        
        if (datosStr) {
            try {
                const datos = JSON.parse(datosStr);
                console.log('📦 Datos cargados:', datos);
                
                const nombreCompleto = (datos.comprador_nombre + ' ' + datos.comprador_apellido).trim();
                
                // Llenar campos por name
                document.querySelector('[name="ACC_NOMBRE_CLIENTE"]').value = nombreCompleto;
                document.querySelector('[name="ACC_DNI_CLIENTE"]').value = datos.comprador_numero_doc || '';
                document.querySelector('[name="ACC_EMPRESA_INSTALADORA"]').value = 'Interamericana Norte SAC';
                
                // Datos del vehículo
                document.querySelector('[name="ACC_CLIENTE_VEHICULO"]').value = nombreCompleto;
                document.querySelector('[name="ACC_MARCA_VEHICULO"]').value = datos.vehiculo_marca || '';
                document.querySelector('[name="ACC_MODELO_VEHICULO"]').value = datos.vehiculo_modelo || '';
                document.querySelector('[name="ACC_ANIO_VEHICULO"]').value = datos.vehiculo_anio || '';
                document.querySelector('[name="ACC_VIN_VEHICULO"]').value = datos.vehiculo_chasis || '';
                document.querySelector('[name="ACC_COLOR_VEHICULO"]').value = datos.vehiculo_color || '';
                
                // Firma
                document.querySelector('[name="ACC_NOMBRE_FIRMA"]').value = nombreCompleto;
                document.querySelector('[name="ACC_DNI_FIRMA"]').value = datos.comprador_numero_doc || '';
                
                if (datos.firma_cliente) {
                    const firmaPreview = document.getElementById('firma-cliente-preview-acc');
                    if (firmaPreview) {
                        firmaPreview.innerHTML = '<img src="' + datos.firma_cliente + '" style="max-width:100%; max-height:50px; display:block;" alt="Firma">';
                        document.getElementById('firma_cliente_ruta_acc').value = datos.firma_cliente;
                    }
                }
                
                console.log('✅ Preview cargado - Acta');
            } catch (e) {
                console.error('❌ Error:', e);
            }
        }
    }
  </script>
</body>
</html>
