<?php
require_once 'config.php';
require_once 'includes/db.php';

// Si el usuario ya está autenticado, redirigir al dashboard
if (isAuthenticated()) {
    redirect('/index.php?page=dashboard');
}

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $error = '';

    try {
        $db = Database::getInstance();
        $stmt = $db->query(
            "SELECT id, nombre, apellidos, email, password, rol, imagen_perfil, estado 
             FROM usuarios 
             WHERE email = ?",
            [$email]
        );
        
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['estado']) {
                $error = 'Su cuenta está desactivada. Por favor, contacte al administrador.';
            } else {
                // Iniciar sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellidos'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['rol'];
                $_SESSION['user_image'] = $user['imagen_perfil'];

                // Generar nuevo token CSRF
                generateCSRFToken();

                // Redirigir al dashboard
                redirect('/index.php?page=dashboard');
            }
        } else {
            $error = 'Credenciales inválidas. Por favor, intente nuevamente.';
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        $error = 'Ha ocurrido un error. Por favor, intente nuevamente más tarde.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo SITE_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <!-- Logo y título -->
        <div class="text-center mb-8">
            <img src="<?php echo ASSETS_PATH; ?>/images/logo.png" alt="<?php echo SITE_NAME; ?>" class="mx-auto h-20 mb-4">
            <h2 class="text-3xl font-bold text-gray-800">Clínica Dental Dentesia</h2>
            <p class="text-gray-600 mt-2">Inicie sesión para acceder al sistema</p>
        </div>

        <!-- Tarjeta de login -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <?php if (isset($error) && $error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <!-- Campo de email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Correo Electrónico
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" id="email" required
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="ejemplo@clinica.com"
                               value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>
                </div>

                <!-- Campo de contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Contraseña
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="••••••••">
                    </div>
                </div>

                <!-- Botón de inicio de sesión -->
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Iniciar Sesión
                    </button>
                </div>
            </form>

            <!-- Enlaces adicionales -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            ¿Necesita ayuda?
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div class="text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                            Olvidé mi contraseña
                        </a>
                    </div>
                    <div class="text-sm text-right">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                            Contactar soporte
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-600">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos los derechos reservados.</p>
        </div>
    </div>

    <script>
        // Validación del formulario en el lado del cliente
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            let isValid = true;

            // Validar email
            if (!email.value.trim() || !email.value.includes('@')) {
                email.classList.add('border-red-500');
                isValid = false;
            } else {
                email.classList.remove('border-red-500');
            }

            // Validar contraseña
            if (!password.value.trim()) {
                password.classList.add('border-red-500');
                isValid = false;
            } else {
                password.classList.remove('border-red-500');
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
