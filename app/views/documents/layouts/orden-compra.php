<?php
// Establecer zona horaria de Perú
date_default_timezone_set('America/Lima');

// Definir lista de bancos para usar en todo el documento
$bancos = ['BCP', 'BBVA', 'Interbank', 'Scotiabank', 'Banco de la Nación', 'Banco Pichincha', 'Banco Interamericano de Finanzas', 'Banco GNB', 'Banco Falabella', 'Banco Ripley', 'MIBANCO', 'Banco Azteca', 'Caja Arequipa', 'Caja Cusco', 'Caja Huancayo', 'Caja Ica', 'Caja Piura', 'Caja Sullana', 'Caja Trujillo', 'CMAC Lima', 'CMAC Maynas', 'CMAC Paita', 'CMAC Tacna', 'CRAC Señor de Luren', 'CRAC Los Andes', 'Financiera Confianza', 'Financiera Credinka', 'Financiera Efectiva', 'Financiera Proempresa', 'Financiera TFC', 'Agrobanco', 'Banco de Comercio', 'Banco Santander'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Compra - Interamericana</title>
    <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
    <script src="/digitalizacion-documentos/public/js/poblar_selects.js"></script>
    <?php endif; ?>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --primary: #1e3a8a;
        --ink: #111827;
        --bg: #f8f9fa;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 10px;
        background-color: var(--bg);
        margin: 0;
        padding: 15px;
    }

    /* Bloque completo tipo carta */
    .page {
        width: 794px; /* Ancho A4 */
        margin: 0 auto;
        background: #fff;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
    }

    .form-container {
        width: 100%;
        max-width: 774px;
        border: 1px solid #000;
        background-color: white;
        overflow: hidden;
    }

    @media print {
       
        body {
            background: #fff;
            padding: 10mm;
            margin: 0;
        }
        html {
            margin: 0;
            padding: 0;
        }

        .page {
            box-shadow: none;
            border-radius: 0;
            padding: 0;
            margin: 0;
            width: 100%;
            max-width: 100%;
        }

        .form-container {
            border: 1px solid #000;
            page-break-inside: avoid;
            width: 780px;
    
        }

        /* Forzar color negro en todos los inputs al imprimir */
        input, select, textarea {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            background-color: #ffffff !important;
            opacity: 1 !important;
            border-color: #000000 !important;
        }
        
        input[readonly], select[readonly], textarea[readonly] {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
        }

        input, select, textarea {
            border: none !important;
            background: transparent !important;
            font-size: 8.5px !important;
            padding: 1px !important;
        }

        /* Ocultar flechitas de los selectores (select) */
        select {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            background-image: none !important;
        }

        /* Ocultar inputs de archivo */
        input[type="file"] {
            display: none !important;
        }

        .no-print {
            display: none !important;
        }

        /* Reducir espacios */
        div[style*="margin"] {
            margin: 2px auto !important;
        }

        div[style*="padding"] {
            padding: 2px !important;
        }

        /* Ajustar altura de firmas para impresión */
        div[style*="height:70px"] {
            height: 60px !important;
        }
        
        /* Asegurar que las firmas se vean completas */
        .firma-container {
            overflow: visible !important;
        }
        
        .firma-container > div {
            overflow: visible !important;
        }

        /* Reducir tamaño de texto */
        div, span, p, li {
            font-size: 7.5px !important;
            line-height: 1.2 !important;
        }

        /* Reducir altura de textarea */
        textarea {
            height: 30px !important;
        }

        /* Reducir tamaño del header */
        .header {
            padding: 4px !important;
        }

        .header-left img {
            width: 150px !important;
        }

        @page {
            size: A4;
            margin: 8mm 8mm 8mm 3mm
        }
    }

    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px;
    }

    .header-left img {
        width: 210px;
    }

    .header-center {
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        flex: 1;
    }

    .header-right {
        display: flex;
        flex-direction: column;
        gap: 4px;
      
    }

    .header-field {
        display: flex;
        justify-content: space-between;
        font-size: 9px;
        align-items: center;
        white-space: nowrap;
    }

    .header-field label {
        margin-right: 5px;
        flex-shrink: 0;
    }

    .header-field input {
        border: 1px solid #000;
        background-color: #fdeee2;
        height: 16px;
        font-size: 9px;
        width: 100px;
        max-width: 100px;
        flex-shrink: 0;
    }

    input,
    select,
    textarea {
        border: none;
        background: #fdeee2;
        padding: 4px;
        font-size: 10px;
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
    }

    /* Animaciones para toast */
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .toast-notification {
        font-family: Arial, sans-serif;
    }
    </style>
</head>

