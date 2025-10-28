<?php

require_once __DIR__ . '/../../crm/config/database.php';
include_once __DIR__ . '/../../crm/includes/auth.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

/* --- API & DB helpers --- */
function fetchShiftsInRange(PDO $pdo, string $start, string $end, ?int $companyId = null, ?int $employeeId = null): array {

    // start, end obligatoires dans les paramètres
    $params = [$start, $end];
    $sql = "SELECT s.*, e.first_name, e.last_name, c.name AS company_name
            FROM erp_shifts s
            LEFT JOIN erp_employees e ON e.id = s.employee_id
            LEFT JOIN erp_companies c ON c.id = s.company_id
            WHERE s.start_datetime >= ? AND s.end_datetime <= ?";

    if ($companyId !== null) {
        $sql .= " AND s.company_id = ?";
        $params[] = $companyId;
    }
    if ($employeeId !== null) {
        $sql .= " AND s.employee_id = ?";
        $params[] = $employeeId;
    }
    $sql .= " ORDER BY s.start_datetime";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function createShift(PDO $pdo, array $data): int {
    // allow employee_id nullable (company-only shift)
    $employee = isset($data['employee_id']) && $data['employee_id'] > 0 ? $data['employee_id'] : null;
    $company = isset($data['company_id']) && $data['company_id'] > 0 ? $data['company_id'] : null;
    if (!$employee && !$company) {
        throw new InvalidArgumentException('employee_id or company_id required');
    }
    $sql = "INSERT INTO erp_shifts (employee_id, start_datetime, end_datetime, role, notes, company_id, created_at)
            VALUES (?,?,?,?,?,?,NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $employee,
        $data['start_datetime'],
        $data['end_datetime'],
        $data['role'] ?? null,
        $data['notes'] ?? null,
        $company,
    ]);
    return (int)$pdo->lastInsertId();
}

function updateShift(PDO $pdo, int $id, array $data): bool {

    $employee = isset($data['employee_id']) && $data['employee_id'] > 0 ? $data['employee_id'] : null;
    $company = isset($data['company_id']) && $data['company_id'] > 0 ? $data['company_id'] : null;
    if (!$employee && !$company) {
        throw new InvalidArgumentException('employee_id or company_id required');
    }
    $stmt = $pdo->prepare("UPDATE erp_shifts SET employee_id=?, start_datetime=?, end_datetime=?, role=?, notes=?, company_id=? WHERE id=?");
    return $stmt->execute([$employee, $data['start_datetime'], $data['end_datetime'], $data['role'] ?? null, $data['notes'] ?? null, $company, $id]);
}

function deleteShift(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("DELETE FROM erp_shifts WHERE id=?");
    return $stmt->execute([$id]);
}

/* --- API routing --- */
$action = $_REQUEST['action'] ?? 'view';
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $start = $_GET['start'] ?? null;
    $end = $_GET['end'] ?? null;
    $companyId = isset($_GET['company_id']) && $_GET['company_id'] !== '' ? (int)$_GET['company_id'] : null;
    $employeeId = isset($_GET['employee_id']) && $_GET['employee_id'] !== '' ? (int)$_GET['employee_id'] : null;
    if (!$start || !$end) { http_response_code(400); echo json_encode(['error' => 'start and end required']); exit; }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(fetchShiftsInRange($pdo, $start, $end, $companyId, $employeeId));
    exit;
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'employee_id' => isset($_POST['employee_id']) ? (int)$_POST['employee_id'] : 0,
        'start_datetime' => $_POST['start_datetime'] ?? '',
        'end_datetime' => $_POST['end_datetime'] ?? '',
        'role' => trim((string)($_POST['role'] ?? '')),
        'notes' => trim((string)($_POST['notes'] ?? '')),
        'company_id' => isset($_POST['company_id']) && $_POST['company_id'] !== '' ? (int)$_POST['company_id'] : null,
    ];
    // validation: allow employee nullable if company provided
    $employee_ok = $data['employee_id'] > 0;
    $company_ok = !empty($data['company_id']);
    if ((!$employee_ok && !$company_ok) || !$data['start_datetime'] || !$data['end_datetime']) {
        // log for debugging
        $log = date('c') . ' CREATE SHIFT VALIDATION FAILED: ' . json_encode($data) . PHP_EOL;
        file_put_contents(sys_get_temp_dir() . '/shifts_debug.log', $log, FILE_APPEND);
        http_response_code(400);
        echo json_encode([
            'error' => 'Invalid data: employee or company required, start & end required',
            'received' => $data
        ]);
        exit;
    }

    // create the shift and return id (with error handling)
    try {
        $id = createShift($pdo, $data);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['id' => $id]);
        exit;
    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage(), 'received' => $data]);
        exit;
    } catch (PDOException $e) {
        // log DB error
        $log = date('c') . ' CREATE SHIFT DB ERROR: ' . $e->getMessage() . ' -- ' . json_encode($data) . PHP_EOL;
        file_put_contents(sys_get_temp_dir() . '/shifts_debug.log', $log, FILE_APPEND);
        http_response_code(500);
        echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
        exit;
    }
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        'employee_id' => isset($_POST['employee_id']) ? (int)$_POST['employee_id'] : 0,
        'start_datetime' => $_POST['start_datetime'] ?? '',
        'end_datetime' => $_POST['end_datetime'] ?? '',
        'role' => trim((string)($_POST['role'] ?? '')),
        'notes' => trim((string)($_POST['notes'] ?? '')),
        'company_id' => isset($_POST['company_id']) && $_POST['company_id'] !== '' ? (int)$_POST['company_id'] : null,
    ];
    $employee_ok = $data['employee_id'] > 0;
    $company_ok = !empty($data['company_id']);
    if ($id <= 0 || (!$employee_ok && !$company_ok)) { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
    try {
        $ok = updateShift($pdo, $id, $data);
    } catch (Exception $e) {
        http_response_code(400); echo json_encode(['error' => $e->getMessage()]); exit;
    }
    header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => (bool)$ok]); exit;
}
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); echo json_encode(['error'=>'Invalid id']); exit; }
    $ok = deleteShift($pdo, $id);
    header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => (bool)$ok]); exit;
}

/* --- Prepare data for view --- */
$employeesStmt = $pdo->prepare("SELECT id, first_name, last_name FROM erp_employees ORDER BY last_name");
$employeesStmt->execute();
$employees = $employeesStmt->fetchAll(PDO::FETCH_ASSOC);

$companiesStmt = $pdo->prepare("SELECT id, name FROM erp_companies ORDER BY name");
$companiesStmt->execute();
$companies = $companiesStmt->fetchAll(PDO::FETCH_ASSOC);