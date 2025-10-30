<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
function h($v){ return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
function trim_or_null($v){ $s = trim((string)($v ?? '')); return ($s === '') ? null : $s; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); echo 'ID manquant'; exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim_or_null($_POST['phone'] ?? null);
    $postal_code = trim_or_null($_POST['postal_code'] ?? null);
    $address = trim_or_null($_POST['address'] ?? null);
    $country = trim_or_null($_POST['country'] ?? null);
    $role = $_POST['role'] ?? 'client';
    $status = $_POST['status'] ?? 'active';

    if ($name === '') { $errors[] = "Le nom est requis"; }
    if ($email === '') { $errors[] = "L'email est requis"; }
    $allowed_roles = ['admin','manager','client','partner'];
    if (!in_array($role, $allowed_roles, true)) { $role = 'client'; }
    $allowed_status = ['active','inactive'];
    if (!in_array($status, $allowed_status, true)) { $status = 'active'; }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare("UPDATE customers SET name=:name, email=:email, phone=:phone, address=:address, country=:country, role=:role, status=:status, postal_code=:postal_code, updated_at=NOW() WHERE id=:id");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
                ':country' => $country,
                ':role' => $role,
                ':status' => $status,
                ':postal_code' => $postal_code,
                ':id' => $id,
            ]);
            header('Location: ../customer_view.php?id=' . $id . '&updated=1');
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Erreur base de données: ' . $e->getMessage();
        }
    }
}

// Charger le client pour l'édition
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$customer) { http_response_code(404); echo 'Client introuvable'; exit; }
