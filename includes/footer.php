</main>
        </div>
    </div>

    <script>
        // Toggle del sidebar en móvil
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('open');
        });

        // Cerrar sidebar al hacer clic fuera en móvil
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (sidebar.classList.contains('open') && 
                !sidebar.contains(event.target) && 
                !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('open');
            }
        });

        // Inicializar dropdowns del navbar
        document.querySelectorAll('.dropdown-toggle').forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                const menu = this.nextElementSibling;
                menu.classList.toggle('hidden');

                // Cerrar otros dropdowns
                document.querySelectorAll('.dropdown-toggle').forEach(other => {
                    if (other !== this) {
                        other.nextElementSibling.classList.add('hidden');
                    }
                });
            });
        });

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.dropdown-toggle').forEach(dropdown => {
                const menu = dropdown.nextElementSibling;
                if (!dropdown.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
