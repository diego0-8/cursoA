// Este evento se asegura de que todo el código se ejecute solo cuando
// el documento HTML ha sido completamente cargado y parseado.
document.addEventListener('DOMContentLoaded', function() {

    console.log('El DOM está listo. script.js cargado.');

    // --- Lógica para el Menú Móvil ---
    // Seleccionamos el botón para abrir/cerrar el menú y el menú en sí.
    // Necesitarás añadir los IDs 'mobile-menu-button' y 'mobile-menu' a tu header.php
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            // La función toggle alterna entre añadir y quitar la clase 'hidden'.
            // Tailwind CSS usa la clase 'hidden' para aplicar 'display: none;'.
            mobileMenu.classList.toggle('hidden');
        });
    }


    // --- Lógica para el Menú Desplegable de Usuario ---
    // Esto es para el menú que aparece al hacer clic en la foto de perfil.
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');

    if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', function() {
            userMenu.classList.toggle('hidden');
        });
    }

    // --- Cerrar menús si se hace clic fuera de ellos ---
    document.addEventListener('click', function(event) {
        // Cierra el menú de usuario si no se hizo clic dentro de él o en su botón.
        if (userMenuButton && !userMenuButton.contains(event.target) && userMenu && !userMenu.contains(event.target)) {
            userMenu.classList.add('hidden');
        }
        
        // Puedes añadir una lógica similar para el menú móvil si lo necesitas.
    });


    // --- Ejemplo: Mostrar un mensaje de confirmación antes de una acción ---
    // Seleccionamos todos los botones o enlaces que necesiten confirmación.
    const deleteButtons = document.querySelectorAll('.confirm-action');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // Prevenimos la acción por defecto (ej. seguir un enlace).
            event.preventDefault(); 
            
            const message = button.getAttribute('data-confirm-message') || '¿Estás seguro de que quieres realizar esta acción?';
            
            if (confirm(message)) {
                // Si el usuario confirma, procedemos con la acción original.
                window.location.href = button.href;
            }
        });
    });

});
