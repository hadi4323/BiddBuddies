<?php
// remove_favorite.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

require 'connection.php';
$user_id = $_SESSION['user_id'];

if (isset($_GET['trade_id'])) {
    $trade_id = intval($_GET['trade_id']);
    $query = "DELETE FROM favorites WHERE user_id = ? AND trade_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $user_id, $trade_id);
} elseif (isset($_GET['auction_id'])) {
    $auction_id = intval($_GET['auction_id']);
    $query = "DELETE FROM favorites WHERE user_id = ? AND auction_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $user_id, $auction_id);
}

if ($stmt->execute()) {
    header("Location: ../html/view_favorites.php");
} else {
    echo "Failed to remove favorite.";
}
?>
