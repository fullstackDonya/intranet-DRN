<?php

require_once __DIR__ . '/../../crm/config/database.php';
include_once __DIR__ . '/../../crm/includes/auth.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$customer_id = $_SESSION['customer_id'] ?? null; // legacy (unused)

$action = $_GET['action'] ?? 'list';
$period = trim((string)($_GET['period'] ?? '')); // format attendu YYYY-MM

function validatePeriod(string $p): bool {
    return $p === '' || preg_match('/^\d{4}-\d{2}$/', $p) === 1;
}

/* Récupère les fiches en filtrant par période et par customer_id si défini */
function fetchPayrolls(PDO $pdo, string $period = ''): array {
    $params = [];
    $sql = "SELECT p.*, e.first_name, e.last_name, e.email FROM erp_payrolls p JOIN erp_employees e ON e.id = p.employee_id";
    if ($period !== '') {
        $sql .= " WHERE p.period = ?";
        $params[] = $period;
    }
    $sql .= " ORDER BY e.last_name, e.first_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Export des employés — apply customer_id filter si présent */
function fetchEmployeesForExport(PDO $pdo): array {
    $stmt = $pdo->query("SELECT id, first_name, last_name, email, hire_date, base_salary, job_title, department, contract_type, status FROM erp_employees ORDER BY last_name, first_name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Résumé (nombre / coût total) filtré par période et customer_id si défini */
function summaryForPeriod(PDO $pdo, string $period = ''): array {
    if ($period === '') {
        $stmt = $pdo->query("SELECT COUNT(*) as cnt, COALESCE(SUM(net_pay + employer_contrib),0) as total_cost FROM erp_payrolls");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['cnt' => 0, 'total_cost' => 0];
    }
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt, COALESCE(SUM(net_pay + employer_contrib),0) as total_cost FROM erp_payrolls WHERE period = ?");
    $stmt->execute([$period]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['cnt' => 0, 'total_cost' => 0];
}

/* Exports */
if ($action === 'export_payrolls') {
    if (!validatePeriod($period)) { http_response_code(400); echo 'Période invalide'; exit; }
    $rows = fetchPayrolls($pdo, $period);
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="payrolls' . ($period ? "_$period" : '') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['payroll_id','employee_id','last_name','first_name','email','period','gross_salary','bonus','overtime','deductions','employee_contrib','employer_contrib','net_pay','created_at']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['id'],$r['employee_id'],$r['last_name'],$r['first_name'],$r['email'],$r['period'],
            $r['gross_salary'],$r['bonus'],$r['overtime'],$r['deductions'],$r['employee_contrib'],$r['employer_contrib'],$r['net_pay'],$r['created_at']
        ]);
    }
    fclose($out);
    exit;
}

if ($action === 'export_employees') {
    $rows = fetchEmployeesForExport($pdo);
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="employees' . ($customer_id ? "_c{$customer_id}" : '') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['id','first_name','last_name','email','hire_date','base_salary','job_title','department','contract_type','status']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['id'],$r['first_name'],$r['last_name'],$r['email'],$r['hire_date'],$r['base_salary'],$r['job_title'],$r['department'],$r['contract_type'],$r['status']
        ]);
    }
    fclose($out);
    exit;
}

/* Page affichage : liste + résumé */
if (!validatePeriod($period)) { $period = ''; }

$payrolls = fetchPayrolls($pdo, $period);
$summary = summaryForPeriod($pdo, $period);