<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título de la página -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4">
        <h1 class="text-2xl font-semibold text-gray-900">Panel de Control</h1>
    </div>
</div>

<!-- Estadísticas generales -->
<div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Total de Pacientes -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total de Pacientes
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">
                                <?php echo number_format($stats['totalPacientes']); ?>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="?page=patients" class="font-medium text-blue-600 hover:text-blue-900">
                    Ver todos los pacientes <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Citas Pendientes -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-alt text-2xl text-green-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Citas Pendientes
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">
                                <?php echo number_format($stats['citasPendientes']); ?>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="?page=appointments" class="font-medium text-green-600 hover:text-green-900">
                    Ver todas las citas <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Tratamientos en Curso -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-notes-medical text-2xl text-purple-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Tratamientos en Curso
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">
                                <?php echo number_format($stats['tratamientosEnCurso']); ?>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="?page=treatments" class="font-medium text-purple-600 hover:text-purple-900">
                    Ver tratamientos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Diagnósticos del Mes -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-tooth text-2xl text-orange-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Diagnósticos del Mes
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">
                                <?php echo number_format($stats['diagnosticosMes']); ?>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="?page=diagnostics" class="font-medium text-orange-600 hover:text-orange-900">
                    Ver diagnósticos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Contenido Principal -->
<div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
    <!-- Calendario -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Calendario de Citas
            </h3>
            <div id="calendar" class="min-h-[400px]"></div>
        </div>
    </div>

    <!-- Actividades Recientes -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Actividades Recientes
            </h3>
            <div class="flow-root">
                <ul class="-mb-8">
                    <?php foreach ($ultimasActividades as $index => $actividad): ?>
                        <li>
                            <div class="relative pb-8">
                                <?php if ($index !== count($ultimasActividades) - 1): ?>
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <?php endif; ?>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white 
                                            <?php echo $actividad['tipo'] === 'diagnostico' ? 'bg-blue-500' : 
                                                ($actividad['tipo'] === 'tratamiento' ? 'bg-green-500' : 'bg-purple-500'); ?>">
                                            <i class="fas <?php echo $actividad['tipo'] === 'diagnostico' ? 'fa-stethoscope' : 
                                                ($actividad['tipo'] === 'tratamiento' ? 'fa-notes-medical' : 'fa-calendar-check'); ?> 
                                                text-white"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($actividad['descripcion']); ?></p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            <time datetime="<?php echo $actividad['fecha']; ?>">
                                                <?php echo date('d M H:i', strtotime($actividad['fecha'])); ?>
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Citas de Hoy -->
<div class="mt-6">
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Citas de Hoy
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hora
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Paciente
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Odontólogo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($citasHoy)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay citas programadas para hoy
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($citasHoy as $cita): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('H:i', strtotime($cita['fecha_hora'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($cita['paciente_nombre'] . ' ' . $cita['paciente_apellidos']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            Dr. <?php echo htmlspecialchars($cita['odontologo_nombre'] . ' ' . $cita['odontologo_apellidos']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $cita['estado'] === 'programada' ? 'bg-yellow-100 text-yellow-800' : 
                                                ($cita['estado'] === 'confirmada' ? 'bg-green-100 text-green-800' : 
                                                'bg-gray-100 text-gray-800'); ?>">
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
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Scripts específicos del dashboard -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el calendario
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        },
        events: '?page=dashboard&action=getCalendarEvents',
        eventClick: function(info) {
            // Mostrar detalles del evento
            showNotification(info.event.title + ' - ' + info.event.extendedProps.description);
        }
    });
    
    calendar.render();
});
</script>
