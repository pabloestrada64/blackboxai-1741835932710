<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4">
        <h1 class="text-2xl font-semibold text-gray-900">Mi Perfil</h1>
    </div>
</div>

<!-- Mensaje de éxito -->
<?php if (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
    <div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">¡Éxito! </strong>
        <span class="block sm:inline">Su perfil ha sido actualizado correctamente.</span>
    </div>
<?php endif; ?>

<!-- Formulario de perfil -->
<div class="mt-6">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <?php if (isset($error)): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error: </strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="?page=users&action=profile" method="POST" enctype="multipart/form-data" class="space-y-6" id="profileForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <!-- Imagen de Perfil -->
                <div class="flex flex-col items-center">
                    <div class="preview-container h-40 w-40 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                        <?php if ($usuario['imagen_perfil']): ?>
                            <img id="preview" src="<?php echo UPLOADS_PATH . '/' . $usuario['imagen_perfil']; ?>" 
                                 alt="Foto de perfil" class="h-full w-full object-cover">
                            <i class="fas fa-user text-5xl text-gray-400 hidden" id="defaultIcon"></i>
                        <?php else: ?>
                            <img id="preview" src="" alt="" class="h-full w-full object-cover hidden">
                            <i class="fas fa-user text-5xl text-gray-400" id="defaultIcon"></i>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4">
                        <input type="file" name="imagen_perfil" id="imagen_perfil" accept="image/*" class="hidden">
                        <button type="button" onclick="document.getElementById('imagen_perfil').click()"
                                class="btn-secondary">
                            <i class="fas fa-camera mr-2"></i>Cambiar foto
                        </button>
                    </div>
                </div>

                <!-- Información Personal -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información Personal</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre" id="nombre" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700">
                                Apellidos <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="apellidos" id="apellidos" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo htmlspecialchars($usuario['apellidos']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Información de Cuenta -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Cuenta</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Correo Electrónico <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo htmlspecialchars($usuario['email']); ?>">
                        </div>

                        <!-- Rol (solo lectura) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rol</label>
                            <div class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 py-2 px-3">
                                <?php
                                switch ($usuario['rol']) {
                                    case 'admin':
                                        echo 'Administrador';
                                        break;
                                    case 'odontologo':
                                        echo 'Odontólogo';
                                        break;
                                    case 'enfermera':
                                        echo 'Enfermera';
                                        break;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cambiar Contraseña -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cambiar Contraseña</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nueva Contraseña -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Nueva Contraseña
                            </label>
                            <input type="password" name="password" id="password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-sm text-gray-500">
                                Dejar en blanco para mantener la contraseña actual
                            </p>
                        </div>

                        <!-- Confirmar Nueva Contraseña -->
                        <div>
                            <label for="password_confirm" class="block text-sm font-medium text-gray-700">
                                Confirmar Nueva Contraseña
                            </label>
                            <input type="password" name="password_confirm" id="password_confirm"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imagen
    const input = document.getElementById('imagen_perfil');
    const preview = document.getElementById('preview');
    const defaultIcon = document.getElementById('defaultIcon');

    input.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                defaultIcon.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    // Validación del formulario
    const form = document.getElementById('profileForm');
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;

        // Validar contraseña solo si se ha ingresado una nueva
        if (password) {
            if (password.length < 8 || 
                !password.match(/[A-Z]/) || 
                !password.match(/[a-z]/) || 
                !password.match(/[0-9]/)) {
                isValid = false;
                showNotification('La nueva contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números', 'error');
            }

            if (password !== passwordConfirm) {
                isValid = false;
                showNotification('Las contraseñas no coinciden', 'error');
            }
        }

        // Validar email
        const email = document.getElementById('email').value;
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            isValid = false;
            showNotification('Por favor, ingrese un correo electrónico válido', 'error');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>
