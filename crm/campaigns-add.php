<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/verify_subscriptions.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$customer_id = $_SESSION['customer_id'] ?? null;

$page_title = "Ajouter une Campagne - CRM Intelligent";

$error = null;
$old = [
    'campaign_name' => $_POST['campaign_name'] ?? '',
    'campaign_type' => $_POST['campaign_type'] ?? 'newsletter',
    'campaign_subject' => $_POST['campaign_subject'] ?? '',
    'sender_name' => $_POST['sender_name'] ?? 'Mon Entreprise',
    'sender_email' => $_POST['sender_email'] ?? 'noreply@monentreprise.com',
    'audience' => $_POST['audience'] ?? 'all',
    'company_ids' => $_POST['company_ids'] ?? [],
    'custom_emails' => $_POST['custom_emails'] ?? '',
    'status' => $_POST['status'] ?? 'draft',
    'schedule_date' => $_POST['schedule_date'] ?? ''
];

// helper: add column if missing (compatible older MySQL)
$ensureColumn = function(PDO $pdo, string $table, string $column, string $definition) {
    $sql = "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$table, $column]);
    $exists = (int)$stmt->fetchColumn() > 0;
    if (!$exists) {
        $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN {$definition}");
    }
};

// Handle POST: insert campaign
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['campaign_name'] ?? '');
    $type = $_POST['campaign_type'] ?? '';
    $subject = trim($_POST['campaign_subject'] ?? '');
    $senderName = trim($_POST['sender_name'] ?? '');
    $senderEmail = trim($_POST['sender_email'] ?? '');
    $audience = $_POST['audience'] ?? '';
    $company_ids = $_POST['company_ids'] ?? [];
    $custom_emails = trim($_POST['custom_emails'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $scheduled_at = !empty($_POST['schedule_date']) ? $_POST['schedule_date'] : null;

    $old = [
        'campaign_name' => $name,
        'campaign_type' => $type,
        'campaign_subject' => $subject,
        'sender_name' => $senderName,
        'sender_email' => $senderEmail,
        'audience' => $audience,
        'company_ids' => $company_ids,
        'custom_emails' => $custom_emails,
        'status' => $status,
        'schedule_date' => $scheduled_at
    ];

    if($name === ''){
        $error = 'Le nom de la campagne est requis.';
    } else {
        try{
            // Ensure base table exists then ensure required columns
            $pdo->exec("CREATE TABLE IF NOT EXISTS campaigns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                customer_id INT NOT NULL,
                name VARCHAR(255),
                subject VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // add missing columns safely
            try {
                $ensureColumn($pdo, 'campaigns', 'type', "type ENUM('newsletter','promotional','transactional','welcome','automation') NOT NULL DEFAULT 'newsletter'");
                $ensureColumn($pdo, 'campaigns', 'status', "status ENUM('draft','scheduled','sent','active','paused') NOT NULL DEFAULT 'draft'");
                $ensureColumn($pdo, 'campaigns', 'recipients', "recipients INT DEFAULT 0");
                $ensureColumn($pdo, 'campaigns', 'recipients_emails', "recipients_emails TEXT NULL");
                $ensureColumn($pdo, 'campaigns', 'recipients_count', "recipients_count INT DEFAULT 0");
                $ensureColumn($pdo, 'campaigns', 'open_rate', "open_rate FLOAT DEFAULT 0");
                $ensureColumn($pdo, 'campaigns', 'click_rate', "click_rate FLOAT DEFAULT 0");
                $ensureColumn($pdo, 'campaigns', 'audience', "audience VARCHAR(255) NULL");
                $ensureColumn($pdo, 'campaigns', 'scheduled_at', "scheduled_at DATETIME NULL");
                $ensureColumn($pdo, 'campaigns', 'sender_name', "sender_name VARCHAR(150) NULL");
                $ensureColumn($pdo, 'campaigns', 'sender_email', "sender_email VARCHAR(150) NULL");
            } catch (Exception $e) {
                error_log('campaigns schema ensure error: '.$e->getMessage());
            }

            // collect emails helper
            $emails = [];
            $collectEmails = function(string $sql, array $params = []) use ($pdo, &$emails) {
                try {
                    $s = $pdo->prepare($sql);
                    $s->execute($params);
                    $rows = $s->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($rows as $r) {
                        $e = trim(strtolower($r['email'] ?? ''));
                        if ($e && filter_var($e, FILTER_VALIDATE_EMAIL)) $emails[] = $e;
                    }
                } catch (Exception $ex) {
                    // ignore individual query errors
                }
            };

            // Normalize audience legacy values like "company_123"
            if (is_string($audience) && strpos($audience, 'company_') === 0) {
                $cid = intval(substr($audience, 8));
                $audience = 'companies';
                $company_ids = [$cid];
            }

            if ($audience === 'all') {
                $collectEmails("SELECT email FROM contacts WHERE customer_id = ? AND email IS NOT NULL AND email <> ''", [$customer_id]);
                $collectEmails("SELECT email FROM companies WHERE customer_id = ? AND email IS NOT NULL AND email <> ''", [$customer_id]);
            } elseif ($audience === 'subscribers') {
                $collectEmails("SELECT email FROM contacts WHERE customer_id = ? AND (subscribed = 1 OR is_subscriber = 1) AND email IS NOT NULL AND email <> ''", [$customer_id]);
            } elseif ($audience === 'customers') {
                $collectEmails("SELECT c.email FROM contacts c INNER JOIN companies co ON c.company_id = co.id WHERE co.customer_id = ? AND co.status = 'client' AND c.email IS NOT NULL AND c.email <> ''", [$customer_id]);
                $collectEmails("SELECT email FROM companies WHERE customer_id = ? AND status = 'client' AND email IS NOT NULL AND email <> ''", [$customer_id]);
            } elseif ($audience === 'prospects') {
                $collectEmails("SELECT c.email FROM contacts c INNER JOIN companies co ON c.company_id = co.id WHERE co.customer_id = ? AND co.status = 'prospect' AND c.email IS NOT NULL AND c.email <> ''", [$customer_id]);
                $collectEmails("SELECT email FROM companies WHERE customer_id = ? AND status = 'prospect' AND email IS NOT NULL AND email <> ''", [$customer_id]);
            } elseif ($audience === 'custom') {
                if ($custom_emails !== '') {
                    $list = preg_split('/[\r\n,;]+/', $custom_emails);
                    foreach ($list as $e) {
                        $e = trim(strtolower($e));
                        if ($e && filter_var($e, FILTER_VALIDATE_EMAIL)) $emails[] = $e;
                    }
                }
            } elseif ($audience === 'companies') {
                if (!empty($company_ids) && is_array($company_ids)) {
                    foreach ($company_ids as $cid) {
                        $cid = intval($cid);
                        if (!$cid) continue;
                        $collectEmails("SELECT email FROM contacts WHERE company_id = ? AND email IS NOT NULL AND email <> ''", [$cid]);
                        $collectEmails("SELECT email FROM companies WHERE id = ? AND email IS NOT NULL AND email <> ''", [$cid]);
                    }
                } else {
                    $collectEmails("SELECT email FROM companies WHERE customer_id = ? AND email IS NOT NULL AND email <> ''", [$customer_id]);
                    $collectEmails("SELECT c2.email FROM contacts c2 INNER JOIN companies co2 ON c2.company_id = co2.id WHERE co2.customer_id = ? AND c2.email IS NOT NULL AND c2.email <> ''", [$customer_id]);
                }
            } else {
                $collectEmails("SELECT email FROM contacts WHERE customer_id = ? AND email IS NOT NULL AND email <> ''", [$customer_id]);
            }

            // unique + safety limit
            $emails = array_values(array_unique($emails));
            $maxRecipients = 100000;
            if (count($emails) > $maxRecipients) {
                $emails = array_slice($emails, 0, $maxRecipients);
            }

            $recipients_count = count($emails);
            $recipients_emails_json = $recipients_count ? json_encode(array_values($emails), JSON_UNESCAPED_UNICODE) : null;

            // Insert including recipients fields
            $stmt = $pdo->prepare('INSERT INTO campaigns (customer_id,name,subject,type,status,recipients,recipients_emails,recipients_count,open_rate,click_rate,scheduled_at,sender_name,sender_email,audience) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([
                $customer_id ?? 0,
                $name,
                $subject,
                $type,
                $status,
                $recipients_count,
                $recipients_emails_json,
                $recipients_count,
                0.0,
                0.0,
                $scheduled_at,
                $senderName,
                $senderEmail,
                (is_array($company_ids) && count($company_ids)) ? 'companies' : $audience
            ]);

            // optional: send immediately if scheduled_at is in the past or user requested immediate send
            require_once __DIR__ . '/includes/send_campaign.php';
            $lastId = $pdo->lastInsertId();
            if ($scheduled_at !== null) {
                $t = strtotime($scheduled_at);
                if ($t !== false && $t <= time()) {
                    // fetch campaign row and send immediately
                    $s = $pdo->prepare('SELECT * FROM campaigns WHERE id = ?');
                    $s->execute([$lastId]);
                    $camp = $s->fetch(PDO::FETCH_ASSOC);
                    if ($camp) send_campaign($pdo, $camp);
                }
            } elseif ($status === 'active' || $status === 'sent') {
                $s = $pdo->prepare('SELECT * FROM campaigns WHERE id = ?');
                $s->execute([$lastId]);
                $camp = $s->fetch(PDO::FETCH_ASSOC);
                if ($camp) send_campaign($pdo, $camp);
            }

            $return = $_GET['return'] ?? 'campaigns.php';
            header('Location: ' . $return);
            exit;
        }catch(PDOException $e){
            $error = 'Erreur en enregistrant la campagne: ' . $e->getMessage();
        }
    }
}

// Fetch companies for the form multi-select
$companies_for_select = [];
try {
    $cstmt = $pdo->prepare('SELECT id,name FROM companies WHERE customer_id = ? ORDER BY name ASC');
    $cstmt->execute([$customer_id]);
    $companies_for_select = $cstmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // ignore
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-bullhorn text-primary"></i> Nouvelle Campagne
                    </h1>
                    <a href="campaigns.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Informations de la Campagne</h6>
                            </div>
                            <div class="card-body">
                                <?php if(!empty($error)): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php endif; ?>
                                <form id="campaign-form" method="post" action="campaigns-add.php">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="campaign_name" class="form-label">Nom de la campagne *</label>
                                                <input type="text" name="campaign_name" class="form-control" id="campaign_name" required value="<?php echo htmlspecialchars($old['campaign_name']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="campaign_type" class="form-label">Type</label>
                                                <select class="form-control" id="campaign_type" name="campaign_type">
                                                    <?php
                                                    $types = ['newsletter'=>'Newsletter','promotional'=>'Promotionnelle','transactional'=>'Transactionnelle','welcome'=>'Bienvenue','automation'=>'Automatisée'];
                                                    foreach($types as $k=>$v) {
                                                        echo '<option value="'.htmlspecialchars($k).'"'.($old['campaign_type']=== $k ? ' selected':'').'>'.htmlspecialchars($v).'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="campaign_subject" class="form-label">Objet</label>
                                                <input type="text" name="campaign_subject" class="form-control" id="campaign_subject" value="<?php echo htmlspecialchars($old['campaign_subject']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sender_name" class="form-label">Nom de l'expéditeur</label>
                                                <input type="text" name="sender_name" class="form-control" id="sender_name" value="<?php echo htmlspecialchars($old['sender_name']); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sender_email" class="form-label">Email expéditeur</label>
                                        <input type="email" name="sender_email" class="form-control" id="sender_email" value="<?php echo htmlspecialchars($old['sender_email']); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="audience" class="form-label">Audience</label>
                                        <select class="form-control" id="audience" name="audience">
                                            <option value="all" <?php if($old['audience']==='all') echo 'selected'; ?>>Tous les contacts</option>
                                            <option value="subscribers" <?php if($old['audience']==='subscribers') echo 'selected'; ?>>Abonnés newsletter</option>
                                            <option value="customers" <?php if($old['audience']==='customers') echo 'selected'; ?>>Clients</option>
                                            <option value="prospects" <?php if($old['audience']==='prospects') echo 'selected'; ?>>Prospects</option>
                                            <option value="custom" <?php if($old['audience']==='custom') echo 'selected'; ?>>Liste personnalisée</option>
                                            <option value="companies" <?php if($old['audience']==='companies') echo 'selected'; ?>>Sélectionner les clients</option>
                                        </select>
                                    </div>

                                    <div id="company-select-wrapper" style="display:none; margin-bottom:10px;">
                                        <label for="company_ids">Choisir une ou plusieurs sociétés</label>
                                        <select id="company_ids" name="company_ids[]" multiple class="form-control" size="6">
                                            <?php
                                            if(!empty($companies_for_select)){
                                                foreach($companies_for_select as $cp){
                                                    $sel = in_array($cp['id'], array_map('intval', (array)$old['company_ids'])) ? ' selected' : '';
                                                    echo '<option value="'.intval($cp['id']).'"'.$sel.'>'.htmlspecialchars($cp['name']).'</option>';
                                                }
                                            } else {
                                                echo '<option disabled>Aucune société disponible</option>';
                                            }
                                            ?>
                                        </select>
                                        <small class="text-muted">Maintenez Ctrl/Cmd pour sélectionner plusieurs sociétés.</small>
                                    </div>

                                    <div id="custom-emails-wrapper" style="display:none; margin-bottom:10px;">
                                        <label for="custom_emails">Emails personnalisés (séparés par virgule/retour)</label>
                                        <textarea name="custom_emails" id="custom_emails" class="form-control" rows="3" placeholder="email1@exemple.com, email2@exemple.com"><?php echo htmlspecialchars($old['custom_emails']); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Statut</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="draft" <?php if($old['status']==='draft') echo 'selected'; ?>>Brouillon</option>
                                            <option value="scheduled" <?php if($old['status']==='scheduled') echo 'selected'; ?>>Programmée</option>
                                            <option value="sent" <?php if($old['status']==='sent') echo 'selected'; ?>>Envoyée</option>
                                            <option value="active" <?php if($old['status']==='active') echo 'selected'; ?>>Active</option>
                                            <option value="paused" <?php if($old['status']==='paused') echo 'selected'; ?>>Suspendue</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="scheduleDateDiv" style="display:none;">
                                        <label for="schedule_date" class="form-label">Date et heure</label>
                                        <input type="datetime-local" name="schedule_date" class="form-control" id="schedule_date" value="<?php echo htmlspecialchars($old['schedule_date']); ?>">
                                    </div>

                                    <div class="text-end">
                                        <button type="reset" class="btn btn-secondary me-2">Annuler</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer la campagne
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Aide</h6>
                            </div>
                            <div class="card-body">
                                <h6>Conseils pour ajouter une campagne :</h6>
                                <ul class="small">
                                    <li>Le nom de la campagne est obligatoire</li>
                                    <li>Sélectionnez une audience pour cibler vos destinataires</li>
                                    <li>Programmez l'envoi si nécessaire</li>
                                    <li>Renseignez un expéditeur professionnel pour améliorer la délivrabilité</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const schedulingType = document.getElementById('status');
    const scheduleDateDiv = document.getElementById('scheduleDateDiv');
    const audience = document.getElementById('audience');
    const compWrap = document.getElementById('company-select-wrapper');
    const customWrap = document.getElementById('custom-emails-wrapper');

    function updateScheduleUI() {
        scheduleDateDiv.style.display = (schedulingType.value === 'scheduled') ? 'block' : 'none';
    }
    function updateAudienceUI(){
        const v = audience.value;
        compWrap.style.display = (v === 'companies') ? 'block' : 'none';
        customWrap.style.display = (v === 'custom') ? 'block' : 'none';
    }

    schedulingType.addEventListener('change', updateScheduleUI);
    audience.addEventListener('change', updateAudienceUI);

    updateScheduleUI();
    updateAudienceUI();
});
</script>
</body>
</html>
```// filepath: /Applications/MAMP/htdocs/PP/webitech/WEB/crm/campaigns-add.php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/verify_subscriptions.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$customer_id = $_SESSION['customer_id'] ?? null;

$page_title = "Ajouter une Campagne - CRM Intelligent";

$error = null;
$old = [
    'campaign_name' => $_POST['campaign_name'] ?? '',
    'campaign_type' => $_POST['campaign_type'] ?? 'newsletter',
    'campaign_subject' => $_POST['campaign_subject'] ?? '',
    'sender_name' => $_POST['sender_name'] ?? 'Mon Entreprise',
    'sender_email' => $_POST['sender_email'] ?? 'noreply@monentreprise.com',
    'audience' => $_POST['audience'] ?? 'all',
    'company_ids' => $_POST['company_ids'] ?? [],
    'custom_emails' => $_POST['custom_emails'] ?? '',
    'status' => $_POST['status'] ?? 'draft',
    'schedule_date' => $_POST['schedule_date'] ?? ''
];

// helper: add column if missing (compatible older MySQL)
$ensureColumn = function(PDO $pdo, string $table, string $column, string $definition) {
    $sql = "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$table, $column]);
    $exists = (int)$stmt->fetchColumn() > 0;
    if (!$exists) {
        $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN {$definition}");
    }
};

// Handle POST: insert campaign
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['campaign_name'] ?? '');
    $type = $_POST['campaign_type'] ?? '';
    $subject = trim($_POST['campaign_subject'] ?? '');
    $senderName = trim($_POST['sender_name'] ?? '');
    $senderEmail = trim($_POST['sender_email'] ?? '');
    $audience = $_POST['audience'] ?? '';
    $company_ids = $_POST['company_ids'] ?? [];
    $custom_emails = trim($_POST['custom_emails'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $scheduled_at = !empty($_POST['schedule_date']) ? $_POST['schedule_date'] : null;

    $old = [
        'campaign_name' => $name,
        'campaign_type' => $type,
        'campaign_subject' => $subject,
        'sender_name' => $senderName,
        'sender_email' => $senderEmail,
        'audience' => $audience,
        'company_ids' => $company_ids,
        'custom_emails' => $custom_emails,
        'status' => $status,
        'schedule_date' => $scheduled_at
    ];

    if($name === ''){
        $error = 'Le nom de la campagne est requis.';
    } else {
        try{
            // Ensure base table exists then ensure required columns
            $pdo->exec("CREATE TABLE IF NOT EXISTS campaigns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                customer_id INT NOT NULL,
                name VARCHAR(255),
                subject VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // add missing columns safely
            try {
                $ensureColumn($pdo, 'campaigns', 'type', "type ENUM('newsletter','promotional','transactional','welcome','automation') NOT NULL DEFAULT 'newsletter'");
                $ensureColumn($pdo, 'campaigns', 'status', "status ENUM('draft','scheduled','sent','active','paused') NOT NULL DEFAULT 'draft'");
                $ensureColumn($pdo, 'campaigns', 'recipients', "recipients INT DEFAULT 0");
                $ensureColumn($pdo, 'campaigns', 'recipients_emails', "recipients_emails TEXT NULL");
                $ensureColumn($pdo, 'campaigns', 'recipients_count', "recipients_count INT DEFAULT 0");
                $ensureColumn($pdo, 'campaigns', 'open_rate', "open_rate FLOAT DEFAULT 0");
                $ensureColumn($pdo, 'campaigns', 'click_rate', "click_rate FLOAT DEFAULT 0");
                $ensureColumn($pdo, 'campaigns', 'audience', "audience VARCHAR(255) NULL");
                $ensureColumn($pdo, 'campaigns', 'scheduled_at', "scheduled_at DATETIME NULL");
                $ensureColumn($pdo, 'campaigns', 'sender_name', "sender_name VARCHAR(150) NULL");
                $ensureColumn($pdo, 'campaigns', 'sender_email', "sender_email VARCHAR(150) NULL");
            } catch (Exception $e) {
                error_log('campaigns schema ensure error: '.$e->getMessage());
            }

            // collect emails helper
            $emails = [];
            $collectEmails = function(string $sql, array $params = []) use ($pdo, &$emails) {
                try {
                    $s = $pdo->prepare($sql);
                    $s->execute($params);
                    $rows = $s->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($rows as $r) {
                        $e = trim(strtolower($r['email'] ?? ''));
                        if ($e && filter_var($e, FILTER_VALIDATE_EMAIL)) $emails[] = $e;
                    }
                } catch (Exception $ex) {
                    // ignore individual query errors
                }
            };

            // Normalize audience legacy values like "company_123"
            if (is_string($audience) && strpos($audience, 'company_') === 0) {
                $cid = intval(substr($audience, 8));
                $audience = 'companies';
                $company_ids = [$cid];
            }

            if ($audience === 'all') {
                $collectEmails("SELECT email FROM contacts WHERE customer_id = ? AND email IS NOT NULL AND email <> ''", [$customer_id]);
                $collectEmails("SELECT email FROM companies WHERE customer_id = ? AND email IS NOT NULL AND email <> ''", [$customer_id]);
            } elseif ($audience === 'subscribers') {
                $collectEmails("SELECT email FROM contacts WHERE customer_id = ? AND (subscribed = 1 OR is_subscriber = 1) AND email IS NOT NULL AND email <> ''", [$customer_id]);
            } elseif ($audience === 'customers') {
                $collectEmails("SELECT c.email FROM contacts c INNER JOIN companies co ON c.company_id = co.id WHERE co.customer_id = ? AND co.status = 'client' AND c.email IS NOT NULL AND c.email <> ''", [$customer_id]);
                $collectEmails("SELECT email FROM companies WHERE customer_id = ? AND status = 'client' AND email IS NOT NULL AND email <> ''", [$customer_id]);
            } elseif ($audience === 'prospects') {
                $collectEmails("SELECT c.email FROM contacts c INNER JOIN companies co ON c.company_id = co.id WHERE co.customer_id = ? AND co.status = 'prospect' AND c.email IS NOT NULL AND c.email <> ''", [$customer_id]);
                $collectEmails("SELECT email FROM companies WHERE customer_id = ? AND status = 'prospect' AND email IS NOT NULL AND email <> ''", [$customer_id]);
            } elseif ($audience === 'custom') {
                if ($custom_emails !== '') {
                    $list = preg_split('/[\r\n,;]+/', $custom_emails);
                    foreach ($list as $e) {
                        $e = trim(strtolower($e));
                        if ($e && filter_var($e, FILTER_VALIDATE_EMAIL)) $emails[] = $e;
                    }
                }
            } elseif ($audience === 'companies') {
                if (!empty($company_ids) && is_array($company_ids)) {
                    foreach ($company_ids as $cid) {
                        $cid = intval($cid);
                        if (!$cid) continue;
                        $collectEmails("SELECT email FROM contacts WHERE company_id = ? AND email IS NOT NULL AND email <> ''", [$cid]);
                        $collectEmails("SELECT email FROM companies WHERE id = ? AND email IS NOT NULL AND email <> ''", [$cid]);
                    }
                } else {
                    $collectEmails("SELECT email FROM companies WHERE customer_id = ? AND email IS NOT NULL AND email <> ''", [$customer_id]);
                    $collectEmails("SELECT c2.email FROM contacts c2 INNER JOIN companies co2 ON c2.company_id = co2.id WHERE co2.customer_id = ? AND c2.email IS NOT NULL AND c2.email <> ''", [$customer_id]);
                }
            } else {
                $collectEmails("SELECT email FROM contacts WHERE customer_id = ? AND email IS NOT NULL AND email <> ''", [$customer_id]);
            }

            // unique + safety limit
            $emails = array_values(array_unique($emails));
            $maxRecipients = 100000;
            if (count($emails) > $maxRecipients) {
                $emails = array_slice($emails, 0, $maxRecipients);
            }

            $recipients_count = count($emails);
            $recipients_emails_json = $recipients_count ? json_encode(array_values($emails), JSON_UNESCAPED_UNICODE) : null;

            // Insert including recipients fields
            $stmt = $pdo->prepare('INSERT INTO campaigns (customer_id,name,subject,type,status,recipients,recipients_emails,recipients_count,open_rate,click_rate,scheduled_at,sender_name,sender_email,audience) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([
                $customer_id ?? 0,
                $name,
                $subject,
                $type,
                $status,
                $recipients_count,
                $recipients_emails_json,
                $recipients_count,
                0.0,
                0.0,
                $scheduled_at,
                $senderName,
                $senderEmail,
                (is_array($company_ids) && count($company_ids)) ? 'companies' : $audience
            ]);

            $return = $_GET['return'] ?? 'campaigns.php';
            header('Location: ' . $return);
            exit;
        }catch(PDOException $e){
            $error = 'Erreur en enregistrant la campagne: ' . $e->getMessage();
        }
    }
}

// Fetch companies for the form multi-select
$companies_for_select = [];
try {
    $cstmt = $pdo->prepare('SELECT id,name FROM companies WHERE customer_id = ? ORDER BY name ASC');
    $cstmt->execute([$customer_id]);
    $companies_for_select = $cstmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // ignore
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-bullhorn text-primary"></i> Nouvelle Campagne
                    </h1>
                    <a href="campaigns.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Informations de la Campagne</h6>
                            </div>
                            <div class="card-body">
                                <?php if(!empty($error)): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php endif; ?>
                                <form id="campaign-form" method="post" action="campaigns-add.php">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="campaign_name" class="form-label">Nom de la campagne *</label>
                                                <input type="text" name="campaign_name" class="form-control" id="campaign_name" required value="<?php echo htmlspecialchars($old['campaign_name']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="campaign_type" class="form-label">Type</label>
                                                <select class="form-control" id="campaign_type" name="campaign_type">
                                                    <?php
                                                    $types = ['newsletter'=>'Newsletter','promotional'=>'Promotionnelle','transactional'=>'Transactionnelle','welcome'=>'Bienvenue','automation'=>'Automatisée'];
                                                    foreach($types as $k=>$v) {
                                                        echo '<option value="'.htmlspecialchars($k).'"'.($old['campaign_type']=== $k ? ' selected':'').'>'.htmlspecialchars($v).'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="campaign_subject" class="form-label">Objet</label>
                                                <input type="text" name="campaign_subject" class="form-control" id="campaign_subject" value="<?php echo htmlspecialchars($old['campaign_subject']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sender_name" class="form-label">Nom de l'expéditeur</label>
                                                <input type="text" name="sender_name" class="form-control" id="sender_name" value="<?php echo htmlspecialchars($old['sender_name']); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sender_email" class="form-label">Email expéditeur</label>
                                        <input type="email" name="sender_email" class="form-control" id="sender_email" value="<?php echo htmlspecialchars($old['sender_email']); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="audience" class="form-label">Audience</label>
                                        <select class="form-control" id="audience" name="audience">
                                            <option value="all" <?php if($old['audience']==='all') echo 'selected'; ?>>Tous les contacts</option>
                                            <option value="subscribers" <?php if($old['audience']==='subscribers') echo 'selected'; ?>>Abonnés newsletter</option>
                                            <option value="customers" <?php if($old['audience']==='customers') echo 'selected'; ?>>Clients</option>
                                            <option value="prospects" <?php if($old['audience']==='prospects') echo 'selected'; ?>>Prospects</option>
                                            <option value="custom" <?php if($old['audience']==='custom') echo 'selected'; ?>>Liste personnalisée</option>
                                            <option value="companies" <?php if($old['audience']==='companies') echo 'selected'; ?>>Sélectionner des sociétés</option>
                                        </select>
                                    </div>

                                    <div id="company-select-wrapper" style="display:none; margin-bottom:10px;">
                                        <label for="company_ids">Choisir une ou plusieurs sociétés</label>
                                        <select id="company_ids" name="company_ids[]" multiple class="form-control" size="6">
                                            <?php
                                            if(!empty($companies_for_select)){
                                                foreach($companies_for_select as $cp){
                                                    $sel = in_array($cp['id'], array_map('intval', (array)$old['company_ids'])) ? ' selected' : '';
                                                    echo '<option value="'.intval($cp['id']).'"'.$sel.'>'.htmlspecialchars($cp['name']).'</option>';
                                                }
                                            } else {
                                                echo '<option disabled>Aucune société disponible</option>';
                                            }
                                            ?>
                                        </select>
                                        <small class="text-muted">Maintenez Ctrl/Cmd pour sélectionner plusieurs sociétés.</small>
                                    </div>

                                    <div id="custom-emails-wrapper" style="display:none; margin-bottom:10px;">
                                        <label for="custom_emails">Emails personnalisés (séparés par virgule/retour)</label>
                                        <textarea name="custom_emails" id="custom_emails" class="form-control" rows="3" placeholder="email1@exemple.com, email2@exemple.com"><?php echo htmlspecialchars($old['custom_emails']); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Statut</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="draft" <?php if($old['status']==='draft') echo 'selected'; ?>>Brouillon</option>
                                            <option value="scheduled" <?php if($old['status']==='scheduled') echo 'selected'; ?>>Programmée</option>
                                            <option value="sent" <?php if($old['status']==='sent') echo 'selected'; ?>>Envoyée</option>
                                            <option value="active" <?php if($old['status']==='active') echo 'selected'; ?>>Active</option>
                                            <option value="paused" <?php if($old['status']==='paused') echo 'selected'; ?>>Suspendue</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="scheduleDateDiv" style="display:none;">
                                        <label for="schedule_date" class="form-label">Date et heure</label>
                                        <input type="datetime-local" name="schedule_date" class="form-control" id="schedule_date" value="<?php echo htmlspecialchars($old['schedule_date']); ?>">
                                    </div>

                                    <div class="text-end">
                                        <button type="reset" class="btn btn-secondary me-2">Annuler</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer la campagne
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Aide</h6>
                            </div>
                            <div class="card-body">
                                <h6>Conseils pour ajouter une campagne :</h6>
                                <ul class="small text-muted">
                                    <li>Le nom de la campagne est obligatoire</li>
                                    <li>Sélectionnez une audience pour cibler vos destinataires</li>
                                    <li>Programmez l'envoi si nécessaire</li>
                                    <li>Renseignez un expéditeur professionnel pour améliorer la délivrabilité</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const schedulingType = document.getElementById('status');
    const scheduleDateDiv = document.getElementById('scheduleDateDiv');
    const audience = document.getElementById('audience');
    const compWrap = document.getElementById('company-select-wrapper');
    const customWrap = document.getElementById('custom-emails-wrapper');

    function updateScheduleUI() {
        scheduleDateDiv.style.display = (schedulingType.value === 'scheduled') ? 'block' : 'none';
    }
    function updateAudienceUI(){
        const v = audience.value;
        compWrap.style.display = (v === 'companies') ? 'block' : 'none';
        customWrap.style.display = (v === 'custom') ? 'block' : 'none';
    }

    schedulingType.addEventListener('change', updateScheduleUI);
    audience.addEventListener('change', updateAudienceUI);

    updateScheduleUI();
    updateAudienceUI();
});
</script>
</body>
</html>