<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/database.php';

$page_title = "Clients - CRM Intelligent";

$user_id = $_SESSION["user_id"];

// Récupérer tous les clients assignés à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM companies WHERE assigned_to = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compter les clients (statut client)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM companies WHERE status = 'client' AND assigned_to = ?");
$stmt->execute([$user_id]);
$total_clients = $stmt->fetchColumn();

// Compter les clients actifs
$stmt = $pdo->prepare("SELECT COUNT(*) FROM companies WHERE status = 'client' AND is_active = 1 AND assigned_to = ?");
$stmt->execute([$user_id]);
$active_clients = $stmt->fetchColumn();

// Revenus moyens
$stmt = $pdo->prepare("SELECT AVG(annual_revenue) FROM companies WHERE annual_revenue IS NOT NULL AND assigned_to = ?");
$stmt->execute([$user_id]);
$avg_revenue = $stmt->fetchColumn();

// Satisfaction moyenne
$stmt = $pdo->prepare("SELECT AVG(satisfaction_score) FROM companies WHERE satisfaction_score IS NOT NULL AND assigned_to = ?");
$stmt->execute([$user_id]);
$satisfaction_score = $stmt->fetchColumn();

