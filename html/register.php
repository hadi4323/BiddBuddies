<?php
session_start();

// If the user is already logged in, redirect them to the home page
if (isset($_SESSION['user_id'])) {
    header("Location: ../html/Home.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="../css files/register page.css">
</head>
<body>
  <div class="background"></div>
  <div class="form-container">
    <h1>Create Your Account</h1>
   
    <form id="register-form" method="POST" action="../php files/register.php">
        <label for="username">Enter your username</label>
        <input type="text" id="username" name="username" placeholder="Username" required>
      
        <label for="email">Enter your email</label>
        <input type="email" id="email" name="email" placeholder="Email" required>
      
        <label for="password">Enter your password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>
      
        <label for="confirm-password">Confirm your password</label>
        <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm password" required>
      
        <label for="phone">Enter your number</label>
        <input 
          type="tel" 
          id="phone" 
          name="phone" 
          placeholder="Phone number" 
          pattern="[0-9]+" 
          required 
          title="Please enter numbers only"
        />
      
        <button type="submit" class="submit-btn">Create Account</button>
      </form>
      <div id="error-message" class="error-message"></div>
      
    <a href="Home.php" class="back-link">&lt; Back</a>
  </div>
  <script src="../Js files/register page.js"></script>
</body>
</html>

