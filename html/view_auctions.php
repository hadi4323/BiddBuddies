<?php
session_start();
require_once "../php files/connection.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch auctions posted by the logged-in user
$query = "SELECT * FROM auctions WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$auctions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $auctions[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Auctions</title>
    <link rel="stylesheet" href="../css files/view_auctions.css">
</head>
<body>
    <a href="Account.php" id="back-link">&#8592;</a>
    <div class="auctions-container">
        <h1>My Auctions</h1>

        <?php if (count($auctions) > 0): ?>
            <div class="auctions-grid">
                <?php foreach ($auctions as $auction): ?>
                    <div class="auction-card">
                        <img src="<?= htmlspecialchars($auction['image_url']) ?>" alt="Auction Image">
                        <h3><?= htmlspecialchars($auction['item_name']) ?></h3>
                        <p><?= htmlspecialchars($auction['description']) ?></p>
                        <p>Starting Bid: $<?= htmlspecialchars($auction['starting_bid']) ?></p>
                        <p>Bid increment: $<?= htmlspecialchars($auction['bid_increment']) ?></p>
                        <p>Current Bid: $<?= htmlspecialchars($auction['current_highest_bid']) ?></p>
                        <p>Status: <?= htmlspecialchars($auction['status']) ?></p>
                        <p>Ends: <?= htmlspecialchars($auction['end_date']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p id="pa">You have not posted any auctions yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
