// public/js/password-toggle-login.js

document.addEventListener("DOMContentLoaded", function () {
    const passwordInputLogin = document.getElementById('password');
    const loginEyeIcon = document.getElementById('login-eye-icon');

    document.getElementById('show-password-login').addEventListener('click', function () {
        togglePasswordVisibility(passwordInputLogin, loginEyeIcon);
    });

    function togglePasswordVisibility(inputField, iconElement) {
        if (inputField.type === 'password') {
            inputField.type = 'text';
            iconElement.classList.remove('fa-eye');
            iconElement.classList.add('fa-eye-slash');
        } else {
            inputField.type = 'password';
            iconElement.classList.remove('fa-eye-slash');
            iconElement.classList.add('fa-eye');
        }
    }
});
