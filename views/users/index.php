<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botón de crear -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Gestión de Usuarios</h1>
        <a href="?page=users&action=create" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Nuevo Usuario
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
                    echo 'Usuario creado correctamente.';
                    break;
                case 'updated':
                    echo 'Usuario actualizado correctamente.';
                    break;
                case 'deleted':
                    echo 'Usuario eliminado correctamente.';
                    break;
                default:
                    echo 'Operación completada con éxito.';
            }
            ?>
        </span>
    </div>
<?php endif; ?>

<!-- Tabla de usuarios -->
<div class="mt-6 bg-white shadow rounded-lg">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Usuario
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Correo Electrónico
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rol
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fecha Registro
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <?php if ($usuario['imagen_perfil']): ?>
                                        <img class="h-10 w-10 rounded-full object-cover" 
                                             src="<?php echo UPLOADS_PATH . '/' . $usuario['imagen_perfil']; ?>" 
                                             alt="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                                    <?php else: ?>
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?php echo htmlspecialchars($usuario['email']); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php
                                switch ($usuario['rol']) {
                                    case 'admin':
                                        echo 'bg-purple-100 text-purple-800';
                                        break;
                                    case 'odontologo':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'enfermera':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                }
                                ?>">
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
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="toggleUserStatus(<?php echo $usuario['id']; ?>)"
                                    class="toggle-status px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $usuario['estado'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $usuario['estado'] ? 'Activo' : 'Inactivo'; ?>
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('d/m/Y H:i', strtotime($usuario['fecha_registro'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="?page=users&action=edit&id=<?php echo $usuario['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteUser(<?php echo $usuario['id']; ?>)"
                                        class="text-red-600 hover:text-red-900">
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

<!-- Scripts específicos para la gestión de usuarios -->
<script>
async function toggleUserStatus(userId) {
    if (!userId) return;

    try {
        const response = await fetch('/?page=users&action=toggleStatus', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${userId}&csrf_token=<?php echo generateCSRFToken(); ?>`
        });

        const data = await response.json();

        if (data.success) {
            const button = event.target.closest('.toggle-status');
            if (data.newStatus) {
                button.classList.remove('bg-red-100', 'text-red-800');
                button.classList.add('bg-green-100', 'text-green-800');
                button.textContent = 'Activo';
            } else {
                button.classList.remove('bg-green-100', 'text-green-800');
                button.classList.add('bg-red-100', 'text-red-800');
                button.textContent = 'Inactivo';
            }
            showNotification('Estado actualizado correctamente', 'success');
        } else {
            throw new Error(data.error || 'Error al actualizar el estado');
        }
    } catch (error) {
        showNotification(error.message, 'error');
    }
}

async function deleteUser(userId) {
    if (!userId) return;

    const confirmed = await confirmAction('¿Está seguro de que desea eliminar este usuario? Esta acción no se puede deshacer.');
    
    if (!confirmed) return;

    try {
        const response = await fetch('/?page=users&action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${userId}&csrf_token=<?php echo generateCSRFToken(); ?>`
        });

        const data = await response.json();

        if (data.success) {
            // Eliminar la fila de la tabla
            const row = event.target.closest('tr');
            row.remove();
            showNotification('Usuario eliminado correctamente', 'success');
        } else {
            throw new Error(data.error || 'Error al eliminar el usuario');
        }
    } catch (error) {
        showNotification(error.message, 'error');
    }
}
</script>