<body>
  <!-- Flecha de regreso -->
  <?php
  // Determinar URL de regreso
  // Si $esVistaCliente ya viene seteado desde el controlador (confirmación cliente, imprimir todos), respetarlo
  if (!isset($esVistaCliente)) {
      $esVistaCliente = isset($_GET['cliente']) && $_GET['cliente'] === '1';
  }
  $esCajera = isset($_GET['cajera']) && $_GET['cajera'] === '1';
  $tokenCajera = $_GET['token'] ?? '';
  
  if ($esVistaCliente) {
      // Si es vista de cliente, cerrar ventana
      $accionRegreso = 'onclick="window.close(); return false;"';
      $urlRegreso = '#';
  } elseif ($esCajera && $tokenCajera) {
      // Si es cajera, regresar a la vista de cajera
      $urlRegreso = '/digitalizacion-documentos/cajera/ver?token=' . urlencode($tokenCajera);
      $accionRegreso = '';
  } else {
      // Si es vista interna, regresar a expedientes o documents
      $urlRegreso = '/digitalizacion-documentos/documents';
      if (isset($_SESSION['orden_id']) && $_SESSION['orden_id']) {
          $urlRegreso = '/digitalizacion-documentos/expedientes/ver?id=' . $_SESSION['orden_id'];
      }
      $accionRegreso = '';
  }
  ?>
  <div class="no-print" style="position: fixed; top: 20px; left: 20px; z-index: 1000;">
    <a href="<?= $urlRegreso ?>" <?= $accionRegreso ?> style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3); font-family: Arial, sans-serif; font-size: 14px; font-weight: 500; transition: all 0.3s ease;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Regresar
    </a>
  </div>

  <!-- Formulario que envuelve todo el documento -->
  <form id="ordenCompraForm" method="POST" action="/digitalizacion-documentos/documents/procesar-orden-compra" enctype="multipart/form-data" style="margin: 0; padding: 0;" autocomplete="off" onsubmit="return validarFormularioAntesDeSalvar()">

    <div class="page">
        <div class="form-container" style="margin-bottom:5px">
        <!-- ENCABEZADO -->
        <div class="header">
            <div class="header-left">
                <img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" alt="Logo Interamericana" width="200">
            </div>
            <div class="header-center">
                SOLICITUD DE COMPRA
            </div>
            <div class="header-right">
                <div class="header-field">
                    <label>Nro. Expediente:</label>
                    <input type="text" name="OC_NUMERO_EXPEDIENTE" 
                           value="<?php echo isset($ordenCompraData['OC_NUMERO_EXPEDIENTE']) ? htmlspecialchars($ordenCompraData['OC_NUMERO_EXPEDIENTE']) : ''; ?>" 
                           readonly>
                </div>
                <div class="header-field">
                    <label>Nro. Cotización: <span style="color: red;">*</span></label>
                    <input type="text" name="OC_NUMERO_COTIZACION" required
                           value="<?= htmlspecialchars($ordenCompraData['OC_NUMERO_COTIZACION'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- AGENCIA / RESPONSABLE / CENTRO DE COSTO -->
        <div style="display:flex; border-bottom:1px solid #000;">
            <div style="background:#ffffff; font-weight:bold; padding:4px; width:80px;">Agencia</div>
            <!-- Siempre input readonly; se prellenará automáticamente si está vacío -->
            <input type="text" id="agencia" name="OC_AGENCIA" value="<?= htmlspecialchars(trim($ordenCompraData['OC_AGENCIA'] ?? '')) ?>" style="flex:1; padding:4px; background:#f9f9f9;" readonly required>
            
            <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; border-left:1px solid #000;">ADV/Cajera</div>
            <input type="text" id="nombre_responsable" name="OC_NOMBRE_RESPONSABLE" value="<?= htmlspecialchars(trim($ordenCompraData['OC_NOMBRE_RESPONSABLE'] ?? '')) ?>" style="flex:1; padding:4px; background:#f9f9f9;" readonly required>
            
            <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px; border-left:1px solid #000;">Centro de Costo</div>
            <?php
                // Valor a mostrar para centro de costo
                $ocCentroCostoValue = '';

                $ocCentroCostoRaw = trim($ordenCompraData['OC_CENTRO_COSTO'] ?? '');
                if ($ocCentroCostoRaw !== '') {
                    $ocCentroCostoPartes = explode('-', $ocCentroCostoRaw, 2);
                    $ocCentroCostoCodigo = trim($ocCentroCostoPartes[0] ?? '');
                    $ocCentroCostoValue = $ocCentroCostoCodigo;

                    // En modo impresión, intentar mostrar también el nombre del centro de costo
                    if (isset($modoImpresion) && $modoImpresion && !empty($ocCentroCostoCodigo) && isset($documentModel)) {
                        $nombreCentroCosto = '';
                        $centrosCosto = $documentModel->getCentrosCosto();
                        foreach ($centrosCosto as $centro) {
                            $codigoCentro = trim($centro['CENTRO DE COSTO'] ?? $centro['centro de costo'] ?? $centro['CENTRO_COSTO'] ?? '');
                            if ($codigoCentro === $ocCentroCostoCodigo) {
                                $nombreCentroCosto = trim($centro['NOMBRE CC'] ?? $centro['nombre cc'] ?? $centro['NOMBRE_CC'] ?? '');
                                break;
                            }
                        }
                        if ($nombreCentroCosto !== '') {
                            $ocCentroCostoValue = $ocCentroCostoCodigo . ' - ' . $nombreCentroCosto;
                        }
                    }
                }
            ?>
            <input type="text" id="centro_costo" name="OC_CENTRO_COSTO" value="<?= htmlspecialchars($ocCentroCostoValue) ?>" style="flex:1; padding:4px; background:#f9f9f9;" readonly required>
            
            <!-- Email de la cajera (se prellenará automáticamente o se mantiene el existente) -->
            <input type="hidden" id="email_centro_costo" name="OC_EMAIL_CENTRO_COSTO" value="<?= htmlspecialchars(trim($ordenCompraData['OC_EMAIL_CENTRO_COSTO'] ?? '')) ?>">
        </div>

        <!-- FECHA / ASESOR -->
        <div style="display:flex; border-bottom:1px solid #000; justify-content:flex-end; align-items:center; gap:20px; margin-right:50px;">
            <label for="fecha_orden" style="background:#ffffff; font-weight:bold; padding:4px;">FECHA</label>
            <input type="date" id="fecha_orden" name="OC_FECHA_ORDEN" style="width:120px;" value="<?php 
                if (!empty($ordenCompraData['OC_FECHA_ORDEN'])) { 
                    $fecha = $ordenCompraData['OC_FECHA_ORDEN']; 
                    if ($fecha instanceof DateTime) { 
                        echo $fecha->format('Y-m-d'); 
                    } else { 
                        echo htmlspecialchars($fecha); 
                    } 
                } else { 
                    echo date('Y-m-d'); 
                } 
            ?>">
            <div style="background:#ffffff; font-weight:bold; padding:4px;">ASESOR</div>
            <input type="text" id="asesor_venta" name="OC_ASESOR_VENTA" style="width:200px; max-width:200px;" 
                   value="<?php echo isset($_SESSION['usuario_nombre_completo']) ? htmlspecialchars($_SESSION['usuario_nombre_completo']) : ''; ?>" 
                   readonly>
            <!-- Datos del asesor para uso de cajera / notificaciones -->
            <input type="hidden" name="OC_ASESOR_CELULAR" 
                   value="<?php echo isset($_SESSION['usuario_celular']) ? htmlspecialchars($_SESSION['usuario_celular']) : ''; ?>">
            <input type="hidden" name="OC_ASESOR_MARCA" 
                   value="<?php echo isset($_SESSION['usuario_marcas']) ? htmlspecialchars($_SESSION['usuario_marcas']) : ''; ?>">
        </div>
    </div>

    <!-- DATOS DEL CLIENTE -->
    <div style="display:flex; border:1px solid #000;width:774px;margin:0 auto;">
        <!-- Columna lateral -->
        <div
            style="writing-mode: vertical-lr; transform: rotate(180deg); background:#ffffff; font-weight:bold; text-align:center; padding:8px; border-left:1px solid #000;">
            Datos del Cliente
        </div>

        <!-- Sección derecha -->
        <div style="flex:1;">
            <!-- Fila 0: Tipo de Cliente -->
            <?php $tipoClienteGuardado = $ordenCompraData['OC_TIPO_CLIENTE'] ?? ''; ?>
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Tipo de Cliente <span style="color: red; font-size: 12px;">*</span></div>
                <div style="background:#fdeee2; flex:1; padding:4px; display:flex; gap:15px; align-items:center;">
                    <label><input type="radio" name="OC_TIPO_CLIENTE" value="natural" <?= $tipoClienteGuardado === 'natural' ? 'checked' : '' ?>> Persona natural</label>
                    <label><input type="radio" name="OC_TIPO_CLIENTE" value="ruc" <?= $tipoClienteGuardado === 'ruc' ? 'checked' : '' ?>> P. Natural con RUC</label>
                    <label><input type="radio" name="OC_TIPO_CLIENTE" value="juridica" <?= $tipoClienteGuardado === 'juridica' ? 'checked' : '' ?>> Persona Jurídica</label>
                </div>
            </div>

            <!-- Fila 1 -->
            <div style="display:flex; border-bottom:1px solid #000; overflow:hidden;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:80px; flex-shrink:0;">Nombre</div>
                <input type="text" name="OC_COMPRADOR_NOMBRE" id="comprador_nombre" style="width:110px; max-width:110px; flex-shrink:0;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? '') ?>">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px; flex-shrink:0;">Apellido</div>
                <input type="text" name="OC_COMPRADOR_APELLIDO" id="comprador_apellido" style="width:110px; max-width:110px; flex-shrink:0;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_COMPRADOR_APELLIDO'] ?? '') ?>">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px; flex-shrink:0;">TIPO DOC <span style="color: red; font-size: 12px;">*</span></div>
                <?php $tipoDocComprador = $ordenCompraData['OC_COMPRADOR_TIPO_DOCUMENTO'] ?? ''; ?>
                <select name="OC_COMPRADOR_TIPO_DOCUMENTO" style="width:100px; max-width:100px; flex-shrink:0;">
                    <option value="" <?= $tipoDocComprador === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                    <option value="dni"    <?= $tipoDocComprador === 'dni' ? 'selected' : '' ?>>DNI</option>
                    <option value="carnet" <?= $tipoDocComprador === 'carnet' ? 'selected' : '' ?>>CARNET EXTRANJERIA</option>
                    <option value="ruc"    <?= $tipoDocComprador === 'ruc' ? 'selected' : '' ?>>RUC</option>
                </select>
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px; flex-shrink:0;">NRO. DOC</div>
                <input type="text" name="OC_COMPRADOR_NUMERO_DOCUMENTO" style="width:100px; max-width:100px; flex-shrink:0;" oninput="validarNumeroDocumentoComprador()"
                       value="<?= htmlspecialchars($ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? '') ?>">
            </div>

            <!-- Fila 2 -->
            <div style="display:flex; border-bottom:1px solid #000; overflow:hidden;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px; flex-shrink:0;">Tipo Doc. de venta <span style="color: red; font-size: 12px;">*</span></div>
                <?php $tipoDocVenta = $ordenCompraData['OC_TIPO_DOCUMENTO_VENTA'] ?? ''; ?>
                <select name="OC_TIPO_DOCUMENTO_VENTA" style="width:120px; max-width:120px; flex-shrink:0;">
                    <option value="" <?= $tipoDocVenta === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                    <option value="boleta"  <?= $tipoDocVenta === 'boleta' ? 'selected' : '' ?>>BOLETA DE VENTA</option>
                    <option value="factura" <?= $tipoDocVenta === 'factura' ? 'selected' : '' ?>>FACTURA DE VENTA</option>
                </select>
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; margin-left:20px; flex-shrink:0;">Fuente
                    Contacto <span style="color: red; font-size: 12px;">*</span></div>
                <?php $fuenteContacto = $ordenCompraData['OC_FUENTE_CONTACTO'] ?? ''; ?>
                <select name="OC_FUENTE_CONTACTO" style="width:160px; max-width:160px; flex-shrink:0;">
                    <option value="" <?= $fuenteContacto === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                    <option value="digital_marca"    <?= $fuenteContacto === 'digital_marca' ? 'selected' : '' ?>>Digital Marca</option>
                    <option value="digital_dealer"   <?= $fuenteContacto === 'digital_dealer' ? 'selected' : '' ?>>Digital Dealer</option>
                    <option value="trabajo_campo"    <?= $fuenteContacto === 'trabajo_campo' ? 'selected' : '' ?>>Trabajo Campo / Campañas</option>
                    <option value="afluencia_piso"   <?= $fuenteContacto === 'afluencia_piso' ? 'selected' : '' ?>>Afluencia Piso</option>
                    <option value="recomendado"      <?= $fuenteContacto === 'recomendado' ? 'selected' : '' ?>>Recomendado</option>
                    <option value="digital_agencias" <?= $fuenteContacto === 'digital_agencias' ? 'selected' : '' ?>>Digital Agencias</option>
                    <option value="recurrente"       <?= $fuenteContacto === 'recurrente' ? 'selected' : '' ?>>Recurrente</option>
                </select>
            </div>

            <!-- Fila 3 -->
            <div style="display:flex; border-bottom:1px solid #000; overflow:hidden;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; flex-shrink:0;">Fecha de Nac.</div>
                <input type="date" name="OC_FECHA_NACIMIENTO" id="fecha_nacimiento" style="width:100px; max-width:100px; flex-shrink:0;" value="<?php if (isset($ordenCompraData['OC_FECHA_NACIMIENTO'])) { $fechaNac = $ordenCompraData['OC_FECHA_NACIMIENTO']; if ($fechaNac instanceof DateTime) { echo $fechaNac->format('Y-m-d'); } else { echo htmlspecialchars($fechaNac); } } ?>" onchange="validarEdadMinima(this)">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:73px; flex-shrink:0;">Estado Civil <span style="color: red; font-size: 12px;">*</span></div>
                <?php $estadoCivil = $ordenCompraData['OC_ESTADO_CIVIL'] ?? ''; ?>
                <select name="OC_ESTADO_CIVIL" style="width:100px; max-width:100px; flex-shrink:0;">
                    <option value="" <?= $estadoCivil === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                    <option value="soltero"     <?= $estadoCivil === 'soltero' ? 'selected' : '' ?>>SOLTERO</option>
                    <option value="casado"      <?= $estadoCivil === 'casado' ? 'selected' : '' ?>>CASADO</option>
                    <option value="divorciado"  <?= $estadoCivil === 'divorciado' ? 'selected' : '' ?>>DIVORCIADO</option>
                    <option value="separado"    <?= $estadoCivil === 'separado' ? 'selected' : '' ?>>SEPARADO</option>
                    <option value="concubino"   <?= $estadoCivil === 'concubino' ? 'selected' : '' ?>>CONCUBINA(O)</option>
                    <option value="conviviente" <?= $estadoCivil === 'conviviente' ? 'selected' : '' ?>>CONVIVIENTE</option>
                </select>
                <div style="background:#ffffff; font-weight:bold; padding:4px; margin-left:5px; flex-shrink:0;">Situación Laboral <span style="color: red; font-size: 12px;">*</span></div>
                <?php $sitLaboral = $ordenCompraData['OC_SITUACION_LABORAL'] ?? ''; ?>
                <select name="OC_SITUACION_LABORAL" style="width:120px; max-width:120px; flex-shrink:0;">
                    <option value="" <?= $sitLaboral === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                    <option value="empleado"     <?= $sitLaboral === 'empleado' ? 'selected' : '' ?>>EMPLEADO</option>
                    <option value="independiente"<?= $sitLaboral === 'independiente' ? 'selected' : '' ?>>INDEPENDIENTE</option>
                    <option value="negociante"   <?= $sitLaboral === 'negociante' ? 'selected' : '' ?>>NEGOCIANTE</option>
                    <option value="jubilado"     <?= $sitLaboral === 'jubilado' ? 'selected' : '' ?>>JUBILADO</option>
                    <option value="dependiente"  <?= $sitLaboral === 'dependiente' ? 'selected' : '' ?>>DEPENDIENTE</option>
                    <option value="desempleado"  <?= $sitLaboral === 'desempleado' ? 'selected' : '' ?>>DESEMPLEADO</option>
                    <option value="otros"        <?= $sitLaboral === 'otros' ? 'selected' : '' ?>>OTROS</option>
                </select>
            </div>

            <!-- Fila 4 -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Cónyuge</div>
                <input type="text" name="OC_CONYUGE_NOMBRE" style="width:130px;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_CONYUGE_NOMBRE'] ?? '') ?>">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">TIPO DOC <span style="color: red; font-size: 12px;">*</span></div>
                <?php $tipoDocConyuge = $ordenCompraData['OC_CONYUGE_TIPO_DOCUMENTO'] ?? ''; ?>
                <select name="OC_CONYUGE_TIPO_DOCUMENTO" style="width:100px;">
                    <option value="" <?= $tipoDocConyuge === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                    <option value="dni"      <?= $tipoDocConyuge === 'dni' ? 'selected' : '' ?>>DNI</option>
                    <option value="pasaporte"<?= $tipoDocConyuge === 'pasaporte' ? 'selected' : '' ?>>PASAPORTE</option>
                    <option value="carnet"   <?= $tipoDocConyuge === 'carnet' ? 'selected' : '' ?>>CARNET EXTRANJERIA</option>
                </select>
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">NRO. DOC</div>
                <input type="text" name="OC_CONYUGE_NUMERO_DOCUMENTO" id="conyuge_numero_doc" style="flex:1;" pattern="[0-9]*" title="Solo números"
                       value="<?= htmlspecialchars($ordenCompraData['OC_CONYUGE_NUMERO_DOCUMENTO'] ?? '') ?>">
            </div>

            <!-- Fila 5 -->
            <div style="display:flex; border-bottom:1px solid #000; overflow:hidden;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; flex-shrink:0;">Dirección</div>
                <input type="text" name="OC_DIRECCION_CLIENTE" style="width:320px; max-width:320px; flex-shrink:0;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_DIRECCION_CLIENTE'] ?? '') ?>">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px; margin-left:5px; flex-shrink:0;">Teléfonos <span style="color: red; font-size: 12px;">*</span></div>
                <input type="text" name="OC_TELEFONO_CLIENTE" style="width:80px; max-width:80px; flex-shrink:0;" maxlength="9" pattern="[0-9]{9}" title="Ingrese 9 dígitos" required
                       value="<?= htmlspecialchars($ordenCompraData['OC_TELEFONO_CLIENTE'] ?? '') ?>">
                <input type="text" name="OC_TELEFONO_ADICIONAL" placeholder="Tel. 2" style="width:80px; max-width:80px; margin-left:5px; flex-shrink:0;" maxlength="9" pattern="[0-9]{9}" title="Ingrese 9 dígitos"
                       value="<?= htmlspecialchars($ordenCompraData['OC_TELEFONO_ADICIONAL'] ?? '') ?>">
            </div>

            <!-- Fila 6 -->
            <div style="display:flex; border-bottom:1px solid #000; overflow:hidden;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; flex-shrink:0;">Email <span style="color: red; font-size: 12px;">*</span></div>
                <input type="email" name="OC_EMAIL_CLIENTE" style="width:250px; max-width:250px; flex-shrink:0;" required
                       value="<?= htmlspecialchars($ordenCompraData['OC_EMAIL_CLIENTE'] ?? '') ?>">
                <input type="email" name="OC_EMAIL_CLIENTE_2" placeholder="Email 2" style="width:250px; max-width:250px; margin-left:5px; flex-shrink:0;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_EMAIL_CLIENTE_2'] ?? '') ?>">
            </div>

            <!-- Fila 7 -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Ocupación</div>
                <input type="text" name="OC_OCUPACION_CLIENTE" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_OCUPACION_CLIENTE'] ?? '') ?>">
            </div>

            <!-- Hobbies -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Hobbies</div>
                <input type="text" name="OC_HOBBIES_CLIENTE" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_HOBBIES_CLIENTE'] ?? '') ?>">
            </div>

            <!-- Nombre / Razon Social -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Nombre / Razón Social</div>
                <input type="text" name="OC_PROPIETARIO_NOMBRE" class="no-adjust" style="width:280px;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_PROPIETARIO_NOMBRE'] ?? '') ?>">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:50px;">RUC</div>
                <input type="text" name="OC_PROPIETARIO_RUC" style="width:90px;" maxlength="11" pattern="[0-9]{11}" title="Ingrese 11 dígitos"
                       value="<?= htmlspecialchars($ordenCompraData['OC_PROPIETARIO_RUC'] ?? '') ?>">
            </div>

            <!-- Co-propietario -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Co-propietario / Cónyuge
                </div>
                <input type="text" name="OC_COPROPIETARIO_NOMBRE" class="no-adjust" style="width:280px;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_COPROPIETARIO_NOMBRE'] ?? '') ?>">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:50px;">DNI</div>
                <input type="text" name="OC_COPROPIETARIO_DNI" style="width:90px;" maxlength="8" pattern="[0-9]{8}" title="Ingrese 8 dígitos"
                       value="<?= htmlspecialchars($ordenCompraData['OC_COPROPIETARIO_DNI'] ?? '') ?>">
            </div>

            <!-- Representante legal -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Representante legal</div>
                <input type="text" name="OC_REPRESENTANTE_LEGAL" class="no-adjust" style="width:280px;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_REPRESENTANTE_LEGAL'] ?? '') ?>">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:50px;">DNI</div>
                <input type="text" name="OC_REPRESENTANTE_DNI" style="width:90px;" maxlength="8" pattern="[0-9]{8}" title="Ingrese 8 dígitos"
                       value="<?= htmlspecialchars($ordenCompraData['OC_REPRESENTANTE_DNI'] ?? '') ?>">
            </div>

            <!-- Tarjeta a Nombre de -->
            <div style="display:flex;">
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Tarjeta a Nombre de</div>
                <input type="text" name="OC_TARJETA_NOMBRE" id="tarjeta_nombre" style="flex:1;" autocomplete="off"
                       value="<?= htmlspecialchars($ordenCompraData['OC_TARJETA_NOMBRE'] ?? '') ?>">
            </div>
        </div>
    </div>

    <!-- VEHÍCULO -->
    <div style="display:flex; border:1px solid #000;width:774px;margin:10px auto 0;">
        <!-- Columna lateral -->
        <div
            style="writing-mode: vertical-lr; transform: rotate(180deg); background:#ffffff; font-weight:bold; text-align:center; padding:8px; border-left:1px solid #000;">
            Vehículo
        </div>

        <!-- Sección derecha -->
        <div style="flex:1;">
            <!-- Encabezados -->
            <div style="display:flex; border-bottom:1px solid #000; text-align:center; font-weight:bold;">
                <div style="flex:1; padding:4px;">Chasis <span style="color: red; font-size: 12px;">*</span></div>
                <div style="flex:1; padding:4px;">Marca</div>
                <div style="flex:1; padding:4px;">Modelo</div>
                <div style="flex:1; padding:4px;">Versión</div>
                <div style="flex:1; padding:4px;">FSC / Código</div>
            </div>
            <!-- Inputs -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <input type="text" name="OC_VEHICULO_CHASIS" id="inputChasis" style="flex:1;" 
                       onclick="<?php if (!isset($modoImpresion) || !$modoImpresion): ?>abrirModalVehiculos()<?php endif; ?>" 
                       placeholder="Click para ver vehículos asignados"
                       value="<?= htmlspecialchars($ordenCompraData['OC_VEHICULO_CHASIS'] ?? '') ?>">
                <input type="text" name="OC_VEHICULO_MARCA" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_VEHICULO_MARCA'] ?? '') ?>">
                <input type="text" name="OC_VEHICULO_MODELO" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_VEHICULO_MODELO'] ?? '') ?>">
                <input type="text" name="OC_VEHICULO_VERSION" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_VEHICULO_VERSION'] ?? '') ?>">
                <input type="text" name="OC_VEHICULO_CODIGO_FSC" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_VEHICULO_CODIGO_FSC'] ?? '') ?>">
            </div>

            <!-- Encabezados -->
            <div style="display:flex; border-bottom:1px solid #000; text-align:center; font-weight:bold;">
                <div style="flex:1; padding:4px;">Motor</div>
                <div style="flex:1; padding:4px;">Clase</div>
                <div style="flex:1; padding:4px;">Color</div>
                <div style="flex:1; padding:4px;">Año Mod.</div>
            </div>
            <!-- Inputs -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <input type="text" name="OC_VEHICULO_MOTOR" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_VEHICULO_MOTOR'] ?? '') ?>">
                <input type="text" name="OC_VEHICULO_CLASE" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_VEHICULO_CLASE'] ?? '') ?>">
                <input type="text" name="OC_VEHICULO_COLOR" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_VEHICULO_COLOR'] ?? '') ?>">
                <input type="text" name="OC_VEHICULO_ANIO_MODELO" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_VEHICULO_ANIO_MODELO'] ?? '') ?>">
            </div>
            
            <!-- Campo oculto para tipo de combustible -->
            <input type="hidden" name="OC_VEHICULO_TIPO_COMBUSTIBLE" value="<?php echo htmlspecialchars($ordenCompraData['OC_VEHICULO_TIPO_COMBUSTIBLE'] ?? ''); ?>">

            <!-- Encabezados -->
            <div style="display:flex; border-bottom:1px solid #000; text-align:center; font-weight:bold;">
                <div style="flex:1; padding:4px;">Período Garantía</div>
                <div style="flex:1; padding:4px;">Periodicidad de mantenimientos</div>
                <div style="flex:1; padding:4px;">Primer mantenimiento</div>
            </div>
            <!-- Inputs -->
            <div style="display:flex; border-bottom:1px solid #000;">
                <input type="text" name="OC_PERIODO_GARANTIA" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_PERIODO_GARANTIA'] ?? '') ?>">
                <input type="text" name="OC_PERIODICIDAD_MANTENIMIENTO" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_PERIODICIDAD_MANTENIMIENTO'] ?? '') ?>">
                <input type="text" name="OC_PRIMER_MANTENIMIENTO" style="flex:1;"
                       value="<?= htmlspecialchars($ordenCompraData['OC_PRIMER_MANTENIMIENTO'] ?? '') ?>">
            </div>

        </div>
    </div>


    <!-- VALOR DE LA COMPRA / FORMA DE PAGO -->
    <!-- VALOR DE LA COMPRA / FORMA DE PAGO -->
    <div style="display:flex; border:1px solid #000; width:774px; margin:10px auto 0;">

        <!-- Columna izquierda -->
        <div style="width:50%; display:flex; border-right:1px solid #000;">
            <!-- Lateral -->
            <div
                style="writing-mode: vertical-lr; transform: rotate(180deg); background:#ffffff; font-weight:bold; text-align:center; padding:8px; border-left:1px solid #000;">
                Valor de la Compra
            </div>
            <!-- Contenido -->
            <div style="flex:1;">
                <!-- Forma de pago -->
                <?php $formaPagoGuardada = $ordenCompraData['OC_FORMA_PAGO'] ?? ''; ?>
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Forma de pago: <span style="color: red; font-size: 12px;">*</span></div>
                    <select name="OC_FORMA_PAGO" id="formaPago" onchange="toggleCuotaInicial()" style="flex:1;" required>
                        <option value="" <?= $formaPagoGuardada === '' ? 'selected' : '' ?>>-- Seleccione forma de pago --</option>
                        <option value="CONTADO" <?= $formaPagoGuardada === 'CONTADO' ? 'selected' : '' ?>>CONTADO</option>
                        <option value="CRÉDITO" <?= $formaPagoGuardada === 'CRÉDITO' ? 'selected' : '' ?>>CRÉDITO</option>
                    </select>
                </div>

                <?php
                    // Calcular montos a mostrar solo para la vista del cliente cuando OC_FAKE_PRECIO está activo
                    $mostrarPrecioVenta      = $ordenCompraData['OC_PRECIO_VENTA']        ?? '';
                    $mostrarTotalEquip      = $ordenCompraData['OC_TOTAL_EQUIPAMIENTO']  ?? '';
                    $mostrarPrecioTotalComp = $ordenCompraData['OC_PRECIO_TOTAL_COMPRA'] ?? '';
                    $mostrarSaldoPendiente  = $ordenCompraData['OC_SALDO_PENDIENTE']     ?? '';

                    // Montos de equipamiento individual
                    $mostrarEquip1 = $ordenCompraData['OC_EQUIPAMIENTO_ADICIONAL_1'] ?? '';
                    $mostrarEquip2 = $ordenCompraData['OC_EQUIPAMIENTO_ADICIONAL_2'] ?? '';
                    $mostrarEquip3 = $ordenCompraData['OC_EQUIPAMIENTO_ADICIONAL_3'] ?? '';
                    $mostrarEquip4 = $ordenCompraData['OC_EQUIPAMIENTO_ADICIONAL_4'] ?? '';
                    $mostrarEquip5 = $ordenCompraData['OC_EQUIPAMIENTO_ADICIONAL_5'] ?? '';

                    if (isset($esVistaCliente) && $esVistaCliente && !empty($ordenCompraData['OC_FAKE_PRECIO'])) {
                        $precioVentaReal = isset($ordenCompraData['OC_PRECIO_VENTA']) ? (float)$ordenCompraData['OC_PRECIO_VENTA'] : 0;
                        $totalEquipReal  = isset($ordenCompraData['OC_TOTAL_EQUIPAMIENTO']) ? (float)$ordenCompraData['OC_TOTAL_EQUIPAMIENTO'] : 0;
                        $pagoCuentaReal  = isset($ordenCompraData['OC_PAGO_CUENTA']) ? (float)$ordenCompraData['OC_PAGO_CUENTA'] : 0;

                        $nuevoPrecioVenta = $precioVentaReal + $totalEquipReal;
                        $nuevoPrecioTotal = $nuevoPrecioVenta;
                        $nuevoSaldo       = $nuevoPrecioTotal - $pagoCuentaReal;

                        // Maquillaje: mover equipamiento al precio de venta y ocultar montos
                        $mostrarPrecioVenta      = number_format($nuevoPrecioVenta, 2, '.', '');
                        $mostrarTotalEquip       = number_format(0, 2, '.', '');
                        $mostrarPrecioTotalComp  = number_format($nuevoPrecioTotal, 2, '.', '');
                        $mostrarSaldoPendiente   = number_format($nuevoSaldo, 2, '.', '');

                        // Cada línea de equipamiento adicional se muestra como 0
                        $mostrarEquip1 = number_format(0, 2, '.', '');
                        $mostrarEquip2 = number_format(0, 2, '.', '');
                        $mostrarEquip3 = number_format(0, 2, '.', '');
                        $mostrarEquip4 = number_format(0, 2, '.', '');
                        $mostrarEquip5 = number_format(0, 2, '.', '');
                    }
                ?>

                <!-- Precio de venta -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Precio de venta</div>
                    <?php $monedaPrecioVenta = $ordenCompraData['OC_MONEDA_PRECIO_VENTA'] ?? 'US$'; ?>
                    <select name="OC_MONEDA_PRECIO_VENTA" style="width:70px; margin-right:5px;">
                        <option value="US$" <?= $monedaPrecioVenta === 'US$' ? 'selected' : '' ?>>US$</option>
                        <option value="MN"  <?= $monedaPrecioVenta === 'MN'  ? 'selected' : '' ?>>MN</option>
                    </select>
                    <input type="text" name="OC_PRECIO_VENTA" id="precioVenta" style="flex:1;" value="<?= htmlspecialchars($mostrarPrecioVenta) ?>">
                </div>

                <!-- Bono Financiamiento -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:110px;">Bono Financiamiento
                    </div>
                    <?php $monedaBonoFin = $ordenCompraData['OC_MONEDA_BONO_FINANCIAMIENTO'] ?? 'US$'; ?>
                    <select name="OC_MONEDA_BONO_FINANCIAMIENTO" style="width:70px; margin-right:5px;">
                        <option value="US$" <?= $monedaBonoFin === 'US$' ? 'selected' : '' ?>>US$</option>
                        <option value="MN"  <?= $monedaBonoFin === 'MN'  ? 'selected' : '' ?>>MN</option>
                    </select>
                    <input type="text" name="OC_BONO_FINANCIAMIENTO" style="flex:1;"
                           value="<?= htmlspecialchars($ordenCompraData['OC_BONO_FINANCIAMIENTO'] ?? '') ?>">
                </div>

                <!-- Bono de Campaña -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:110px;">Bono de Campaña
                    </div>
                    <?php $monedaBonoCamp = $ordenCompraData['OC_MONEDA_BONO_CAMPANA'] ?? 'US$'; ?>
                    <select name="OC_MONEDA_BONO_CAMPANA" style="width:70px; margin-right:5px;">
                        <option value="US$" <?= $monedaBonoCamp === 'US$' ? 'selected' : '' ?>>US$</option>
                        <option value="MN"  <?= $monedaBonoCamp === 'MN'  ? 'selected' : '' ?>>MN</option>
                    </select>
                    <input type="text" name="OC_BONO_CAMPANA" style="flex:1;"
                           value="<?= htmlspecialchars($ordenCompraData['OC_BONO_CAMPANA'] ?? '') ?>">
                </div>

                <!-- Cuota Inicial -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:110px;">Cuota Inicial
                    </div>
                    <?php $monedaCuotaIni = $ordenCompraData['OC_MONEDA_CUOTA_INICIAL'] ?? 'US$'; ?>
                    <select name="OC_MONEDA_CUOTA_INICIAL" id="monedaCuotaInicial" style="width:70px; margin-right:5px;" disabled>
                        <option value="US$" <?= $monedaCuotaIni === 'US$' ? 'selected' : '' ?>>US$</option>
                        <option value="MN"  <?= $monedaCuotaIni === 'MN'  ? 'selected' : '' ?>>MN</option>
                    </select>
                    <input type="text" name="OC_CUOTA_INICIAL" id="cuotaInicial" style="flex:1;" disabled
                           value="<?= htmlspecialchars($ordenCompraData['OC_CUOTA_INICIAL'] ?? '') ?>">
                </div>

                <!-- Equipamiento adicional -->
                <div style="background:#ffffff; font-weight:bold; padding:4px;">Equipamiento adicional</div>
                <div style="border-bottom:1px solid #000; padding:4px; overflow:hidden;">
                    <?php 
                        $descEq1 = $ordenCompraData['OC_DESCRIPCION_EQUIPAMIENTO_1'] ?? '';
                        $monEq1  = $ordenCompraData['OC_MONEDA_EQUIPAMIENTO_1'] ?? 'US$';
                        $descEq2 = $ordenCompraData['OC_DESCRIPCION_EQUIPAMIENTO_2'] ?? '';
                        $monEq2  = $ordenCompraData['OC_MONEDA_EQUIPAMIENTO_2'] ?? 'US$';
                        $descEq3 = $ordenCompraData['OC_DESCRIPCION_EQUIPAMIENTO_3'] ?? '';
                        $monEq3  = $ordenCompraData['OC_MONEDA_EQUIPAMIENTO_3'] ?? 'US$';
                        $descEq4 = $ordenCompraData['OC_DESCRIPCION_EQUIPAMIENTO_4'] ?? '';
                        $monEq4  = $ordenCompraData['OC_MONEDA_EQUIPAMIENTO_4'] ?? 'US$';
                        $descEq5 = $ordenCompraData['OC_DESCRIPCION_EQUIPAMIENTO_5'] ?? '';
                        $monEq5  = $ordenCompraData['OC_MONEDA_EQUIPAMIENTO_5'] ?? 'US$';
                    ?>
                    <div style="display:flex; margin-bottom:4px; overflow:hidden;">
                        <select name="OC_DESCRIPCION_EQUIPAMIENTO_1" style="width:110px; max-width:110px; margin-right:3px; flex-shrink:0;">
                            <option value="" <?= $descEq1 === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                            <option value="ACCESORIOS" <?= $descEq1 === 'ACCESORIOS' ? 'selected' : '' ?>>ACCESORIOS</option>
                            <option value="GPS"        <?= $descEq1 === 'GPS' ? 'selected' : '' ?>>GPS</option>
                            <option value="GLP"        <?= $descEq1 === 'GLP' ? 'selected' : '' ?>>GLP</option>
                            <option value="PPM"        <?= $descEq1 === 'PPM' ? 'selected' : '' ?>>PPM</option>
                            <option value="SEGURO"     <?= $descEq1 === 'SEGURO' ? 'selected' : '' ?>>SEGURO</option>
                        </select>
                        <select name="OC_MONEDA_EQUIPAMIENTO_1" style="width:50px; max-width:50px; margin-right:3px; flex-shrink:0;">
                            <option value="US$" <?= $monEq1 === 'US$' ? 'selected' : '' ?>>US$</option>
                            <option value="MN"  <?= $monEq1 === 'MN'  ? 'selected' : '' ?>>MN</option>
                        </select>
                        <input type="text" name="OC_EQUIPAMIENTO_ADICIONAL_1" style="width:80px; max-width:80px; flex-shrink:0;" value="<?= htmlspecialchars($mostrarEquip1) ?>">
                    </div>
                    <div style="display:flex; margin-bottom:4px; overflow:hidden;">
                        <select name="OC_DESCRIPCION_EQUIPAMIENTO_2" style="width:110px; max-width:110px; margin-right:3px; flex-shrink:0;">
                            <option value="" <?= $descEq2 === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                            <option value="ACCESORIOS" <?= $descEq2 === 'ACCESORIOS' ? 'selected' : '' ?>>ACCESORIOS</option>
                            <option value="GPS"        <?= $descEq2 === 'GPS' ? 'selected' : '' ?>>GPS</option>
                            <option value="GLP"        <?= $descEq2 === 'GLP' ? 'selected' : '' ?>>GLP</option>
                            <option value="PPM"        <?= $descEq2 === 'PPM' ? 'selected' : '' ?>>PPM</option>
                            <option value="SEGURO"     <?= $descEq2 === 'SEGURO' ? 'selected' : '' ?>>SEGURO</option>
                        </select>
                        <select name="OC_MONEDA_EQUIPAMIENTO_2" style="width:50px; max-width:50px; margin-right:3px; flex-shrink:0;">
                            <option value="US$" <?= $monEq2 === 'US$' ? 'selected' : '' ?>>US$</option>
                            <option value="MN"  <?= $monEq2 === 'MN'  ? 'selected' : '' ?>>MN</option>
                        </select>
                        <input type="text" name="OC_EQUIPAMIENTO_ADICIONAL_2" style="width:80px; max-width:80px; flex-shrink:0;" value="<?= htmlspecialchars($mostrarEquip2) ?>">
                    </div>
                    <div style="display:flex; margin-bottom:4px; overflow:hidden;">
                        <select name="OC_DESCRIPCION_EQUIPAMIENTO_3" style="width:110px; max-width:110px; margin-right:3px; flex-shrink:0;">
                            <option value="" <?= $descEq3 === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                            <option value="ACCESORIOS" <?= $descEq3 === 'ACCESORIOS' ? 'selected' : '' ?>>ACCESORIOS</option>
                            <option value="GPS"        <?= $descEq3 === 'GPS' ? 'selected' : '' ?>>GPS</option>
                            <option value="GLP"        <?= $descEq3 === 'GLP' ? 'selected' : '' ?>>GLP</option>
                            <option value="PPM"        <?= $descEq3 === 'PPM' ? 'selected' : '' ?>>PPM</option>
                            <option value="SEGURO"     <?= $descEq3 === 'SEGURO' ? 'selected' : '' ?>>SEGURO</option>
                        </select>
                        <select name="OC_MONEDA_EQUIPAMIENTO_3" style="width:50px; max-width:50px; margin-right:3px; flex-shrink:0;">
                            <option value="US$" <?= $monEq3 === 'US$' ? 'selected' : '' ?>>US$</option>
                            <option value="MN"  <?= $monEq3 === 'MN'  ? 'selected' : '' ?>>MN</option>
                        </select>
                        <input type="text" name="OC_EQUIPAMIENTO_ADICIONAL_3" style="width:80px; max-width:80px; flex-shrink:0;" value="<?= htmlspecialchars($mostrarEquip3) ?>">
                    </div>
                    <div style="display:flex; margin-bottom:4px; overflow:hidden;">
                        <select name="OC_DESCRIPCION_EQUIPAMIENTO_4" style="width:110px; max-width:110px; margin-right:3px; flex-shrink:0;">
                            <option value="" <?= $descEq4 === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                            <option value="ACCESORIOS" <?= $descEq4 === 'ACCESORIOS' ? 'selected' : '' ?>>ACCESORIOS</option>
                            <option value="GPS"        <?= $descEq4 === 'GPS' ? 'selected' : '' ?>>GPS</option>
                            <option value="GLP"        <?= $descEq4 === 'GLP' ? 'selected' : '' ?>>GLP</option>
                            <option value="PPM"        <?= $descEq4 === 'PPM' ? 'selected' : '' ?>>PPM</option>
                            <option value="SEGURO"     <?= $descEq4 === 'SEGURO' ? 'selected' : '' ?>>SEGURO</option>
                        </select>
                        <select name="OC_MONEDA_EQUIPAMIENTO_4" style="width:50px; max-width:50px; margin-right:3px; flex-shrink:0;">
                            <option value="US$" <?= $monEq4 === 'US$' ? 'selected' : '' ?>>US$</option>
                            <option value="MN"  <?= $monEq4 === 'MN'  ? 'selected' : '' ?>>MN</option>
                        </select>
                        <input type="text" name="OC_EQUIPAMIENTO_ADICIONAL_4" style="width:80px; max-width:80px; flex-shrink:0;" value="<?= htmlspecialchars($mostrarEquip4) ?>">
                    </div>
                    <div style="display:flex; overflow:hidden;">
                        <select name="OC_DESCRIPCION_EQUIPAMIENTO_5" style="width:110px; max-width:110px; margin-right:3px; flex-shrink:0;">
                            <option value="" <?= $descEq5 === '' ? 'selected' : '' ?>>-- Seleccione --</option>
                            <option value="ACCESORIOS" <?= $descEq5 === 'ACCESORIOS' ? 'selected' : '' ?>>ACCESORIOS</option>
                            <option value="GPS"        <?= $descEq5 === 'GPS' ? 'selected' : '' ?>>GPS</option>
                            <option value="GLP"        <?= $descEq5 === 'GLP' ? 'selected' : '' ?>>GLP</option>
                            <option value="PPM"        <?= $descEq5 === 'PPM' ? 'selected' : '' ?>>PPM</option>
                            <option value="SEGURO"     <?= $descEq5 === 'SEGURO' ? 'selected' : '' ?>>SEGURO</option>
                        </select>
                        <select name="OC_MONEDA_EQUIPAMIENTO_5" style="width:50px; max-width:50px; margin-right:3px; flex-shrink:0;">
                            <option value="US$" <?= $monEq5 === 'US$' ? 'selected' : '' ?>>US$</option>
                            <option value="MN"  <?= $monEq5 === 'MN'  ? 'selected' : '' ?>>MN</option>
                        </select>
                        <input type="text" name="OC_EQUIPAMIENTO_ADICIONAL_5" style="width:80px; max-width:80px; flex-shrink:0;" value="<?= htmlspecialchars($mostrarEquip5) ?>">
                    </div>
                </div>

                <!-- Total equipamiento -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Total Equipamiento
                    </div>
                    <input type="text" name="OC_TOTAL_EQUIPAMIENTO" style="flex:1;" value="<?= htmlspecialchars($mostrarTotalEquip) ?>">
                </div>

                <!-- Precio compra total -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:130px;">Precio compra total
                    </div>
                    <select name="OC_MONEDA_PRECIO_TOTAL" style="width:70px; margin-right:5px;">
                        <option value="US$" selected>US$</option>
                        <option value="MN">MN</option>
                    </select>
                    <input type="text" name="OC_PRECIO_TOTAL_COMPRA" style="flex:1;" value="<?= htmlspecialchars($mostrarPrecioTotalComp) ?>">
                </div>

                <!-- Tipo cambio -->
                <div style="display:flex; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:130px;">Tipo Cambio Ref. S/.
                    </div>
                    <input type="text" name="OC_TIPO_CAMBIO" style="width:80px; margin-right:5px;" placeholder="3.93">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:30px;">S/.</div>
                    <input type="text" name="OC_TIPO_CAMBIO_SOL" style="flex:1;" placeholder="76,635.00">
                </div>

                <!-- Modificar precio -->
                <div style="display:flex; align-items:center; border-top:1px solid #000; padding:2px 4px;">
                    <label style="display:flex; align-items:center; gap:4px; font-weight:bold; cursor:pointer;">
                        <input type="checkbox" name="OC_FAKE_PRECIO" style="width:auto; margin:0;">
                        <span>fk fake</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Columna derecha -->
        <div style="width:50%; display:flex;">
            <!-- Lateral -->
            <div
                style="writing-mode: vertical-lr; transform: rotate(180deg); background:#ffffff; font-weight:bold; text-align:center; padding:8px; border-left:1px solid #000;">
                Forma de Pago
            </div>
            <!-- Contenido -->
            <div style="flex:1;">
                <!-- Pago a cuenta -->
                <?php 
                    $monedaPagoCuenta = $ordenCompraData['OC_MONEDA_PAGO_CUENTA'] ?? 'US$';
                    $pagoCuentaVal    = $ordenCompraData['OC_PAGO_CUENTA'] ?? '';
                ?>
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Pago a cuenta</div>
                    <select name="OC_MONEDA_PAGO_CUENTA" style="width:70px; margin-right:5px;">
                        <option value="US$" <?= $monedaPagoCuenta === 'US$' ? 'selected' : '' ?>>US$</option>
                        <option value="MN"  <?= $monedaPagoCuenta === 'MN'  ? 'selected' : '' ?>>MN</option>
                    </select>
                    <input type="text" name="OC_PAGO_CUENTA" id="pagoCuenta" style="flex:1;" autocomplete="off"
                           value="<?= htmlspecialchars($pagoCuentaVal) ?>">
                </div>

                <!-- Saldo -->
                <div style="display:flex; border-bottom:1px solid #000; align-items:center;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px;">Saldo</div>
                    <select name="OC_MONEDA_SALDO" style="width:70px; margin-right:5px;">
                        <option value="US$" selected>US$</option>
                        <option value="MN">MN</option>
                    </select>
                    <input type="text" name="OC_SALDO_PENDIENTE" style="flex:1;" autocomplete="off">
                </div>

                <!-- TABLA DE ABONOS -->
                <div style="background:#ffffff; font-weight:bold; padding:6px 4px; margin-top:10px; border-bottom:1px solid #000; text-align:center;">
                    DETALLE DE ABONOS
                </div>
                
                <!-- Encabezados de tabla -->
                <div style="display:flex; background:#e5e7eb; border-bottom:1px solid #000; font-weight:bold; font-size:9px; text-align:center;">
                    <div style="flex:0.8; padding:3px; border-right:1px solid #000;">Monto</div>
                    <div style="flex:1.2; padding:3px; border-right:1px solid #000;">Nro. Operación</div>
                    <div style="flex:1.5; padding:3px; border-right:1px solid #000;">Entidad Financiera</div>
                    <div style="flex:1.5; padding:3px;">Archivo Voucher</div>
                </div>

                <!-- Contenedor de abonos dinámicos -->
                <div id="abonos-container">
                    <?php if (isset($modoImpresion) && $modoImpresion && isset($ordenCompraData)): ?>
                        <?php 
                        for ($i = 1; $i <= 7; $i++):
                            $monto = $ordenCompraData['OC_MONTO_' . $i] ?? '';
                            $nroOp = $ordenCompraData['OC_NRO_OPERACION_' . $i] ?? '';
                            $entidad = $ordenCompraData['OC_ENTIDAD_FINANCIERA_' . $i] ?? '';
                            
                            // Solo mostrar fila si hay al menos un dato
                            if (!empty($monto) || !empty($nroOp) || !empty($entidad)):
                        ?>
                            <div style="display:flex; align-items:center; border-bottom:1px solid #000; font-size:9px;">
                                <div style="flex:0.8; padding:2px; border-right:1px solid #000;">
                                    <input type="text" name="OC_MONTO_<?= $i ?>" value="<?= htmlspecialchars($monto) ?>" placeholder="US$ 0.00 o MN 0.00" style="width:100%; font-size:9px; padding:2px;">
                                </div>
                                <div style="flex:1.2; padding:2px; border-right:1px solid #000;">
                                    <input type="text" name="OC_NRO_OPERACION_<?= $i ?>" value="<?= htmlspecialchars($nroOp) ?>" placeholder="Nro. Operación" style="width:100%; font-size:9px; padding:2px;">
                                </div>
                                <div style="flex:1.5; padding:2px; border-right:1px solid #000;">
                                    <select name="OC_ENTIDAD_FINANCIERA_<?= $i ?>" style="width:100%; font-size:8px; padding:2px;">
                                        <option value="">-- Seleccione Banco --</option>
                                        <?php foreach ($bancos as $banco): ?>
                                            <option value="<?= htmlspecialchars($banco) ?>" <?= (trim($entidad) === trim($banco)) ? 'selected' : '' ?>><?= htmlspecialchars($banco) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="flex:1.5; padding:2px;">
                                    <?php if (!empty($ordenCompraData['OC_ARCHIVO_ABONO' . $i])): ?>
                                        <small style="color: green; font-size: 7px;">✓ Archivo guardado</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endfor; 
                        ?>
                    <?php endif; ?>
                </div>
                
                <button type="button" id="btnAgregarAbono" onclick="agregarAbono()" class="no-print" style="margin-top:8px; margin-left:5px; padding:4px 10px; background:#27769c; color:white; border:none; border-radius:3px; font-size:10px; cursor:pointer;">+ Agregar Abono</button>

                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; height:15px;"></div>


                <!-- Banco -->
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; height:20px;"></div>
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:100px; height:20px;"></div>


                <div style="display:flex; ">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">Banco: <span style="color: red; font-size: 12px;">*</span></div>
                    <?php $bancoGuardado = trim($ordenCompraData['OC_BANCO_ABONO'] ?? ''); ?>
                    <select name="OC_BANCO_ABONO" style="flex:1;border-bottom:1px solid #000;" required>
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($bancos as $banco): ?>
                            <?php $bancoVal = trim($banco); ?>
                            <option value="<?php echo htmlspecialchars($bancoVal); ?>" <?php echo ($bancoVal === $bancoGuardado) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bancoVal); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sectorista -->
                <div style="display:flex;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">Sectorista:</div>
                    <input type="text" name="OC_SECTORISTA_BANCO" style="flex:1;border-bottom:1px solid #000;" autocomplete="off">
                </div>

                <!-- Oficina -->
                <div style="display:flex;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:70px;">Oficina:</div>
                    <input type="text" name="OC_OFICINA_BANCO" style="flex:1;border-bottom:1px solid #000;" autocomplete="off">
                </div>

                <!-- Teléf. Sector -->
                <div style="display:flex;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:90px;">Teléf. Sector:</div>
                    <input type="text" name="OC_TELEFONO_SECTORISTA" style="flex:1;border-bottom:1px solid #000;" autocomplete="off" maxlength="9" pattern="[0-9]{9}" title="Ingrese 9 dígitos">
                </div>

                <!-- Archivos adicionales -->
                <div style="display:flex; align-items:center; margin-top:10px;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">DNI</div>
                    <div style="flex:1;">
                        <?php if (isset($ordenCompraData['OC_ARCHIVO_DNI']) && !empty($ordenCompraData['OC_ARCHIVO_DNI'])): ?>
                            <input type="text" value="✓ Archivo cargado" readonly style="width:100%; color: green; font-weight: bold; cursor: default; border: 1px solid #10b981; background: #f0fdf4; padding: 4px;">
                            <input type="hidden" name="OC_ARCHIVO_DNI_EXISTENTE" value="<?php echo htmlspecialchars($ordenCompraData['OC_ARCHIVO_DNI']); ?>">
                        <?php else: ?>
                            <input type="file" name="OC_ARCHIVO_DNI" accept=".pdf,.jpg,.png,.jpeg" style="width:100%;">
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- CONFIRMACIÓN SANTANDER -->
                <div style="display:flex; align-items:center; margin-top:4px;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">CONFIRMACIÓN SANTANDER</div>
                    <div style="flex:1;">
                        <?php if (isset($ordenCompraData['OC_CONFIRMACION_SANTANDER']) && !empty($ordenCompraData['OC_CONFIRMACION_SANTANDER'])): ?>
                            <input type="text" value="✓ Archivo cargado" readonly style="width:100%; color: green; font-weight: bold; cursor: default; border: 1px solid #10b981; background: #f0fdf4; padding: 4px;">
                            <input type="hidden" name="OC_CONFIRMACION_SANTANDER_EXISTENTE" value="<?php echo htmlspecialchars($ordenCompraData['OC_CONFIRMACION_SANTANDER']); ?>">
                        <?php else: ?>
                            <input type="file" id="input_confirmacion_santander" name="OC_CONFIRMACION_SANTANDER_FILE" accept="image/*,.pdf" style="width:100%;">
                        <?php endif; ?>
                    </div>
                </div>
                
                <div style="display:flex; align-items:center; margin-top:4px;">
                    <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">PEDIDO SALESFORCE</div>
                    <div style="flex:1;">
                        <?php if (isset($ordenCompraData['OC_ARCHIVO_PEDIDO_SALESFORCE']) && !empty($ordenCompraData['OC_ARCHIVO_PEDIDO_SALESFORCE'])): ?>
                            <input type="text" value="✓ Archivo cargado" readonly style="width:100%; color: green; font-weight: bold; cursor: default; border: 1px solid #10b981; background: #f0fdf4; padding: 4px;">
                            <input type="hidden" name="OC_ARCHIVO_PEDIDO_SALESFORCE_EXISTENTE" value="<?php echo htmlspecialchars($ordenCompraData['OC_ARCHIVO_PEDIDO_SALESFORCE']); ?>">
                        <?php else: ?>
                            <input type="file" name="OC_ARCHIVO_PEDIDO_SALESFORCE" accept=".pdf,.jpg,.png,.jpeg" style="width:100%;">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Otros documentos -->
                <div id="otros-container">
                    <?php
                    // Cargar archivos "otros" guardados
                    $otrosArchivos = [];
                    foreach ($ordenCompraData as $key => $value) {
                        if (strpos($key, 'OC_ARCHIVO_OTROS_') === 0 && !empty($value)) {
                            $numero = str_replace('OC_ARCHIVO_OTROS_', '', $key);
                            $otrosArchivos[$numero] = $value;
                        }
                    }
                    foreach ($otrosArchivos as $numero => $archivo):
                    ?>
                    <div style="display:flex; align-items:center; margin-top:4px;">
                        <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Otro documento <?php echo $numero; ?></div>
                        <input type="text" value="✓ Archivo cargado" readonly style="flex:1; color: green; font-weight: bold; cursor: default; border: 1px solid #10b981; background: #f0fdf4; padding: 4px;">
                        <input type="hidden" name="OC_ARCHIVO_OTROS_<?php echo $numero; ?>_EXISTENTE" value="<?php echo htmlspecialchars($archivo); ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="agregarOtro()" class="no-print" style="margin-top:10px; margin-left:5px; margin-bottom:5px; padding:3px 8px; background:#27769c; color:white; border:none; border-radius:3px; font-size:10px;">Agregar Otro Documento</button>
            </div>
        </div>
    </div>

    <!-- OBSEQUIOS / CORTESÍAS / CAMPAÑAS -->
 <div style="border:1px solid #000; width:774px; margin:10px auto 0;">
    <div style="background:#ffffff; font-weight:bold; padding:4px; border-bottom:1px solid #000; text-align:center;">
        Obsequios / Cortesías / Campañas
    </div>
    <textarea name="OC_OBSEQUIOS_CORTESIAS" style="width:100%; height:60px; border:none; background:#fdeee2;">tarjeta y placa</textarea>
