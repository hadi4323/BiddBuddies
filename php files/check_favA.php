<?php
session_start();
require_once 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Query to fetch favorited auctions for the logged-in user
$sql = "SELECT auction_id FROM favorites WHERE user_id = ? AND auction_id IS NOT NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$favoritedAuctions = [];
while ($row = $result->fetch_assoc()) {
    $favoritedAuctions[] = $row['auction_id'];
}

$stmt->close();
$conn->close();

// Return the list of favorited auctions as JSON
echo json_encode(['success' => true, 'favoritedAuctions' => $favoritedAuctions]);
?>
