<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo ucfirst($page ?? 'Inicio'); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- FullCalendar -->
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/web-component@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.8/index.global.min.js'></script>

    <!-- Estilos personalizados -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .sidebar-link {
            @apply flex items-center gap-3 px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-200;
        }
        
        .sidebar-link.active {
            @apply bg-blue-50 text-blue-600;
        }
        
        .btn-primary {
            @apply bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200;
        }
        
        .btn-secondary {
            @apply bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200;
        }
        
        .card {
            @apply bg-white rounded-lg shadow-md p-6;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php if (isAuthenticated()): ?>
        <!-- Barra de navegación superior -->
        <nav class="bg-white shadow-md fixed w-full z-10">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <img class="h-8 w-auto" src="<?php echo ASSETS_PATH; ?>/images/logo.png" alt="<?php echo SITE_NAME; ?>">
                        </div>
                    </div>
                    
                    <!-- Menú derecho -->
                    <div class="flex items-center">
                        <!-- Notificaciones -->
                        <button class="p-2 rounded-full text-gray-600 hover:bg-gray-100 relative">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500"></span>
                        </button>
                        
                        <!-- Perfil -->
                        <div class="ml-4 relative">
                            <div class="flex items-center">
                                <img class="h-8 w-8 rounded-full object-cover" 
                                     src="<?php echo $_SESSION['user_image'] ?? ASSETS_PATH . '/images/default-avatar.png'; ?>" 
                                     alt="Foto de perfil">
                                <span class="ml-2 text-gray-700"><?php echo $_SESSION['user_name'] ?? 'Usuario'; ?></span>
                            </div>
                        </div>
                        
                        <!-- Botón de cerrar sesión -->
                        <a href="logout.php" class="ml-4 text-gray-600 hover:text-gray-800">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Barra lateral -->
        <aside class="fixed left-0 top-16 h-full w-64 bg-white shadow-md">
            <nav class="mt-5 px-2">
                <a href="?page=dashboard" class="sidebar-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Panel Principal</span>
                </a>
                
                <?php if (hasRole('admin')): ?>
                <a href="?page=users" class="sidebar-link <?php echo $page === 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Gestión de Usuarios</span>
                </a>
                <?php endif; ?>
                
                <a href="?page=patients" class="sidebar-link <?php echo $page === 'patients' ? 'active' : ''; ?>">
                    <i class="fas fa-user-injured"></i>
                    <span>Pacientes</span>
                </a>
                
                <a href="?page=appointments" class="sidebar-link <?php echo $page === 'appointments' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Citas</span>
                </a>
                
                <a href="?page=diagnostics" class="sidebar-link <?php echo $page === 'diagnostics' ? 'active' : ''; ?>">
                    <i class="fas fa-tooth"></i>
                    <span>Diagnósticos</span>
                </a>
                
                <a href="?page=treatments" class="sidebar-link <?php echo $page === 'treatments' ? 'active' : ''; ?>">
                    <i class="fas fa-notes-medical"></i>
                    <span>Tratamientos</span>
                </a>
                
                <a href="?page=reports" class="sidebar-link <?php echo $page === 'reports' ? 'active' : ''; ?>">
                    <i class="fas fa-file-medical-alt"></i>
                    <span>Reportes</span>
                </a>
                
                <?php if (hasRole('admin')): ?>
                <a href="?page=configuration" class="sidebar-link <?php echo $page === 'configuration' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="ml-64 pt-16">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- El contenido específico de cada página irá aquí -->
    <?php endif; ?>