</div>

<!-- FIRMAS -->
<div class="firma-container" style="display:flex; justify-content:space-between; width:774px; margin:15px auto 5px;">
    <!-- Asesor de venta -->
    <div class="firma-container" style="flex:1; border:1px solid #000; margin-right:5px; display:flex; flex-direction:column; justify-content:space-between; height:70px; min-width:0;">
        <div id="asesor-firma-preview">
            <?php 
            // Mostrar firma del asesor logueado automáticamente
            $firmaAsesor = '';
            if (!empty($ordenCompraData['OC_ASESOR_FIRMA'])) {
                // Si ya existe firma guardada, mostrarla
                $firmaAsesor = $ordenCompraData['OC_ASESOR_FIRMA'];
            } elseif (!empty($_SESSION['usuario_firma'])) {
                // Si no hay firma guardada pero el usuario está logueado, usar su firma
                $firmaAsesor = $_SESSION['usuario_firma'];
            }
            
            if (!empty($firmaAsesor)): 
            ?>
                <img src="<?= htmlspecialchars($firmaAsesor) ?>" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">
            <?php endif; ?>
        </div>
        <div style="background:#ccc; text-align:center; font-weight:bold; padding:2px; font-size:10px;">ASESOR DE VENTA</div>
    </div>

    <!-- Firma cliente -->
    <div class="firma-container" style="flex:2.8; border:1px solid #000; margin-right:5px; display:flex; flex-direction:column; justify-content:space-between; height:70px; min-width:0;">
        <div id="firma-cliente-preview" style="flex:1; position:relative;">
            <?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?>
                <img src="<?= htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']) ?>" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">
                <img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">
            <?php endif; ?>
        </div>
        <div style="background:#ccc; text-align:center; font-weight:bold; padding:2px; font-size:10px; cursor:pointer;" onclick="abrirCapturadorFirma()">FIRMA CLIENTE</div>
    </div>
</div>

<div class="firma-container" style="display:flex; justify-content:space-between; width:774px; margin:0 auto 10px;">
    <!-- Jefe de tienda -->
    <div class="firma-container" style="flex:1; border:1px solid #000; margin-right:5px; display:flex; flex-direction:column; justify-content:space-between; height:70px; min-width:0;">
        <div><?php if (!empty($ordenCompraData['OC_JEFE_FIRMA'])): ?><img src="<?= htmlspecialchars($ordenCompraData['OC_JEFE_FIRMA']) ?>" style="max-width:100%; max-height:50px; display:block; margin:0 auto;"><?php endif; ?></div>
        <div style="background:#ccc; text-align:center; font-weight:bold; padding:2px; font-size:10px; cursor:pointer;" onclick="mostrarLogin(this, 'jefe')">JEFE DE TIENDA</div>
    </div>

    <!-- Visto ADV (Firma de Cajera) -->
    <div class="firma-container" style="flex:1; border:1px solid #000; display:flex; flex-direction:column; justify-content:space-between; height:70px; min-width:0;">
        <div id="cajera-firma-preview">
            <?php if (!empty($ordenCompraData['OC_VISTO_ADV'])): ?>
                <img src="<?= htmlspecialchars($ordenCompraData['OC_VISTO_ADV']) ?>" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">
            <?php endif; ?>
        </div>
        <div style="background:#ccc; text-align:center; font-weight:bold; padding:2px; font-size:10px; <?php if (isset($modoImpresion) && $modoImpresion): ?>cursor:default;<?php else: ?>cursor:pointer;" onclick="mostrarLogin(this, 'cajera')<?php endif; ?>">VISTO ADV°</div>
    </div>
</div>

<!-- SECCIÓN DE DOCUMENTOS RELACIONADOS -->
<div style="width:774px; margin:10px auto; padding:10px; border:1px solid #000; background:#ffffff;">
    <h3 style="margin:0 0 8px 0; color:#000; font-size:11px; font-weight:bold; text-align:center;">
        DOCUMENTOS RELACIONADOS - Marque los que desea generar:
    </h3>
    
    <?php
    $ordenId = $ordenCompraData['OC_ID'] ?? null;
    
    // Lista completa de documentos (sin filtrar por condiciones aquí)
    $todosDocumentos = [
        ['id' => 'carta_conocimiento_aceptacion', 'nombre' => 'Carta Conocimiento y Aceptación', 'siempre' => true],
        ['id' => 'carta_recepcion', 'nombre' => 'Carta de Recepción', 'siempre' => true],
        ['id' => 'carta_felicitaciones', 'nombre' => 'Carta de Felicitaciones', 'siempre' => true],
        ['id' => 'carta-caracteristicas', 'nombre' => 'Carta de Características', 'siempre' => false, 'requiere' => 'credito_normal'],
        ['id' => 'carta_caracteristicas_banbif', 'nombre' => 'Carta Características Banbif', 'siempre' => false, 'requiere' => 'credito_banbif'],
        ['id' => 'politica_proteccion_datos', 'nombre' => 'Política Protección de Datos', 'siempre' => true],
        ['id' => 'acta-conocimiento-conformidad', 'nombre' => 'Acta Conocimiento y Conformidad GLP', 'siempre' => false, 'requiere' => 'glp'],
        ['id' => 'actorizacion-datos-personales', 'nombre' => 'Autorización Uso de Imagen', 'siempre' => true]
    ];
    ?>
    
    <div id="lista-documentos" style="display:grid; grid-template-columns:1fr 1fr; gap:5px;">
        <?php foreach ($todosDocumentos as $doc): ?>
            <?php
            // Verificar si el documento ya existe en BD
            $existe = false;
            if ($ordenId && isset($documentModel)) {
                try {
                    $documentData = $documentModel->getDocumentData($doc['id'], $ordenId);
                    $existe = !empty($documentData);
                } catch (Exception $e) {
                    error_log("Error al verificar documento {$doc['id']}: " . $e->getMessage());
                }
            }
            $checked = $existe ? 'checked' : '';
            // Si el documento aún no existe, el check se habilitará solo después de ver la vista previa
            $disabled = $existe ? '' : 'disabled';
            
            // Determinar si debe mostrarse inicialmente
            $displayValue = $doc['siempre'] ? 'flex' : 'none';
            ?>
            <div class="doc-item" 
                 data-doc-id="<?= htmlspecialchars($doc['id']) ?>"
                 data-requiere="<?= htmlspecialchars($doc['requiere'] ?? 'ninguno') ?>"
                 style="display:<?= $displayValue ?>; align-items:center; gap:5px; padding:3px; border:1px solid #ccc;">
                <input type="checkbox" 
                       name="generar_documento[]" 
                       value="<?= htmlspecialchars($doc['id']) ?>" 
                       id="doc_<?= htmlspecialchars($doc['id']) ?>"
                       <?= $checked ?>
                       <?= $disabled ?>
                       style="width:14px; height:14px; cursor:pointer;">
                <label for="doc_<?= htmlspecialchars($doc['id']) ?>" style="flex:1; cursor:pointer; font-size:9px; margin-left:5px;">
                    <?= htmlspecialchars($doc['nombre']) ?>
                    <?php if ($existe): ?>
                        <span style="color:#10b981; font-size:8px;">(Generado)</span>
                    <?php endif; ?>
                </label>
                <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
                <button type="button" 
                        onclick="verDocumentoPreview('<?= htmlspecialchars($doc['id']) ?>')"
                        style="padding:2px 6px; background:#3b82f6; color:white; border:none; font-size:8px; cursor:pointer; min-width:28px; height:16px; margin-right:5px; display:flex; align-items:center; justify-content:center;"
                        class="no-print">
                    Ver
                </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Habilitar/Deshabilitar Cuota Inicial según Forma de Pago
function toggleCuotaInicial() {
    const formaPago = document.getElementById('formaPago').value;
    const cuotaInicial = document.getElementById('cuotaInicial');
    const monedaCuotaInicial = document.getElementById('monedaCuotaInicial');
    
    if (formaPago === 'CRÉDITO') {
        cuotaInicial.disabled = false;
        monedaCuotaInicial.disabled = false;
        cuotaInicial.style.background = 'white';
    } else {
        cuotaInicial.disabled = true;
        monedaCuotaInicial.disabled = true;
        cuotaInicial.value = '';
        cuotaInicial.style.background = '#f3f4f6';
    }
}

// Variables globales para validación
let validacionPendiente = false;
let callbackDespuesValidacion = null;

// Validar pagos antes de guardar
function validarPagos(callback) {
    const formaPago = document.getElementById('formaPago').value;
    const precioVenta = parseFloat(document.getElementById('precioVenta').value) || 0;
    const pagoCuenta = parseFloat(document.getElementById('pagoCuenta').value) || 0;
    const cuotaInicial = parseFloat(document.getElementById('cuotaInicial').value) || 0;
    
    console.log('🔍 DEBUG VALIDACIÓN:');
    console.log('Forma Pago:', formaPago);
    console.log('Precio Venta:', precioVenta);
    console.log('Pago a Cuenta:', pagoCuenta);
    console.log('Cuota Inicial:', cuotaInicial);
    
    let mensajeError = '';
    
    // Validación 1: CRÉDITO - Cuota Inicial >= Pago a Cuenta
    if (formaPago === 'CRÉDITO' && cuotaInicial > 0 && cuotaInicial < pagoCuenta) {
        mensajeError = `⚠️ La Cuota Inicial (${cuotaInicial.toFixed(2)}) debe ser mayor o igual al Pago a Cuenta (${pagoCuenta.toFixed(2)})`;
        console.log('❌ ERROR VALIDACIÓN 1:', mensajeError);
    }
    
    // Validación 2: CONTADO - Pago a Cuenta >= 80% Precio Venta
    if (formaPago === 'CONTADO' && precioVenta > 0) {
        const minimoRequerido = precioVenta * 0.80;
        console.log('Mínimo requerido (80%):', minimoRequerido);
        if (pagoCuenta < minimoRequerido) {
            mensajeError = `⚠️ El Pago a Cuenta (${pagoCuenta.toFixed(2)}) debe ser mayor o igual al 80% del Precio de Venta (${minimoRequerido.toFixed(2)})`;
            console.log('❌ ERROR VALIDACIÓN 2:', mensajeError);
        }
    }
    
    // Si hay error, mostrar modal de comentario
    if (mensajeError) {
        console.log('🚨 Abriendo modal de comentario...');
        document.getElementById('mensajeValidacion').textContent = mensajeError;
        document.getElementById('comentarioValidacion').value = '';
        document.getElementById('modalComentario').style.display = 'block';
        validacionPendiente = true;
        callbackDespuesValidacion = callback;
        return false;
    }
    
    console.log('✅ Validación OK, continuando...');
    // Si no hay error, continuar
    return true;
}

// Cerrar modal de comentario
function cerrarModalComentario() {
    document.getElementById('modalComentario').style.display = 'none';
    validacionPendiente = false;
    callbackDespuesValidacion = null;
}

// Confirmar comentario y continuar
function confirmarComentario() {
    const comentario = document.getElementById('comentarioValidacion').value.trim();
    
    if (!comentario) {
        alert('⚠️ Debe ingresar un comentario para continuar');
        return;
    }
    
    console.log('💬 Comentario ingresado:', comentario);
    
    // Guardar comentario en un campo oculto o agregarlo al formulario
    let inputComentario = document.querySelector('input[name="OC_COMENTARIO_VALIDACION"]');
    if (!inputComentario) {
        inputComentario = document.createElement('input');
        inputComentario.type = 'hidden';
        inputComentario.name = 'OC_COMENTARIO_VALIDACION';
        document.querySelector('form').appendChild(inputComentario);
    }
    inputComentario.value = comentario;
    
    // Marcar que ya se validó con comentario
    validacionPendiente = true; // ✅ Importante: marcar como validado
    
    // Cerrar modal
    document.getElementById('modalComentario').style.display = 'none';
    
    console.log('✅ Ejecutando callback para enviar formulario...');
    
    // Ejecutar callback si existe (enviar formulario)
    if (callbackDespuesValidacion) {
        callbackDespuesValidacion();
    }
}

// Actualizar documentos visibles según forma de pago y banco
function actualizarDocumentosDisponibles() {
    const formaPagoRaw = document.querySelector('select[name="OC_FORMA_PAGO"]')?.value || '';
    const bancoAbono = document.querySelector('select[name="OC_BANCO_ABONO"]')?.value || '';

    const formaPago = formaPagoRaw.toString().trim().toUpperCase();
    console.log('📄 Actualizando documentos - Forma Pago:', formaPago, '| Banco:', bancoAbono);
    
    // Determinar si es CRÉDITO de forma estricta
    const esCredito = (formaPago === 'CRÉDITO' || formaPago === 'CREDITO');
    const esBanbif = bancoAbono.includes('Interamericano') || bancoAbono.includes('Banbif');
    
    // Obtener todos los items de documentos
    const docItems = document.querySelectorAll('.doc-item');
    
    docItems.forEach(item => {
        const requiere = item.getAttribute('data-requiere');
        
        if (requiere === 'ninguno') {
            // Documentos que siempre se muestran
            item.style.display = 'flex';
        } else if (requiere === 'credito_normal') {
            // Carta de Características (no Banbif)
            item.style.display = (esCredito && !esBanbif) ? 'flex' : 'none';
        } else if (requiere === 'credito_banbif') {
            // Carta de Características Banbif
            item.style.display = (esCredito && esBanbif) ? 'flex' : 'none';
        } else if (requiere === 'glp') {
            // Acta de Conocimiento - se maneja por separado con tipo combustible
            // No hacer nada aquí, se controla en actualizarVisibilidadActaGLP()
        }
    });
}

// Función para mostrar/ocultar Acta según tipo de combustible
function actualizarVisibilidadActaGLP(tipoCombustible) {
    const actaItem = document.querySelector('[data-doc-id="acta-conocimiento-conformidad"]');
    
    if (!actaItem) {
        console.warn('⚠️ No se encontró el elemento del Acta');
        return;
    }
    
    const esGLP = tipoCombustible && tipoCombustible.trim().toUpperCase() === 'DU';
    
    if (esGLP) {
        console.log('✅ Vehículo es GLP - Mostrando Acta de Conocimiento');
        actaItem.style.display = 'flex';
    } else {
        console.log('❌ Vehículo NO es GLP - Ocultando Acta de Conocimiento');
        actaItem.style.display = 'none';
        // Desmarcar checkbox si estaba marcado
        const checkbox = actaItem.querySelector('input[type="checkbox"]');
        if (checkbox) {
            checkbox.checked = false;
        }
    }
}

// Maquillar visualmente precios para vista de cliente cuando OC_FAKE_PRECIO está activo
function maquillarPreciosFkFake() {
    try {
        // Solo aplicar en vista cliente y cuando el flag esté activo
        const esVistaCliente = <?php echo isset($esVistaCliente) && $esVistaCliente ? 'true' : 'false'; ?>;
        const fkFakeActivo = <?php echo isset($ordenCompraData['OC_FAKE_PRECIO']) && $ordenCompraData['OC_FAKE_PRECIO'] ? 'true' : 'false'; ?>;

        console.log('🧪 fk fake - esVistaCliente:', esVistaCliente, 'OC_FAKE_PRECIO:', fkFakeActivo ? 1 : 0);

        if (!esVistaCliente || !fkFakeActivo) {
            console.log('🧪 fk fake - no aplica (no es vista cliente o flag apagado)');
            return;
        }

        const precioVentaInput = document.querySelector('[name="OC_PRECIO_VENTA"]');
        const totalEquipInput = document.querySelector('[name="OC_TOTAL_EQUIPAMIENTO"]');
        const precioTotalInput = document.querySelector('[name="OC_PRECIO_TOTAL_COMPRA"]');
        const pagoCuentaInput = document.querySelector('[name="OC_PAGO_CUENTA"]');
        const saldoInput = document.querySelector('[name="OC_SALDO_PENDIENTE"]');
        const equipInputs = [
            document.querySelector('[name="OC_EQUIPAMIENTO_ADICIONAL_1"]'),
            document.querySelector('[name="OC_EQUIPAMIENTO_ADICIONAL_2"]'),
            document.querySelector('[name="OC_EQUIPAMIENTO_ADICIONAL_3"]'),
            document.querySelector('[name="OC_EQUIPAMIENTO_ADICIONAL_4"]'),
            document.querySelector('[name="OC_EQUIPAMIENTO_ADICIONAL_5"]')
        ];

        if (!precioVentaInput || !totalEquipInput || !precioTotalInput || !pagoCuentaInput || !saldoInput) {
            console.log('🧪 fk fake - no se encontraron uno o más inputs esperados');
            return;
        }

        // Tomar los valores reales directamente desde PHP (BD)
        const precioVentaReal = <?php echo isset($ordenCompraData['OC_PRECIO_VENTA']) ? (float)$ordenCompraData['OC_PRECIO_VENTA'] : 0; ?>;
        const totalEquipReal = <?php echo isset($ordenCompraData['OC_TOTAL_EQUIPAMIENTO']) ? (float)$ordenCompraData['OC_TOTAL_EQUIPAMIENTO'] : 0; ?>;
        const pagoCuenta = <?php echo isset($ordenCompraData['OC_PAGO_CUENTA']) ? (float)$ordenCompraData['OC_PAGO_CUENTA'] : 0; ?>;

        console.log('🧪 fk fake - valores reales (PHP) -> precioVenta:', precioVentaReal, 'totalEquip:', totalEquipReal, 'pagoCuenta:', pagoCuenta);

        const nuevoPrecioVenta = precioVentaReal + totalEquipReal;
        const nuevoPrecioTotal = nuevoPrecioVenta;
        const nuevoSaldo = nuevoPrecioTotal - pagoCuenta;

        console.log('🧪 fk fake - nuevos valores -> precioVenta:', nuevoPrecioVenta, 'precioTotal:', nuevoPrecioTotal, 'saldo:', nuevoSaldo);

        // Aplicar solo a nivel visual (DOM), sin enviar nada al servidor
        const format = (n) => n.toFixed(2);

        precioVentaInput.value = format(nuevoPrecioVenta);
        totalEquipInput.value = format(0);
        precioTotalInput.value = format(nuevoPrecioTotal);
        saldoInput.value = format(nuevoSaldo);
        // Poner en 0 cada línea de equipamiento adicional solo de forma visual
        equipInputs.forEach(function(inp) {
            if (inp) {
                inp.value = format(0);
            }
        });
    } catch (e) {
        console.error('Error al maquillar precios fk fake:', e);
    }
}

// Reaplicar el maquillaje al final de toda la carga (por si otros scripts rellenan valores después)
window.addEventListener('load', function() {
    try {
        maquillarPreciosFkFake();
    } catch (e) {
        console.error('Error al ejecutar fk fake en window.load:', e);
    }
});

// Función para ver documento en preview con datos actuales del formulario
function verDocumentoPreview(documentoId) {
    console.log('👁️ Abriendo preview de:', documentoId);
    
    // Validaciones específicas para cartas de características
    try {
        const formaPagoActual = document.querySelector('[name="OC_FORMA_PAGO"]')?.value || '';
        const bancoAbonoActual = document.querySelector('[name="OC_BANCO_ABONO"]')?.value || '';

        // Solo permitir cartas de características cuando la forma de pago es CRÉDITO
        if (documentoId === 'carta-caracteristicas' || documentoId === 'carta_caracteristicas_banbif') {
            if (formaPagoActual !== 'CRÉDITO') {
                if (typeof mostrarToast === 'function') {
                    mostrarToast('⚠️ Las cartas de características solo están disponibles para compras a CRÉDITO.', 'warning');
                } else {
                    alert('Las cartas de características solo están disponibles para compras a CRÉDITO.');
                }
                return;
            }

            // Validar banco para cada tipo de carta (mismas reglas que en DocumentController::show)
            if (documentoId === 'carta_caracteristicas_banbif' && bancoAbonoActual !== 'Banco Interamericano de Finanzas') {
                if (typeof mostrarToast === 'function') {
                    mostrarToast('⚠️ Esta carta solo está disponible para Banco Interamericano de Finanzas.', 'warning');
                } else {
                    alert('Esta carta solo está disponible para Banco Interamericano de Finanzas.');
                }
                return;
            }

            if (documentoId === 'carta-caracteristicas' && bancoAbonoActual === 'Banco Interamericano de Finanzas') {
                if (typeof mostrarToast === 'function') {
                    mostrarToast('⚠️ Para Banco Interamericano de Finanzas debe usar la Carta Características Banbif.', 'warning');
                } else {
                    alert('Para Banco Interamericano de Finanzas debe usar la Carta Características Banbif.');
                }
                return;
            }
        }
    } catch (e) {
        console.error('Error en validación previa de cartas de características:', e);
    }

    // Capturar TODOS los datos del formulario actual
    const datosOrden = {
        // Datos del comprador
        comprador_nombre: document.querySelector('[name="OC_COMPRADOR_NOMBRE"]')?.value || '',
        comprador_apellido: document.querySelector('[name="OC_COMPRADOR_APELLIDO"]')?.value || '',
        comprador_tipo_doc: document.querySelector('[name="OC_COMPRADOR_TIPO_DOCUMENTO"]')?.value || '',
        comprador_numero_doc: document.querySelector('[name="OC_COMPRADOR_NUMERO_DOCUMENTO"]')?.value || '',
        
        // Datos del cliente (si es diferente)
        cliente_nombre: document.querySelector('[name="OC_CLIENTE_NOMBRE"]')?.value || '',
        cliente_dni: document.querySelector('[name="OC_CLIENTE_DNI"]')?.value || '',
        cliente_direccion: document.querySelector('[name="OC_CLIENTE_DIRECCION"]')?.value || '',
        cliente_telefono: document.querySelector('[name="OC_CLIENTE_TELEFONO"]')?.value || '',
        cliente_email: document.querySelector('[name="OC_CLIENTE_EMAIL"]')?.value || '',
        
        // Datos del vehículo
        vehiculo_marca: document.querySelector('[name="OC_VEHICULO_MARCA"]')?.value || '',
        vehiculo_modelo: document.querySelector('[name="OC_VEHICULO_MODELO"]')?.value || '',
        vehiculo_version: document.querySelector('[name="OC_VEHICULO_VERSION"]')?.value || '',
        vehiculo_chasis: document.querySelector('[name="OC_VEHICULO_CHASIS"]')?.value || '',
        vehiculo_motor: document.querySelector('[name="OC_VEHICULO_MOTOR"]')?.value || '',
        vehiculo_color: document.querySelector('[name="OC_VEHICULO_COLOR"]')?.value || '',
        vehiculo_anio: document.querySelector('[name="OC_VEHICULO_ANIO_MODELO"]')?.value || '',
        vehiculo_clase: document.querySelector('[name="OC_VEHICULO_CLASE"]')?.value || '',
        
        // Datos de venta
        forma_pago: document.querySelector('[name="OC_FORMA_PAGO"]')?.value || '',
        banco_abono: document.querySelector('[name="OC_BANCO_ABONO"]')?.value || '',
        precio_venta: document.querySelector('[name="OC_PRECIO_VENTA"]')?.value || '',
        moneda_precio_venta: document.querySelector('[name="OC_MONEDA_PRECIO_VENTA"]')?.value || '',
        cuota_inicial: document.querySelector('[name="OC_CUOTA_INICIAL"]')?.value || '',
        moneda_cuota_inicial: document.querySelector('[name="OC_MONEDA_CUOTA_INICIAL"]')?.value || '',
        
        // Datos del asesor
        asesor_nombre: document.querySelector('[name="OC_ASESOR_VENTA"]')?.value || '',
        asesor_celular: document.querySelector('[name="OC_ASESOR_CELULAR"]')?.value || '',
        
        // Firma del cliente (si existe)
        firma_cliente: document.getElementById('cliente_firma_hidden')?.value || '',
        
        // Fecha actual
        fecha_orden: new Date().toISOString().split('T')[0]
    };
    
    console.log('📦 Datos capturados:', datosOrden);
    
    // Guardar en localStorage para que el documento lo use
    localStorage.setItem('preview_orden_compra', JSON.stringify(datosOrden));
    console.log('💾 Datos guardados en localStorage');
    
    // Abrir documento en modal en lugar de nueva ventana
    // Enviamos forma de pago y banco por GET para que el backend pueda validar
    const params = new URLSearchParams({
        id: documentoId,
        preview: '1',
        forma_pago: datosOrden.forma_pago || '',
        banco_abono: datosOrden.banco_abono || ''
    });
    const url = '/digitalizacion-documentos/documents/show?' + params.toString();
    abrirModalPreview(url);
    console.log('✅ Modal de preview abierto');

    // Marcar que el documento ha sido visto: habilitar su checkbox en la lista
    try {
        const checkbox = document.getElementById('doc_' + documentoId);
        if (checkbox) {
            checkbox.disabled = false;
        }
    } catch (e) {
        console.error('Error al habilitar checkbox de documento visto:', e);
    }
}

