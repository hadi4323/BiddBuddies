<?php
session_start();
require 'connection.php'; // Include database connection

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = "You must be logged in to place a bid.";
        echo json_encode($response);
        exit();
    }

    $userId = $_SESSION['user_id'];
    $data = json_decode(file_get_contents("php://input"), true);
    $auctionId = intval($data['auction_id']);

    // Fetch auction details
    $stmt = $conn->prepare("SELECT starting_bid, current_highest_bid, bid_increment, highest_bidder_id, status FROM auctions WHERE auction_id = ?");
    $stmt->bind_param("i", $auctionId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $auction = $result->fetch_assoc();

        if ($auction['status'] !== 'Approved') {
            $response['message'] = "You can only bid on approved auctions.";
            echo json_encode($response);
            exit();
        }

        if ($auction['highest_bidder_id'] === $userId) {
            $response['message'] = "You cannot bid again until another user outbids you.";
            echo json_encode($response);
            exit();
        }

        // Determine the next bid amount
        $currentBid = $auction['current_highest_bid'] ?: $auction['starting_bid'];
        $nextBid = $currentBid + $auction['bid_increment'];

        // Place the bid
        $updateStmt = $conn->prepare("UPDATE auctions SET current_highest_bid = ?, highest_bidder_id = ? WHERE auction_id = ?");
        $updateStmt->bind_param("dii", $nextBid, $userId, $auctionId);

        if ($updateStmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Bid placed successfully!";
        } else {
            $response['message'] = "Failed to place bid.";
        }

        $updateStmt->close();
    } else {
        $response['message'] = "Auction not found.";
    }

    $stmt->close();
} else {
    $response['message'] = "Invalid request method.";
}

$conn->close();
echo json_encode($response);
?>
