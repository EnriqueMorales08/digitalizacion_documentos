<?php
date_default_timezone_set('America/Lima');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carta de Recepción de Merchandising - Interamericana</title>
  <style>
    @page {
      size: A4;
      margin: 2.5cm;
    }

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

    @media print {
      @page { 
        margin: 0; 
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
        margin-bottom: 15px !important;
      }

      .header img {
        height: 50px !important;
      }

      .title {
        font-size: 15pt !important;
        margin: 15px 0 !important;
      }

      .date-section {
        margin-bottom: 15px !important;
      }

      p, li, span {
        font-size: 15px !important;
        line-height: 1.4 !important;
        margin: 3px 0 !important;
      }

      .content {
        margin: 10px 0 !important;
      }

      .signature-section {
        margin-top: 15px !important;
      }

      @page {
        size: A4;
        margin: 10mm;
      }

      /* Ocultar bordes y placeholder de firma en impresión */
      #firma-cliente-preview-cr {
        border: none !important;
        min-height: auto !important;
      }
      
      #firma-cliente-preview-cr span {
        display: none !important;
      }
    }

    .header {
      text-align: left;
      margin-bottom: 40px;
    }

    .header img {
      height: 70px;
    }

    .title {
      text-align: center;
      font-size: 14pt;
      font-weight: bold;
      margin: 40px 0;
      text-decoration: underline;
    }

    .date-section {
      text-align: right;
      margin-bottom: 40px;
      margin-right: 25px;
      font-style: italic;
    }

    .content {
      margin-bottom: 190px;
      text-align: justify;
    }

    .paragraph {
      margin-bottom: 20px;
      margin-right: 25px;
    }

    input {
      border: none;
      border-bottom: 1px solid #000;
      font-family: inherit;
      font-size: inherit;
      padding: 2px 5px;
      background: transparent;
    }

    .short {
      width: 100px;
    }

    .medium {
      width: 200px;
    }

    .long {
      width: 350px;
    }

    .signature-section {
      margin-top: 100px;
      text-align: center;
    }

    .signature-line {
      border-top: 1px solid #000;
      width: 300px;
      margin: 20px auto;
    }

    .signature-label {
      font-weight: bold;
      margin-top: 10px;
    }
  </style>
