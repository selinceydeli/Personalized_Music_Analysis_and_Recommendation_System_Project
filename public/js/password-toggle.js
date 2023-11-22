// public/js/password-toggle.js

document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    document.getElementById('show-password').addEventListener('click', function () {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });

    const confirmInput = document.getElementById('password_confirmation');
    const confirmEyeIcon = document.getElementById('confirm-eye-icon');

    document.getElementById('show-confirm-password').addEventListener('click', function () {
        togglePasswordVisibility(confirmInput, confirmEyeIcon);
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

