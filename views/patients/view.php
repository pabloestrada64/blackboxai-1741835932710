<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botones de acción -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">
            Ficha del Paciente: <?php echo htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellidos']); ?>
        </h1>
        <div class="flex space-x-2">
            <a href="?page=patients&action=edit&id=<?php echo $paciente['id']; ?>" class="btn-secondary">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
            <a href="?page=patients" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<!-- Información del Paciente -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Columna izquierda: Información personal -->
    <div class="lg:col-span-1">
        <!-- Foto y datos básicos -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex flex-col items-center">
                <div class="h-32 w-32 rounded-full overflow-hidden bg-gray-100">
                    <?php if ($paciente['imagen_perfil']): ?>
                        <img src="<?php echo UPLOADS_PATH . '/' . $paciente['imagen_perfil']; ?>" 
                             alt="Foto de perfil" class="h-full w-full object-cover">
                    <?php else: ?>
                        <div class="h-full w-full flex items-center justify-center">
                            <i class="fas fa-user text-4xl text-gray-400"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h2 class="mt-4 text-xl font-semibold text-gray-900">
                    <?php echo htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellidos']); ?>
                </h2>
                <p class="text-gray-500">
                    ID: <?php echo str_pad($paciente['id'], 6, '0', STR_PAD_LEFT); ?>
                </p>
            </div>

            <div class="mt-6 border-t border-gray-200 pt-4">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Edad</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php
                            $edad = date_diff(date_create($paciente['fecha_nacimiento']), date_create('today'))->y;
                            echo $edad . ' años';
                            ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Género</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php
                            echo $paciente['genero'] === 'M' ? 'Masculino' : 
                                ($paciente['genero'] === 'F' ? 'Femenino' : 'Otro');
                            ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo htmlspecialchars($paciente['telefono']); ?>
                        </dd>
                    </div>
                    <?php if ($paciente['email']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?php echo htmlspecialchars($paciente['email']); ?>
                            </dd>
                        </div>
                    <?php endif; ?>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo htmlspecialchars($paciente['direccion']); ?>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Historial Médico -->
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900">Historial Médico</h3>
            <dl class="mt-4 space-y-4">
                <?php if ($paciente['tipo_sangre']): ?>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tipo de Sangre</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($paciente['tipo_sangre']); ?></dd>
                    </div>
                <?php endif; ?>
                
                <?php if ($paciente['alergias']): ?>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Alergias</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo nl2br(htmlspecialchars($paciente['alergias'])); ?></dd>
                    </div>
                <?php endif; ?>
                
                <?php if ($paciente['enfermedades_cronicas']): ?>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Enfermedades Crónicas</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo nl2br(htmlspecialchars($paciente['enfermedades_cronicas'])); ?></dd>
                    </div>
                <?php endif; ?>
                
                <?php if ($paciente['medicamentos_actuales']): ?>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Medicamentos Actuales</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo nl2br(htmlspecialchars($paciente['medicamentos_actuales'])); ?></dd>
                    </div>
                <?php endif; ?>
                
                <?php if ($paciente['antecedentes_familiares']): ?>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Antecedentes Familiares</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo nl2br(htmlspecialchars($paciente['antecedentes_familiares'])); ?></dd>
                    </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <!-- Columna derecha: Citas y Diagnósticos -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Resumen -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-semibold text-blue-600">
                        <?php echo $paciente['total_citas']; ?>
                    </div>
                    <div class="text-sm text-gray-500">Citas Totales</div>
                </div>
                <div>
                    <div class="text-2xl font-semibold text-green-600">
                        <?php echo $paciente['total_diagnosticos']; ?>
                    </div>
                    <div class="text-sm text-gray-500">Diagnósticos</div>
                </div>
                <div>
                    <div class="text-2xl font-semibold text-purple-600">
                        <?php 
                        $tratamientosActivos = array_reduce($diagnosticos, function($carry, $diagnostico) {
                            return $carry + ($diagnostico['total_tratamientos'] ?? 0);
                        }, 0);
                        echo $tratamientosActivos;
                        ?>
                    </div>
                    <div class="text-sm text-gray-500">Tratamientos</div>
                </div>
            </div>
        </div>

        <!-- Próximas Citas -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Próximas Citas</h3>
                <a href="?page=appointments&action=create&patient_id=<?php echo $paciente['id']; ?>" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Nueva Cita
                </a>
            </div>
            
            <?php if (empty($citas)): ?>
                <p class="text-gray-500 text-center py-4">No hay citas programadas</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Odontólogo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($citas as $cita): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('d/m/Y H:i', strtotime($cita['fecha_hora'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Dr. <?php echo htmlspecialchars($cita['odontologo_nombre'] . ' ' . $cita['odontologo_apellidos']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php
                                            switch ($cita['estado']) {
                                                case 'programada':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'confirmada':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'completada':
                                                    echo 'bg-blue-100 text-blue-800';
                                                    break;
                                                case 'cancelada':
                                                    echo 'bg-red-100 text-red-800';
                                                    break;
                                            }
                                            ?>">
                                            <?php echo ucfirst($cita['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="?page=appointments&action=view&id=<?php echo $cita['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900">
                                            Ver detalles
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Diagnósticos -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Diagnósticos</h3>
                <a href="?page=diagnostics&action=create&patient_id=<?php echo $paciente['id']; ?>" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Nuevo Diagnóstico
                </a>
            </div>

            <?php if (empty($diagnosticos)): ?>
                <p class="text-gray-500 text-center py-4">No hay diagnósticos registrados</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($diagnosticos as $diagnostico): ?>
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo date('d/m/Y', strtotime($diagnostico['fecha_diagnostico'])); ?>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-900">
                                        Dr. <?php echo htmlspecialchars($diagnostico['odontologo_nombre'] . ' ' . $diagnostico['odontologo_apellidos']); ?>
                                    </div>
                                </div>
                                <a href="?page=diagnostics&action=view&id=<?php echo $diagnostico['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900">
                                    Ver detalles
                                </a>
                            </div>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">
                                    <?php echo nl2br(htmlspecialchars($diagnostico['descripcion'])); ?>
                                </p>
                            </div>
                            <?php if ($diagnostico['total_tratamientos'] > 0): ?>
                                <div class="mt-2 flex items-center">
                                    <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">
                                        <?php echo $diagnostico['total_tratamientos']; ?> tratamiento(s) asociado(s)
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
