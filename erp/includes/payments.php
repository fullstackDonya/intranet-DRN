<?php
require_once __DIR__ . '/../../crm/config/database.php';
include_once __DIR__ . '/../../crm/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { http_response_code(401); echo json_encode(['error'=>'Not authenticated']); exit; }

$action = $_REQUEST['action'] ?? null;

// lister paiements (table 'payments' attendue)
// structure minimale attendue : payments(id, user_id, rental_request_id, amount, status, receipt_url, created_at, paid_at)
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("
            SELECT pay.id, pay.rental_request_id, pay.amount, pay.status, pay.receipt_url, pay.created_at, pay.paid_at,
                   rr.product_id, rr.start_date, rr.end_date, p.name AS product_name, p.sku
            FROM payments pay
            LEFT JOIN rental_requests rr ON rr.id = pay.rental_request_id
            LEFT JOIN erp_products p ON p.id = rr.product_id
            WHERE pay.user_id = ?
            ORDER BY pay.created_at DESC
        ");
        $stmt->execute([$user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rows);
    } catch (PDOException $e) {
        // si la table n'existe pas : renvoyer tableau vide pour ne pas casser l'UI
        echo json_encode([]);
    }
    exit;
}

// marquer comme payé (simulation)
if ($action === 'pay' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'Missing id']); exit; }

    // vérifier propriété
    $stmt = $pdo->prepare("SELECT user_id, status FROM payments WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$p || (int)$p['user_id'] !== (int)$user_id) { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }
    if ($p['status'] === 'paid') { echo json_encode(['ok'=>true]); exit; }

    $u = $pdo->prepare("UPDATE payments SET status = 'paid', paid_at = NOW() WHERE id = ?");
    $ok = $u->execute([$id]);
    // (optionnel) générer ou référencer une quittance
    if ($ok) {
        echo json_encode(['ok'=>true]);
    } else {
        http_response_code(500);
        echo json_encode(['error'=>'DB error']);
    }
    exit;
}

http_response_code(400);
echo json_encode(['error'=>'Action non supportée']);