<?php

session_start();
require_once __DIR__ . '/../config/database.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: ../../login.php');
    exit;
}

// Single-tenant: plus de customer_id nécessaire

// Récupération et sécurisation des champs
$name            = trim($_POST['name'] ?? '');
$industry        = trim($_POST['industry'] ?? '');
$website         = trim($_POST['website'] ?? '');
$phone           = trim($_POST['phone'] ?? '');
$email           = trim($_POST['email'] ?? '');
$address         = trim($_POST['address'] ?? '');
$city            = trim($_POST['city'] ?? '');
$postal_code     = trim($_POST['postal_code'] ?? '');
$country         = trim($_POST['country'] ?? 'France');
$employee_count  = $_POST['employee_count'] ?? null;
$annual_revenue  = $_POST['annual_revenue'] ?? null;
$status          = $_POST['status'] ?? 'prospect';
$source          = trim($_POST['source'] ?? '');
$notes           = trim($_POST['notes'] ?? '');


// Vérifie si une société existe déjà avec ce nom
$stmt = $pdo->prepare("SELECT id FROM companies WHERE name = ?");
$stmt->execute([$name]);
$existing_company = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_company) {
    // Elle existe : rien d'autre à lier en single-tenant
    header('Location: ../index.php');
    exit;
}

// Sinon, création de la société
$stmt = $pdo->prepare("INSERT INTO companies (name, industry, website, phone, email, address, city, postal_code, country, employee_count, annual_revenue, status, source, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
$stmt->execute([
    $name, $industry, $website, $phone, $email, $address, $city, $postal_code, $country,
    $employee_count, $annual_revenue, $status, $source, $notes
]);

header('Location: ../index.php');
exit;