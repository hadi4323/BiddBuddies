<?php
session_start(); // Start the session

require 'connection.php';

$error = ""; // Initialize error variable

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Input validation
    if (empty($email) || empty($password)) {
        $error = "Please fill in both email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Sanitize email
        $email = htmlspecialchars($email);

        // Check if user exists
        $stmt = $conn->prepare("SELECT user_id, username, password, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check if the user is banned
            if ($user['status'] === 'Banned') {
                $error = "Your account has been banned for violating the rules.";
            } else {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];

                    // Check if the user is an admin
                    $adminStmt = $conn->prepare("SELECT admin_id FROM admin WHERE user_id = ?");
                    $adminStmt->bind_param("i", $user['user_id']);
                    $adminStmt->execute();
                    $adminResult = $adminStmt->get_result();

                    if ($adminResult->num_rows === 1) {
                        // Redirect to the admin page
                        header("Location: ../html/admin_approve.php");
                        exit();
                    }

                    $adminStmt->close();

                    // Redirect to the home page if not an admin
                    header("Location: ../html/Home.php");
                    exit();
                } else {
                    $error = "Incorrect password.";
                }
            }
        } else {
            $error = "No account found with that email.";
        }
        $stmt->close();
    }
}

$conn->close();
include '../html/Login.php'; // Include the HTML form with error message
?>


