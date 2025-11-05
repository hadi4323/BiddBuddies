<?php
session_start();
require '../php files/connection.php'; // Include database connection

// Fetch all pending trades with joined category and location information
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $conn->prepare("
        SELECT 
            t.trade_id, 
            t.user_id, 
            t.item_name, 
            t.description, 
            t.preferred_item, 
            t.contact_info, 
            t.image_url, 
            t.created_at,
            c.name AS category_name,
            l.name AS location_name
        FROM trades t
        JOIN categories c ON t.category_id = c.category_id
        JOIN locations l ON t.location_id = l.location_id
        WHERE t.status = 'Pending'
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    include 'nav_admin.php';
    echo "<link rel='stylesheet' href='../css files/admin_trade.css'>";
    echo "<h1>Pending Trades</h1>";
    echo "<div class='trade-container'>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='trade-card'>";
            if (!empty($row['image_url'])) {
                echo "<img src='../uploads/" . htmlspecialchars($row['image_url']) . "' alt='Trade Image' class='trade-image'>";
            }
            echo "<p><strong>Trade ID:</strong> " . htmlspecialchars($row['trade_id']) . "</p>";
            echo "<p><strong>User ID:</strong> " . htmlspecialchars($row['user_id']) . "</p>";
            echo "<p><strong>Item Name:</strong> " . htmlspecialchars($row['item_name']) . "</p>";
            echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
            echo "<p><strong>Preferred Item:</strong> " . htmlspecialchars($row['preferred_item']) . "</p>";
            echo "<p><strong>Contact Info:</strong> " . htmlspecialchars($row['contact_info']) . "</p>";
            echo "<p><strong>Category:</strong> " . htmlspecialchars($row['category_name']) . "</p>";
            echo "<p><strong>Location:</strong> " . htmlspecialchars($row['location_name']) . "</p>";
            echo "<p><strong>Created At:</strong> " . htmlspecialchars($row['created_at']) . "</p>";
            echo "<form method='POST' class='action-buttons'>";
            echo "<input type='hidden' name='trade_id' value='" . htmlspecialchars($row['trade_id']) . "'>";
            echo "<button type='submit' name='approve' class='approve-btn'>Approve</button>";
            echo "<button type='submit' name='reject' class='reject-btn'>Reject</button>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "<p class='no-pending'>No pending trades at the moment.</p>";
    }
    echo "</div>";

    $stmt->close();
}

// Handle approval or rejection
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tradeId = intval($_POST['trade_id']);

    if (isset($_POST['approve'])) {
        $stmt = $conn->prepare("UPDATE trades SET status = 'Approved' WHERE trade_id = ?");
        $stmt->bind_param("i", $tradeId);

        if ($stmt->execute()) {
            echo "Trade ID $tradeId approved successfully.<br>";
        } else {
            echo "Failed to approve Trade ID $tradeId.<br>";
        }
        $stmt->close();
    } elseif (isset($_POST['reject'])) {
        $stmt = $conn->prepare("DELETE FROM trades WHERE trade_id = ?");
        $stmt->bind_param("i", $tradeId);

        if ($stmt->execute()) {
            echo "Trade ID $tradeId rejected and deleted successfully.<br>";
        } else {
            echo "Failed to reject Trade ID $tradeId.<br>";
        }
        $stmt->close();
    }
}

$conn->close();
?>

