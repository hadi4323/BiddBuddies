<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/Login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../php files/connection.php'; // Include your database connection file

    $user_id = $_SESSION['user_id'];
    $entered_password = $_POST['password'];

    // Fetch the user's password hash
    $query = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $query->bind_result($hashed_password);
    $query->fetch();
    $query->close();

    // Verify the password
    if (password_verify($entered_password, $hashed_password)) {
        $_SESSION['verified'] = true;
        header("Location: update_admin.php");
        exit();
    } else {
        $error_message = "Incorrect password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css files/verify_pass.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Password</title>
</head>
<body>
<div class="background-blur"></div>
   <a href="Account_admin.php" id="back-link">&#8592;</a>
    <form method="POST">
    
    <h2>Veify Your Password before continuing</h2>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
        <button type="submit">Verify</button>
        <p><a href="ForgotPasswordA.php">Forgot Password?</a></p>
    </form>
    
</body>
</html>
