<?php
require_once __DIR__ . '/includes/customer_edit.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Éditer: <?php echo h($customer['name']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Éditer le client</h1>
    <div class="btn-group">
      <a href="customer_view.php?id=<?php echo (int)$customer['id']; ?>" class="btn btn-secondary">Annuler</a>
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
      <input type="text" name="name" class="form-control" required value="<?php echo h($customer['name']); ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Email<span class="text-danger">*</span></label>
      <input type="email" name="email" class="form-control" required value="<?php echo h($customer['email']); ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label">Téléphone</label>
      <input type="text" name="phone" class="form-control" value="<?php echo h($customer['phone']); ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Code postal</label>
      <input type="text" name="postal_code" class="form-control" value="<?php echo h($customer['postal_code']); ?>">
    </div>

    <div class="col-12">
      <label class="form-label">Adresse</label>
      <textarea name="address" class="form-control" rows="2"><?php echo h($customer['address']); ?></textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label">Pays</label>
      <input type="text" name="country" class="form-control" value="<?php echo h($customer['country']); ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Rôle</label>
      <select name="role" class="form-select">
        <?php foreach (['client','admin','manager','partner'] as $r): ?>
          <option value="<?php echo $r; ?>" <?php if(($customer['role'] ?? '')===$r) echo 'selected'; ?>><?php echo $r; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Statut</label>
      <select name="status" class="form-select">
        <?php foreach (['active','inactive'] as $s): ?>
          <option value="<?php echo $s; ?>" <?php if(($customer['status'] ?? '')===$s) echo 'selected'; ?>><?php echo $s; ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-12 text-end">
      <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
