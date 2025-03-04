document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".profile-form");
    const usernameInput = form.querySelector("input[name='username']");
    const bioInput = form.querySelector("textarea[name='bio']");
    const countryInput = form.querySelector("input[name='country']");
    const birthDateInput = form.querySelector("input[name='birthdate']");
    
    const usernameRegex = /^.{1,25}$/;
    const bioRegex = /^.{0,100}$/;
    const countryRegex = /^[A-Za-z\s]{1,50}$/;

    // Mostrar error
    function showError(element, message) {
        let errorElement = element.parentElement.querySelector(".error-message");
        if (!errorElement) {
            errorElement = document.createElement("div");
            errorElement.classList.add("error-message");
            element.parentElement.appendChild(errorElement);
        }
        errorElement.textContent = message;
        errorElement.style.display = "block";
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
            return false;
        } else if (birthDate > minBirthDate) {
            showError(birthDateInput, "You must be at least 6 years old.");
            return false;
        } else {
            clearError(birthDateInput);
            return true;
        }
    }

    usernameInput.addEventListener("input", function () {
        if (!usernameRegex.test(usernameInput.value.trim())) {
            showError(usernameInput, "Username must be max 25 characters.");
        } else {
            clearError(usernameInput);
        }
    });

    bioInput.addEventListener("input", function () {
        if (!bioRegex.test(bioInput.value.trim())) {
            showError(bioInput, "Bio must be max 100 characters.");
        } else {
            clearError(bioInput);
        }
    });

    countryInput.addEventListener("input", function () {
        if (!countryRegex.test(countryInput.value.trim())) {
            showError(countryInput, "Country must be max 50 characters and contain only letters.");
        } else {
            clearError(countryInput);
        }
    });

    birthDateInput.addEventListener("input", validateBirthDate);

    form.addEventListener("submit", function (event) {
        let hasError = false;

        if (!usernameRegex.test(usernameInput.value.trim())) {
            showError(usernameInput, "Username must be max 25 characters.");
            hasError = true;
        }
        if (!bioRegex.test(bioInput.value.trim())) {
            showError(bioInput, "Bio must be max 100 characters.");
            hasError = true;
        }
        if (!countryRegex.test(countryInput.value.trim())) {
            showError(countryInput, "Country must be max 50 characters and contain only letters.");
            hasError = true;
        }
        if (!validateBirthDate()) 
        {  
            hasError = true;
        }

        if (hasError) {
            event.preventDefault();
        }
    });
});
