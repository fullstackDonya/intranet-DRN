<?php
require_once __DIR__ . '/../../crm/config/database.php';
header('Content-Type: application/json');

$stmt = $pdo->query('SELECT id, first_name, last_name, email, job_title, department, status FROM erp_employees ORDER BY last_name, first_name');
$rows = $stmt->fetchAll();
echo json_encode(['data' => $rows]);