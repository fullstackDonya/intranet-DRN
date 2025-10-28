
<?php
require_once __DIR__ . '/../../crm/config/database.php';
include_once __DIR__ . '/../../crm/includes/auth.php';

$action = $_REQUEST['action'] ?? 'view';
$user_id = $_SESSION['user_id'] ?? null;

/* --- Products (remplace ancien erp_stock) --- */
function fetchProducts(PDO $pdo): array {
    $stmt = $pdo->query("SELECT id, sku, name, quantity, sale_price, is_rental FROM erp_products ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProduct(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM erp_products WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function createProduct(PDO $pdo, array $data): int {
    $stmt = $pdo->prepare("INSERT INTO erp_products (sku, name, description, quantity, purchase_price, sale_price, rental_rate_per_day, is_rental, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $data['sku'] ?? null,
        $data['name'] ?? '',
        $data['description'] ?? '',
        $data['quantity'] ?? 0,
        $data['purchase_price'] ?? null,
        $data['sale_price'] ?? 0,
        $data['rental_rate_per_day'] ?? null,
        !empty($data['is_rental']) ? 1 : 0,
    ]);
    return (int)$pdo->lastInsertId();
}

function updateProduct(PDO $pdo, int $id, array $data): bool {
    $stmt = $pdo->prepare("UPDATE erp_products SET sku=?, name=?, description=?, quantity=?, purchase_price=?, sale_price=?, rental_rate_per_day=?, is_rental=?, updated_at=NOW() WHERE id=?");
    return $stmt->execute([
        $data['sku'] ?? null,
        $data['name'] ?? '',
        $data['description'] ?? '',
        $data['quantity'] ?? 0,
        $data['purchase_price'] ?? null,
        $data['sale_price'] ?? 0,
        $data['rental_rate_per_day'] ?? null,
        !empty($data['is_rental']) ? 1 : 0,
        $id,
    ]);
}

function deleteProduct(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("DELETE FROM erp_products WHERE id = ?");
    return $stmt->execute([$id]);
}

/* --- API routing --- */
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(fetchProducts($pdo));
    exit;
}

if ($action === 'get' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = (int)($_GET['id'] ?? 0);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(getProduct($pdo, $id));
    exit;
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'sku' => $_POST['sku'] ?? null,
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'quantity' => (int)($_POST['quantity'] ?? 0),
        'purchase_price' => isset($_POST['purchase_price']) ? (float)$_POST['purchase_price'] : null,
        'sale_price' => (float)($_POST['sale_price'] ?? 0),
        'rental_rate_per_day' => isset($_POST['rental_rate_per_day']) ? (float)$_POST['rental_rate_per_day'] : null,
        'is_rental' => !empty($_POST['is_rental']) ? 1 : 0,
    ];
    try {
        $id = createProduct($pdo, $data);
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
        'sku' => $_POST['sku'] ?? null,
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'quantity' => (int)($_POST['quantity'] ?? 0),
        'purchase_price' => isset($_POST['purchase_price']) ? (float)$_POST['purchase_price'] : null,
        'sale_price' => (float)($_POST['sale_price'] ?? 0),
        'rental_rate_per_day' => isset($_POST['rental_rate_per_day']) ? (float)$_POST['rental_rate_per_day'] : null,
        'is_rental' => !empty($_POST['is_rental']) ? 1 : 0,
    ];
    $ok = updateProduct($pdo, $id, $data);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $ok = deleteProduct($pdo, $id);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

/* --- Page view fallback --- */
$products = fetchProducts($pdo);