<?php

session_start();

// Charger la DB puis l'auth
require_once __DIR__ . '/crm/config/database.php';
require_once __DIR__ . '/crm/includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        $error = 'Tous les champs sont requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } elseif ($password !== $password_confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        $result = register([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'role' => 'user'
        ]);

        if ($result['success']) {
            // inscription OK -> rediriger vers connexion
            header('Location: login.php');
            exit;
        } else {
            $error = $result['message'] ?? 'Erreur lors de l\'inscription.';
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Inscription — DRN</title>
  <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">
  <main class="auth-card" role="main" aria-labelledby="register-title">
    <header class="auth-head">
      <div class="brand-logo" aria-hidden="true">DRN</div>
      <div>
        <h1 id="register-title">Créer un compte</h1>
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
        <label for="username">Nom</label>
        <input id="username" class="input" type="text" name="username" required placeholder="Votre nom" value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES); ?>">
      </div>

      <div class="form-row">
        <label for="email">Email</label>
        <input id="email" class="input" type="email" name="email" required placeholder="votre@adresse.com" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>">
      </div>

      <div class="form-row">
        <label for="phone">Téléphone</label>
        <input id="phone" class="input" type="tel" name="phone" required placeholder="0123456789" value="<?php echo htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES); ?>">
      </div>

      <div class="form-row input-with-icon">
        <label for="password">Mot de passe</label>
        <input id="password" class="input" type="password" name="password" required placeholder="••••••••">
        <span class="icon" aria-hidden="true">●●</span>
      </div>

      <div class="form-row input-with-icon">
        <label for="password_confirm">Confirmer le mot de passe</label>
        <input id="password_confirm" class="input" type="password" name="password_confirm" required placeholder="••••••••">
      </div>

      <div class="actions">
        <button type="submit" class="btn btn-primary">S'inscrire</button>
        <a class="btn btn-ghost" href="login.php" role="button">Se connecter</a>
      </div>

      <div class="auth-footer" style="margin-top:14px;">
        <small>Déjà inscrit ? <a href="login.php">Se connecter</a></small>
        <small><a href="#">Conditions & confidentialité</a></small>
      </div>
    </form>
  </main>
</body>
</html>
