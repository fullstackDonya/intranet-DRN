<?php

require_once __DIR__ . '/../config/database.php';

$page_title = "Pipeline Opportunités - CRM Intelligent";
// $user = getCurrentUser();
// $user_id = $user['id'];

// Valeur totale du pipeline (hors fermé gagné/perdu)
$stmt = $pdo->prepare("SELECT SUM(amount) FROM opportunities WHERE assigned_to = ? AND stage NOT IN ('closed_won','closed_lost')");
$stmt->execute([$user_id]);
$pipeline_value = $stmt->fetchColumn() ?: 0;

// Nombre d'opportunités actives
$stmt = $pdo->prepare("SELECT COUNT(*) FROM opportunities WHERE assigned_to = ? AND stage NOT IN ('closed_won','closed_lost')");
$stmt->execute([$user_id]);
$active_opportunities = $stmt->fetchColumn() ?: 0;

// Nombre et valeur des opportunités gagnées
$stmt = $pdo->prepare("SELECT COUNT(*), SUM(amount) FROM opportunities WHERE assigned_to = ? AND stage = 'closed_won'");
$stmt->execute([$user_id]);
list($won_count, $won_value) = $stmt->fetch(PDO::FETCH_NUM);
$won_count = $won_count ?: 0;
$won_value = $won_value ?: 0;

// Nombre et valeur des opportunités perdues
$stmt = $pdo->prepare("SELECT COUNT(*), SUM(amount) FROM opportunities WHERE assigned_to = ? AND stage = 'closed_lost'");
$stmt->execute([$user_id]);
list($lost_count, $lost_value) = $stmt->fetch(PDO::FETCH_NUM);
$lost_count = $lost_count ?: 0;
$lost_value = $lost_value ?: 0;

// Taux de fermeture
$total_closed = $won_count + $lost_count;
$close_rate = $total_closed > 0 ? round(($won_count / $total_closed) * 100) : 0;

// Cycle moyen (en jours) pour les opportunités gagnées
$stmt = $pdo->prepare("SELECT created_at, actual_close_date FROM opportunities WHERE assigned_to = ? AND stage = 'closed_won' AND actual_close_date IS NOT NULL");
$stmt->execute([$user_id]);
$cycles = $stmt->fetchAll(PDO::FETCH_ASSOC);
$cycle_sum = 0;
foreach ($cycles as $row) {
    $start = strtotime($row['created_at']);
    $end = strtotime($row['actual_close_date']);
    if ($start && $end && $end > $start) {
        $cycle_sum += round(($end - $start) / 86400);
    }
}
$avg_cycle = ($won_count > 0 && $cycle_sum > 0) ? round($cycle_sum / $won_count) : 0;