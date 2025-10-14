<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Digitalización</title>
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

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }

        .login-header {
            background: #1e3a8a;
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .login-body {
            padding: 40px 30px;
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

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
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

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🚗 Sistema de Digitalización</h1>
            <p>Interamericana Norte</p>
        </div>

        <div class="login-body">
            <div id="alert" class="alert"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>

            <div class="loading">
                <div class="loading-spinner"></div>
                <p style="margin-top: 10px; color: #666;">Verificando credenciales...</p>
            </div>
        </div>

        <div class="footer">
            &copy; 2025 Interamericana Norte. Todos los derechos reservados.
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const usuario = document.getElementById('usuario').value;
            const password = document.getElementById('password').value;
            const alert = document.getElementById('alert');
            const form = document.getElementById('loginForm');
            const loading = document.querySelector('.loading');

            // Mostrar loading
            form.style.display = 'none';
            loading.style.display = 'block';
            alert.style.display = 'none';

            // Enviar solicitud de login
            const formData = new FormData();
            formData.append('usuario', usuario);
            formData.append('password', password);

            fetch('/digitalizacion-documentos/auth/login', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                form.style.display = 'block';

                if (data.success) {
                    alert.className = 'alert success';
                    alert.style.display = 'block';
                    alert.textContent = '✓ Login exitoso. Redirigiendo...';
                    
                    setTimeout(() => {
                        window.location.href = '/digitalizacion-documentos/';
                    }, 1000);
                } else {
                    alert.className = 'alert error';
                    alert.style.display = 'block';
                    alert.textContent = '✗ ' + (data.error || 'Usuario o contraseña incorrectos');
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
