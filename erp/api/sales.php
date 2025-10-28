<?php
declare(strict_types=1);

require_once __DIR__ . '/common.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'view';

if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT s.*, e.first_name, e.last_name, p.product_name AS product_name FROM erp_sales s JOIN erp_employees e ON e.id = s.employee_id JOIN erp_stock p ON p.id = s.product_id ORDER BY s.created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_SESSION['customer_id'] ?? null; // legacy
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

    $stmt = $pdo->prepare("INSERT INTO erp_sales (product_id, employee_id, quantity, total_price, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$data['product_id'], $data['employee_id'], $data['quantity'], $data['total_price']]);
    $newId = (int)$pdo->lastInsertId();

    // Emit real-time event through rt_events table for SSE subscribers
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS rt_events (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            topic VARCHAR(100) NOT NULL,
            type VARCHAR(100) NOT NULL,
            entity_id INT NULL,
            data JSON NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_topic_id (topic, id),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $topic = 'sales';
        $payload = [
            'sale_id' => $newId,
            'product_id' => $data['product_id'],
            'employee_id' => $data['employee_id'],
            'quantity' => $data['quantity'],
            'total_price' => $data['total_price']
        ];
        $insEvt = $pdo->prepare("INSERT INTO rt_events (topic, type, entity_id, data) VALUES (?, ?, ?, ?)");
        $insEvt->execute([$topic, 'sale.created', $newId, json_encode($payload, JSON_UNESCAPED_UNICODE)]);
    } catch (\Throwable $e) {
        // swallow errors: eventing is best-effort
    }

    echo json_encode(['id' => $newId]);
    exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $customer_id = $_SESSION['customer_id'] ?? null; // legacy
    $data = [
        'product_id' => (int)($_POST['product_id'] ?? 0),
        'employee_id' => (int)($_POST['employee_id'] ?? 0),
        'quantity' => (int)($_POST['quantity'] ?? 0),
        'total_price' => (float)($_POST['total_price'] ?? 0),
    ];

    if ($id <= 0 || $data['product_id'] <= 0 || $data['employee_id'] <= 0 || $data['quantity'] <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE erp_sales SET product_id=?, employee_id=?, quantity=?, total_price=? WHERE id=?");
    $stmt->execute([$data['product_id'], $data['employee_id'], $data['quantity'], $data['total_price'], $id]);
    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $customer_id = $_SESSION['customer_id'] ?? null; // legacy
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid id']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM erp_sales WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
?>