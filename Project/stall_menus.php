<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
    <link rel="stylesheet" href="css/style_stall_menus.css">
</head>

<body>

    <div class="button-container">
        <button class="action-button" onclick="openModal()">Add</button>
        <button class="action-button" id="remove-button">Remove</button>
        <div class="checkbox-button">
            <input type="checkbox" id="select-mode" onclick="toggleSelectMode()">
            <span>Select Mode</span>
        </div>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modal-title">Add Menu Item</h2>
            <form id="menu-form">
                <label for="menu-image">Menu Image:</label>
                <input type="file" id="menu-image" name="menu-image" accept="image/*"><br><br>

                <label for="menu-name">Menu Name:</label>
                <input type="text" id="menu-name" name="menu-name"><br><br>

                <label for="menu-description">Menu Description:</label>
                <textarea id="menu-description" name="menu-description" rows="3"></textarea><br><br>

                <label for="menu-price">Menu Price (RM):</label>
                <input type="number" id="menu-price" name="menu-price" step="0.01"><br><br>

                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </div>

    <!-- View Details Modal -->
    <div id="view-details-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeViewDetailsModal()">&times;</span>
            <h2>Menu Item Details</h2>
            <img id="view-image" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
            <h3 id="view-name"></h3>
            <p id="view-description"></p>
            <div class="price" id="view-price"></div>
        </div>
    </div>

    <div id="menu-cards-container"></div>

    <script>
        let isSelectionMode = false;
        let menuItems = [];
        let editingMenuId = null;

        // Toggle selection mode
        function toggleSelectMode() {
            isSelectionMode = document.getElementById("select-mode").checked;
            const cards = document.querySelectorAll(".menu-card");
            cards.forEach((card) => {
                const checkbox = card.querySelector(".select-checkbox");
                checkbox.style.display = isSelectionMode ? "block" : "none";
                checkbox.checked = false;
                card.classList.remove("selected");
            });
        }

        // Open the modal for Add or Edit
        function openModal() {
            document.getElementById("menu-form").reset();
            document.getElementById("modal-title").textContent = "Add Menu Item";
            editingMenuId = null;
            document.getElementById("modal").style.display = "block";
        }

        // Open the modal to edit a menu item
        function editMenuItem(menuId) {
            const menuItem = menuItems.find(item => item.id === menuId);

            if (menuItem) {
                document.getElementById("modal-title").textContent = "Edit Menu Item";
                document.getElementById("menu-name").value = menuItem.name;
                document.getElementById("menu-description").value = menuItem.description;
                document.getElementById("menu-price").value = menuItem.price;
                editingMenuId = menuItem.id;
                document.getElementById("modal").style.display = "block";
            }
        }

        // View details of a menu item
        function viewMenuItemDetails(menuId) {
            const menuItem = menuItems.find(item => item.id === menuId);
            if (menuItem) {
                document.getElementById("view-image").src = menuItem.image_url;
                document.getElementById("view-name").textContent = menuItem.name;
                document.getElementById("view-description").textContent = menuItem.description;
                document.getElementById("view-price").textContent = `RM${menuItem.price}`;
                document.getElementById("view-details-modal").style.display = "block";
            }
        }

        // Close the modal
        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }

        // Close the View Details Modal
        function closeViewDetailsModal() {
            document.getElementById("view-details-modal").style.display = "none";
        }

        function loadMenuItems() {
            fetch("menu_operations.php?action=fetch")
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        menuItems = data.data;
                        renderMenuItems();
                    } else {
                        alert(data.message);
                    }
                })
                .catch((error) => console.error("Error fetching menu items:", error));
        }

        document.getElementById("menu-form").addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById("menu-form"));
            formData.append("action", editingMenuId ? "update" : "add");

            if (editingMenuId) {
                formData.append("id", editingMenuId);
            }

            const menuImage = document.getElementById("menu-image").files[0];
            if (!menuImage) {
                formData.delete("menu-image");
            }

            fetch("menu_operations.php", {
                    method: "POST",
                    body: formData,
                })
                .then((response) => response.json())
                .then((data) => {
                    alert(data.message);
                    closeModal();

                    if (data.success) {
                        menuItems = data.data;
                        renderMenuItems();
                    }
                })
                .catch((error) => console.error("Error:", error));
        });

        function renderMenuItems() {
            const container = document.getElementById("menu-cards-container");
            container.innerHTML = "";

            menuItems.forEach((item) => {
                const card = document.createElement("div");
                card.classList.add("menu-card");
                card.setAttribute("data-id", item.id);

                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.classList.add("select-checkbox");
                checkbox.style.display = isSelectionMode ? "block" : "none";
                card.appendChild(checkbox);

                const img = document.createElement("img");
                img.src = item.image_url;
                card.appendChild(img);

                const name = document.createElement("h3");
                name.textContent = item.name;
                card.appendChild(name);

                const price = document.createElement("div");
                price.textContent = `RM${item.price}`;
                card.appendChild(price);

                // Edit Button
                const editButton = document.createElement("button");
                editButton.textContent = "Edit";
                editButton.addEventListener("click", function() {
                    editMenuItem(item.id);
                });
                card.appendChild(editButton);

                // View Details Button
                const viewDetailsButton = document.createElement("button");
                viewDetailsButton.textContent = "View Details";
                viewDetailsButton.addEventListener("click", function() {
                    viewMenuItemDetails(item.id);
                });
                card.appendChild(viewDetailsButton);

                container.appendChild(card);
            });
        }


        // Handle the remove button action
        document.getElementById("remove-button").addEventListener("click", function() {
            const selectedCards = document.querySelectorAll(".menu-card .select-checkbox:checked");
            if (selectedCards.length === 0) {
                alert("Please select at least one menu card to remove.");
                return;
            }

            const idsToDelete = Array.from(selectedCards).map((checkbox) =>
                checkbox.parentElement.getAttribute("data-id")
            );

            fetch("menu_operations.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        action: "remove",
                        ids: idsToDelete
                    }),
                })
                .then((response) => response.text())
                .then((data) => {
                    alert(data);
                    loadMenuItems();
                })
                .catch((error) => console.error("Error:", error));
        });

        window.onload = loadMenuItems;
    </script>

</body>

</html>