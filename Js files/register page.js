document.getElementById('register-form').addEventListener('submit', function (e) {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const errorMessage = document.getElementById('error-message');

    errorMessage.textContent = ''; // Clear previous error messages
    errorMessage.style.color = 'red';

    // Validate username: Ensure it’s not empty
    if (username.trim() === '') {
        e.preventDefault();
        errorMessage.textContent = 'Username cannot be empty!';
        return;
    }

    // Check if passwords match
    if (password !== confirmPassword) {
        e.preventDefault();
        errorMessage.textContent = 'Passwords do not match!';
        return;
    }

    // Check if password length is sufficient
    if (password.length < 6) {
        e.preventDefault();
        errorMessage.textContent = 'Password must be at least 6 characters long!';
        return;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        errorMessage.textContent = 'Please enter a valid email address!';
        return;
    }

    // Validate phone number length
    if (phone.length < 8 || phone.length > 15) {
        e.preventDefault();
        errorMessage.textContent = 'Phone number must be between 8 and 15 digits!';
        return;
    }
    
});

// Ensure the phone number input only allows numbers
document.getElementById('phone').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
