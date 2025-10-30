<?php

// afficher toutes les erreurs 
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

session_start();

require_once __DIR__ . '/../config/database.php';

// Récupérer l'ID du dossier depuis l'URL
$folder_id = isset($_POST['folder_id']) ? intval($_POST['folder_id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
// Récupérer les statuts (pour missions uniquement)
$stmt = $pdo->query("SELECT id, name FROM statuses");
$all_statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Charger propriétés (catalogue)
$properties = [];
try {
    $pstmt = $pdo->query("SELECT id, name, community, building, unit_ref FROM properties ORDER BY name ASC");
    $properties = $pstmt ? $pstmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Throwable $e) {
    $properties = [];
}

// Traitement de l'ajout de mission
$mission_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_mission'])) {
    $type = trim($_POST['type'] ?? 'visite');
    $status_id = intval($_POST['status_id'] ?? 0);

    // Champs communs
    $fields = [
        'folder_id' => $folder_id,
        'type' => $type,
        'status_id' => $status_id,
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Normaliser datetime HTML5 (remplacer 'T' par espace)
    $normalizeDateTime = function($v) {
        $v = trim($v ?? '');
        if ($v === '') return '';
        $v = str_replace('T', ' ', $v);
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $v)) { $v .= ':00'; }
        return $v;
    };

    // Champs selon le type (immobilier Dubaï)
    if ($type === 'visite') {
        $fields['name'] = trim($_POST['name'] ?? '');
        $fields['project'] = trim($_POST['project'] ?? '');
        $fields['product'] = trim($_POST['product'] ?? '');
        $fields['datetime'] = $normalizeDateTime($_POST['datetime'] ?? '');
        $fields['client'] = trim($_POST['client'] ?? '');
        if (!$fields['project'] || !$fields['product'] || !$fields['datetime']) {
            $mission_error = "Champs Visite obligatoires: projet, bien, date/heure.";
        }
    } elseif ($type === 'offre') {
        $fields['name'] = trim($_POST['name'] ?? '');
        $fields['project'] = trim($_POST['project'] ?? '');
        $fields['product'] = trim($_POST['product'] ?? '');
        $fields['prix'] = trim($_POST['prix'] ?? '');
        $fields['description'] = trim($_POST['description'] ?? '');
        if (!$fields['project'] || !$fields['product']) {
            $mission_error = "Champs Offre obligatoires: projet et bien.";
        }
    } elseif ($type === 'vente') {
        $fields['name'] = trim($_POST['name'] ?? '');
        $fields['project'] = trim($_POST['project'] ?? '');
        $fields['product'] = trim($_POST['product'] ?? '');
        $fields['prix'] = trim($_POST['prix'] ?? '');
        $fields['datetime'] = $normalizeDateTime($_POST['datetime'] ?? '');
        $fields['responsible'] = trim($_POST['responsible'] ?? '');
        if (!$fields['project'] || !$fields['product'] || !$fields['prix'] || !$fields['datetime']) {
            $mission_error = "Champs Vente obligatoires: projet, bien, prix, date/heure.";
        }
    }

    // Lien optionnel vers un bien du catalogue
    $propId = isset($_POST['property_id']) && $_POST['property_id'] !== '' ? (int)$_POST['property_id'] : null;
    if ($propId) { $fields['property_id'] = $propId; }

    // Insertion si pas d'erreur
    if (!$mission_error) {
        $columns = array_keys($fields);
        $placeholders = array_fill(0, count($fields), '?');
        $sql = "INSERT INTO missions (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($fields));
        } catch (Throwable $e) {
            // Si la colonne property_id n'existe pas encore, réessayer sans
            if (isset($fields['property_id'])) {
                unset($fields['property_id']);
                $columns = array_keys($fields);
                $placeholders = array_fill(0, count($fields), '?');
                $sql = "INSERT INTO missions (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_values($fields));
            } else {
                throw $e;
            }
        }
        header("Location: folder_view.php?id=" . $folder_id . "&success=1");
        exit;
    }
}



// Charger les infos du dossier
$stmt = $pdo->prepare("SELECT f.*, c.name AS company_name FROM folders f INNER JOIN companies c ON f.company_id = c.id WHERE f.id = ?");
$stmt->execute([$folder_id]);
$folder = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$folder) {
    echo "Dossier introuvable.";
    exit;
}

// Charger les missions liées à ce dossier

$stmt = $pdo->prepare("
    SELECT m.*, s.name AS status_name,
           p.name AS property_name, p.community AS property_community, p.building AS property_building, p.unit_ref AS property_unit_ref
    FROM missions m
    LEFT JOIN statuses s ON m.status_id = s.id
    LEFT JOIN properties p ON p.id = m.property_id
    WHERE m.folder_id = ?
    ORDER BY m.created_at DESC
");
$stmt->execute([$folder_id]);
$missions = $stmt->fetchAll(PDO::FETCH_ASSOC);