<?php
if (!defined('ROOT_PATH')) {
    die('Acceso directo al archivo no permitido');
}
?>

<!-- Título y botón de volver -->
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Nuevo Diagnóstico</h1>
        <a href="?page=diagnostics" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
    </div>
</div>

<!-- Formulario de diagnóstico -->
<div class="mt-6">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <?php if (isset($error)): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error: </strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="?page=diagnostics&action=create" method="POST" enctype="multipart/form-data" class="space-y-6" id="createDiagnosticForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="odontograma" id="odontogramaData">

                <!-- Selección de Paciente -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Selección de Paciente</h3>
                    
                    <div>
                        <label for="paciente_id" class="block text-sm font-medium text-gray-700">
                            Paciente <span class="text-red-500">*</span>
                        </label>
                        <select name="paciente_id" id="paciente_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione un paciente</option>
                            <?php foreach ($pacientes as $paciente): ?>
                                <option value="<?php echo $paciente['id']; ?>"
                                        <?php echo (isset($oldInput['paciente_id']) && $oldInput['paciente_id'] == $paciente['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellidos']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Odontograma -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Odontograma</h3>
                    
                    <!-- Herramientas del odontograma -->
                    <div class="mb-4 flex flex-wrap gap-2">
                        <button type="button" class="tool-btn" data-tool="caries" title="Caries">
                            <i class="fas fa-circle text-red-500"></i> Caries
                        </button>
                        <button type="button" class="tool-btn" data-tool="restauracion" title="Restauración">
                            <i class="fas fa-square text-blue-500"></i> Restauración
                        </button>
                        <button type="button" class="tool-btn" data-tool="ausente" title="Ausente">
                            <i class="fas fa-times text-gray-500"></i> Ausente
                        </button>
                        <button type="button" class="tool-btn" data-tool="corona" title="Corona">
                            <i class="fas fa-crown text-yellow-500"></i> Corona
                        </button>
                        <button type="button" class="tool-btn" data-tool="endodoncia" title="Endodoncia">
                            <i class="fas fa-tooth text-green-500"></i> Endodoncia
                        </button>
                        <button type="button" class="tool-btn" data-tool="borrar" title="Borrar">
                            <i class="fas fa-eraser text-gray-500"></i> Borrar
                        </button>
                    </div>

                    <!-- Canvas del odontograma -->
                    <div class="relative bg-white border rounded-lg p-4 overflow-x-auto">
                        <canvas id="odontograma" width="800" height="400" class="mx-auto"></canvas>
                    </div>
                </div>

                <!-- Descripción del diagnóstico -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Descripción del Diagnóstico</h3>
                    
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">
                            Descripción detallada <span class="text-red-500">*</span>
                        </label>
                        <textarea name="descripcion" id="descripcion" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Describa el diagnóstico detalladamente..."><?php echo isset($oldInput['descripcion']) ? htmlspecialchars($oldInput['descripcion']) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Imagen del diagnóstico -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Imagen del Diagnóstico</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Imagen de rayos X o fotografía
                        </label>
                        <div class="mt-2 flex items-center">
                            <div class="preview-container h-40 w-40 bg-gray-100 flex items-center justify-center">
                                <img id="preview" src="" alt="" class="max-h-full max-w-full hidden">
                                <i class="fas fa-image text-4xl text-gray-400" id="defaultIcon"></i>
                            </div>
                            <div class="ml-5">
                                <input type="file" name="imagen_diagnostico" id="imagen_diagnostico" 
                                       accept="image/*" class="hidden">
                                <button type="button" onclick="document.getElementById('imagen_diagnostico').click()"
                                        class="btn-secondary">
                                    <i class="fas fa-upload mr-2"></i>Subir imagen
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
                    <button type="button" onclick="window.location.href='?page=diagnostics'"
                            class="btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Guardar Diagnóstico
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.tool-btn {
    @apply px-3 py-2 bg-white border rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500;
}

.tool-btn.active {
    @apply bg-blue-50 border-blue-500 text-blue-700;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imagen
    const input = document.getElementById('imagen_diagnostico');
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

    // Inicialización del odontograma
    const canvas = document.getElementById('odontograma');
    const ctx = canvas.getContext('2d');
    let currentTool = null;
    let odontogramaData = {
        dientes: {},
        anotaciones: []
    };

    // Configuración de dientes
    const dientes = {
        superior: [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28],
        inferior: [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38]
    };

    // Dibujar odontograma inicial
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
        // Dibujar cuadrado del diente
        ctx.beginPath();
        ctx.rect(x - 20, y - 20, 40, 40);
        ctx.strokeStyle = '#000';
        ctx.stroke();

        // Dibujar número del diente
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

    // Manejar clics en el canvas
    canvas.addEventListener('click', function(e) {
        if (!currentTool) return;

        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        // Encontrar el diente más cercano
        let dienteEncontrado = null;
        let distanciaMinima = Infinity;

        [...dientes.superior, ...dientes.inferior].forEach(num => {
            const index = dientes.superior.includes(num) 
                ? dientes.superior.indexOf(num)
                : dientes.inferior.indexOf(num);
            const dentX = 50 + index * 45;
            const dentY = dientes.superior.includes(num) ? 100 : 250;
            
            const distancia = Math.sqrt(Math.pow(x - dentX, 2) + Math.pow(y - dentY, 2));
            if (distancia < 30 && distancia < distanciaMinima) {
                distanciaMinima = distancia;
                dienteEncontrado = num;
            }
        });

        if (dienteEncontrado) {
            if (currentTool === 'borrar') {
                delete odontogramaData.dientes[dienteEncontrado];
            } else {
                if (!odontogramaData.dientes[dienteEncontrado]) {
                    odontogramaData.dientes[dienteEncontrado] = [];
                }
                odontogramaData.dientes[dienteEncontrado].push({
                    x: x,
                    y: y,
                    tipo: currentTool
                });
            }
            drawOdontograma();
        }
    });

    // Manejar selección de herramientas
    document.querySelectorAll('.tool-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentTool = this.dataset.tool;
        });
    });

    // Validación del formulario
    const form = document.getElementById('createDiagnosticForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar selección de paciente
        if (!document.getElementById('paciente_id').value) {
            showNotification('Por favor, seleccione un paciente', 'error');
            return;
        }

        // Validar descripción
        if (!document.getElementById('descripcion').value.trim()) {
            showNotification('Por favor, ingrese una descripción', 'error');
            return;
        }

        // Guardar datos del odontograma
        document.getElementById('odontogramaData').value = JSON.stringify(odontogramaData);

        // Enviar formulario
        this.submit();
    });

    // Dibujar odontograma inicial
    drawOdontograma();
});
</script>
