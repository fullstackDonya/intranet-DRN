<?php

require_once __DIR__ . '/../../crm/config/database.php';
// include_once __DIR__ . '/../../crm/includes/auth.php';


// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../login.php');
//     exit();
// }

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// single-tenant; no debug output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'auto_generate') {
    $currentPeriod = date('Y-m');
    try {
        if ($customer_id) {
            $stmt = $pdo->prepare("SELECT id, base_salary FROM erp_employees WHERE status = 'active' ");
            $stmt->execute([$customer_id]);
        } else {
            $stmt = $pdo->query("SELECT id, base_salary FROM erp_employees WHERE status = 'active' ");
        }
        $employeesToProcess = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pdo->beginTransaction();

        if ($customer_id) {
            $insert = $pdo->prepare(
                "INSERT INTO erp_payrolls (employee_id, period, gross_salary, bonus, overtime, deductions, employee_contrib, employer_contrib, net_pay, customer_id, created_at)
                 VALUES (?, ?, ?, 0, 0, 0, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE
                   gross_salary=VALUES(gross_salary),
                   bonus=VALUES(bonus),
                   overtime=VALUES(overtime),
                   deductions=VALUES(deductions),
                   employee_contrib=VALUES(employee_contrib),
                   employer_contrib=VALUES(employer_contrib),
                   net_pay=VALUES(net_pay),
                   customer_id=VALUES(customer_id),
                   created_at=NOW()"
            );
        
       

        foreach ($employeesToProcess as $emp) {
            $gross = (float)$emp['base_salary'];
            $employeeContrib = round($gross * 0.22, 2);
            $employerContrib = round($gross * 0.42, 2);
            $net = round($gross - $employeeContrib, 2);

            if ($customer_id) {
                $insert->execute([
                    (int)$emp['id'],
                    $currentPeriod,
                    $gross,
                    $employeeContrib,
                    $employerContrib,
                    $net,
                    $customer_id,
                ]);
            } else {
                $insert->execute([
                    (int)$emp['id'],
                    $currentPeriod,
                    $gross,
                    $employeeContrib,
                    $employerContrib,
                    $net,
                ]);
            }
        }

        $pdo->commit();

        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        error_log("Erreur génération paies automatiques : " . $e->getMessage());
    }
    header('Location: index.php');
    exit;
}

try {
    // métriques — respecter customer_id si présent
   
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM erp_employees ");
        $stmt->execute([]);
        $totalEmployees = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM erp_employees WHERE status = 'active' ");
        $stmt->execute([]);
        $activeEmployees = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT AVG(base_salary) FROM erp_employees WHERE base_salary > 0 ");
        $stmt->execute([]);
        $avgSalary = $stmt->fetchColumn();
    
    $avgSalary = $avgSalary !== null ? round((float)$avgSalary, 2) : 0.00;

    $currentPeriod = date('Y-m');


        $stmt = $pdo->prepare("SELECT COUNT(*) FROM erp_payrolls WHERE period = ? ");
        $stmt->execute([$currentPeriod]);
        $payrollsThisMonth = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(net_pay + employer_contrib),0) FROM erp_payrolls WHERE period = ? ");
        $stmt->execute([$currentPeriod]);
        $totalPayrollCost = (float)$stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT id, first_name, last_name, hire_date, job_title FROM erp_employees WHERE hire_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)  ORDER BY hire_date DESC LIMIT 8");
        $stmt->execute([]);
        $recentHires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT p.id, p.period, p.net_pay, e.first_name, e.last_name FROM erp_payrolls p JOIN erp_employees e ON e.id = p.employee_id  ORDER BY p.created_at DESC LIMIT 8");
        $stmt->execute([]);
        $recentPayrolls = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

} catch (Throwable $e) {
    // en cas d'erreur, on garde des valeurs par défaut et on log
    error_log("ERP dashboard error: " . $e->getMessage());
    $totalEmployees = $activeEmployees = 0;
    $avgSalary = 0.0;
    $payrollsThisMonth = 0;
    $totalPayrollCost = 0.0;
    $recentHires = [];
    $recentPayrolls = [];
}