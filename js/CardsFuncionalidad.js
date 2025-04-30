document.addEventListener("DOMContentLoaded", function () {
    const slides = document.querySelectorAll(".swiper-slide");

    slides.forEach((slide) => {
        slide.addEventListener("click", function (event) {
            // Evita la animación si el clic fue en el botón "Jugar"
            if (event.target.classList.contains("play-button")) {
                return;
            }

            // Alternar la clase "active" (si ya está activa, la oculta)
            if (this.classList.contains("active")) {
                this.classList.remove("active");
            } else {
                slides.forEach((s) => s.classList.remove("active"));
                this.classList.add("active");
            }
        });
    });

    // Redirección cuando se hace clic en el botón "Jugar"
    const playButtons = document.querySelectorAll(".play-button");
    playButtons.forEach((button) => {
        button.addEventListener("click", function (event) {
            event.stopPropagation(); // Evita que el clic en el botón afecte la tarjeta
            window.location.href = "./web/consolitaJuegos.html"; // Cambia la URL a la que quieras redirigir
        });
    });
});
