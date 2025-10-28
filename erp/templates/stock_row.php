<?php
// stock_row.php - Modèle pour afficher une ligne de stock dans l'interface utilisateur

function renderStockRow($stock) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($stock['id']) . '</td>';
    echo '<td>' . htmlspecialchars($stock['product_name']) . '</td>';
    echo '<td>' . htmlspecialchars($stock['quantity']) . '</td>';
    echo '<td>' . htmlspecialchars($stock['price']) . '</td>';
    echo '<td>' . htmlspecialchars($stock['status']) . '</td>';
    echo '<td>';
    echo '<button class="btn btn-edit" data-id="' . htmlspecialchars($stock['id']) . '">Modifier</button>';
    echo '<button class="btn btn-delete" data-id="' . htmlspecialchars($stock['id']) . '">Supprimer</button>';
    echo '</td>';
    echo '</tr>';
}
?>