<?php
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize user input
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $phone = htmlspecialchars(trim($_POST['phone']));

    $errorMessage = '';

    // Validate the inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($phone)) {
        $errorMessage = 'All fields are required!';
    } elseif ($password !== $confirmPassword) {
        $errorMessage = 'Passwords do not match!';
    } elseif (strlen($password) < 6) {
        $errorMessage = 'Password must be at least 6 characters!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address!';
    } elseif (strlen($phone) < 8 || strlen($phone) > 15) {
        $errorMessage = 'Phone number must be between 8 and 15 digits!';
    }

    // If there are no validation errors, proceed with registration
    if (empty($errorMessage)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if email or username already exists
        $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            
            $errorMessage = 'Username or email already exists!';
            echo "<script>setTimeout(function() { window.location.href = '../html/register.php'; }, 2000);</script>";
        } else {
            // Insert user data into the database
            $sql = "INSERT INTO users (username, email, password, phone_number) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $phone);

            if ($stmt->execute()) {
                // Redirect to the login page after successful registration
                header("Location: ../html/Login.php");
                exit();
            } else {
                $errorMessage = 'There was an error creating your account. Please try again later.';
            }
        }
    }

    // Display error message if any
    if (!empty($errorMessage)) {
        echo "<div class='error-message'>$errorMessage</div>";
    }
}

$conn->close();
?>


