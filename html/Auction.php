<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiddBuddies</title>
    <link rel="stylesheet" href="../css files/Auction.css">
    <script>
    <?php if (isset($_SESSION['user_id'])): ?>
        sessionStorage.setItem('user_id', <?php echo json_encode($_SESSION['user_id']); ?>);
    <?php endif; ?>
    </script>

</head>
<body>
<?php include 'nav.php'; ?>
    <div class="filter-container">
        <input type="text" id="searchBar" placeholder="Search trades..." />
        <select id="categoryFilter">
          <option value="all">All Categories</option>
          <option value="electronics">Electronics</option>
          <option value="furniture">Furniture</option>
          <option value="clothing">Clothing</option>
          <option value="books">Books</option>
          <option value="real-estate">Real estate</option>
          <option value="vehicle">Vehicle</option>
          <option value="others">Others</option>
         
        </select>
        <select id="locationFilter">
          <option value="all">All Locations</option>
          <option value="new-york">New York</option>
          <option value="san-francisco">San Francisco</option>
          <option value="los-angeles">Los Angeles</option>
          <option value="chicago">Chicago</option>
          <option value="miami">Miami</option>
          <option value="others">Others</option>
        </select>
      </div>
      <div class="auction-container" id="auction-container"></div>
  <button id="deleteSelectedAuctions">Delete Auctions</button>
  <button class="post-auction-btn" id="postAuctionButton">Post Auction</button>

  <div class="auction-form-modal" id="auctionFormModal">
    <div class="auction-form">
      <h2>Post a New Auction</h2>
      <form id="auctionForm" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-column">
            <label for="auctionImage">Product Image:</label>
            <input type="file" id="auctionImage" name="image" accept="image/*" required>
        </div>
        <div class="form-column">
            <label for="auctionName">Product Name:</label>
            <input type="text" id="auctionName" name="item_name" placeholder="Enter product name" required>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-column">
            <label for="auctionDescription">Description:</label>
            <textarea id="auctionDescription" name="description" placeholder="Enter description" required></textarea>
        </div>
        <div class="form-column">
            
            
            
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-column">
            <label for="minimumPrice">Minimum Bid Price:</label>
            <input type="number" id="minimumPrice" name="starting_bid" placeholder="Enter minimum price" required>
        </div>
        <div class="form-column">
            <label for="bidIncrement">Bid Increment:</label>
            <input type="number" id="bidIncrement" name="bid_increment" placeholder="Enter bid increment" required>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-column">
            <label for="location">Location:</label>
            <select id="location" name="location" required>
                <option value="" disabled selected>Select your location</option>
                <option value="1">New York</option>
                <option value="4">San Francisco</option>
                <option value="2">Los Angeles</option>
                <option value="3">Chicago</option>
                <option value="5">Miami</option>
                <option value="6">Others</option>
            </select>
        </div>
        <div class="form-column">
            <label for="auctionCategory">Category:</label>
            <select id="auctionCategory" name="category" required>
                <option value="1">Electronics</option>
                <option value="2">Furniture</option>
                <option value="3">Clothing</option>
                <option value="5">Books</option>
                <option value="6">Real estate</option>
                <option value="7">Vehicle</option>
                <option value="4">Others</option>
            </select>
        </div>
    </div>
    
    <button type="submit" id="postAuctionButton" class="form-submit-btn">Add Auction</button>
</form>


      
      <button class="close-modal-btn" id="closeModalButton">Close</button>
    </div>
  </div>
  
  <footer>
    <div class="footer-content">
        <div class="help-links">
            <p style="font-weight: bold; font-size: 20px;">Help</p>
            <p><a href="#">Terms of Use</a></p>
            <p><a href="#">Privacy Policy</a></p>
        </div>
        <div class="contact-info">
            <p style="font-weight: bold; font-size: 20px;">Contacts</p>
            <p><img src="../Photo/ci_location.png">Beirut, Lebanon</p>
            <p><img src="../Photo/clarity_phone-handset-solid.png"> +961 71 565 438</p>
            <p><img src="../Photo/fluent_mail-16-filled.png"> BiddBuddies@gmail.com</p>
        </div>
        <div class="social-media">
            <p style="font-weight: bold; font-size: 20px; margin-left: 25px;">Social Media</p>
            <a href="https://twitter.com" target="_blank">
                <img src="../Photo/ant-design_twitter-circle-filled.png" alt="Twitter"></a>
                <a href="https://facebook.com" target="_blank">
                <img src="../Photo/entypo-social_facebook-with-circle.png" alt="Facebook"></a>
                <a href="https://instagram.com" target="_blank">
                <img src="../Photo/ant-design_twitter-circle-filled (1).png" alt="Instagram"></a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>Copyright © 2022. All rights reserved.</p>
    </div>
</footer>
<script src="../Js files/Auction.js"></script>
</body>
</html>