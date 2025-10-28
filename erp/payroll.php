<?php

include __DIR__ . '/includes/payroll.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ERP - Générateur de fiches de paie</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>.container{max-width:900px;margin:18px auto}</style>
</head>
<body>
    <?php include 'erp_nav.php'; ?>

  <div class="container">
    <h1>Générateur de fiches de paie</h1>

    <form method="post" style="max-width:680px">
      <div class="form-row">
        <div class="form-field">
          <label>Employé</label>
          <select name="employee_id" required>
            <option value="">-- Sélectionner --</option>
            <?php foreach ($employees as $e): ?>
              <option value="<?= (int)$e['id'] ?>"><?= htmlspecialchars($e['last_name'] . ' ' . $e['first_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-field">
          <label>Période (ex: 2025-10)</label>
          <input name="period" placeholder="YYYY-MM" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label>Société (facultatif)</label>
          <select name="company_id">
            <option value="">-- Aucune / Sélectionner --</option>
            <?php foreach ($companies as $c): ?>
              <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <div class="small muted">Liste filtrée par client si applicable.</div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label>Salaire brut (laisser vide pour salaire de base)</label>
          <input type="number" step="0.01" name="gross_salary" placeholder="">
        </div>
        <div class="form-field">
          <label>Primes</label>
          <input type="number" step="0.01" name="bonus" value="0">
        </div>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label>Heures supplémentaires (montant)</label>
          <input type="number" step="0.01" name="overtime" value="0">
        </div>
        <div class="form-field">
          <label>Retenues (ex: titres resto, absences)</label>
          <input type="number" step="0.01" name="deductions" value="0">
        </div>
      </div>

      <div style="margin-top:12px">
        <button class="btn btn-primary" type="submit">Générer le PDF et enregistrer</button>
      </div>
    <p class="small" style="margin-top:8px">Calculs indicatifs — vérifier les taux selon convention collective et bulletin RH final. Ce document doit être validé par le service paie.</p>

    </form>
  </div>
</body>
</html>
