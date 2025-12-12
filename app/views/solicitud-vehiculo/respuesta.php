<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            max-width: 600px;
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            background: <?php echo $color; ?>;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid <?php echo $color; ?>;
            margin-bottom: 20px;
        }
        
        .info-box p {
            margin: 8px 0;
            color: #555;
        }
        
        .info-box strong {
            color: #333;
        }
        
        .icon {
            font-size: 80px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo $titulo; ?></h1>
        </div>
        
        <div class="content">
            <div class="icon">
                <?php echo ($tipo === 'aceptado') ? '✅' : '❌'; ?>
            </div>
            
            <div class="message">
                <?php echo $mensaje; ?>
            </div>
            
            <div class="info-box">
                <p><strong>📋 Chasis:</strong> <?php echo htmlspecialchars($chasis); ?></p>
                <p><strong>🚙 Marca:</strong> <?php echo htmlspecialchars($marca); ?></p>
                <p><strong>👤 Solicitante:</strong> <?php echo htmlspecialchars($solicitante); ?></p>
            </div>
            
            <div style="text-align: center; color: #666; font-size: 14px;">
                <?php if ($tipo === 'aceptado'): ?>
                    <p>El asesor solicitante ha sido notificado de tu decisión.</p>
                    <p style="margin-top: 10px;"><em>Nota: La reasignación en la base de datos se realizará próximamente.</em></p>
                <?php else: ?>
                    <p>El asesor solicitante ha sido notificado de tu decisión.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer">
            <p>Sistema de Digitalización Interamericana</p>
            <p style="margin-top: 5px; font-size: 12px;">Este proceso fue registrado automáticamente</p>
        </div>
    </div>
</body>
</html>
