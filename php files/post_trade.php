<?php
session_start();
require 'connection.php'; // Include database connection

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = "You must be logged in to post a trade.";
        echo json_encode($response);
        exit();
    }

    $userId = $_SESSION['user_id'];
    $itemName = trim($_POST['item_name']);
    $description = trim($_POST['description']);
    $preferredItem = trim($_POST['preferred_item']);
    $contact_info = trim($_POST['contact_info']);
    $category = intval($_POST['category']);
    $location = intval($_POST['location']);
    

    // Input validation
    if (empty($itemName) || empty($description) || empty($preferredItem) || empty($category) || empty($location)) {
        $response['message'] = "All fields are required.";
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
    INSERT INTO trades (user_id, item_name, description, Preferred_item, contact_info, category_id, location_id, image_url, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");
    $stmt->bind_param("isssssis", $userId, $itemName, $description, $preferredItem, $contact_info, $category, $location, $imageUrl);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Trade posted successfully and is awaiting admin approval.";
    } else {
        $response['message'] = "Failed to post trade.";
    }

    $stmt->close();
} else {
    $response['message'] = "Invalid request method.";
}

$conn->close();
echo json_encode($response);
?>
