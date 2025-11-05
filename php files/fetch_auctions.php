<?php
session_start();
require 'connection.php';

// Fetch all approved auctions
$stmt = $conn->prepare("
    SELECT 
        a.auction_id, 
        a.item_name, 
        a.description, 
        a.image_url,  
        a.starting_bid, 
        a.current_highest_bid, 
        a.bid_increment,
        a.end_date, -- Use this as the end time
        a.status, 
        c.name AS category, 
        l.name AS location, 
        u.username AS winner, 
        a.user_id AS owner_id
    FROM auctions a
    JOIN categories c ON a.category_id = c.category_id
    JOIN locations l ON a.location_id = l.location_id
    LEFT JOIN users u ON a.highest_bidder_id = u.user_id
    WHERE a.status = 'Approved'
");
$stmt->execute();
$result = $stmt->get_result();

$auctions = [];
while ($row = $result->fetch_assoc()) {
    // Format the end_date for display
    $endDate = new DateTime($row['end_date']);
    $formattedEndDate = $endDate->format('Y-m-d H:i:s');

    $auctions[] = [
        'auction_id' => $row['auction_id'],
        'item_name' => $row['item_name'],
        'description' => $row['description'],
        'image_url' => $row['image_url'],
        'starting_bid' => $row['starting_bid'],
        'current_highest_bid' => $row['current_highest_bid'],
        'bid_increment' => $row['bid_increment'],
        'end_date' => $formattedEndDate,
        'status' => $row['status'],
        'category' => $row['category'],
        'location' => $row['location'],
        'winner' => $row['winner'],
        'owner_id' => $row['owner_id']
    ];
}

echo json_encode($auctions);
$stmt->close();
$conn->close();
?>
