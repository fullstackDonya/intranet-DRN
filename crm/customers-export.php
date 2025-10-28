<?php
require_once __DIR__ . '/includes/verify_subscriptions.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="customers-export-'.date('Ymd-His').'.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { http_response_code(401); echo "unauthenticated"; exit; }

$headers = ['ID','Nom','Email','Téléphone','Secteur','Revenus annuels','Statut','Actif','Score satisfaction','Créé le','Mis à jour le'];
fputcsv($out, $headers);

$sql = "SELECT id, name, email, phone, industry, annual_revenue, status, is_active, satisfaction_score, created_at, updated_at FROM companies WHERE assigned_to = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($out, [
        $row['id'],
        $row['name'] ?? '',
        $row['email'] ?? '',
        $row['phone'] ?? '',
        $row['industry'] ?? '',
        $row['annual_revenue'] ?? '',
        $row['status'] ?? '',
        isset($row['is_active']) ? (int)$row['is_active'] : 0,
        $row['satisfaction_score'] ?? '',
        $row['created_at'] ?? '',
        $row['updated_at'] ?? '',
    ]);
}

fclose($out);
exit;