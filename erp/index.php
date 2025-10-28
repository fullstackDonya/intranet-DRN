<?php

include __DIR__ . '/includes/dashboard.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ERP - Tableau de bord</title>
  <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
  
  <?php include 'erp_nav.php'; ?>
<main class="main-content">
  <section class="cards" aria-label="Statistiques">
    <h1>ERP - Tableau de bord</h1>
    <div class="card">
      <h3>Total employés</h3>
      <div class="value"><?= htmlspecialchars((string)$totalEmployees) ?></div>
      <div style="color:#6b7280;font-size:13px;margin-top:6px">Actifs : <?= htmlspecialchars((string)$activeEmployees) ?></div>
    </div>

    <div class="card">
      <h3>Salaire moyen</h3>
      <div class="value"><?= number_format((float)$avgSalary, 2, ',', ' ') ?> €</div>
      <div style="color:#6b7280;font-size:13px;margin-top:6px">Période courante : <?= htmlspecialchars($currentPeriod) ?></div>
    </div>

    <div class="card">
      <h3>Fiches paie (<?= htmlspecialchars($currentPeriod) ?>)</h3>
      <div class="value"><?= htmlspecialchars((string)$payrollsThisMonth) ?></div>
      <div style="color:#6b7280;font-size:13px;margin-top:6px">Coût total : <?= number_format((float)$totalPayrollCost, 2, ',', ' ') ?> €</div>
    </div>

    <div class="card">
      <h3>Actions rapides</h3>
      <div style="margin-top:8px">
        <a href="employees.php">Ajouter / Gérer employés</a><br>
        <a href="payroll.php">Générer fiche de paie</a><br>
        <a href="reports.php">Exporter rapports</a>
        <form class="small-form" method="post" onsubmit="return confirm('Générer automatiquement les fiches de paie pour la période courante pour tous les employés actifs ?')">
          <input type="hidden" name="action" value="auto_generate">
          <button class="btn btn-primary" type="submit" style="margin-top:8px;padding:6px 10px">Générer paies automatiques</button>
        </form>
      </div>
    </div>
  </section>

  <section class="section-flex" style="margin-top:18px">
    <div style="flex:1">
      <h2>Nouvelles embauches (30 derniers jours)</h2>
      <?php if (count($recentHires) === 0): ?>
        <p>Aucune embauche récente.</p>
      <?php else: ?>
        <table class="table-compact">
          <thead><tr><th>Employé</th><th>Poste</th><th>Date d'embauche</th></tr></thead>
          <tbody>
            <?php foreach ($recentHires as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['last_name'] . ' ' . $r['first_name']) ?></td>
                <td><?= htmlspecialchars($r['job_title']) ?></td>
                <td><?= htmlspecialchars($r['hire_date']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <div style="width:420px">
      <h2>Dernières fiches de paie</h2>
      <?php if (count($recentPayrolls) === 0): ?>
        <p>Aucune fiche générée.</p>
      <?php else: ?>
        <table class="table-compact">
          <thead><tr><th>Employé</th><th>Période</th><th>Net</th></tr></thead>
          <tbody>
            <?php foreach ($recentPayrolls as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['last_name'] . ' ' . $p['first_name']) ?></td>
                <td><?= htmlspecialchars($p['period']) ?></td>
                <td><?= number_format((float)$p['net_pay'], 2, ',', ' ') ?> €</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </section>

  <footer style="margin-top:24px;color:#6b7280;font-size:13px">
    <small>ERP minimal — ajoutez rapports, exports CSV, permissions et API selon vos besoins.</small>
  </footer>
</main>
</body>
</html>
