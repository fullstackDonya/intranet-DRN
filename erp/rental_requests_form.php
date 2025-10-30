<?php
// Simple demo form to query rental_requests API
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Demo API - Demandes de location</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 24px; color: #1f2937; }
    h1 { font-size: 20px; margin-bottom: 16px; }
    form { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); align-items: end; }
    label { display: flex; flex-direction: column; font-size: 12px; color: #374151; gap: 6px; }
    input, select { padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
    .row { grid-column: 1 / -1; display: flex; gap: 10px; flex-wrap: wrap; }
    button { background: #2563eb; color: white; border: 0; padding: 10px 14px; border-radius: 8px; cursor: pointer; font-weight: 600; }
    button:disabled { opacity: 0.6; cursor: not-allowed; }
    .card { margin-top: 20px; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #fff; }
    .muted { color: #6b7280; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
    th, td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; vertical-align: top; }
    th { background: #f8fafc; }
    .error { color: #b91c1c; background: #fee2e2; border: 1px solid #fecaca; padding: 10px; border-radius: 8px; }
    .success { color: #065f46; background: #d1fae5; border: 1px solid #a7f3d0; padding: 10px; border-radius: 8px; }
    .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; }
    .summary { display: flex; gap: 16px; flex-wrap: wrap; }
    code { background: #f1f5f9; padding: 2px 6px; border-radius: 6px; }
  </style>
</head>
<body>
  <h1>Rechercher des demandes de location</h1>
  <form id="filtersForm">
    <label>
      ID
      <input type="number" name="id" placeholder="ex: 123" />
    </label>
    <label>
      User ID
      <input type="number" name="user_id" placeholder="ex: 5" />
    </label>
    <label>
      Product ID
      <input type="number" name="product_id" placeholder="ex: 42" />
    </label>
    <label>
      Statut
      <select name="status">
        <option value="">— indifférent —</option>
        <option value="pending">pending</option>
        <option value="approved">approved</option>
        <option value="rejected">rejected</option>
        <option value="cancelled">cancelled</option>
      </select>
    </label>

    <label>
      Début ≥ (start_date_from)
      <input type="date" name="start_date_from" />
    </label>
    <label>
      Début ≤ (start_date_to)
      <input type="date" name="start_date_to" />
    </label>
    <label>
      Fin ≥ (end_date_from)
      <input type="date" name="end_date_from" />
    </label>
    <label>
      Fin ≤ (end_date_to)
      <input type="date" name="end_date_to" />
    </label>

    <label>
      Créée ≥ (created_from)
      <input type="date" name="created_from" />
    </label>
    <label>
      Créée ≤ (created_to)
      <input type="date" name="created_to" />
    </label>

    <label>
      Page
      <input type="number" name="page" value="1" min="1" />
    </label>
    <label>
      Par page
      <input type="number" name="per_page" value="25" min="1" max="200" />
    </label>

    <label>
      Tri par
      <select name="sort_by">
        <option value="created_at">created_at</option>
        <option value="id">id</option>
        <option value="user_id">user_id</option>
        <option value="product_id">product_id</option>
        <option value="start_date">start_date</option>
        <option value="end_date">end_date</option>
        <option value="status">status</option>
      </select>
    </label>
    <label>
      Direction
      <select name="sort_dir">
        <option value="desc">desc</option>
        <option value="asc">asc</option>
      </select>
    </label>

    <div class="row">
      <button type="submit" id="submitBtn">Rechercher</button>
      <button type="button" id="resetBtn" style="background:#64748b">Réinitialiser</button>
      <span class="muted">Appel API POST vers <code>erp/api/rental_requests.php</code></span>
    </div>
  </form>

  <div id="feedback" style="margin-top:16px"></div>
  <div id="result" class="card" style="display:none">
    <div class="summary">
      <div><strong>Total:</strong> <span id="total"></span></div>
      <div><strong>Page:</strong> <span id="page"></span></div>
      <div><strong>Par page:</strong> <span id="per_page"></span></div>
    </div>
    <div style="overflow:auto">
      <table id="resultTable"></table>
    </div>
  </div>

  <script>
    const form = document.getElementById('filtersForm');
    const feedback = document.getElementById('feedback');
    const result = document.getElementById('result');
    const table = document.getElementById('resultTable');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');

    function showError(msg) {
      feedback.innerHTML = `<div class="error">${msg}</div>`;
      result.style.display = 'none';
    }

    function showSuccess(msg) {
      feedback.innerHTML = `<div class="success">${msg}</div>`;
    }

    function toRows(data) {
      if (!Array.isArray(data) || data.length === 0) {
        table.innerHTML = '<tr><td>Aucune donnée</td></tr>';
        return;
      }
      const cols = Array.from(
        data.reduce((set, row) => { Object.keys(row).forEach(k => set.add(k)); return set; }, new Set())
      );
      const thead = `<thead><tr>${cols.map(c => `<th>${c}</th>`).join('')}</tr></thead>`;
      const tbody = `<tbody>${data.map(r => `<tr>${cols.map(c => `<td>${r[c] ?? ''}</td>`).join('')}</tr>`).join('')}</tbody>`;
      table.innerHTML = thead + tbody;
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      feedback.innerHTML = '';
      submitBtn.disabled = true;
      try {
        const fd = new FormData(form);
        const res = await fetch('api/rental_requests.php', { method: 'POST', body: fd });
        const text = await res.text();
        let json;
        try { json = JSON.parse(text); } catch (e) { throw new Error('Réponse non JSON: ' + text.slice(0, 300)); }
        if (!res.ok) { throw new Error(json.error || ('Erreur HTTP ' + res.status)); }

        showSuccess('Requête réussie');
        document.getElementById('total').textContent = json.total ?? 0;
        document.getElementById('page').textContent = json.page ?? 1;
        document.getElementById('per_page').textContent = json.per_page ?? 25;
        toRows(json.data || []);
        result.style.display = '';
      } catch (err) {
        showError(err.message || String(err));
      } finally {
        submitBtn.disabled = false;
      }
    });

    resetBtn.addEventListener('click', () => {
      form.reset();
      feedback.innerHTML = '';
      result.style.display = 'none';
      table.innerHTML = '';
    });
  </script>
</body>
</html>
