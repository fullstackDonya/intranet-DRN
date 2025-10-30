<?php
header('Content-Type: application/json; charset=utf-8');
session_start();


require_once __DIR__ . '/../config/database.php';




$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'unauthenticated']);
    exit;
}

// Récupération du customer_id
$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    if (!empty($_SESSION['pending_customer_id'])) {
        $customer_id = intval($_SESSION['pending_customer_id']);
        error_log('api/dashboard-data: using pending_customer_id from session: ' . $customer_id);
    }
}
try {
    // Récupération des paramètres de filtre
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
    $source = isset($_GET['source']) ? $_GET['source'] : null;
    $customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;

    // Construction de la clause WHERE dynamique
    $where_conditions = ["o.created_at BETWEEN :start_date AND :end_date"];
    $params = [
        ':start_date' => $start_date,
        ':end_date' => $end_date
    ];

    if ($source) {
        $where_conditions[] = "o.source = :source";
        $params[':source'] = $source;
    }

    if ($customer_id) {
        $where_conditions[] = "c.customer_id = :customer_id";
        $params[':customer_id'] = $customer_id;
    }

    if ($status) {
        $where_conditions[] = "o.stage = :status";
        $params[':status'] = $status;
    }

    $where_clause = implode(' AND ', $where_conditions);

    // Revenus totaux (uniquement sociétés non internes)
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(o.amount), 0) as total_revenue
        FROM opportunities o
        INNER JOIN companies c ON o.company_id = c.id
        WHERE {$where_clause} AND o.stage = 'closed_won' AND c.interne_customer = 0
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $total_revenue = $stmt->fetchColumn();

    // Clients actifs (uniquement sociétés non internes)
    $client_where = str_replace('o.', 'c.', $where_clause);
    $client_where = str_replace('o.created_at', 'c.created_at', $client_where);
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT c.id) as active_clients
        FROM companies c
        WHERE {$client_where} AND c.status = 'client' AND c.interne_customer = 0
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $active_clients = $stmt->fetchColumn();

    // Taux de conversion
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(CASE WHEN o.stage = 'closed_won' THEN 1 END) as won_deals,
            COUNT(*) as total_deals
        FROM opportunities o
        INNER JOIN companies c ON o.company_id = c.id
        WHERE {$where_clause} AND c.interne_customer = 0
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $conversion_data = $stmt->fetch();

    $conversion_rate = 0;
    if ($conversion_data['total_deals'] > 0) {
        $conversion_rate = round(($conversion_data['won_deals'] / $conversion_data['total_deals']) * 100, 1);
    }

    // Opportunités actives
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as opportunities
        FROM opportunities o
        INNER JOIN companies c ON o.company_id = c.id
        WHERE {$where_clause} AND o.stage NOT IN ('closed_won', 'closed_lost') AND c.interne_customer = 0
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $opportunities = $stmt->fetchColumn();

    // Données de performance par utilisateur
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            CONCAT(u.first_name, ' ', u.last_name) as name,
            COUNT(o.id) as total_opportunities,
            COUNT(CASE WHEN o.stage = 'closed_won' THEN 1 END) as won_opportunities,
            COALESCE(SUM(CASE WHEN o.stage = 'closed_won' THEN o.amount END), 0) as revenue,
            AVG(o.amount) as avg_deal_size
        FROM users u
        LEFT JOIN opportunities o ON u.id = o.assigned_to
        LEFT JOIN companies c ON o.company_id = c.id
        " . ($customer_id ? "AND c.customer_id = :customer_id" : "") . "
        WHERE u.is_active = 1 AND u.role IN ('sales', 'manager')
        GROUP BY u.id
        ORDER BY revenue DESC
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $user_performance = $stmt->fetchAll();

    // Évolution temporelle
    $stmt = $pdo->prepare("
        SELECT 
            DATE(o.created_at) as date,
            COUNT(*) as opportunities_created,
            COUNT(CASE WHEN o.stage = 'closed_won' THEN 1 END) as opportunities_won,
            COALESCE(SUM(CASE WHEN o.stage = 'closed_won' THEN o.amount END), 0) as daily_revenue
        FROM opportunities o
        INNER JOIN companies c ON o.company_id = c.id
        WHERE {$where_clause} AND c.interne_customer = 0
        GROUP BY DATE(o.created_at)
        ORDER BY DATE(o.created_at)
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $timeline_data = $stmt->fetchAll();

    // Répartition par étape du pipeline
    $stmt = $pdo->prepare("
        SELECT 
            ps.name as stage_name,
            ps.color_code,
            COUNT(o.id) as count,
            COALESCE(SUM(o.amount), 0) as total_amount
        FROM pipeline_stages ps
        LEFT JOIN opportunities o ON ps.name = o.stage
        LEFT JOIN companies c ON o.company_id = c.id
        WHERE ps.is_active = 1 " . ($customer_id ? "AND c.customer_id = :customer_id" : "") . " AND c.interne_customer = 0
        GROUP BY ps.id, ps.name, ps.color_code
        ORDER BY ps.order_position
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $pipeline_data = $stmt->fetchAll();

    // Activités récentes filtrées
    $activity_where = str_replace('o.', 'al.', $where_clause);
    $activity_where = str_replace('al.created_at', 'al.created_at', $activity_where);
    $stmt = $pdo->prepare("
        SELECT 
            al.action,
            al.table_name,
            al.created_at,
            CONCAT(u.first_name, ' ', u.last_name) as user_name
        FROM activity_logs al
        LEFT JOIN users u ON al.user_id = u.id
        WHERE {$activity_where}
        ORDER BY al.created_at DESC
        LIMIT 5
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $recent_activities = $stmt->fetchAll();

    
    // Toutes les factures (invoices)
    $sql_invoices = "
        SELECT i.*, f.name AS folder_name, c.name AS company_name
        FROM invoices i
        INNER JOIN folders f ON i.folder_id = f.id
        INNER JOIN companies c ON f.company_id = c.id
        WHERE c.interne_customer = 0
        " . ($customer_id ? "AND c.customer_id = :customer_id" : "") . "
        ORDER BY i.issued_at DESC
    ";
    $stmt = $pdo->prepare($sql_invoices);
    if ($customer_id) {
        $stmt->bindValue(':customer_id', $customer_id);
    }
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Toutes les missions (missions) avec état
    $sql_missions = "
        SELECT m.*, f.name AS folder_name, c.name AS company_name
        FROM missions m
        INNER JOIN folders f ON m.folder_id = f.id
        INNER JOIN companies c ON f.company_id = c.id
        WHERE c.interne_customer = 0
        " . ($customer_id ? "AND c.customer_id = :customer_id" : "") . "
        ORDER BY m.created_at DESC
    ";
    $stmt = $pdo->prepare($sql_missions);
    if ($customer_id) {
        $stmt->bindValue(':customer_id', $customer_id);
    }
    $stmt->execute();
    $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Statistiques sur l'état des missions
     $sql_mission_status = "
        SELECT s.name, COUNT(*) as count
        FROM missions m
        INNER JOIN folders f ON m.folder_id = f.id
        INNER JOIN companies c ON f.company_id = c.id
        INNER JOIN statuses s ON m.status_id = s.id
        WHERE c.interne_customer = 0
        
        " . ($customer_id ? "AND c.customer_id = :customer_id" : "") . "
        GROUP BY s.name
    ";
    $stmt = $pdo->prepare($sql_mission_status);
    if ($customer_id) {
        $stmt->bindValue(':customer_id', $customer_id);
    }
    $stmt->execute();
    $missions_status = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Win/Loss (comptes et revenus)
    $stmt = $pdo->prepare("\n        SELECT \n            o.stage,\n            COUNT(*) AS deal_count,\n            COALESCE(SUM(o.amount), 0) AS revenue\n        FROM opportunities o\n        INNER JOIN companies c ON o.company_id = c.id\n        WHERE {$where_clause} AND o.stage IN ('closed_won','closed_lost') AND c.interne_customer = 0\n        GROUP BY o.stage\n    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $win_loss_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $win_loss = [
        'won' => ['count' => 0, 'revenue' => 0.0],
        'lost' => ['count' => 0, 'revenue' => 0.0]
    ];
    foreach ($win_loss_rows as $row) {
        if ($row['stage'] === 'closed_won') {
            $win_loss['won'] = [
                'count' => (int)$row['deal_count'],
                'revenue' => (float)$row['revenue']
            ];
        } elseif ($row['stage'] === 'closed_lost') {
            $win_loss['lost'] = [
                'count' => (int)$row['deal_count'],
                'revenue' => (float)$row['revenue']
            ];
        }
    }

    // CA par Source (revenue by source)
    $stmt = $pdo->prepare("\n        SELECT \n            COALESCE(NULLIF(o.source, ''), 'Inconnu') AS source,\n            COALESCE(SUM(CASE WHEN o.stage = 'closed_won' THEN o.amount END), 0) AS revenue\n        FROM opportunities o\n        INNER JOIN companies c ON o.company_id = c.id\n        WHERE {$where_clause} AND c.interne_customer = 0\n        GROUP BY COALESCE(NULLIF(o.source, ''), 'Inconnu')\n        ORDER BY revenue DESC\n    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $revenue_by_source_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $revenue_by_source = array_map(function($r) {
        return [
            'source' => $r['source'],
            'revenue' => (float)$r['revenue']
        ];
    }, $revenue_by_source_rows);

    // Âge des opportunités (ouvertes uniquement)
    $stmt = $pdo->prepare("\n        SELECT \n            SUM(CASE WHEN DATEDIFF(CURDATE(), o.created_at) BETWEEN 0 AND 30 THEN 1 ELSE 0 END) AS bucket_0_30,\n            SUM(CASE WHEN DATEDIFF(CURDATE(), o.created_at) BETWEEN 31 AND 60 THEN 1 ELSE 0 END) AS bucket_31_60,\n            SUM(CASE WHEN DATEDIFF(CURDATE(), o.created_at) BETWEEN 61 AND 90 THEN 1 ELSE 0 END) AS bucket_61_90,\n            SUM(CASE WHEN DATEDIFF(CURDATE(), o.created_at) > 90 THEN 1 ELSE 0 END) AS bucket_90_plus\n        FROM opportunities o\n        INNER JOIN companies c ON o.company_id = c.id\n        WHERE {$where_clause} AND o.stage NOT IN ('closed_won','closed_lost') AND c.interne_customer = 0\n    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $age_row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $opportunity_age = [
        '0_30' => (int)($age_row['bucket_0_30'] ?? 0),
        '31_60' => (int)($age_row['bucket_31_60'] ?? 0),
        '61_90' => (int)($age_row['bucket_61_90'] ?? 0),
        '90_plus' => (int)($age_row['bucket_90_plus'] ?? 0)
    ];

    // Top 10 Clients (CA) - sociétés externes
    $stmt = $pdo->prepare("\n        SELECT \n            c.id AS company_id,\n            c.name AS company_name,\n            COUNT(o.id) AS won_deals,\n            COALESCE(SUM(o.amount), 0) AS revenue\n        FROM opportunities o\n        INNER JOIN companies c ON o.company_id = c.id\n        WHERE {$where_clause} AND o.stage = 'closed_won' AND c.interne_customer = 0\n        GROUP BY c.id, c.name\n        ORDER BY revenue DESC\n        LIMIT 10\n    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $top_clients_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $top_clients = array_map(function($r) {
        return [
            'company_id' => (int)$r['company_id'],
            'company_name' => $r['company_name'],
            'won_deals' => (int)$r['won_deals'],
            'revenue' => (float)$r['revenue']
        ];
    }, $top_clients_rows);

    $response = [
        'success' => true,
        'kpis' => [
            'total_revenue' => floatval($total_revenue),
            'active_clients' => intval($active_clients),
            'conversion_rate' => floatval($conversion_rate),
            'opportunities' => intval($opportunities),
            'invoices' => $invoices,
            'missions' => $missions,
            'missions_status' => $missions_status,
        ],
        'user_performance' => array_map(function($user) {
            return [
                'id' => $user['id'],
                'name' => $user['name'],
                'total_opportunities' => intval($user['total_opportunities']),
                'won_opportunities' => intval($user['won_opportunities']),
                'revenue' => floatval($user['revenue']),
                'avg_deal_size' => round(floatval($user['avg_deal_size']), 2),
                'conversion_rate' => $user['total_opportunities'] > 0 ? 
                    round(($user['won_opportunities'] / $user['total_opportunities']) * 100, 1) : 0
            ];
        }, $user_performance),
        'timeline' => array_map(function($day) {
            return [
                'date' => $day['date'],
                'opportunities_created' => intval($day['opportunities_created']),
                'opportunities_won' => intval($day['opportunities_won']),
                'daily_revenue' => floatval($day['daily_revenue'])
            ];
        }, $timeline_data),
        'pipeline' => array_map(function($stage) {
            return [
                'stage_name' => $stage['stage_name'],
                'color_code' => $stage['color_code'],
                'count' => intval($stage['count']),
                'total_amount' => floatval($stage['total_amount'])
            ];
        }, $pipeline_data),
        'recent_activities' => $recent_activities,
        // Nouvelles métriques demandées
        'win_loss' => $win_loss,
        'revenue_by_source' => $revenue_by_source,
        'opportunity_age' => $opportunity_age,
        'top_clients' => $top_clients,
        'filters' => [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'source' => $source,
            'customer_id' => $customer_id,
            'status' => $status
        ],
        'generated_at' => date('Y-m-d H:i:s')
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors du chargement des données du dashboard: ' . $e->getMessage(),
        'kpis' => [
            'total_revenue' => 0,
            'active_clients' => 0,
            'conversion_rate' => 0,
            'opportunities' => 0
        ]
    ]);
}
?>