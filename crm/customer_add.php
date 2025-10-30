<?php
require_once __DIR__ . '/includes/customer_add.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajouter un client</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Nouveau client</h1>
    <div class="btn-group">
      <a href="customer_import.php" class="btn btn-outline-secondary">Importer</a>
      <a href="customer_export.php" class="btn btn-outline-primary">Exporter</a>
    </div>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e): ?>
        <div>- <?php echo htmlspecialchars($e); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Nom<span class="text-danger">*</span></label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Email<span class="text-danger">*</span></label>
      <input type="email" name="email" class="form-control" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">Téléphone</label>
      <input type="text" name="phone" class="form-control">
    </div>
    <div class="col-md-6">
      <label class="form-label">Code postal</label>
      <input type="text" name="postal_code" class="form-control">
    </div>

    <div class="col-12">
      <label class="form-label">Adresse</label>
      <textarea name="address" class="form-control" rows="2"></textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label">Pays</label>
      <input type="text" name="country" class="form-control" value="UAE">
    </div>
    <div class="col-md-3">
      <label class="form-label">Rôle</label>
      <select name="role" class="form-select">
        <option value="client" selected>client</option>
        <option value="admin">admin</option>
        <option value="manager">manager</option>
        <option value="partner">partner</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Statut</label>
      <select name="status" class="form-select">
        <option value="active" selected>active</option>
        <option value="inactive">inactive</option>
      </select>
    </div>

    <div class="col-12 text-end">
      <a href="customers.php" class="btn btn-secondary">Annuler</a>
      <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
