<?php
date_default_timezone_set('America/Lima');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad - Interamericana</title>
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
        font-size: 12pt;
        line-height: 1.3;
        color: #000;
        background-color: var(--bg);
        margin: 0;
        padding: 20px;
        text-align: justify;
    }

    /* Bloque completo tipo carta */
    .page {
        width: 794px;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        border-radius: 0;
        box-shadow: none;
    }

    @media print {
        @page { 
            margin: 0; 
            size: auto;
        }
        body {
            background: #fff;
            font-size: 14px;
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
            margin-bottom: 15px !important;
        }

        p, li {
            font-size: 14.5px !important;
            line-height: 1.4 !important;
            margin: 3px 0 !important;
        }

        ul, ol {
            margin: 5px 0 !important;
            padding-left: 15px !important;
        }
        ul{
            margin-right: 25px;
        }
        section{
            margin-right: 25px;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        /* Ocultar bordes y placeholder de firma en impresión */
        #firma-cliente-preview-ppd {
            border: none !important;
            min-height: auto !important;
        }
        
        #firma-cliente-preview-ppd span {
            display: none !important;
        }
    }

    /* ==== ENCABEZADO SOLO CON LOGO ==== */
    .header {
        text-align: left;
        margin-bottom: 40px;
    }

    .header img {
        height: 70px;
        /* tamaño ajustado del logo */
    }

    .title {
        text-align: center;
        font-size: 13pt;
        font-weight: bold;
        text-decoration: underline;
        margin-bottom: 30px;
    }

    .section {
        margin-bottom: 20px;
    }

    .section ol {
        margin: 10px 0 10px 40px;
    }

    .section li {
        margin-bottom: 6px;
    }

    .anexo-title {
        font-weight: bold;
        margin: 25px 0 10px 0;
    }

    .company-list {
        list-style-type: "➤ ";
        padding-left: 25px;
    }

    .company-list li {
        margin-bottom: 10px;
    }

    .signature {
        text-align: center;
        margin-top: 100px;
    }

    .signature::after {
        content: "";
        display: block;
        width: 300px;
        /* línea larga */
        margin: 10px auto 0 auto;
        border-top: 1px solid #000;
    }

        font-size: 11pt;
        font-weight: bold;
  
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
        <!-- Encabezado con logo -->
        <div class="header">
        <img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" alt="Logo Interamericana" width="200">

    </div>

    <!-- Título -->
    <div class="title">POLÍTICA DE PRIVACIDAD – TRATAMIENTO DE DATOS</div>

    <!-- Contenido -->
    <div class="section">
        Mediante el presente documento, usted autoriza a Interamericana Norte SAC, con domicilio en Prolong. Sanchez
        Cerro Mz. 240 Lt. 02 Zona Industrial, Piura, así como Kia Import Perú S.A, con domicilio en Av. Rivera Navarrete
        Nº 495, Int 6, San Isidro y sus empresas vinculadas (en adelante, “Los Aliados”), a tratar sus datos personales
        por un plazo indeterminado o hasta que decida revocar la presente autorización. Su información será almacenada
        en los bancos de datos denominados de clientes Interamericana RNPDP-PJP*14554.
    </div>

    <div class="section">
        <strong>Las finalidades de tratamiento:</strong>
        <ol>
            <li>Establecer un medio de comunicación efectivo y eficaz para dar respuesta a las preguntas, consultas y
                sugerencias sobre los productos y/o servicios ofrecidos que son de su interés por parte de los ALIADOS.
            </li>
            <li>Procesar su información para fines estadísticos e históricos.</li>
            <li>Realizar encuestas relacionadas con la calidad del servicio brindado.</li>
            <li>Remitir a su correo electrónico, teléfono u otro canal similar: promociones, ofertas e información
                adicional sobre los bienes y servicios ofrecidos por INTERNOR SAC.</li>
        </ol>
    </div>

    <div class="section">
        <strong>Finalmente, se le ha informado que:</strong>
        <ol>
            <li>Podrá ejercer los derechos contenidos en la Ley de Protección de Datos Personales, su reglamento y
                normas modificatorias, dirigiendo una solicitud al correo <u>atcliente@interamericananorte.com</u>.</li>
            <li>Su información será tratada directamente por INTERNOR SAC, así como los terceros señalados en el
                <strong>Anexo 1</strong>.</li>
            <li>Su autorización es obligatoria para cumplir con las finalidades antes indicadas.</li>
        </ol>
    </div>

    <!-- Anexo -->
    <div class="anexo-title">ANEXO 1</div>
    <ul class="company-list">
        <li>Astara Perú SAC con dirección Av. Rivera Navarrete Nº 495, Int 6, San Isidro.</li>
        <li>Astara Chile SpA (Dirección: Américo Vespucio Nº 1561, Vitacura, Santiago de Chile), como empresa vinculada
            a Astara Perú S.A.</li>
        <li>Mercedes-Benz do Brasil Ltda. (Dirección: Av. Alfred Jurzykowski, 562, 09680-900, São Bernardo do Campo –
            Brasil).</li>
        <li>Quest Inteligencia (Dirección: Av. Francisco Glicério, 285 - Vila Lídia, Campinas - SP, 13026-501).</li>
        <li>Salesforce Latin América, con domicilio en Calle Montes Urales 424, Lomas - Virreyes, Lomas de Chapultepec V
            Secc., Miguel Hidalgo, 11000 Ciudad de México, empresa vinculada encargada del almacenamiento.</li>
        <li>Sentinel Risk S.A., con domicilio en Av. Salaverry N°2375 - San Isidro, empresa que nos da servicios de
            central de riesgo.</li>
        <li>Sendinblue, con domicilio en 7 rue de Madrid, 75008, París (Francia), con objeto de ofrecer soluciones para
            envío de correos electrónicos y SMS transaccionales y de marketing a través de su sitio web
            www.sendinblue.com.</li>
        <li>GoDaddy con domicilio en Corporate Headquarters 14455 N. Hayden Rd., Ste. 226 Scottsdale, AZ 85260 EE.UU.,
            empresa que nos brinda prestaciones de alojamiento web.</li>
        <li>Facebook Ads con domicilio en Menlo Park, California 94025, empresa que nos brinda administración de
            campañas digitales tales como anuncios publicitarios.</li>
        <li>Hubspot, con domicilio en 25 First Street, 2nd Floor Cambridge, MA 02141 Estados Unidos, empresa que nos
            brinda administración de datos del cliente obtenidos mediante campañas digitales.</li>
        <li>Walcu, Aldajo Trading S.L. B-88049705 (+34) 91 198 11 72 billing@walcu.com, empresa que nos brinda
            administración de datos del cliente obtenidos mediante campañas digitales.</li>
    </ul>

    <!-- Firma -->
    <div class="signature">
        <div id="firma-cliente-preview-ppd" style="min-width:300px; min-height:50px; display:block; margin:0 auto; border:1px solid #ccc; padding:5px; position:relative; cursor:pointer;" onclick="abrirCapturadorFirmaPPD()">
            <span style="color:#999; font-size:11px; text-align:center; display:block;">Haga clic aquí para firmar</span>
        </div>
    </div>
    <div style="text-align: center; font-weight: bold; margin-top: 5px;">Firma del cliente</div>

    <!-- Campos ocultos para guardar datos -->
    <input type="hidden" name="PPD_CLIENTE_NOMBRE" value="<?php echo htmlspecialchars($documentData['PPD_CLIENTE_NOMBRE'] ?? (trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))) ?? ''); ?>">
    <input type="hidden" name="PPD_CLIENTE_DNI" value="<?php echo htmlspecialchars($documentData['PPD_CLIENTE_DNI'] ?? $ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?>">
    <input type="hidden" name="PPD_FECHA_AUTORIZACION" value="<?php $fecha = $documentData['PPD_FECHA_AUTORIZACION'] ?? $ordenCompraData['OC_FECHA_ORDEN'] ?? date('Y-m-d'); if ($fecha instanceof DateTime) { $fecha = $fecha->format('Y-m-d'); } echo $fecha; ?>">
    <input type="hidden" name="PPD_FIRMA_CLIENTE" id="firma_cliente_ruta_ppd">

    <!-- Botón de guardar -->
    <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
        <input type="hidden" name="document_type" value="politica_proteccion_datos">
        <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
            💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
        </button>
    </div>
    <?php endif; ?>
    <?php if (isset($modoImpresion) && $modoImpresion): ?>
    <div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
      <a href="/digitalizacion-documentos/documents/show?id=politica_proteccion_datos&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
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

  <!-- Modal para capturar firma del cliente -->
  <div id="modalFirmaClientePPD" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:10000; justify-content:center; align-items:center;">
    <div style="background:white; padding:30px; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,0.3); max-width:600px; width:90%;">
      <h4 style="margin:0 0 20px 0; color:#333; text-align:center;">Firma del Cliente</h4>
      <div style="border:2px solid #667eea; border-radius:10px; overflow:hidden; margin-bottom:20px;">
        <canvas id="canvasFirmaClientePPD" width="540" height="200" style="display:block; cursor:crosshair; touch-action:none;"></canvas>
      </div>
      <p style="text-align:center; color:#666; font-size:14px; margin-bottom:20px;">Dibuje su firma usando el mouse o el dedo (en pantallas táctiles)</p>
      <div style="display:flex; gap:10px; justify-content:center;">
        <button type="button" onclick="limpiarFirmaPPD()" style="background:#dc3545; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Limpiar</button>
        <button type="button" onclick="guardarFirmaClientePPD()" style="background:#28a745; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Guardar Firma</button>
        <button type="button" onclick="cerrarModalFirmaPPD()" style="background:#6c757d; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Cancelar</button>
      </div>
    </div>
  </div>

  <script>
    let canvasPPD, ctxPPD, dibujandoPPD = false;
    let ultimoXPPD = 0, ultimoYPPD = 0;

    function abrirCapturadorFirmaPPD() {
      const modal = document.getElementById('modalFirmaClientePPD');
      modal.style.display = 'flex';
      canvasPPD = document.getElementById('canvasFirmaClientePPD');
      ctxPPD = canvasPPD.getContext('2d');
      ctxPPD.strokeStyle = '#000';
      ctxPPD.lineWidth = 2;
      ctxPPD.lineCap = 'round';
      ctxPPD.lineJoin = 'round';
      ctxPPD.fillStyle = '#fff';
      ctxPPD.fillRect(0, 0, canvasPPD.width, canvasPPD.height);
      canvasPPD.addEventListener('mousedown', iniciarDibujoPPD);
      canvasPPD.addEventListener('mousemove', dibujarPPD);
      canvasPPD.addEventListener('mouseup', detenerDibujoPPD);
      canvasPPD.addEventListener('mouseout', detenerDibujoPPD);
      canvasPPD.addEventListener('touchstart', iniciarDibujoTouchPPD);
      canvasPPD.addEventListener('touchmove', dibujarTouchPPD);
      canvasPPD.addEventListener('touchend', detenerDibujoPPD);
    }

    function iniciarDibujoPPD(e) {
      dibujandoPPD = true;
      const rect = canvasPPD.getBoundingClientRect();
      ultimoXPPD = e.clientX - rect.left;
      ultimoYPPD = e.clientY - rect.top;
    }

    function dibujarPPD(e) {
      if (!dibujandoPPD) return;
      const rect = canvasPPD.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      ctxPPD.beginPath();
      ctxPPD.moveTo(ultimoXPPD, ultimoYPPD);
      ctxPPD.lineTo(x, y);
      ctxPPD.stroke();
      ultimoXPPD = x;
      ultimoYPPD = y;
    }

    function detenerDibujoPPD() {
      dibujandoPPD = false;
    }

    function iniciarDibujoTouchPPD(e) {
      e.preventDefault();
      dibujandoPPD = true;
      const rect = canvasPPD.getBoundingClientRect();
      const touch = e.touches[0];
      ultimoXPPD = touch.clientX - rect.left;
      ultimoYPPD = touch.clientY - rect.top;
    }

    function dibujarTouchPPD(e) {
      if (!dibujandoPPD) return;
      e.preventDefault();
      const rect = canvasPPD.getBoundingClientRect();
      const touch = e.touches[0];
      const x = touch.clientX - rect.left;
      const y = touch.clientY - rect.top;
      ctxPPD.beginPath();
      ctxPPD.moveTo(ultimoXPPD, ultimoYPPD);
      ctxPPD.lineTo(x, y);
      ctxPPD.stroke();
      ultimoXPPD = x;
      ultimoYPPD = y;
    }

    function limpiarFirmaPPD() {
      ctxPPD.fillStyle = '#fff';
      ctxPPD.fillRect(0, 0, canvasPPD.width, canvasPPD.height);
    }

    function guardarFirmaClientePPD() {
      const imageData = ctxPPD.getImageData(0, 0, canvasPPD.width, canvasPPD.height);
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
      const firmaDataURL = canvasPPD.toDataURL('image/png');
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
          document.getElementById('firma_cliente_ruta_ppd').value = data.ruta;
          const preview = document.getElementById('firma-cliente-preview-ppd');
          preview.innerHTML = '<img src="' + data.ruta + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">' +
                             '<img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">';
          cerrarModalFirmaPPD();
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

    function cerrarModalFirmaPPD() {
      document.getElementById('modalFirmaClientePPD').style.display = 'none';
    }

    // Cargar firma guardada al cargar el documento
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (isset($documentData) && !empty($documentData['PPD_FIRMA_CLIENTE'])): ?>
        const firmaRuta = '<?php echo htmlspecialchars($documentData['PPD_FIRMA_CLIENTE']); ?>';
        document.getElementById('firma_cliente_ruta_ppd').value = firmaRuta;
        const preview = document.getElementById('firma-cliente-preview-ppd');
        preview.innerHTML = '<img src="' + firmaRuta + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">' +
                           '<img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">';
        console.log('✅ Firma cargada desde BD:', firmaRuta);
      <?php else: ?>
        console.log('⚠️ No hay firma guardada en BD');
      <?php endif; ?>
    });
  </script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
    </div>

    <!-- Preview desde localStorage -->
    <script>
      const urlParams = new URLSearchParams(window.location.search);
      const esPreview = urlParams.get('preview') === '1';
      const tieneOrdenId = urlParams.get('orden_id') !== null;
      
      if (esPreview && !tieneOrdenId) {
          console.log('👁️ Modo PREVIEW - Política Protección Datos');
          const datosStr = localStorage.getItem('preview_orden_compra');
          
          if (datosStr) {
              try {
                  const datos = JSON.parse(datosStr);
                  console.log('📦 Datos cargados:', datos);
                  
                  const nombreCompleto = (datos.comprador_nombre + ' ' + datos.comprador_apellido).trim();
                  
                  // Llenar campos ocultos por name
                  document.querySelector('[name="PPD_CLIENTE_NOMBRE"]').value = nombreCompleto;
                  document.querySelector('[name="PPD_CLIENTE_DNI"]').value = datos.comprador_numero_doc || '';
                  
                  // Fecha actual
                  const hoy = new Date();
                  document.querySelector('[name="PPD_FECHA_AUTORIZACION"]').value = hoy.toISOString().split('T')[0];
                  
                  // Firma
                  if (datos.firma_cliente) {
                      const firmaPreview = document.getElementById('firma-cliente-preview-ppd');
                      if (firmaPreview) {
                          firmaPreview.innerHTML = '<img src="' + datos.firma_cliente + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;" alt="Firma">';
                          document.getElementById('firma_cliente_ruta_ppd').value = datos.firma_cliente;
                          console.log('✅ Firma cargada');
                      }
                  }
                  
                  console.log('✅ Preview cargado - Política Protección Datos');
              } catch (e) {
                  console.error('❌ Error:', e);
              }
          }
      }
    </script>
</body>

</html>
