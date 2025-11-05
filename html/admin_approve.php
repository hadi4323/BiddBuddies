<?php
session_start();
require '../php files/connection.php'; // Include database connection

// Include CSS file
echo '<link rel="stylesheet" type="text/css" href="../css files/admin_approve.css">';

// Fetch all pending auctions
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $conn->prepare("
        SELECT 
            a.auction_id, a.item_name, a.description, a.starting_bid, a.image_url, 
            a.bid_increment, c.name AS category, l.name AS location
        FROM auctions a
        JOIN categories c ON a.category_id = c.category_id
        JOIN locations l ON a.location_id = l.location_id
        WHERE a.status = 'Pending'
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    include 'nav_admin.php';

    echo "<h1>Pending Auctions</h1>";
    echo "<div class='auction-container'>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='auction-card'>";
            if (!empty($row['image_url'])) {
                echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='Item Image' class='item-image'>";
            }
            echo "<p><strong>Auction ID:</strong> " . htmlspecialchars($row['auction_id']) . "</p>";
            echo "<p><strong>Item Name:</strong> " . htmlspecialchars($row['item_name']) . "</p>";
            echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
            echo "<p><strong>Starting Bid:</strong> $" . htmlspecialchars($row['starting_bid']) . "</p>";
            echo "<p><strong>Bid Increment:</strong> $" . htmlspecialchars($row['bid_increment']) . "</p>";
            echo "<p><strong>Category:</strong> " . htmlspecialchars($row['category']) . "</p>";
            echo "<p><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>";
            echo "<form method='POST' class='action-buttons'>";
            echo "<input type='hidden' name='auction_id' value='" . htmlspecialchars($row['auction_id']) . "'>";
            echo "<button type='submit' name='approve' class='approve-btn'>Approve</button>";
            echo "<button type='submit' name='reject' class='reject-btn'>Reject</button>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "<p class='no-pending'>No pending auctions at the moment.</p>";
    }
    echo "</div>";
    
    $stmt->close();
}

// Handle approval or rejection
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $auctionId = intval($_POST['auction_id']);

    if (isset($_POST['approve'])) {
        $endDate = (new DateTime())->modify('+24 hours')->format('Y-m-d H:i:s');
        $stmt = $conn->prepare("UPDATE auctions SET status = 'Approved', end_date = ? WHERE auction_id = ?");
        $stmt->bind_param("si", $endDate, $auctionId);

        if ($stmt->execute()) {
            echo "<p class='success-msg'>Auction ID $auctionId approved successfully. End time set to $endDate.</p>";
        } else {
            echo "<p class='error-msg'>Failed to approve Auction ID $auctionId.</p>";
        }
        $stmt->close();
    } elseif (isset($_POST['reject'])) {
        $stmt = $conn->prepare("DELETE FROM auctions WHERE auction_id = ?");
        $stmt->bind_param("i", $auctionId);

        if ($stmt->execute()) {
            echo "<p class='success-msg'>Auction ID $auctionId rejected and deleted successfully.</p>";
        } else {
            echo "<p class='error-msg'>Failed to reject Auction ID $auctionId.</p>";
        }
        $stmt->close();
    }
}

$conn->close();
?>


