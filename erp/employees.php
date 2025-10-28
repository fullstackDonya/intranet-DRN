<?php

include __DIR__ . '/includes/employees.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ERP - Employés</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
   <?php include 'erp_nav.php'; ?>

  <div class="wrapper">
    <h1>Employés</h1>


    <section style="margin-bottom:12px">
      <form method="get" style="display:flex;gap:8px;align-items:center">
        <input type="hidden" name="page" value="1">
        <input name="q" placeholder="Recherche nom / département" value="<?= htmlspecialchars($q) ?>">
        <button class="btn btn-ghost">Rechercher</button>
        <a class="btn btn-primary" href="employees.php?action=add">Ajouter un employé</a>
        <a class="btn" href="employees.php?action=export<?= $q ? '&q=' . urlencode($q) : '' ?>">Exporter CSV</a>
      </form>
    </section>

    <section>
      <h2><?= $current ? 'Modifier' : 'Ajouter' ?> un employé</h2>
      <form method="post" style="max-width:760px">
        <?php if ($current): ?>
          <input type="hidden" name="id" value="<?= htmlspecialchars((string)$current['id']) ?>">
        <?php endif; ?>
        <div class="form-row">
          <div class="form-field">
            <label>Prénom</label>
            <input name="first_name" required value="<?= htmlspecialchars($current['first_name'] ?? '') ?>">
          </div>
          <div class="form-field">
            <label>Nom</label>
            <input name="last_name" required value="<?= htmlspecialchars($current['last_name'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($current['email'] ?? '') ?>">
          </div>
          <div class="form-field">
            <label>Date d'embauche</label>
            <input type="date" name="hire_date" value="<?= htmlspecialchars($current['hire_date'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label>Salaire de base (mensuel brut)</label>
            <input type="number" step="0.01" name="base_salary" value="<?= htmlspecialchars($current['base_salary'] ?? '') ?>">
          </div>
          <div class="form-field">
            <label>Poste</label>
            <input name="job_title" value="<?= htmlspecialchars($current['job_title'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label>Département</label>
            <input name="department" value="<?= htmlspecialchars($current['department'] ?? '') ?>">
          </div>
          <div class="form-field">
            <label>Type de contrat</label>
            <select name="contract_type">
              <?php foreach (['CDI','CDD','Freelance','Stage','Alternance'] as $ct): ?>
                <option value="<?= $ct ?>" <?= (($current['contract_type'] ?? '') === $ct) ? 'selected' : '' ?>><?= $ct ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div style="margin-top:8px">
          <label>Statut</label>
          <select name="status">
            <?php foreach (['active' => 'Actif', 'inactive' => 'Inactif'] as $val => $label): ?>
              <option value="<?= $val ?>" <?= (($current['status'] ?? 'active') === $val) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="margin-top:12px">
          <button class="btn btn-primary" type="submit">Enregistrer</button>
          <?php if ($current): ?>
            <a class="btn btn-ghost" href="employees.php">Annuler</a>
          <?php endif; ?>
        </div>
      </form>
    </section>

    <section style="margin-top:18px">
      <h2>Liste des employés</h2>
      <table class="table-compact">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Date d'embauche</th>
            <th>Salaire de base</th>
            <th>Poste</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($employees as $e): ?>
            <tr>
              <td><?= htmlspecialchars($e['last_name'] . ' ' . $e['first_name']) ?></td>
              <td><?= htmlspecialchars($e['email']) ?></td>
              <td><?= htmlspecialchars($e['hire_date']) ?></td>
              <td><?= number_format((float)$e['base_salary'], 2, ',', ' ') ?> €</td>
              <td><?= htmlspecialchars($e['job_title']) ?></td>
              <td>
                <a href="employees.php?action=edit&id=<?= (int)$e['id'] ?>">Modifier</a>
                <form method="post" style="display:inline" onsubmit="return confirm('Supprimer cet employé ?')">
                  <input type="hidden" name="_method" value="DELETE">
                  <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                  <button class="btn btn-danger" type="submit">Supprimer</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($employees)): ?>
            <tr><td colspan="6" class="muted">Aucun employé trouvé.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <?php if (!empty($totalPages) && $totalPages > 1): ?>
        <div style="margin-top:12px">
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a class="btn <?= $p === $page ? 'btn-primary' : 'btn-ghost' ?>" href="employees.php?page=<?= $p ?>&q=<?= urlencode($q) ?>"><?= $p ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </section>
    <footer style="margin-top:18px" class="small">ERP — gestion des employés. Ajoutez validations et permissions selon besoin.</footer>
  </div>
</body>
</html>