</head>
<body>
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

  <form method="POST" action="/digitalizacion-documentos/documents/guardar-documento" style="margin: 0; padding: 0;">
  <div class="page">
    <!-- Encabezado -->
    <div class="header">
     <img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" alt="Logo Interamericana" width="200">

    </div>
    
    <!-- Título -->
    <div class="title">CARTA DE RECEPCIÓN DE MERCHANDISING</div>
    
    <!-- Fecha -->
    <div class="date-section">
      <?php 
      $fechaOrden = $ordenCompraData['OC_FECHA_ORDEN'] ?? date('Y-m-d');
      if ($fechaOrden instanceof DateTime) {
          $fechaOrden = $fechaOrden->format('Y-m-d');
      }
      ?>
      Piura, <input type="text" id="fecha-dia" name="CR_FECHA_DIA" value="<?php echo $documentData['CR_FECHA_DIA'] ?? date('d', strtotime($fechaOrden)); ?>" class="short"> / <input type="text" id="fecha-mes" name="CR_FECHA_MES" value="<?php echo $documentData['CR_FECHA_MES'] ?? date('m', strtotime($fechaOrden)); ?>" class="short"> / <input type="text" id="fecha-anio" name="CR_FECHA_ANIO" value="<?php echo $documentData['CR_FECHA_ANIO'] ?? date('Y', strtotime($fechaOrden)); ?>" class="short">
    </div>

    <!-- Contenido -->
    <div class="content">
      <div class="paragraph">
        Mediante la presente, Yo, <input type="text" id="cliente-nombre" name="CR_CLIENTE_NOMBRE" value="<?php echo htmlspecialchars($documentData['CR_CLIENTE_NOMBRE'] ?? (trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))) ?? ''); ?>" class="long">,
      </div>

      <div class="paragraph">
        identificado con DNI <input type="text" id="cliente-dni" name="CR_CLIENTE_DNI" value="<?php echo htmlspecialchars($documentData['CR_CLIENTE_DNI'] ?? $ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?>" class="short">, confirmo que en la fecha de entrega de mi vehículo marca
      </div>

      <div class="paragraph">
        <input type="text" id="vehiculo-marca" name="CR_VEHICULO_MARCA" value="<?php echo htmlspecialchars($documentData['CR_VEHICULO_MARCA'] ?? $ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?>" class="medium"> modelo <input type="text" id="vehiculo-modelo" name="CR_VEHICULO_MODELO" value="<?php echo htmlspecialchars($documentData['CR_VEHICULO_MODELO'] ?? $ordenCompraData['OC_VEHICULO_MODELO'] ?? ''); ?>" class="medium">, he recibido de parte <strong>INTERAMERICANA NORTE S.A.C.</strong>,
      </div>
      
      <div class="paragraph">
        identificado con RUC: <strong>20483998270</strong> (1) kit de merchandising / obsequio correspondiente.
      </div>
      
      <div class="paragraph" style="margin-top: 30px;">
        Asimismo, autorizo la toma de una fotografía en la que aparezco con mi vehículo y el kit recibido, la cual será utilizada únicamente para fines internos de seguimiento y control de calidad de la empresa.
      </div>
      
      <div class="paragraph" style="margin-top: 30px;">
        Conforme con la recepción del merchandising y los términos expuestos, firmo la presente como constancia.
      </div>
    </div>
    
    <!-- Firma -->
    <div class="signature-section" style="margin-top: 20px">
      <div id="firma-cliente-preview-cr" style="min-width:300px; min-height:50px; display:block; margin:0 auto 5px auto; border:1px solid #ccc; padding:5px; position:relative; cursor:pointer;" onclick="abrirCapturadorFirmaCR()">
        <span style="color:#999; font-size:11px; text-align:center; display:block;">Haga clic aquí para firmar</span>
      </div>
      <input type="hidden" name="CR_FIRMA_CLIENTE" id="firma_cliente_ruta_cr">
      <div class="signature-line"></div>
      <div class="signature-label">FIRMA Y DNI DEL CLIENTE</div>
    </div>

    <!-- Botón de guardar -->
    <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
        <input type="hidden" name="document_type" value="carta_recepcion">
        <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
            💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
        </button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($modoImpresion) && $modoImpresion): ?>
    <div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
      <a href="/digitalizacion-documentos/documents/show?id=carta_recepcion&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
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
    <?php if (isset($modoImpresion) && $modoImpresion): ?>
    document.addEventListener('DOMContentLoaded', function() {
      const inputs = document.querySelectorAll('input:not([type="hidden"]), select, textarea');
      inputs.forEach(function(el) {
        el.setAttribute('readonly', 'readonly');
        el.setAttribute('disabled', 'disabled');
        el.style.cursor = 'default';
        el.style.pointerEvents = 'none';
      });
    });
    <?php endif; ?>
    </script>

  <!-- Modal para capturar firma del cliente -->
  <div id="modalFirmaClienteCR" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:10000; justify-content:center; align-items:center;">
    <div style="background:white; padding:30px; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,0.3); max-width:600px; width:90%;">
      <h4 style="margin:0 0 20px 0; color:#333; text-align:center;">Firma del Cliente</h4>
      <div style="border:2px solid #667eea; border-radius:10px; overflow:hidden; margin-bottom:20px;">
        <canvas id="canvasFirmaClienteCR" width="540" height="200" style="display:block; cursor:crosshair; touch-action:none;"></canvas>
      </div>
      <p style="text-align:center; color:#666; font-size:14px; margin-bottom:20px;">Dibuje su firma usando el mouse o el dedo (en pantallas táctiles)</p>
      <div style="display:flex; gap:10px; justify-content:center;">
        <button type="button" onclick="limpiarFirmaCR()" style="background:#dc3545; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Limpiar</button>
        <button type="button" onclick="guardarFirmaClienteCR()" style="background:#28a745; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Guardar Firma</button>
        <button type="button" onclick="cerrarModalFirmaCR()" style="background:#6c757d; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Cancelar</button>
      </div>
    </div>
  </div>

  <script>
    let canvasCR, ctxCR, dibujandoCR = false;
    let ultimoXCR = 0, ultimoYCR = 0;

    function abrirCapturadorFirmaCR() {
      const modal = document.getElementById('modalFirmaClienteCR');
      modal.style.display = 'flex';
      canvasCR = document.getElementById('canvasFirmaClienteCR');
      ctxCR = canvasCR.getContext('2d');
      ctxCR.strokeStyle = '#000';
      ctxCR.lineWidth = 2;
      ctxCR.lineCap = 'round';
      ctxCR.lineJoin = 'round';
      ctxCR.fillStyle = '#fff';
      ctxCR.fillRect(0, 0, canvasCR.width, canvasCR.height);
      canvasCR.addEventListener('mousedown', iniciarDibujoCR);
      canvasCR.addEventListener('mousemove', dibujarCR);
      canvasCR.addEventListener('mouseup', detenerDibujoCR);
      canvasCR.addEventListener('mouseout', detenerDibujoCR);
      canvasCR.addEventListener('touchstart', iniciarDibujoTouchCR);
      canvasCR.addEventListener('touchmove', dibujarTouchCR);
      canvasCR.addEventListener('touchend', detenerDibujoCR);
    }

    function iniciarDibujoCR(e) {
      dibujandoCR = true;
      const rect = canvasCR.getBoundingClientRect();
      ultimoXCR = e.clientX - rect.left;
      ultimoYCR = e.clientY - rect.top;
    }

    function dibujarCR(e) {
      if (!dibujandoCR) return;
      const rect = canvasCR.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      ctxCR.beginPath();
      ctxCR.moveTo(ultimoXCR, ultimoYCR);
      ctxCR.lineTo(x, y);
      ctxCR.stroke();
      ultimoXCR = x;
      ultimoYCR = y;
    }

    function detenerDibujoCR() {
      dibujandoCR = false;
    }

    function iniciarDibujoTouchCR(e) {
      e.preventDefault();
      dibujandoCR = true;
      const rect = canvasCR.getBoundingClientRect();
      const touch = e.touches[0];
      ultimoXCR = touch.clientX - rect.left;
      ultimoYCR = touch.clientY - rect.top;
    }

    function dibujarTouchCR(e) {
      if (!dibujandoCR) return;
      e.preventDefault();
      const rect = canvasCR.getBoundingClientRect();
      const touch = e.touches[0];
      const x = touch.clientX - rect.left;
      const y = touch.clientY - rect.top;
      ctxCR.beginPath();
      ctxCR.moveTo(ultimoXCR, ultimoYCR);
      ctxCR.lineTo(x, y);
      ctxCR.stroke();
      ultimoXCR = x;
      ultimoYCR = y;
    }

    function limpiarFirmaCR() {
      ctxCR.fillStyle = '#fff';
      ctxCR.fillRect(0, 0, canvasCR.width, canvasCR.height);
    }

    function guardarFirmaClienteCR() {
      const imageData = ctxCR.getImageData(0, 0, canvasCR.width, canvasCR.height);
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
      const firmaDataURL = canvasCR.toDataURL('image/png');
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
          document.getElementById('firma_cliente_ruta_cr').value = data.ruta;
          const preview = document.getElementById('firma-cliente-preview-cr');
          preview.innerHTML = '<img src="' + data.ruta + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">' +
                             '<img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">';
          cerrarModalFirmaCR();
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

    function cerrarModalFirmaCR() {
      document.getElementById('modalFirmaClienteCR').style.display = 'none';
    }

    // Cargar firma guardada al cargar el documento
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (isset($documentData) && !empty($documentData['CR_FIRMA_CLIENTE'])): ?>
        const firmaRuta = '<?php echo htmlspecialchars($documentData['CR_FIRMA_CLIENTE']); ?>';
        document.getElementById('firma_cliente_ruta_cr').value = firmaRuta;
        const preview = document.getElementById('firma-cliente-preview-cr');
        preview.innerHTML = '<img src="' + firmaRuta + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">' +
                           '<img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">';
        console.log('✅ Firma cargada desde BD:', firmaRuta);
      <?php else: ?>
        console.log('⚠️ No hay firma guardada en BD');
      <?php endif; ?>
    });
  </script>
  </form>
  <script>
    // Función para ajustar el ancho del input según el texto
    function adjustInputWidth(input) {
      const canvas = document.createElement('canvas');
      const context = canvas.getContext('2d');
      context.font = getComputedStyle(input).font;
      const textWidth = context.measureText(input.value || ' ').width;
      input.style.width = Math.max(textWidth + 20, 50) + 'px'; // mínimo 50px
    }

    // Ajustar al cargar
    document.addEventListener('DOMContentLoaded', function() {
      const inputs = [
        'fecha-dia', 'fecha-mes', 'fecha-anio',
        'cliente-nombre', 'cliente-dni',
        'vehiculo-marca', 'vehiculo-modelo'
      ];

      inputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
          adjustInputWidth(input);
          input.addEventListener('input', function() {
            adjustInputWidth(this);
          });
        }
      });
    });
  </script>
  </div>

  <!-- Script para cargar datos de preview desde localStorage -->
  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const esPreview = urlParams.get('preview') === '1';
    const tieneOrdenId = urlParams.get('orden_id') !== null;
    
    // Si tiene orden_id, viene del expediente (usa BD, no preview)
    if (esPreview && !tieneOrdenId) {
        console.log('👁️ Modo PREVIEW activado - Carta Recepción');
        const datosPreviewStr = localStorage.getItem('preview_orden_compra');
        
        if (datosPreviewStr) {
            try {
                const datos = JSON.parse(datosPreviewStr);
                console.log('📦 Datos cargados:', datos);
                
                // Fecha actual
                const hoy = new Date();
                document.getElementById('fecha-dia').value = String(hoy.getDate()).padStart(2, '0');
                document.getElementById('fecha-mes').value = String(hoy.getMonth() + 1).padStart(2, '0');
                document.getElementById('fecha-anio').value = hoy.getFullYear();
                
                // Nombre completo
                if (datos.comprador_nombre || datos.comprador_apellido) {
                    const nombre = (datos.comprador_nombre + ' ' + datos.comprador_apellido).trim();
                    document.getElementById('cliente-nombre').value = nombre;
                    console.log('✅ Nombre:', nombre);
                }
                
                // DNI
                if (datos.comprador_numero_doc) {
                    document.getElementById('cliente-dni').value = datos.comprador_numero_doc;
                    console.log('✅ DNI:', datos.comprador_numero_doc);
                }
                
                // Vehículo
                if (datos.vehiculo_marca) {
                    document.getElementById('vehiculo-marca').value = datos.vehiculo_marca;
                    console.log('✅ Marca:', datos.vehiculo_marca);
                }
                
                if (datos.vehiculo_modelo) {
                    document.getElementById('vehiculo-modelo').value = datos.vehiculo_modelo;
                    console.log('✅ Modelo:', datos.vehiculo_modelo);
                }
                
                // Firma
                if (datos.firma_cliente) {
                    const firmaPreview = document.getElementById('firma-cliente-preview-cr');
                    if (firmaPreview) {
                        firmaPreview.innerHTML = '<img src="' + datos.firma_cliente + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;" alt="Firma">';
                        document.getElementById('firma_cliente_ruta_cr').value = datos.firma_cliente;
                        console.log('✅ Firma cargada');
                    }
                }
                
                console.log('✅ Preview cargado - Carta Recepción');
            } catch (e) {
                console.error('❌ Error:', e);
            }
        }
    }
  </script>
</body>
</html>
