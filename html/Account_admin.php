<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../html/Login.php");
    exit();
}

// Fetch the username from the session
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : "User";

// Additional setup can go here if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Settings</title>
  <link rel="stylesheet" href="../css files/Account setting.css">
</head>
<body>
  <div class="background-blur"></div>
  <div class="account-settings-container">
    
    <div class="header">
      <a href="admin_approve.php" class="back-button">&#8592;</a>
      <h1 id="username-display"><?php echo $username; ?></h1>
    </div>

    <!-- Personal Information Section -->
    <h2>Edit your personal information</h2>
    <div class="section">
      <a href="verify_admin.php" class="button">Update your personal info</a>
    </div>

    

    <!-- Logout Button -->
    <a href="../php files/logout.php" class="logout-button">Log out</a>
    
  </div>

  <script src="../Js files/Account setting.js"></script>
</body>
</html>

