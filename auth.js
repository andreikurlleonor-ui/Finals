/* =================================
   PASSWORD VISIBILITY
================================= */

const toggleButtons = document.querySelectorAll(".toggle-password");

toggleButtons.forEach(function(button) {

    button.addEventListener("click", function() {

        const inputId = button.getAttribute("data-toggle");
        const passwordInput = document.getElementById(inputId);

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            button.textContent = "Hide";
        } else {
            passwordInput.type = "password";
            button.textContent = "Show";
        }

    });

});

const loginMessage = document.getElementById("loginMessage");

if (loginMessage && new URLSearchParams(window.location.search).has("error")) {
    loginMessage.textContent = "Invalid email or password. Please try again.";
    loginMessage.className = "form-message error";
}


/* =================================
   REGISTRATION
================================= */

const registerForm = document.getElementById("registerForm");

if (registerForm) {

    registerForm.addEventListener("submit", async function(event) {

        event.preventDefault();

        const name = document.getElementById("studentName").value.trim();
        const email = document.getElementById("registerEmail").value.trim();
        const password = document.getElementById("registerPassword").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        const message = document.getElementById("registerMessage");

        if (!name || !email || !password || !confirmPassword) {
            message.textContent = "Please complete all fields.";
            message.className = "form-message error";
            return;
        }

        if (password.length < 8) {
            message.textContent = "Your password must have at least 8 characters.";
            message.className = "form-message error";
            return;
        }

        if (password !== confirmPassword) {
            message.textContent = "Your passwords do not match. Please try again.";
            message.className = "form-message error";
            return;
        }

        const submitButton = registerForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        try {
            const response = await fetch(registerForm.action, {
                method: "POST",
                body: new FormData(registerForm)
            });
            const result = await response.json();

            if (!response.ok || result.success !== true) {
                message.textContent = result.error || "Registration failed. Please try again.";
                message.className = "form-message error";
                return;
            }

            message.textContent = "Your account was created successfully.";
            message.className = "form-message success";
            registerForm.reset();
        } catch (error) {
            console.error("Could not register account:", error);
            message.textContent = "Could not connect to the server. Please try again.";
            message.className = "form-message error";
        } finally {
            submitButton.disabled = false;
        }

    });

}
