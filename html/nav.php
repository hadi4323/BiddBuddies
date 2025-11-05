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
            <li><a href="Home.php">Home</a></li>
            <li><a href="Auction.php">Auctions</a></li>
            <li><a href="Trades.php">Trades</a></li>
            <li><a href="About.php">About</a></li>
        </ul>
    
    <div class="auth-buttons">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="Account.php" class="account">Account</a>
        <?php else: ?>
            <a href="register.php" class="signup">Signup</a>
            <a href="Login.php" class="login">Login</a>
        <?php endif; ?>
    </div>
</nav>
</header>
