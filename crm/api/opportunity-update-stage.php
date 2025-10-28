<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'unauthenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? intval($input['id']) : 0;
$newStage = $input['stage'] ?? null;

$validStages = ['prospecting','qualification','needs_analysis','proposal','negotiation','closed_won','closed_lost'];
if (!$id || !$newStage || !in_array($newStage, $validStages)) {
    echo json_encode(['success' => false, 'error' => 'invalid_params']);
    exit;
}

try {
    // Optionnel : vérifier que l'utilisateur peut modifier cette opportunité (assigned_to)
    $stmt = $pdo->prepare("SELECT assigned_to FROM opportunities WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'not_found']);
        exit;
    }
    if (intval($row['assigned_to']) !== intval($user_id)) {
        echo json_encode(['success' => false, 'error' => 'forbidden']);
        exit;
    }

    // Update stage (et actual_close_date si closed_won)
    if ($newStage === 'closed_won') {
        $stmt = $pdo->prepare("UPDATE opportunities SET stage = ?, actual_close_date = NOW() WHERE id = ?");
        $stmt->execute([$newStage, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE opportunities SET stage = ? WHERE id = ?");
        $stmt->execute([$newStage, $id]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('opportunity-update-stage error: '.$e->getMessage());
    echo json_encode(['success' => false, 'error' => 'server_error']);
}