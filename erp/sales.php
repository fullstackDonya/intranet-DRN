<?php

require_once __DIR__ . '/../crm/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$customer_id = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null; // legacy (unused)

/* --- API & DB helpers --- */
function fetchSales(PDO $pdo): array {
    $sql = "SELECT s.*, e.first_name, e.last_name, p.product_name AS product_name
            FROM erp_sales s
            JOIN erp_employees e ON e.id = s.employee_id
            JOIN erp_stock p ON p.id = s.product_id
            ORDER BY s.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createSale(PDO $pdo, array $data): int {
    $sql = "INSERT INTO erp_sales (product_id, employee_id, quantity, total_price, created_at)
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['product_id'],
        $data['employee_id'],
        $data['quantity'],
        $data['total_price'],
    ]);
    return (int)$pdo->lastInsertId();
}

function deleteSale(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("DELETE FROM erp_sales WHERE id=?");
    return $stmt->execute([$id]);
}

/* --- API routing --- */
$action = $_REQUEST['action'] ?? 'view';
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(fetchSales($pdo));
    exit;
}
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'product_id' => (int)($_POST['product_id'] ?? 0),
        'employee_id' => (int)($_POST['employee_id'] ?? 0),
        'quantity' => (int)($_POST['quantity'] ?? 0),
        'total_price' => (float)($_POST['total_price'] ?? 0),
    ];
    if ($data['product_id'] <= 0 || $data['employee_id'] <= 0 || $data['quantity'] <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }
    try {
        $id = createSale($pdo, $data);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['id' => $id]);
    exit;
}
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid id']);
        exit;
    }
    $ok = deleteSale($pdo, $id);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

/* --- Prepare data for view --- */
$sales = fetchSales($pdo);

// employees and products limited to session customer
$employeesStmt = $pdo->prepare("SELECT id, first_name, last_name FROM erp_employees ORDER BY last_name");
$employeesStmt->execute();
$employees = $employeesStmt->fetchAll(PDO::FETCH_ASSOC);

$productsStmt = $pdo->prepare("SELECT id, product_name AS name FROM erp_stock ORDER BY product_name");
$productsStmt->execute();
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ventes - ERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      .modal { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.45); z-index: 60; }
      .modal.show { display: flex; }
      .modal .box { background: #fff; padding: 16px; border-radius: 8px; width: 420px; max-width: 95%; box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
      .form-row { display:flex;flex-direction:column;margin-bottom:8px }
      .form-row label{font-weight:600;margin-bottom:6px}
      .form-row input, .form-row select{padding:8px;border:1px solid #e6e9ef;border-radius:6px}
      .form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:10px}
    </style>
</head>
<body>
    <?php include 'erp_nav.php'; ?>
    <div class="container">
        <h1>Gestion des Ventes</h1>
        <div class="controls">
            <button id="btnAdd" class="btn btn-primary">Ajouter une Vente</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produit</th>
                    <th>Employé</th>
                    <th>Quantité</th>
                    <th>Prix Total</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="salesTableBody">
                <?php foreach ($sales as $sale): ?>
                    <tr data-id="<?= htmlspecialchars($sale['id']) ?>">
                        <td><?= htmlspecialchars($sale['id']) ?></td>
                        <td><?= htmlspecialchars($sale['product_name']) ?></td>
                        <td><?= htmlspecialchars($sale['first_name'] . ' ' . $sale['last_name']) ?></td>
                        <td><?= htmlspecialchars($sale['quantity']) ?></td>
                        <td><?= htmlspecialchars($sale['total_price']) ?></td>
                        <td><?= htmlspecialchars($sale['created_at']) ?></td>
                        <td>
                            <button class="btn btn-delete">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- modal form -->
    <div class="modal" id="salesModal" aria-hidden="true">
      <div class="box" role="dialog" aria-modal="true">
        <h3 id="modalTitle">Ajouter une vente</h3>
        <form id="salesForm" novalidate>
          <input type="hidden" id="saleId" name="id" value="">
          <div class="form-row">
            <label for="selectProduct">Produit</label>
            <select id="selectProduct" name="product_id" required>
              <option value="">-- Sélectionner --</option>
              <?php foreach ($products as $p): ?>
                <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-row">
            <label for="selectEmployee">Employé</label>
            <select id="selectEmployee" name="employee_id" required>
              <option value="">-- Sélectionner --</option>
              <?php foreach ($employees as $e): ?>
                <option value="<?= (int)$e['id'] ?>"><?= htmlspecialchars($e['last_name'].' '.$e['first_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-row">
            <label for="inputQty">Quantité</label>
            <input id="inputQty" name="quantity" type="number" min="1" value="1" required>
          </div>
          <div class="form-row">
            <label for="inputTotal">Prix total (€)</label>
            <input id="inputTotal" name="total_price" type="number" step="0.01" min="0" value="0.00" required>
          </div>
          <div class="form-actions">
            <button type="button" id="btnCancel" class="btn btn-ghost">Annuler</button>
            <button type="submit" id="btnSave" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>

    <script src="assets/js/sales.js"></script>
</body>
</html>