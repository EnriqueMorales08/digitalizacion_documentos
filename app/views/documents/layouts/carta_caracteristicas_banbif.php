<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento BanBif - Financiamiento Vehicular</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .document {
            max-width: 850px;
            margin: 0 auto;
            background-color: white;
            padding: 60px 80px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header-field {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header-field label {
            font-size: 14px;
            font-weight: normal;
        }
        
        .header-field input {
            border: 1px solid #333;
            padding: 4px 8px;
            font-size: 14px;
        }
        
        .date-input {
            width: 60px;
        }
        
        .month-input {
            width: 120px;
        }
        
        .year-input {
            width: 150px;
        }
        
        .name-input {
            flex: 1;
            width: 100%;
        }
        
        .ruc-input {
            width: 350px;
        }
        
        .salutation {
            margin-top: 40px;
            margin-bottom: 5px;
        }
        
        .salutation p {
            font-size: 14px;
            line-height: 1.4;
            margin-bottom: 3px;
        }
        
        .salutation .company {
            font-weight: bold;
        }
        
        .content {
            margin-top: 30px;
        }
        
        .content p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
            text-align: justify;
        }
        
        .financing-box {
            border: 1px solid #333;
            padding: 8px;
            margin: 10px 0 20px 0;
            min-height: 30px;
        }
        
        .list-items {
            margin: 20px 0;
            padding-left: 40px;
        }
        
        .list-items p {
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .recipient-box {
            border: 1px solid #333;
            padding: 8px;
            margin: 10px 0 20px 0;
            min-height: 30px;
        }
        
        .vehicle-table {
            width: 100%;
            margin: 30px 0;
        }
        
        .vehicle-table td {
            padding: 5px;
            font-size: 14px;
        }
        
        .vehicle-table input {
            width: 100%;
            border: 1px solid #666;
            padding: 4px 8px;
            font-size: 13px;
        }
        
        .vehicle-table .label-cell {
            width: 140px;
            text-align: left;
        }
        
        .vehicle-table .input-cell {
            width: 210px;
        }
        
        .financial-info {
            margin: 20px 0;
            padding-left: 40px;
        }
        
        .financial-row {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .financial-row .bullet {
            width: 8px;
            height: 8px;
            background-color: black;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .financial-row label {
            min-width: 280px;
        }
        
        .financial-row .currency {
            margin: 0 10px;
        }
        
        .financial-row input {
            border: 1px solid #666;
            padding: 4px 8px;
            font-size: 13px;
        }

        .financial-row .price-input {
            width: 200px;
        }

        .financial-row .currency-label {
            margin-left: 10px;
            margin-right: 10px;
        }

        .financial-row .amount-input {
            width: 200px;
        }
        
        .benefit-row {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            margin-top: 8px;
            font-size: 14px;
            padding-left: 40px;
        }
        
        .benefit-row .bullet {
            width: 8px;
            height: 8px;
            background-color: black;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .benefit-row label {
            min-width: 280px;
        }
        
        .benefit-row .currency {
            margin: 0 10px;
        }
        
        .benefit-row input {
            border: 1px solid #000;
            border-width: 2px;
            padding: 4px 8px;
            font-size: 13px;
            width: 150px;
        }
        
        .benefit-row .currency-label {
            margin-left: 10px;
            margin-right: 10px;
        }
        
        .benefit-row .amount-input {
            width: 200px;
        }
        
        .footnote {
            font-size: 12px;
            margin-top: 20px;
            font-style: italic;
        }
        
        .signature-section {
            margin-top: 80px;
            text-align: right;
        }
        
        .signature-line {
            border-top: 2px solid black;
            width: 300px;
            margin-left: auto;
            padding-top: 5px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Flecha de regreso -->
    <div style="position: fixed; top: 20px; left: 20px; z-index: 1000;" class="no-print">
        <a href="/digitalizacion-documentos/documents" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3); font-family: Arial, sans-serif; font-size: 14px; font-weight: 500; transition: all 0.3s ease;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Regresar
        </a>
    </div>

    <form method="POST" action="/digitalizacion-documentos/documents/guardar-documento" style="margin: 0; padding: 0;">
    <div class="document">
        <?php
        // Formatear fecha
        $fecha = $ordenCompraData['OC_FECHA_ORDEN'] ?? date('Y-m-d');
        if ($fecha) {
            if (is_string($fecha)) {
                $date = new DateTime($fecha);
            } else {
                $date = new DateTime($fecha);
            }
            $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            $dia = $date->format('d');
            $mes = $meses[(int)$date->format('m')];
            $anio = $date->format('Y');
            $formatted = "Piura, $dia de $mes de $anio";
        } else {
            $formatted = "Piura, 28 de Agosto de 2025";
        }
        ?>
        <div class="header-field">
            <label>Fecha:</label>
            <input type="text" name="fecha_banbif" value="<?php echo htmlspecialchars($formatted); ?>" style="width: 300px;">
        </div>
        
        <div class="header-field">
            <label>Nombre del Concesionario:</label>
            <input type="text" class="name-input">
        </div>
        
        <div class="header-field">
            <label>N° del RUC:</label>
            <input type="text" class="ruc-input">
        </div>
        
        <div class="salutation">
            <p>Señores</p>
            <p class="company">BanBif – Banco Interamericano de Finanzas</p>
            <p>Presente.-</p>
        </div>
        
        <div class="content">
            <p>De nuestra consideración:</p>
            <p>Por medio de la presente, en virtud del financiamiento otorgado a:</p>
            <input type="text" value="<?php echo htmlspecialchars($documentData['CCB_CLIENTE_NOMBRE'] ?? $ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>" style="width: 100%; border: 1px solid #333; padding: 8px; margin: 10px 0 20px 0; font-size: 14px;">

            <p>para la adquisición del vehículo que se describe en el presente documento, dejamos constancia expresa de nuestro compromiso irrevocable e incondicional de poder entregar en un plazo no mayor de 20 días útiles de haber cancelado el precio de venta, los siguientes documentos a sola solicitud de BanBif. Además, informaré oportunamente a BanBif cualquier suceso que pueda demorar la entrega.</p>

            <div class="list-items">
                <p>a) Copia de la factura cancelada,</p>
                <p>b) Copia de la tarjeta de propiedad del vehículo materia de compra/venta.</p>
            </div>

            <p>De acuerdo a lo coordinado con nuestro cliente y BanBif, les confirmamos que la factura y la tarjeta de propiedad serán emitidas a nombre de:</p>
            <input type="text" value="<?php echo htmlspecialchars($documentData['CCB_PROPIETARIO_TARJETA'] ?? $ordenCompraData['OC_PROPIETARIO_NOMBRE'] ?? ''); ?>" style="width: 100%; border: 1px solid #333; padding: 8px; margin: 10px 0 20px 0; font-size: 14px;">
            
            <p>Asimismo, en caso de que luego de la inmatriculación se den variaciones en los titulares registrales del vehículo, nos comprometemos a regularizar según como se indica en la presente carta o dar aviso a BanBif y al cliente para las regularizaciones correspondientes.</p>
            
            <p>Al momento de emitir esta carta, confirmamos haber recibido de nuestro cliente el abono total de la cuota inicial. Los datos del vehículo son:</p>
        </div>
        
        <table class="vehicle-table">
            <tr>
                <td class="label-cell">Marca:</td>
                <td class="input-cell"><input type="text" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_MARCA'] ?? $ordenCompraData['OC_VEHICULO_MARCA'] ?? ''); ?>"></td>
                <td class="label-cell">Color:</td>
                <td class="input-cell"><input type="text" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_COLOR'] ?? $ordenCompraData['OC_VEHICULO_COLOR'] ?? ''); ?>"></td>
            </tr>
            <tr>
                <td class="label-cell">Modelo:</td>
                <td class="input-cell"><input type="text" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_MODELO'] ?? $ordenCompraData['OC_VEHICULO_MODELO'] ?? ''); ?>"></td>
                <td class="label-cell">Clase:</td>
                <td class="input-cell"><input type="text" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_CLASE'] ?? $ordenCompraData['OC_VEHICULO_CLASE'] ?? ''); ?>"></td>
            </tr>
            <tr>
                <td class="label-cell">Año de modelo:</td>
                <td class="input-cell"><input type="text" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_ANIO_MODELO'] ?? $ordenCompraData['OC_VEHICULO_ANIO_MODELO'] ?? ''); ?>"></td>
                <td class="label-cell">N°. De motor:</td>
                <td class="input-cell"><input type="text" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_MOTOR'] ?? $ordenCompraData['OC_VEHICULO_MOTOR'] ?? ''); ?>"></td>
            </tr>
            <tr>
                <td class="label-cell">Tipo de carrocería:</td>
                <td class="input-cell"><input type="text"></td>
                <td class="label-cell">N°. De serie:</td>
                <td class="input-cell"><input type="text" value="<?php echo htmlspecialchars($documentData['CCB_VEHICULO_CHASIS'] ?? $ordenCompraData['OC_VEHICULO_CHASIS'] ?? ''); ?>"></td>
            </tr>
        </table>
        
        <div class="financial-info">
            <div class="financial-row">
                <div class="bullet"></div>
                <label>Precio de Venta (incluido I.G.V.)</label>
                <span class="currency">$</span>
                <input type="text" class="price-input">
                <span class="currency-label">S/</span>
                <input type="text" class="amount-input">
            </div>

            <div class="financial-row">
                <div class="bullet"></div>
                <label>Cuota inicial</label>
                <span class="currency">$</span>
                <input type="text" class="price-input">
                <span class="currency-label">S/</span>
                <input type="text" class="amount-input">
            </div>

            <div class="financial-row">
                <div class="bullet"></div>
                <label>Saldo de Precio (Desembolso)</label>
                <span class="currency">$</span>
                <input type="text" class="price-input">
                <span class="currency-label">S/</span>
                <input type="text" class="amount-input">
            </div>
        </div>
        
        <div class="benefit-row">
            <div class="bullet"></div>
            <label>Beneficio de Fidelización BanBif*</label>
            <span class="currency">$</span>
            <input type="text" class="amount-input">
            <span class="currency-label">S/</span>
            <input type="text" class="amount-input">
        </div>
        
        <div class="content">
            <p>Cualquier variación en las condiciones y/o términos de la operación de venta que se realice antes o después del desembolso del saldo de precio, deberá ser informada a BanBif con anticipación, caso contrario la venta no tendrá efecto y aceptamos devolver dentro de los 2 días útiles siguientes de la sola solicitud de BanBif, cualquier monto que nos hubiera entregado.</p>
            
            <p>Solicitamos que el desembolso se efectúe según los montos indicados en la presente carta en soles o dólares; y que se abone en la cuenta corriente que tenemos en BanBif, o en su defecto, se emita un cheque de gerencia.</p>
            
            <p class="footnote">*Descuento aplicado al precio de vehículo</p>
        </div>
        
        <div class="signature-section">
            <div class="signature-line">
                Firma del representante
            </div>
        </div>
    </div>

    <!-- Botón de guardar -->
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;" class="no-print">
        <input type="hidden" name="document_type" value="carta_caracteristicas_banbif">
        <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5); transition: all 0.3s ease;">
            💾 GUARDAR
        </button>
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