// Función para abrir modal de preview
function abrirModalPreview(url) {
    // Crear modal si no existe
    let modal = document.getElementById('modalPreview');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'modalPreview';
        modal.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; display:flex; align-items:center; justify-content:center;';
        
        const contenido = document.createElement('div');
        contenido.style.cssText = 'width:90%; height:90%; background:white; border-radius:8px; overflow:hidden; display:flex; flex-direction:column;';
        
        const header = document.createElement('div');
        header.style.cssText = 'padding:15px; background:#1e3a8a; color:white; display:flex; justify-content:space-between; align-items:center;';
        header.innerHTML = '<h3 style="margin:0;">Vista Previa del Documento</h3><button onclick="cerrarModalPreview()" style="background:white; color:#1e3a8a; border:none; padding:8px 15px; border-radius:5px; cursor:pointer; font-weight:bold;">✕ Cerrar</button>';
        
        const iframe = document.createElement('iframe');
        iframe.id = 'iframePreview';
        iframe.style.cssText = 'flex:1; border:none; width:100%;';
        
        contenido.appendChild(header);
        contenido.appendChild(iframe);
        modal.appendChild(contenido);
        document.body.appendChild(modal);
        
        // Cerrar modal al hacer clic fuera del contenido
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                cerrarModalPreview();
            }
        });
        
        // Prevenir que el clic en el contenido cierre el modal
        contenido.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Ocultar botones de Regresar y Guardar cuando el iframe cargue
        iframe.addEventListener('load', function() {
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                // Crear estilo para ocultar botones
                const style = iframeDoc.createElement('style');
                style.textContent = `
                    .btn-regresar, 
                    .btn-guardar,
                    button[onclick*="history.back"],
                    button[onclick*="guardar"],
                    button[onclick*="window.history"],
                    a[href*="back"],
                    a[onclick*="history.back"],
                    form button[type="submit"],
                    input[type="submit"],
                    button:contains("Regresar"),
                    button:contains("Guardar"),
                    a:contains("Regresar"),
                    .action-buttons,
                    .form-actions {
                        display: none !important;
                    }
                `;
                iframeDoc.head.appendChild(style);
                
                // También ocultar botones por texto
                const buttons = iframeDoc.querySelectorAll('button, a, input[type="submit"]');
                buttons.forEach(btn => {
                    const text = btn.textContent.toLowerCase();
                    if (text.includes('regresar') || text.includes('guardar') || text.includes('volver')) {
                        btn.style.display = 'none';
                    }
                });
                
                console.log('✅ Botones de navegación ocultados en el preview');
            } catch (e) {
                console.log('⚠️ No se pudieron ocultar botones (restricción CORS)');
            }
        });
    }
    
    // Mostrar modal y cargar URL
    modal.style.display = 'flex';
    document.getElementById('iframePreview').src = url;
}

// Función para cerrar modal de preview
function cerrarModalPreview() {
    const modal = document.getElementById('modalPreview');
    if (modal) {
        modal.style.display = 'none';
        document.getElementById('iframePreview').src = '';
    }
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarDocumentosDisponibles();
    
    // Verificar tipo de combustible al cargar (para edición)
    const tipoCombustibleInput = document.querySelector('[name="OC_VEHICULO_TIPO_COMBUSTIBLE"]');
    if (tipoCombustibleInput && tipoCombustibleInput.value) {
        actualizarVisibilidadActaGLP(tipoCombustibleInput.value);
    }
    
    // Escuchar cambios en forma de pago y banco
    const formaPagoSelect = document.querySelector('select[name="OC_FORMA_PAGO"]');
    const bancoSelect = document.querySelector('select[name="OC_BANCO_ABONO"]');
    
    if (formaPagoSelect) {
        formaPagoSelect.addEventListener('change', actualizarDocumentosDisponibles);
    }
    
    if (bancoSelect) {
        bancoSelect.addEventListener('change', actualizarDocumentosDisponibles);
    }
    
    console.log('✅ Listeners de documentos activados');
        
    // 🆕 PRELLENAR AGENCIA, CAJERA Y CENTRO DE COSTO
    // Solo cuando es una orden NUEVA interna (sin modo edición ni modo impresión)
    <?php if ((!isset($modoEdicion) || !$modoEdicion) && (!isset($modoImpresion) || !$modoImpresion)): ?>
    prellenarDatosAsesor();
    <?php endif; ?>
    
    // Inicializar cálculos
    manejarBonoFinanciamiento();
    calcularTotalEquipamiento();
    
    // Maquillar precios fk fake DESPUÉS de todos los recálculos iniciales (solo vista cliente)
    if (typeof maquillarPreciosFkFake === 'function') {
        maquillarPreciosFkFake();
    }
    
    // Event listeners para tipo de cliente (radio buttons)
    const tipoClienteRadios = document.querySelectorAll('input[name="OC_TIPO_CLIENTE"]');
    tipoClienteRadios.forEach(radio => {
        radio.addEventListener('change', manejarCamposTipoCliente);
    });
    console.log('✅ Listeners de tipo de cliente activados');
});

// Función para prellenar datos del asesor
function prellenarDatosAsesor() {
    console.log('🔄 Prellenando datos del asesor...');
    
    fetch('/digitalizacion-documentos/documents/obtenerDatosAsesor')
        .then(response => response.json())
        .then(data => {
            console.log('📦 Datos recibidos del servidor:', data);
            console.log('   - Agencia:', data.agencia || '(vacío)');
            console.log('   - Cajera:', data.cajera || '(vacío)');
            console.log('   - Centro Costo:', data.centro_costo || '(vacío)');
            console.log('   - Email Cajera:', data.email_cajera || '(vacío)');
            
            if (data.success) {
                const agenciaEl = document.getElementById('agencia');
                const responsableEl = document.getElementById('nombre_responsable');
                const centroEl = document.getElementById('centro_costo');
                const emailHidden = document.getElementById('email_centro_costo');

                const agenciaVal = data.agencia || '';
                const cajeraVal = data.cajera || '';
                const centroVal = data.centro_costo || '';

                // Caso 1: campos como INPUT (modo edición / impresión)
                if (agenciaEl && agenciaEl.tagName === 'INPUT') {
                    agenciaEl.value = agenciaVal;
                }
                if (responsableEl && responsableEl.tagName === 'INPUT') {
                    responsableEl.value = cajeraVal;
                }
                if (centroEl && centroEl.tagName === 'INPUT') {
                    centroEl.value = centroVal;
                }

                // Caso 2: campos como SELECT (nueva orden interna)
                if (agenciaEl && agenciaEl.tagName === 'SELECT') {
                    console.log('⏳ Esperando opciones de AGENCIA para seleccionar', agenciaVal);
                    esperarOpcionesSelect(agenciaEl, function() {
                        agenciaEl.value = agenciaVal;
                        agenciaEl.dispatchEvent(new Event('change'));
                        console.log('✅ Agencia seleccionada automáticamente:', agenciaEl.value);

                        if (responsableEl && responsableEl.tagName === 'SELECT') {
                            console.log('⏳ Esperando opciones de RESPONSABLE para seleccionar', cajeraVal);
                            esperarOpcionesSelect(responsableEl, function() {
                                responsableEl.value = cajeraVal;
                                responsableEl.dispatchEvent(new Event('change'));
                                console.log('✅ Responsable seleccionado automáticamente:', responsableEl.value);

                                if (centroEl && centroEl.tagName === 'SELECT') {
                                    console.log('⏳ Esperando opciones de CENTRO DE COSTO para seleccionar', centroVal);
                                    esperarOpcionesSelect(centroEl, function() {
                                        centroEl.value = centroVal;
                                        centroEl.dispatchEvent(new Event('change'));
                                        console.log('✅ Centro de costo seleccionado automáticamente:', centroEl.value);
                                    });
                                }
                            });
                        }
                    });
                }

                if (emailHidden && data.email_cajera) {
                    emailHidden.value = data.email_cajera;
                    console.log('   ✅ Email de cajera guardado:', data.email_cajera);
                }
                
                console.log('✅ Prellenado automático disparado correctamente');
            } else {
                console.error('❌ Error al obtener datos:', data.error);
            }
        })
        .catch(error => {
            console.error('❌ Error en la petición:', error);
        });
}
</script>

<!-- IMPORTANTE -->
<div style="width:774px; margin:0 auto; padding:8px; font-size:10px; line-height:1.4; background:#ffffff;">
    <p style="font-weight:bold;">IMPORTANTE:</p>
    <ol>
        <li>Esta solicitud está sujeta a la aprobación de INTERAMERICANA NORTE SAC.</li>
        <li>Cualquier pedido de equipamiento adicional a las características de la presente solicitud será por cuenta y costo del cliente.</li>
        <li>Luego de la entrega de los pagos a cuenta cualquier devolución estará afecta al 7% o $100 como mínimo de gastos administrativos. El monto abonado por el cliente será entregado en los quince (15) días útiles después de presentada la Solicitud de Devolución y en cheque no negociable a nombre del titular de la reserva.</li>
        <li>El trámite de placas de rodaje y tarjeta de propiedad es una cortesía que otorgamos a nuestros clientes. Dicho trámite se encuentra sujeto a los criterios de calificación autónomos de cada registrador, por lo que nuestra empresa no se hace responsable por las demoras ocasionadas como consecuencia de la aplicación de criterios registrales empleados por SUNARP.</li>
        <li>El solicitante acepta formalmente todas las características del vehículo descritos en el presente documento.</li>
        <li>El Cliente declara conocer que en caso el vehículo no se encuentre en stock, libera a la empresa de cualquier responsabilidad relacionada con los plazos de entrega. Las fechas de entrega son variables y están sujetas a cambio, con previa comunicación al cliente.</li>
        <li>Manifiesto que los datos consignados son exactos y se ajustan fielmente a la realidad.</li>
        <li>El tipo de cambio es referencial, cualquier variación que ocurra al momento de la cancelación del vehículo será asumido por el cliente.</li>
        <li>El cliente ha sido informado que podrían presentarse ciertas características audibles y/o perceptibles propias del funcionamiento, accionamiento o desempeño de los componentes y/o elementos del vehículo (Motor, sistema de frenos, transmisión, aire acondicionado, suspensión, eléctrico, refrigeración entre otros) y en mayor medida en determinadas condiciones climáticas o de exigencia en la conducción.</li>
        <li>El cliente declara que se le ha informado desde la fase de oferta y exhibición de los modelos Tiggo 7 Pro y Tiggo 8, que CHERY importa y comercializa algunos vehículos que vienen con lunas oscurecidas/polarizadas de fábrica y que conforme el D.S. N° 004-2019-IN y D.S. N° 058-2003-MTC, EL CLIENTE deberá tramitar bajo su costo y cargo el permiso de lunas polarizadas ante la autoridad competente en caso fuese aplicable.</li>
        <li>Toda pago y obligaciones tributarias dependen directamente del adquiriente (Impuesto Vehicular).</li>
    </ol>
</div>

<!-- Botones de acción -->
<?php if (!isset($modoImpresion) || !$modoImpresion): ?>
<div class="no-print" style="width:774px; margin:20px auto; text-align:center;">
    <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 30px; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.3s ease;">
        💾 <?php echo (isset($modoEdicion) && $modoEdicion) ? 'ACTUALIZAR ORDEN DE COMPRA' : 'GUARDAR ORDEN DE COMPRA'; ?>
    </button>
</div>
<?php endif; ?>

<?php if (isset($modoImpresion) && $modoImpresion && !$esVistaCliente): ?>
<div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
  <a href="/digitalizacion-documentos/documents/show?id=orden-compra&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
     style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4); font-family: Arial, sans-serif; font-size: 14px; font-weight: 600; transition: all 0.3s ease;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    ✏️ EDITAR
  </a>
</div>
<?php endif; ?>

<script>
<?php if (isset($modoImpresion) && $modoImpresion): ?>
// Suprimir errores de consola en modo impresión
window.addEventListener('error', function(e) { e.preventDefault(); return true; }, true);
console.error = function() {};
console.warn = function() {};

