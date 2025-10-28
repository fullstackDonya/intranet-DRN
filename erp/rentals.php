
<?php

include __DIR__ . '/includes/rentals.php';

// récupérer la liste des companies pour le select client dans le modal
$companiesStmt = $pdo->prepare("SELECT id, name FROM erp_companies ORDER BY name");
$companiesStmt->execute();
$companies = $companiesStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Locations - ERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      .modal { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.45); z-index: 60; }
      .modal.show { display: flex; }
      .modal .box { background: #fff; padding: 16px; border-radius: 8px; width: 520px; max-width: 95%; box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
      .form-row { display:flex;flex-direction:column;margin-bottom:8px }
      .form-row label{font-weight:600;margin-bottom:6px}
      .form-row input, .form-row select, .form-row textarea{padding:8px;border:1px solid #e6e9ef;border-radius:6px}
      .form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:10px}
      table{width:100%;border-collapse:collapse}
      table th, table td{border-bottom:1px solid #eee;padding:8px;text-align:left}
    </style>
</head>
<body>
    <?php include 'erp_nav.php'; ?>
    <div class="container">
        <h1>Locations</h1>
        <button id="btnAdd" class="btn btn-primary">Nouvelle location</button>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="rentalTableBody">
                <?php foreach ($rentals as $r): ?>
                    <tr data-id="<?= $r['id'] ?>">
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['customer_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($r['start_date']) ?></td>
                        <td><?= htmlspecialchars($r['end_date']) ?></td>
                        <td><?= htmlspecialchars($r['status']) ?></td>
                        <td><?= number_format((float)($r['total_price'] ?? 0), 2, ',', ' ') ?> €</td>
                        <td>
                            <button class="btn btn-edit">Modifier</button>
                            <button class="btn btn-delete">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- modal -->
    <div class="modal" id="rentalModal" aria-hidden="true">
      <div class="box" role="dialog" aria-modal="true">
        <h3 id="modalTitle">Nouvelle location</h3>
        <form id="rentalForm" novalidate>
          <input type="hidden" id="rentalId" name="id" value="">
          <div class="form-row">
            <label for="rentalCustomer">Client (company)</label>
            <select id="rentalCustomer" name="customer_id">
              <option value="">— Aucun —</option>
              <?php foreach ($companies as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-row">
            <label for="rentalStart">Date de début</label>
            <input id="rentalStart" name="start_date" type="datetime-local" required>
          </div>
          <div class="form-row">
            <label for="rentalEnd">Date de fin</label>
            <input id="rentalEnd" name="end_date" type="datetime-local" required>
          </div>
          <div class="form-row">
            <label for="rentalStatus">Status</label>
            <select id="rentalStatus" name="status">
              <option value="draft">draft</option>
              <option value="active">active</option>
              <option value="completed">completed</option>
              <option value="cancelled">cancelled</option>
            </select>
          </div>
          <div class="form-row">
            <label for="rentalTotal">Total (€)</label>
            <input id="rentalTotal" name="total_price" type="number" step="0.01" min="0" value="0">
          </div>
          <div class="form-row">
            <label for="rentalDeposit">Dépôt (€)</label>
            <input id="rentalDeposit" name="deposit" type="number" step="0.01" min="0" value="0">
          </div>
          <div class="form-actions">
            <button type="button" id="btnDeleteModal" class="btn btn-danger" style="display:none">Supprimer</button>
            <button type="button" id="btnCancel" class="btn btn-ghost">Annuler</button>
            <button type="submit" id="btnSave" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>

    <script src="assets/js/rentals.js"></script>
</body>
</html>
