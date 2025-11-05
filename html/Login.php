<?php


// If the user is already logged in, redirect them to the home page
if (isset($_SESSION['user_id'])) {
    header("Location: Home.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - BiddBuddies</title>
  <link rel="stylesheet" href="../css files/Login.css">
</head>
<body>
  <div class="background-blur"></div>
  <div class="login-container">
    <div class="login-card">
      <h2>Login</h2>
      <form id="login-form"  method="POST" action="../php files/login.php">
        <label for="email">Enter your email</label>
        <input type="email" id="email" name="email" placeholder="Email" required>

        <label for="password">Enter your password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <?php if (!empty($error)) { ?>
    <div class="error-message" style="color: red;"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <a href="../html/ForgotPassword.php" class="forgot-password">Forgot password?</a>

        <button type="submit" class="login-button">Login</button>
      </form>
      <a href="Home.php" class="back-link">&lt; Back</a>
    </div>
  </div>
  <script src="../Js files/LoginP.js"></script>
</body>
</html>

