document.getElementById('login-form').addEventListener('submit', function (e) {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorMessage = document.getElementById('error-message');

    if (!email || !password) {
        e.preventDefault(); // Prevent form submission
        errorMessage.textContent = 'Email and password are required!';
        errorMessage.style.color = 'red';
    }
});


