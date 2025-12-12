<?php
date_default_timezone_set('America/Lima');
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenido a la Familia Interamericana</title>
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

    .header {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      margin-bottom: 20px;
      padding-bottom: 10px;
    }

    .brand-logo {
      max-width: 220px;
      height: auto;
    }

    .main-title {
      text-align: center;
      color: var(--primary);
      font-size: 24px;
      font-weight: 700;
      text-decoration: underline;
      margin: 30px 0;
    }

    .greeting {
      margin-bottom: 20px;
    }

    .highlight {
      padding: 2px 4px;
      border-radius: 3px;
      font-weight: 700;
    }

    .services-list {
      margin: 20px 0;
      padding-left: 20px;
    }

    .services-list li {
      margin: 8px 0;
    }

    .signatures {
      display: flex;
      justify-content: space-around;
      gap: 40px;
      margin: 40px 0;
      text-align: center;
      flex-wrap: wrap;
    }

    .signature {
      flex: 1 1 260px;
    }

    .signature-line {
      margin: 20px 0 10px;
      height: 80px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .signature-line::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: url('/digitalizacion-documentos/assets/images/logo_interamericana.jpg');
      background-size: 150px auto;
      background-position: center;
      background-repeat: no-repeat;
      opacity: 0.15;
      z-index: 0;
    }

    .signature-line img {
      max-width: 250px;
      max-height: 80px;
      object-fit: contain;
      position: relative;
      z-index: 1;
    }

    .signature-name {
      font-weight: 700;
      margin-bottom: 4px;
    }

    .signature-title {
      font-size: 14px;
      color: #666;
    }

    .footer-info {
      margin-top: 30px;
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
        margin-bottom: 10px !important;
      }

      .header img {
        height: 50px !important;
      }

      .title {
        font-size: 15pt !important;
        margin: 10px 0 !important;
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

      ul {
        margin: 5px 0 !important;
        padding-left: 15px !important;
      }

      @page {
        size: A4;
        margin: 10mm;
      }
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
    <h1 class="main-title">¡BIENVENIDO A LA FAMILIA INTERAMERICANA!</h1>

    <!-- Contenido -->
    <div class="greeting"><strong>Estimado(a):</strong> <input type="text" id="cliente-nombre" name="CF_CLIENTE_NOMBRE" value="<?php echo htmlspecialchars($documentData['CF_CLIENTE_NOMBRE'] ?? (trim(($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') . ' ' . ($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? ''))) ?? ''); ?>" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></div>

    <p style="text-align: center;">¡Llegó el momento de estrenar tu <span class="highlight"><input type="text" id="vehiculo-marca" name="CF_VEHICULO_MARCA" value="<?php echo htmlspecialchars($documentData['CF_VEHICULO_MARCA'] ?? $ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?>" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></span> de ensueño!</p>

    <p>
      Estamos muy emocionados de compartir contigo este momento tan especial.
      Hoy empieza una nueva aventura, llena de experiencias maravillosas, y nos
      sentimos felices de formar parte. Tu asesor
      <span class="highlight"><input type="text" id="asesor-nombre" name="CF_ASESOR_NOMBRE" value="<?php echo htmlspecialchars($documentData['CF_ASESOR_NOMBRE'] ?? $ordenCompraData['OC_ASESOR_VENTA'] ?? ''); ?>" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></span>, con teléfono de contacto
      <span class="highlight"><input type="text" id="asesor-celular" name="CF_ASESOR_CELULAR" value="<?php echo htmlspecialchars($documentData['CF_ASESOR_CELULAR'] ?? ''); ?>" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></span>, junto a todo nuestro equipo,
      está complacido con tu elección.
    </p>

    <p>
      Recuerda que es importante cumplir con los servicios de mantenimiento porque
      permite cuidar tu seguridad, la de tu familia, tu inversión y la garantía
      de tu vehículo. Para ello puedes separar tu cita de servicio al número
      <strong>944 232 262</strong>, por correo a
      <a href="mailto:castallerpiura@interamericananorte.com">
        castallerpiura@interamericananorte.com
      </a>,
      o si lo deseas, ingresa a nuestra web:
      <a href="https://interamericananorte.com/agenda-tu-cita" target="_blank" rel="noopener">
        interamericananorte.com/agenda-tu-cita
      </a>.
    </p>

    <p>
      Además, queremos invitarte a descargar y conocer la aplicación
      <span class="highlight"><input type="text" id="aplicacion-nombre" name="CF_APLICACION_NOMBRE" value="<?php echo htmlspecialchars($documentData['CF_APLICACION_NOMBRE'] ?? 'NOMBRE DE APLICACIÓN - SI APLICA'); ?>" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></span>,
      un sistema donde podrás agendar directamente tus citas, conocer beneficios
      de tu vehículo y más.
    </p>

    <p>
      Nuestro equipo de Posventa quiere darte la cordial bienvenida con una
      <strong>inspección gratuita</strong> a los <strong>45 días o 1,000 km</strong>
      de la entrega de tu unidad (<em>Welcome Check</em>).
    </p>

    <p><strong>Te recordamos que contamos con los siguientes servicios:</strong></p>
    <ul class="services-list">
      <li>Taxi gratuito cuando dejas tu vehículo en taller (sujeto a disponibilidad).</li>
      <li>Escáneres especializados.</li>
      <li>Técnicos certificados por las marcas que representamos.</li>
      <li>Repuestos originales.</li>
      <li>Servicio de planchado y pintura.</li>
      <li>Accesorios originales.</li>
    </ul>

    <p>
      En el caso de tu vehículo
      <span class="highlight"><input type="text" id="vehiculo-completo" name="vehiculo_completo" value="<?php echo htmlspecialchars(($ordenCompraData['OC_VEHICULO_MARCA'] ?? '') . ', ' . ($ordenCompraData['OC_VEHICULO_MODELO'] ?? '') . ' ' . ($ordenCompraData['OC_VEHICULO_VERSION'] ?? '')); ?>" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></span>, ten presente que su
      primer mantenimiento deberá realizarse según la
      <a href="#" target="_blank">tabla de periodicidad</a>.
    </p>

    <p><strong>Te deseamos lo mejor hoy y siempre.</strong></p>
    <p><strong>¡Disfruta tu vehículo!</strong></p>

    <!-- Firmas -->
    <div class="signatures">
      <div class="signature">
        <div class="signature-line">
          <img src="/digitalizacion-documentos/assets/images/Alonso_gerente.jpg" alt="Firma Alonso Puig">
        </div>
        <div style="border-top: 2px solid #333; padding-top: 10px;">
          <div class="signature-name">ALONSO PUIG</div>
          <div class="signature-title">GERENTE GENERAL</div>
        </div>
      </div>
      <div class="signature">
        <div class="signature-line">
          <img src="/digitalizacion-documentos/assets/images/Jorge_Aguilera_gerente.jpg" alt="Firma Jorge Aguilera">
        </div>
        <div style="border-top: 2px solid #333; padding-top: 10px;">
          <div class="signature-name">JORGE AGUILERA</div>
          <div class="signature-title">GERENTE POSVENTA</div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <hr style="border:1px solid #000; margin:40px 0;">

    <div class="footer-info">
      <p><strong>Te recordamos información importante de nuestros talleres:</strong></p>
      <p>
        <strong>Atención de lunes a viernes:</strong> 08:00 am – 05:00 pm &nbsp;|&nbsp;
        <strong>Sábados:</strong> 08:30 am – 01:00 pm.
      </p>

      <p><strong>Contactos:</strong></p>
      <p>
        Taller de Livianos: 944 232 262 | 073-323355 ·
        Taller de Pesados: 976 438 098 ·
        Repuestos: 979 900 412 ·
        Accesorios: 975 713 284 ·
        Planchado &amp; Pintura: 968 955 004 ·
        Emergencias / Servicio de grúa: 969 642 205
      </p>

      <!-- Web + redes -->
      <div style="display:flex; align-items:center; justify-content:space-between; margin-top:20px; flex-wrap:wrap;">
        <a href="https://www.interamericananorte.com" target="_blank" rel="noopener"
          style="color:#2563eb; text-decoration:none;">
          www.interamericananorte.com
        </a>

        <div style="display:flex; align-items:center; gap:18px;">
          <div style="display:flex; align-items:center; gap:6px;">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook"
              style="width:20px; height:20px;">
            <span>InteramericanaNorte</span>
          </div>
          <div style="display:flex; align-items:center; gap:6px;">
            <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram"
              style="width:20px; height:20px;">
            <span>interamericananorte</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Botón de guardar -->
    <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
        <input type="hidden" name="document_type" value="carta_felicitaciones">
        <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
            💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR' : 'GUARDAR'; ?>
        </button>
    </div>
    <?php endif; ?>
    <?php if (isset($modoImpresion) && $modoImpresion): ?>
    <div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
      <a href="/digitalizacion-documentos/documents/show?id=carta_felicitaciones&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
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

  </div>
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
        'cliente-nombre',
        'vehiculo-marca',
        'asesor-nombre',
        'asesor-celular',
        'aplicacion-nombre',
        'vehiculo-completo'
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

  <!-- Preview desde localStorage -->
  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const esPreview = urlParams.get('preview') === '1';
    const tieneOrdenId = urlParams.get('orden_id') !== null;
    
    if (esPreview && !tieneOrdenId) {
        console.log('👁️ Modo PREVIEW - Carta Felicitaciones');
        const datosStr = localStorage.getItem('preview_orden_compra');
        
        if (datosStr) {
            try {
                const datos = JSON.parse(datosStr);
                console.log('📦 Datos cargados:', datos);
                
                const nombreCompleto = (datos.comprador_nombre + ' ' + datos.comprador_apellido).trim();
                
                // IDs con guiones
                document.getElementById('cliente-nombre').value = nombreCompleto;
                document.getElementById('vehiculo-marca').value = datos.vehiculo_marca || '';
                
                // Vehículo completo (marca + modelo + versión)
                const vehiculoCompleto = [datos.vehiculo_marca, datos.vehiculo_modelo, datos.vehiculo_version].filter(v => v).join(', ');
                if (vehiculoCompleto) {
                    document.getElementById('vehiculo-completo').value = vehiculoCompleto;
                }
                
                // Asesor
                if (datos.asesor_nombre) {
                    const asesorInput = document.getElementById('asesor-nombre');
                    if (asesorInput) asesorInput.value = datos.asesor_nombre;
                }
                if (datos.asesor_celular) {
                    const celularInput = document.getElementById('asesor-celular');
                    if (celularInput) celularInput.value = datos.asesor_celular;
                }
                
                console.log('✅ Nombre:', nombreCompleto);
                console.log('✅ Marca:', datos.vehiculo_marca);
                console.log('✅ Vehículo completo:', vehiculoCompleto);
                console.log('✅ Asesor:', datos.asesor_nombre);
                
                console.log('✅ Preview cargado - Carta Felicitaciones');
            } catch (e) {
                console.error('❌ Error:', e);
            }
        }
    }
  </script>
</body>

</html>
