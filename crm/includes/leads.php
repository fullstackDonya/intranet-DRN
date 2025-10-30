<?php
require_once __DIR__ . '/../config/database.php';

$customer_id = $_SESSION['customer_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? ($user['id'] ?? null);

// Ensure minimal leads support using contacts table with status='lead'
$leads_kpis = [
    'new_leads' => 0,
    'qualified' => 0,
    'conversion_rate' => '0%',
    'avg_score' => '--',
];
$leads_list = [];

try {
    // New leads in last 30 days
    if ($customer_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE customer_id = ? AND status = 'lead' AND created_at >= (NOW() - INTERVAL 30 DAY)");
        $stmt->execute([$customer_id]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'lead' AND created_at >= (NOW() - INTERVAL 30 DAY)");
    }
    $leads_kpis['new_leads'] = (int)$stmt->fetchColumn();
} catch (Throwable $e) {}

try {
    if ($customer_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE customer_id = ? AND status = 'qualified'");
        $stmt->execute([$customer_id]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'qualified'");
    }
    $leads_kpis['qualified'] = (int)$stmt->fetchColumn();
} catch (Throwable $e) {}

try {
    // Conversion rate approximation: qualified / total leads
    if ($customer_id) {
        $tstmt = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE customer_id = ? AND status IN ('lead','qualified')");
        $tstmt->execute([$customer_id]);
        $total = (int)$tstmt->fetchColumn();
        $q = (int)$leads_kpis['qualified'];
    } else {
        $total = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE status IN ('lead','qualified')")->fetchColumn();
        $q = (int)$leads_kpis['qualified'];
    }
    $rate = $total > 0 ? round(100 * $q / $total, 1) : 0;
    $leads_kpis['conversion_rate'] = $rate . '%';
} catch (Throwable $e) {}

try {
    // Avg AI score if column exists
    $avg = null;
    try {
        if ($customer_id) {
            $s = $pdo->prepare("SELECT AVG(ai_score) FROM contacts WHERE customer_id = ? AND status IN ('lead','qualified') AND ai_score IS NOT NULL");
            $s->execute([$customer_id]);
        } else {
            $s = $pdo->query("SELECT AVG(ai_score) FROM contacts WHERE status IN ('lead','qualified') AND ai_score IS NOT NULL");
        }
        $avg = $s->fetchColumn();
    } catch (Throwable $ignore) { $avg = null; }
    if ($avg !== null) { $leads_kpis['avg_score'] = (string)round((float)$avg, 1); }
} catch (Throwable $e) {}

try {
    // List leads joined with company name if available
    if ($customer_id) {
        $sql = "SELECT c.id, c.first_name, c.last_name, c.email, c.status, c.created_at, c.ai_score, co.name AS company_name
                FROM contacts c
                LEFT JOIN companies co ON c.company_id = co.id
                WHERE (co.customer_id = :cid OR co.customer_id IS NULL) AND c.status IN ('lead','qualified')
                ORDER BY c.created_at DESC LIMIT 200";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cid' => $customer_id]);
    } else {
        $sql = "SELECT c.id, c.first_name, c.last_name, c.email, c.status, c.created_at, c.ai_score, co.name AS company_name
                FROM contacts c
                LEFT JOIN companies co ON c.company_id = co.id
                WHERE c.status IN ('lead','qualified')
                ORDER BY c.created_at DESC LIMIT 200";
        $stmt = $pdo->query($sql);
    }
    $leads_list = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $e) {
    $leads_list = [];
}