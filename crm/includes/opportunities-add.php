<?php

include("verify_subscriptions.php");

$user = getCurrentUser();
$user_id = $user['id'];

$success_message = $error_message = null;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("
            INSERT INTO opportunities (
                title, description, company_id, contact_id, assigned_to, stage, probability, amount, expected_close_date, source, competitor, loss_reason, next_action, next_action_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            !empty($_POST['company_id']) ? $_POST['company_id'] : null,
            !empty($_POST['contact_id']) ? $_POST['contact_id'] : null,
            $user_id,
            $_POST['stage'],
            !empty($_POST['probability']) ? $_POST['probability'] : 0,
            !empty($_POST['amount']) ? $_POST['amount'] : 0,
            !empty($_POST['expected_close_date']) ? $_POST['expected_close_date'] : null,
            $_POST['source'],
            $_POST['competitor'],
            $_POST['loss_reason'],
            $_POST['next_action'],
            !empty($_POST['next_action_date']) ? $_POST['next_action_date'] : null
        ]);

        $success_message = "Opportunité ajoutée avec succès !";
    } catch (Exception $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}

// Liste des étapes possibles
$stages = [
    'prospecting' => 'Prospection',
    'qualification' => 'Qualification',
    'needs_analysis' => 'Analyse',
    'proposal' => 'Proposition',
    'negotiation' => 'Négociation',
    'closed_won' => 'Fermé Gagné',
    'closed_lost' => 'Fermé Perdu'
];