<?php
session_start();
require 'connection.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = trim($_POST['token']);
    $userId = trim($_POST['user_id']);
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Input validation
    if (empty($token) || empty($userId) || empty($newPassword) || empty($confirmPassword)) {
        die("All fields are required.");
    }

    if ($newPassword !== $confirmPassword) {
        die("Passwords do not match.");
    }

    // Validate token and user_id in the database
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE user_id = ? AND token = ?");
    $stmt->bind_param("is", $userId, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Invalid or expired token.");
    }

    $row = $result->fetch_assoc();
    $expiry = $row['expiry'];

    // Check if the token has expired (expire time is in UNIX timestamp format)
    if (time() > strtotime($expiry)) {
        die("This token has expired.");
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update the user's password in the database
    $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $updateStmt->bind_param("si", $hashedPassword, $userId);

    if ($updateStmt->execute()) {
        // Delete the token from the password_resets table
        $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $deleteStmt->bind_param("i", $userId);
        $deleteStmt->execute();

        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session
    
    
        // Redirect to login page
        header("Location: ../html/Login.php?reset=success");
        exit();
    } else {
        echo "Failed to reset password. Please try again.";
    }
} else {
    echo "Invalid request method.";
}
?>
