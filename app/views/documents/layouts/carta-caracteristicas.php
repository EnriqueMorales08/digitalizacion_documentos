<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta de Características - Interamericana</title>
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
            line-height: 1.4;
            color: #333;
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

        @media print {
            body {
                background: #fff;
            }

            .page {
                box-shadow: none;
            }
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .logo-section img {
            height: 60px;
            width: auto;
        }
        
        .contact-info {
            text-align: right;
            font-size: 12px;
            line-height: 1.3;
        }
        
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 30px 0;
            text-transform: uppercase;
        }
        
        .date {
            text-align: right;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .recipient {
            margin-bottom: 30px;
        }
        
        .recipient h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        
        .content {
            margin-bottom: 30px;
            text-align: justify;
        }
        
        .vehicle-details {
            margin: 20px 0;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .detail-label {
            width: 200px;
            font-weight: bold;
        }
        
        .detail-value {
            flex: 1;
        }
        
        .values-section {
            margin: 30px 0;
        }
        
        .commitment {
            text-align: center;
            margin: 40px 0;
            font-weight: bold;
        }
        
        .closing {
            margin-top: 40px;
        }
        
        .footer-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 10px;
        }

        /* === Inputs sin líneas ni bordes visibles === */
        input {
            border: none;
            outline: none;
            font-family: inherit;
            font-size: inherit;
            background: transparent;
            width: auto;
            min-width: 100px;
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
        <div class="header">
        <div class="logo-section">
           <img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" alt="Logo Interamericana" width="200">
        </div>
        <div class="contact-info">
            Principal: Av. Sanchez Cerro Mz. 240 Lt. 02 Zona Industrial – Piura<br>
            Telef.: 073-325352<br>
            Tienda: José Leonardo Ortiz Nro. 450 – Chiclayo Web: www.interamericananorte.com
        </div>
    </div>

    <div class="title">CARTA DE CARACTERÍSTICAS</div>

    <div class="date">
        <input type="text" name="CC_FECHA_CARTA" value="Piura, 28 de Agosto de 2025" style="width: 300px;">
    </div>

    <div class="recipient">
        <h3>Señores:</h3>
        <h2><input type="text" name="CC_EMPRESA_DESTINO" value="AUTOPLAN EAFC S.A." style="width: 400px;"></h2>
        <p><strong>Presente. -</strong></p>
    </div>

    <div class="content">
        <p><strong>Estimados Señores:</strong></p>

        <p>Mediante la presente, les informamos que nuestro mutuo cliente
        <strong><input type="text" name="CC_CLIENTE_NOMBRE" value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>" style="width: 350px;"></strong>
        con <strong>DNI. <input type="text" name="CC_CLIENTE_DNI" value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?>" style="width: 100px;"></strong>
        ha obtenido el crédito vehicular por la siguiente unidad:</p>
    </div>

    <div class="vehicle-details">
        <div class="detail-row">
            <div class="detail-label">Marca</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_MARCA" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?>"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Modelo</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_MODELO" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_MODELO'] ?? ''); ?>" style="width: 250px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Año de Modelo</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_ANIO_MODELO" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_ANIO_MODELO'] ?? ''); ?>" style="width: 100px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Número de Chasis</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_CHASIS" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_CHASIS'] ?? ''); ?>" style="width: 250px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Número de Motor</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_MOTOR" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_MOTOR'] ?? ''); ?>" style="width: 200px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Carrocería</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_CARROCERIA" value="SEDAN" style="width: 150px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Color</div>
            <div class="detail-value">: <input type="text" name="CC_VEHICULO_COLOR" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_COLOR'] ?? ''); ?>" style="width: 200px;"></div>
        </div>
    </div>

    <div class="values-section">
        <h4>Valores:</h4>
        <div class="detail-row">
            <div class="detail-label">Precio del vehículo</div>
            <div class="detail-value">: <input type="text" name="CC_PRECIO_VEHICULO" value="<?php echo htmlspecialchars(($ordenCompraData['OC_MONEDA_PRECIO_VENTA'] ?? '') . ' ' . ($ordenCompraData['OC_PRECIO_VENTA'] ?? '')); ?>" style="width: 150px;"></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Monto a desembolsar</div>
            <div class="detail-value">: <input type="text" name="CC_MONTO_DESEMBOLSO" value="<?php echo htmlspecialchars(($ordenCompraData['OC_MONEDA_PRECIO_VENTA'] ?? '') . ' ' . ($ordenCompraData['OC_PRECIO_VENTA'] ?? '')); ?>" style="width: 150px;"></div>
        </div>
    </div>

    <div class="commitment">
        Nos comprometemos a gestionar la tarjeta de Propiedad del vehículo a nombre de:
        <strong><input type="text" name="CC_PROPIETARIO_TARJETA" value="<?php echo htmlspecialchars($ordenCompraData['OC_PROPIETARIO_NOMBRE'] ?? ''); ?>" style="width: 350px;"></strong>
    </div>

    <div class="content">
        <p>A la espera del desembolso correspondiente, quedamos de ustedes.</p>
    </div>

    <div class="closing">
        <p>Atentamente,</p>
    </div>

    <div class="footer-line"></div>
    </div>
</body>
</html>
