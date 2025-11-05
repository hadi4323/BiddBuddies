const postTradeButton = document.getElementById("proposeTradeButton");
const tradeFormModal = document.getElementById("tradeFormModal");
const closeModalButton = document.getElementById("closeTradeModalButton");
const tradeForm = document.getElementById("tradeForm");
const tradeContainer = document.getElementById("trade-container");
const deleteSelectedButton = document.getElementById("deleteSelectedTrades");
const loggedInUserId = parseInt(sessionStorage.getItem('user_id')) || null;

window.addEventListener("DOMContentLoaded", () => {
  tradeFormModal.style.display = "none";
});

// Open Modal
postTradeButton.addEventListener("click", () => {
  if (loggedInUserId) {
    tradeFormModal.style.display = "flex";
  } else {
    alert("You need to log in to post a trade.");
  }
});

// Close Modal
closeModalButton.addEventListener("click", () => {
  tradeFormModal.style.display = "none";
});

function fetchTrades() {
  fetch('../php files/fetch_trade.php')
    .then(response => response.json())
    .then(data => {
      console.log(data);

      tradeContainer.innerHTML = ''; // Clear existing trades

      data.forEach(trade => {
        const tradeCard = createTradeCard(trade);
        tradeContainer.appendChild(tradeCard);
      });
    })
    .catch(error => console.error('Error fetching trades:', error));
}

document.addEventListener("DOMContentLoaded", () => {
  fetch("../php files/check_favT.php")
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              const favoritedTrades = data.favoritedTrades;
              favoritedTrades.forEach(tradeId => {
                  const favoriteButton = document.querySelector(`#favorite-${tradeId}`);
                  if (favoriteButton) {
                      favoriteButton.classList.add("favorited");
                  }
              });
          } else {
              console.error(data.message);
          }
      })
      .catch(error => console.error("Error fetching favorited trades:", error));
});

// Submit Trade Form
tradeForm.addEventListener("submit", (e) => {
  e.preventDefault();

  const formData = new FormData(tradeForm);

  fetch('../php files/post_trade.php', {
    method: 'POST',
    body: formData,
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Trade posted successfully and awaiting approval.");
        tradeFormModal.style.display = "none";
        tradeForm.reset();
        fetchTrades(); // Refresh trades
      } else {
        alert(data.message || "Failed to post trade.");
      }
    })
    .catch(error => console.error('Error posting trade:', error));
});



function createTradeCard(trade) {
  const tradeCard = document.createElement("div");
  tradeCard.className = "trade-card";
  tradeCard.setAttribute("data-category", trade.category.toLowerCase().trim());
  tradeCard.setAttribute("data-location", trade.location.toLowerCase().trim().replace(/\s+/g, '-'));

  const loggedInUserId = parseInt(sessionStorage.getItem('user_id'));
  const tradeOwnerId = trade.owner_id;

  // Add checkbox for the logged-in user's own trades
  let checkboxHTML = '';
  if (tradeOwnerId === loggedInUserId) {
    checkboxHTML = `<input type="checkbox" class="delete-checkbox" data-trade-id="${trade.trade_id}"> `;
  }

  // Add "Mark as Done" button for the post owner
  let markAsDoneHTML = '';
  if (tradeOwnerId === loggedInUserId && trade.status !== "Done") {
    markAsDoneHTML = `<button class="mark-done-btn" id="mark-done-${trade.trade_id}" data-trade-id="${trade.trade_id}">Mark as Done</button>`;
  }

  // Add to Favorites button for posts not owned by the user
  const favoriteButtonHTML =
    tradeOwnerId !== loggedInUserId
      ? `<button class="favorite-btn ${trade.is_favorited ? 'favorited' : ''}" id="favorite-${trade.trade_id}" data-trade-id="${trade.trade_id}">&#9825;</button>`
      : "";

  tradeCard.innerHTML = `
    ${checkboxHTML}
    ${favoriteButtonHTML}
    <img src="${trade.image || 'placeholder.png'}" alt="Trade Item" class="trade-photo" id="trade-photo-${trade.trade_id}">
    <h2 class="trade-title" id="trade-title-${trade.trade_id}">${trade.item_name}</h2>
    <p class="trade-description" id="trade-description-${trade.trade_id}">${trade.description}</p>
    <p class="trade-category" id="trade-category-${trade.trade_id}">Category: ${trade.category}</p>
    <p class="trade-location" id="trade-location-${trade.trade_id}">Location: ${trade.location}</p>
    <p class="trade-preferred-item" id="trade-preferred-item-${trade.trade_id}">Preferred Item: ${trade.preferred_item}</p>
    <p class="trade-contact_info" id="trade-contact_info-${trade.trade_id}">Contact_info: ${trade.contact_info}</p>
    <p>Status: <span class="trade-status" id="trade-status-${trade.trade_id}">${trade.status}</span></p>
    <p class="trade-mark" id="trade-mark-${trade.trade_id}">Mark: ${trade.mark}</p>
    <p class="owner-info" id="owner-info-${trade.trade_id}">Posted by: ${trade.owner}</p>
    ${markAsDoneHTML}
  `;

  // Add event listener for the favorite button
  const favoriteButton = tradeCard.querySelector(".favorite-btn");
  if (favoriteButton) {
    favoriteButton.addEventListener("click", () => {
      toggleFavorite(trade.trade_id, favoriteButton);
    });
  }

  // Add event listener for the "Mark as Done" button
  const markDoneButton = tradeCard.querySelector(".mark-done-btn");
  if (markDoneButton) {
    markDoneButton.addEventListener("click", () => {
      markTradeAsDone(trade.trade_id, markDoneButton);
    });
  }

  return tradeCard;
}

