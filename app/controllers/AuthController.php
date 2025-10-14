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

        $sql = "SELECT usuario, password, firma_nombre, firma_apellido, firma_mail, firma_data 
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
        sqlsrv_close($docDigitalesConn);

        if ($user) {
            // Login exitoso - Guardar datos en sesión
            $_SESSION['usuario_logueado'] = true;
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['usuario_nombre'] = $user['firma_nombre'];
            $_SESSION['usuario_apellido'] = $user['firma_apellido'];
            $_SESSION['usuario_nombre_completo'] = trim($user['firma_nombre'] . ' ' . $user['firma_apellido']);
            $_SESSION['usuario_email'] = $user['firma_mail'];
            $_SESSION['usuario_firma'] = $user['firma_data'];

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
}
