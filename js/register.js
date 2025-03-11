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

    const form = document.querySelector(".register-form");

    const firstNameInput = document.getElementById("first-name");
    const lastNameInput = document.getElementById("last-name");
    const birthDateInput = document.getElementById("birth-date"); 
    // const countryInput = document.getElementById("country");
    const emailInput = document.getElementById("email");
    const usernameInput = document.getElementById("username");

    const nameRegex = /^[A-Za-z\s]{1,15}$/;
    const lastNameRegex = /^[A-Za-z\s]{1,30}$/;
    // const countryRegex = /^[A-Za-z\s]{1,50}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const usernameRegex = /^.{1,25}$/;

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

    function validateBirthDate() {
        const birthDateValue = birthDateInput.value;
        if (!birthDateValue) return;

        const birthDate = new Date(birthDateValue);
        const today = new Date();

        const minBirthDate = new Date();
        minBirthDate.setFullYear(today.getFullYear() - 6);

        if (birthDate > today) {
            showError(birthDateInput, "Birth date cannot be in the future.");
            console.log("Asdf");
            return false;
        } else if (birthDate > minBirthDate) {
            showError(birthDateInput, "You must be at least 6 years old.");
            console.log("Asdf");
            return false;
        } else {
            clearError(birthDateInput);
            return true;
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

    // countryInput.addEventListener("input", function () {
    //     if (!countryRegex.test(countryInput.value.trim())) {
    //         showError(countryInput, "Country must be max 50 characters and contain only letters.");
    //     } else {
    //         clearError(countryInput);
    //     }
    // });

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

    birthDateInput.addEventListener("input", validateBirthDate);

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
        if (!validateBirthDate()) {
            hasError = true;
        }

        if (hasError) {
            event.preventDefault();
        }
    });
});
