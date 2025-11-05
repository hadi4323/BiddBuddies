<?php
// view_favorites.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

require '../php files/connection.php'; // Update to your DB connection file
$user_id = $_SESSION['user_id'];

// Fetch favorited trades
$tradeQuery = "
    SELECT t.trade_id, t.item_name, t.description, t.image_url, t.category_id, t.location_id ,t.Mark
    FROM favorites f
    JOIN trades t ON f.trade_id = t.trade_id
    WHERE f.user_id = ? AND f.trade_id IS NOT NULL
";
$tradeStmt = $conn->prepare($tradeQuery);
$tradeStmt->bind_param('i', $user_id);
$tradeStmt->execute();
$trades = $tradeStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch favorited auctions
$auctionQuery = "
    SELECT a.auction_id, a.item_name, a.description, a.image_url, a.starting_bid, a.current_highest_bid, a.end_date
    FROM favorites f
    JOIN auctions a ON f.auction_id = a.auction_id
    WHERE f.user_id = ? AND f.auction_id IS NOT NULL
";
$auctionStmt = $conn->prepare($auctionQuery);
$auctionStmt->bind_param('i', $user_id);
$auctionStmt->execute();
$auctions = $auctionStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites</title>
    <link rel="stylesheet" href="../css files/view_favorites.css">
</head>
<body>
    <a href="Account.php" id="back-link">&#8592;</a>
    
    <div class="favorites-container">
        <h1>My Favorites</h1>

        <!-- Favorites Columns (Trades on the left, Auctions on the right) -->
        <div class="favorites-columns">
            <!-- Favorited Trades Section -->
            <section class="favorites-trades">
               
                <div class="favorites-grid">
                <h2>Favorited Trades</h2>
                    <?php if (count($trades) > 0): ?>
                        <?php foreach ($trades as $trade): ?>
                            <div class="favorite-card">
                                <img src="<?= htmlspecialchars($trade['image_url']) ?>" alt="Trade Image">
                                <h3><?= htmlspecialchars($trade['item_name']) ?></h3>
                                <p><?= htmlspecialchars($trade['description']) ?></p>
                                <p><?= htmlspecialchars($trade['Mark']) ?></p>
                                <a href="../php files/remove_fav.php?trade_id=<?= $trade['trade_id'] ?>" class="button">Remove</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No favorited trades yet.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Favorited Auctions Section -->
            <section class="favorites-auctions">
                
                <div class="favorites-grid">
                <h2>Favorited Auctions</h2>
                    <?php if (count($auctions) > 0): ?>
                        <?php foreach ($auctions as $auction): ?>
                            <div class="favorite-card">
                                <img src="<?= htmlspecialchars($auction['image_url']) ?>" alt="Auction Image">
                                <h3><?= htmlspecialchars($auction['item_name']) ?></h3>
                                <p><?= htmlspecialchars($auction['description']) ?></p>
                                <p>Current Bid: $<?= htmlspecialchars($auction['current_highest_bid']) ?></p>
                                <p>Ends: <?= htmlspecialchars($auction['end_date']) ?></p>
                                <a href="../php files/remove_fav.php?auction_id=<?= $auction['auction_id'] ?>" class="button">Remove</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No favorited auctions yet.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>

