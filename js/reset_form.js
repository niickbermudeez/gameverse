document.addEventListener("DOMContentLoaded", function () {
    function togglePasswordVisibility(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);

        if (!input || !button) {
            console.error(`Elemento no encontrado: inputId=${inputId}, buttonId=${buttonId}`);
            return;
        }

        button.addEventListener("click", () => {
            if (input.type === "password") {
                input.type = "text";
                button.textContent = "🙈";
            } else {
                input.type = "password";
                button.textContent = "👁️";
            }
        });
    }

    togglePasswordVisibility("password", "toggle-password");
    togglePasswordVisibility("confirm-password", "toggle-confirm-password");

    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirm-password");
    const form = document.querySelector(".register-form");

    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,@$!%*?&])[A-Za-z\d.,@$!%*?&]{8,}$/;

    function showError(element, message) {
        const errorElement = element.parentElement.querySelector(".error-message");
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        }
    }

    function clearError(element) {
        const errorElement = element.parentElement.querySelector(".error-message");
        if (errorElement) {
            errorElement.textContent = "";
            errorElement.style.display = "none";
        }
    }

    passwordInput.addEventListener("input", function () {
        let tPassword = document.getElementById("toggle-password");
        let tPassword1 = document.getElementById("toggle-confirm-password");
        // toggle-password
        if (!passwordRegex.test(passwordInput.value)) {
            showError(passwordInput, "Password must be at least 8 characters, include uppercase, lowercase, numbers, and special characters.");
            tPassword.style.top="35%";
        } else {
            clearError(passwordInput);
            tPassword.style.top="65%";
        }

        if (confirmPasswordInput.value !== "" && passwordInput.value !== confirmPasswordInput.value) {
        } else {
            clearError(confirmPasswordInput);
        }
    });

    confirmPasswordInput.addEventListener("input", function () {
        let tPassword = document.getElementById("toggle-confirm-password");
        if (passwordInput.value !== confirmPasswordInput.value) {
            showError(confirmPasswordInput, "Passwords do not match.");
            tPassword.style.top="35%";
        } else {
            clearError(confirmPasswordInput);
            tPassword.style.top="65%";
        }
    });

    form.addEventListener("submit", function (event) {
        let hasError = false;

        if (!passwordRegex.test(passwordInput.value)) {
            showError(passwordInput, "Password must be at least 8 characters, include uppercase, lowercase, numbers, and special characters.");
            hasError = true;
        }
        if (passwordInput.value !== confirmPasswordInput.value) {
            showError(confirmPasswordInput, "Passwords do not match.");
            hasError = true;
        }

        if (hasError) {
            event.preventDefault();
        }
    });
});
