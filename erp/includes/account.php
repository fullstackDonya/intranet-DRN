<?php
require_once __DIR__ . '/../../crm/config/database.php';
include_once __DIR__ . '/../../crm/includes/auth.php';



header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { http_response_code(401); echo json_encode(['error'=>'Not authenticated']); exit; }

if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT rr.id, rr.product_id, rr.start_date, rr.end_date, rr.quantity, rr.message, rr.status, rr.created_at, p.name AS product_name, p.sku, p.image
        FROM rental_requests rr
        LEFT JOIN erp_products p ON p.id = rr.product_id
        WHERE rr.user_id = ? ORDER BY rr.created_at DESC");
    $stmt->execute([$user_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
    exit;
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $message = $_POST['message'] ?? '';

    if (!$product_id || !$start_date) {
        http_response_code(400);
        echo json_encode(['error'=>'Champs manquants']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO rental_requests (user_id, product_id, start_date, end_date, quantity, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $ok = $stmt->execute([$user_id, $product_id, $start_date, $end_date ?: null, $qty, $message]);
    if ($ok) {
        echo json_encode(['ok'=>true, 'id' => (int)$pdo->lastInsertId()]);
    } else {
        http_response_code(500);
        echo json_encode(['error'=>'DB error']);
    }
    exit;
}

http_response_code(400);
echo json_encode(['error'=>'Action non supportée']);