<?php
session_start();
session_unset(); // Clear session variables
session_destroy(); // Destroy the session
header("Location: ../html/Login.php"); // Redirect to login page
exit();
?>
