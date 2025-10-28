<?php

// voir toutes les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../config/database.php';
$user_id = $_SESSION['user_id'] ?? null;

// Récupère l'email de l'utilisateur
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_email = $user['email'] ?? '';



// Récupérer tous les customers pour la liste déroulante
$all_customers = $pdo->query("SELECT id, name, email FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);



// Pour pré-remplir le formulaire société si besoin
$existing_company = null;

// récupère le nom passé en paramètre (GET ou POST) — évite d'exécuter une requête avec un placeholder vide
$companyName = trim((string)($_GET['company_name'] ?? $_POST['company_name'] ?? ''));

if ($companyName !== '') {
    // Utilise la bonne table : ici erp_companies (adapte si ta table s'appelle companies)
    $stmt = $pdo->prepare("SELECT * FROM erp_companies WHERE name = ? LIMIT 1");
    $stmt->execute([$companyName]);
    $existing_company = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} else {
    $existing_company = null;
}


// --- KPI server-side pour l'affichage initial du dashboard ---
$total_revenue = 0.0;
$active_clients = 0;
$conversion_rate = 0.0; // en %
$opportunities = 0;



// Fallback: si pas de customer_id, calculer des KPI basés sur l'utilisateur (assigned_to)
try {
    // Total revenue for user's closed_won opportunities
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM opportunities WHERE assigned_to = ? AND stage = 'closed_won'");
    $stmt->execute([$user_id]);
    $total_revenue = floatval($stmt->fetchColumn());

    // Active clients assigned to user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM companies WHERE assigned_to = ? AND status = 'client' AND is_active = 1");
    $stmt->execute([$user_id]);
    $active_clients = intval($stmt->fetchColumn());

    // Open opportunities assigned to user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM opportunities WHERE assigned_to = ? AND stage NOT IN ('closed_won','closed_lost')");
    $stmt->execute([$user_id]);
    $opportunities = intval($stmt->fetchColumn());

    // Conversion rate for user's deals in last 30 days
    $stmt = $pdo->prepare("SELECT SUM(CASE WHEN stage = 'closed_won' THEN 1 ELSE 0 END) as won_deals, COUNT(*) as total_deals FROM opportunities WHERE assigned_to = ? AND created_at BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['total_deals'] > 0) {
        $conversion_rate = round(($row['won_deals'] / $row['total_deals']) * 100, 1);
    } else {
        $conversion_rate = 0.0;
    }
} catch (Exception $e) {
    error_log('KPI fallback load error: ' . $e->getMessage());
}

