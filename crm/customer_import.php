<?php
require_once __DIR__ . '/includes/customer_import.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Importer des clients (CSV)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <h1 class="h4 mb-3">Importer des clients (CSV)</h1>

  <?php if (!empty($report['errors'])): ?>
    <div class="alert alert-danger">
      <?php foreach ($report['errors'] as $e): ?><div>- <?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($report['errors'])): ?>
    <div class="alert alert-success">
      Import terminé. Insérés: <?php echo (int)$report['inserted']; ?>, Ignorés: <?php echo (int)$report['skipped']; ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-12">
          <label class="form-label">Fichier CSV</label>
          <input type="file" name="csv" class="form-control" accept=".csv" required>
          <div class="form-text">Colonnes attendues: name, email, phone, postal_code, address, country, role, status</div>
        </div>
        <div class="col-12 text-end">
          <a href="customers.php" class="btn btn-secondary">Retour</a>
          <button class="btn btn-primary" type="submit">Importer</button>
        </div>
      </form>
    </div>
  </div>

  <div class="mt-4">
    <h6>Exemple CSV</h6>
    <pre class="bg-light p-3 border">name,email,phone,postal_code,address,country,role,status
John Doe,john@example.com,+971501112233,00000,"Dubai Marina",UAE,client,active
    </pre>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
