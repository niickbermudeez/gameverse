document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".mobile-menu-icon");
    const authLinks = document.querySelector(".auth-links");
    const navRight = document.querySelector(".nav-right");
    const body = document.body;

    // Solo continúa si existen los elementos necesarios
    if (!menuToggle || !authLinks || !navRight) return;

    menuToggle.addEventListener("click", function (e) {
        e.stopPropagation(); // evita que se cierre al hacer clic en el mismo icono

        authLinks.classList.toggle("active");
        navRight.classList.toggle("active");

        const isMenuVisible = authLinks.classList.contains("active");
        body.style.overflow = isMenuVisible ? "hidden" : "auto";
    });

    // Cierra el menú si se hace clic fuera
    document.addEventListener("click", function (event) {
        if (!authLinks.contains(event.target) && !menuToggle.contains(event.target)) {
            authLinks.classList.remove("active");
            navRight.classList.remove("active");
            body.style.overflow = "auto";
        }
    });
});
