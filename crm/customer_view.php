<?php
require_once __DIR__ . '/includes/customer_view.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client: <?php echo h($customer['name']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Client: <?php echo h($customer['name']); ?></h1>
    <div class="btn-group">
      <a class="btn btn-outline-secondary" href="customer_edit.php?id=<?php echo (int)$customer['id']; ?>">Éditer</a>
      <a class="btn btn-outline-primary" href="customer_export.php">Exporter (CSV)</a>
      <a class="btn btn-secondary" href="customers.php">Retour</a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6"><strong>Email</strong><div><?php echo h($customer['email']); ?></div></div>
        <div class="col-md-6"><strong>Téléphone</strong><div><?php echo h($customer['phone']); ?></div></div>
        <div class="col-md-4"><strong>Pays</strong><div><?php echo h($customer['country']); ?></div></div>
        <div class="col-md-4"><strong>Code postal</strong><div><?php echo h($customer['postal_code']); ?></div></div>
        <div class="col-md-4"><strong>Rôle</strong><div><?php echo h($customer['role']); ?></div></div>
        <div class="col-md-4"><strong>Statut</strong><div><span class="badge bg-<?php echo ($customer['status']==='active'?'success':'secondary'); ?>"><?php echo h($customer['status']); ?></span></div></div>
        <div class="col-12"><strong>Adresse</strong><div><?php echo nl2br(h($customer['address'])); ?></div></div>
        <div class="col-md-6 text-muted"><small>Créé le: <?php echo h($customer['created_at']); ?></small></div>
        <div class="col-md-6 text-muted text-end"><small>Mis à jour: <?php echo h($customer['updated_at']); ?></small></div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
