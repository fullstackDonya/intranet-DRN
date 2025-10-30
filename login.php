<?php

// Charger la DB puis l'auth
require_once __DIR__ . '/crm/config/database.php';
require_once __DIR__ . '/crm/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email et mot de passe requis.';
    } else {
        if (login($email, $password)) {
            // redirection vers le CRM principal
            header('Location: erp/account.php');
            exit;
        } else {
            $error = 'Identifiants invalides ou compte inactif.';
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Connexion — DRN</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">
  <main class="auth-card" role="main" aria-labelledby="login-title">
    <header class="auth-head">
      <div class="brand-logo" aria-hidden="true">DRN</div>
      <div>
        <h1 id="login-title">Connexion</h1>
        <p class="lead">Accédez au CRM et à l'ERP — espace réservé aux utilisateurs</p>
      </div>
    </header>

    <?php if ($error): ?>
      <div class="alert alert-error" role="alert">
        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <form class="form" method="post" action="" autocomplete="on" novalidate>
      <div class="form-row">
        <label for="email">Email</label>
        <input id="email" class="input" type="email" name="email" required placeholder="votre@adresse.com" autofocus>
      </div>

      <div class="form-row input-with-icon">
        <label for="password">Mot de passe</label>
        <input id="password" class="input" type="password" name="password" required placeholder="••••••••">
        <!-- icône optionnelle -->
        <span class="icon" aria-hidden="true">●●</span>
      </div>

      <div class="actions">
        <button type="submit" class="btn btn-primary">Se connecter</button>
        <a class="btn btn-ghost" href="register.php" role="button">S'enregistrer</a>
      </div>

      <div class="auth-footer" style="margin-top:14px;">
        <small>Vous n'avez pas de compte ? <a href="register.php">Créer un compte</a></small>
        <small><a href="#">Mot de passe oublié ?</a></small>
      </div>
    </form>
  </main>
</body>
</html>
