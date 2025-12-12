<?php
date_default_timezone_set('America/Lima');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorización de Uso de Imagen</title>
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
            font-size: 12pt;
        }

        /* Bloque completo tipo carta */
        .page {
            width: 794px;
            margin: 40px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
            line-height: 1.6;
            color: #000;
        }

        @media print {
            @page { 
                margin: 8mm 8mm 8mm 3mm;
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

            /* Ocultar bordes y placeholder de firma en impresión */
            #firma-cliente-preview-adp {
                border: none !important;
                min-height: auto !important;
            }
            
            #firma-cliente-preview-adp span {
                display: none !important;
            }
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            height: 70px;
        }

        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 30px 0;
            text-transform: uppercase;
        }

        .content {
            text-align: justify;
            margin-bottom: 40px;
            margin-right: 25px;
        }

        .content p {
            margin-bottom: 15px;
        }

        .numbered-list {
            margin: 20px 0 20px 40px;
        }

        .numbered-list li {
            margin-bottom: 10px;
        }

        .signature-section {
            margin-top: 110px;
            text-align: center;
            margin-right: 25px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 280px;
            margin: 0 auto 8px auto;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-fields {
            margin-top: 30px;
            text-align: left;
            max-width: 350px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        .form-group input {
            border: none;
            border-bottom: 1px solid #000;
            background: transparent;
            width: 230px;
            font-size: 12pt;
        }

        .form-group input:focus {
            outline: none;
            border-bottom: 2px solid #2c5aa0;
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
      // Si es cajera, regresar a la vista de cajera
      $urlRegreso = '/digitalizacion-documentos/cajera/ver?token=' . urlencode($tokenCajera);
  } elseif (isset($_SESSION['orden_id']) && $_SESSION['orden_id']) {
      $urlRegreso = '/digitalizacion-documentos/expedientes/ver?id=' . $_SESSION['orden_id'];
  } else {
      $urlRegreso = '/digitalizacion-documentos/documents';
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
        <!-- Encabezado -->
        <div class="header">
           <img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" alt="Logo Interamericana" width="200">

        </div>

        <!-- Título -->
        <div class="title">
            AUTORIZACIÓN DE USO DE IMAGEN EN REDES SOCIALES Y PUBLICIDAD
        </div>

        <!-- Contenido -->
        <div class="content">
            <p>
                Por el presente documento, otorgo libremente mi autorización para que mis datos personales sean tratados y almacenados en el banco de datos denominado INTERAMERICANA NORTE S.A.C (en adelante, Interamericana), debidamente identificada con RUC N° 20483998270 por un plazo indeterminado, para fines netamente comerciales y publicitarios, ello bajo el amparo de la Ley N° 29733, Ley de Protección de Datos Personales.
            </p>

            <p>
                En ese sentido, brindo mi consentimiento libre, informado, expreso e inequívoco para que Interamericana pueda recopilar, registrar, organizar, almacenar, conservar, utilizar, difundir y/o transferir a terceros a nivel nacional y/o internacional y, en general, realizar el tratamiento de sus datos personales, conforme al siguiente detalle:
            </p>

            <ol class="numbered-list">
                <li>Gestionar la reproducción de material publicitario físico respecto de nuestros productos y servicios referidos a la venta de vehículos.</li>
                <li>Difusión de la imagen a través de redes sociales, servicios de Mailing y página web institucional, así como cualquier otro medio digital existente o por existir con fines publicitarios y de comunicación de los servicios que presta Interamericana.</li>
            </ol>

            <p>
                Finalmente, Interamericana me ha informado que podré ejercer mis derechos de Acceso, Rectificación, Cancelación y Oposición (ARCO) a través de comunicación escrita en el domicilio fiscal de Interamericana.
            </p>
        </div>

        <!-- Firma -->
        <div class="signature-section">
            <div id="firma-cliente-preview-adp" style="min-width:280px; min-height:50px; display:block; margin:0 auto 5px auto; border:1px solid #ccc; padding:5px; position:relative; cursor:pointer;" onclick="abrirCapturadorFirmaADP()">
                <span style="color:#999; font-size:11px; text-align:center; display:block;">Haga clic aquí para firmar</span>
            </div>
            <input type="hidden" name="ADP_FIRMA_CLIENTE" id="firma_cliente_ruta_adp">
            <div class="signature-line"></div>
            <div class="signature-label">FIRMA DEL TITULAR</div>

            <div class="form-fields">
                <div class="form-group">
                    <label for="nombre_autorizacion">NOMBRE:</label>
                    <input type="text" id="nombre_autorizacion" name="ADP_NOMBRE_AUTORIZACION" value="<?php echo htmlspecialchars($documentData['ADP_NOMBRE_AUTORIZACION'] ?? (trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))) ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="dni_autorizacion">D.N.I./C.E.:</label>
                    <input type="text" id="dni_autorizacion" name="ADP_DNI_AUTORIZACION" value="<?php echo htmlspecialchars($documentData['ADP_DNI_AUTORIZACION'] ?? $ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="fecha_autorizacion">FECHA:</label>
                    <input type="text" id="fecha_autorizacion_display" value="<?php $fecha = $documentData['ADP_FECHA_AUTORIZACION'] ?? $ordenCompraData['OC_FECHA_ORDEN'] ?? date('Y-m-d'); if ($fecha instanceof DateTime) { echo $fecha->format('d/m/Y'); } else { echo date('d/m/Y', strtotime($fecha)); } ?>" readonly style="cursor: default;">
                    <input type="hidden" id="fecha_autorizacion" name="ADP_FECHA_AUTORIZACION" value="<?php $fecha = $documentData['ADP_FECHA_AUTORIZACION'] ?? $ordenCompraData['OC_FECHA_ORDEN'] ?? date('Y-m-d'); if ($fecha instanceof DateTime) { echo $fecha->format('Y-m-d'); } else { echo date('Y-m-d', strtotime($fecha)); } ?>">
                </div>
            </div>
        </div>
    </div>
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
      const inputs = document.querySelectorAll('input[type="text"], input[type="date"]');
      inputs.forEach(input => {
        adjustInputWidth(input);
        input.addEventListener('input', function() {
          adjustInputWidth(this);
        });
      });

      // Cargar firma guardada al cargar el documento
      <?php if (isset($documentData) && !empty($documentData['ADP_FIRMA_CLIENTE'])): ?>
        const firmaRuta = '<?php echo htmlspecialchars($documentData['ADP_FIRMA_CLIENTE']); ?>';
        document.getElementById('firma_cliente_ruta_adp').value = firmaRuta;
        const preview = document.getElementById('firma-cliente-preview-adp');
        preview.innerHTML = '<img src="' + firmaRuta + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">' +
                           '<img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">';
        console.log('✅ Firma cargada desde BD:', firmaRuta);
      <?php else: ?>
        console.log('⚠️ No hay firma guardada en BD');
      <?php endif; ?>
    });
  </script>

  <!-- Botón de guardar -->
  <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
  <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
    <input type="hidden" name="document_type" value="actorizacion-datos-personales">
    <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
        💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
    </button>
  </div>
  <?php endif; ?>
  
  <!-- Botón de EDITAR cuando está en modo visualización -->
  <?php if (isset($modoImpresion) && $modoImpresion): ?>
  <div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
    <a href="/digitalizacion-documentos/documents/show?id=actorizacion-datos-personales&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
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
  <div id="modalFirmaClienteADP" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:10000; justify-content:center; align-items:center;">
    <div style="background:white; padding:30px; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,0.3); max-width:600px; width:90%;">
      <h4 style="margin:0 0 20px 0; color:#333; text-align:center;">Firma del Cliente</h4>
      <div style="border:2px solid #667eea; border-radius:10px; overflow:hidden; margin-bottom:20px;">
        <canvas id="canvasFirmaClienteADP" width="540" height="200" style="display:block; cursor:crosshair; touch-action:none;"></canvas>
      </div>
      <p style="text-align:center; color:#666; font-size:14px; margin-bottom:20px;">Dibuje su firma usando el mouse o el dedo (en pantallas táctiles)</p>
      <div style="display:flex; gap:10px; justify-content:center;">
        <button type="button" onclick="limpiarFirmaADP()" style="background:#dc3545; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Limpiar</button>
        <button type="button" onclick="guardarFirmaClienteADP()" style="background:#28a745; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Guardar Firma</button>
        <button type="button" onclick="cerrarModalFirmaADP()" style="background:#6c757d; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Cancelar</button>
      </div>
    </div>
  </div>

  <script>
    let canvasADP, ctxADP, dibujandoADP = false;
    let ultimoXADP = 0, ultimoYADP = 0;

    function abrirCapturadorFirmaADP() {
      const modal = document.getElementById('modalFirmaClienteADP');
      modal.style.display = 'flex';
      canvasADP = document.getElementById('canvasFirmaClienteADP');
      ctxADP = canvasADP.getContext('2d');
      ctxADP.strokeStyle = '#000';
      ctxADP.lineWidth = 2;
      ctxADP.lineCap = 'round';
      ctxADP.lineJoin = 'round';
      ctxADP.fillStyle = '#fff';
      ctxADP.fillRect(0, 0, canvasADP.width, canvasADP.height);
      canvasADP.addEventListener('mousedown', iniciarDibujoADP);
      canvasADP.addEventListener('mousemove', dibujarADP);
      canvasADP.addEventListener('mouseup', detenerDibujoADP);
      canvasADP.addEventListener('mouseout', detenerDibujoADP);
      canvasADP.addEventListener('touchstart', iniciarDibujoTouchADP);
      canvasADP.addEventListener('touchmove', dibujarTouchADP);
      canvasADP.addEventListener('touchend', detenerDibujoADP);
    }

    function iniciarDibujoADP(e) {
      dibujandoADP = true;
      const rect = canvasADP.getBoundingClientRect();
      ultimoXADP = e.clientX - rect.left;
      ultimoYADP = e.clientY - rect.top;
    }

    function dibujarADP(e) {
      if (!dibujandoADP) return;
      const rect = canvasADP.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      ctxADP.beginPath();
      ctxADP.moveTo(ultimoXADP, ultimoYADP);
      ctxADP.lineTo(x, y);
      ctxADP.stroke();
      ultimoXADP = x;
      ultimoYADP = y;
    }

    function detenerDibujoADP() {
      dibujandoADP = false;
    }

    function iniciarDibujoTouchADP(e) {
      e.preventDefault();
      dibujandoADP = true;
      const rect = canvasADP.getBoundingClientRect();
      const touch = e.touches[0];
      ultimoXADP = touch.clientX - rect.left;
      ultimoYADP = touch.clientY - rect.top;
    }

    function dibujarTouchADP(e) {
      if (!dibujandoADP) return;
      e.preventDefault();
      const rect = canvasADP.getBoundingClientRect();
      const touch = e.touches[0];
      const x = touch.clientX - rect.left;
      const y = touch.clientY - rect.top;
      ctxADP.beginPath();
      ctxADP.moveTo(ultimoXADP, ultimoYADP);
      ctxADP.lineTo(x, y);
      ctxADP.stroke();
      ultimoXADP = x;
      ultimoYADP = y;
    }

    function limpiarFirmaADP() {
      ctxADP.fillStyle = '#fff';
      ctxADP.fillRect(0, 0, canvasADP.width, canvasADP.height);
    }

    function guardarFirmaClienteADP() {
      const imageData = ctxADP.getImageData(0, 0, canvasADP.width, canvasADP.height);
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
      const firmaDataURL = canvasADP.toDataURL('image/png');
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
          document.getElementById('firma_cliente_ruta_adp').value = data.ruta;
          const preview = document.getElementById('firma-cliente-preview-adp');
          preview.innerHTML = '<img src="' + data.ruta + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">' +
                             '<img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">';
          cerrarModalFirmaADP();
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

    function cerrarModalFirmaADP() {
      document.getElementById('modalFirmaClienteADP').style.display = 'none';
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

  <!-- Preview desde localStorage -->
  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const esPreview = urlParams.get('preview') === '1';
    const tieneOrdenId = urlParams.get('orden_id') !== null;
    
    if (esPreview && !tieneOrdenId) {
        console.log('👁️ Modo PREVIEW - Autorización Datos Personales');
        const datosStr = localStorage.getItem('preview_orden_compra');
        
        if (datosStr) {
            try {
                const datos = JSON.parse(datosStr);
                console.log('📦 Datos cargados:', datos);
                
                const nombreCompleto = (datos.comprador_nombre + ' ' + datos.comprador_apellido).trim();
                
                document.getElementById('nombre_autorizacion').value = nombreCompleto;
                document.getElementById('dni_autorizacion').value = datos.comprador_numero_doc || '';
                
                // Fecha actual
                const hoy = new Date();
                document.getElementById('fecha_autorizacion_display').value = hoy.toLocaleDateString('es-PE');
                document.getElementById('fecha_autorizacion').value = hoy.toISOString().split('T')[0];
                
                // Firma
                if (datos.firma_cliente) {
                    const firmaPreview = document.getElementById('firma-cliente-preview-adp');
                    if (firmaPreview) {
                        firmaPreview.innerHTML = '<img src="' + datos.firma_cliente + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;" alt="Firma">';
                        document.getElementById('firma_cliente_ruta_adp').value = datos.firma_cliente;
                    }
                }
                
                console.log('✅ Preview cargado - Autorización');
            } catch (e) {
                console.error('❌ Error:', e);
            }
        }
    }
  </script>
</body>
</html>
