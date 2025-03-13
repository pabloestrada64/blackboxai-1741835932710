<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botones de acción -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Editar Tratamiento</h1>
        <div class="flex space-x-2">
            <a href="?page=treatments&action=view&id=<?php echo $tratamiento['id']; ?>" class="btn-secondary">
                <i class="fas fa-eye mr-2"></i>Ver Detalles
            </a>
            <a href="?page=treatments" class="btn-secondary">
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

            <form action="?page=treatments&action=edit&id=<?php echo $tratamiento['id']; ?>" method="POST" class="space-y-6" id="editTreatmentForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <!-- Información del Diagnóstico -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Diagnóstico Asociado</h3>
                    
                    <div class="rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Paciente: <?php echo htmlspecialchars($tratamiento['paciente_nombre'] . ' ' . $tratamiento['paciente_apellidos']); ?>
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p><?php echo nl2br(htmlspecialchars($tratamiento['diagnostico_descripcion'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                <?php
                                $tipos = [
                                    'Limpieza Dental', 'Extracción', 'Endodoncia', 'Empaste',
                                    'Corona', 'Puente', 'Implante', 'Ortodoncia', 'Otro'
                                ];
                                foreach ($tipos as $tipo):
                                ?>
                                    <option value="<?php echo $tipo; ?>" 
                                            <?php echo ($tratamiento['tipo'] === $tipo) ? 'selected' : ''; ?>>
                                        <?php echo $tipo; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <select name="estado" id="estado" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="pendiente" <?php echo $tratamiento['estado'] === 'pendiente' ? 'selected' : ''; ?>>
                                    Pendiente
                                </option>
                                <option value="en_progreso" <?php echo $tratamiento['estado'] === 'en_progreso' ? 'selected' : ''; ?>>
                                    En Progreso
                                </option>
                                <option value="completado" <?php echo $tratamiento['estado'] === 'completado' ? 'selected' : ''; ?>>
                                    Completado
                                </option>
                                <option value="cancelado" <?php echo $tratamiento['estado'] === 'cancelado' ? 'selected' : ''; ?>>
                                    Cancelado
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
                                   value="<?php echo htmlspecialchars($tratamiento['fecha_inicio']); ?>">
                        </div>

                        <!-- Fecha de Fin -->
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700">
                                Fecha de Finalización
                            </label>
                            <input type="date" name="fecha_fin" id="fecha_fin"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo $tratamiento['fecha_fin'] ? htmlspecialchars($tratamiento['fecha_fin']) : ''; ?>">
                        </div>

                        <!-- Costo -->
                        <div>
                            <label for="costo" class="block text-sm font-medium text-gray-700">
                                Costo ($)
                            </label>
                            <input type="number" name="costo" id="costo" step="0.01" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="<?php echo htmlspecialchars($tratamiento['costo']); ?>">
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mt-6">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">
                            Descripción del Tratamiento <span class="text-red-500">*</span>
                        </label>
                        <textarea name="descripcion" id="descripcion" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Describa el tratamiento detalladamente..."><?php echo htmlspecialchars($tratamiento['descripcion']); ?></textarea>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="window.location.href='?page=treatments'"
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
    const form = document.getElementById('editTreatmentForm');
    const estadoSelect = document.getElementById('estado');
    const fechaFinInput = document.getElementById('fecha_fin');

    // Manejar cambios en el estado
    estadoSelect.addEventListener('change', function() {
        if (this.value === 'completado') {
            fechaFinInput.value = new Date().toISOString().split('T')[0];
            fechaFinInput.required = true;
        } else {
            fechaFinInput.required = false;
        }
    });

    // Validación del formulario
    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Validar fecha de inicio
        const fechaInicio = new Date(document.getElementById('fecha_inicio').value);
        const hoy = new Date();
        if (fechaInicio > hoy) {
            isValid = false;
            showNotification('La fecha de inicio no puede ser futura', 'error');
        }

        // Validar fecha de fin si está presente
        const fechaFin = document.getElementById('fecha_fin').value;
        if (fechaFin) {
            const fechaFinDate = new Date(fechaFin);
            if (fechaFinDate < fechaInicio) {
                isValid = false;
                showNotification('La fecha de finalización no puede ser anterior a la fecha de inicio', 'error');
            }
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

        // Validar estado completado
        if (estadoSelect.value === 'completado' && !fechaFinInput.value) {
            isValid = false;
            showNotification('Debe especificar una fecha de finalización para un tratamiento completado', 'error');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>
