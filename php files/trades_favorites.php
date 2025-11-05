<?php
session_start();
require 'connection.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if (isset($_POST['trade_id'], $_POST['action'])) {
    $userId = $_SESSION['user_id'];
    $tradeId = $_POST['trade_id'];
    $action = $_POST['action'];

    if ($action === 'add') {
        // Add to favorites
        $query = "
            INSERT INTO favorites (user_id, trade_id) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE user_id=user_id";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $tradeId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to favorites']);
        }
    } elseif ($action === 'remove') {
        // Remove from favorites
        $query = "DELETE FROM favorites WHERE user_id = ? AND trade_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $tradeId);
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
