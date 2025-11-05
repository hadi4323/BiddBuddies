<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="../css files/ForgotPassword.css">
</head>
<body>
<div class="background-blur"></div>
  <div class="forgot-password-container">
    <h2>Forgot Password</h2>
    <form action="../php files/ProcessForgotPassword.php" method="POST">
      <label for="email">Enter your registered email address</label>
      <input type="email" id="email" name="email" placeholder="Email" required>
      <button type="submit">Send Reset Link</button><br>
      <a href="Login.php" class="back-link">&lt; Back </a>
    </form>
    
  </div>
</body>
</html>
