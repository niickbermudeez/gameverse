document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".mobile-menu-icon");
    const menuMobile = document.querySelector(".mobile-menu");
    const body = document.body;

    menuToggle.addEventListener("click", function () {
        if (window.getComputedStyle(menuMobile).display === "flex") {
            menuMobile.style.display = "none";
            body.style.overflow = "auto";
        } else {
            menuMobile.style.display = "flex";
            body.style.overflow = "hidden";
        }
    });

    document.addEventListener("click", function (event) {
        if (!menuMobile.contains(event.target) && !menuToggle.contains(event.target)) {
            menuMobile.style.display = "none";
            body.style.overflow = "auto";
        }
    });
});