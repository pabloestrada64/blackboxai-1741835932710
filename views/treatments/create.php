<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botón de volver -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Nuevo Tratamiento</h1>
        <a href="?page=treatments" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
    </div>
</div>

<!-- Formulario de tratamiento -->
<div class="mt-6">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <?php if (isset($error)): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error: </strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="?page=treatments&action=create" method="POST" class="space-y-6" id="createTreatmentForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <!-- Selección de Diagnóstico -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Diagnóstico Asociado</h3>
                    
                    <?php if ($diagnostico): ?>
                        <!-- Si el diagnóstico viene preseleccionado -->
                        <input type="hidden" name="diagnostico_id" value="<?php echo $diagnostico['id']; ?>">
                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Paciente: <?php echo htmlspecialchars($diagnostico['paciente_nombre'] . ' ' . $diagnostico['paciente_apellidos']); ?>
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p><?php echo nl2br(htmlspecialchars($diagnostico['descripcion'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Si hay que seleccionar el diagnóstico -->
                        <div>
                            <label for="diagnostico_id" class="block text-sm font-medium text-gray-700">
                                Seleccione un diagnóstico <span class="text-red-500">*</span>
                            </label>
                            <select name="diagnostico_id" id="diagnostico_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione un diagnóstico</option>
                                <?php foreach ($diagnosticos as $diag): ?>
                                    <option value="<?php echo $diag['id']; ?>"
                                            <?php echo (isset($oldInput['diagnostico_id']) && $oldInput['diagnostico_id'] == $diag['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($diag['paciente_nombre'] . ' ' . $diag['paciente_apellidos'] . ' - ' . substr($diag['descripcion'], 0, 100) . '...'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Detalles del Tratamiento -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detalles del Tratamiento</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipo de Tratamiento -->
                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700">
                                Tipo de Tratamiento <span class="text-red-500">*</span>
                            </label>
                            <select name="tipo" id="tipo" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione un tipo</option>
                                <option value="Limpieza Dental" <?php echo (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Limpieza Dental') ? 'selected' : ''; ?>>
                                    Limpieza Dental
                                </option>
                                <option value="Extracción" <?php echo (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Extracción') ? 'selected' : ''; ?>>
                                    Extracción
                                </option>
                                <option value="Endodoncia" <?php echo (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Endodoncia') ? 'selected' : ''; ?>>
                                    Endodoncia
                                </option>
                                <option value="Empaste" <?php echo (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Empaste') ? 'selected' : ''; ?>>
                                    Empaste
                                </option>
                                <option value="Corona" <?php echo (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Corona') ? 'selected' : ''; ?>>
                                    Corona
                                </option>
                                <option value="Puente" <?php echo (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Puente') ? 'selected' : ''; ?>>
                                    Puente
                                </option>
                                <option value="Implante" <?php echo (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Implante') ? 'selected' : ''; ?>>
                                    Implante
                                </option>
                                <option value="Ortodoncia" <?php echo (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Ortodoncia') ? 'selected' : ''; ?>>
                                    Ortodoncia
                                </option>
                                <option value="Otro" <?php echo (isset($oldInput['tipo']) && $oldInput['tipo'] === 'Otro') ? 'selected' : ''; ?>>
                                    Otro
                                </option>
                            </select>
                        </div>

                        <!-- Fecha de Inicio -->
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">
                                Fecha de Inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo isset($oldInput['fecha_inicio']) ? htmlspecialchars($oldInput['fecha_inicio']) : date('Y-m-d'); ?>">
                        </div>

                        <!-- Costo -->
                        <div>
                            <label for="costo" class="block text-sm font-medium text-gray-700">
                                Costo ($)
                            </label>
                            <input type="number" name="costo" id="costo" step="0.01" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo isset($oldInput['costo']) ? htmlspecialchars($oldInput['costo']) : '0.00'; ?>">
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mt-6">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">
                            Descripción del Tratamiento <span class="text-red-500">*</span>
                        </label>
                        <textarea name="descripcion" id="descripcion" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Describa el tratamiento detalladamente..."><?php echo isset($oldInput['descripcion']) ? htmlspecialchars($oldInput['descripcion']) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="window.location.href='?page=treatments'"
                            class="btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Guardar Tratamiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    const form = document.getElementById('createTreatmentForm');
    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Validar fecha de inicio
        const fechaInicio = new Date(document.getElementById('fecha_inicio').value);
        const hoy = new Date();
        if (fechaInicio > hoy) {
            isValid = false;
            showNotification('La fecha de inicio no puede ser futura', 'error');
        }

        // Validar costo
        const costo = parseFloat(document.getElementById('costo').value);
        if (isNaN(costo) || costo < 0) {
            isValid = false;
            showNotification('El costo debe ser un número positivo', 'error');
        }

        // Validar descripción
        const descripcion = document.getElementById('descripcion').value.trim();
        if (descripcion.length < 10) {
            isValid = false;
            showNotification('La descripción debe tener al menos 10 caracteres', 'error');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>
