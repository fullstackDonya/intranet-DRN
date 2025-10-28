<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'drn'); // Nom de la base de données
define('DB_USER', 'root');
define('DB_PASS', 'root');

// Configuration Power BI
define('POWERBI_CLIENT_ID', 'votre-client-id-azure');
define('POWERBI_CLIENT_SECRET', 'votre-client-secret');
define('POWERBI_TENANT_ID', 'votre-tenant-id');
define('POWERBI_WORKSPACE_ID', 'votre-workspace-id');
define('POWERBI_REPORT_ID', 'votre-report-id');

// URLs Power BI
define('POWERBI_API_URL', 'https://api.powerbi.com/v1.0/myorg/');
define('POWERBI_AUTH_URL', 'https://login.microsoftonline.com/' . POWERBI_TENANT_ID . '/oauth2/v2.0/token');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}
?>
