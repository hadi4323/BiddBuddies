// Modal Elements
const postAuctionButton = document.getElementById("postAuctionButton");
const auctionFormModal = document.getElementById("auctionFormModal");
const closeModalButton = document.getElementById("closeModalButton");
const auctionForm = document.getElementById("auctionForm");
const auctionContainer = document.getElementById("auction-container");
const deleteSelectedButton = document.getElementById("deleteSelectedAuctions");
const loggedInUserId = parseInt(sessionStorage.getItem('user_id')) || null;


window.addEventListener("DOMContentLoaded", () => {auctionFormModal.style.display="none";});


// Open Modal
postAuctionButton.addEventListener("click", () => {
  if (loggedInUserId) {
    auctionFormModal.style.display = "flex";
  } else {
    alert("You need to log in to post an auction.");
  }
});



// Close Modal
closeModalButton.addEventListener("click", () => {
  auctionFormModal.style.display = "none";
});

// Fetch Auctions from Database
function fetchAuctions() {
  fetch('../php files/fetch_auctions.php')
    .then(response => response.json())
    .then(data => {
      console.log(data);
      
      auctionContainer.innerHTML = ''; // Clear existing auctions

      data.forEach(auction => {
        console.log(auction.starting_bid);
        const auctionCard = createAuctionCard(auction);
        auctionContainer.appendChild(auctionCard);
      });
    })
    .catch(error => console.error('Error fetching auctions:', error));
}

document.addEventListener("DOMContentLoaded", () => {
  fetch("../php files/check_favA.php")
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              const favoritedAuctions = data.favoritedAuctions;
              favoritedAuctions.forEach(auctionId => {
                  const favoriteButton = document.querySelector(`#favorite-${auctionId}`);
                  if (favoriteButton) {
                      favoriteButton.classList.add("favorited");
                  }
              });
          } else {
              console.error(data.message);
          }
      })
      .catch(error => console.error("Error fetching favorited auctions:", error));
});


// Submit Auction Form
auctionForm.addEventListener("submit", (e) => {
  e.preventDefault();

  const formData = new FormData(auctionForm);

  fetch('../php files/post_auction.php', {
    method: 'POST',
    body: formData,
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Auction posted successfully and awaiting approval.");
        auctionFormModal.style.display = "none";
        auctionForm.reset();
        fetchAuctions(); // Refresh auctions
      } else {
        alert(data.message || "Failed to post auction.");
      }
    })
    .catch(error => console.error('Error posting auction:', error));
});

