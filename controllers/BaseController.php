<?php
class BaseController {
    protected $db;
    protected $user;
    protected $view;

    public function __construct() {
        // Verificar autenticación
        if (!isAuthenticated() && !$this->isPublicAction()) {
            redirect('/login.php');
        }

        // Inicializar conexión a base de datos
        try {
            $this->db = Database::getInstance();
        } catch (Exception $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            die("Error de conexión a la base de datos");
        }

        // Cargar información del usuario actual si está autenticado
        if (isAuthenticated()) {
            $this->loadCurrentUser();
        }
    }

    protected function loadCurrentUser() {
        try {
            $stmt = $this->db->query(
                "SELECT id, nombre, apellidos, email, rol, imagen_perfil, estado 
                 FROM usuarios 
                 WHERE id = ?",
                [$_SESSION['user_id']]
            );
            $this->user = $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error al cargar usuario: " . $e->getMessage());
            $this->user = null;
        }
    }

    protected function isPublicAction() {
        // Lista de acciones públicas que no requieren autenticación
        $publicActions = ['login', 'register', 'forgotPassword'];
        
        // Obtener la acción actual
        $action = $_GET['action'] ?? 'index';
        
        return in_array($action, $publicActions);
    }

    protected function render($view, $data = []) {
        // Extraer los datos para que estén disponibles en la vista
        extract($data);
        
        // Incluir la vista
        $viewPath = VIEWS_PATH . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("Vista no encontrada: $viewPath");
        }
        
        require $viewPath;
    }

    protected function validateCSRFToken() {
        $token = $_POST['csrf_token'] ?? '';
        if (!validateCSRFToken($token)) {
            throw new Exception("Token CSRF inválido");
        }
    }

    protected function checkPermission($requiredRole) {
        if (!hasRole($requiredRole)) {
            $this->renderError(403, "No tiene permisos para acceder a esta sección");
            exit();
        }
    }

    protected function renderError($code, $message) {
        http_response_code($code);
        $this->render('error', [
            'code' => $code,
            'message' => $message
        ]);
    }

    protected function jsonResponse($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function validateRequiredParams($params, $source = 'POST') {
        $missing = [];
        $data = $source === 'POST' ? $_POST : $_GET;
        
        foreach ($params as $param) {
            if (!isset($data[$param]) || trim($data[$param]) === '') {
                $missing[] = $param;
            }
        }
        
        if (!empty($missing)) {
            throw new Exception("Parámetros requeridos faltantes: " . implode(', ', $missing));
        }
    }

    protected function sanitizeArray($array) {
        return array_map(function($item) {
            return is_array($item) ? $this->sanitizeArray($item) : sanitizeInput($item);
        }, $array);
    }

    protected function handleFileUpload($file, $allowedTypes = ['image/jpeg', 'image/png'], $maxSize = 5242880) {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception('Parámetros de archivo inválidos');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No se seleccionó ningún archivo');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('El archivo excede el tamaño permitido');
            default:
                throw new Exception('Error desconocido al subir el archivo');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('El archivo es demasiado grande');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido');
        }

        $extension = array_search($mimeType, [
            'jpg' => 'image/jpeg',
            'png' => 'image/png'
        ], true);

        if ($extension === false) {
            throw new Exception('Extensión de archivo no válida');
        }

        $fileName = sprintf(
            '%s.%s',
            sha1_file($file['tmp_name']),
            $extension
        );

        $uploadPath = UPLOADS_PATH . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Error al guardar el archivo');
        }

        return $fileName;
    }

    protected function generatePDF($html, $filename = 'documento.pdf') {
        require_once ROOT_PATH . '/vendor/autoload.php';
        
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9
        ]);
        
        $mpdf->SetTitle($filename);
        $mpdf->WriteHTML($html);
        
        $mpdf->Output($filename, 'D');
    }

    protected function sendEmail($to, $subject, $body) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: ' . SITE_NAME . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>',
            'Reply-To: noreply@' . $_SERVER['HTTP_HOST'],
            'X-Mailer: PHP/' . phpversion()
        ];

        return mail($to, $subject, $body, implode("\r\n", $headers));
    }

    protected function log($message, $type = 'info') {
        $logMessage = sprintf(
            "[%s] [%s] [User: %s] %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($type),
            $_SESSION['user_id'] ?? 'guest',
            $message
        );
        
        error_log($logMessage, 3, ROOT_PATH . '/logs/app.log');
    }
}
