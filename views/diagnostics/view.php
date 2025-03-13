<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botones de acción -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">
            Detalles del Diagnóstico
        </h1>
        <div class="flex space-x-2">
            <?php if ($diagnostico['estado'] === 'activo'): ?>
                <a href="?page=diagnostics&action=edit&id=<?php echo $diagnostico['id']; ?>" class="btn-secondary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
            <?php endif; ?>
            <a href="?page=diagnostics" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Columna izquierda: Información principal -->
    <div class="lg:col-span-2">
        <!-- Información del diagnóstico -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">
                        <?php echo htmlspecialchars($diagnostico['paciente_nombre'] . ' ' . $diagnostico['paciente_apellidos']); ?>
                    </h2>
                    <p class="text-sm text-gray-500">
                        Dr. <?php echo htmlspecialchars($diagnostico['odontologo_nombre'] . ' ' . $diagnostico['odontologo_apellidos']); ?>
                    </p>
                </div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                    <?php
                    switch ($diagnostico['estado']) {
                        case 'activo':
                            echo 'bg-green-100 text-green-800';
                            break;
                        case 'completado':
                            echo 'bg-blue-100 text-blue-800';
                            break;
                        case 'cancelado':
                            echo 'bg-red-100 text-red-800';
                            break;
                    }
                    ?>">
                    <?php echo ucfirst($diagnostico['estado']); ?>
                </span>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fecha del Diagnóstico</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo date('d/m/Y H:i', strtotime($diagnostico['fecha_diagnostico'])); ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo nl2br(htmlspecialchars($diagnostico['descripcion'])); ?>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Odontograma -->
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Odontograma</h3>
            <div class="relative bg-white border rounded-lg p-4 overflow-x-auto">
                <canvas id="odontograma" width="800" height="400" class="mx-auto"></canvas>
            </div>
        </div>

        <!-- Tratamientos asociados -->
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tratamientos Asociados</h3>
                <?php if ($diagnostico['estado'] === 'activo'): ?>
                    <a href="?page=treatments&action=create&diagnostic_id=<?php echo $diagnostico['id']; ?>" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Nuevo Tratamiento
                    </a>
                <?php endif; ?>
            </div>

            <?php if (empty($tratamientos)): ?>
                <p class="text-gray-500 text-center py-4">No hay tratamientos registrados</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($tratamientos as $tratamiento): ?>
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($tratamiento['tipo']); ?>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        Inicio: <?php echo date('d/m/Y', strtotime($tratamiento['fecha_inicio'])); ?>
                                        <?php if ($tratamiento['fecha_fin']): ?>
                                            - Fin: <?php echo date('d/m/Y', strtotime($tratamiento['fecha_fin'])); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                            <?php if ($tratamiento['descripcion']): ?>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">
                                        <?php echo nl2br(htmlspecialchars($tratamiento['descripcion'])); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            <div class="mt-2">
                                <a href="?page=treatments&action=view&id=<?php echo $tratamiento['id']; ?>" 
                                   class="text-sm text-blue-600 hover:text-blue-900">
                                    Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Columna derecha: Imagen y acciones -->
    <div class="lg:col-span-1">
        <!-- Imagen del diagnóstico -->
        <?php if ($diagnostico['imagen_diagnostico']): ?>
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Imagen del Diagnóstico</h3>
                <div class="aspect-w-1 aspect-h-1">
                    <img src="<?php echo UPLOADS_PATH . '/' . $diagnostico['imagen_diagnostico']; ?>" 
                         alt="Imagen del diagnóstico" 
                         class="w-full h-full object-cover rounded-lg">
                </div>
            </div>
        <?php endif; ?>

        <!-- Acciones rápidas -->
        <?php if ($diagnostico['estado'] === 'activo'): ?>
            <div class="mt-6 bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-3">
                    <button onclick="updateStatus('completado')" class="w-full btn-success">
                        <i class="fas fa-check mr-2"></i>Marcar como Completado
                    </button>
                    <button onclick="updateStatus('cancelado')" class="w-full btn-danger">
                        <i class="fas fa-times mr-2"></i>Cancelar Diagnóstico
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicialización del odontograma
    const canvas = document.getElementById('odontograma');
    const ctx = canvas.getContext('2d');
    const odontogramaData = <?php echo $diagnostico['odontograma']; ?>;

    // Configuración de dientes
    const dientes = {
        superior: [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28],
        inferior: [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38]
    };

    // Dibujar odontograma
    function drawOdontograma() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Dibujar dientes superiores
        dientes.superior.forEach((num, index) => {
            const x = 50 + index * 45;
            const y = 100;
            drawTooth(x, y, num, 'superior');
        });

        // Dibujar dientes inferiores
        dientes.inferior.forEach((num, index) => {
            const x = 50 + index * 45;
            const y = 250;
            drawTooth(x, y, num, 'inferior');
        });

        // Dibujar marcas guardadas
        Object.entries(odontogramaData.dientes).forEach(([diente, marcas]) => {
            marcas.forEach(marca => {
                drawMark(marca.x, marca.y, marca.tipo);
            });
        });
    }

    // Dibujar un diente individual
    function drawTooth(x, y, num, position) {
        ctx.beginPath();
        ctx.rect(x - 20, y - 20, 40, 40);
        ctx.strokeStyle = '#000';
        ctx.stroke();

        ctx.font = '12px Arial';
        ctx.fillStyle = '#000';
        ctx.textAlign = 'center';
        ctx.fillText(num, x, y + 35);
    }

    // Dibujar una marca según el tipo
    function drawMark(x, y, tipo) {
        ctx.beginPath();
        switch (tipo) {
            case 'caries':
                ctx.fillStyle = '#ef4444';
                ctx.arc(x, y, 5, 0, Math.PI * 2);
                ctx.fill();
                break;
            case 'restauracion':
                ctx.fillStyle = '#3b82f6';
                ctx.fillRect(x - 5, y - 5, 10, 10);
                break;
            case 'ausente':
                ctx.strokeStyle = '#6b7280';
                ctx.moveTo(x - 5, y - 5);
                ctx.lineTo(x + 5, y + 5);
                ctx.moveTo(x + 5, y - 5);
                ctx.lineTo(x - 5, y + 5);
                ctx.stroke();
                break;
            case 'corona':
                ctx.fillStyle = '#eab308';
                ctx.beginPath();
                ctx.moveTo(x, y - 5);
                ctx.lineTo(x + 5, y);
                ctx.lineTo(x, y + 5);
                ctx.lineTo(x - 5, y);
                ctx.closePath();
                ctx.fill();
                break;
            case 'endodoncia':
                ctx.fillStyle = '#22c55e';
                ctx.beginPath();
                ctx.moveTo(x, y - 5);
                ctx.lineTo(x + 5, y + 5);
                ctx.lineTo(x - 5, y + 5);
                ctx.closePath();
                ctx.fill();
                break;
        }
    }

    // Dibujar odontograma inicial
    drawOdontograma();
});

// Función para actualizar el estado del diagnóstico
async function updateStatus(newStatus) {
    const confirmed = await confirmAction(`¿Está seguro de que desea marcar este diagnóstico como ${newStatus}?`);
    
    if (!confirmed) return;

    try {
        const response = await fetch('/?page=diagnostics&action=updateStatus', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=<?php echo $diagnostico['id']; ?>&estado=${newStatus}&csrf_token=<?php echo generateCSRFToken(); ?>`
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
