<?php
declare(strict_types=1);

require_once __DIR__ . '/../../crm/config/database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed. Use POST.']);
    exit;
}

// Allowed filters
$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
$status = isset($_POST['status']) ? trim((string)$_POST['status']) : null; // pending|approved|rejected|cancelled
$id = isset($_POST['id']) ? (int)$_POST['id'] : null;

$startDateFrom = isset($_POST['start_date_from']) ? $_POST['start_date_from'] : null; // YYYY-MM-DD
$startDateTo = isset($_POST['start_date_to']) ? $_POST['start_date_to'] : null;
$endDateFrom = isset($_POST['end_date_from']) ? $_POST['end_date_from'] : null;
$endDateTo = isset($_POST['end_date_to']) ? $_POST['end_date_to'] : null;
$createdFrom = isset($_POST['created_from']) ? $_POST['created_from'] : null; // YYYY-MM-DD
$createdTo = isset($_POST['created_to']) ? $_POST['created_to'] : null;

// Pagination
$page = max(1, (int)($_POST['page'] ?? 1));
$perPage = (int)($_POST['per_page'] ?? 25);
if ($perPage <= 0 || $perPage > 200) { $perPage = 25; }
$offset = ($page - 1) * $perPage;

// Sorting
$allowedSortBy = ['id','user_id','product_id','start_date','end_date','status','created_at'];
$sortBy = $_POST['sort_by'] ?? 'created_at';
$sortBy = in_array($sortBy, $allowedSortBy, true) ? $sortBy : 'created_at';
$sortDir = strtolower((string)($_POST['sort_dir'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';

$where = [];
$params = [];

if ($id) { $where[] = 'rr.id = :id'; $params[':id'] = $id; }
if ($userId) { $where[] = 'rr.user_id = :user_id'; $params[':user_id'] = $userId; }
if ($productId) { $where[] = 'rr.product_id = :product_id'; $params[':product_id'] = $productId; }
if ($status) { $where[] = 'rr.status = :status'; $params[':status'] = $status; }

if ($startDateFrom) { $where[] = 'rr.start_date >= :start_from'; $params[':start_from'] = $startDateFrom; }
if ($startDateTo) { $where[] = 'rr.start_date <= :start_to'; $params[':start_to'] = $startDateTo; }
if ($endDateFrom) { $where[] = 'rr.end_date >= :end_from'; $params[':end_from'] = $endDateFrom; }
if ($endDateTo) { $where[] = 'rr.end_date <= :end_to'; $params[':end_to'] = $endDateTo; }
if ($createdFrom) { $where[] = 'DATE(rr.created_at) >= :created_from'; $params[':created_from'] = $createdFrom; }
if ($createdTo) { $where[] = 'DATE(rr.created_at) <= :created_to'; $params[':created_to'] = $createdTo; }

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

try {
    // Total count
    $countSql = "SELECT COUNT(*) AS cnt FROM rental_requests rr $whereSql";
    $countStmt = $pdo->prepare($countSql);
    foreach ($params as $k => $v) { $countStmt->bindValue($k, $v); }
    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();

    // Data query with optional join on products for extra context
    $sql = "
        SELECT rr.id, rr.user_id, rr.product_id, rr.start_date, rr.end_date, rr.quantity, rr.message, rr.status, rr.created_at, rr.updated_at,
               p.name AS product_name, p.sku AS product_sku
        FROM rental_requests rr
        LEFT JOIN erp_products p ON p.id = rr.product_id
        $whereSql
        ORDER BY rr.$sortBy $sortDir
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'data' => $rows,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
