<?php

include __DIR__ . '/includes/products.php';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Produits - ERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      /* minimal modal styles */
      .modal { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.45); z-index: 60; }
      .modal.show { display: flex; }
      .modal .box { background: #fff; padding: 16px; border-radius: 8px; width: 360px; max-width: 95%; box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
      .form-row { display:flex;flex-direction:column;margin-bottom:8px }
      .form-row label{font-weight:600;margin-bottom:6px}
      .form-row input{padding:8px;border:1px solid #e6e9ef;border-radius:6px}
      .form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:10px}
    </style>
</head>
<body>
    <?php include 'erp_nav.php'; ?>
    <div class="container">
        <h1>Produits</h1>
        <button id="btnAdd" class="btn btn-primary">Ajouter un produit</button>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>SKU</th>
                    <th>Nom</th>
                    <th>Quantité</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="stockTableBody">
                <?php foreach ($products as $p): ?>
                    <tr data-id="<?= $p['id'] ?>">
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['sku'] ?? '') ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= (int)($p['quantity'] ?? 0) ?></td>
                        <td><?= number_format((float)($p['sale_price'] ?? 0), 2, ',', ' ') ?> €</td>
                        <td>
                            <button class="btn btn-edit">Modifier</button>
                            <button class="btn btn-delete">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal form -->
    <div class="modal" id="stockModal" aria-hidden="true">
      <div class="box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <h3 id="modalTitle">Ajouter / Modifier un produit</h3>
        <form id="stockForm" novalidate>
          <input type="hidden" id="stockId" name="id" value="">
          <div class="form-row">
            <label for="inputSku">SKU</label>
            <input id="inputSku" name="sku">
          </div>
          <div class="form-row">
            <label for="inputName">Nom</label>
            <input id="inputName" name="name" required>
          </div>
          <div class="form-row">
            <label for="inputQty">Quantité</label>
            <input id="inputQty" name="quantity" type="number" min="0" required>
          </div>
          <div class="form-row">
            <label for="inputPrice">Prix de vente (€)</label>
            <input id="inputPrice" name="sale_price" type="number" step="0.01" min="0" required>
          </div>
          <div class="form-row">
            <label for="inputRentalRate">Tarif location / jour (€)</label>
            <input id="inputRentalRate" name="rental_rate_per_day" type="number" step="0.01" min="0">
          </div>
          <div class="form-row">
            <label for="inputIsRental"><input id="inputIsRental" name="is_rental" type="checkbox" value="1"> Disponible à la location</label>
          </div>
          <div class="form-actions">
            <button type="button" id="btnDeleteModal" class="btn btn-danger" style="display:none">Supprimer</button>
            <button type="button" id="btnCancel" class="btn btn-ghost">Annuler</button>
            <button type="submit" id="btnSave" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>

    <script src="assets/js/stock.js"></script>
</body>
</html>
// ...existing code...