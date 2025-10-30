<?php


// Gestion de l'authentification
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    global $pdo;
    
    if (!isAuthenticated()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function login($email, $password) {
    global $pdo;
    if (session_status() === PHP_SESSION_NONE) session_start();

    // utiliser la colonne 'password' (aliasée en password_hash) — évite l'erreur si password_hash n'existe pas
    $stmt = $pdo->prepare("SELECT id, password AS password_hash, role, customer_id, is_active FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) return false;

    // compte inactif si colonne is_active présente
    if (isset($user['is_active']) && (int)$user['is_active'] === 0) return false;

    // choix du hash disponible
    $hash = $user['password_hash'] ?? $user['password'] ?? null;
    if (!$hash) return false;

    if (password_verify($password, $hash)) {
        // initialiser la session et stocker les infos essentielles
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_role'] = $user['role'] ?? null;
        $_SESSION['customer_id'] = $user['customer_id'] ?? null;
        $_SESSION['user'] = ['id' => (int)$user['id'], 'email' => $email];

        // renouveller l'id de session
        session_regenerate_id(true);

        // journaliser
        logActivity($user['id'], 'login', 'Connexion utilisateur');

        return true;
    }

    return false;
}

function logout() {
    if (isAuthenticated()) {
        logActivity($_SESSION['user_id'], 'logout', 'Déconnexion utilisateur');
    }
    
    session_destroy();
}



function register($data) {
    global $pdo;
    // Vérif email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
    }

    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // whitelist des rôles (adaptez selon votre schéma)
    $allowedRoles = ['user','admin','manager'];
    $role = $data['role'] ?? 'user';
    if (!in_array($role, $allowedRoles, true)) {
        $role = 'user';
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, phone, role, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$username, $email, $hashedPassword, $phone, $role]);

        return ['success' => true, 'message' => 'Utilisateur créé avec succès'];
    } catch (PDOException $e) {
        error_log('Register error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur lors de la création: ' . $e->getMessage()];
    }
}


function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

function hasPermission($permission) {
    global $pdo;
    
    if (!isAuthenticated()) {
        return false;
    }
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.id
        WHERE up.user_id = ? AND p.name = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $permission]);
    
    return $stmt->fetchColumn() > 0;
}


function logActivity($userId, $action, $description = null, $entityType = null, $entityId = null) {
    global $pdo;

    // helper pour vérifier si une colonne existe
    $columnExists = function($table, $column) use ($pdo) {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        return (bool)$stmt->fetch();
    };

    $table = 'activity_logs';
    $cols = [];
    $vals = [];

    // colonnes minimales
    if ($columnExists($table, 'user_id')) { $cols[] = 'user_id'; $vals[] = $userId; }
    if ($columnExists($table, 'action')) { $cols[] = 'action'; $vals[] = $action; }
    if ($description !== null && $columnExists($table, 'description')) { $cols[] = 'description'; $vals[] = $description; }
    if ($entityType !== null && $columnExists($table, 'entity_type')) { $cols[] = 'entity_type'; $vals[] = $entityType; }
    if ($entityId !== null && $columnExists($table, 'entity_id')) { $cols[] = 'entity_id'; $vals[] = $entityId; }

    // toujours ajouter created_at si présent
    if ($columnExists($table, 'created_at')) {
        $cols[] = 'created_at';
        // use NOW() in SQL instead of binding value
        $useNow = true;
    } else {
        $useNow = false;
    }

    if (empty($cols)) {
        // rien à insérer — loguer et sortir
        error_log('logActivity: aucune colonne valide trouvée dans ' . $table);
        return;
    }

    // construire la requête
    $placeholders = implode(',', array_fill(0, count($vals), '?'));
    $colsSql = implode(',', $cols);

    if ($useNow) {
        // retirer created_at du binding (on utilisera NOW())
        // enlever la dernière colonne (created_at) des placeholders
        $colsSql = implode(',', $cols);
        $sql = "INSERT INTO `$table` ($colsSql) VALUES ($placeholders, NOW())";
    } else {
        $sql = "INSERT INTO `$table` ($colsSql) VALUES ($placeholders)";
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($vals);
    } catch (PDOException $e) {
        error_log('logActivity error: ' . $e->getMessage());
    }
}


function getRecentActivities($limit = 10) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT al.*, u.name as user_name 
        FROM activity_logs al
        JOIN users u ON al.user_id = u.id
        ORDER BY al.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    
    return $stmt->fetchAll();
}
?>
