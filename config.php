<?php
// Configuración general del sistema
define('DB_HOST', 'localhost');
define('DB_NAME', 'clinica_dental');
define('DB_USER', 'root');
define('DB_PASS', '');
define('SITE_URL', 'http://localhost:8000');
define('SITE_NAME', 'Clínica Dental Dentesia');

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de idioma
setlocale(LC_ALL, 'es_ES.UTF-8');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Configuración de errores en desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Rutas del sistema
define('ROOT_PATH', __DIR__);
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers/');
define('MODELS_PATH', ROOT_PATH . '/models/');
define('VIEWS_PATH', ROOT_PATH . '/views/');
define('INCLUDES_PATH', ROOT_PATH . '/includes/');
define('ASSETS_PATH', ROOT_PATH . '/assets/');
define('UPLOADS_PATH', ROOT_PATH . '/uploads/');

// Función para manejar errores personalizados
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error = date('[Y-m-d H:i:s]') . " Error ($errno): $errstr en $errfile:$errline\n";
    error_log($error, 3, __DIR__ . '/logs/error.log');
    
    if (ini_get('display_errors')) {
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px;'>";
        echo "Ha ocurrido un error. Por favor, contacte al administrador.";
        echo "</div>";
    }
    
    return true;
}

// Establecer el manejador de errores personalizado
set_error_handler('customErrorHandler');

// Función para cargar clases automáticamente
spl_autoload_register(function ($class_name) {
    $possible_paths = [
        MODELS_PATH . $class_name . '.php',
        CONTROLLERS_PATH . $class_name . '.php'
    ];
    
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Funciones auxiliares globales
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

function redirect($path) {
    header("Location: " . SITE_URL . $path);
    exit();
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
