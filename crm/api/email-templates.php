<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    echo json_encode(['success' => false, 'message' => 'missing customer']);
    exit;
}

function ensureEmailTemplatesTable(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS email_templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        type ENUM('newsletter','promotional','transactional','welcome','automation') NOT NULL DEFAULT 'newsletter',
        subject VARCHAR(255) DEFAULT NULL,
        content_html MEDIUMTEXT NULL,
        content_text MEDIUMTEXT NULL,
        thumbnail_url VARCHAR(500) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_templates_customer (customer_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

function seedDefaultTemplatesIfMissing(PDO $pdo, int $customerId): void {
    // Define three defaults
    $defaults = [
        [
            'name' => 'Séquence de Bienvenue',
            'type' => 'welcome',
            'subject' => 'Bienvenue chez {{company_name}}',
            'content_html' => '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f9fc;padding:24px"><tr><td align="center"><table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background:#ffffff;border-radius:8px;overflow:hidden;font-family:Inter,Arial,sans-serif"><tr><td style="padding:24px 24px 8px"><h2 style="margin:0;color:#111827">Bienvenue, {{contact_first_name}} 👋</h2><p style="margin:8px 0 0;color:#4b5563;font-size:14px">Nous sommes ravis de vous compter parmi nos clients. Voici comment bien démarrer avec {{company_name}}.</p></td></tr><tr><td style="padding:0 24px 16px"><a href="https://example.com/app" style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px">Accéder à votre espace</a></td></tr><tr><td style="padding:0 24px 24px;color:#6b7280;font-size:12px">Si vous avez des questions, répondez directement à cet email — nous sommes là pour vous aider.</td></tr><tr><td style="padding:0"><img src="track-open.php?cid={{campaign_id}}" alt="" width="1" height="1" style="display:block"></td></tr></table></td></tr></table>'
        ],
        [
            'name' => 'Panier Abandonné',
            'type' => 'promotional',
            'subject' => 'Votre panier vous attend encore 🛒',
            'content_html' => '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#fff7ed;padding:24px"><tr><td align="center"><table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background:#ffffff;border-radius:8px;overflow:hidden;font-family:Inter,Arial,sans-serif"><tr><td style="padding:24px 24px 8px"><h2 style="margin:0;color:#111827">Toujours intéressé(e) ?</h2><p style="margin:8px 0 0;color:#4b5563;font-size:14px">Votre sélection vous attend. Profitez de <strong>-10%</strong> avec le code <strong>COMEBACK10</strong>.</p></td></tr><tr><td style="padding:0 24px 16px"><a href="https://example.com/cart" style="display:inline-block;background:#d97706;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px">Finaliser ma commande</a></td></tr><tr><td style="padding:0 24px 24px;color:#6b7280;font-size:12px">Cette offre expire dans 48h. Ne la manquez pas !</td></tr><tr><td><img src="track-open.php?cid={{campaign_id}}" alt="" width="1" height="1" style="display:block"></td></tr></table></td></tr></table>'
        ],
        [
            'name' => 'Anniversaire Client',
            'type' => 'welcome',
            'subject' => 'Joyeux anniversaire 🎉 Un cadeau pour vous',
            'content_html' => '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f0fdf4;padding:24px"><tr><td align="center"><table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background:#ffffff;border-radius:8px;overflow:hidden;font-family:Inter,Arial,sans-serif"><tr><td style="padding:24px 24px 8px"><h2 style="margin:0;color:#111827">Bon anniversaire, {{contact_first_name}} ! 🥳</h2><p style="margin:8px 0 0;color:#4b5563;font-size:14px">Pour fêter ça, profitez de <strong>-15%</strong> sur votre prochaine commande.</p></td></tr><tr><td style="padding:0 24px 16px"><a href="https://example.com/offers" style="display:inline-block;background:#16a34a;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px">Découvrir mon cadeau</a></td></tr><tr><td style="padding:0 24px 24px;color:#6b7280;font-size:12px">Offre valable 7 jours.</td></tr><tr><td><img src="track-open.php?cid={{campaign_id}}" alt="" width="1" height="1" style="display:block"></td></tr></table></td></tr></table>'
        ],
    ];

    foreach ($defaults as $tpl) {
        $check = $pdo->prepare('SELECT id FROM email_templates WHERE customer_id = ? AND name = ? LIMIT 1');
        $check->execute([$customerId, $tpl['name']]);
        if (!$check->fetch()) {
            $ins = $pdo->prepare('INSERT INTO email_templates (customer_id,name,type,subject,content_html) VALUES (?,?,?,?,?)');
            $ins->execute([$customerId, $tpl['name'], $tpl['type'], $tpl['subject'], $tpl['content_html']]);
        }
    }
}

function json_input(): array {
    $ct = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($ct, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
    return $_POST;
}

try {
    ensureEmailTemplatesTable($pdo);

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'list';
        if ($action === 'ensure_defaults') {
            seedDefaultTemplatesIfMissing($pdo, (int)$customer_id);
            echo json_encode(['success' => true]);
            exit;
        }
        if ($action === 'get') {
            $id = intval($_GET['id'] ?? 0);
            if (!$id) { echo json_encode(['success' => false, 'message' => 'missing id']); exit; }
            $stmt = $pdo->prepare('SELECT * FROM email_templates WHERE id = ? AND customer_id = ?');
            $stmt->execute([$id, $customer_id]);
            $tpl = $stmt->fetch();
            echo json_encode(['success' => (bool)$tpl, 'template' => $tpl]);
            exit;
        }
        if ($action === 'get_by_name') {
            $name = trim($_GET['name'] ?? '');
            if ($name === '') { echo json_encode(['success' => false, 'message' => 'missing name']); exit; }
            $stmt = $pdo->prepare('SELECT * FROM email_templates WHERE customer_id = ? AND name = ? LIMIT 1');
            $stmt->execute([$customer_id, $name]);
            $tpl = $stmt->fetch();
            echo json_encode(['success' => (bool)$tpl, 'template' => $tpl]);
            exit;
        }
        // default: list
        $type = $_GET['type'] ?? null;
        if (isset($_GET['seed']) && $_GET['seed'] == '1') {
            seedDefaultTemplatesIfMissing($pdo, (int)$customer_id);
        }
        if ($type) {
            $stmt = $pdo->prepare('SELECT id,name,type,subject,updated_at FROM email_templates WHERE customer_id = ? AND type = ? ORDER BY updated_at DESC');
            $stmt->execute([$customer_id, $type]);
        } else {
            $stmt = $pdo->prepare('SELECT id,name,type,subject,updated_at FROM email_templates WHERE customer_id = ? ORDER BY updated_at DESC');
            $stmt->execute([$customer_id]);
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        echo json_encode(['success' => true, 'templates' => $rows]);
        exit;
    }

    if ($method === 'POST') {
        $data = json_input();
        $id = isset($data['id']) ? intval($data['id']) : 0;
        $name = trim($data['name'] ?? '');
        $type = $data['type'] ?? 'newsletter';
        $subject = trim($data['subject'] ?? '');
        $content_html = $data['content_html'] ?? '';
        $content_text = $data['content_text'] ?? null;

        if ($name === '') { echo json_encode(['success' => false, 'message' => 'name required']); exit; }

        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE email_templates SET name=?, type=?, subject=?, content_html=?, content_text=? WHERE id=? AND customer_id=?');
            $stmt->execute([$name, $type, $subject, $content_html, $content_text, $id, $customer_id]);
            echo json_encode(['success' => true, 'id' => $id, 'message' => 'updated']);
            exit;
        } else {
            $stmt = $pdo->prepare('INSERT INTO email_templates (customer_id,name,type,subject,content_html,content_text) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$customer_id, $name, $type, $subject, $content_html, $content_text]);
            $newId = intval($pdo->lastInsertId());
            echo json_encode(['success' => true, 'id' => $newId, 'message' => 'created']);
            exit;
        }
    }

    if ($method === 'DELETE') {
        parse_str($_SERVER['QUERY_STRING'] ?? '', $q);
        $id = isset($q['id']) ? intval($q['id']) : 0;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'missing id']); exit; }
        $stmt = $pdo->prepare('DELETE FROM email_templates WHERE id = ? AND customer_id = ?');
        $stmt->execute([$id, $customer_id]);
        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'unsupported method']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}