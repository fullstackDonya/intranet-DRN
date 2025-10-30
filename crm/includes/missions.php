<?php

require_once __DIR__ . '/../config/database.php';

// Récupérer l'ID du client connecté (exemple : stocké dans $_SESSION['customer_id'])
$customer_id = $_SESSION['customer_id'] ?? 0;

// Titre de page adapté à l'immobilier
$page_title = "Visites immobilières - DRN CRM";

// Filtres (sans changer la structure BDD)
$date_from = isset($_GET['date_from']) ? trim((string)$_GET['date_from']) : '';
$date_to   = isset($_GET['date_to']) ? trim((string)$_GET['date_to']) : '';
$status    = isset($_GET['status']) ? trim((string)$_GET['status']) : '';
$agent     = isset($_GET['agent']) ? trim((string)$_GET['agent']) : '';
$property  = isset($_GET['property']) ? trim((string)$_GET['property']) : '';

// Charger les missions liées au client, avec filtres optionnels
$sql = "
    SELECT 
        m.id AS mission_id,
        m.departure,
        m.arrival,
        m.datetime,
        m.driver,
        m.vehicle,
        m.status_id,
        s.name AS status_name,
        m.created_at,
        f.id AS folder_id,
        f.name AS folder_name,
        c.name AS company_name
    FROM missions m
    INNER JOIN folders f ON m.folder_id = f.id
    INNER JOIN companies c ON f.company_id = c.id
    LEFT JOIN statuses s ON m.status_id = s.id
    WHERE c.customer_id = ?
";

$params = [$customer_id];

if ($date_from !== '') {
    $sql .= " AND DATE(m.datetime) >= ?";
    $params[] = $date_from;
}
if ($date_to !== '') {
    $sql .= " AND DATE(m.datetime) <= ?";
    $params[] = $date_to;
}
if ($status !== '') {
    $sql .= " AND (s.name LIKE ?)";
    $params[] = "%$status%";
}
if ($agent !== '') {
    $sql .= " AND (m.driver LIKE ?)";
    $params[] = "%$agent%";
}
if ($property !== '') {
    // Recherche sur l'adresse d'arrivée (bien), le nom du dossier ou de la société
    $sql .= " AND (m.arrival LIKE ? OR f.name LIKE ? OR c.name LIKE ?)";
    $params[] = "%$property%";
    $params[] = "%$property%";
    $params[] = "%$property%";
}

$sql .= " ORDER BY m.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$missions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Petites métriques pour tableaux de bord simples
$nowTs = time();
$today = date('Y-m-d');
$kpi_total = count($missions);
$kpi_today = 0;
$kpi_upcoming = 0; // futur strict
$kpi_past = 0;     // passé strict

foreach ($missions as $m) {
    $dt = $m['datetime'] ?? null;
    if (!$dt) { continue; }
    $ts = strtotime($dt);
    if ($ts === false) { continue; }
    $d = date('Y-m-d', $ts);
    if ($d === $today) { $kpi_today++; }
    if ($ts > $nowTs) { $kpi_upcoming++; }
    if ($ts < $nowTs) { $kpi_past++; }
}

$missions_kpis = [
    'total' => $kpi_total,
    'today' => $kpi_today,
    'upcoming' => $kpi_upcoming,
    'past' => $kpi_past,
];
