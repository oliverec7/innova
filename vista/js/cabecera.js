document.addEventListener('DOMContentLoaded', () => {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        
        // Función principal de Toggle
        sidebarToggle.addEventListener('click', (e) => {
            e.preventDefault(); // Evita cualquier comportamiento de enlace/formulario por defecto
            
            // 1. Alterna la clase 'toggled' en el menú lateral
            sidebar.classList.toggle('toggled');

            // 2. Opcional: También alterna la clase en el BODY si tu plantilla
            //    (como SB Admin) necesita esto para ajustar el contenido principal.
            //    Si solo ajustas la barra lateral, puedes omitir la línea de abajo.
            document.body.classList.toggle('sidebar-toggled');
        });

        // Manejo Responsivo: Colapsa automáticamente en pantallas pequeñas
        const toggleResponsiveness = () => {
            if (window.innerWidth < 768) { 
                // Colapsa la barra en móvil, pero solo si no tiene 'toggled' 
                // para mantener el estado si el usuario la abrió manualmente.
                // Sin embargo, para un comportamiento estricto de SB Admin, siempre se añade.
                sidebar.classList.add('toggled'); 
                document.body.classList.add('sidebar-toggled'); 
            } else {
                // En pantallas grandes, asegúrate de que el contenido principal no esté forzado.
                // Esto es crucial si la clase 'sidebar-toggled' afecta el ancho del contenido.
                // Por lo general, SB Admin usa este patrón.
                // document.body.classList.remove('sidebar-toggled'); 
            }
        };

        window.addEventListener('resize', toggleResponsiveness);
        toggleResponsiveness(); // Ejecuta al cargar
    }
});