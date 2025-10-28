<?php

require_once __DIR__ . '/../config/database.php';


// If an id is provided, load that campaign
$campaign = null;
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $sql = 'SELECT * FROM campaigns WHERE id = ?';
    $params = [$id];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $campaign = $stmt->fetch();
}

// Récupérer les companies liées au même customer_id pour les listes d'audience
$companies = [];

    try{
        $cstmt = $pdo->prepare('SELECT id, name FROM companies ORDER BY name ASC');
        $cstmt->execute([]);
        $companies = $cstmt->fetchAll();
    }catch(Exception $e){
        // ignore, keep empty
        $companies = [];
    }


// Récupérer la liste des campagnes pour ce customer (liste principale)
$campaigns = [];
try{
    $sql = 'SELECT * FROM campaigns';
    $params = [];

    $sql .= ' ORDER BY created_at DESC LIMIT 500';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $campaigns = $stmt->fetchAll();
}catch(Exception $e){
    $campaigns = [];
}

