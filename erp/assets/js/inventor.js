// filepath: /webitech-erp/webitech-erp/erp/assets/js/inventory.js

document.addEventListener('DOMContentLoaded', function() {
    const inventoryTable = document.getElementById('inventoryTable');
    const addInventoryForm = document.getElementById('addInventoryForm');
    const inventoryModal = document.getElementById('inventoryModal');
    const inventoryIdInput = document.getElementById('inventoryId');
    
    function fetchInventory() {
        fetch('api/inventory.php?action=fetch')
            .then(response => response.json())
            .then(data => {
                renderInventory(data);
            })
            .catch(error => console.error('Error fetching inventory:', error));
    }

    function renderInventory(items) {
        inventoryTable.innerHTML = '';
        items.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.id}</td>
                <td>${item.name}</td>
                <td>${item.quantity}</td>
                <td>${item.description}</td>
                <td>
                    <button class="edit-btn" data-id="${item.id}">Edit</button>
                    <button class="delete-btn" data-id="${item.id}">Delete</button>
                </td>
            `;
            inventoryTable.appendChild(row);
        });
        attachEventListeners();
    }

    function attachEventListeners() {
        const editButtons = document.querySelectorAll('.edit-btn');
        const deleteButtons = document.querySelectorAll('.delete-btn');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                openEditModal(id);
            });
        });

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                deleteInventoryItem(id);
            });
        });
    }

    function openEditModal(id) {
        fetch(`api/inventory.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                inventoryIdInput.value = data.id;
                document.getElementById('inventoryName').value = data.name;
                document.getElementById('inventoryQuantity').value = data.quantity;
                document.getElementById('inventoryDescription').value = data.description;
                inventoryModal.style.display = 'block';
            })
            .catch(error => console.error('Error fetching inventory item:', error));
    }

    function deleteInventoryItem(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            fetch(`api/inventory.php?action=delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchInventory();
                } else {
                    alert('Error deleting item');
                }
            })
            .catch(error => console.error('Error deleting inventory item:', error));
        }
    }

    addInventoryForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const id = inventoryIdInput.value;
        const name = document.getElementById('inventoryName').value;
        const quantity = document.getElementById('inventoryQuantity').value;
        const description = document.getElementById('inventoryDescription').value;

        const action = id ? 'update' : 'create';
        fetch(`api/inventory.php?action=${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id, name, quantity, description })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchInventory();
                inventoryModal.style.display = 'none';
            } else {
                alert('Error saving inventory item');
            }
        })
        .catch(error => console.error('Error saving inventory item:', error));
    });

    fetchInventory();
});