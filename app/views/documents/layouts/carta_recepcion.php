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
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
      line-height: 1.6;
      color: #000;
    }

    @media print {
      body {
        background: #fff;
      }

      .page {
        box-shadow: none;
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
      font-style: italic;
    }

    .content {
      margin-bottom: 190px;
      text-align: justify;
    }

    .paragraph {
      margin-bottom: 20px;
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
      Piura, <input type="text" id="fecha-dia" name="CR_FECHA_DIA" value="<?php echo date('d'); ?>" class="short"> / <input type="text" id="fecha-mes" name="CR_FECHA_MES" value="<?php echo date('m'); ?>" class="short"> / <input type="text" id="fecha-anio" name="CR_FECHA_ANIO" value="<?php echo date('Y'); ?>" class="short">
    </div>

    <!-- Contenido -->
    <div class="content">
      <div class="paragraph">
        Mediante la presente, Yo, <input type="text" id="cliente-nombre" name="CR_CLIENTE_NOMBRE" value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>" class="long">,
      </div>

      <div class="paragraph">
        identificado con DNI <input type="text" id="cliente-dni" name="CR_CLIENTE_DNI" value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?>" class="short">, confirmo que en la fecha de entrega de mi vehículo marca
      </div>

      <div class="paragraph">
        <input type="text" id="vehiculo-marca" name="CR_VEHICULO_MARCA" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?>" class="medium"> modelo <input type="text" id="vehiculo-modelo" name="CR_VEHICULO_MODELO" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_MODELO'] ?? ''); ?>" class="medium">, he recibido de parte <strong>INTERAMERICANA NORTE S.A.C.</strong>,
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
      <?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?>
      <img src="<?php echo htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']); ?>" style="max-width:300px; max-height:50px; display:block; margin:0 auto 5px auto;">
      <?php else: ?>
      <input type="text" name="CR_FIRMA_CLIENTE" value="Firma" style="border: none; text-align: center; font-weight: bold; width: 300px; margin-bottom: 5px;">
      <?php endif; ?>
      <div class="signature-line"></div>
      <div class="signature-label">FIRMA Y DNI DEL CLIENTE</div>
    </div>

    <!-- Botón de guardar -->
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
        <input type="hidden" name="document_type" value="carta_recepcion">
        <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
            💾 GUARDAR
        </button>
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
</body>
</html>
