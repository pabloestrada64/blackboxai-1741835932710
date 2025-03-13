<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botón de crear -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Gestión de Diagnósticos</h1>
        <a href="?page=diagnostics&action=create" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Nuevo Diagnóstico
        </a>
    </div>
</div>

<!-- Mensaje de éxito -->
<?php if (isset($_GET['success'])): ?>
    <div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">¡Éxito! </strong>
        <span class="block sm:inline">
            <?php
            switch ($_GET['success']) {
                case 'created':
                    echo 'Diagnóstico registrado correctamente.';
                    break;
                case 'updated':
                    echo 'Diagnóstico actualizado correctamente.';
                    break;
                default:
                    echo 'Operación completada con éxito.';
            }
            ?>
        </span>
    </div>
<?php endif; ?>

<!-- Filtros y búsqueda -->
<div class="mt-6 bg-white shadow rounded-lg p-4">
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" id="searchInput" placeholder="Buscar diagnóstico..." 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="flex gap-2">
            <select id="filterStatus" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                <option value="activo">Activo</option>
                <option value="completado">Completado</option>
                <option value="cancelado">Cancelado</option>
            </select>
            <button onclick="resetFilters()" class="btn-secondary">
                <i class="fas fa-sync-alt mr-2"></i>Resetear
            </button>
        </div>
    </div>
</div>

<!-- Lista de diagnósticos -->
<div class="mt-6 grid grid-cols-1 gap-6">
    <?php if (empty($diagnosticos)): ?>
        <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
            No hay diagnósticos registrados
        </div>
    <?php else: ?>
        <?php foreach ($diagnosticos as $diagnostico): ?>
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex flex-col md:flex-row justify-between">
                    <!-- Información principal -->
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-tooth text-blue-600"></i>
                                </span>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-medium text-gray-900">
                                    <?php echo htmlspecialchars($diagnostico['paciente_nombre'] . ' ' . $diagnostico['paciente_apellidos']); ?>
                                </h2>
                                <div class="text-sm text-gray-500">
                                    Dr. <?php echo htmlspecialchars($diagnostico['odontologo_nombre'] . ' ' . $diagnostico['odontologo_apellidos']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            <?php echo nl2br(htmlspecialchars(substr($diagnostico['descripcion'], 0, 200))); ?>
                            <?php if (strlen($diagnostico['descripcion']) > 200): ?>...<?php endif; ?>
                        </div>
                    </div>

                    <!-- Metadatos y acciones -->
                    <div class="mt-4 md:mt-0 md:ml-6 flex flex-col items-end justify-between">
                        <div class="text-sm text-gray-500">
                            <?php echo date('d/m/Y H:i', strtotime($diagnostico['fecha_diagnostico'])); ?>
                        </div>
                        
                        <div class="mt-2 flex flex-col items-end space-y-2">
                            <!-- Estado -->
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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

                            <!-- Tratamientos -->
                            <?php if ($diagnostico['total_tratamientos'] > 0): ?>
                                <div class="text-sm text-purple-600">
                                    <i class="fas fa-clipboard-list mr-1"></i>
                                    <?php echo $diagnostico['total_tratamientos']; ?> tratamiento(s)
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Botones de acción -->
                        <div class="mt-4 flex space-x-2">
                            <a href="?page=diagnostics&action=view&id=<?php echo $diagnostico['id']; ?>" 
                               class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($diagnostico['estado'] === 'activo'): ?>
                                <a href="?page=diagnostics&action=edit&id=<?php echo $diagnostico['id']; ?>" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="updateStatus(<?php echo $diagnostico['id']; ?>, 'completado')"
                                        class="text-green-600 hover:text-green-900" title="Marcar como completado">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="updateStatus(<?php echo $diagnostico['id']; ?>, 'cancelado')"
                                        class="text-red-600 hover:text-red-900" title="Cancelar">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (hasRole('admin')): ?>
                                <button onclick="deleteDiagnostic(<?php echo $diagnostico['id']; ?>)"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
// Función para filtrar los diagnósticos
function filterDiagnostics() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const diagnostics = document.querySelectorAll('.bg-white.shadow.rounded-lg');

    diagnostics.forEach(diagnostic => {
        if (diagnostic.classList.contains('p-6')) { // Excluir el div de filtros
            const text = diagnostic.textContent.toLowerCase();
            const status = diagnostic.querySelector('.rounded-full').textContent.toLowerCase();
            
            const matchesSearch = text.includes(searchInput);
            const matchesStatus = !statusFilter || status.includes(statusFilter);

            diagnostic.style.display = matchesSearch && matchesStatus ? '' : 'none';
        }
    });
}

// Función para resetear los filtros
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    filterDiagnostics();
}

// Función para actualizar el estado de un diagnóstico
async function updateStatus(diagnosticId, newStatus) {
    if (!diagnosticId || !newStatus) return;

    const confirmed = await confirmAction(`¿Está seguro de que desea marcar este diagnóstico como ${newStatus}?`);
    
    if (!confirmed) return;

    try {
        const response = await fetch('/?page=diagnostics&action=updateStatus', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${diagnosticId}&estado=${newStatus}&csrf_token=<?php echo generateCSRFToken(); ?>`
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

// Función para eliminar un diagnóstico
async function deleteDiagnostic(diagnosticId) {
    if (!diagnosticId) return;

    const confirmed = await confirmAction('¿Está seguro de que desea eliminar este diagnóstico? Esta acción eliminará también todos los tratamientos asociados y no se puede deshacer.');
    
    if (!confirmed) return;

    try {
        const response = await fetch('/?page=diagnostics&action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${diagnosticId}&csrf_token=<?php echo generateCSRFToken(); ?>`
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Diagnóstico eliminado correctamente', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error(data.error || 'Error al eliminar el diagnóstico');
        }
    } catch (error) {
        showNotification(error.message, 'error');
    }
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', filterDiagnostics);
document.getElementById('filterStatus').addEventListener('change', filterDiagnostics);
</script>