document.addEventListener('DOMContentLoaded', function() {
  const inputs = document.querySelectorAll('input:not([type="hidden"]), select, textarea');
  inputs.forEach(el => { 
    el.setAttribute('readonly', 'readonly'); 
    el.style.cursor = 'default'; 
    el.style.pointerEvents = 'none';
    el.style.color = '#000000'; // Forzar color negro
    el.style.backgroundColor = '#ffffff'; // Fondo blanco
    el.style.opacity = '1'; // Opacidad completa
    el.style.webkitTextFillColor = '#000000'; // Para Safari/Chrome
  });
});
<?php endif; ?>
</script>


        </div>
    <script>
        // Función para calcular el tipo de cambio en soles
        function calcularTipoCambio() {
            const precioTotal = parseFloat(document.getElementsByName('OC_PRECIO_TOTAL_COMPRA')[0].value) || 0;
            const tipoCambio = parseFloat(document.getElementsByName('OC_TIPO_CAMBIO')[0].value) || 0;
            const moneda = document.getElementsByName('OC_MONEDA_PRECIO_TOTAL')[0].value;
            let resultado;
            if (moneda === 'US$') {
                resultado = precioTotal * tipoCambio;
            } else {
                resultado = precioTotal;
            }
            document.getElementsByName('OC_TIPO_CAMBIO_SOL')[0].value = resultado.toFixed(2);
        }

        // Función de autocompletar fecha de nacimiento eliminada (campo duplicado removido)

        // Función para aplicar estilo deshabilitado a un elemento
        function aplicarEstiloDeshabilitado(elemento, deshabilitar) {
            if (!elemento) return;
            
            elemento.disabled = deshabilitar;
            if (deshabilitar) {
                elemento.style.backgroundColor = '#d1d5db'; // Color plomo/gris
                elemento.style.color = '#6b7280'; // Texto gris oscuro
                elemento.style.cursor = 'not-allowed';
            } else {
                elemento.style.backgroundColor = '';
                elemento.style.color = '';
                elemento.style.cursor = '';
            }
        }

        // Función para calcular el saldo automáticamente
        function calcularSaldo() {
            // IMPORTANTE: Usar OC_PRECIO_TOTAL_COMPRA (no OC_PRECIO_VENTA)
            const precioTotalCompraInput = document.getElementsByName('OC_PRECIO_TOTAL_COMPRA')[0];
            const bonoFinanInput = document.getElementsByName('OC_BONO_FINANCIAMIENTO')[0];
            const bonoCampanaInput = document.getElementsByName('OC_BONO_CAMPANA')[0];
            const pagoCuentaInput = document.getElementsByName('OC_PAGO_CUENTA')[0];
            const saldoInput = document.getElementsByName('OC_SALDO_PENDIENTE')[0];
            
            if (!precioTotalCompraInput || !saldoInput) {
                console.warn('No se encontraron los campos necesarios para calcular saldo');
                return;
            }
            
            // Obtener valores actuales
            const precioTotalCompra = parseFloat(precioTotalCompraInput.value) || 0;
            const bonoFinan = parseFloat(bonoFinanInput?.value) || 0;
            const bonoCampana = parseFloat(bonoCampanaInput?.value) || 0;
            const pagoCuenta = parseFloat(pagoCuentaInput?.value) || 0;
            
            let saldo = 0;
            
            console.log('═══════════════════════════════════════');
            console.log('📊 CALCULANDO SALDO:');
            console.log('Precio Total Compra:', precioTotalCompra);
            console.log('Bono Financiamiento:', bonoFinan);
            console.log('Bono Campaña:', bonoCampana);
            console.log('Pago a Cuenta:', pagoCuenta);
            
            // Determinar qué bono usar
            // Si hay bono de financiamiento, se resta
            // Si hay bono de campaña, se suma
            if (bonoFinan > 0) {
                // SALDO = PRECIO TOTAL COMPRA - BONO FINANCIAMIENTO - PAGO A CUENTA
                saldo = precioTotalCompra - bonoFinan - pagoCuenta;
                console.log(`✅ Fórmula: ${precioTotalCompra} - ${bonoFinan} - ${pagoCuenta} = ${saldo}`);
            } else if (bonoCampana > 0) {
                // SALDO = PRECIO TOTAL COMPRA + BONO CAMPAÑA - PAGO A CUENTA
                saldo = precioTotalCompra + bonoCampana - pagoCuenta;
                console.log(`✅ Fórmula: ${precioTotalCompra} + ${bonoCampana} - ${pagoCuenta} = ${saldo}`);
            } else {
                // Sin bonos: SALDO = PRECIO TOTAL COMPRA - PAGO A CUENTA
                saldo = precioTotalCompra - pagoCuenta;
                console.log(`✅ Fórmula: ${precioTotalCompra} - ${pagoCuenta} = ${saldo}`);
            }
            
            // Asegurar que el saldo no sea negativo
            saldo = Math.max(0, saldo);
            
            console.log('💰 SALDO FINAL:', saldo.toFixed(2));
            console.log('═══════════════════════════════════════');
            
            saldoInput.value = saldo.toFixed(2);
        }

        // Función para manejar bloqueo de bono de financiamiento y campos bancarios
        function manejarBonoFinanciamiento() {
            const formaPagoElement = document.getElementsByName('OC_FORMA_PAGO')[0];
            if (!formaPagoElement) return;
            
            const formaPago = formaPagoElement.value;
            
            // Campos de bonos
            const bonoFinanMoneda = document.getElementsByName('OC_MONEDA_BONO_FINANCIAMIENTO')[0];
            const bonoFinanInput = document.getElementsByName('OC_BONO_FINANCIAMIENTO')[0];
            const bonoCampanaMoneda = document.getElementsByName('OC_MONEDA_BONO_CAMPANA')[0];
            const bonoCampanaInput = document.getElementsByName('OC_BONO_CAMPANA')[0];
            
            // Campos bancarios
            const entidadFinancieraSelect = document.getElementsByName('OC_ENTIDAD_FINANCIERA')[0];
            const bancoAbonoSelect = document.getElementsByName('OC_BANCO_ABONO')[0];
            const sectoristaInput = document.getElementsByName('OC_SECTORISTA_BANCO')[0];
            const oficinaInput = document.getElementsByName('OC_OFICINA_BANCO')[0];
            const telefonoSectorInput = document.getElementsByName('OC_TELEFONO_SECTORISTA')[0];
            const monedaSaldoSelect = document.getElementsByName('OC_MONEDA_SALDO')[0];

            // Verificar que los elementos principales existen
            if (!bonoFinanMoneda || !bonoFinanInput || !bonoCampanaMoneda || !bonoCampanaInput) {
                return;
            }

            // Campos de la tabla de abonos (7 abonos)
            const abonosInputs = [];
            for (let i = 1; i <= 7; i++) {
                abonosInputs.push({
                    monto: document.getElementsByName('OC_MONTO_' + i)[0],
                    operacion: document.getElementsByName('OC_NRO_OPERACION_' + i)[0],
                    entidad: document.getElementsByName('OC_ENTIDAD_FINANCIERA_' + i)[0],
                    archivo: document.getElementsByName('OC_ARCHIVO_ABONO' + i)[0]
                });
            }

            // Botón Agregar Abono
            const btnAgregarAbono = document.getElementById('btnAgregarAbono');
            
            // Campo de confirmación Santander
            const confirmacionSantander = document.getElementById('input_confirmacion_santander');

            if (formaPago === 'CONTADO') {
                console.log('Forma de pago: CONTADO');
                
                // DESHABILITAR bonos de financiamiento y campaña
                aplicarEstiloDeshabilitado(bonoFinanMoneda, true);
                aplicarEstiloDeshabilitado(bonoFinanInput, true);
                bonoFinanInput.value = '';
                aplicarEstiloDeshabilitado(bonoCampanaMoneda, true);
                aplicarEstiloDeshabilitado(bonoCampanaInput, true);
                bonoCampanaInput.value = '';
                
                // DESHABILITAR confirmación Santander cuando es CONTADO
                if (confirmacionSantander) {
                    aplicarEstiloDeshabilitado(confirmacionSantander, true);
                    console.log('✅ Confirmación Santander deshabilitada (CONTADO)');
                }
                
                // Habilitar entidad financiera
                if (entidadFinancieraSelect) aplicarEstiloDeshabilitado(entidadFinancieraSelect, false);
                
                // Deshabilitar campos de banco
                if (bancoAbonoSelect) {
                    aplicarEstiloDeshabilitado(bancoAbonoSelect, true);
                    bancoAbonoSelect.value = '';
                }
                if (sectoristaInput) {
                    aplicarEstiloDeshabilitado(sectoristaInput, true);
                    sectoristaInput.value = '';
                }
                if (oficinaInput) {
                    aplicarEstiloDeshabilitado(oficinaInput, true);
                    oficinaInput.value = '';
                }
                if (telefonoSectorInput) {
                    aplicarEstiloDeshabilitado(telefonoSectorInput, true);
                    telefonoSectorInput.value = '';
                }
                if (monedaSaldoSelect) aplicarEstiloDeshabilitado(monedaSaldoSelect, false);
                
                // HABILITAR tabla de abonos
                abonosInputs.forEach(abono => {
                    if (abono.monto) aplicarEstiloDeshabilitado(abono.monto, false);
                    if (abono.operacion) aplicarEstiloDeshabilitado(abono.operacion, false);
                    if (abono.entidad) aplicarEstiloDeshabilitado(abono.entidad, false);
                    if (abono.archivo) aplicarEstiloDeshabilitado(abono.archivo, false);
                });
                
                // HABILITAR botón Agregar Abono
                if (btnAgregarAbono) {
                    btnAgregarAbono.disabled = false;
                    btnAgregarAbono.style.opacity = '1';
                    btnAgregarAbono.style.cursor = 'pointer';
                }
                
            } else if (formaPago === 'CRÉDITO') {
                console.log('Forma de pago: CRÉDITO');
                
                // HABILITAR bonos de financiamiento y campaña
                aplicarEstiloDeshabilitado(bonoFinanMoneda, false);
                aplicarEstiloDeshabilitado(bonoFinanInput, false);
                aplicarEstiloDeshabilitado(bonoCampanaMoneda, false);
                aplicarEstiloDeshabilitado(bonoCampanaInput, false);
                
                // HABILITAR confirmación Santander cuando es CRÉDITO
                if (confirmacionSantander) {
                    aplicarEstiloDeshabilitado(confirmacionSantander, false);
                    console.log('✅ Confirmación Santander habilitada (CRÉDITO)');
                }
                
                // Deshabilitar entidad financiera
                if (entidadFinancieraSelect) {
                    aplicarEstiloDeshabilitado(entidadFinancieraSelect, true);
                    entidadFinancieraSelect.value = '';
                }
                
                // Habilitar campos de banco
                if (bancoAbonoSelect) aplicarEstiloDeshabilitado(bancoAbonoSelect, false);
                if (sectoristaInput) aplicarEstiloDeshabilitado(sectoristaInput, false);
                if (oficinaInput) aplicarEstiloDeshabilitado(oficinaInput, false);
                if (telefonoSectorInput) aplicarEstiloDeshabilitado(telefonoSectorInput, false);
                if (monedaSaldoSelect) aplicarEstiloDeshabilitado(monedaSaldoSelect, false);
                
                // HABILITAR tabla de abonos
                abonosInputs.forEach(abono => {
                    if (abono.monto) aplicarEstiloDeshabilitado(abono.monto, false);
                    if (abono.operacion) aplicarEstiloDeshabilitado(abono.operacion, false);
                    if (abono.entidad) aplicarEstiloDeshabilitado(abono.entidad, false);
                    if (abono.archivo) aplicarEstiloDeshabilitado(abono.archivo, false);
                });
                
                // HABILITAR botón Agregar Abono
                if (btnAgregarAbono) {
                    btnAgregarAbono.disabled = false;
                    btnAgregarAbono.style.opacity = '1';
                    btnAgregarAbono.style.cursor = 'pointer';
                }
                
            } else {
                // Sin selección o vacío: habilitar todo
                aplicarEstiloDeshabilitado(bonoFinanMoneda, false);
                aplicarEstiloDeshabilitado(bonoFinanInput, false);
                aplicarEstiloDeshabilitado(bonoCampanaMoneda, false);
                aplicarEstiloDeshabilitado(bonoCampanaInput, false);
                if (entidadFinancieraSelect) aplicarEstiloDeshabilitado(entidadFinancieraSelect, false);
                if (bancoAbonoSelect) aplicarEstiloDeshabilitado(bancoAbonoSelect, false);
                if (sectoristaInput) aplicarEstiloDeshabilitado(sectoristaInput, false);
                if (oficinaInput) aplicarEstiloDeshabilitado(oficinaInput, false);
                if (telefonoSectorInput) aplicarEstiloDeshabilitado(telefonoSectorInput, false);
                if (monedaSaldoSelect) aplicarEstiloDeshabilitado(monedaSaldoSelect, false);
                
                // Habilitar tabla de abonos
                abonosInputs.forEach(abono => {
                    if (abono.monto) aplicarEstiloDeshabilitado(abono.monto, false);
                    if (abono.operacion) aplicarEstiloDeshabilitado(abono.operacion, false);
                    if (abono.entidad) aplicarEstiloDeshabilitado(abono.entidad, false);
                    if (abono.archivo) aplicarEstiloDeshabilitado(abono.archivo, false);
                });
                
                // Habilitar botón Agregar Abono
                if (btnAgregarAbono) {
                    btnAgregarAbono.disabled = false;
                    btnAgregarAbono.style.opacity = '1';
                    btnAgregarAbono.style.cursor = 'pointer';
                }
            }
            
            // Calcular saldo después de cambiar forma de pago
            calcularSaldo();
        }

        // Función para calcular total de equipamiento
        function calcularTotalEquipamiento() {
            let total = 0;
            for (let i = 1; i <= 5; i++) {
                const valor = parseFloat(document.getElementsByName('OC_EQUIPAMIENTO_ADICIONAL_' + i)[0].value) || 0;
                total += valor;
            }
            document.getElementsByName('OC_TOTAL_EQUIPAMIENTO')[0].value = total.toFixed(2);
            calcularPrecioTotalCompra();
        }

        // Función para mostrar toast de notificación
        function mostrarToast(mensaje, tipo = 'info') {
            // Crear el elemento toast
            const toast = document.createElement('div');
            toast.className = 'toast-notification toast-' + tipo;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${tipo === 'warning' ? '#f59e0b' : tipo === 'error' ? '#ef4444' : '#3b82f6'};
                color: white;
                padding: 16px 24px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                z-index: 10000;
                font-size: 14px;
                font-weight: 500;
                max-width: 400px;
                animation: slideIn 0.3s ease-out;
            `;
            toast.textContent = mensaje;
            
            // Agregar al body
            document.body.appendChild(toast);
            
            // Remover después de 4 segundos
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 4000);
        }

        // Función para calcular precio total de compra
        function calcularPrecioTotalCompra() {
            // Si es vista cliente y el flag fk fake está activo, aplicar las reglas de maquillaje
            const esVistaCliente = <?php echo isset($esVistaCliente) && $esVistaCliente ? 'true' : 'false'; ?>;
            const fkFakeActivo = <?php echo isset($ordenCompraData['OC_FAKE_PRECIO']) && $ordenCompraData['OC_FAKE_PRECIO'] ? 'true' : 'false'; ?>;

            const precioVentaInput = document.getElementsByName('OC_PRECIO_VENTA')[0];
            const totalEquipInput  = document.getElementsByName('OC_TOTAL_EQUIPAMIENTO')[0];
            const precioTotalInput = document.getElementsByName('OC_PRECIO_TOTAL_COMPRA')[0];
            const pagoCuentaInput  = document.getElementsByName('OC_PAGO_CUENTA')[0];
            const saldoInput       = document.getElementsByName('OC_SALDO_PENDIENTE')[0];

            if (esVistaCliente && fkFakeActivo && precioVentaInput && totalEquipInput && precioTotalInput && pagoCuentaInput && saldoInput) {
                const precioVentaReal = <?php echo isset($ordenCompraData['OC_PRECIO_VENTA']) ? (float)$ordenCompraData['OC_PRECIO_VENTA'] : 0; ?>;
                const totalEquipReal  = <?php echo isset($ordenCompraData['OC_TOTAL_EQUIPAMIENTO']) ? (float)$ordenCompraData['OC_TOTAL_EQUIPAMIENTO'] : 0; ?>;
                const pagoCuentaReal  = <?php echo isset($ordenCompraData['OC_PAGO_CUENTA']) ? (float)$ordenCompraData['OC_PAGO_CUENTA'] : 0; ?>;

                const nuevoPrecioVenta = precioVentaReal + totalEquipReal;
                const nuevoPrecioTotal = nuevoPrecioVenta;
                const nuevoSaldo       = nuevoPrecioTotal - pagoCuentaReal;

                const format = (n) => n.toFixed(2);

                precioVentaInput.value = format(nuevoPrecioVenta);
                totalEquipInput.value  = format(0);
                precioTotalInput.value = format(nuevoPrecioTotal);
                saldoInput.value       = format(nuevoSaldo);

                // Mantener cálculo de tipo de cambio con el nuevo total
                calcularTipoCambio();
                return;
            }

            const precioVenta = parseFloat(precioVentaInput ? precioVentaInput.value : 0) || 0;
            const totalEquipamiento = parseFloat(totalEquipInput ? totalEquipInput.value : 0) || 0;
            const precioTotal = precioVenta + totalEquipamiento;
            if (precioTotalInput) {
                precioTotalInput.value = precioTotal.toFixed(2);
            }
            calcularTipoCambio();
            // Recalcular saldo cuando cambia el precio total
            calcularSaldo();
        }

        // Función para autocompletar datos de mantenimiento por marca y modelo
        function autocompletarDatosMantenimiento() {
            <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
            const marcaInput = document.getElementsByName('OC_VEHICULO_MARCA')[0];
            const modeloInput = document.getElementsByName('OC_VEHICULO_MODELO')[0];
            
            const buscarDatos = function() {
                const marca = marcaInput.value.trim();
                const modelo = modeloInput.value.trim();
                
                if (marca && modelo) {
                    fetch('/digitalizacion-documentos/documents/buscar-datos-mantenimiento?marca=' + encodeURIComponent(marca) + '&modelo=' + encodeURIComponent(modelo))
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.GARANTIA) {
                                document.getElementsByName('OC_PERIODO_GARANTIA')[0].value = data.GARANTIA || '';
                                document.getElementsByName('OC_PERIODICIDAD_MANTENIMIENTO')[0].value = data.PERIODICIDAD || '';
                                document.getElementsByName('OC_PRIMER_MANTENIMIENTO')[0].value = data.PRIMER_INGRESO || '';
                            }
                        })
                        .catch(error => console.error('Error al buscar datos de mantenimiento:', error));
                }
            };
            
            if (marcaInput && modeloInput) {
                marcaInput.addEventListener('blur', buscarDatos);
                modeloInput.addEventListener('blur', buscarDatos);
            }
            <?php endif; ?>
        }

        // Variable global para controlar si el chasis es válido
        let chasisValido = false;

        // Función para validar formulario antes de guardar
        function validarFormularioAntesDeSalvar() {
            const chasisInput = document.getElementsByName('OC_VEHICULO_CHASIS')[0];
            const chasis = chasisInput ? chasisInput.value.trim() : '';
            
            // Si hay un chasis ingresado pero no es válido, bloquear el guardado
            if (chasis && !chasisValido) {
                mostrarToast('❌ El chasis ingresado no está asignado a usted. Por favor, ingrese un chasis válido.', 'error');
                chasisInput.focus();
                return false; // Bloquear el envío del formulario
            }
            
            // VALIDACIÓN DE CAMPOS OBLIGATORIOS CUANDO ESTÉN HABILITADOS
            // Validar TIPO DE CLIENTE (siempre obligatorio)
            const tipoClienteSeleccionado = document.querySelector('input[name="OC_TIPO_CLIENTE"]:checked');
            if (!tipoClienteSeleccionado) {
                mostrarToast('❌ El campo "Tipo de Cliente" es obligatorio.', 'error');
                // Enfocar en el primer radio button
                const primerRadio = document.querySelector('input[name="OC_TIPO_CLIENTE"]');
                if (primerRadio) primerRadio.focus();
                return false;
            }
            
            // Validar campos de DATOS DEL CLIENTE
            const camposCliente = [
                { campo: 'OC_COMPRADOR_TIPO_DOCUMENTO', nombre: 'Tipo de Documento del Comprador' },
                { campo: 'OC_TIPO_DOCUMENTO_VENTA', nombre: 'Tipo de Documento de Venta' },
                { campo: 'OC_FUENTE_CONTACTO', nombre: 'Fuente de Contacto' },
                { campo: 'OC_ESTADO_CIVIL', nombre: 'Estado Civil' },
                { campo: 'OC_SITUACION_LABORAL', nombre: 'Situación Laboral' },
                { campo: 'OC_CONYUGE_TIPO_DOCUMENTO', nombre: 'Tipo de Documento del Cónyuge' }
            ];
            
            for (const item of camposCliente) {
                const elemento = document.getElementsByName(item.campo)[0];
                if (elemento && !elemento.disabled && !elemento.value.trim()) {
                    mostrarToast(`❌ El campo "${item.nombre}" es obligatorio.`, 'error');
                    elemento.focus();
                    return false;
                }
            }
            
            // Validar FORMA DE PAGO (siempre obligatorio)
            const formaPago = document.getElementsByName('OC_FORMA_PAGO')[0];
            if (formaPago && !formaPago.value.trim()) {
                mostrarToast('❌ El campo "Forma de Pago" es obligatorio.', 'error');
                formaPago.focus();
                return false;
            }
            
            // Validar BANCO (obligatorio solo si NO es CONTADO)
            const bancoAbono = document.getElementsByName('OC_BANCO_ABONO')[0];
            const esContado = formaPago && formaPago.value.trim().toUpperCase() === 'CONTADO';
            
            if (!esContado && bancoAbono && !bancoAbono.value.trim()) {
                mostrarToast('❌ El campo "Banco" es obligatorio.', 'error');
                bancoAbono.focus();
                return false;
            }
            
            // Validar campos de ENTIDAD FINANCIERA dinámicos (obligatorios cuando existen)
            const entidadesFinancieras = document.querySelectorAll('[name^="OC_ENTIDAD_FINANCIERA_"]');
            for (const entidad of entidadesFinancieras) {
                if (entidad && !entidad.disabled && !entidad.value.trim()) {
                    mostrarToast('❌ Los campos de "Entidad Financiera" en los abonos son obligatorios.', 'error');
                    entidad.focus();
                    return false;
                }
            }
            
            // Validar EMAIL (siempre obligatorio y formato correcto)
            const emailCliente = document.getElementsByName('OC_EMAIL_CLIENTE')[0];
            if (emailCliente) {
                const email = emailCliente.value.trim();
                
                // Verificar que no esté vacío
                if (!email) {
                    mostrarToast('❌ El campo "Email" es obligatorio.', 'error');
                    emailCliente.focus();
                    return false;
                }
                
                // Validar formato de email con expresión regular
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    mostrarToast('❌ El formato del email no es válido. Ejemplo: usuario@dominio.com', 'error');
                    emailCliente.focus();
                    return false;
                }
            }
            
            // Validar TELÉFONO (siempre obligatorio)
            const telefonoCliente = document.getElementsByName('OC_TELEFONO_CLIENTE')[0];
            if (telefonoCliente && !telefonoCliente.value.trim()) {
                mostrarToast('❌ El campo "Teléfono" es obligatorio.', 'error');
                telefonoCliente.focus();
                return false;
            }
            
            // Validar NÚMERO DE DOCUMENTO DEL CÓNYUGE (si está habilitado y tiene valor)
            const tipoDocConyuge = document.getElementsByName('OC_CONYUGE_TIPO_DOCUMENTO')[0];
            const numeroDocConyuge = document.getElementById('conyuge_numero_doc');
            if (tipoDocConyuge && numeroDocConyuge && !numeroDocConyuge.disabled && numeroDocConyuge.value.trim()) {
                const tipo = tipoDocConyuge.value;
                const numero = numeroDocConyuge.value;
                
                if (tipo === 'dni' && numero.length !== 8) {
                    mostrarToast('❌ El DNI del cónyuge debe tener exactamente 8 dígitos.', 'error');
                    numeroDocConyuge.focus();
                    return false;
                }
                
                if ((tipo === 'pasaporte' || tipo === 'carnet') && numero.length > 12) {
                    mostrarToast('❌ El ' + (tipo === 'pasaporte' ? 'Pasaporte' : 'Carnet de Extranjería') + ' del cónyuge no puede tener más de 12 dígitos.', 'error');
                    numeroDocConyuge.focus();
                    return false;
                }
            }
            
            // Validar EQUIPAMIENTO ADICIONAL (descripción obligatoria si hay monto)
            for (let i = 1; i <= 5; i++) {
                const montoEquip = document.getElementsByName('OC_EQUIPAMIENTO_ADICIONAL_' + i)[0];
                const descripcionEquip = document.getElementsByName('OC_DESCRIPCION_EQUIPAMIENTO_' + i)[0];
                
                if (montoEquip && descripcionEquip) {
                    const monto = montoEquip.value.trim();
                    const descripcion = descripcionEquip.value.trim();
                    
                    // Si hay monto pero no hay descripción
                    if (monto && parseFloat(monto) > 0 && !descripcion) {
                        mostrarToast(`❌ Debe seleccionar una descripción para el Equipamiento Adicional ${i}.`, 'error');
                        descripcionEquip.focus();
                        return false;
                    }
                }
            }
            
            // Validar pagos (Cuota Inicial y Pago a Cuenta)
            if (!validacionPendiente) {
                const validacionOk = validarPagos(function() {
                    // Callback: Enviar formulario después de validación
                    document.getElementById('ordenCompraForm').submit();
                });
                
                if (!validacionOk) {
                    return false; // Bloquear hasta que se ingrese comentario
                }
            }
            
            // Si no hay chasis o el chasis es válido, permitir guardar
            return true;
        }

        // Función para autocompletar vehículo por chasis
        function autocompletarVehiculo() {
            <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
            const chasisInput = document.getElementsByName('OC_VEHICULO_CHASIS')[0];
            if (chasisInput) {
                // Resetear validación cuando el usuario cambia el chasis
                chasisInput.addEventListener('input', function() {
                    if (!this.value.trim()) {
                        chasisValido = false; // Si borra el chasis, resetear validación
                    }
                });
                
                chasisInput.addEventListener('blur', function() {
                    const chasis = this.value.trim();
                    if (chasis) {
                        // Primero validar asignación del vehículo
                        fetch('/digitalizacion-documentos/documents/validar-asignacion-vehiculo?chasis=' + encodeURIComponent(chasis))
                            .then(response => response.json())
                            .then(validacion => {
                                if (!validacion.valido) {
                                    // El vehículo NO está asignado al usuario logueado
                                    const vendedorAsignado = validacion.vendedor_asignado || 'OTRO ASESOR';
                                    
                                    // Enviar correo al asesor asignado notificándole del intento
                                    fetch('/digitalizacion-documentos/documents/notificar-intento-uso-vehiculo', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            chasis: chasis,
                                            vendedor_asignado: vendedorAsignado
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(resultado => {
                                        console.log('📧 Notificación enviada:', resultado);
                                    })
                                    .catch(error => {
                                        console.error('❌ Error al enviar notificación:', error);
                                    });
                                    
                                    // Mostrar toast de advertencia (sin bloquear)
                                    mostrarToast(`⚠️ Este vehículo ha sido asignado a: ${vendedorAsignado}. Se ha enviado una notificación.`, 'warning');
                                    
                                    // Marcar como NO válido
                                    chasisValido = false;
                                    
                                    // Limpiar el campo de chasis
                                    chasisInput.value = '';
                                    chasisInput.focus();
                                    return; // Detener el proceso
                                }
                                
                                // Marcar como válido
                                chasisValido = true;
                                
                                // Validación pasada (el vehículo SÍ está asignado al usuario), continuar con búsqueda del vehículo
                                fetch('/digitalizacion-documentos/documents/buscar-vehiculo?chasis=' + encodeURIComponent(chasis))
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data && data.VE_CCHASIS) {
                                            document.getElementsByName('OC_VEHICULO_MODELO')[0].value = data.MODELO || '';
                                            document.getElementsByName('OC_VEHICULO_COLOR')[0].value = data.COLOR || '';
                                            document.getElementsByName('OC_VEHICULO_MARCA')[0].value = data.MARCA || '';
                                            document.getElementsByName('OC_VEHICULO_ANIO_MODELO')[0].value = data.ANIO_FABRICACION || '';
                                            document.getElementsByName('OC_VEHICULO_CLASE')[0].value = data.CLASE || '';
                                            document.getElementsByName('OC_VEHICULO_VERSION')[0].value = data.VERSION || '';
                                            document.getElementsByName('OC_VEHICULO_MOTOR')[0].value = data.MOTOR || '';
                                            document.getElementsByName('OC_VEHICULO_CODIGO_FSC')[0].value = data.FSC || '';
                                            // document.getElementsByName('OC_PRECIO_VENTA')[0].value = data.PRECIO || ''; // ❌ DESHABILITADO: Ya no se autocompleta
                                            document.getElementsByName('OC_VEHICULO_TIPO_COMBUSTIBLE')[0].value = data.TIPO_COMBUSTIBLE || '';
                                            
                                            // Actualizar visibilidad del Acta según tipo de combustible
                                            actualizarVisibilidadActaGLP(data.TIPO_COMBUSTIBLE);
                                            
                                            // Disparar cálculo del precio total de compra después de autocompletar el precio
                                            calcularPrecioTotalCompra();
                                            
                                            // Después de autocompletar por chasis, buscar datos de mantenimiento
                                            const marca = data.MARCA || '';
                                            const modelo = data.MODELO || '';
                                            console.log('🔍 Buscando datos de mantenimiento para:', { marca, modelo });
                                            
                                            if (marca && modelo) {
                                                const url = '/digitalizacion-documentos/documents/buscar-datos-mantenimiento?marca=' + encodeURIComponent(marca) + '&modelo=' + encodeURIComponent(modelo);
                                                console.log('📡 URL de búsqueda:', url);
                                                
                                                fetch(url)
                                                    .then(response => {
                                                        console.log('📥 Respuesta recibida:', response.status);
                                                        return response.json();
                                                    })
                                                    .then(datosMantenimiento => {
                                                        console.log('📦 Datos de mantenimiento:', datosMantenimiento);
                                                        
                                                        if (datosMantenimiento && datosMantenimiento.GARANTIA) {
                                                            console.log('✅ Autocompletando campos de mantenimiento');
                                                            document.getElementsByName('OC_PERIODO_GARANTIA')[0].value = datosMantenimiento.GARANTIA || '';
                                                            document.getElementsByName('OC_PERIODICIDAD_MANTENIMIENTO')[0].value = datosMantenimiento.PERIODICIDAD || '';
                                                            document.getElementsByName('OC_PRIMER_MANTENIMIENTO')[0].value = datosMantenimiento.PRIMER_INGRESO || '';
                                                        } else {
                                                            console.warn('⚠️ No se encontraron datos de mantenimiento para esta marca/modelo');
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('❌ Error al buscar datos de mantenimiento:', error);
                                                    });
                                            } else {
                                                console.warn('⚠️ Marca o modelo vacío, no se buscan datos de mantenimiento');
                                            }
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));
                            })
                            .catch(error => console.error('Error en validación:', error));
                    }
                });
            }
            <?php endif; ?>
        }

        // Función para actualizar el atributo required de campos según estén habilitados
        function actualizarCamposRequeridos() {
            // Campos de DATOS DEL CLIENTE que son obligatorios cuando están habilitados
            const camposCliente = [
                'OC_COMPRADOR_TIPO_DOCUMENTO',
                'OC_TIPO_DOCUMENTO_VENTA', 
                'OC_FUENTE_CONTACTO',
                'OC_ESTADO_CIVIL',
                'OC_SITUACION_LABORAL',
                'OC_CONYUGE_TIPO_DOCUMENTO'
            ];
            
            camposCliente.forEach(campo => {
                const elemento = document.getElementsByName(campo)[0];
                if (elemento) {
                    if (elemento.disabled) {
                        elemento.removeAttribute('required');
                    } else {
                        elemento.setAttribute('required', 'required');
                    }
                }
            });
            
            // Campos de ENTIDAD FINANCIERA dinámicos (siempre obligatorios cuando existen)
            const entidadesFinancieras = document.querySelectorAll('[name^="OC_ENTIDAD_FINANCIERA_"]');
            entidadesFinancieras.forEach(entidad => {
                if (entidad && !entidad.disabled) {
                    entidad.setAttribute('required', 'required');
                }
            });
        }

        // Función para manejar habilitación/deshabilitación según tipo de cliente
        function manejarCamposTipoCliente() {
            const tipoCliente = document.querySelector('input[name="OC_TIPO_CLIENTE"]:checked');
            if (!tipoCliente) {
                console.log('No hay tipo de cliente seleccionado');
                return;
            }
            
            const valor = tipoCliente.value;
            console.log('Tipo de cliente seleccionado:', valor);
            
            // Campos de propietario
            const propietarioNombre = document.getElementsByName('OC_PROPIETARIO_NOMBRE')[0];
            const propietarioRuc = document.getElementsByName('OC_PROPIETARIO_RUC')[0];
            
            // Campos de copropietario
            const copropietarioNombre = document.getElementsByName('OC_COPROPIETARIO_NOMBRE')[0];
            const copropietarioDni = document.getElementsByName('OC_COPROPIETARIO_DNI')[0];
            
            // Campos de representante legal
            const representanteLegal = document.getElementsByName('OC_REPRESENTANTE_LEGAL')[0];
            const representanteDni = document.getElementsByName('OC_REPRESENTANTE_DNI')[0];
            
            // Campos de información personal
            const fechaNacimiento = document.getElementsByName('OC_FECHA_NACIMIENTO')[0];
            const estadoCivil = document.getElementsByName('OC_ESTADO_CIVIL')[0];
            const situacionLaboral = document.getElementsByName('OC_SITUACION_LABORAL')[0];
            
            // Campos de cónyuge
            const conyugeNombre = document.getElementsByName('OC_CONYUGE_NOMBRE')[0];
            const conyugeTipoDoc = document.getElementsByName('OC_CONYUGE_TIPO_DOCUMENTO')[0];
            const conyugeNumDoc = document.getElementsByName('OC_CONYUGE_NUMERO_DOCUMENTO')[0];
            
            // Verificar que todos los elementos existen
            if (!propietarioNombre || !propietarioRuc || !copropietarioNombre || !copropietarioDni ||
                !representanteLegal || !representanteDni || !fechaNacimiento || !estadoCivil ||
                !situacionLaboral || !conyugeNombre || !conyugeTipoDoc || !conyugeNumDoc) {
                console.error('Algunos campos no se encontraron en el DOM');
                return;
            }
            
            // CASO 1: PERSONA NATURAL
            if (valor === 'natural') {
                console.log('Aplicando reglas para PERSONA NATURAL');
                // Deshabilitar propietario
                aplicarEstiloDeshabilitado(propietarioNombre, true);
                aplicarEstiloDeshabilitado(propietarioRuc, true);
                propietarioNombre.value = '';
                propietarioRuc.value = '';
                
                // Deshabilitar copropietario
                aplicarEstiloDeshabilitado(copropietarioNombre, true);
                aplicarEstiloDeshabilitado(copropietarioDni, true);
                copropietarioNombre.value = '';
                copropietarioDni.value = '';
                
                // Deshabilitar representante legal
                aplicarEstiloDeshabilitado(representanteLegal, true);
                aplicarEstiloDeshabilitado(representanteDni, true);
                representanteLegal.value = '';
                representanteDni.value = '';
                
                // Habilitar información personal
                aplicarEstiloDeshabilitado(fechaNacimiento, false);
                aplicarEstiloDeshabilitado(estadoCivil, false);
                aplicarEstiloDeshabilitado(situacionLaboral, false);
                
                // Habilitar cónyuge (depende del estado civil)
                aplicarEstiloDeshabilitado(conyugeNombre, false);
                aplicarEstiloDeshabilitado(conyugeTipoDoc, false);
                aplicarEstiloDeshabilitado(conyugeNumDoc, false);
            }
            // CASO 2: PERSONA NATURAL CON RUC
            else if (valor === 'ruc') {
                // Habilitar propietario
                aplicarEstiloDeshabilitado(propietarioNombre, false);
                aplicarEstiloDeshabilitado(propietarioRuc, false);
                
                // Deshabilitar copropietario
                aplicarEstiloDeshabilitado(copropietarioNombre, true);
                aplicarEstiloDeshabilitado(copropietarioDni, true);
                copropietarioNombre.value = '';
                copropietarioDni.value = '';
                
                // Deshabilitar representante legal
                aplicarEstiloDeshabilitado(representanteLegal, true);
                aplicarEstiloDeshabilitado(representanteDni, true);
                representanteLegal.value = '';
                representanteDni.value = '';
                
                // Habilitar información personal
                aplicarEstiloDeshabilitado(fechaNacimiento, false);
                aplicarEstiloDeshabilitado(estadoCivil, false);
                aplicarEstiloDeshabilitado(situacionLaboral, false);
            }
            // CASO 3: PERSONA JURÍDICA
            else if (valor === 'juridica') {
                // Habilitar propietario
                aplicarEstiloDeshabilitado(propietarioNombre, false);
                aplicarEstiloDeshabilitado(propietarioRuc, false);
                
                // Deshabilitar copropietario
                aplicarEstiloDeshabilitado(copropietarioNombre, true);
                aplicarEstiloDeshabilitado(copropietarioDni, true);
                copropietarioNombre.value = '';
                copropietarioDni.value = '';
                
                // Habilitar representante legal
                aplicarEstiloDeshabilitado(representanteLegal, false);
                aplicarEstiloDeshabilitado(representanteDni, false);
                
                // Deshabilitar información personal
                aplicarEstiloDeshabilitado(fechaNacimiento, true);
                aplicarEstiloDeshabilitado(estadoCivil, true);
                aplicarEstiloDeshabilitado(situacionLaboral, true);
                fechaNacimiento.value = '';
                estadoCivil.value = '';
                situacionLaboral.value = '';
                
                // Deshabilitar cónyuge
                aplicarEstiloDeshabilitado(conyugeNombre, true);
                aplicarEstiloDeshabilitado(conyugeTipoDoc, true);
                aplicarEstiloDeshabilitado(conyugeNumDoc, true);
                conyugeNombre.value = '';
                conyugeTipoDoc.value = '';
                conyugeNumDoc.value = '';
            }
            
            // Después de cambiar tipo de cliente, verificar estado civil
            // Siempre llamar a manejarCamposConyuge para que maneje la lógica correctamente
            manejarCamposConyuge();
            
            // Actualizar atributos required después de cambiar campos
            actualizarCamposRequeridos();
        }

        // Función para manejar bloqueo de campos de cónyuge
        function manejarCamposConyuge() {
            const tipoCliente = document.querySelector('input[name="OC_TIPO_CLIENTE"]:checked');
            const conyugeNombre = document.getElementsByName('OC_CONYUGE_NOMBRE')[0];
            const conyugeTipoDoc = document.getElementsByName('OC_CONYUGE_TIPO_DOCUMENTO')[0];
            const conyugeNumDoc = document.getElementsByName('OC_CONYUGE_NUMERO_DOCUMENTO')[0];
            
            // Verificar si es persona jurídica o persona natural con RUC, deshabilitar cónyuge
            if (tipoCliente && (tipoCliente.value === 'juridica' || tipoCliente.value === 'ruc')) {
                aplicarEstiloDeshabilitado(conyugeNombre, true);
                aplicarEstiloDeshabilitado(conyugeTipoDoc, true);
                aplicarEstiloDeshabilitado(conyugeNumDoc, true);
                conyugeNombre.value = '';
                conyugeTipoDoc.value = '';
                conyugeNumDoc.value = '';
                console.log('⚠️ Campos de cónyuge deshabilitados para:', tipoCliente.value === 'juridica' ? 'Persona Jurídica' : 'P. Natural con RUC');
                actualizarCamposRequeridos();
                return;
            }
            
            const estadoCivil = document.getElementsByName('OC_ESTADO_CIVIL')[0].value;

            // Si no hay estado civil seleccionado (-- Seleccione --), NO hacer nada
            if (!estadoCivil || estadoCivil === '') {
                console.log('⚠️ Estado civil vacío, no se modifican campos de cónyuge');
                return;
            }

            // Solo habilitar campos de cónyuge para estados civiles específicos
            const estadosConConyuge = ['casado', 'conviviente', 'concubino'];
            
            if (estadosConConyuge.includes(estadoCivil)) {
                // Habilitar cónyuge para CASADO, CONVIVIENTE y CONCUBINA(O)
                aplicarEstiloDeshabilitado(conyugeNombre, false);
                aplicarEstiloDeshabilitado(conyugeTipoDoc, false);
                aplicarEstiloDeshabilitado(conyugeNumDoc, false);
                console.log('✅ Campos de cónyuge habilitados para estado civil:', estadoCivil);
            } else {
                // Deshabilitar cónyuge para otros estados civiles (soltero, divorciado, etc.)
                aplicarEstiloDeshabilitado(conyugeNombre, true);
                aplicarEstiloDeshabilitado(conyugeTipoDoc, true);
                aplicarEstiloDeshabilitado(conyugeNumDoc, true);
                conyugeNombre.value = '';
                conyugeTipoDoc.value = '';
                conyugeNumDoc.value = '';
                console.log('⚠️ Campos de cónyuge deshabilitados para estado civil:', estadoCivil);
            }
            
            // Actualizar atributos required después de cambiar campos de cónyuge
            actualizarCamposRequeridos();
        }

        // Función para validar número de documento del cónyuge
        function validarNumeroDocumentoConyuge() {
            const tipoDocConyuge = document.getElementsByName('OC_CONYUGE_TIPO_DOCUMENTO')[0];
            const numeroDocConyuge = document.getElementById('conyuge_numero_doc');
            
            if (!tipoDocConyuge || !numeroDocConyuge) return;
            
            // Event listener para cuando cambia el tipo de documento
            tipoDocConyuge.addEventListener('change', function() {
                numeroDocConyuge.value = ''; // Limpiar el campo al cambiar tipo
                aplicarValidacionConyuge();
            });
            
            // Event listener para validar mientras escribe
            numeroDocConyuge.addEventListener('input', function(e) {
                // Solo permitir números
                this.value = this.value.replace(/[^0-9]/g, '');
                aplicarValidacionConyuge();
            });
            
            function aplicarValidacionConyuge() {
                const tipo = tipoDocConyuge.value;
                
                // Remover atributos anteriores
                numeroDocConyuge.removeAttribute('maxlength');
                numeroDocConyuge.removeAttribute('minlength');
                
                if (tipo === 'dni') {
                    numeroDocConyuge.setAttribute('maxlength', '8');
                    numeroDocConyuge.setAttribute('minlength', '8');
                    numeroDocConyuge.setAttribute('title', 'DNI debe tener 8 dígitos');
                } else if (tipo === 'pasaporte' || tipo === 'carnet') {
                    numeroDocConyuge.setAttribute('maxlength', '12');
                    const tipoNombre = tipo === 'pasaporte' ? 'Pasaporte' : 'Carnet de Extranjería';
                    numeroDocConyuge.setAttribute('title', tipoNombre + ' debe tener máximo 12 dígitos');
                }
            }
            
            // Aplicar validación inicial si ya hay un tipo seleccionado
            if (tipoDocConyuge.value) {
                aplicarValidacionConyuge();
            }
        }

        // Función para validar edad mínima de 18 años
        function validarEdadMinima(input) {
            if (!input.value) return;
            
            const fechaNacimiento = new Date(input.value);
            const hoy = new Date();
            
            // Calcular edad
            let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
            const mes = hoy.getMonth() - fechaNacimiento.getMonth();
            
            if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                edad--;
            }
            
            if (edad < 18) {
                alert('El cliente debe ser mayor de 18 años. Edad actual: ' + edad + ' años');
                input.value = '';
                input.focus();
                return false;
            }
            return true;
        }

        // Función para validar número de documento del comprador
        function validarNumeroDocumentoComprador() {
            const tipoDoc = document.getElementsByName('OC_COMPRADOR_TIPO_DOCUMENTO')[0].value;
            const numDocInput = document.getElementsByName('OC_COMPRADOR_NUMERO_DOCUMENTO')[0];
            let numDoc = numDocInput.value;
            // Allow only digits
            numDoc = numDoc.replace(/\D/g, '');
            let expectedLength = 0;
            if (tipoDoc === 'dni') {
                expectedLength = 8;
            } else if (tipoDoc === 'ruc') {
                expectedLength = 11;
            } else if (tipoDoc === 'carnet') {
                expectedLength = 12;
            }
            // Limit to expected length
            if (numDoc.length > expectedLength) {
                numDoc = numDoc.substring(0, expectedLength);
            }
            numDocInput.value = numDoc;
            // Set border color
            if (numDoc.length === expectedLength && expectedLength > 0) {
                numDocInput.style.borderColor = 'green';
            } else if (numDoc.length > 0) {
                numDocInput.style.borderColor = 'red';
            } else {
                numDocInput.style.borderColor = '';
            }
        }

        // Función para auto-completar "Tarjeta a Nombre de" cuando estado civil es CASADO
        function autoRellenarTarjetaCasado() {
            const estadoCivilSelect = document.getElementsByName('OC_ESTADO_CIVIL')[0];
            const tarjetaNombre = document.getElementById('tarjeta_nombre');
            
            function actualizarTarjetaPorEstadoCivil() {
                if (estadoCivilSelect && tarjetaNombre) {
                    const estadoCivil = estadoCivilSelect.value;
                    
                    if (estadoCivil === 'casado') {
                        // Obtener datos del cliente
                        const nombreCliente = document.getElementById('comprador_nombre')?.value.trim() || '';
                        const apellidoCliente = document.getElementById('comprador_apellido')?.value.trim() || '';
                        const nombreConyuge = document.getElementsByName('OC_CONYUGE_NOMBRE')[0]?.value.trim() || '';
                        
                        // Concatenar: (nombre + apellido) + " y " + nombre del cónyuge
                        let nombreCompleto = '';
                        if (nombreCliente || apellidoCliente) {
                            nombreCompleto = [nombreCliente, apellidoCliente].filter(n => n.length > 0).join(' ');
                        }
                        if (nombreConyuge) {
                            if (nombreCompleto) {
                                nombreCompleto += ' y ' + nombreConyuge;
                            } else {
                                nombreCompleto = nombreConyuge;
                            }
                        }
                        
                        tarjetaNombre.value = nombreCompleto;
                        console.log('✅ Auto-rellenado tarjeta para CASADO:', nombreCompleto);
                    }
                    // Para otros estados civiles, no modificamos la tarjeta (puede tener valor manual)
                }
            }
            
            // Escuchar cambios en estado civil
            if (estadoCivilSelect) {
                estadoCivilSelect.addEventListener('change', actualizarTarjetaPorEstadoCivil);
            }
            
            // También escuchar cambios en nombre, apellido y nombre del cónyuge (solo cuando estado civil es casado)
            const camposCliente = ['comprador_nombre', 'comprador_apellido'];
            camposCliente.forEach(id => {
                const elemento = document.getElementById(id);
                if (elemento) {
                    elemento.addEventListener('input', function() {
                        if (estadoCivilSelect && estadoCivilSelect.value === 'casado') {
                            actualizarTarjetaPorEstadoCivil();
                        }
                    });
                }
            });
            
            // Escuchar cambios en nombre del cónyuge
            const nombreConyuge = document.getElementsByName('OC_CONYUGE_NOMBRE')[0];
            if (nombreConyuge) {
                nombreConyuge.addEventListener('input', function() {
                    if (estadoCivilSelect && estadoCivilSelect.value === 'casado') {
                        actualizarTarjetaPorEstadoCivil();
                    }
                });
            }
        }

        // Función para auto-completar "Tarjeta a Nombre de" con el nombre y apellido del comprador
        function autoRellenarTarjetaNombre() {
            const compradorNombre = document.getElementById('comprador_nombre');
            const compradorApellido = document.getElementById('comprador_apellido');
            const tarjetaNombre = document.getElementById('tarjeta_nombre');
            
            function actualizarTarjeta() {
                if (tarjetaNombre) {
                    const nombre = compradorNombre ? compradorNombre.value.trim() : '';
                    const apellido = compradorApellido ? compradorApellido.value.trim() : '';
                    tarjetaNombre.value = (nombre + ' ' + apellido).trim();
                }
            }
            
            if (compradorNombre && compradorApellido && tarjetaNombre) {
                compradorNombre.addEventListener('input', actualizarTarjeta);
                compradorApellido.addEventListener('input', actualizarTarjeta);
                
                // También rellenar al cargar si ya hay valores
                actualizarTarjeta();
            }
        }

        // Función para auto-completar "Nombre / Razón Social" del propietario con el nombre y apellido del comprador
        function autoCompletarPropietarioNombre() {
            const compradorNombre = document.getElementById('comprador_nombre');
            const compradorApellido = document.getElementById('comprador_apellido');
            const propietarioNombre = document.getElementsByName('OC_PROPIETARIO_NOMBRE')[0];
            
            function actualizarPropietario() {
                if (propietarioNombre && !propietarioNombre.disabled) {
                    // Solo auto-completar si el campo NO está deshabilitado
                    const nombre = compradorNombre ? compradorNombre.value.trim() : '';
                    const apellido = compradorApellido ? compradorApellido.value.trim() : '';
                    propietarioNombre.value = (nombre + ' ' + apellido).trim();
                    console.log('✅ Auto-completado propietario nombre:', propietarioNombre.value);
                } else {
                    console.log('⚠️ Campo propietario nombre deshabilitado, no se auto-completa');
                }
            }
            
            if (compradorNombre && compradorApellido && propietarioNombre) {
                compradorNombre.addEventListener('input', actualizarPropietario);
                compradorApellido.addEventListener('input', actualizarPropietario);
                
                // También rellenar al cargar si ya hay valores Y el campo está habilitado
                if (!propietarioNombre.disabled) {
                    actualizarPropietario();
                }
            }
        }

        // Función para auto-completar RUC del propietario cuando es persona natural con RUC
        function autoCompletarPropietarioRUC() {
            const numeroDocumentoComprador = document.getElementsByName('OC_COMPRADOR_NUMERO_DOCUMENTO')[0];
            const propietarioRuc = document.getElementsByName('OC_PROPIETARIO_RUC')[0];
            
            function actualizarRUC() {
                if (propietarioRuc && numeroDocumentoComprador) {
                    // Solo auto-completar si el tipo de cliente es "P. Natural con RUC"
                    const tipoClienteSeleccionado = document.querySelector('input[name="OC_TIPO_CLIENTE"]:checked');
                    if (tipoClienteSeleccionado && tipoClienteSeleccionado.value === 'ruc') {
                        propietarioRuc.value = numeroDocumentoComprador.value.trim();
                    } else {
                        // Si no es "P. Natural con RUC", limpiar el campo RUC
                        propietarioRuc.value = '';
                    }
                }
            }
            
            // Agregar event listeners tanto al número de documento como a los radios de tipo de cliente
            if (numeroDocumentoComprador && propietarioRuc) {
                numeroDocumentoComprador.addEventListener('input', actualizarRUC);
                
                // Agregar event listeners a todos los radios de tipo de cliente
                const tipoClienteRadios = document.querySelectorAll('input[name="OC_TIPO_CLIENTE"]');
                tipoClienteRadios.forEach(function(radio) {
                    radio.addEventListener('change', actualizarRUC);
                });
                
                // También rellenar al cargar si ya hay valores
                actualizarRUC();
            }
        }

        // Función para manejar bloqueo de estado civil según tipo de documento del comprador
        function manejarEstadoCivilPorDocumento() {
            const tipoDocumentoComprador = document.getElementsByName('OC_COMPRADOR_TIPO_DOCUMENTO')[0];
            const estadoCivilSelect = document.getElementsByName('OC_ESTADO_CIVIL')[0];
            
            function actualizarEstadoCivil() {
                if (tipoDocumentoComprador && estadoCivilSelect) {
                    const tipoDoc = tipoDocumentoComprador.value;
                    
                    // Primero verificar el tipo de cliente
                    const tipoClienteSeleccionado = document.querySelector('input[name="OC_TIPO_CLIENTE"]:checked');
                    const esJuridica = tipoClienteSeleccionado && tipoClienteSeleccionado.value === 'juridica';
                    
                    if (esJuridica) {
                        // Si es persona jurídica, estado civil siempre deshabilitado
                        aplicarEstiloDeshabilitado(estadoCivilSelect, true);
                        estadoCivilSelect.value = '';
                    } else if (tipoDoc === 'ruc') {
                        // Si es persona natural pero documento es RUC, estado civil deshabilitado
                        aplicarEstiloDeshabilitado(estadoCivilSelect, true);
                        estadoCivilSelect.value = '';
                    } else {
                        // En cualquier otro caso, estado civil habilitado
                        aplicarEstiloDeshabilitado(estadoCivilSelect, false);
                    }
                }
            }
            
            if (tipoDocumentoComprador && estadoCivilSelect) {
                tipoDocumentoComprador.addEventListener('change', actualizarEstadoCivil);
                
                // Ya no necesitamos listeners para los radios de tipo de cliente,
                // porque manejarEstadoCivilPorDocumento() se ejecuta al final de manejarCamposTipoCliente()
                
                // También verificar al cargar si ya hay valores
                actualizarEstadoCivil();
                
                // Actualizar atributos required después de cambiar estado civil
                actualizarCamposRequeridos();
            }
        }

        // Función para auto-seleccionar tipo de documento de venta según tipo de cliente
        function autoSeleccionarTipoDocumento() {
            const tipoClienteRadios = document.getElementsByName('OC_TIPO_CLIENTE');
            const tipoDocVentaSelect = document.getElementsByName('OC_TIPO_DOCUMENTO_VENTA')[0];
            
            if (tipoClienteRadios && tipoDocVentaSelect) {
                tipoClienteRadios.forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        if (this.checked) {
                            if (this.value === 'natural') {
                                // Persona natural -> BOLETA DE VENTA
                                tipoDocVentaSelect.value = 'boleta';
                                console.log('✅ Auto-seleccionado: BOLETA DE VENTA (Persona natural)');
                            } else if (this.value === 'ruc' || this.value === 'juridica') {
                                // P. Natural con RUC o Persona Jurídica -> FACTURA DE VENTA
                                tipoDocVentaSelect.value = 'factura';
                                console.log('✅ Auto-seleccionado: FACTURA DE VENTA (' + (this.value === 'ruc' ? 'P. Natural con RUC' : 'Persona Jurídica') + ')');
                            }
                        }
                    });
                });
            }
        }

        // Función para auto-seleccionar tipo de documento del COMPRADOR según tipo de cliente
        function autoSeleccionarTipoDocumentoComprador() {
            const tipoClienteRadios = document.getElementsByName('OC_TIPO_CLIENTE');
            const tipoDocCompradorSelect = document.getElementsByName('OC_COMPRADOR_TIPO_DOCUMENTO')[0];
            
            if (tipoClienteRadios && tipoDocCompradorSelect) {
                tipoClienteRadios.forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        if (this.checked) {
                            if (this.value === 'natural') {
                                // Persona natural -> DNI (auto-seleccionado, pero SIN deshabilitar)
                                tipoDocCompradorSelect.value = 'dni';
                                console.log('✅ Auto-seleccionado Tipo Doc Comprador: DNI (Persona natural)');
                            } else if (this.value === 'ruc') {
                                // P. Natural con RUC -> RUC (auto-seleccionado, pero SIN deshabilitar)
                                tipoDocCompradorSelect.value = 'ruc';
                                console.log('✅ Auto-seleccionado Tipo Doc Comprador: RUC (P. Natural con RUC)');
                            } else if (this.value === 'juridica') {
                                // Persona jurídica -> Habilitar para que el usuario elija
                                aplicarEstiloDeshabilitado(tipoDocCompradorSelect, false);
                                console.log('✅ Tipo Doc Comprador habilitado (Persona Jurídica)');
                            }
                            
                            // IMPORTANTE: Después de cambiar el tipo de documento, verificar estado civil
                            // Usar setTimeout para asegurar que el valor se haya actualizado en el DOM
                            setTimeout(function() {
                                manejarEstadoCivilPorDocumento();
                            }, 0);
                        }
                    });
                });
            }
        }

        // Contador para abonos
        let contadorAbonos = 1;

        // Función para agregar abono dinámicamente
        function agregarAbono() {
            if (contadorAbonos > 7) {
                alert('Máximo 7 abonos permitidos');
                return;
            }
            
            const container = document.getElementById('abonos-container');
            const div = document.createElement('div');
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.borderBottom = '1px solid #000';
            div.style.fontSize = '9px';
            div.innerHTML = `
                <div style="flex:0.8; padding:2px; border-right:1px solid #000;">
                    <input type="text" name="OC_MONTO_${contadorAbonos}" placeholder="US$ 0.00 o MN 0.00" style="width:100%; font-size:9px; padding:2px;">
                </div>
                <div style="flex:1.2; padding:2px; border-right:1px solid #000;">
                    <input type="text" name="OC_NRO_OPERACION_${contadorAbonos}" placeholder="Nro. Operación" style="width:100%; font-size:9px; padding:2px;">
                </div>
                <div style="flex:1.5; padding:2px; border-right:1px solid #000;">
                    <select name="OC_ENTIDAD_FINANCIERA_${contadorAbonos}" style="width:100%; font-size:8px; padding:2px;" required>
                        <option value="">-- Seleccione Banco * --</option>
                        <?php foreach ($bancos as $banco): ?>
                            <option value="<?php echo htmlspecialchars($banco); ?>"><?php echo htmlspecialchars($banco); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex:1.5; padding:2px;">
                    <input type="file" name="OC_ARCHIVO_ABONO${contadorAbonos}" accept=".pdf,.jpg,.png,.jpeg" style="width:100%; font-size:8px;">
                </div>
            `;
            container.appendChild(div);
            contadorAbonos++;
            
            // Actualizar atributos required después de agregar nuevo abono
            actualizarCamposRequeridos();
        }

        // Contador para "Otros documentos" - empezar desde el siguiente número realmente usado (1..6)
        let contadorOtros = <?php 
            $maxOtros = 0;
            foreach ($ordenCompraData as $key => $value) {
                if (strpos($key, 'OC_ARCHIVO_OTROS_') === 0 && !empty($value)) {
                    $numero = (int)str_replace('OC_ARCHIVO_OTROS_', '', $key);
                    if ($numero > $maxOtros) {
                        $maxOtros = $numero;
                    }
                }
            }
            // Respetar límite de 6 campos definidos en la BD
            if ($maxOtros >= 6) {
                $maxOtros = 6;
            }
            echo $maxOtros + 1;
        ?>;

        // Función para agregar otro documento dinámicamente
        function agregarOtro() {
            const container = document.getElementById('otros-container');

            // Límite máximo según columnas definidas en la BD (OC_ARCHIVO_OTROS_1 .. OC_ARCHIVO_OTROS_6)
            if (contadorOtros > 6) {
                if (typeof mostrarToast === 'function') {
                    mostrarToast('⚠️ Solo se pueden adjuntar hasta 6 "Otros documentos" por orden de compra.', 'warning');
                } else {
                    alert('Solo se pueden adjuntar hasta 6 "Otros documentos" por orden de compra.');
                }
                return;
            }

            const div = document.createElement('div');
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.marginTop = '4px';
            div.innerHTML = `
                <div style="background:#ffffff; font-weight:bold; padding:4px; width:120px;">Otro documento ${contadorOtros}</div>
                <input type="file" name="OC_ARCHIVO_OTROS_${contadorOtros}" accept=".pdf,.jpg,.png,.jpeg" style="flex:1;">
            `;
            container.appendChild(div);
            contadorOtros++;
        }


        // Variables para login de firma
        let elementoFirmaActual = null;

        // Función para mostrar login
        function mostrarLogin(elemento, tipo) {
            elementoFirmaActual = elemento;
            const form = document.getElementById('login-form');
            form.style.display = 'block';
            form.style.left = (elemento.offsetLeft + 10) + 'px';
            form.style.top = (elemento.offsetTop + elemento.offsetHeight + 10) + 'px';
            document.getElementById('login-usuario').value = '';
            document.getElementById('login-password').value = '';
            document.getElementById('login-usuario').focus();
        }

        // Función para cerrar login
        function cerrarLogin() {
            document.getElementById('login-form').style.display = 'none';
            elementoFirmaActual = null;
        }

        // Función para verificar firma
        function verificarFirma() {
            const usuario = document.getElementById('login-usuario').value.trim();
            const password = document.getElementById('login-password').value.trim();
            if (!usuario || !password) {
                alert('Ingrese usuario y contraseña.');
                return;
            }
            fetch('/digitalizacion-documentos/documents/verificar-firma', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'usuario=' + encodeURIComponent(usuario) + '&password=' + encodeURIComponent(password)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Insertar imagen de firma en el div vacío correspondiente
                    const text = elementoFirmaActual.innerText.trim();
                    if (text === 'ASESOR DE VENTA') {
                        const emptyDiv = elementoFirmaActual.previousElementSibling;
                        if (emptyDiv) {
                            emptyDiv.innerHTML = '<img src="' + data.firma + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">';
                        }
                        document.getElementById('asesor_firma_hidden').value = data.firma;
                    } else if (text === 'FIRMA CLIENTE') {
                        const topFlex = elementoFirmaActual.parentElement.previousElementSibling;
                        if (topFlex && topFlex.children[0]) {
                            topFlex.children[0].innerHTML = '<img src="' + data.firma + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">';
                        }
                        document.getElementById('cliente_firma_hidden').value = data.firma;
                    } else if (text === 'JEFE DE TIENDA') {
                        const emptyDiv = elementoFirmaActual.previousElementSibling;
                        if (emptyDiv) {
                            emptyDiv.innerHTML = '<img src="' + data.firma + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">';
                        }
                        document.getElementById('jefe_firma_hidden').value = data.firma;
                    } else if (text === 'VISTO ADV°') {
                        // Firma de cajera
                        const previewDiv = document.getElementById('cajera-firma-preview');
                        if (previewDiv) {
                            previewDiv.innerHTML = '<img src="' + data.firma + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">';
                        }
                        document.getElementById('cajera_firma_hidden').value = data.firma;
                    }
                    cerrarLogin();
                } else {
                    alert('Usuario o contraseña incorrectos.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al verificar firma.');
            });
        }

        // Agregar event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Prevenir que el formulario se envíe al presionar Enter
            const form = document.getElementById('ordenCompraForm');
            if (form) {
                form.addEventListener('keydown', function(event) {
                    // Si se presiona Enter y el elemento activo NO es el botón de submit
                    if (event.key === 'Enter' && event.target.type !== 'submit' && event.target.type !== 'button') {
                        event.preventDefault();
                        return false;
                    }
                });
            }
            
            const precioTotalInput = document.getElementsByName('OC_PRECIO_TOTAL_COMPRA')[0];
            const tipoCambioInput = document.getElementsByName('OC_TIPO_CAMBIO')[0];
            const monedaSelect = document.getElementsByName('OC_MONEDA_PRECIO_TOTAL')[0];
            const formaPagoSelect = document.getElementsByName('OC_FORMA_PAGO')[0];
            const precioVentaInput = document.getElementsByName('OC_PRECIO_VENTA')[0];

            if (precioTotalInput && tipoCambioInput) {
                precioTotalInput.addEventListener('input', calcularTipoCambio);
                tipoCambioInput.addEventListener('input', calcularTipoCambio);
            }

            if (monedaSelect) {
                monedaSelect.addEventListener('change', calcularTipoCambio);
            }

            // Event listener para forma de pago
            if (formaPagoSelect) {
                formaPagoSelect.addEventListener('change', manejarBonoFinanciamiento);
            }

            // Event listeners para equipamientos
            for (let i = 1; i <= 5; i++) {
                const equipInput = document.getElementsByName('OC_EQUIPAMIENTO_ADICIONAL_' + i)[0];
                if (equipInput) {
                    equipInput.addEventListener('input', calcularTotalEquipamiento);
                }
            }

            // Event listener para precio de venta
            if (precioVentaInput) {
                precioVentaInput.addEventListener('input', calcularPrecioTotalCompra);
            }

            // Event listeners para calcular saldo automáticamente
            const bonoFinanInput = document.getElementsByName('OC_BONO_FINANCIAMIENTO')[0];
            const bonoCampanaInput = document.getElementsByName('OC_BONO_CAMPANA')[0];
            const pagoCuentaInput = document.getElementsByName('OC_PAGO_CUENTA')[0];
            
            if (bonoFinanInput) {
                bonoFinanInput.addEventListener('input', calcularSaldo);
            }
            if (bonoCampanaInput) {
                bonoCampanaInput.addEventListener('input', calcularSaldo);
            }
            if (pagoCuentaInput) {
                pagoCuentaInput.addEventListener('input', calcularSaldo);
            }
            if (precioTotalInput) {
                precioTotalInput.addEventListener('input', calcularSaldo);
            }

            // Autocompletado de fecha eliminado

            <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
            // Inicializar autocompletado de vehículo
            autocompletarVehiculo();
            
            // Inicializar autocompletado de datos de mantenimiento
            autocompletarDatosMantenimiento();
            <?php endif; ?>
            
            // Los selects de centros de costo se inicializan automáticamente con poblar_selects.js

            // Inicializar cálculos
            manejarBonoFinanciamiento();
            calcularTotalEquipamiento();
            
            // Maquillar precios fk fake DESPUÉS de todos los recálculos iniciales (solo vista cliente)
            maquillarPreciosFkFake();
            
            // Event listeners para tipo de cliente (radio buttons)
            const tipoClienteRadios = document.querySelectorAll('input[name="OC_TIPO_CLIENTE"]');
            tipoClienteRadios.forEach(radio => {
                radio.addEventListener('change', manejarCamposTipoCliente);
            });

            // Event listener para estado civil
            const estadoCivilSelect = document.getElementsByName('OC_ESTADO_CIVIL')[0];
            if (estadoCivilSelect) {
                estadoCivilSelect.addEventListener('change', manejarCamposConyuge);
            }

            // Inicializar campos de cónyuge
            manejarCamposConyuge();
            
            // Inicializar campos según tipo de cliente (si ya hay uno seleccionado)
            manejarCamposTipoCliente();
            
            // Validación de número de documento del cónyuge
            validarNumeroDocumentoConyuge();

            // Event listener para resetear validación al cambiar tipo de documento
            const tipoDocSelect = document.getElementsByName('OC_COMPRADOR_TIPO_DOCUMENTO')[0];
            if (tipoDocSelect) {
                tipoDocSelect.addEventListener('change', validarNumeroDocumentoComprador);
            }

            // Inicializar auto-relleno de "Tarjeta a Nombre de"
            autoRellenarTarjetaNombre();
            
            // Inicializar auto-relleno de tarjeta cuando estado civil es CASADO
            autoRellenarTarjetaCasado();
            
            // Inicializar auto-completado del nombre del propietario
            autoCompletarPropietarioNombre();
            
            // Inicializar auto-completado del RUC del propietario
            autoCompletarPropietarioRUC();
            
            // Inicializar auto-selección de tipo de documento de venta
            autoSeleccionarTipoDocumento();
            
            // Inicializar auto-selección de tipo de documento del comprador
            autoSeleccionarTipoDocumentoComprador();
            
            // Verificar estado civil inicial según tipo de documento
            manejarEstadoCivilPorDocumento();
            
            // Actualizar atributos required iniciales
            actualizarCamposRequeridos();

            // Reaplicar reglas de tipo de cliente y cónyuge al final de la carga
            // Esto asegura que al abrir en modo EDICIÓN (botón EDITAR) se respeten
            // las mismas deshabilitaciones de campos según el tipo de cliente
            setTimeout(function () {
                manejarCamposTipoCliente();
                manejarCamposConyuge();
            }, 0);
        });
    </script>

    <!-- Cargar datos de la orden actual -->
    <?php
        // Preparar copia de datos para JS (solo visual). No modifica lo almacenado en BD.
        $ordenDataParaJs = isset($ordenCompraData) ? $ordenCompraData : null;

        if ($ordenDataParaJs && isset($modoImpresion) && $modoImpresion && isset($esVistaCliente) && $esVistaCliente && !empty($ordenDataParaJs['OC_FAKE_PRECIO'])) {
            $precioVentaReal = isset($ordenDataParaJs['OC_PRECIO_VENTA']) ? (float)$ordenDataParaJs['OC_PRECIO_VENTA'] : 0;
            $totalEquipReal  = isset($ordenDataParaJs['OC_TOTAL_EQUIPAMIENTO']) ? (float)$ordenDataParaJs['OC_TOTAL_EQUIPAMIENTO'] : 0;
            $pagoCuentaReal  = isset($ordenDataParaJs['OC_PAGO_CUENTA']) ? (float)$ordenDataParaJs['OC_PAGO_CUENTA'] : 0;

            $nuevoPrecioVenta = $precioVentaReal + $totalEquipReal;
            $nuevoPrecioTotal = $nuevoPrecioVenta;
            $nuevoSaldo       = $nuevoPrecioTotal - $pagoCuentaReal;

            // Solo para la vista del cliente: maquillar montos
            $ordenDataParaJs['OC_PRECIO_VENTA']        = number_format($nuevoPrecioVenta, 2, '.', '');
            $ordenDataParaJs['OC_TOTAL_EQUIPAMIENTO']  = number_format(0, 2, '.', '');
            $ordenDataParaJs['OC_PRECIO_TOTAL_COMPRA'] = number_format($nuevoPrecioTotal, 2, '.', '');
            $ordenDataParaJs['OC_SALDO_PENDIENTE']     = number_format($nuevoSaldo, 2, '.', '');
        }
    ?>
    <script>
        // Usar ordenDataParaJs solo para efectos visuales en el navegador
        const ordenData = <?= ($ordenDataParaJs && !empty($ordenDataParaJs)) ? json_encode($ordenDataParaJs, JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) : 'null' ?>;
        const firmasData = <?= (isset($ordenCompraData) && !empty($ordenCompraData)) ? json_encode($ordenCompraData, JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) : 'null' ?>;
        
        // Cargar firmas si existen
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar firma del asesor automáticamente desde la sesión
            const firmaAsesorSesion = '<?php echo !empty($_SESSION["usuario_firma"]) ? addslashes($_SESSION["usuario_firma"]) : ""; ?>';
            
            if (firmasData && firmasData.OC_ASESOR_FIRMA) {
                // Si ya existe firma guardada en la BD, usarla
                document.getElementById('asesor_firma_hidden').value = firmasData.OC_ASESOR_FIRMA;
            } else if (firmaAsesorSesion) {
                // Si no hay firma guardada pero el usuario está logueado, usar su firma
                document.getElementById('asesor_firma_hidden').value = firmaAsesorSesion;
                console.log('✅ Firma del asesor cargada automáticamente desde la sesión');
            }
            
            // Cargar otras firmas si existen
            if (firmasData) {
                if (firmasData.OC_CLIENTE_FIRMA) document.getElementById('cliente_firma_hidden').value = firmasData.OC_CLIENTE_FIRMA;
                if (firmasData.OC_CLIENTE_HUELLA) document.getElementById('cliente_huella_hidden').value = firmasData.OC_CLIENTE_HUELLA;
                if (firmasData.OC_JEFE_FIRMA) document.getElementById('jefe_firma_hidden').value = firmasData.OC_JEFE_FIRMA;
                if (firmasData.OC_JEFE_HUELLA) document.getElementById('jefe_huella_hidden').value = firmasData.OC_JEFE_HUELLA;
                if (firmasData.OC_VISTO_ADV) document.getElementById('cajera_firma_hidden').value = firmasData.OC_VISTO_ADV;
            }
        });

        // Función para limpiar el formulario y generar nueva orden
        function limpiarFormulario() {
            if (confirm('¿Estás seguro de que deseas generar una nueva orden? Se limpiarán todos los datos del formulario actual.')) {
                // Limpiar sesión en el servidor
                fetch('/digitalizacion-documentos/documents/limpiar-sesion', {
                    method: 'POST'
                })
                .then(() => {
                    // Recargar la página para mostrar formulario limpio
                    window.location.href = '/digitalizacion-documentos/documents/show?id=orden-compra';
                })
                .catch(error => {
                    console.error('Error al limpiar sesión:', error);
                    // Recargar de todas formas
                    window.location.href = '/digitalizacion-documentos/documents/show?id=orden-compra';
                });
            }
        }
    </script>
    <?php if (!isset($esVistaCliente) || !$esVistaCliente): ?>
    <script src="/digitalizacion-documentos/public/js/cargar_datos_sesion.js"></script>
    <?php endif; ?>

    <!-- Campos ocultos para firmas -->
    <input type="hidden" name="OC_ASESOR_FIRMA" id="asesor_firma_hidden">
    <input type="hidden" name="OC_CLIENTE_FIRMA" id="cliente_firma_hidden">
    <input type="hidden" name="OC_CLIENTE_HUELLA" id="cliente_huella_hidden">
    <input type="hidden" name="OC_JEFE_FIRMA" id="jefe_firma_hidden">
    <input type="hidden" name="OC_JEFE_HUELLA" id="jefe_huella_hidden">
    <input type="hidden" name="OC_VISTO_ADV" id="cajera_firma_hidden">

    <!-- Formulario de login para firmas -->
    <div id="login-form" style="display:none; position:absolute; background:white; border:1px solid #000; padding:10px; z-index:1000;">
        <div style="margin-bottom:5px;">Usuario: <input type="text" id="login-usuario" style="width:100px;"></div>
        <div style="margin-bottom:5px;">Contraseña: <input type="password" id="login-password" style="width:100px;"></div>
        <button type="button" onclick="verificarFirma()">Ingresar</button>
        <button type="button" onclick="cerrarLogin()">Cancelar</button>
    </div>

    </form>

    <!-- Script para prellenar selects cuando hay datos existentes -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // DEBUG: Verificar datos de la orden
        console.log('🔍 DEBUG: Verificando ordenCompraData...');
        console.log('¿Existe ordenCompraData?', <?= isset($ordenCompraData) ? 'true' : 'false' ?>);
        console.log('¿Está vacío?', <?= empty($ordenCompraData) ? 'true' : 'false' ?>);
        <?php if (isset($ordenCompraData) && !empty($ordenCompraData)): ?>
            console.log('✅ ordenCompraData tiene datos:', <?= json_encode($ordenCompraData, JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>);
            console.log('Agencia guardada:', '<?= $ordenCompraData['OC_AGENCIA'] ?? 'NO DEFINIDA' ?>');
            console.log('Responsable guardado:', '<?= $ordenCompraData['OC_NOMBRE_RESPONSABLE'] ?? 'NO DEFINIDO' ?>');
            console.log('Centro de Costo guardado:', '<?= $ordenCompraData['OC_CENTRO_COSTO'] ?? 'NO DEFINIDO' ?>');
            
            // CARGAR ABONOS EXISTENTES (dentro del DOMContentLoaded)
            // Solo cargar con JavaScript si NO es modo impresión
            <?php if (!isset($modoImpresion) || !$modoImpresion): ?>
                console.log('📋 Cargando abonos existentes con JavaScript...');
                <?php for ($i = 1; $i <= 7; $i++): ?>
                <?php 
                $monto = $ordenCompraData['OC_MONTO_' . $i] ?? '';
                $nroOp = $ordenCompraData['OC_NRO_OPERACION_' . $i] ?? '';
                $entidad = $ordenCompraData['OC_ENTIDAD_FINANCIERA_' . $i] ?? '';
                ?>
                console.log('🔍 Abono <?php echo $i; ?>: Monto=<?= json_encode($monto) ?>, NroOp=<?= json_encode($nroOp) ?>, Entidad=<?= json_encode($entidad) ?>');
                <?php 
                // Solo crear fila si hay al menos un dato
                if (!empty($monto) || !empty($nroOp) || !empty($entidad)):
                ?>
                    // Crear fila de abono <?php echo $i; ?> de forma segura
                    console.log('➕ Creando fila de abono <?php echo $i; ?>');
                    try {
                        (function() {
                            const container = document.getElementById('abonos-container');
                        const div = document.createElement('div');
                        div.style.display = 'flex';
                        div.style.alignItems = 'center';
                        div.style.borderBottom = '1px solid #000';
                        div.style.fontSize = '9px';
                        
                        // Columna 1: Monto
                        const col1 = document.createElement('div');
                        col1.style.cssText = 'flex:0.8; padding:2px; border-right:1px solid #000;';
                        const inputMonto = document.createElement('input');
                        inputMonto.type = 'text';
                        inputMonto.name = 'OC_MONTO_<?php echo $i; ?>';
                        inputMonto.value = <?= json_encode($monto, JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>;
                        inputMonto.placeholder = 'US$ 0.00 o MN 0.00';
                        inputMonto.style.cssText = 'width:100%; font-size:9px; padding:2px;';
                        col1.appendChild(inputMonto);
                        
                        // Columna 2: Nro Operacion
                        const col2 = document.createElement('div');
                        col2.style.cssText = 'flex:1.2; padding:2px; border-right:1px solid #000;';
                        const inputNroOp = document.createElement('input');
                        inputNroOp.type = 'text';
                        inputNroOp.name = 'OC_NRO_OPERACION_<?php echo $i; ?>';
                        inputNroOp.value = <?= json_encode($nroOp, JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>;
                        inputNroOp.placeholder = 'Nro. Operación';
                        inputNroOp.style.cssText = 'width:100%; font-size:9px; padding:2px;';
                        col2.appendChild(inputNroOp);
                        
                        // Columna 3: Entidad Financiera
                        const col3 = document.createElement('div');
                        col3.style.cssText = 'flex:1.5; padding:2px; border-right:1px solid #000;';
                        const selectEntidad = document.createElement('select');
                        selectEntidad.name = 'OC_ENTIDAD_FINANCIERA_<?php echo $i; ?>';
                        selectEntidad.style.cssText = 'width:100%; font-size:8px; padding:2px;';
                        selectEntidad.innerHTML = '<option value="">-- Seleccione Banco --</option><?php foreach ($bancos as $banco): ?><option value="<?php echo htmlspecialchars($banco); ?>"><?php echo htmlspecialchars($banco); ?></option><?php endforeach; ?>';
                        col3.appendChild(selectEntidad);
                        // Asignar valor DESPUÉS de agregar al DOM
                        const valorEntidad = <?= json_encode($entidad, JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>;
                        if (valorEntidad) {
                            selectEntidad.value = valorEntidad;
                            console.log('✅ Entidad seleccionada para abono <?php echo $i; ?>:', valorEntidad, '| Valor actual:', selectEntidad.value);
                        }
                        
                        // Columna 4: Archivo
                        const col4 = document.createElement('div');
                        col4.style.cssText = 'flex:1.5; padding:2px;';
                        const inputFile = document.createElement('input');
                        inputFile.type = 'file';
                        inputFile.name = 'OC_ARCHIVO_ABONO<?php echo $i; ?>';
                        inputFile.accept = '.pdf,.jpg,.png,.jpeg';
                        inputFile.style.cssText = 'width:100%; font-size:8px;';
                        col4.appendChild(inputFile);
                        <?php if (!empty($ordenCompraData['OC_ARCHIVO_ABONO' . $i])): ?>
                        const small = document.createElement('small');
                        small.style.cssText = 'color: green; font-size: 7px;';
                        small.textContent = '✓ Archivo guardado';
                        col4.appendChild(small);
                        <?php endif; ?>
                        
                        // Agregar columnas al div
                        div.appendChild(col1);
                        div.appendChild(col2);
                        div.appendChild(col3);
                        div.appendChild(col4);
                        
                        container.appendChild(div);
                        contadorAbonos = <?php echo $i + 1; ?>;
                        })();
                        console.log('✅ Fila de abono <?php echo $i; ?> creada exitosamente');
                    } catch (error) {
                        console.error('❌ ERROR creando fila de abono <?php echo $i; ?>:', error);
                        console.error('Detalles:', error.message, error.stack);
                    }
                <?php endif; ?>
            <?php endfor; ?>
                console.log('✅ Abonos cargados completamente con JavaScript');
            <?php endif; // Fin de: if (!isset($modoImpresion) || !$modoImpresion) ?>
        <?php endif; ?>
        
        // Función para esperar a que un select tenga opciones (SIEMPRE definida)
        function esperarOpcionesSelect(select, callback, maxIntentos = 50) {
            // Si no existe el select, no hacer nada para evitar errores en vistas donde no aplica
            if (!select) {
                console.warn('⚠️ esperarOpcionesSelect llamado sin select válido');
                return;
            }

            let intentos = 0;
            const intervalo = setInterval(function() {
                intentos++;
                // Verificar si el select tiene más de 1 opción (más que el placeholder)
                if (select.options.length > 1) {
                    clearInterval(intervalo);
                    console.log(`✅ Opciones disponibles para ${select.id} después de ${intentos} intentos`);
                    callback();
                } else if (intentos >= maxIntentos) {
                    clearInterval(intervalo);
                    console.error(`❌ TIMEOUT esperando opciones para ${select.id} después de ${maxIntentos} intentos (${maxIntentos * 200}ms)`);
                    console.error(`Opciones actuales:`, select.options.length);
                }
            }, 200); // Revisar cada 200ms (máximo 10 segundos con 50 intentos)
        }
        
        // Esperar a que poblar_selects.js termine de cargar las opciones
        <?php if (isset($ordenCompraData) && !empty($ordenCompraData)): ?>
            console.log('⏰ Esperando 500ms antes de iniciar carga de selectores...');
            setTimeout(function() {
                console.log('=== Iniciando carga de datos de orden ===');
                console.log('📦 Datos completos de la orden:', <?= json_encode($ordenCompraData, JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>);
                
                const agenciaSelect = document.getElementById('agencia');
                const agenciaValor = '<?php echo htmlspecialchars($ordenCompraData['OC_AGENCIA'] ?? ''); ?>';
                
                console.log('🔍 SELECT AGENCIA:', agenciaSelect);
                console.log('🔍 VALOR A CARGAR:', agenciaValor);
                console.log('🔍 LONGITUD DEL VALOR:', agenciaValor.length);
                console.log('🔍 VALOR EN BYTES:', JSON.stringify(agenciaValor));
                console.log('🔍 OPCIONES DISPONIBLES:', agenciaSelect ? agenciaSelect.options.length : 'SELECT NO EXISTE');
                
                if (!agenciaSelect) {
                    console.error('❌ ERROR: El select de agencia NO existe en el DOM');
                    return;
                }
                
                if (!agenciaValor || agenciaValor.trim() === '') {
                    console.warn('⚠️ ADVERTENCIA: No hay valor de agencia para cargar');
                    return;
                }
                
                if (agenciaSelect && agenciaValor) {
                    console.log('✅ Condición cumplida, esperando opciones de Agencia...');
                    
                    esperarOpcionesSelect(agenciaSelect, function() {
                        console.log('Opciones de Agencia cargadas. Seleccionando:', agenciaValor);
                        console.log('📋 Opciones disponibles:', Array.from(agenciaSelect.options).map(o => o.value));
                        
                        agenciaSelect.value = agenciaValor;
                        
                        console.log('✅ Valor seleccionado:', agenciaSelect.value);
                        console.log('❓ ¿Se seleccionó correctamente?', agenciaSelect.value === agenciaValor);
                        
                        // Disparar evento change para cargar responsables
                        agenciaSelect.dispatchEvent(new Event('change'));
                        
                        const responsableSelect = document.getElementById('nombre_responsable');
                        const responsableValor = '<?php echo htmlspecialchars($ordenCompraData['OC_NOMBRE_RESPONSABLE'] ?? ''); ?>';
                        
                        if (responsableSelect && responsableValor) {
                            console.log('Esperando opciones de Responsable...');
                            
                            esperarOpcionesSelect(responsableSelect, function() {
                                console.log('Opciones de Responsable cargadas. Seleccionando:', responsableValor);
                                responsableSelect.value = responsableValor;
                                
                                // Disparar evento change para cargar centros de costo
                                responsableSelect.dispatchEvent(new Event('change'));
                                
                                const centroCostoSelect = document.getElementById('centro_costo');
                                const centroCostoValor = '<?php echo htmlspecialchars($ordenCompraData['OC_CENTRO_COSTO'] ?? ''); ?>';
                                
                                if (centroCostoSelect && centroCostoValor) {
                                    console.log('Esperando opciones de Centro de Costo...');
                                    
                                    esperarOpcionesSelect(centroCostoSelect, function() {
                                        console.log('Opciones de Centro de Costo cargadas. Seleccionando:', centroCostoValor);
                                        centroCostoSelect.value = centroCostoValor;
                                        
                                        // Disparar evento change para cargar email
                                        centroCostoSelect.dispatchEvent(new Event('change'));
                                        
                                        console.log('=== Carga de selectores completada ===');
                                    });
                                }
                            });
                        }
                    });
                }
            }, 500); // Esperar 500ms iniciales, luego esperarOpcionesSelect espera activamente
        <?php endif; ?>
        
        // ===== FUNCIONALIDAD: Enter para pasar al siguiente campo =====
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input:not([type="file"]):not([type="submit"]):not([type="button"]), select, textarea');
            
            // Prevenir que Enter envíe el formulario
            form.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    
                    // Encontrar el índice del campo actual
                    const currentIndex = Array.from(inputs).indexOf(e.target);
                    
                    if (currentIndex > -1 && currentIndex < inputs.length - 1) {
                        // Pasar al siguiente campo
                        inputs[currentIndex + 1].focus();
                    }
                }
            });
            
            console.log('✅ Funcionalidad Enter activada para', inputs.length, 'campos');
        });
    });
    </script>

    <!-- Modal para capturar firma del cliente -->
    <div id="modalFirmaCliente" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:10000; justify-content:center; align-items:center;">
        <div style="background:white; padding:30px; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,0.3); max-width:600px; width:90%;">
            <h4 style="margin:0 0 20px 0; color:#333; text-align:center;">
                <i class="bi bi-pen"></i> Firma del Cliente
            </h4>
            
            <div style="border:2px solid #667eea; border-radius:10px; overflow:hidden; margin-bottom:20px;">
                <canvas id="canvasFirmaCliente" width="540" height="200" style="display:block; cursor:crosshair; touch-action:none;"></canvas>
            </div>
            
            <p style="text-align:center; color:#666; font-size:14px; margin-bottom:20px;">
                Dibuje su firma usando el mouse o el dedo (en pantallas táctiles)
            </p>
            
            <div style="display:flex; gap:10px; justify-content:center;">
                <button onclick="limpiarFirma()" style="background:#dc3545; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">
                    <i class="bi bi-trash"></i> Limpiar
                </button>
                <button onclick="guardarFirmaCliente()" style="background:#28a745; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">
                    <i class="bi bi-check-circle"></i> Guardar Firma
                </button>
                <button onclick="cerrarModalFirma()" style="background:#6c757d; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
            </div>
        </div>
    </div>

    <script>
        let canvas, ctx, dibujando = false;
        let ultimoX = 0, ultimoY = 0;

        function abrirCapturadorFirma() {
            const modal = document.getElementById('modalFirmaCliente');
            modal.style.display = 'flex';
            
            // Inicializar canvas
            canvas = document.getElementById('canvasFirmaCliente');
            ctx = canvas.getContext('2d');
            
            // Configurar canvas
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            
            // Limpiar canvas
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Eventos para mouse
            canvas.addEventListener('mousedown', iniciarDibujo);
            canvas.addEventListener('mousemove', dibujar);
            canvas.addEventListener('mouseup', detenerDibujo);
            canvas.addEventListener('mouseout', detenerDibujo);
            
            // Eventos para touch (móviles/tablets)
            canvas.addEventListener('touchstart', iniciarDibujoTouch);
            canvas.addEventListener('touchmove', dibujarTouch);
            canvas.addEventListener('touchend', detenerDibujo);
        }

        function iniciarDibujo(e) {
            dibujando = true;
            const rect = canvas.getBoundingClientRect();
            ultimoX = e.clientX - rect.left;
            ultimoY = e.clientY - rect.top;
        }

        function dibujar(e) {
            if (!dibujando) return;
            
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            ctx.beginPath();
            ctx.moveTo(ultimoX, ultimoY);
            ctx.lineTo(x, y);
            ctx.stroke();
            
            ultimoX = x;
            ultimoY = y;
        }

        function detenerDibujo() {
            dibujando = false;
        }

        function iniciarDibujoTouch(e) {
            e.preventDefault();
            dibujando = true;
            const rect = canvas.getBoundingClientRect();
            const touch = e.touches[0];
            ultimoX = touch.clientX - rect.left;
            ultimoY = touch.clientY - rect.top;
        }

        function dibujarTouch(e) {
            if (!dibujando) return;
            e.preventDefault();
            
            const rect = canvas.getBoundingClientRect();
            const touch = e.touches[0];
            const x = touch.clientX - rect.left;
            const y = touch.clientY - rect.top;
            
            ctx.beginPath();
            ctx.moveTo(ultimoX, ultimoY);
            ctx.lineTo(x, y);
            ctx.stroke();
            
            ultimoX = x;
            ultimoY = y;
        }

        function limpiarFirma() {
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }

        function guardarFirmaCliente() {
            // Verificar que haya algo dibujado
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
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
            
            // Convertir canvas a imagen base64
            const firmaDataURL = canvas.toDataURL('image/png');
            
            // Mostrar mensaje de carga
            const btnGuardar = event.target;
            const textoOriginal = btnGuardar.innerHTML;
            btnGuardar.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';
            btnGuardar.disabled = true;
            
            // Enviar por AJAX al servidor para guardar como archivo
            fetch('/digitalizacion-documentos/documents/guardar-firma-cliente', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'firma_base64=' + encodeURIComponent(firmaDataURL)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Guardar la ruta del archivo en el campo oculto
                    document.getElementById('cliente_firma_hidden').value = data.ruta;
                    
                    // Mostrar preview con marca de agua
                    const preview = document.getElementById('firma-cliente-preview');
                    preview.innerHTML = '<img src="' + data.ruta + '" style="max-width:100%; max-height:50px; display:block; margin:0 auto;">' +
                                       '<img src="/digitalizacion-documentos/assets/images/logo_interamericana.jpg" style="position:absolute; bottom:0; left:50%; transform:translateX(-50%); max-width:60%; opacity:0.15; max-height:30px;">';
                    
                    // Cerrar modal
                    cerrarModalFirma();
                    
                    console.log('✅ Firma del cliente guardada en:', data.ruta);
                } else {
                    alert('Error al guardar la firma: ' + data.error);
                    btnGuardar.innerHTML = textoOriginal;
                    btnGuardar.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar la firma. Por favor intente de nuevo.');
                btnGuardar.innerHTML = textoOriginal;
                btnGuardar.disabled = false;
            });
        }

        function cerrarModalFirma() {
            const modal = document.getElementById('modalFirmaCliente');
            modal.style.display = 'none';
        }
    </script>

    <!-- MODAL: Vehículos Asignados al Asesor -->
    <div id="modalVehiculosAsesor" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:10000; overflow:auto;">
        <div style="background:white; margin:30px auto; max-width:1400px; width:95%; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
            <!-- Header -->
            <div style="background:#3b82f6; color:white; padding:15px 20px; border-radius:8px 8px 0 0; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; font-size:16px;">🚗 Seleccionar Vehículo</h3>
                <button onclick="cerrarModalVehiculos()" style="background:transparent; border:none; color:white; font-size:24px; cursor:pointer; padding:0; width:30px; height:30px;">&times;</button>
            </div>
            
            <!-- Buscador y Filtros -->
            <div style="padding:15px 20px; border-bottom:1px solid #e5e7eb;">
                <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:10px; margin-bottom:10px;">
                    <input type="text" id="buscadorVehiculos" placeholder="🔍 Buscar por chasis, marca, modelo..." 
                           style="padding:10px; border:1px solid #d1d5db; border-radius:4px; font-size:14px;"
                           onkeyup="filtrarVehiculos()">
                    
                    <select id="filtroMarca" onchange="actualizarFiltroModelos(); actualizarFiltroColores(); filtrarVehiculos();" 
                            style="padding:10px; border:1px solid #d1d5db; border-radius:4px; font-size:14px; cursor:pointer;">
                        <option value="">🏷️ Todas las marcas</option>
                    </select>
                    
                    <select id="filtroModelo" onchange="actualizarFiltroColores(); filtrarVehiculos()" 
                            style="padding:10px; border:1px solid #d1d5db; border-radius:4px; font-size:14px; cursor:pointer;">
                        <option value="">🚙 Todos los modelos</option>
                    </select>
                </div>
                
                <!-- Segunda fila de filtros -->
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:10px;">
                    <select id="filtroPrioridad" onchange="filtrarVehiculos()" 
                            style="padding:10px; border:1px solid #d1d5db; border-radius:4px; font-size:14px; cursor:pointer;">
                        <option value="">⚡ Todas las prioridades</option>
                        <option value="2">🔴 MUY ALTA</option>
                        <option value="1">🟠 ALTA</option>
                        <option value="0">🟢 NORMAL</option>
                    </select>
                    
                    <select id="filtroColor" onchange="filtrarVehiculos()" 
                            style="padding:10px; border:1px solid #d1d5db; border-radius:4px; font-size:14px; cursor:pointer;">
                        <option value="">🎨 Todos los colores</option>
                    </select>
                    
                    <div style="display:flex; align-items:center; gap:8px; padding:8px; background:#fef3c7; border-radius:4px; font-size:12px;">
                        <span style="background:#fbbf24; width:16px; height:16px; border-radius:3px; display:inline-block;"></span>
                        <span style="font-weight:bold;">💰 Dinero Propio</span>
                    </div>
                </div>
            </div>
            
            <!-- Tabs -->
            <div style="padding:0 20px; border-bottom:1px solid #e5e7eb;">
                <div style="display:flex; gap:5px;">
                    <button id="tabAsignados" onclick="cambiarTab('asignados')" 
                            style="padding:12px 20px; background:#3b82f6; color:white; border:none; border-radius:4px 4px 0 0; cursor:pointer; font-size:13px; font-weight:bold;">
                        ✅ Mis Vehículos (<span id="countAsignados">0</span>)
                    </button>
                    <button id="tabLibres" onclick="cambiarTab('libres')" 
                            style="padding:12px 20px; background:#e5e7eb; color:#6b7280; border:none; border-radius:4px 4px 0 0; cursor:pointer; font-size:13px; font-weight:bold;">
                        🆓 Vehículos Libres (<span id="countLibres">0</span>)
                    </button>
                    <button id="tabOtros" onclick="cambiarTab('otros')" 
                            style="padding:12px 20px; background:#e5e7eb; color:#6b7280; border:none; border-radius:4px 4px 0 0; cursor:pointer; font-size:13px; font-weight:bold;">
                        👥 Otros Asesores (<span id="countOtros">0</span>)
                    </button>
                </div>
            </div>
            
            <!-- Contenido de las tabs -->
            <div style="padding:20px; max-height:400px; overflow-y:auto;">
                <!-- Tab: Vehículos Asignados -->
                <div id="contentAsignados" style="display:block;">
                    <table id="tablaAsignados" style="width:100%; border-collapse:collapse; font-size:11px;">
                        <thead>
                            <tr style="background:#f3f4f6; border-bottom:2px solid #d1d5db;">
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">CHASIS</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">MARCA</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">MODELO</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">VERSIÓN</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">COLOR</th>
                                <th style="padding:8px; text-align:center; font-weight:bold; font-size:10px;">PRIORIDAD</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">UBICACIÓN</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">ASESOR</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">FECHA</th>
                            </tr>
                        </thead>
                        <tbody id="bodyAsignados">
                            <!-- Se llena dinámicamente -->
                        </tbody>
                    </table>
                    <div id="mensajeAsignados" style="display:none; text-align:center; padding:40px; color:#6b7280;">
                        <p style="font-size:14px; margin:0;">No tienes vehículos asignados.</p>
                    </div>
                </div>
                
                <!-- Tab: Vehículos Libres -->
                <div id="contentLibres" style="display:none;">
                    <table id="tablaLibres" style="width:100%; border-collapse:collapse; font-size:11px;">
                        <thead>
                            <tr style="background:#f3f4f6; border-bottom:2px solid #d1d5db;">
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">CHASIS</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">MARCA</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">MODELO</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">VERSIÓN</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">COLOR</th>
                                <th style="padding:8px; text-align:center; font-weight:bold; font-size:10px;">PRIORIDAD</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">UBICACIÓN</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">FECHA</th>
                            </tr>
                        </thead>
                        <tbody id="bodyLibres">
                            <!-- Se llena dinámicamente -->
                        </tbody>
                    </table>
                    <div id="mensajeLibres" style="display:none; text-align:center; padding:40px; color:#6b7280;">
                        <p style="font-size:14px; margin:0;">No hay vehículos libres disponibles.</p>
                    </div>
                </div>
                
                <!-- Tab: Vehículos de Otros -->
                <div id="contentOtros" style="display:none;">
                    <table id="tablaOtros" style="width:100%; border-collapse:collapse; font-size:11px;">
                        <thead>
                            <tr style="background:#f3f4f6; border-bottom:2px solid #d1d5db;">
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">CHASIS</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">MARCA</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">MODELO</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">VERSIÓN</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">COLOR</th>
                                <th style="padding:8px; text-align:center; font-weight:bold; font-size:10px;">PRIORIDAD</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">UBICACIÓN</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">ASESOR</th>
                                <th style="padding:8px; text-align:left; font-weight:bold; font-size:10px;">FECHA</th>
                            </tr>
                        </thead>
                        <tbody id="bodyOtros">
                            <!-- Se llena dinámicamente -->
                        </tbody>
                    </table>
                    <div id="mensajeOtros" style="display:none; text-align:center; padding:40px; color:#6b7280;">
                        <p style="font-size:14px; margin:0;">No hay vehículos asignados a otros asesores.</p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div style="padding:15px 20px; border-top:1px solid #e5e7eb; text-align:right;">
                <button onclick="cerrarModalVehiculos()" style="padding:8px 20px; background:#6b7280; color:white; border:none; border-radius:4px; cursor:pointer; font-size:14px;">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: Comentario de Validación -->
    <div id="modalComentario" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:10001; overflow:auto;">
        <div style="background:white; margin:100px auto; max-width:500px; width:90%; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
            <!-- Header -->
            <div style="background:#ef4444; color:white; padding:15px 20px; border-radius:8px 8px 0 0;">
                <h3 style="margin:0; font-size:16px;">⚠️ Validación de Pago</h3>
            </div>
            
            <!-- Body -->
            <div style="padding:20px;">
                <p id="mensajeValidacion" style="margin:0 0 15px 0; font-size:14px; color:#374151;"></p>
                
                <label style="display:block; margin-bottom:5px; font-weight:bold; font-size:14px;">
                    Comentario (obligatorio):
                </label>
                <textarea id="comentarioValidacion" rows="4" 
                          style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px; font-size:14px; resize:vertical;"
                          placeholder="Explique el motivo por el cual no se cumple con la validación..."></textarea>
            </div>
            
            <!-- Footer -->
            <div style="padding:15px 20px; border-top:1px solid #e5e7eb; text-align:right; display:flex; gap:10px; justify-content:flex-end;">
                <button onclick="cerrarModalComentario()" 
                        style="padding:8px 20px; background:#6b7280; color:white; border:none; border-radius:4px; cursor:pointer; font-size:14px;">
                    Cancelar
                </button>
                <button onclick="confirmarComentario()" 
                        style="padding:8px 20px; background:#3b82f6; color:white; border:none; border-radius:4px; cursor:pointer; font-size:14px;">
                    Continuar
                </button>
            </div>
        </div>
    </div>

    <script>
        // DEBUG: Ver datos de sesión PHP
        console.log('📋 Datos de sesión PHP:', {
            nombre: '<?php echo $_SESSION['usuario_nombre_completo'] ?? 'NO EXISTE'; ?>',
            email: '<?php echo $_SESSION['usuario_email'] ?? 'NO EXISTE'; ?>'
        });
        
        // Variable global para almacenar los datos de vehículos
        let datosVehiculos = {
            asignados: [],
            libres: [],
            otros: []
        };
        
        // Tab activa actual
        let tabActiva = 'asignados';
        
        // Abrir modal de vehículos
        function abrirModalVehiculos() {
            console.log('🚗 Abriendo modal de vehículos...');
            const modal = document.getElementById('modalVehiculosAsesor');
            modal.style.display = 'block';
            
            // Cargar vehículos
            cargarVehiculosAsesor();
        }
        
        // Cerrar modal de vehículos
        function cerrarModalVehiculos() {
            const modal = document.getElementById('modalVehiculosAsesor');
            modal.style.display = 'none';
            document.getElementById('buscadorVehiculos').value = '';
        }
        
        // Cambiar de tab
        function cambiarTab(tab) {
            // Actualizar tab activa
            tabActiva = tab;
            
            // Ocultar todos los contenidos
            document.getElementById('contentAsignados').style.display = 'none';
            document.getElementById('contentLibres').style.display = 'none';
            document.getElementById('contentOtros').style.display = 'none';
            
            // Resetear estilos de todos los botones
            document.getElementById('tabAsignados').style.background = '#e5e7eb';
            document.getElementById('tabAsignados').style.color = '#6b7280';
            document.getElementById('tabLibres').style.background = '#e5e7eb';
            document.getElementById('tabLibres').style.color = '#6b7280';
            document.getElementById('tabOtros').style.background = '#e5e7eb';
            document.getElementById('tabOtros').style.color = '#6b7280';
            
            // Mostrar contenido y activar botón de la tab seleccionada
            if (tab === 'asignados') {
                document.getElementById('contentAsignados').style.display = 'block';
                document.getElementById('tabAsignados').style.background = '#3b82f6';
                document.getElementById('tabAsignados').style.color = 'white';
            } else if (tab === 'libres') {
                document.getElementById('contentLibres').style.display = 'block';
                document.getElementById('tabLibres').style.background = '#3b82f6';
                document.getElementById('tabLibres').style.color = 'white';
            } else if (tab === 'otros') {
                document.getElementById('contentOtros').style.display = 'block';
                document.getElementById('tabOtros').style.background = '#3b82f6';
                document.getElementById('tabOtros').style.color = 'white';
            }
        }
        
        // Cargar vehículos del asesor (usa la sesión del usuario logueado)
        function cargarVehiculosAsesor() {
            console.log('📡 Cargando vehículos del asesor logueado...');
            console.log('🔗 URL:', '/digitalizacion-documentos/documents/obtenerVehiculosPorAsesor');
            
            // NO es necesario enviar el nombre, el backend lo obtiene de la sesión
            fetch('/digitalizacion-documentos/documents/obtenerVehiculosPorAsesor', {
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {
                    console.log('📦 Datos procesados:', data);
                    
                    // Mostrar debug logs si existen
                    if (data.vehiculos && data.vehiculos.debug) {
                        console.log('🔍 DEBUG COMPARACIONES:');
                        data.vehiculos.debug.forEach(log => {
                            console.log(`Chasis ${log.chasis}:`);
                            console.log(`  Asesor original: "${log.asesor_original}"`);
                            console.log(`  Asesor normalizado: "${log.asesor_normalizado}"`);
                            console.log(`  Vendedor original: "${log.vendedor_original}"`);
                            console.log(`  Vendedor normalizado: "${log.vendedor_normalizado}"`);
                            console.log(`  Asesor en vendedor: ${log.asesor_en_vendedor ? 'SÍ' : 'NO'}`);
                            console.log(`  Vendedor en asesor: ${log.vendedor_en_asesor ? 'SÍ' : 'NO'}`);
                            console.log(`  Resultado final: ${log.resultado ? 'COINCIDE ✅' : 'NO COINCIDE ❌'}`);
                            console.log('---');
                        });
                    }
                    
                    if (data.success && data.vehiculos) {
                        // Guardar datos en variable global
                        datosVehiculos = data.vehiculos;
                        
                        console.log('✅ Asignados:', datosVehiculos.asignados.length);
                        console.log('✅ Libres:', datosVehiculos.libres.length);
                        console.log('✅ Otros:', datosVehiculos.otros.length);
                        
                        // Actualizar contadores
                        document.getElementById('countAsignados').textContent = datosVehiculos.asignados.length;
                        document.getElementById('countLibres').textContent = datosVehiculos.libres.length;
                        document.getElementById('countOtros').textContent = datosVehiculos.otros.length;
                        
                        // Mostrar vehículos en cada tabla
                        mostrarVehiculosAsignados(datosVehiculos.asignados);
                        mostrarVehiculosLibres(datosVehiculos.libres);
                        mostrarVehiculosOtros(datosVehiculos.otros);
                        
                        // Poblar filtros de marca y modelo
                        poblarFiltros();
                    } else {
                        console.log('⚠️ No se encontraron vehículos o hubo un error');
                        mostrarMensajesSinVehiculos();
                    }
                })
                .catch(error => {
                    console.error('❌ ERROR COMPLETO:', error);
                    mostrarMensajesSinVehiculos();
                });
        }
        
        // Mostrar vehículos asignados
        function mostrarVehiculosAsignados(vehiculos) {
            const tbody = document.getElementById('bodyAsignados');
            const mensaje = document.getElementById('mensajeAsignados');
            
            tbody.innerHTML = '';
            
            if (vehiculos.length === 0) {
                mensaje.style.display = 'block';
                return;
            }
            
            mensaje.style.display = 'none';
            
            vehiculos.forEach(vehiculo => {
                const tr = crearFilaVehiculo(vehiculo, true, 'asignados');
                tbody.appendChild(tr);
            });
        }
        
        // Mostrar vehículos libres
        function mostrarVehiculosLibres(vehiculos) {
            const tbody = document.getElementById('bodyLibres');
            const mensaje = document.getElementById('mensajeLibres');
            
            tbody.innerHTML = '';
            
            if (vehiculos.length === 0) {
                mensaje.style.display = 'block';
                return;
            }
            
            mensaje.style.display = 'none';
            
            vehiculos.forEach(vehiculo => {
                const tr = crearFilaVehiculo(vehiculo, false, 'libres');
                tbody.appendChild(tr);
            });
        }
        
        // Mostrar vehículos de otros asesores
        function mostrarVehiculosOtros(vehiculos) {
            const tbody = document.getElementById('bodyOtros');
            const mensaje = document.getElementById('mensajeOtros');
            
            tbody.innerHTML = '';
            
            if (vehiculos.length === 0) {
                mensaje.style.display = 'block';
                return;
            }
            
            mensaje.style.display = 'none';
            
            vehiculos.forEach(vehiculo => {
                const tr = crearFilaVehiculo(vehiculo, true, 'otros');
                tbody.appendChild(tr);
            });
        }
        
        // Crear fila de vehículo
        // Obtener badge de prioridad con colores
        function getBadgePrioridad(nivel, label) {
            const estilos = {
                0: 'background:#10b981; color:white',  // Verde - NORMAL
                1: 'background:#f59e0b; color:white',  // Naranja - ALTA
                2: 'background:#ef4444; color:white',  // Rojo - MUY ALTA
                3: 'background:#6b7280; color:white'   // Gris - SIN STOCK (nunca aparecerá)
            };
            
            const estilo = estilos[nivel] || estilos[0];
            
            return `<span style="padding:4px 8px; border-radius:4px; font-size:11px; font-weight:bold; white-space:nowrap; ${estilo}">${label}</span>`;
        }
        
        function crearFilaVehiculo(vehiculo, mostrarAsesor, tipoVehiculo) {
            const tr = document.createElement('tr');
            tr.style.cursor = 'pointer';
            tr.style.borderBottom = '1px solid #e5e7eb';
            
            // Resaltar si es dinero propio (STO_CANCELADA == 1)
            const esDineroPropio = vehiculo.dineroPropio || false;
            const colorFondo = esDineroPropio ? '#fef3c7' : 'white';  // Amarillo clarito
            const colorHover = esDineroPropio ? '#fde68a' : '#f9fafb';
            
            tr.style.background = colorFondo;
            tr.onmouseover = function() { this.style.background = colorHover; };
            tr.onmouseout = function() { this.style.background = colorFondo; };
            
            // Guardar datos del vehículo en el elemento para filtrado
            tr.dataset.chasis = vehiculo.chasis.toUpperCase();
            tr.dataset.marca = vehiculo.marca.toUpperCase();
            tr.dataset.modelo = (vehiculo.modelo || '').toUpperCase();
            tr.dataset.version = (vehiculo.version || '').toUpperCase();
            tr.dataset.color = (vehiculo.color || '').toUpperCase();
            tr.dataset.ubicacion = vehiculo.ubicacion.toUpperCase();
            tr.dataset.vendedor = (vehiculo.vendedor || '').toUpperCase();
            tr.dataset.prioridad = vehiculo.prioridad || 0;
            
            // Usar el tipo pasado como parámetro
            tr.onclick = function() { seleccionarVehiculo(vehiculo, tipoVehiculo); };
            
            // Badge de prioridad con colores
            const badgePrioridad = getBadgePrioridad(vehiculo.prioridad || 0, vehiculo.prioridadLabel || 'NORMAL');
            
            if (mostrarAsesor) {
                // Con columna de asesor (para "Asignados" y "Otros")
                tr.innerHTML = `
                    <td style="padding:8px; font-size:10px;">${vehiculo.chasis}</td>
                    <td style="padding:8px;">${vehiculo.marca}</td>
                    <td style="padding:8px;">${vehiculo.modelo || '-'}</td>
                    <td style="padding:8px; font-size:9px;">${vehiculo.version || '-'}</td>
                    <td style="padding:8px;">${vehiculo.color || '-'}</td>
                    <td style="padding:8px; text-align:center;">${badgePrioridad}</td>
                    <td style="padding:8px;">${vehiculo.ubicacion}</td>
                    <td style="padding:8px; font-size:9px;">${vehiculo.vendedor || '-'}</td>
                    <td style="padding:8px;">${vehiculo.fecha}</td>
                `;
            } else {
                // Sin columna de asesor (para "Libres")
                tr.innerHTML = `
                    <td style="padding:8px; font-size:10px;">${vehiculo.chasis}</td>
                    <td style="padding:8px;">${vehiculo.marca}</td>
                    <td style="padding:8px;">${vehiculo.modelo || '-'}</td>
                    <td style="padding:8px; font-size:9px;">${vehiculo.version || '-'}</td>
                    <td style="padding:8px;">${vehiculo.color || '-'}</td>
                    <td style="padding:8px; text-align:center;">${badgePrioridad}</td>
                    <td style="padding:8px;">${vehiculo.ubicacion}</td>
                    <td style="padding:8px;">${vehiculo.fecha}</td>
                `;
            }
            
            return tr;
        }
        
        // Mostrar mensajes cuando no hay vehículos
        function mostrarMensajesSinVehiculos() {
            document.getElementById('bodyAsignados').innerHTML = '';
            document.getElementById('mensajeAsignados').style.display = 'block';
            document.getElementById('bodyLibres').innerHTML = '';
            document.getElementById('mensajeLibres').style.display = 'block';
            document.getElementById('bodyOtros').innerHTML = '';
            document.getElementById('mensajeOtros').style.display = 'block';
            
            document.getElementById('countAsignados').textContent = '0';
            document.getElementById('countLibres').textContent = '0';
            document.getElementById('countOtros').textContent = '0';
        }
        
        // Seleccionar vehículo de la tabla
        function seleccionarVehiculo(vehiculo, tipoVehiculo) {
            console.log('✅ Vehículo seleccionado:', vehiculo);
            console.log('📋 Tipo:', tipoVehiculo);
            
            // CASO 1: Vehículo ya asignado al asesor actual
            if (tipoVehiculo === 'asignados') {
                llenarChasisInput(vehiculo.chasis);
                cerrarModalVehiculos();
                return;
            }
            
            // CASO 2: Vehículo LIBRE
            if (tipoVehiculo === 'libres') {
                mostrarConfirmacionLibre(vehiculo);
                return;
            }
            
            // CASO 3: Vehículo de OTRO ASESOR
            if (tipoVehiculo === 'otros') {
                solicitarReasignacion(vehiculo);
                return;
            }
        }
        
        // Llenar input de chasis
        function llenarChasisInput(chasis) {
            const chasisInput = document.getElementsByName('OC_VEHICULO_CHASIS')[0];
            if (chasisInput) {
                chasisInput.value = chasis;
                chasisInput.dispatchEvent(new Event('blur'));
            }
        }
        
        // Mostrar confirmación para vehículo libre
        function mostrarConfirmacionLibre(vehiculo) {
            console.log('🔔 Mostrando confirmación para vehículo libre:', vehiculo);
            
            const confirmar = confirm(
                `⚠️ ¿Estás seguro que quieres asignar este vehículo a tu nombre?\n\n` +
                `Vehículo: ${vehiculo.marca} - ${vehiculo.chasis}\n` +
                `Ubicación: ${vehiculo.ubicacion}`
            );
            
            console.log('👤 Usuario confirmó:', confirmar);
            
            if (!confirmar) {
                console.log('❌ Usuario canceló la asignación');
                return;
            }
            
            console.log('✅ Usuario aceptó, enviando solicitud...');
            
            // Enviar solicitud al servidor
            const formData = new FormData();
            formData.append('chasis', vehiculo.chasis);
            formData.append('marca', vehiculo.marca);
            formData.append('ubicacion', vehiculo.ubicacion);
            
            fetch('/digitalizacion-documentos/solicitud-vehiculo/solicitar-libre', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('📬 Respuesta del servidor:', data);
                
                if (data.success) {
                    let mensaje = data.message;
                    if (data.email_destino) {
                        mensaje += '\n\n📧 Correo enviado a: ' + data.email_destino;
                    }
                    if (data.correo_enviado === false) {
                        mensaje += '\n\n⚠️ ADVERTENCIA: El correo NO se envió correctamente';
                    }
                    
                    alert('✅ ' + mensaje);
                    llenarChasisInput(vehiculo.chasis);
                    cerrarModalVehiculos();
                } else {
                    alert('❌ Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al procesar la solicitud');
            });
        }
        
        // Solicitar reasignación de vehículo de otro asesor
        function solicitarReasignacion(vehiculo) {
            console.log('📧 Solicitando reasignación de vehículo:', vehiculo);
            
            const confirmar = confirm(
                `📧 Este vehículo está asignado a: ${vehiculo.vendedor}\n\n` +
                `¿Deseas enviar una solicitud de reasignación?\n\n` +
                `Vehículo: ${vehiculo.marca} - ${vehiculo.chasis}\n` +
                `Se enviará un correo al asesor para que acepte o rechace tu solicitud.`
            );
            
            console.log('👤 Usuario confirmó solicitud:', confirmar);
            
            if (!confirmar) {
                console.log('❌ Usuario canceló la solicitud');
                return;
            }
            
            console.log('✅ Enviando solicitud de reasignación...');
            
            // Enviar solicitud al servidor
            const formData = new FormData();
            formData.append('chasis', vehiculo.chasis);
            formData.append('marca', vehiculo.marca);
            formData.append('ubicacion', vehiculo.ubicacion);
            formData.append('asesor_dueno', vehiculo.vendedor);
            
            fetch('/digitalizacion-documentos/solicitud-vehiculo/solicitar-reasignacion', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('📬 Respuesta del servidor:', data);
                
                if (data.success) {
                    let mensaje = data.message;
                    if (data.email_dueno) {
                        mensaje += '\n\n📧 Correo enviado a: ' + data.email_dueno;
                    }
                    if (data.correo_enviado === false) {
                        mensaje += '\n\n⚠️ ADVERTENCIA: El correo NO se envió correctamente';
                    }
                    
                    alert('✅ ' + mensaje);
                    cerrarModalVehiculos();
                } else {
                    alert('❌ Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al enviar la solicitud');
            });
        }
        
        // Poblar filtros de marca y modelo
        function poblarFiltros() {
            const marcas = new Set();
            
            // Recopilar todas las marcas de todos los vehículos
            [...datosVehiculos.asignados, ...datosVehiculos.libres, ...datosVehiculos.otros].forEach(v => {
                if (v.marca) marcas.add(v.marca.toUpperCase());
            });
            
            // Obtener marcas del asesor desde PHP
            const marcasAsesor = '<?php echo $_SESSION["usuario_marcas"] ?? ""; ?>';
            const esMarcaMultiple = marcasAsesor.toUpperCase().trim() === 'MULTIMARCAS';
            
            // Convertir marcas del asesor en array
            let marcasPermitidas = [];
            if (!esMarcaMultiple && marcasAsesor) {
                marcasPermitidas = marcasAsesor.split(',').map(m => m.trim().toUpperCase());
            }
            
            console.log('🔍 Marcas del asesor:', marcasAsesor);
            console.log('🔍 Es MULTIMARCAS:', esMarcaMultiple);
            console.log('🔍 Marcas permitidas:', marcasPermitidas);
            
            // Poblar dropdown de marcas
            const filtroMarca = document.getElementById('filtroMarca');
            const marcaSeleccionada = filtroMarca.value; // Guardar selección actual
            
            // Solo mostrar "Todas las marcas" si es MULTIMARCAS
            if (esMarcaMultiple) {
                filtroMarca.innerHTML = '<option value="">🏷️ Todas las marcas</option>';
            } else {
                filtroMarca.innerHTML = '';
            }
            
            // Filtrar marcas según el asesor
            Array.from(marcas).sort().forEach(marca => {
                // Si es MULTIMARCAS, mostrar todas las marcas
                // Si no, solo mostrar las marcas permitidas
                if (esMarcaMultiple || marcasPermitidas.includes(marca)) {
                    const selected = marca === marcaSeleccionada ? 'selected' : '';
                    filtroMarca.innerHTML += `<option value="${marca}" ${selected}>${marca}</option>`;
                }
            });
            
            // Poblar modelos según la marca seleccionada
            actualizarFiltroModelos();
            
            console.log('✅ Filtros poblados con restricción de marcas');
        }
        
        // Actualizar dropdown de modelos según la marca seleccionada
        function actualizarFiltroModelos() {
            const marcaSeleccionada = document.getElementById('filtroMarca').value.toUpperCase();
            const filtroModelo = document.getElementById('filtroModelo');
            const modeloSeleccionado = filtroModelo.value; // Guardar selección actual
            
            const modelos = new Set();
            
            // Recopilar modelos según la marca seleccionada
            [...datosVehiculos.asignados, ...datosVehiculos.libres, ...datosVehiculos.otros].forEach(v => {
                // Si hay marca seleccionada, solo agregar modelos de esa marca
                if (marcaSeleccionada) {
                    if (v.marca && v.marca.toUpperCase() === marcaSeleccionada && v.modelo) {
                        modelos.add(v.modelo.toUpperCase());
                    }
                } else {
                    // Si no hay marca seleccionada, agregar todos los modelos
                    if (v.modelo) {
                        modelos.add(v.modelo.toUpperCase());
                    }
                }
            });
            
            // Poblar dropdown de modelos
            filtroModelo.innerHTML = '<option value="">🚙 Todos los modelos</option>';
            Array.from(modelos).sort().forEach(modelo => {
                if (modelo) {
                    const selected = modelo === modeloSeleccionado ? 'selected' : '';
                    filtroModelo.innerHTML += `<option value="${modelo}" ${selected}>${modelo}</option>`;
                }
            });
            
            console.log('✅ Modelos actualizados:', modelos.size, 'para marca:', marcaSeleccionada || 'TODAS');
        }
        
        // Actualizar dropdown de colores según marca y modelo seleccionados
        function actualizarFiltroColores() {
            const marcaSeleccionada = document.getElementById('filtroMarca').value.toUpperCase();
            const modeloSeleccionado = document.getElementById('filtroModelo').value.toUpperCase();
            const filtroColor = document.getElementById('filtroColor');
            const colorSeleccionado = filtroColor.value; // Guardar selección actual
            
            const colores = new Set();
            
            // Recopilar colores según marca y modelo seleccionados
            [...datosVehiculos.asignados, ...datosVehiculos.libres, ...datosVehiculos.otros].forEach(v => {
                let agregar = true;
                
                // Filtrar por marca si está seleccionada
                if (marcaSeleccionada && v.marca && v.marca.toUpperCase() !== marcaSeleccionada) {
                    agregar = false;
                }
                
                // Filtrar por modelo si está seleccionado
                if (modeloSeleccionado && v.modelo && v.modelo.toUpperCase() !== modeloSeleccionado) {
                    agregar = false;
                }
                
                // Agregar color si cumple los filtros
                if (agregar && v.color) {
                    colores.add(v.color.toUpperCase());
                }
            });
            
            // Poblar dropdown de colores
            filtroColor.innerHTML = '<option value="">🎨 Todos los colores</option>';
            Array.from(colores).sort().forEach(color => {
                if (color) {
                    const selected = color === colorSeleccionado ? 'selected' : '';
                    filtroColor.innerHTML += `<option value="${color}" ${selected}>${color}</option>`;
                }
            });
            
            console.log('✅ Colores actualizados:', colores.size, 'para marca:', marcaSeleccionada || 'TODAS', 'modelo:', modeloSeleccionado || 'TODOS');
        }
        
        // Filtrar vehículos en TODAS las tablas (las 3 pestañas)
        function filtrarVehiculos() {
            const buscador = document.getElementById('buscadorVehiculos').value.toUpperCase();
            const marcaFiltro = document.getElementById('filtroMarca').value.toUpperCase();
            const modeloFiltro = document.getElementById('filtroModelo').value.toUpperCase();
            const prioridadFiltro = document.getElementById('filtroPrioridad').value;
            const colorFiltro = document.getElementById('filtroColor').value.toUpperCase();
            
            // IDs de las 3 tablas y sus contadores
            const tablas = [
                { id: 'tablaAsignados', contador: 'countAsignados' },
                { id: 'tablaLibres', contador: 'countLibres' },
                { id: 'tablaOtros', contador: 'countOtros' }
            ];
            
            // Filtrar en las 3 tablas simultáneamente y contar visibles
            tablas.forEach(({ id, contador }) => {
                const tabla = document.getElementById(id);
                if (!tabla) return;
                
                const filas = tabla.getElementsByTagName('tr');
                let visibles = 0;
                
                for (let i = 1; i < filas.length; i++) {
                    const fila = filas[i];
                    
                    // Obtener datos de la fila desde los data attributes
                    const marca = fila.dataset.marca || '';
                    const modelo = fila.dataset.modelo || '';
                    const color = fila.dataset.color || '';
                    const prioridad = fila.dataset.prioridad || '0';
                    const textoFila = fila.textContent || fila.innerText;
                    
                    // Aplicar filtros (AND: deben cumplirse todos)
                    let mostrar = true;
                    
                    // Filtro de texto (busca en todo el texto)
                    if (buscador && textoFila.toUpperCase().indexOf(buscador) === -1) {
                        mostrar = false;
                    }
                    
                    // Filtro de marca
                    if (marcaFiltro && marca !== marcaFiltro) {
                        mostrar = false;
                    }
                    
                    // Filtro de modelo
                    if (modeloFiltro && modelo !== modeloFiltro) {
                        mostrar = false;
                    }
                    
                    // Filtro de prioridad
                    if (prioridadFiltro && prioridad !== prioridadFiltro) {
                        mostrar = false;
                    }
                    
                    // Filtro de color
                    if (colorFiltro && color !== colorFiltro) {
                        mostrar = false;
                    }
                    
                    fila.style.display = mostrar ? '' : 'none';
                    
                    // Contar filas visibles
                    if (mostrar) {
                        visibles++;
                    }
                }
                
                // Actualizar contador en el badge
                const spanContador = document.getElementById(contador);
                if (spanContador) {
                    spanContador.textContent = visibles;
                }
            });
            
            console.log('🔍 Filtros aplicados a las 3 pestañas:', {
                texto: buscador || 'ninguno',
                marca: marcaFiltro || 'todas',
                modelo: modeloFiltro || 'todos'
            });
        }
        
        // Cerrar modal al hacer clic fuera
        document.getElementById('modalVehiculosAsesor')?.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalVehiculos();
            }
        });
    </script>
</body>
