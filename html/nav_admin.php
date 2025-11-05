<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
    <div class="logo">
        <img src="../Photo/image.png" alt="BiddBuddies Logo">
    </div>
    <nav>
        <ul>
            <li><a href="admin_approve.php">Auctions</a></li>
            <li><a href="admin_trade.php">Trades</a></li>
            <li><a href="manage_users.php">Users</a></li>
        </ul>
   
    <div class="auth-buttons">
            <a href="Account_admin.php" class="account">Account</a>
    </div>
 </nav>
</header>