function markTradeAsDone(tradeId, markDoneButton) {
  fetch('../php files/mark_trade.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ trade_id: tradeId }),
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const statusSpan = document.getElementById(`trade-status-${tradeId}`);
        statusSpan.textContent = "Done";
        markDoneButton.remove(); // Remove the button
        alert("Trade marked as done.");
      } else {
        alert(data.message || "Failed to mark as done.");
      }
    })
    .catch(error => console.error('Error marking trade as done:', error));
}

function toggleFavorite(tradeId, button) {
  const userId = sessionStorage.getItem('user_id'); // Get the logged-in user ID from session
  if (!userId) {
    alert('You need to be logged in to manage favorites.');
    return;
  }

  const isFavorited = button.classList.contains('favorited'); // Check if the button is already favorited
  const action = isFavorited ? 'remove' : 'add'; // Determine the action

  fetch('../php files/trades_favorites.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `trade_id=${tradeId}&action=${action}`, // Send trade_id and action
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        if (action === 'add') {
          button.classList.add('favorited'); // Add "favorited" style
          alert('Trade added to favorites!');
        } else {
          button.classList.remove('favorited'); // Remove "favorited" style
          alert('Trade removed from favorites!');
        }
      } else {
        alert('Failed to update favorites: ' + data.message);
      }
    })
    .catch((error) => {
      console.error('Error updating favorites:', error);
      alert('An error occurred while updating favorites.');
    });
}


document.addEventListener("DOMContentLoaded", function () {
  // Handle the deletion when "Delete Trades" button is clicked
  document.getElementById("deleteSelectedTrades").addEventListener("click", function() {
    if (!loggedInUserId) {
      alert("You need to log in to delete trades.");
      return; // Stop further execution if the user is not logged in
    }

    const selectedTrades = [];
    const checkboxes = document.querySelectorAll('.delete-checkbox:checked');
    
    // Collect selected trade IDs
    checkboxes.forEach(checkbox => {
      const tradeId = checkbox.getAttribute("data-trade-id");
      selectedTrades.push(tradeId);
    });

    if (selectedTrades.length > 0) {
      deleteSelectedTrades(selectedTrades);
    } else {
      alert("Please select at least one trade to delete.");
    }
  });
});

// Function to delete selected trades
function deleteSelectedTrades(tradeIds) {
  fetch('../php files/delete_trade.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ trade_ids: tradeIds })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert("Selected trades deleted successfully!");
      fetchTrades(); // Refresh the list of trades
    } else {
      alert("Failed to delete selected trades.");
    }
  })
  .catch(error => console.error('Error deleting selected trades:', error));
}


fetchTrades();


  // Ensure this code runs after DOM is loaded

  const searchBar = document.getElementById("searchBar");
  const categoryFilter = document.getElementById("categoryFilter");
  const locationFilter = document.getElementById("locationFilter");
  const tradeContainer1 = document.querySelector(".trade-container"); // The container for trade cards

  // Function to filter trades
  function filterTrades() {
    const searchQuery = searchBar.value.toLowerCase().trim();
    const selectedCategory = categoryFilter.value.toLowerCase().trim();
    const selectedLocation = locationFilter.value.toLowerCase().trim().replace(/\s+/g, '-');
  
    const tradeCards = tradeContainer.querySelectorAll(".trade-card");
    
    tradeCards.forEach((card) => {
      const itemName = card.querySelector(".trade-title").textContent.toLowerCase().trim();
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
        card.style.display = "block"; // Show the card if all filters match
      } else {
        card.style.display = "none"; // Hide the card if not matching
      }
    });
  }

  // Attach event listeners
  searchBar.addEventListener("input", filterTrades);
  categoryFilter.addEventListener("change", filterTrades);
  locationFilter.addEventListener("change", filterTrades);

