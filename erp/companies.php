<?php

include __DIR__ . '/includes/companies.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ERP - Sociétés</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
  .wrapper {
    max-width: 1000px;
    margin: 30px auto;
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }

  .form-row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 15px;
  }

  .form-field {
    flex: 1;
    min-width: 200px;
    display: flex;
    flex-direction: column;
  }

  label {
    font-weight: 500;
    color: #555;
    margin-bottom: 6px;
  }

  input, textarea {
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
    background-color: #fafafa;
    transition: border-color 0.2s, background-color 0.2s;
  }

  input:focus, textarea:focus {
    border-color: #007bff;
    background-color: #fff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.1);
  }

  textarea {
    resize: vertical;
    min-height: 100px;
  }

  form button {
    justify-self: start;
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    font-size: 15px;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.1s;
  }

  form button:hover {
    background-color: #0056b3;
  }

  form button:active {
    transform: scale(0.98);
  }

  form a {
    color: #555;
    text-decoration: none;
    margin-left: 10px;
    font-size: 15px;
  }

  form a:hover {
    text-decoration: underline;
  }

  .table-compact {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  .table-compact th, .table-compact td {
    border: 1px solid #eee;
    padding: 10px;
    text-align: left;
  }

  .table-compact th {
    background-color: #f3f4f6;
    font-weight: 600;
  }
  </style>
</head>
<body>
   <?php include 'erp_nav.php'; ?>

  <div class="wrapper" style="max-width:1100px;margin:18px auto">
    <h1>Sociétés</h1>


    <section style="margin-bottom:12px">
      <h2><?= $current ? 'Modifier' : 'Ajouter' ?> une société</h2>
      <form method="post" style="max-width:900px">
        <?php if ($current): ?>
          <input type="hidden" name="id" value="<?= htmlspecialchars((string)$current['id']) ?>">
        <?php endif; ?>
        <div class="form-row">
          <div class="form-field">
            <label>Raison sociale</label>
            <input name="name" required value="<?= htmlspecialchars($current['name'] ?? '') ?>">
          </div>
          <div class="form-field">
            <label>SIRET</label>
            <input name="siret" value="<?= htmlspecialchars($current['siret'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label>NAF</label>
            <input name="naf" value="<?= htmlspecialchars($current['naf'] ?? '') ?>">
          </div>
          <div class="form-field">
            <label>Téléphone</label>
            <input name="phone" value="<?= htmlspecialchars($current['phone'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-field" style="flex:2">
            <label>Adresse</label>
            <input name="address_line1" value="<?= htmlspecialchars($current['address_line1'] ?? '') ?>">
          </div>
          <div class="form-field" style="flex:2">
            <label>Adresse ligne 2</label>
            <input name="address_line2" value="<?= htmlspecialchars($current['address_line2'] ?? '') ?>">
      
        </div>
        <div class="form-row">
          <div class="form-field">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($current['email'] ?? '') ?>">
          </div>
        </div>

        <div style="">
          <label>Notes (interne)</label>
          <textarea name="notes" style="width:100%;min-height:150px;min-width:300px"><?= htmlspecialchars($current['notes'] ?? '') ?></textarea>
        </div>

        <div style="margin-top:10px">
          <button type="submit">Enregistrer</button>
          <?php if ($current): ?><a href="companies.php">Annuler</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="mt-5">
      <h2>Liste des sociétés</h2>
      <div style="margin-bottom:8px">
        <a href="companies.php?action=export" class="btn">Exporter CSV</a>
      </div>

      <table class="table-compact" aria-label="Liste sociétés">
        <thead>
          <tr>
            <th>Nom</th>
            <th>SIRET</th>
            <th>NAF</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($companies as $c): ?>
            <tr>
              <td><?= htmlspecialchars($c['name']) ?></td>
              <td><?= htmlspecialchars($c['siret']) ?></td>
              <td><?= htmlspecialchars($c['naf']) ?></td>
              <td><?= htmlspecialchars($c['phone']) ?></td>
              <td><?= htmlspecialchars($c['email']) ?></td>
              <td>
                <a href="companies.php?action=edit&id=<?= (int)$c['id'] ?>">Modifier</a>
                <form method="post" style="display:inline" onsubmit="return confirm('Supprimer cette société ?')">
                  <input type="hidden" name="_method" value="DELETE">
                  <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                  <button type="submit">Supprimer</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($companies)): ?>
            <tr><td colspan="6" class="muted">Aucune société enregistrée.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</body>
</html>
