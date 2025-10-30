<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';

function h($v){ return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
function trim_or_null($v){ $s = trim((string)($v ?? '')); return ($s === '') ? null : $s; }

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
            $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone, address, country, role, status, postal_code, created_at, updated_at) VALUES (:name, :email, :phone, :address, :country, :role, :status, :postal_code, NOW(), NOW())");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
                ':country' => $country,
                ':role' => $role,
                ':status' => $status,
                ':postal_code' => $postal_code,
            ]);
            $newId = (int)$pdo->lastInsertId();
            header('Location: ../customer_view.php?id=' . $newId . '&created=1');
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Erreur base de données: ' . $e->getMessage();
        }
    }
}
