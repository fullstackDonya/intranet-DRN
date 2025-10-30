<?php

include __DIR__ . '/includes/products.php';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Produits - ERP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Table + layout improvements (conserve la structure modal existante) */
    :root{
      --bg:#f6f8fb; --card:#fff; --muted:#6b7280; --accent:#0b74ff;
    }
    body{background:var(--bg);font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial;}
    .container{max-width:1280px;margin:28px auto;padding:0 18px}
    h1{font-size:20px;margin:0 0 14px;color:#111827}
    .toolbar{display:flex;gap:12px;align-items:center;margin-bottom:14px;flex-wrap:wrap}
    .toolbar .left{display:flex;gap:8px;align-items:center;flex:1}
    .toolbar .right{display:flex;gap:8px;align-items:center}

    .search{padding:10px 12px;border-radius:10px;border:1px solid #e6e9ef;min-width:280px;background:#fff}
    .select{padding:8px 10px;border-radius:8px;border:1px solid #e6e9ef;background:#fff}
    .btn{padding:8px 10px;border-radius:10px;border:0;cursor:pointer}
    .btn-primary{background:var(--accent);color:#fff}
    .btn-ghost{background:transparent;border:1px solid #e6e9ef;color:#111827}
    .btn-icon{background:#fff;border:1px solid #e6e9ef;padding:6px;border-radius:8px}

    .card{background:var(--card);border-radius:12px;box-shadow:0 8px 20px rgba(16,24,40,0.04);padding:12px}
    table.products{width:100%;border-collapse:collapse;font-size:14px;table-layout:fixed}
    table.products thead th{padding:12px 10px;text-align:left;color:#374151;font-weight:700;border-bottom:1px solid #eef2f7;cursor:pointer;user-select:none}
    table.products tbody td{padding:12px 10px;border-bottom:1px solid #f3f6fa;vertical-align:middle;color:#111827}
    table.products tbody tr:hover{background:linear-gradient(90deg, rgba(11,116,255,0.03), rgba(11,116,255,0.015))}

    .thumb{width:72px;height:56px;object-fit:cover;border-radius:8px;display:block}
    .col-sku{width:140px;color:var(--muted)}
    .col-name{width:44%}
    .col-qty{text-align:center;width:90px}
    .col-price{text-align:right;width:120px}
    .col-actions{text-align:center;width:140px}

    .actions-group{display:flex;gap:8px;justify-content:center}
    .badge{padding:4px 8px;border-radius:999px;font-size:12px;background:#eef2ff;color:var(--accent)}

    /* sort indicator */
    th.asc::after, th.desc::after{content:'';display:inline-block;margin-left:8px;border:6px solid transparent}
    th.asc::after{border-bottom-color:var(--accent);transform:translateY(-4px)}
    th.desc::after{border-top-color:var(--accent);transform:translateY(4px)}

    @media (max-width:900px){
      .col-sku{display:none}
      .col-name{width:60%}
      .thumb{width:56px;height:44px}
    }

    /* preserve modal styles from original */
    .modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);z-index:60}
    .modal.show{display:flex}
    .modal .box{background:#fff;padding:16px;border-radius:8px;width:420px;max-width:95%;box-shadow:0 8px 24px rgba(0,0,0,0.2)}
    .form-row{display:flex;flex-direction:column;margin-bottom:8px}
    .form-row label{font-weight:600;margin-bottom:6px}
    .form-row input,.form-row textarea{padding:8px;border:1px solid #e6e9ef;border-radius:6px}
    .form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:10px}
    #imagePreview{width:100%;max-height:180px;object-fit:contain;margin-bottom:8px;display:none}
  </style>
</head>
<body>
  <?php include 'erp_nav.php'; ?>
  <div class="container">
    <h1>Produits</h1>

    <div class="toolbar">
      <div class="left">
        <button id="btnAdd" class="btn btn-primary">+ Ajouter</button>
        <input id="searchInput" class="search" type="search" placeholder="Rechercher nom, sku...">
        <select id="filterRental" class="select">
          <option value="">Tous</option>
          <option value="1">Location</option>
          <option value="0">Vente</option>
        </select>
      </div>
      <div class="right">
        <label for="pageSize"><small>Taille</small>
          <select id="pageSize" class="select">
            <option value="10">10</option>
            <option value="25" selected>25</option>
            <option value="50">50</option>
          </select>
        </label>
        <button id="btnReload" class="btn btn-ghost">Rafraîchir</button>
      </div>
    </div>

    <div class="card" role="region" aria-labelledby="products-title">
      <table class="products" aria-describedby="products-list">
        <thead>
          <tr>
            <th data-key="id">ID</th>
            <th data-key="image">Image</th>
            <th class="col-sku" data-key="sku">SKU</th>
            <th class="col-name" data-key="name">Nom</th>
            <th class="col-qty" data-key="quantity">Quantité</th>
            <th class="col-price" data-key="sale_price">Prix</th>
            <th class="col-actions">Actions</th>
          </tr>
        </thead>
        <tbody id="stockTableBody">
          <?php foreach ($products as $p): ?>
            <tr data-id="<?= $p['id'] ?>"
                data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
                data-sku="<?= htmlspecialchars(strtolower($p['sku'] ?? '')) ?>"
                data-rental="<?= (!empty($p['is_rental']) ? '1' : '0') ?>">
              <td><?= $p['id'] ?></td>
              <td>
                <?php $img = $p['image'] ?? ''; ?>
                <img class="thumb" src="<?= htmlspecialchars($img ? '../assets/uploads/products/'.$img : '../assets/img/placeholder.png') ?>" alt="">
              </td>
              <td class="col-sku"><?= htmlspecialchars($p['sku'] ?? '') ?></td>
              <td class="col-name"><?= htmlspecialchars($p['name']) ?></td>
              <td class="col-qty"><?= (int)($p['quantity'] ?? 0) ?></td>
              <td class="col-price"><?= number_format((float)($p['sale_price'] ?? 0), 2, ',', ' ') ?> €</td>
              <td class="col-actions">
                <div class="actions-group">
                  <button class="btn btn-icon btn-edit" title="Modifier">✎</button>
                  <button class="btn btn-icon btn-delete" title="Supprimer">🗑</button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Modal form (même structure que l'original) -->
    <div class="modal" id="stockModal" aria-hidden="true">
      <div class="box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <h3 id="modalTitle">Ajouter / Modifier un produit</h3>
        <form id="stockForm" enctype="multipart/form-data" novalidate>
          <input type="hidden" id="stockId" name="id" value="">
          <img id="imagePreview" src="" alt="aperçu image">
          <div class="form-row">
            <label for="inputImage">Image produit</label>
            <input id="inputImage" name="image" type="file" accept="image/*">
          </div>
          <div class="form-row">
            <label for="inputSku">SKU</label>
            <input id="inputSku" name="sku">
          </div>
          <div class="form-row">
            <label for="inputName">Nom</label>
            <input id="inputName" name="name" required>
          </div>
          <div class="form-row">
            <label for="inputQty">Quantité</label>
            <input id="inputQty" name="quantity" type="number" min="0" required>
          </div>
          <div class="form-row">
            <label for="inputPrice">Prix de vente (€)</label>
            <input id="inputPrice" name="sale_price" type="number" step="0.01" min="0" required>
          </div>
          <div class="form-row">
            <label for="inputRentalRate">Tarif location / jour (€)</label>
            <input id="inputRentalRate" name="rental_rate_per_day" type="number" step="0.01" min="0">
          </div>
          <div class="form-row">
            <label for="inputIsRental"><input id="inputIsRental" name="is_rental" type="checkbox" value="1"> Disponible à la location</label>
          </div>
          <div class="form-actions">
            <button type="button" id="btnDeleteModal" class="btn btn-danger" style="display:none">Supprimer</button>
            <button type="button" id="btnCancel" class="btn btn-ghost">Annuler</button>
            <button type="submit" id="btnSave" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>

  </div>

  <script src="assets/js/products.js"></script>
  <script>
    // client helpers: search, filter, sort (keeps existing modal + products.js behavior)
    document.addEventListener('DOMContentLoaded', function(){
      const search = document.getElementById('searchInput');
      const filterRental = document.getElementById('filterRental');
      const pageSize = document.getElementById('pageSize');
      const btnReload = document.getElementById('btnReload');
      const table = document.querySelector('table.products');
      const tbody = document.getElementById('stockTableBody');

      function applyFilters(){
        const q = (search.value || '').trim().toLowerCase();
        const rental = filterRental.value;
        Array.from(tbody.children).forEach(tr => {
          const name = tr.dataset.name || '';
          const sku  = tr.dataset.sku || '';
          const isRental = tr.dataset.rental || '0';
          const matchQ = !q || name.includes(q) || sku.includes(q);
          const matchRental = rental === '' || rental === isRental;
          tr.style.display = (matchQ && matchRental) ? '' : 'none';
        });
      }

      // simple sort
      Array.from(table.querySelectorAll('thead th[data-key]')).forEach(th => {
        th.addEventListener('click', function(){
          const key = th.dataset.key;
          const asc = !th.classList.contains('asc');
          table.querySelectorAll('thead th').forEach(h=>h.classList.remove('asc','desc'));
          th.classList.add(asc ? 'asc' : 'desc');
          const rows = Array.from(tbody.querySelectorAll('tr')).filter(r=>r.style.display!=='none');
          rows.sort((a,b)=>{
            let va = a.dataset[key] ?? (a.querySelector('.' + ( 'col-' + key))?.textContent?.trim() ?? '') ;
            let vb = b.dataset[key] ?? (b.querySelector('.' + ( 'col-' + key))?.textContent?.trim() ?? '') ;
            // try numeric
            const na = parseFloat(va.replace(/[^0-9\.\-]+/g,'')) || NaN;
            const nb = parseFloat(vb.replace(/[^0-9\.\-]+/g,'')) || NaN;
            if (!isNaN(na) && !isNaN(nb)) return asc ? na-nb : nb-na;
            return asc ? va.localeCompare(vb) : vb.localeCompare(va);
          });
          rows.forEach(r=>tbody.appendChild(r));
        });
      });

      search.addEventListener('input', applyFilters);
      filterRental.addEventListener('change', applyFilters);
      btnReload.addEventListener('click', ()=> window.location.reload());

      // initial apply
      applyFilters();
    });
  </script>
</body>
</html>
