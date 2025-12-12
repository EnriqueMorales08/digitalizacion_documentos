<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Sistema de Digitalización</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .header {
            background: #1e3a8a;
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .body {
            padding: 40px 30px;
        }

        .info-box {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
        }

        .info-box p {
            font-size: 14px;
            color: #0c4a6e;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: #1e3a8a;
            color: white;
        }

        .btn-primary:hover {
            background: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.3);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .alert.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1e3a8a;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Nueva Contraseña</h1>
            <p>Sistema de Digitalización</p>
        </div>

        <div class="body">
            <div class="info-box">
                <p><strong>✓ Token válido</strong><br>
                Ahora puedes crear una nueva contraseña para tu cuenta.</p>
            </div>

            <div id="alert" class="alert"></div>

            <form id="resetForm">
                <input type="hidden" id="token" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                
                <div class="form-group">
                    <label for="password">Nueva Contraseña</label>
                    <input type="password" id="password" name="password" required autofocus>
                    <div class="password-requirements">Mínimo 6 caracteres</div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>

                <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
                <a href="/digitalizacion-documentos/auth/login" class="btn btn-secondary" style="display: block; text-align: center; text-decoration: none;">Volver al Login</a>
            </form>

            <div class="loading">
                <div class="loading-spinner"></div>
                <p style="margin-top: 10px; color: #666;">Actualizando contraseña...</p>
            </div>
        </div>

        <div class="footer">
            &copy; 2025 Interamericana Norte. Todos los derechos reservados.
        </div>
    </div>

    <script>
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            const token = document.getElementById('token').value;
            const alert = document.getElementById('alert');
            const form = document.getElementById('resetForm');
            const loading = document.querySelector('.loading');

            // Validar que las contraseñas coincidan
            if (password !== passwordConfirm) {
                alert.className = 'alert error';
                alert.style.display = 'block';
                alert.textContent = '✗ Las contraseñas no coinciden';
                return;
            }

            // Validar longitud mínima
            if (password.length < 6) {
                alert.className = 'alert error';
                alert.style.display = 'block';
                alert.textContent = '✗ La contraseña debe tener al menos 6 caracteres';
                return;
            }

            // Mostrar loading
            form.style.display = 'none';
            loading.style.display = 'block';
            alert.style.display = 'none';

            // Enviar solicitud
            const formData = new FormData();
            formData.append('token', token);
            formData.append('password', password);

            fetch('/digitalizacion-documentos/auth/reset-password', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';

                if (data.success) {
                    alert.className = 'alert success';
                    alert.style.display = 'block';
                    alert.innerHTML = '✓ ' + data.message + '<br><br>Redirigiendo al login...';
                    
                    // Redirigir al login después de 2 segundos
                    setTimeout(() => {
                        window.location.href = '/digitalizacion-documentos/auth/login';
                    }, 2000);
                } else {
                    form.style.display = 'block';
                    alert.className = 'alert error';
                    alert.style.display = 'block';
                    alert.textContent = '✗ ' + (data.error || 'Error al restablecer la contraseña');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loading.style.display = 'none';
                form.style.display = 'block';
                alert.className = 'alert error';
                alert.style.display = 'block';
                alert.textContent = '✗ Error de conexión. Por favor intente nuevamente.';
            });
        });
    </script>
</body>
</html>
