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
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
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
  <form method="POST" action="/digitalizacion-documentos/documents/guardar-documento">
  <!-- Flecha de regreso -->
  <div style="position: fixed; top: 20px; left: 20px; z-index: 1000;" class="no-print">
    <a href="/digitalizacion-documentos/documents" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3); font-family: Arial, sans-serif; font-size: 14px; font-weight: 500; transition: all 0.3s ease;">
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
    <strong>Lugar y Fecha:</strong> Piura, Perú; <input type="date" name="ACC_FECHA_ACTA" class="short" value="<?php echo htmlspecialchars($documentData['ACC_FECHA_ACTA'] ?? date('Y-m-d')); ?>">
  </div>

  <div class="content">
    Yo, <input type="text" name="ACC_NOMBRE_CLIENTE" class="long" value="<?php echo htmlspecialchars($documentData['ACC_NOMBRE_CLIENTE'] ?? $ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>" placeholder="Nombre Completo del Cliente">,
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
      <li><strong>Boleta/Factura de Venta N.º:</strong> <input type="text" name="ACC_BOLETA_FACTURA_NUMERO" class="medium" value="<?php echo htmlspecialchars($ordenCompraData['OC_ID'] ?? ''); ?>" placeholder="N.º Boleta o Factura"></li>
      <li><strong>Nombre del Cliente:</strong> <input type="text" name="ACC_CLIENTE_VEHICULO" class="long" value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>" placeholder="Nombre Completo"></li>
      <li><strong>Fecha de Venta:</strong> <input type="date" name="ACC_FECHA_VENTA" class="medium" value="<?php echo htmlspecialchars($ordenCompraData['OC_FECHA_ORDEN'] ?? ''); ?>"></li>
      <li><strong>Marca:</strong> <input type="text" name="ACC_MARCA_VEHICULO" class="medium" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?>" placeholder="Marca"></li>
      <li><strong>Modelo:</strong> <input type="text" name="ACC_MODELO_VEHICULO" class="medium" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_MODELO'] ?? ''); ?>" placeholder="Modelo"></li>
      <li><strong>Año:</strong> <input type="text" name="ACC_ANIO_VEHICULO" class="short" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_ANIO_MODELO'] ?? ''); ?>" placeholder="Año"></li>
      <li><strong>VIN (Número de Identificación Vehicular):</strong> <input type="text" name="ACC_VIN_VEHICULO" class="long" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_CHASIS'] ?? ''); ?>" placeholder="N.º VIN"></li>
      <li><strong>Color:</strong> <input type="text" name="ACC_COLOR_VEHICULO" class="medium" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_COLOR'] ?? ''); ?>" placeholder="Color"></li>
    </ul>
  </div>

  <div class="content">
    Con mi firma, dejo constancia de haber leído, comprendido y aceptado en su totalidad los términos expuestos en la presente acta.
  </div>

  <div class="signature-section">
    <p><strong>Firma del Cliente:</strong> <?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?><img src="<?php echo htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']); ?>" style="max-width:200px; max-height:50px;"><?php else: ?><input type="text" name="ACC_FIRMA_CLIENTE" class="medium" value="Firma"><?php endif; ?></p>
    <p><strong>Nombre del Cliente:</strong> <input type="text" name="ACC_NOMBRE_FIRMA" class="medium" value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>"></p>
    <p><strong>DNI:</strong> <input type="text" name="ACC_DNI_FIRMA" class="medium" value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?>"></p>
  </div>

  <!-- Botón de guardar -->
  <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
    <input type="hidden" name="document_type" value="acta-conocimiento-conformidad">
    <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
        💾 GUARDAR
    </button>
  </div>
  </div>
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
