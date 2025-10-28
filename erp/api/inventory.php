<?php
declare(strict_types=1);

require_once __DIR__ . '/../../crm/config/database.php';

// Fetch all erp_inventory items
function fetchInventory(PDO $pdo): array {
    $stmt = $pdo->query("SELECT * FROM erp_inventory ORDER BY id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create a new erp_inventory item
function createInventoryItem(PDO $pdo, array $data): int {
    $stmt = $pdo->prepare("INSERT INTO erp_inventory (item_name, quantity, description, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$data['item_name'], $data['quantity'], $data['description']]);
    return (int)$pdo->lastInsertId();
}

// Update an existing erp_inventory item
function updateInventoryItem(PDO $pdo, int $id, array $data): bool {
    $stmt = $pdo->prepare("UPDATE erp_inventory SET item_name = ?, quantity = ?, description = ? WHERE id = ?");
    return $stmt->execute([$data['item_name'], $data['quantity'], $data['description'], $id]);
}

// Delete an erp_inventory item
function deleteInventoryItem(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("DELETE FROM erp_inventory WHERE id = ?");
    return $stmt->execute([$id]);
}

// API routing
$action = $_REQUEST['action'] ?? 'view';
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(fetchInventory($pdo));
    exit;
}
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'item_name' => $_POST['item_name'] ?? '',
        'quantity' => (int)($_POST['quantity'] ?? 0),
        'description' => $_POST['description'] ?? '',
    ];
    $id = createInventoryItem($pdo, $data);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['id' => $id]);
    exit;
}
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        'item_name' => $_POST['item_name'] ?? '',
        'quantity' => (int)($_POST['quantity'] ?? 0),
        'description' => $_POST['description'] ?? '',
    ];
    $ok = updateInventoryItem($pdo, $id, $data);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $ok = deleteInventoryItem($pdo, $id);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}
?>