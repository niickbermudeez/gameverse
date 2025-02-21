document.addEventListener("DOMContentLoaded", function () {
    function togglePasswordVisibility(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);

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


    const nameRegex = /^[A-Za-z\s]{1,15}$/;
    const lastNameRegex = /^[A-Za-z\s]{1,30}$/;
    const countryRegex = /^[A-Za-z\s]{1,50}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const usernameRegex = /^.{1,25}$/;
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,@$!%*?&])[A-Za-z\d.,@$!%*?&]{8,}$/;

    function showError(element, message) {

        console.log("showError");

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
        if (!passwordRegex.test(passwordInput.value)) {
            showError(passwordInput, "Password must be at least 8 characters, include uppercase, lowercase, numbers, and special characters.");
            document.getElementById("toggle-password").style.top="35%";
        } else {
            clearError(passwordInput);
            document.getElementById("toggle-password").style.top="65%";
        }

        if (confirmPasswordInput.value !== "" && passwordInput.value !== confirmPasswordInput.value) {
            showError(confirmPasswordInput, "Passwords do not match. Check them both to solve the differences.");
            document.getElementById("toggle-confirm-password").style.top="35%";
        } else {
            clearError(confirmPasswordInput);
            document.getElementById("toggle-confirm-password").style.top="65%";
        }
    });

    confirmPasswordInput.addEventListener("input", function () {
        if (passwordInput.value !== confirmPasswordInput.value) {
            showError(confirmPasswordInput, "Passwords do not match. Check them both to solve the differences.");
            document.getElementById("toggle-confirm-password").style.top="35%";
        } else {
            clearError(confirmPasswordInput);
            document.getElementById("toggle-confirm-password").style.top="65%";
        }
    });

    form.addEventListener("submit", function (event) {
        let hasError = false;
        
        if (!passwordRegex.test(passwordInput.value)) {
            showError(passwordInput, "Password must be at least 8 characters, include uppercase, lowercase, numbers, and special characters.");
            hasError = true;
            document.getElementById("toggle-password").style.top="35%";
        }
        if (passwordInput.value !== confirmPasswordInput.value) {
            showError(confirmPasswordInput, "Passwords do not match. Check them both to solve the differences.");
            hasError = true;
            document.getElementById("toggle-confirm-password").style.top="35%";
        }

        if (hasError) {
            event.preventDefault();
        }
    });
});