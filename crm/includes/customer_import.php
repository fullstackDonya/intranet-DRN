<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';

$report = ['inserted'=>0,'skipped'=>0,'errors'=>[]];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv']) && is_uploaded_file($_FILES['csv']['tmp_name'])) {
    $fh = fopen($_FILES['csv']['tmp_name'], 'r');
    if ($fh === false) { $report['errors'][] = 'Impossible de lire le fichier.'; }
    else {
        // Expect header
        $header = fgetcsv($fh);
        // Map indices
        $map = array_flip(array_map('strtolower', $header ?: []));
        $required = ['name','email'];
        foreach ($required as $req) { if (!isset($map[$req])) { $report['errors'][] = "Colonne requise manquante: $req"; } }
        if (!$report['errors']) {
            while (($row = fgetcsv($fh)) !== false) {
                $name = trim($row[$map['name']] ?? '');
                $email = trim($row[$map['email']] ?? '');
                if ($name === '' || $email === '') { $report['skipped']++; continue; }
                $phone = trim($row[$map['phone']] ?? '') ?: null;
                $postal_code = trim($row[$map['postal_code']] ?? '') ?: null;
                $address = trim($row[$map['address']] ?? '') ?: null;
                $country = trim($row[$map['country']] ?? '') ?: null;
                $role = strtolower(trim($row[$map['role']] ?? 'client'));
                if (!in_array($role, ['admin','manager','client','partner'], true)) $role = 'client';
                $status = strtolower(trim($row[$map['status']] ?? 'active'));
                if (!in_array($status, ['active','inactive'], true)) $status = 'active';
                try {
                    $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone, postal_code, address, country, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    $stmt->execute([$name,$email,$phone,$postal_code,$address,$country,$role,$status]);
                    $report['inserted']++;
                } catch (Throwable $e) {
                    $report['errors'][] = 'Ligne ignorée (' . $email . '): ' . $e->getMessage();
                    $report['skipped']++;
                }
            }
        }
        fclose($fh);
    }
}
