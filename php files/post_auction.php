<?php
session_start();
require 'connection.php'; // Include database connection

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = "You must be logged in to post an auction.";
        echo json_encode($response);
        exit();
    }

    // Input validation
    $userId = $_SESSION['user_id'];
    $itemName = trim($_POST['item_name']);
    $description = trim($_POST['description']);
    $startingBid = floatval($_POST['starting_bid']);
    $categoryId = intval($_POST['category']);
    $locationId = intval($_POST['location']);
    $bidIncrement = floatval($_POST['bid_increment']);

    if (empty($itemName) || empty($description) || $startingBid <= 0 || empty($categoryId) || empty($locationId) || empty($bidIncrement)) {
        $response['message'] = "All fields are required and must be valid.";
        echo json_encode($response);
        exit();
    }

    // Handle image upload
    $imageUrl = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../uploads/';
        $imageName = basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $imageUrl = $imagePath;
        } else {
            $response['message'] = "Failed to upload image.";
            echo json_encode($response);
            exit();
        }
    }

    // Insert into database
    $stmt = $conn->prepare("
    INSERT INTO auctions (user_id, item_name, description, starting_bid, bid_increment, category_id, location_id, image_url, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
");
    $stmt->bind_param("issddiss", $userId, $itemName, $description, $startingBid, $bidIncrement, $categoryId, $locationId, $imageUrl);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Auction posted successfully and awaiting admin approval, it will go live for 24h after approval.";
    } else {
        $response['message'] = "Failed to post auction.";
    }

    $stmt->close();
} else {
    $response['message'] = "Invalid request method.";
}

$conn->close();
echo json_encode($response);
?>

