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
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #06b6d4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            backdrop-filter: blur(10px);
        }

        .login-header {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.9) 0%, rgba(37, 99, 235, 0.85) 100%);
            color: white;
            padding: 35px 30px 30px;
            text-align: center;
            position: relative;
        }

        .car-icon {
            font-size: 42px;
            margin-bottom: 12px;
            display: inline-block;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .login-header p {
            font-size: 15px;
            opacity: 0.95;
            font-weight: 400;
        }

        .login-body {
            padding: 35px 35px 30px;
            background: white;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
            color: #1f2937;
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
            font-weight: 500;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }

        .alert.success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .loading-spinner {
            border: 4px solid #e5e7eb;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .forgot-password {
            text-align: center;
            margin-top: 18px;
        }

        .forgot-password a {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="car-icon">🚗</div>
            <h1>Sistema de<br>Digitalización</h1>
            <p>Interamericana Norte</p>
        </div>

        <div class="login-body">
            <div id="alert" class="alert"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Ingresa tu usuario" required autofocus maxlength="5">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                </div>

                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>

            <div class="forgot-password">
                <a href="/digitalizacion-documentos/auth/forgot-password">¿Olvidaste tu usuario o contraseña?</a>
            </div>

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
        // Contador de intentos fallidos
        let intentosFallidos = 0;
        
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
                    // Resetear contador en caso de éxito
                    intentosFallidos = 0;
                    
                    alert.className = 'alert success';
                    alert.style.display = 'block';
                    alert.textContent = '✓ Login exitoso. Redirigiendo...';
                    
                    setTimeout(() => {
                        window.location.href = '/digitalizacion-documentos/';
                    }, 1000);
                } else {
                    // Incrementar contador de intentos fallidos
                    intentosFallidos++;
                    
                    alert.className = 'alert error';
                    alert.style.display = 'block';
                    
                    // Mostrar mensaje especial después de 5 intentos
                    if (intentosFallidos >= 5) {
                        alert.textContent = '✗ Sus credenciales son incorrectas. Contáctese con el Área de Sistemas.';
                    } else {
                        alert.textContent = '✗ ' + (data.error || 'Usuario o contraseña incorrectos');
                    }
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
