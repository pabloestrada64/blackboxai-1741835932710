<?php
require_once 'config.php';
require_once 'includes/db.php';

// Verificar si el usuario está autenticado
if (!isAuthenticated() && !in_array($_GET['page'] ?? '', ['login', 'logout'])) {
    redirect('/login.php');
}

// Manejar la solicitud
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Mapeo de páginas a controladores
$controllers = [
    'dashboard' => 'DashboardController',
    'users' => 'UsersController',
    'patients' => 'PatientsController',
    'diagnostics' => 'DiagnosticsController',
    'appointments' => 'AppointmentsController',
    'treatments' => 'TreatmentsController',
    'reports' => 'ReportsController',
    'configuration' => 'ConfigurationController'
];

// Verificar si la página solicitada existe
if (!array_key_exists($page, $controllers)) {
    header("HTTP/1.0 404 Not Found");
    require_once VIEWS_PATH . '404.php';
    exit();
}

// Cargar el controlador correspondiente
$controllerName = $controllers[$page];
$controllerPath = CONTROLLERS_PATH . $controllerName . '.php';

if (!file_exists($controllerPath)) {
    error_log("Controlador no encontrado: $controllerPath");
    header("HTTP/1.0 500 Internal Server Error");
    require_once VIEWS_PATH . '500.php';
    exit();
}

require_once $controllerPath;
$controller = new $controllerName();

// Verificar si el método existe
if (!method_exists($controller, $action)) {
    header("HTTP/1.0 404 Not Found");
    require_once VIEWS_PATH . '404.php';
    exit();
}

try {
    // Iniciar el buffer de salida
    ob_start();
    
    // Incluir el header
    require_once INCLUDES_PATH . 'header.php';
    
    // Ejecutar la acción del controlador
    $controller->$action();
    
    // Incluir el footer
    require_once INCLUDES_PATH . 'footer.php';
    
    // Enviar el contenido al navegador
    ob_end_flush();
} catch (Exception $e) {
    // Registrar el error
    error_log($e->getMessage());
    
    // Limpiar cualquier salida anterior
    ob_end_clean();
    
    // Mostrar página de error
    header("HTTP/1.0 500 Internal Server Error");
    require_once VIEWS_PATH . '500.php';
}
