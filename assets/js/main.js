// Funciones comunes para el sistema de gestión dental

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type} transform translate-y-0`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateY(-1rem)';
    }, 100);
    
    // Eliminar después de 3 segundos
    setTimeout(() => {
        notification.style.transform = 'translateY(1rem)';
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Función para confirmar acciones
async function confirmAction(message) {
    return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 overflow-y-auto';
        modal.innerHTML = `
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Confirmar acción
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        ${message}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="btn-danger sm:ml-3 sm:w-auto" id="confirmYes">
                            Confirmar
                        </button>
                        <button type="button" class="btn-secondary mt-3 sm:mt-0 sm:w-auto" id="confirmNo">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        document.getElementById('confirmYes').addEventListener('click', () => {
            document.body.removeChild(modal);
            resolve(true);
        });
        
        document.getElementById('confirmNo').addEventListener('click', () => {
            document.body.removeChild(modal);
            resolve(false);
        });
    });
}

// Función para formatear fechas
function formatDate(date) {
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Función para formatear moneda
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

// Función para validar formularios
function validateForm(form, rules) {
    const errors = {};
    
    for (const field in rules) {
        const value = form[field].value;
        const fieldRules = rules[field];
        
        if (fieldRules.required && !value) {
            errors[field] = 'Este campo es requerido';
            continue;
        }
        
        if (fieldRules.email && value && !value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            errors[field] = 'Email inválido';
        }
        
        if (fieldRules.minLength && value.length < fieldRules.minLength) {
            errors[field] = `Mínimo ${fieldRules.minLength} caracteres`;
        }
        
        if (fieldRules.maxLength && value.length > fieldRules.maxLength) {
            errors[field] = `Máximo ${fieldRules.maxLength} caracteres`;
        }
        
        if (fieldRules.pattern && !value.match(new RegExp(fieldRules.pattern))) {
            errors[field] = fieldRules.message || 'Formato inválido';
        }
    }
    
    return errors;
}

// Event listeners globales
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', e => {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = e.target.dataset.tooltip;
            
            const rect = e.target.getBoundingClientRect();
            tooltip.style.top = `${rect.top - 30}px`;
            tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
            
            document.body.appendChild(tooltip);
        });
        
        element.addEventListener('mouseleave', () => {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) {
                document.body.removeChild(tooltip);
            }
        });
    });
    
    // Inicializar dropdowns
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', e => {
            const menu = e.target.nextElementSibling;
            menu.classList.toggle('hidden');
            
            // Cerrar al hacer clic fuera
            const closeMenu = (event) => {
                if (!menu.contains(event.target) && event.target !== e.target) {
                    menu.classList.add('hidden');
                    document.removeEventListener('click', closeMenu);
                }
            };
            
            document.addEventListener('click', closeMenu);
        });
    });
});
