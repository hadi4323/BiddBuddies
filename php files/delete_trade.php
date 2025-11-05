<?php
session_start();
require 'connection.php'; // Include your database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Check if trade_ids are provided and if it's an array
if (isset($data['trade_ids']) && is_array($data['trade_ids'])) {
    $tradeIds = $data['trade_ids'];
    $userId = $_SESSION['user_id']; // Get logged-in user ID

    // Begin transaction to delete trades
    $conn->begin_transaction();

    try {
        foreach ($tradeIds as $tradeId) {
            // Check if the logged-in user is the owner of each trade
            $checkOwnerQuery = "SELECT user_id FROM trades WHERE trade_id = ?";
            $stmt = $conn->prepare($checkOwnerQuery);
            $stmt->bind_param("i", $tradeId);
            $stmt->execute();
            $result = $stmt->get_result();
            $trade = $result->fetch_assoc();

            if ($trade && $trade['user_id'] == $userId) {
                // Delete the trade if the user is the owner
                $deleteQuery = "DELETE FROM trades WHERE trade_id = ?";
                $stmt = $conn->prepare($deleteQuery);
                $stmt->bind_param("i", $tradeId);
                $stmt->execute();
            } else {
                throw new Exception('Not the owner of the trade or trade not found');
            }
        }

        // Commit transaction if everything is successful
        $conn->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        // Rollback transaction if there is an error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No trade IDs provided']);
}
?>
