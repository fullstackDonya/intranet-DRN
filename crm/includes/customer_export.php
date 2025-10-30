<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=customers_export_' . date('Ymd_His') . '.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['id','name','email','phone','postal_code','address','country','role','status','created_at','updated_at']);

$stmt = $pdo->query("SELECT id, name, email, phone, postal_code, address, country, role, status, created_at, updated_at FROM customers ORDER BY id DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($out, $row);
}

fclose($out);
exit;