// Create Auction Card
function createAuctionCard(auction) {
  const auctionCard = document.createElement("div");
  auctionCard.className = "auction-card";
  auctionCard.setAttribute("data-category", auction.category.toLowerCase().trim());
  auctionCard.setAttribute("data-location", auction.location.toLowerCase().trim().replace(/\s+/g, '-'));
  console.log("Creating auction card with data:", auction);
  // Ensure starting_bid is a number
  const startingBid = parseFloat(auction.starting_bid) || 0; // Default to 0 if invalid
  const currentBid = auction.current_highest_bid ? parseFloat(auction.current_highest_bid) : 0;
  const bidIncrement = parseFloat(auction.bid_increment);
  const loggedInUserId = parseInt(sessionStorage.getItem('user_id'));
  const auctionOwnerId = auction.owner_id;

  // Add checkbox for the logged-in user's own auctions
  let checkboxHTML = '';
  if (auctionOwnerId === loggedInUserId) {
    checkboxHTML = `<input type="checkbox" class="delete-checkbox" data-auction-id="${auction.auction_id}"> `;
  }
  const endTime = new Date(auction.end_date).getTime();

  auctionCard.innerHTML = `
      ${checkboxHTML}
      ${auction.owner_id !== loggedInUserId ? `<button class="favorite-btn ${auction.is_favorited ? 'favorited' : ''}" id="favorite-${auction.auction_id}" data-auction-id="${auction.auction_id}">&#9825;</button>` : ''}
      <img src="${auction.image_url}" alt="Auction Item" class="auction-photo" id="auction-photo-${auction.auction_id}">
      <h2 class="auction-title" id="auction-title-${auction.auction_id}">${auction.item_name}</h2>
      <p class="auction-description" id="auction-description-${auction.auction_id}">${auction.description}</p>
      <p class="auction-category" id="auction-category-${auction.auction_id}">Category: ${auction.category}</p>
      <p class="auction-location" id="auction-location-${auction.auction_id}">Location: ${auction.location}</p>
      <p class="auction-starting-bid" id="auction-starting-bid-${auction.auction_id}">Starting Bid: $${startingBid.toFixed(2)}</p>
      <p class="auction-current-bid" id="auction-current-bid-${auction.auction_id}">Current Bid: $${currentBid > 0 ? currentBid.toFixed(2) : "None"}</p>
      <p class="auction-bid-increment" id="auction-bid-increment-${auction.auction_id}">Bid Increment: $${bidIncrement.toFixed(2)}</p>
      <p class="auction-end-time" id="auction-end-time-${auction.auction_id}">End Time: ${new Date(endTime).toLocaleString()}</p>
      <p>Status: <span class="auction-status" id="auction-status-${auction.auction_id}">${auction.status}</span></p>
      <p class="winner-info" id="winner-info-${auction.auction_id}">${auction.status === "Ended" ? `Winner: ${auction.winner || "No bids"}` : ""}</p>
      ${auction.owner_id === loggedInUserId ? '' : `<button class="bid-now-btn" id="bid-now-${auction.auction_id}" data-auction-id="${auction.auction_id}">Bid Now</button>`}
      
      `;
    
      const favoriteButton = auctionCard.querySelector(".favorite-btn");
      if (favoriteButton) {
        favoriteButton.addEventListener("click", () => {
          toggleFavorite(auction.auction_id, favoriteButton);
        });
      }

  const bidNowButton = auctionCard.querySelector(".bid-now-btn");

  if (bidNowButton) {
    bidNowButton.addEventListener("click", () => {
      placeBid(auction.auction_id);
    });
  }

 

  // Add the countdown timer for auction status
  const countdownInterval = setInterval(() => {
    const timeRemaining = endTime - new Date().getTime();

    if (timeRemaining <= 0) {
      clearInterval(countdownInterval);
      auctionCard.querySelector(".auction-status").textContent = "Ended";
      auctionCard.querySelector(".winner-info").textContent = `Winner: ${auction.winner || "No bids"}`;
      if (bidNowButton) {
        bidNowButton.disabled = true;
      }
    } else {
      auctionCard.querySelector(".auction-status").textContent = "Live";
    }
  }, 1000);

  return auctionCard;
}




// Place Bid
function placeBid(auctionId) {
  fetch('../php files/place_bid.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify({ auction_id: auctionId }),
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          alert("Bid placed successfully!");
          fetchAuctions(); // Refresh auctions to update the current bid
      } else {
          alert(data.message || "Failed to place bid.");
      }
  })
  .catch(error => console.error('Error placing bid:', error));
}


// Format Time Remaining
function formatTimeRemaining(milliseconds) {
  const minutes = Math.floor((milliseconds / 1000 / 60) % 60);
  const seconds = Math.floor((milliseconds / 1000) % 60);
  return `${minutes}m ${seconds}s`;
}

// Initial Fetch
fetchAuctions();



  const searchBar = document.getElementById("searchBar");
  const categoryFilter = document.getElementById("categoryFilter");
  const locationFilter = document.getElementById("locationFilter");
  const auctionContainer1 = document.querySelector(".auction-container");

  // Function to filter auctions
  function filterAuctions() {
    const searchQuery = searchBar.value.toLowerCase().trim();
    const selectedCategory = categoryFilter.value.toLowerCase().trim();
    const selectedLocation = locationFilter.value.toLowerCase().trim().replace(/\s+/g, '-');
  
    const auctionCards = auctionContainer.querySelectorAll(".auction-card");
    
    auctionCards.forEach((card) => {
      const itemName = card.querySelector(".auction-title").textContent.toLowerCase().trim();
      const category = card.getAttribute("data-category");
      const location = card.getAttribute("data-location");
  
      const matchesSearch = itemName.includes(searchQuery);
      const matchesCategory = selectedCategory === "all" || category === selectedCategory;
      const matchesLocation = selectedLocation === "all" || location === selectedLocation;
  
      console.log({
        itemName,
        category,
        location,
        selectedCategory,
        selectedLocation,
        matchesSearch,
        matchesCategory,
        matchesLocation,
      });
  
      if (matchesSearch && matchesCategory && matchesLocation) {
        card.style.display = "block"; // Make sure to show the card
      } else {
        card.style.display = "none"; // Hide the card if not matching
      }
    });
  }
  
  
  


  // Attach event listeners
  searchBar.addEventListener("input", filterAuctions);
  categoryFilter.addEventListener("change", filterAuctions);
  locationFilter.addEventListener("change", filterAuctions);
 
  document.addEventListener("DOMContentLoaded", function () {
    // Handle the deletion when "Delete Auctions" button is clicked
    document.getElementById("deleteSelectedAuctions").addEventListener("click", function() {
      if (!loggedInUserId) {
        alert("You need to log in to delete auctions.");
        return; // Stop further execution if the user is not logged in
      }
  
      const selectedAuctions = [];
      const checkboxes = document.querySelectorAll('.delete-checkbox:checked');
      
      // Collect selected auction IDs
      checkboxes.forEach(checkbox => {
        const auctionId = checkbox.getAttribute("data-auction-id");
        selectedAuctions.push(auctionId);
      });
  
      if (selectedAuctions.length > 0) {
        deleteSelectedAuctions(selectedAuctions);
      } else {
        alert("Please select at least one auction to delete.");
      }
    });
  });
  
  
  function deleteSelectedAuctions(auctionIds) {
    fetch('../php files/delete_auction.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ auction_ids: auctionIds })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Selected auctions deleted successfully!");
        fetchAuctions(); // Refresh the list of auctions
      } else {
        alert("Failed to delete selected auctions.");
      }
    })
    .catch(error => console.error('Error deleting selected auctions:', error));
  }
  
  function toggleFavorite(auctionId, button) {
    const userId = sessionStorage.getItem('user_id'); // Get the logged-in user ID from session
    if (!userId) {
      alert('You need to be logged in to manage favorites.');
      return;
    }
  
    const isFavorited = button.classList.contains('favorited'); // Check if the button is already favorited
    const action = isFavorited ? 'remove' : 'add'; // Determine the action
  
    fetch('../php files/add_to_favorites.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `auction_id=${auctionId}&action=${action}`, // Send auction_id and action
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (action === 'add') {
            button.classList.add('favorited'); // Add red color
            alert('Auction added to favorites!');
          } else {
            button.classList.remove('favorited'); // Remove red color
            alert('Auction removed from favorites!');
          }
        } else {
          alert('Failed to update favorites: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error updating favorites:', error);
        alert('An error occurred while updating favorites.');
      });
  }
 
  

fetchAuctions();