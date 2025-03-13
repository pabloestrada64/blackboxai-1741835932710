<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botón de crear -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Gestión de Pacientes</h1>
        <a href="?page=patients&action=create" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Nuevo Paciente
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
                    echo 'Paciente registrado correctamente.';
                    break;
                case 'updated':
                    echo 'Datos del paciente actualizados correctamente.';
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
            <input type="text" id="searchInput" placeholder="Buscar paciente..." 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="flex gap-2">
            <select id="filterStatus" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                <option value="activo">Con tratamiento activo</option>
                <option value="inactivo">Sin tratamiento activo</option>
            </select>
            <button onclick="resetFilters()" class="btn-secondary">
                <i class="fas fa-sync-alt mr-2"></i>Resetear
            </button>
        </div>
    </div>
</div>

<!-- Tabla de pacientes -->
<div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="patientsTable">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Paciente
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Contacto
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Edad
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Citas
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($pacientes as $paciente): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <?php if ($paciente['imagen_perfil']): ?>
                                        <img class="h-10 w-10 rounded-full object-cover" 
                                             src="<?php echo UPLOADS_PATH . '/' . $paciente['imagen_perfil']; ?>" 
                                             alt="<?php echo htmlspecialchars($paciente['nombre']); ?>">
                                    <?php else: ?>
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellidos']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo $paciente['genero'] === 'M' ? 'Masculino' : ($paciente['genero'] === 'F' ? 'Femenino' : 'Otro'); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?php echo htmlspecialchars($paciente['telefono']); ?>
                            </div>
                            <?php if ($paciente['email']): ?>
                                <div class="text-sm text-gray-500">
                                    <?php echo htmlspecialchars($paciente['email']); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php
                            $edad = date_diff(date_create($paciente['fecha_nacimiento']), date_create('today'))->y;
                            echo $edad . ' años';
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                Total: <?php echo $paciente['total_citas']; ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                Diagnósticos: <?php echo $paciente['total_diagnosticos']; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $paciente['tratamientos_activos'] > 0 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo $paciente['tratamientos_activos'] > 0 
                                    ? 'Tratamiento Activo' 
                                    : 'Sin Tratamiento Activo'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="?page=patients&action=view&id=<?php echo $paciente['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?page=patients&action=edit&id=<?php echo $paciente['id']; ?>" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deletePatient(<?php echo $paciente['id']; ?>)"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Función para filtrar la tabla
function filterTable() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#patientsTable tbody tr');

    rows.forEach(row => {
        const nameCell = row.querySelector('td:first-child').textContent.toLowerCase();
        const statusCell = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
        const matchesSearch = nameCell.includes(searchInput);
        const matchesStatus = !statusFilter || 
            (statusFilter === 'activo' && statusCell.includes('activo')) ||
            (statusFilter === 'inactivo' && statusCell.includes('sin tratamiento'));

        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
}

// Función para resetear los filtros
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    filterTable();
}

// Función para eliminar paciente
async function deletePatient(patientId) {
    if (!patientId) return;

    const confirmed = await confirmAction('¿Está seguro de que desea eliminar este paciente? Esta acción eliminará todos los registros asociados y no se puede deshacer.');
    
    if (!confirmed) return;

    try {
        const response = await fetch('/?page=patients&action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${patientId}&csrf_token=<?php echo generateCSRFToken(); ?>`
        });

        const data = await response.json();

        if (data.success) {
            // Eliminar la fila de la tabla
            const row = event.target.closest('tr');
            row.remove();
            showNotification('Paciente eliminado correctamente', 'success');
        } else {
            throw new Error(data.error || 'Error al eliminar el paciente');
        }
    } catch (error) {
        showNotification(error.message, 'error');
    }
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('filterStatus').addEventListener('change', filterTable);
</script>
