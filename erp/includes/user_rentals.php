<?php

require_once __DIR__ . '/../../crm/config/database.php';
include_once __DIR__ . '/../../crm/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { http_response_code(401); echo json_encode(['error'=>'Not authenticated']); exit; }

$action = $_REQUEST['action'] ?? null;

// lister les demandes de l'utilisateur
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("
        SELECT rr.id, rr.product_id, rr.start_date, rr.end_date, rr.quantity, rr.message, rr.status, rr.created_at,
               p.name AS product_name, p.sku, p.image
        FROM rental_requests rr
        LEFT JOIN erp_products p ON p.id = rr.product_id
        WHERE rr.user_id = ?
        ORDER BY rr.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
    exit;
}

// créer une demande
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $message = trim($_POST['message'] ?? '');

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

// annuler une demande (si propriétaire et état autorise)
if ($action === 'cancel' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'Missing id']); exit; }

    // vérifier propriétaire + état
    $stmt = $pdo->prepare("SELECT user_id, status FROM rental_requests WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$r || (int)$r['user_id'] !== (int)$user_id) { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }
    if ($r['status'] !== 'pending') { http_response_code(400); echo json_encode(['error'=>'Impossible d\'annuler']); exit; }

    $u = $pdo->prepare("UPDATE rental_requests SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
    $ok = $u->execute([$id]);
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

http_response_code(400);
echo json_encode(['error'=>'Action non supportée']);