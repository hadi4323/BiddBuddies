<?php
session_start();
require 'connection.php'; // Include  database connection


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if (isset($_POST['auction_id'], $_POST['action'])) {
    $userId = $_SESSION['user_id'];
    $auctionId = $_POST['auction_id'];
    $action = $_POST['action'];

    if ($action === 'add') {
        // Add to favorites
        $query = "INSERT INTO favorites (user_id, auction_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE user_id=user_id";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $auctionId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to favorites']);
        }
    } elseif ($action === 'remove') {
        // Remove from favorites
        $query = "DELETE FROM favorites WHERE user_id = ? AND auction_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $auctionId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove from favorites']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
