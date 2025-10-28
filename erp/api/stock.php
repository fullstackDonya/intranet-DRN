<?php
declare(strict_types=1);

require_once __DIR__ . '/../../crm/config/database.php';

// Fetch all stock items
function fetchAllStock(PDO $pdo): array {
    $stmt = $pdo->query("SELECT * FROM erp_stock ORDER BY id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create a new erp_stock item
function createStockItem(PDO $pdo, array $data): int {
    $stmt = $pdo->prepare("INSERT INTO erp_stock (product_name, quantity, price, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$data['product_name'], $data['quantity'], $data['price']]);
    return (int)$pdo->lastInsertId();
}

// Update an existing erp_stock item
function updateStockItem(PDO $pdo, int $id, array $data): bool {
    $stmt = $pdo->prepare("UPDATE erp_stock SET product_name = ?, quantity = ?, price = ? WHERE id = ?");
    return $stmt->execute([$data['product_name'], $data['quantity'], $data['price'], $id]);
}

// Delete a erp_stock item
function deleteStockItem(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("DELETE FROM erp_stock WHERE id = ?");
    return $stmt->execute([$id]);
}

// API routing
$action = $_REQUEST['action'] ?? 'fetch';
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(fetchAllStock($pdo));
    exit;
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'product_name' => $_POST['product_name'] ?? '',
        'quantity' => (int)($_POST['quantity'] ?? 0),
        'price' => (float)($_POST['price'] ?? 0.0),
    ];
    $id = createStockItem($pdo, $data);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['id' => $id]);
    exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        'product_name' => $_POST['product_name'] ?? '',
        'quantity' => (int)($_POST['quantity'] ?? 0),
        'price' => (float)($_POST['price'] ?? 0.0),
    ];
    $ok = updateStockItem($pdo, $id, $data);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $ok = deleteStockItem($pdo, $id);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}
?>