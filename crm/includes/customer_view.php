<?php
require_once __DIR__ . '/../config/database.php';
function h($v){ return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); echo 'ID manquant'; exit; }

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$customer) { http_response_code(404); echo 'Client introuvable'; exit; }
