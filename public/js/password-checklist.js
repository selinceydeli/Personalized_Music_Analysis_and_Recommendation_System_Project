// public/js/password-checklist.js

const passwordInput = document.getElementById('password');
const checklist = document.getElementById('password-checklist');

passwordInput.addEventListener('input', () => {
    const password = passwordInput.value;
    // Check password requirements and update the checklist accordingly
    const isLengthValid = password.length >= 6;
    const hasSpecialCharacter = /[!@#$%^&*]/.test(password); // Customize this regex for your special character requirements

    if (isLengthValid && hasSpecialCharacter) {
        checklist.textContent = 'Password strength: Strong';
        checklist.classList.remove('text-red-500');
        checklist.classList.add('text-green-500');
    } else {
        let message = 'Password strength: ';
        if (!isLengthValid) {
            message += 'Minimum 6 characters. ';
        }
        if (!hasSpecialCharacter) {
            message += 'At least one special character (!@#$%^&*)';
        }
        checklist.textContent = message;
        checklist.classList.remove('text-green-500');
        checklist.classList.add('text-red-500');
    }
});
