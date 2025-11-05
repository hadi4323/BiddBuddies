<?php
session_start();
require '../php files/connection.php'; // Include database connection

// Check if the token is provided in the URL
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $token = $_GET['token'] ?? '';

    if (empty($token)) {
        die("Invalid password reset request.");
    }

    // Validate token in the database
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Invalid or expired token.");
    }

    // Get the user_id from the token for further validation
    $row = $result->fetch_assoc();
    $userId = $row['user_id'];
    $expiry = $row['expiry'];

    // Check if the token has expired (expire time is in UNIX timestamp format)
    if (time() > strtotime($expiry)) {
        die("This token has expired.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css files/ResetPassword.css">
</head>
<body>
<div class="background-blur"></div>
    <div class="reset-password-container">
        <h2>Reset Your Password</h2>
        <form method="POST" action="../php files/ProcessResetPassword.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter new password" required>
            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm new password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
