<?php
session_start();
require 'connection.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get the raw POST data (JSON)
$input = file_get_contents('php://input'); // Get the raw POST data
$data = json_decode($input, true); // Decode JSON data into an associative array

// Check if trade_id is provided in the request
if (isset($data['trade_id'])) {
    $userId = $_SESSION['user_id'];
    $tradeId = $data['trade_id']; // Use trade_id from the JSON payload

    // Check if the trade exists and belongs to the logged-in user
    $stmt = $conn->prepare("SELECT user_id, status FROM trades WHERE trade_id = ?");
    $stmt->bind_param("i", $tradeId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($ownerId, $status);
        $stmt->fetch();

        // Ensure the logged-in user is the owner of the trade
        if ($ownerId === $userId) {
            // Only update if the trade is not already marked as 'Done'
            if ($status !== 'Done') {
                // Update the trade status to 'Done'
                $updateStmt = $conn->prepare("UPDATE trades SET status = 'Done' WHERE trade_id = ?");
                $updateStmt->bind_param("i", $tradeId);
                if ($updateStmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Trade marked as done']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update trade status']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Trade is already marked as done']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to mark this trade as done']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Trade not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Trade ID not provided']);
}

$conn->close();
?>

