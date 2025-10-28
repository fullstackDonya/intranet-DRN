<?php
// inventory_row.php - Template for displaying a row in the inventory list

function renderInventoryRow($item) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($item['id']) . '</td>';
    echo '<td>' . htmlspecialchars($item['name']) . '</td>';
    echo '<td>' . htmlspecialchars($item['quantity']) . '</td>';
    echo '<td>' . htmlspecialchars($item['location']) . '</td>';
    echo '<td>' . htmlspecialchars($item['last_updated']) . '</td>';
    echo '<td>';
    echo '<button class="btn btn-edit" data-id="' . htmlspecialchars($item['id']) . '">Edit</button>';
    echo '<button class="btn btn-delete" data-id="' . htmlspecialchars($item['id']) . '">Delete</button>';
    echo '</td>';
    echo '</tr>';
}
?>