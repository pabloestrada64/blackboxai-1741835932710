<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botón de volver -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Crear Nuevo Usuario</h1>
        <a href="?page=users" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
    </div>
</div>

<!-- Formulario de creación -->
<div class="mt-6">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <?php if (isset($error)): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error: </strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="?page=users&action=create" method="POST" enctype="multipart/form-data" class="space-y-6" id="createUserForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

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
                                   value="<?php echo isset($oldInput['nombre']) ? htmlspecialchars($oldInput['nombre']) : ''; ?>">
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700">
                                Apellidos <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="apellidos" id="apellidos" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo isset($oldInput['apellidos']) ? htmlspecialchars($oldInput['apellidos']) : ''; ?>">
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
                                   value="<?php echo isset($oldInput['email']) ? htmlspecialchars($oldInput['email']) : ''; ?>">
                        </div>

                        <!-- Contraseña -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-sm text-gray-500">
                                Mínimo 8 caracteres, debe incluir mayúsculas, minúsculas y números
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Rol y Estado -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Rol y Estado</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rol -->
                        <div>
                            <label for="rol" class="block text-sm font-medium text-gray-700">
                                Rol <span class="text-red-500">*</span>
                            </label>
                            <select name="rol" id="rol" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione un rol</option>
                                <option value="admin" <?php echo (isset($oldInput['rol']) && $oldInput['rol'] === 'admin') ? 'selected' : ''; ?>>
                                    Administrador
                                </option>
                                <option value="odontologo" <?php echo (isset($oldInput['rol']) && $oldInput['rol'] === 'odontologo') ? 'selected' : ''; ?>>
                                    Odontólogo
                                </option>
                                <option value="enfermera" <?php echo (isset($oldInput['rol']) && $oldInput['rol'] === 'enfermera') ? 'selected' : ''; ?>>
                                    Enfermera
                                </option>
                            </select>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="estado" value="1" class="form-radio" checked>
                                    <span class="ml-2">Activo</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="estado" value="0" class="form-radio">
                                    <span class="ml-2">Inactivo</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imagen de Perfil -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Imagen de Perfil</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Foto</label>
                        <div class="mt-2 flex items-center">
                            <div class="preview-container h-32 w-32 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                <img id="preview" src="" alt="" class="h-full w-full object-cover hidden">
                                <i class="fas fa-user text-4xl text-gray-400" id="defaultIcon"></i>
                            </div>
                            <div class="ml-5">
                                <input type="file" name="imagen_perfil" id="imagen_perfil" accept="image/*" class="hidden">
                                <button type="button" onclick="document.getElementById('imagen_perfil').click()"
                                        class="btn-secondary">
                                    <i class="fas fa-upload mr-2"></i>Subir foto
                                </button>
                                <p class="mt-1 text-sm text-gray-500">
                                    PNG, JPG hasta 5MB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="window.location.href='?page=users'"
                            class="btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Guardar Usuario
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
        } else {
            preview.classList.add('hidden');
            defaultIcon.classList.remove('hidden');
        }
    });

    // Validación del formulario
    const form = document.getElementById('createUserForm');
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const password = document.getElementById('password').value;

        // Validar contraseña
        if (password.length < 8 || 
            !password.match(/[A-Z]/) || 
            !password.match(/[a-z]/) || 
            !password.match(/[0-9]/)) {
            isValid = false;
            showNotification('La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números', 'error');
        }

        // Validar email
        const email = document.getElementById('email').value;
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            isValid = false;
            showNotification('Por favor, ingrese un correo electrónico válido', 'error');
        }

        // Validar rol
        const rol = document.getElementById('rol').value;
        if (!rol) {
            isValid = false;
            showNotification('Por favor, seleccione un rol', 'error');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>
