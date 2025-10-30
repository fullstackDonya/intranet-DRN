<?php
require_once __DIR__ . '/../../crm/config/database.php';
include_once __DIR__ . '/../../crm/includes/auth.php';

$action = $_REQUEST['action'] ?? 'view';
$user_id = $_SESSION['user_id'] ?? null;

/* helper upload */
function handle_image_upload($fileField = 'image') {
    if (empty($_FILES[$fileField]) || $_FILES[$fileField]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$fileField];
    if ($f['error'] !== UPLOAD_ERR_OK) return null;
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (!in_array($f['type'], $allowed, true)) return null;
    $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
    $dir = __DIR__ . '/../assets/uploads/products';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $filename = uniqid('prod_', true) . '.' . ($ext ?: 'jpg');
    $target = $dir . '/' . $filename;
    if (move_uploaded_file($f['tmp_name'], $target)) {
        return $filename;
    }
    return null;
}

/* --- Products --- */
function fetchProducts(PDO $pdo): array {
    $stmt = $pdo->query("SELECT id, sku, name, quantity, sale_price, is_rental, image FROM erp_products ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProduct(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM erp_products WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function createProduct(PDO $pdo, array $data): int {
    $stmt = $pdo->prepare("INSERT INTO erp_products (sku, name, description, quantity, purchase_price, sale_price, rental_rate_per_day, is_rental, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $data['sku'] ?? null,
        $data['name'] ?? '',
        $data['description'] ?? '',
        $data['quantity'] ?? 0,
        $data['purchase_price'] ?? null,
        $data['sale_price'] ?? 0,
        $data['rental_rate_per_day'] ?? null,
        !empty($data['is_rental']) ? 1 : 0,
        $data['image'] ?? null,
    ]);
    return (int)$pdo->lastInsertId();
}

function updateProduct(PDO $pdo, int $id, array $data): bool {
    $sql = "UPDATE erp_products SET sku=?, name=?, description=?, quantity=?, purchase_price=?, sale_price=?, rental_rate_per_day=?, is_rental=?, updated_at=NOW()";
    $params = [
        $data['sku'] ?? null,
        $data['name'] ?? '',
        $data['description'] ?? '',
        $data['quantity'] ?? 0,
        $data['purchase_price'] ?? null,
        $data['sale_price'] ?? 0,
        $data['rental_rate_per_day'] ?? null,
        !empty($data['is_rental']) ? 1 : 0,
    ];
    if (!empty($data['image'])) {
        $sql .= ", image=?";
        $params[] = $data['image'];
    }
    $sql .= " WHERE id=?";
    $params[] = $id;
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function deleteProduct(PDO $pdo, int $id): bool {
    // Optionnel : supprimer le fichier image en lecture avant suppression en DB
    $row = getProduct($pdo, $id);
    if ($row && !empty($row['image'])) {
        $file = __DIR__ . '/../assets/uploads/products/' . $row['image'];
        if (file_exists($file)) @unlink($file);
    }
    $stmt = $pdo->prepare("DELETE FROM erp_products WHERE id = ?");
    return $stmt->execute([$id]);
}

/* --- API routing --- */
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(fetchProducts($pdo));
    exit;
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = handle_image_upload('image');
    $data = [
        'sku' => $_POST['sku'] ?? null,
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'quantity' => (int)($_POST['quantity'] ?? 0),
        'purchase_price' => isset($_POST['purchase_price']) ? (float)$_POST['purchase_price'] : null,
        'sale_price' => (float)($_POST['sale_price'] ?? 0),
        'rental_rate_per_day' => isset($_POST['rental_rate_per_day']) ? (float)$_POST['rental_rate_per_day'] : null,
        'is_rental' => !empty($_POST['is_rental']) ? 1 : 0,
        'image' => $image,
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
    $image = handle_image_upload('image'); // may be null
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
    if ($image) $data['image'] = $image;
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

/* Page view fallback */
$products = fetchProducts($pdo);