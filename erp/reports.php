<?php

include __DIR__ . '/includes/reports.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ERP - Rapports</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .controls{display:flex;gap:8px;align-items:center;margin-bottom:12px}
    .panel{background:#fff;border:1px solid #e6e9ef;padding:12px;border-radius:8px;margin-bottom:12px}
    .table-compact{width:100%;border-collapse:collapse}
    .table-compact th,.table-compact td{border:1px solid #eee;padding:8px;text-align:left}
    .muted{color:#666;font-size:13px}
    .badge {display:inline-block;padding:4px 8px;background:#f0f0f0;border-radius:4px;font-size:12px;margin-left:8px}
  </style>
</head>
<body>
    <?php include 'erp_nav.php'; ?>
    
  <div class="wrapper" style="max-width:1100px;margin:18px auto">
    <h1>Rapports</h1>


    <div class="panel">
      <form method="get" class="controls" style="flex-wrap:wrap">
        <input type="hidden" name="action" value="list">
        <label style="display:flex;gap:6px;align-items:center">
          Période
          <input name="period" placeholder="YYYY-MM" value="<?= htmlspecialchars($period) ?>" pattern="\d{4}-\d{2}">
        </label>
        <button class="btn btn-ghost" type="submit">Filtrer</button>
        <div style="margin-left:auto;display:flex;gap:8px">
          <a class="btn" href="reports.php?action=export_payrolls<?= $period ? '&period=' . urlencode($period) : '' ?>">Exporter fiches (CSV)</a>
          <a class="btn" href="reports.php?action=export_employees">Exporter employés (CSV)</a>
        </div>
      </form>

      <div style="display:flex;gap:12px;align-items:center">
        <div>
          <div class="muted">Nombre de fiches</div>
          <div style="font-weight:700;font-size:18px"><?= (int)($summary['cnt'] ?? 0) ?></div>
        </div>
        <div>
          <div class="muted">Coût total (net + charges employeur)</div>
          <div style="font-weight:700;font-size:18px"><?= number_format((float)($summary['total_cost'] ?? 0), 2, ',', ' ') ?> €</div>
        </div>
        <?php if ($period): ?>
          <div class="muted" style="margin-left:auto">Période affichée: <strong><?= htmlspecialchars($period) ?></strong></div>
        <?php endif; ?>
      </div>
    </div>

    <section class="panel">
      <h2>Fiches de paie</h2>
      <?php if (empty($payrolls)): ?>
        <p class="muted">Aucune fiche trouvée pour la période sélectionnée.</p>
      <?php else: ?>
        <table class="table-compact" aria-label="Liste fiches paie">
          <thead>
            <tr>
              <th>Employé</th>
              <th>Période</th>
              <th>Brut</th>
              <th>Charges salarié</th>
              <th>Charges employeur</th>
              <th>Net</th>
              <th>Créé le</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($payrolls as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['last_name'] . ' ' . $p['first_name']) ?></td>
                <td><?= htmlspecialchars($p['period']) ?></td>
                <td class="right"><?= number_format((float)$p['gross_salary'],2,',',' ') ?> €</td>
                <td class="right"><?= number_format((float)$p['employee_contrib'],2,',',' ') ?> €</td>
                <td class="right"><?= number_format((float)$p['employer_contrib'],2,',',' ') ?> €</td>
                <td class="right"><?= number_format((float)$p['net_pay'],2,',',' ') ?> €</td>
                <td><?= htmlspecialchars($p['created_at'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>

    <section class="panel">
      <h2>Export rapide / Utilitaires</h2>
      <p class="muted">Téléchargez les CSV des employés ou des fiches de paie pour archivage ou import dans un tableur.</p>
      <div style="display:flex;gap:8px">
        <a class="btn btn-primary" href="reports.php?action=export_payrolls<?= $period ? '&period=' . urlencode($period) : '' ?>">Exporter fiches (CSV)</a>
        <a class="btn" href="reports.php?action=export_employees">Exporter employés (CSV)</a>
      </div>
    </section>

  </div>
</body>
</html>
