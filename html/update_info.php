<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['verified']) || !$_SESSION['verified']) {
    header("Location: verify_password.php");
    exit();
}

require '../php files/connection.php'; // Include your database connection file

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $new_password = $_POST['new_password'];

    $query = $conn->prepare("UPDATE users SET username = ?, email = ?, phone_number = ?" . 
                             (!empty($new_password) ? ", password = ?" : "") . " WHERE user_id = ?");
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query->bind_param("ssssi", $username, $email, $phone_number, $hashed_password, $user_id);
    } else {
        $query->bind_param("sssi", $username, $email, $phone_number, $user_id);
    }
    $query->execute();
    $query->close();
    $_SESSION['verified'] = false; // Reset the verified status
    header("Location: Account.php");
    exit();
}

// Fetch current user data
$query = $conn->prepare("SELECT username, email, phone_number FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$query->bind_result($username, $email, $phone_number);
$query->fetch();
$query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css files/update_info.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Personal Info</title>
</head>
<body>

<div class="background-blur"></div>
    <form method="POST">
         <a href="Account.php" id="back-link">&#8592;</a>
         <h2>Update Your Information</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        
        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">

        <label for="new_password">New Password (leave blank if unchanged):</label>
        <input type="password" id="new_password" name="new_password">

        <button type="submit">Update Info</button>
    </form>
</body>
</html>
