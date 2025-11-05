<?php
session_start();
require 'connection.php';

// Fetch all trades
$stmt = $conn->prepare("
    SELECT 
        t.trade_id, 
        t.item_name, 
        t.description, 
        t.image_url, 
        t.preferred_item, 
        t.contact_info,
        t.status AS trade_status, 
        t.mark AS trade_mark, -- Fetch the 'Mark' field
        c.name AS category, 
        l.name AS location, 
        u.username AS owner, 
        t.user_id AS owner_id
    FROM trades t
    JOIN categories c ON t.category_id = c.category_id
    JOIN locations l ON t.location_id = l.location_id
    JOIN users u ON t.user_id = u.user_id
    WHERE t.status = 'Approved'
");
$stmt->execute();
$result = $stmt->get_result();

$trades = [];
while ($row = $result->fetch_assoc()) {
    $trades[] = [
        'trade_id' => $row['trade_id'],
        'item_name' => $row['item_name'],
        'description' => $row['description'],
        'image' => $row['image_url'],
        'preferred_item' => $row['preferred_item'],
        'contact_info' => $row['contact_info'],
        'status' => $row['trade_status'],
        'mark' => $row['trade_mark'], // Add 'Mark' to the response
        'category' => $row['category'],
        'location' => $row['location'],
        'owner' => $row['owner'],
        'owner_id' => $row['owner_id']
    ];
}

echo json_encode($trades);
$stmt->close();
$conn->close();
?>
