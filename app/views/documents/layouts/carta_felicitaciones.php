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
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
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
      border-bottom: 2px solid #333;
      margin: 20px 0 10px;
      height: 60px;
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
      body {
        background: #fff;
      }

      .page {
        box-shadow: none;
      }
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

  <div class="page">
    <!-- Encabezado -->
    <div class="header">
      <img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" alt="Logo Interamericana" width="200">

    </div>

    <!-- Título -->
    <h1 class="main-title">¡BIENVENIDO A LA FAMILIA INTERAMERICANA!</h1>

    <!-- Contenido -->
    <div class="greeting"><strong>Estimado(a):</strong> <input type="text" id="cliente-nombre" name="CF_CLIENTE_NOMBRE" value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></div>

    <p style="text-align: center;">¡Llegó el momento de estrenar tu <span class="highlight"><input type="text" id="vehiculo-marca" name="CF_VEHICULO_MARCA" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?>" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></span> de ensueño!</p>

    <p>
      Estamos muy emocionados de compartir contigo este momento tan especial.
      Hoy empieza una nueva aventura, llena de experiencias maravillosas, y nos
      sentimos felices de formar parte. Tu asesor
      <span class="highlight"><input type="text" id="asesor-nombre" name="CF_ASESOR_NOMBRE" value="<?php echo htmlspecialchars($ordenCompraData['OC_ASESOR_VENTA'] ?? ''); ?>" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></span>, con teléfono de contacto
      <span class="highlight"><input type="text" id="asesor-celular" name="CF_ASESOR_CELULAR" value="" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></span>, junto a todo nuestro equipo,
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
      <span class="highlight"><input type="text" id="aplicacion-nombre" name="CF_APLICACION_NOMBRE" value="NOMBRE DE APLICACIÓN - SI APLICA" style="border: none; background: transparent; font-weight: bold; width: auto; min-width: 50px;"></span>,
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
        <div class="signature-line"></div>
        <div class="signature-name">ALONSO PUIG</div>
        <div class="signature-title">GERENTE GENERAL</div>
      </div>
      <div class="signature">
        <div class="signature-line"></div>
        <div class="signature-name">JORGE AGUILERA</div>
        <div class="signature-title">GERENTE POSVENTA</div>
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
</body>

</html>
