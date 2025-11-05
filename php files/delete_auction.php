<?php
session_start();
require 'connection.php'; // Include your database connection


// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['auction_ids']) && is_array($data['auction_ids'])) {
    $auctionIds = $data['auction_ids'];
    $userId = $_SESSION['user_id']; // Get logged-in user ID
    
    // Begin transaction to delete auctions
    $conn->begin_transaction();

    try {
        foreach ($auctionIds as $auctionId) {
            // Check if the logged-in user is the owner of each auction
            $checkOwnerQuery = "SELECT user_id FROM auctions WHERE auction_id = ?";
            $stmt = $conn->prepare($checkOwnerQuery);
            $stmt->bind_param("i", $auctionId);
            $stmt->execute();
            $result = $stmt->get_result();
            $auction = $result->fetch_assoc();

            if ($auction && $auction['user_id'] == $userId) {
                // Delete the auction if the user is the owner
                $deleteQuery = "DELETE FROM auctions WHERE auction_id = ?";
                $stmt = $conn->prepare($deleteQuery);
                $stmt->bind_param("i", $auctionId);
                $stmt->execute();
            } else {
                throw new Exception('Not the owner of the auction or auction not found');
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
    echo json_encode(['success' => false, 'message' => 'No auction IDs provided']);
}
?>
