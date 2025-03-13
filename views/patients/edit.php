<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botones de acción -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">
            Editar Paciente: <?php echo htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellidos']); ?>
        </h1>
        <div class="flex space-x-2">
            <a href="?page=patients&action=view&id=<?php echo $paciente['id']; ?>" class="btn-secondary">
                <i class="fas fa-eye mr-2"></i>Ver Detalles
            </a>
            <a href="?page=patients" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<!-- Formulario de edición -->
<div class="mt-6">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <?php if (isset($error)): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error: </strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="?page=patients&action=edit&id=<?php echo $paciente['id']; ?>" method="POST" enctype="multipart/form-data" class="space-y-6" id="editPatientForm">
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
                                   value="<?php echo htmlspecialchars($paciente['nombre']); ?>">
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700">
                                Apellidos <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="apellidos" id="apellidos" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo htmlspecialchars($paciente['apellidos']); ?>">
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div>
                            <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">
                                Fecha de Nacimiento <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo htmlspecialchars($paciente['fecha_nacimiento']); ?>">
                        </div>

                        <!-- Género -->
                        <div>
                            <label for="genero" class="block text-sm font-medium text-gray-700">
                                Género <span class="text-red-500">*</span>
                            </label>
                            <select name="genero" id="genero" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="M" <?php echo $paciente['genero'] === 'M' ? 'selected' : ''; ?>>
                                    Masculino
                                </option>
                                <option value="F" <?php echo $paciente['genero'] === 'F' ? 'selected' : ''; ?>>
                                    Femenino
                                </option>
                                <option value="O" <?php echo $paciente['genero'] === 'O' ? 'selected' : ''; ?>>
                                    Otro
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Contacto</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Teléfono -->
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700">
                                Teléfono <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="telefono" id="telefono" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo htmlspecialchars($paciente['telefono']); ?>">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Correo Electrónico
                            </label>
                            <input type="email" name="email" id="email"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo htmlspecialchars($paciente['email'] ?? ''); ?>">
                        </div>

                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <label for="direccion" class="block text-sm font-medium text-gray-700">
                                Dirección <span class="text-red-500">*</span>
                            </label>
                            <textarea name="direccion" id="direccion" rows="3" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($paciente['direccion']); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Historial Médico -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historial Médico</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipo de Sangre -->
                        <div>
                            <label for="tipo_sangre" class="block text-sm font-medium text-gray-700">
                                Tipo de Sangre
                            </label>
                            <select name="tipo_sangre" id="tipo_sangre"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione tipo de sangre</option>
                                <?php
                                $tipos_sangre = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                foreach ($tipos_sangre as $tipo) {
                                    $selected = ($paciente['tipo_sangre'] === $tipo) ? 'selected' : '';
                                    echo "<option value=\"$tipo\" $selected>$tipo</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Alergias -->
                        <div class="md:col-span-2">
                            <label for="alergias" class="block text-sm font-medium text-gray-700">
                                Alergias
                            </label>
                            <textarea name="alergias" id="alergias" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Describa las alergias conocidas"><?php echo htmlspecialchars($paciente['alergias'] ?? ''); ?></textarea>
                        </div>

                        <!-- Enfermedades Crónicas -->
                        <div class="md:col-span-2">
                            <label for="enfermedades_cronicas" class="block text-sm font-medium text-gray-700">
                                Enfermedades Crónicas
                            </label>
                            <textarea name="enfermedades_cronicas" id="enfermedades_cronicas" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Describa las enfermedades crónicas"><?php echo htmlspecialchars($paciente['enfermedades_cronicas'] ?? ''); ?></textarea>
                        </div>

                        <!-- Medicamentos Actuales -->
                        <div class="md:col-span-2">
                            <label for="medicamentos_actuales" class="block text-sm font-medium text-gray-700">
                                Medicamentos Actuales
                            </label>
                            <textarea name="medicamentos_actuales" id="medicamentos_actuales" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Liste los medicamentos que toma actualmente"><?php echo htmlspecialchars($paciente['medicamentos_actuales'] ?? ''); ?></textarea>
                        </div>

                        <!-- Antecedentes Familiares -->
                        <div class="md:col-span-2">
                            <label for="antecedentes_familiares" class="block text-sm font-medium text-gray-700">
                                Antecedentes Familiares
                            </label>
                            <textarea name="antecedentes_familiares" id="antecedentes_familiares" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Describa los antecedentes familiares relevantes"><?php echo htmlspecialchars($paciente['antecedentes_familiares'] ?? ''); ?></textarea>
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
                                <?php if ($paciente['imagen_perfil']): ?>
                                    <img id="preview" src="<?php echo UPLOADS_PATH . '/' . $paciente['imagen_perfil']; ?>" 
                                         alt="Foto de perfil" class="h-full w-full object-cover">
                                    <i class="fas fa-user text-4xl text-gray-400 hidden" id="defaultIcon"></i>
                                <?php else: ?>
                                    <img id="preview" src="" alt="" class="h-full w-full object-cover hidden">
                                    <i class="fas fa-user text-4xl text-gray-400" id="defaultIcon"></i>
                                <?php endif; ?>
                            </div>
                            <div class="ml-5">
                                <input type="file" name="imagen_perfil" id="imagen_perfil" accept="image/*" class="hidden">
                                <button type="button" onclick="document.getElementById('imagen_perfil').click()"
                                        class="btn-secondary">
                                    <i class="fas fa-upload mr-2"></i>Cambiar foto
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
                    <button type="button" onclick="window.location.href='?page=patients'"
                            class="btn-secondary">
                        Cancelar
                    </button>
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
    const form = document.getElementById('editPatientForm');
    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Validar fecha de nacimiento
        const fechaNacimiento = new Date(document.getElementById('fecha_nacimiento').value);
        const hoy = new Date();
        if (fechaNacimiento >= hoy) {
            isValid = false;
            showNotification('La fecha de nacimiento no puede ser futura', 'error');
        }

        // Validar teléfono
        const telefono = document.getElementById('telefono').value;
        if (!telefono.match(/^\d{8,15}$/)) {
            isValid = false;
            showNotification('Por favor, ingrese un número de teléfono válido (8-15 dígitos)', 'error');
        }

        // Validar email si se proporcionó
        const email = document.getElementById('email').value;
        if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            isValid = false;
            showNotification('Por favor, ingrese un correo electrónico válido', 'error');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>
