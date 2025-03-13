<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botones de acción -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">
            Detalles del Tratamiento
        </h1>
        <div class="flex space-x-2">
            <?php if ($tratamiento['estado'] !== 'completado' && $tratamiento['estado'] !== 'cancelado'): ?>
                <a href="?page=treatments&action=edit&id=<?php echo $tratamiento['id']; ?>" class="btn-secondary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
            <?php endif; ?>
            <a href="?page=treatments" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Columna izquierda: Información principal -->
    <div class="lg:col-span-2">
        <!-- Información del tratamiento -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">
                        <?php echo htmlspecialchars($tratamiento['tipo']); ?>
                    </h2>
                    <p class="text-sm text-gray-500">
                        Paciente: <?php echo htmlspecialchars($tratamiento['paciente_nombre'] . ' ' . $tratamiento['paciente_apellidos']); ?>
                    </p>
                </div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                    <?php
                    switch ($tratamiento['estado']) {
                        case 'pendiente':
                            echo 'bg-yellow-100 text-yellow-800';
                            break;
                        case 'en_progreso':
                            echo 'bg-blue-100 text-blue-800';
                            break;
                        case 'completado':
                            echo 'bg-green-100 text-green-800';
                            break;
                        case 'cancelado':
                            echo 'bg-red-100 text-red-800';
                            break;
                    }
                    ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $tratamiento['estado'])); ?>
                </span>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Odontólogo</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            Dr. <?php echo htmlspecialchars($tratamiento['odontologo_nombre'] . ' ' . $tratamiento['odontologo_apellidos']); ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fechas</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            Inicio: <?php echo date('d/m/Y', strtotime($tratamiento['fecha_inicio'])); ?>
                            <?php if ($tratamiento['fecha_fin']): ?>
                                <br>Finalización: <?php echo date('d/m/Y', strtotime($tratamiento['fecha_fin'])); ?>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Costo</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            $<?php echo number_format($tratamiento['costo'], 2); ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo nl2br(htmlspecialchars($tratamiento['descripcion'])); ?>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Diagnóstico asociado -->
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Diagnóstico Asociado</h3>
            <div class="rounded-md bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm text-blue-700">
                            <?php echo nl2br(htmlspecialchars($tratamiento['diagnostico_descripcion'])); ?>
                        </div>
                        <div class="mt-2">
                            <a href="?page=diagnostics&action=view&id=<?php echo $tratamiento['diagnostico_id']; ?>" 
                               class="text-sm text-blue-600 hover:text-blue-900">
                                Ver diagnóstico completo <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna derecha: Estado y acciones -->
    <div class="lg:col-span-1">
        <!-- Estado actual -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Estado del Tratamiento</h3>
            <div class="flex flex-col items-center">
                <div class="w-24 h-24 rounded-full flex items-center justify-center 
                    <?php
                    switch ($tratamiento['estado']) {
                        case 'pendiente':
                            echo 'bg-yellow-100';
                            break;
                        case 'en_progreso':
                            echo 'bg-blue-100';
                            break;
                        case 'completado':
                            echo 'bg-green-100';
                            break;
                        case 'cancelado':
                            echo 'bg-red-100';
                            break;
                    }
                    ?>">
                    <?php
                    switch ($tratamiento['estado']) {
                        case 'pendiente':
                            echo '<i class="fas fa-clock text-4xl text-yellow-600"></i>';
                            break;
                        case 'en_progreso':
                            echo '<i class="fas fa-spinner text-4xl text-blue-600"></i>';
                            break;
                        case 'completado':
                            echo '<i class="fas fa-check text-4xl text-green-600"></i>';
                            break;
                        case 'cancelado':
                            echo '<i class="fas fa-times text-4xl text-red-600"></i>';
                            break;
                    }
                    ?>
                </div>
                <p class="mt-4 text-lg font-medium text-gray-900">
                    <?php echo ucfirst(str_replace('_', ' ', $tratamiento['estado'])); ?>
                </p>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <?php if ($tratamiento['estado'] !== 'completado' && $tratamiento['estado'] !== 'cancelado'): ?>
            <div class="mt-6 bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-3">
                    <?php if ($tratamiento['estado'] === 'pendiente'): ?>
                        <button onclick="updateStatus('en_progreso')" class="w-full btn-primary">
                            <i class="fas fa-play mr-2"></i>Iniciar Tratamiento
                        </button>
                    <?php endif; ?>
                    <?php if ($tratamiento['estado'] === 'en_progreso'): ?>
                        <button onclick="updateStatus('completado')" class="w-full btn-success">
                            <i class="fas fa-check mr-2"></i>Marcar como Completado
                        </button>
                    <?php endif; ?>
                    <button onclick="updateStatus('cancelado')" class="w-full btn-danger">
                        <i class="fas fa-times mr-2"></i>Cancelar Tratamiento
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Función para actualizar el estado del tratamiento
async function updateStatus(newStatus) {
    const statusText = {
        'en_progreso': 'iniciar',
        'completado': 'completar',
        'cancelado': 'cancelar'
    };

    const confirmed = await confirmAction(`¿Está seguro de que desea ${statusText[newStatus]} este tratamiento?`);
    
    if (!confirmed) return;

    try {
        const response = await fetch('/?page=treatments&action=updateStatus', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=<?php echo $tratamiento['id']; ?>&estado=${newStatus}&csrf_token=<?php echo generateCSRFToken(); ?>`
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Estado actualizado correctamente', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error(data.error || 'Error al actualizar el estado');
        }
    } catch (error) {
        showNotification(error.message, 'error');
    }
}
</script>
