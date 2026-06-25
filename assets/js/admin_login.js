document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("adminLoginForm");
    const username = document.getElementById("username");
    const password = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");

    // Form validation
    form.addEventListener("submit", function (e) {
        const usernameValue = username.value.trim();
        const passwordValue = password.value.trim();

        if (usernameValue === "" || passwordValue === "") {
            alert("Please fill in all fields.");
            e.preventDefault();
            return;
        }

        if (passwordValue.length < 4) {
            alert("Password must be at least 4 characters.");
            e.preventDefault();
        }
    });

    // Show / Hide password
    togglePassword.addEventListener("click", function () {
        if (password.type === "password") {
            password.type = "text";
            togglePassword.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
        } else {
            password.type = "password";
            togglePassword.innerHTML = '<i class="fa-solid fa-eye"></i>';
        }
    });
});