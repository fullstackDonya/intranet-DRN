<?php
require_once __DIR__ . '/verify_subscriptions.php';

$customer_id = $_SESSION['customer_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? ($user['id'] ?? null);

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $company_name = trim($_POST['company'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $source = trim($_POST['source'] ?? 'website');
        $status = trim($_POST['status'] ?? 'new');
        $budget = $_POST['budget'] ?? null;
        $interest = trim($_POST['interest'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($first === '' || $last === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Champs requis manquants ou email invalide.');
        }

        // Resolve/create company
        $company_id = null;
        if ($company_name !== '') {
            $s = $pdo->prepare('SELECT id FROM companies WHERE name = ?' . ($customer_id ? ' AND customer_id = ?' : '') . ' LIMIT 1');
            $s->execute($customer_id ? [$company_name, $customer_id] : [$company_name]);
            $company_id = $s->fetchColumn();
            if (!$company_id) {
                try {
                    $pdo->prepare('INSERT INTO companies (name, customer_id' . ($user_id ? ', assigned_to' : '') . ') VALUES (' . ($user_id ? '?,?,?' : '?,?') . ')')->execute($user_id ? [$company_name, $customer_id ?? null, $user_id] : [$company_name, $customer_id ?? null]);
                    $company_id = (int)$pdo->lastInsertId();
                } catch (Exception $e) {
                    try { $pdo->prepare('INSERT INTO companies (name) VALUES (?)')->execute([$company_name]); $company_id = (int)$pdo->lastInsertId(); } catch (Exception $e2) { $company_id = null; }
                }
            }
        }

        // Upsert contact as lead
        $stmt = $pdo->prepare('SELECT id FROM contacts WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            $upd = $pdo->prepare('UPDATE contacts SET first_name=?, last_name=?, phone=?, company_id=?, position=?, source=?, status=?, budget=?, interest=?, notes=?, assigned_to=?, customer_id=? WHERE id=?');
            $upd->execute([
                $first,
                $last,
                $phone ?: null,
                $company_id,
                $position ?: null,
                $source ?: null,
                $status ?: 'new',
                is_numeric($budget) ? (float)$budget : null,
                $interest ?: null,
                $notes ?: null,
                $user_id,
                $customer_id,
                $existingId
            ]);
        } else {
            $ins = $pdo->prepare('INSERT INTO contacts (first_name,last_name,email,phone,company_id,position,source,status,budget,interest,notes,assigned_to,customer_id,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())');
            $ins->execute([
                $first,
                $last,
                $email,
                $phone ?: null,
                $company_id,
                $position ?: null,
                $source ?: null,
                $status ?: 'new',
                is_numeric($budget) ? (float)$budget : null,
                $interest ?: null,
                $notes ?: null,
                $user_id,
                $customer_id
            ]);
        }

        header('Location: ../leads.php?success=1');
        exit;
    } catch (Throwable $e) {
        $error_message = $e->getMessage();
        header('Location: ../leads-add.php?error=' . urlencode($error_message));
        exit;
    }
}