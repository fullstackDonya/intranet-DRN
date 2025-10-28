<?php
require_once __DIR__ . '/../../crm/config/database.php';
include_once __DIR__ . '/../../crm/includes/auth.php';

$action = $_REQUEST['action'] ?? 'view';
$user_id = $_SESSION['user_id'] ?? null;

/* --- Rentals (remplace ancien erp_inventory) --- */
function fetchRentals(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT r.*, c.name AS customer_name FROM erp_rentals r LEFT JOIN erp_companies c ON r.customer_id = c.id ORDER BY r.start_date DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRental(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM erp_rentals WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function createRental(PDO $pdo, array $data): int {
    $stmt = $pdo->prepare("INSERT INTO erp_rentals (customer_id, start_date, end_date, status, total_price, deposit, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $data['customer_id'] ?? null,
        $data['start_date'] ?? null,
        $data['end_date'] ?? null,
        $data['status'] ?? 'draft',
        $data['total_price'] ?? 0,
        $data['deposit'] ?? 0,
    ]);
    return (int)$pdo->lastInsertId();
}

function updateRental(PDO $pdo, int $id, array $data): bool {
    $stmt = $pdo->prepare("UPDATE erp_rentals SET customer_id=?, start_date=?, end_date=?, status=?, total_price=?, deposit=?, updated_at=NOW() WHERE id=?");
    return $stmt->execute([
        $data['customer_id'] ?? null,
        $data['start_date'] ?? null,
        $data['end_date'] ?? null,
        $data['status'] ?? 'draft',
        $data['total_price'] ?? 0,
        $data['deposit'] ?? 0,
        $id,
    ]);
}

function deleteRental(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("DELETE FROM erp_rentals WHERE id = ?");
    return $stmt->execute([$id]);
}

/* --- API routing --- */
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(fetchRentals($pdo));
    exit;
}

if ($action === 'get' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = (int)($_GET['id'] ?? 0);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(getRental($pdo, $id));
    exit;
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'customer_id' => isset($_POST['customer_id']) && $_POST['customer_id'] !== '' ? (int)$_POST['customer_id'] : null,
        'start_date' => $_POST['start_date'] ?? null,
        'end_date' => $_POST['end_date'] ?? null,
        'status' => $_POST['status'] ?? 'draft',
        'total_price' => isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0,
        'deposit' => isset($_POST['deposit']) ? (float)$_POST['deposit'] : 0,
    ];
    try {
        $id = createRental($pdo, $data);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['id' => $id]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        'customer_id' => isset($_POST['customer_id']) && $_POST['customer_id'] !== '' ? (int)$_POST['customer_id'] : null,
        'start_date' => $_POST['start_date'] ?? null,
        'end_date' => $_POST['end_date'] ?? null,
        'status' => $_POST['status'] ?? 'draft',
        'total_price' => isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0,
        'deposit' => isset($_POST['deposit']) ? (float)$_POST['deposit'] : 0,
    ];
    $ok = updateRental($pdo, $id, $data);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $ok = deleteRental($pdo, $id);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

/* --- Page view fallback --- */
$rentals = fetchRentals($pdo);