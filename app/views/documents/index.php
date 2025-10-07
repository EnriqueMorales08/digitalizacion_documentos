<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Documentos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
    }
    .header {
      text-align: center;
      margin-bottom: 40px;
    }
    .header h1 {
      font-weight: 700;
      color: #1e3a8a;
    }
    .header p {
      font-size: 18px;
      color: #374151;
    }
    .card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0px 6px 18px rgba(0,0,0,0.15);
    }
    .card-title {
      font-weight: 600;
      color: #111827;
    }
  </style>
</head>
<body>
  <div class="container py-5">

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>✅ ¡Éxito!</strong>
        <?php
        switch ($_GET['success']) {
          case 'orden_compra':
            echo 'La orden de compra ha sido guardada exitosamente.';
            if (isset($_GET['documento_id'])) {
              echo ' ID del documento: <strong>' . htmlspecialchars($_GET['documento_id']) . '</strong>';
            }
            break;
          case 'acta_conformidad':
            echo 'El acta de conocimiento y conformidad ha sido guardada exitosamente.';
            break;
          default:
            echo 'El documento ha sido procesado exitosamente.';
        }
        ?>
        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Error:</strong>
            <?php
            switch ($_GET['error']) {
              case 'no_orden':
                echo 'Primero debe guardar la orden de compra antes de acceder a otros documentos.';
                break;
              default:
                echo htmlspecialchars($_GET['error']);
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        <?php
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>❌ Error:</strong> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>


    <!-- Header con Bienvenida -->
    <div class="header">
       <?php $user = $_GET['user'] ?? 'Usuario'; ?>
       <h1>📂 Panel de Documentos</h1>
       <p>Bienvenido, <strong><?= htmlspecialchars($user) ?></strong></p>
       <?php if (!$orden_guardada): ?>
           <div class="alert alert-warning mt-3">
               <strong>Primero debe llenar y guardar la Orden de Compra.</strong> Los demás documentos estarán disponibles después.
           </div>
       <?php elseif (isset($forma_pago)): ?>
           <div class="alert alert-info mt-3">
               <strong>Orden de Compra guardada.</strong>
               <?php if ($forma_pago === 'CRÉDITO'): ?>
                   Se requiere llenar la <strong>Carta de Características</strong> para compras a crédito.
               <?php elseif ($forma_pago === 'CONTADO'): ?>
                   Para compras al contado, se genera la Factura (no disponible en este panel).
               <?php endif; ?>
           </div>
       <?php endif; ?>
    </div>

    <!-- Grid de Tarjetas -->
    <div class="row">
      <?php foreach ($documents as $doc): ?>
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center d-flex flex-column justify-content-between">
              <div>
                <div style="font-size:40px; color:#1e3a8a;">📄</div>
                <h5 class="card-title mt-3">
                  <?= $doc['title'] ?>
                </h5>
              </div>
              <a href="/digitalizacion-documentos/documents/show?id=<?= $doc['id'] ?>" class="btn btn-primary mt-3">Ver Documento</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
