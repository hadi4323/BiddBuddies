<?php
session_start();
require_once "../php files/connection.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch trades posted by the logged-in user
$query = "SELECT * FROM trades WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$trades = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $trades[] = $row;
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
    <title>My Trades</title>
    <link rel="stylesheet" href="../css files/view_trades.css">
</head>
<body>
    <a href="Account.php" id="back-link">&#8592;</a>
    <div class="trades-container">
        <h1>My Trades</h1>

        <?php if (count($trades) > 0): ?>
            <div class="trades-grid">
                <?php foreach ($trades as $trade): ?>
                    <div class="trade-card">
                        <img src="<?= htmlspecialchars($trade['image_url']) ?>" alt="Trade Image">
                        <h3><?= htmlspecialchars($trade['item_name']) ?></h3>
                        <p><?= htmlspecialchars($trade['description']) ?></p>
                        <p>Preferred Item: <?= htmlspecialchars($trade['preferred_item']) ?></p>
                        <p>Contact info: <?= htmlspecialchars($trade['contact_info']) ?></p>
                        <p>Mark: <?= htmlspecialchars($trade['Mark']) ?></p>
                        <p>Status: <?= htmlspecialchars($trade['status']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>You have not posted any trades yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
