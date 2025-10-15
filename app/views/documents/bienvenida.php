<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenido - Sistema de Documentos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .welcome-container {
      background: white;
      border-radius: 20px;
      padding: 60px 40px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      text-align: center;
      max-width: 600px;
      width: 90%;
    }
    .welcome-icon {
      font-size: 80px;
      color: #667eea;
      margin-bottom: 20px;
    }
    .welcome-title {
      font-size: 2.5rem;
      font-weight: 700;
      color: #1e3a8a;
      margin-bottom: 15px;
    }
    .welcome-subtitle {
      font-size: 1.2rem;
      color: #6b7280;
      margin-bottom: 40px;
    }
    .user-info {
      background: #f3f4f6;
      padding: 15px 25px;
      border-radius: 10px;
      margin-bottom: 30px;
      display: inline-block;
    }
    .user-info strong {
      color: #1e3a8a;
      font-size: 1.1rem;
    }
    .btn-logout {
      position: absolute;
      top: 20px;
      right: 20px;
      background: #ef4444;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .btn-logout:hover {
      background: #dc2626;
      transform: translateY(-2px);
    }
    .btn-generar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      padding: 18px 50px;
      font-size: 1.3rem;
      font-weight: 600;
      border-radius: 50px;
      color: white;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }
    .btn-generar:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
      background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
    .btn-expedientes {
      margin-top: 20px;
      background: white;
      border: 2px solid #667eea;
      color: #667eea;
      padding: 12px 30px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 50px;
      transition: all 0.3s ease;
    }
    .btn-expedientes:hover {
      background: #667eea;
      color: white;
      transform: translateY(-2px);
    }
    .user-badge {
      display: inline-block;
      background: #f3f4f6;
      padding: 10px 25px;
      border-radius: 50px;
      margin-bottom: 30px;
      font-size: 1.1rem;
      color: #374151;
    }
    .user-badge i {
      color: #667eea;
      margin-right: 8px;
    }
  </style>
</head>
<body>
  <button class="btn-logout" onclick="window.location.href='/digitalizacion-documentos/auth/logout'">
    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
  </button>

  <div class="welcome-container">
    <div class="welcome-icon">
      <i class="bi bi-person-circle"></i>
    </div>
    <h1 class="welcome-title">¡Bienvenido!</h1>
    
    <?php if (isset($_SESSION['usuario_nombre_completo'])): ?>
    <div class="user-info">
      <strong><?= htmlspecialchars($_SESSION['usuario_nombre_completo']) ?></strong>
    </div>
    <?php endif; ?>
    
    <p class="welcome-subtitle">Sistema de Digitalización de Documentos</p>
    </p>
    
    <div class="d-grid gap-3">
      <a href="#" onclick="generarNuevaOrden(event)" class="btn btn-generar">
        <i class="bi bi-file-earmark-plus"></i> Generar Orden de Compra
      </a>
      
      <a href="/digitalizacion-documentos/expedientes" class="btn btn-expedientes">
        <i class="bi bi-folder2-open"></i> Gestionar Expedientes
      </a>
    </div>
    
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success mt-4" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_GET['success']) ?>
      </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger mt-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function generarNuevaOrden(event) {
      event.preventDefault();
      // Limpiar sesión antes de generar nueva orden
      fetch('/digitalizacion-documentos/documents/limpiar-sesion', {
        method: 'POST'
      })
      .then(() => {
        // Redirigir a la orden de compra con formulario limpio
        window.location.href = '/digitalizacion-documentos/documents/show?id=orden-compra';
      })
      .catch(error => {
        console.error('Error al limpiar sesión:', error);
        // Redirigir de todas formas
        window.location.href = '/digitalizacion-documentos/documents/show?id=orden-compra';
      });
    }
  </script>
</body>
</html>
