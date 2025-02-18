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

    const form = document.querySelector(".register-form");

    const firstNameInput = document.getElementById("first-name");
    const lastNameInput = document.getElementById("last-name");
    const ageInput = document.getElementById("age");
    const countryInput = document.getElementById("country");
    const emailInput = document.getElementById("email");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirm-password");

    const nameRegex = /^[A-Za-z\s]{1,15}$/;
    const lastNameRegex = /^[A-Za-z\s]{1,30}$/;
    const countryRegex = /^[A-Za-z\s]{1,50}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const usernameRegex = /^.{1,25}$/;
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

    firstNameInput.addEventListener("input", function () {
        if (!nameRegex.test(firstNameInput.value.trim())) {
            showError(firstNameInput, "First Name must be max 15 characters and contain only letters.");
        } else {
            clearError(firstNameInput);
        }
    });

    lastNameInput.addEventListener("input", function () {
        if (!lastNameRegex.test(lastNameInput.value.trim())) {
            showError(lastNameInput, "Last Name must be max 30 characters and contain only letters.");
        } else {
            clearError(lastNameInput);
        }
    });

    ageInput.addEventListener("input", function () {
        if (isNaN(ageInput.value) || ageInput.value < 0 || ageInput.value > 100) {
            showError(ageInput, "Age must be a number between 0 and 100.");
        } else {
            clearError(ageInput);
        }
    });

    countryInput.addEventListener("input", function () {
        if (!countryRegex.test(countryInput.value.trim())) {
            showError(countryInput, "Country must be max 50 characters and contain only letters.");
        } else {
            clearError(countryInput);
        }
    });

    emailInput.addEventListener("input", function () {
        if (!emailRegex.test(emailInput.value.trim())) {
            showError(emailInput, "Enter a valid email address.");
        } else {
            clearError(emailInput);
        }
    });

    usernameInput.addEventListener("input", function () {
        if (!usernameRegex.test(usernameInput.value.trim())) {
            showError(usernameInput, "Username must be max 25 characters.");
        } else {
            clearError(usernameInput);
        }
    });

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

        if (!nameRegex.test(firstNameInput.value.trim())) {
            showError(firstNameInput, "First Name must be max 15 characters and contain only letters.");
            hasError = true;
        }
        if (!lastNameRegex.test(lastNameInput.value.trim())) {
            showError(lastNameInput, "Last Name must be max 30 characters and contain only letters.");
            hasError = true;
        }
        if (isNaN(ageInput.value) || ageInput.value < 0 || ageInput.value > 100) {
            showError(ageInput, "Age must be a number between 0 and 100.");
            hasError = true;
        }
        if (!countryRegex.test(countryInput.value.trim())) {
            showError(countryInput, "Country must be max 50 characters and contain only letters.");
            hasError = true;
        }
        if (!emailRegex.test(emailInput.value.trim())) {
            showError(emailInput, "Enter a valid email address.");
            hasError = true;
        }
        if (!usernameRegex.test(usernameInput.value.trim())) {
            showError(usernameInput, "Username must be max 25 characters.");
            hasError = true;
        }
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