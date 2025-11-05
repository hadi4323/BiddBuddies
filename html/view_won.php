<?php
session_start();
require_once "../php files/connection.php";
 // Make sure to include your DB connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

// Fetch the logged-in user ID
$user_id = $_SESSION['user_id'];

// SQL query to get the auctions won by the logged-in user
$query = "
    SELECT 
        a.auction_id, 
        a.item_name, 
        a.description, 
        a.image_url,  
        a.starting_bid, 
        a.current_highest_bid, 
        a.bid_increment,
        a.end_date,
        a.status, 
        c.name AS category, 
        l.name AS location, 
        u.username AS winner, 
        a.user_id AS owner_id
    FROM auctions a
    JOIN categories c ON a.category_id = c.category_id
    JOIN locations l ON a.location_id = l.location_id
    LEFT JOIN users u ON a.highest_bidder_id = u.user_id
    WHERE a.highest_bidder_id = ? 
    AND a.status = 'Approved' 
    AND a.end_date < NOW(); 
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user has won any auctions
if ($result->num_rows > 0) {
    $auctions = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $auctions = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Won Auctions - BiddBuddies</title>
    <link rel="stylesheet" href="../css files/view_won.css"> <!-- Link to your main CSS file -->
</head>
<body>
<a href="Account.php" id="back-link">&#8592;</a>
    <div class="container">
        <h1>Your Won Auctions</h1>
        <div class="auction-list">
            <?php if (count($auctions) > 0): ?>
                <?php foreach ($auctions as $auction): ?>
                    <div class="auction-card">
                        <img src="<?= htmlspecialchars($auction['image_url']) ?>" alt="Auction Image" class="auction-photo">
                        <h2 class="auction-title"><?= htmlspecialchars($auction['item_name']) ?></h2>
                        <p class="auction-description"><?= htmlspecialchars($auction['description']) ?></p>
                        <p class="auction-category">Category: <?= htmlspecialchars($auction['category']) ?></p>
                        <p class="auction-location">Location: <?= htmlspecialchars($auction['location']) ?></p>
                        <p class="auction-starting-bid">Starting Bid: $<?= number_format($auction['starting_bid'], 2) ?></p>
                        <p class="auction-current-bid">Winning Bid: $<?= number_format($auction['current_highest_bid'], 2) ?></p>
                        <p class="auction-bid-increment">Bid Increment: $<?= number_format($auction['bid_increment'], 2) ?></p>
                        <p class="auction-end-time">End Time: <?= date('F j, Y, g:i a', strtotime($auction['end_date'])) ?></p>
                        <p>Status: <span class="auction-status"><?= htmlspecialchars($auction['status']) ?></span></p>
                        <p class="winner-info">Winner: <?= htmlspecialchars($auction['winner']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p id="pa">You haven't won any auctions yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
