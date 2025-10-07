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
            body {
                background: #fff;
            }

            .page {
                box-shadow: none;
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
            width: 200px;
            font-size: 12pt;
        }

        .form-group input:focus {
            outline: none;
            border-bottom: 2px solid #2c5aa0;
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
            <?php if (!empty($ordenCompraData['OC_CLIENTE_FIRMA'])): ?>
            <img src="<?php echo htmlspecialchars($ordenCompraData['OC_CLIENTE_FIRMA']); ?>" style="max-width:280px; max-height:50px; display:block; margin:0 auto 5px auto;">
            <?php else: ?>
            <input type="text" name="ADP_FIRMA_CLIENTE" value="Firma" style="border: none; text-align: center; font-weight: bold; width: 280px; margin-bottom: 5px;">
            <?php endif; ?>
            <div class="signature-line"></div>
            <div class="signature-label">FIRMA DEL TITULAR</div>

            <div class="form-fields">
                <div class="form-group">
                    <label for="nombre_autorizacion">NOMBRE:</label>
                    <input type="text" id="nombre_autorizacion" name="ADP_NOMBRE_AUTORIZACION" value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NOMBRE'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="dni_autorizacion">D.N.I./C.E.:</label>
                    <input type="text" id="dni_autorizacion" name="ADP_DNI_AUTORIZACION" value="<?php echo htmlspecialchars($ordenCompraData['OC_COMPRADOR_NUMERO_DOCUMENTO'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="fecha_autorizacion">FECHA:</label>
                    <input type="date" id="fecha_autorizacion" name="ADP_FECHA_AUTORIZACION" value="<?php echo date('Y-m-d'); ?>">
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
    });
  </script>
</body>
</html>
