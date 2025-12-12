<?php
require_once __DIR__ . '/../models/Document.php';

class AuthController {
    private $documentModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->documentModel = new Document();
    }

    // Mostrar página de login
    public function showLogin() {
        // Si ya está logueado, redirigir al inicio
        if (isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true) {
            header('Location: /digitalizacion-documentos/');
            exit;
        }
        
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Procesar login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        $usuario = trim($_POST['usuario'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($usuario) || empty($password)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Usuario y contraseña son requeridos']);
            exit;
        }

        // Verificar credenciales en BD DOC_DIGITALES
        $db = new Database();
        $docDigitalesConn = $db->getDocDigitalesConnection();

        if (!$docDigitalesConn) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
            exit;
        }

        $sql = "SELECT usuario, password, firma_nombre, firma_apellido, firma_mail, firma_data, rol, marca, tienda, firma_cargo, firma_celular 
                FROM firmas 
                WHERE usuario = ? AND password = ?";
        
        $result = sqlsrv_query($docDigitalesConn, $sql, [$usuario, $password]);

        if (!$result) {
            sqlsrv_close($docDigitalesConn);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error en la consulta']);
            exit;
        }

        $user = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        
        // DEBUG: Ver qué datos trae la consulta
        // error_log("🔍 DEBUG LOGIN - Datos usuario: " . print_r($user, true));
        
        sqlsrv_close($docDigitalesConn);

        if ($user) {
            // Login exitoso - Guardar datos en sesión
            $_SESSION['usuario_logueado'] = true;
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['usuario_nombre'] = $user['firma_nombre'] ?? '';
            $_SESSION['usuario_apellido'] = $user['firma_apellido'] ?? '';
            
            // Construir nombre completo (NOMBRE + APELLIDO para mostrar en el panel)
            $nombreCompleto = trim(($user['firma_nombre'] ?? '') . ' ' . ($user['firma_apellido'] ?? ''));
            $_SESSION['usuario_nombre_completo'] = $nombreCompleto;
            
            // Log para verificar
            // error_log("✅ LOGIN - Nombre completo guardado: '{$nombreCompleto}'");
            // error_log("✅ LOGIN - Marca guardada: '" . ($user['marca'] ?? '') . "'");
            
            $_SESSION['usuario_email'] = $user['firma_mail'];
            $_SESSION['usuario_firma'] = $user['firma_data'];
            $_SESSION['usuario_rol'] = $user['rol']; // Guardar el rol del usuario
            $_SESSION['usuario_marcas'] = $user['marca'] ?? ''; // Guardar las marcas del usuario
            $_SESSION['usuario_tiendas'] = $user['tienda'] ?? ''; // Guardar las tiendas del usuario
            $_SESSION['usuario_cargo'] = $user['firma_cargo'] ?? ''; // Guardar el cargo del usuario
            $_SESSION['usuario_celular'] = $user['firma_celular'] ?? ''; // Guardar el celular del usuario

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'usuario' => $user['usuario'],
                'nombre_completo' => $_SESSION['usuario_nombre_completo']
            ]);
        } else {
            // Login fallido
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Usuario o contraseña incorrectos']);
        }
        exit;
    }

    // Cerrar sesión
    public function logout() {
        session_destroy();
        header('Location: /digitalizacion-documentos/auth/login');
        exit;
    }

    // Verificar si el usuario está logueado
    public static function verificarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
            header('Location: /digitalizacion-documentos/auth/login');
            exit;
        }
    }

    // Mostrar página de recuperación de contraseña
    public function showForgotPassword() {
        require_once __DIR__ . '/../views/auth/forgot-password.php';
    }

    // Procesar solicitud de recuperación de contraseña
    public function requestReset() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Email inválido']);
            exit;
        }

        // Buscar usuario por email en BD DOC_DIGITALES
        $db = new Database();
        $docDigitalesConn = $db->getDocDigitalesConnection();

        if (!$docDigitalesConn) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
            exit;
        }

        $sql = "SELECT usuario, firma_nombre, firma_apellido, firma_mail 
                FROM firmas 
                WHERE firma_mail = ?";
        
        $result = sqlsrv_query($docDigitalesConn, $sql, [$email]);

        if (!$result) {
            sqlsrv_close($docDigitalesConn);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error en la consulta']);
            exit;
        }

        $user = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

        if (!$user) {
            sqlsrv_close($docDigitalesConn);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No existe una cuenta con ese email']);
            exit;
        }

        // Generar token único
        $token = bin2hex(random_bytes(32)); // 64 caracteres
        
        // Crear objeto DateTime para SQL Server
        $expiraDateTime = new DateTime('+1 hour');
        $expiraFormatted = $expiraDateTime->format('Y-m-d H:i:s');

        error_log("=== INTENTANDO ACTUALIZAR TOKEN ===");
        error_log("Email: " . $email);
        error_log("Token: " . $token);
        error_log("Expira: " . $expiraFormatted);

        // Guardar token en la base de datos usando CONVERT para el datetime
        $sqlUpdate = "UPDATE firmas 
                      SET reset_token = ?, 
                          reset_token_expira = CONVERT(DATETIME, ?, 120)
                      WHERE firma_mail = ?";
        
        $params = [
            $token,
            $expiraFormatted,
            $email
        ];
        
        $resultUpdate = sqlsrv_query($docDigitalesConn, $sqlUpdate, $params);
        
        error_log("Resultado UPDATE: " . ($resultUpdate ? "EXITOSO" : "FALLIDO"));

        if (!$resultUpdate) {
            $errors = sqlsrv_errors();
            error_log("Error al actualizar token: " . print_r($errors, true));
            sqlsrv_close($docDigitalesConn);
            header('Content-Type: application/json');
            
            // Mostrar error detallado para debugging
            $errorMsg = 'Error desconocido';
            $errorCode = '';
            if ($errors && isset($errors[0])) {
                $errorMsg = $errors[0]['message'] ?? 'Error desconocido';
                $errorCode = $errors[0]['code'] ?? '';
            }
            
            echo json_encode([
                'success' => false, 
                'error' => 'Error al generar token: ' . $errorMsg,
                'sql_error' => $errorMsg,
                'sql_code' => $errorCode,
                'debug' => [
                    'email' => $email,
                    'token_length' => strlen($token),
                    'expira' => $expiraFormatted
                ]
            ]);
            exit;
        }

        sqlsrv_close($docDigitalesConn);

        // Enviar correo con el token
        $nombreCompleto = trim($user['firma_nombre'] . ' ' . $user['firma_apellido']);
        $usuario = $user['usuario'];
        $resetLink = "http://190.238.78.104:3800/digitalizacion-documentos/auth/show-reset?token=" . $token;

        $resultado = $this->enviarCorreoRecuperacion($email, $nombreCompleto, $usuario, $resetLink);

        if ($resultado) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Correo enviado exitosamente. Revisa tu bandeja de entrada.'
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al enviar el correo. Intenta nuevamente.'
            ]);
        }
        exit;
    }

    // Enviar correo de recuperación
    private function enviarCorreoRecuperacion($emailDestino, $nombreCompleto, $usuario, $resetLink) {
        try {
            $emailData = [
                'to' => $emailDestino,
                'subject' => '🔑 Recuperación de Acceso - Sistema de Digitalización',
                'html' => "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                        <div style='background: #1e3a8a; color: white; padding: 20px; text-align: center;'>
                            <h1 style='margin: 0;'>🔑 Recuperación de Acceso</h1>
                            <p style='margin: 10px 0 0 0;'>Sistema de Digitalización</p>
                        </div>
                        
                        <div style='padding: 30px; background: #f8f9fa;'>
                            <p style='font-size: 16px; color: #333;'>Hola <strong>{$nombreCompleto}</strong>,</p>
                            
                            <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                                Recibimos una solicitud para recuperar tu acceso al sistema.
                            </p>
                            
                            <div style='background: #e0f2fe; border-left: 4px solid #0284c7; padding: 15px; margin: 20px 0;'>
                                <p style='margin: 0; font-size: 14px; color: #0c4a6e;'>
                                    <strong>Tu usuario es:</strong> {$usuario}
                                </p>
                            </div>
                            
                            <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                                Para crear una nueva contraseña, haz clic en el siguiente botón:
                            </p>
                            
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='{$resetLink}' 
                                   style='display: inline-block; padding: 15px 30px; background: #1e3a8a; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;'>
                                    Restablecer Contraseña
                                </a>
                            </div>
                            
                            <p style='font-size: 12px; color: #999; line-height: 1.6;'>
                                Si no puedes hacer clic en el botón, copia y pega este enlace en tu navegador:<br>
                                <a href='{$resetLink}' style='color: #0284c7; word-break: break-all;'>{$resetLink}</a>
                            </p>
                            
                            <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;'>
                                <p style='margin: 0; font-size: 13px; color: #856404;'>
                                    ⚠️ <strong>Importante:</strong> Este enlace expirará en 1 hora por seguridad.
                                </p>
                            </div>
                            
                            <p style='font-size: 13px; color: #999; line-height: 1.6;'>
                                Si no solicitaste este cambio, ignora este correo. Tu contraseña actual seguirá siendo válida.
                            </p>
                        </div>
                        
                        <div style='background: #333; color: white; padding: 20px; text-align: center; font-size: 12px;'>
                            <p style='margin: 0;'>© 2025 Interamericana Norte. Todos los derechos reservados.</p>
                        </div>
                    </div>
                ",
                'from' => 'comunica@interamericana.shop',
                'from_name' => 'Sistema de Digitalización Interamericana'
            ];

            // Enviar correo usando la misma API que usas para las cajeras
            error_log("=== ENVIANDO CORREO DE RECUPERACIÓN ===");
            error_log("Destino: " . $emailDestino);
            error_log("URL API: http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php");
            
            $ch = curl_init('http://190.238.78.104:3800/robot-sdg-ford/api/pv/mail/mail-generico.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            error_log("HTTP Code: " . $httpCode);
            error_log("Response: " . $response);
            if ($curlError) {
                error_log("cURL Error: " . $curlError);
            }

            return ($httpCode >= 200 && $httpCode < 300);

        } catch (Exception $e) {
            error_log("Error al enviar correo de recuperación: " . $e->getMessage());
            return false;
        }
    }

    // Mostrar página de reseteo de contraseña
    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            header('Location: /digitalizacion-documentos/auth/login');
            exit;
        }

        // Verificar que el token sea válido y no haya expirado
        $db = new Database();
        $docDigitalesConn = $db->getDocDigitalesConnection();

        if (!$docDigitalesConn) {
            die('Error de conexión a la base de datos');
        }

        $sql = "SELECT usuario, reset_token_expira 
                FROM firmas 
                WHERE reset_token = ?";
        
        $result = sqlsrv_query($docDigitalesConn, $sql, [$token]);

        if (!$result) {
            sqlsrv_close($docDigitalesConn);
            die('Error en la consulta');
        }

        $user = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        sqlsrv_close($docDigitalesConn);

        if (!$user) {
            die('Token inválido o expirado. <a href="/digitalizacion-documentos/auth/forgot-password">Solicitar nuevo enlace</a>');
        }

        // Verificar si el token ha expirado
        $expira = $user['reset_token_expira'];
        if ($expira instanceof DateTime) {
            $expira = $expira->format('Y-m-d H:i:s');
        }
        
        if (strtotime($expira) < time()) {
            die('El enlace ha expirado. <a href="/digitalizacion-documentos/auth/forgot-password">Solicitar nuevo enlace</a>');
        }

        // Token válido, mostrar formulario
        require_once __DIR__ . '/../views/auth/reset-password.php';
    }

    // Procesar reseteo de contraseña
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        $token = trim($_POST['token'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($token) || empty($password)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            exit;
        }

        if (strlen($password) < 6) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            exit;
        }

        // Verificar token en BD DOC_DIGITALES
        $db = new Database();
        $docDigitalesConn = $db->getDocDigitalesConnection();

        if (!$docDigitalesConn) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
            exit;
        }

        $sql = "SELECT usuario, reset_token_expira 
                FROM firmas 
                WHERE reset_token = ?";
        
        $result = sqlsrv_query($docDigitalesConn, $sql, [$token]);

        if (!$result) {
            sqlsrv_close($docDigitalesConn);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error en la consulta']);
            exit;
        }

        $user = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

        if (!$user) {
            sqlsrv_close($docDigitalesConn);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Token inválido']);
            exit;
        }

        // Verificar si el token ha expirado
        $expira = $user['reset_token_expira'];
        if ($expira instanceof DateTime) {
            $expira = $expira->format('Y-m-d H:i:s');
        }
        
        if (strtotime($expira) < time()) {
            sqlsrv_close($docDigitalesConn);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'El enlace ha expirado']);
            exit;
        }

        // Actualizar contraseña y limpiar token
        $sqlUpdate = "UPDATE firmas 
                      SET password = ?, reset_token = NULL, reset_token_expira = NULL 
                      WHERE reset_token = ?";
        
        $resultUpdate = sqlsrv_query($docDigitalesConn, $sqlUpdate, [$password, $token]);

        if (!$resultUpdate) {
            sqlsrv_close($docDigitalesConn);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error al actualizar la contraseña']);
            exit;
        }

        sqlsrv_close($docDigitalesConn);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);
        exit;
    }
}
