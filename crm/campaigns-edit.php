<?php
require_once __DIR__ . '/includes/verify_subscriptions.php';
 

$customer_id = $_SESSION['customer_id'] ?? null;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $subject = trim($_POST['subject']);
    $sender_name = trim($_POST['sender_name']);
    $sender_email = trim($_POST['sender_email']);
    $audience = $_POST['audience'];
    $status = $_POST['status'];
    $scheduled_at = !empty($_POST['scheduled_at']) ? $_POST['scheduled_at'] : null;

    // Ensure campaign belongs to customer
    $sql = 'SELECT id FROM campaigns WHERE id = ?'; $params = [$id];
    if($customer_id){ $sql .= ' AND customer_id = ?'; $params[] = $customer_id; }
    $stmt = $pdo->prepare($sql); $stmt->execute($params); $camp = $stmt->fetch();
    if(!$camp){ header('Location: campaigns.php?error=not_found'); exit; }

    $upd = $pdo->prepare('UPDATE campaigns SET name=?,type=?,subject=?,sender_name=?,sender_email=?,audience=?,status=?,scheduled_at=? WHERE id=?');
    $upd->execute([$name,$type,$subject,$sender_name,$sender_email,$audience,$status,$scheduled_at,$id]);

    // optional: send immediately if scheduled_at is in the past or status requires immediate send
    require_once __DIR__ . '/includes/send_campaign.php';
    $s = $pdo->prepare('SELECT * FROM campaigns WHERE id = ?');
    $s->execute([$id]);
    $camp = $s->fetch(PDO::FETCH_ASSOC);
    if ($camp) {
        if ($scheduled_at !== null) {
            $t = strtotime($scheduled_at);
            if ($t !== false && $t <= time()) {
                send_campaign($pdo, $camp);
            }
        } elseif ($status === 'active' || $status === 'sent') {
            send_campaign($pdo, $camp);
        }
    }

    header('Location: campaigns.php?success=updated'); exit;

    header('Location: campaigns.php?success=updated'); exit;
}

// GET: show edit form
if(!isset($_GET['id'])){ header('Location: campaigns.php?error=missing_id'); exit; }
$id = intval($_GET['id']);
$sql = 'SELECT * FROM campaigns WHERE id = ?';
$params = [$id];
if($customer_id){ $sql .= ' AND customer_id = ?';
     $params[] = $customer_id; }
$stmt = $pdo->prepare($sql);
$stmt->execute($params);


  $campaign = $stmt->fetch();
if(!$campaign){ header('Location: campaigns.php?error=not_found'); exit; }

// Récupérer companies pour le select audience
$companies = [];
if($customer_id){
    try{
        $cstmt = $pdo->prepare('SELECT id, name FROM companies WHERE customer_id = ? ORDER BY name ASC');
        $cstmt->execute([$customer_id]);
        $companies = $cstmt->fetchAll();
    }catch(Exception $e){ $companies = []; }
}

// Format scheduled_at pour datetime-local
$scheduled_val = '';
if(!empty($campaign['scheduled_at'])){
    $t = strtotime($campaign['scheduled_at']);
    if($t !== false) $scheduled_val = date('Y-m-d\TH:i', $t);
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
                <h3>Éditer : <?php echo htmlspecialchars($campaign['name']); ?></h3>
                <form method="post">
                    <input type="hidden" name="id" value="<?php echo intval($campaign['id']); ?>">
                    <div class="mb-3">
                        <label>Nom</label>
                        <input class="form-control" name="name" value="<?php echo htmlspecialchars($campaign['name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="newsletter" <?php echo ($campaign['type']==='newsletter')? 'selected':''; ?>>Newsletter</option>
                            <option value="promotional" <?php echo ($campaign['type']==='promotional')? 'selected':''; ?>>Promotionnelle</option>
                            <option value="transactional" <?php echo ($campaign['type']==='transactional')? 'selected':''; ?>>Transactionnelle</option>
                            <option value="welcome" <?php echo ($campaign['type']==='welcome')? 'selected':''; ?>>Bienvenue</option>
                            <option value="automation" <?php echo ($campaign['type']==='automation')? 'selected':''; ?>>Automatisée</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Objet</label>
                        <input class="form-control" name="subject" value="<?php echo htmlspecialchars($campaign['subject']); ?>">
                    </div>
                    <div class="mb-3">
                        <label>Expéditeur</label>
                        <input class="form-control" name="sender_name" value="<?php echo htmlspecialchars($campaign['sender_name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label>Email expéditeur</label>
                        <input class="form-control" name="sender_email" value="<?php echo htmlspecialchars($campaign['sender_email']); ?>">
                    </div>
                    <div class="mb-3">
                        <label>Audience</label>
                        <select name="audience" class="form-control">
                            <option value="all" <?php echo ($campaign['audience']==='all')? 'selected':''; ?>>Tous</option>
                            <option value="subscribers" <?php echo ($campaign['audience']==='subscribers')? 'selected':''; ?>>Abonnés</option>
                            <option value="customers" <?php echo ($campaign['audience']==='customers')? 'selected':''; ?>>Clients</option>
                            <option value="prospects" <?php echo ($campaign['audience']==='prospects')? 'selected':''; ?>>Prospects</option>
                            <option value="custom" <?php echo ($campaign['audience']==='custom')? 'selected':''; ?>>Personnalisée</option>
                            <?php if(!empty($companies)): ?>
                                <optgroup label="Companies">
                                    <?php foreach($companies as $comp): $val = 'company_' . intval($comp['id']); ?>
                                        <option value="<?php echo $val; ?>" <?php echo ($campaign['audience']===$val)? 'selected':''; ?>><?php echo htmlspecialchars($comp['name']); ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Statut</label>
                        <select name="status" class="form-control">
                            <option value="draft" <?php echo ($campaign['status']==='draft')? 'selected':''; ?>>Brouillon</option>
                            <option value="scheduled" <?php echo ($campaign['status']==='scheduled')? 'selected':''; ?>>Programmée</option>
                            <option value="sent" <?php echo ($campaign['status']==='sent')? 'selected':''; ?>>Envoyée</option>
                            <option value="active" <?php echo ($campaign['status']==='active')? 'selected':''; ?>>Active</option>
                            <option value="paused" <?php echo ($campaign['status']==='paused')? 'selected':''; ?>>Suspendue</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Date de programmation</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" value="<?php echo htmlspecialchars($scheduled_val); ?>">
                    </div>
                    <button class="btn btn-primary">Enregistrer</button>
                </form>
            </div> 
        
        </div>
    </div>
</body>
</html>