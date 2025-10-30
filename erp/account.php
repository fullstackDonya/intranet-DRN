<?php

if (session_status() === PHP_SESSION_NONE) session_start();

// accès réservé
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// charge la liste des produits pour le select
require_once __DIR__ . '/includes/products.php';
include_once __DIR__ . '/../crm/includes/auth.php';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Mon espace — Locations & Paiements</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    :root{--card:#fff;--muted:#6b7280;--accent:#0b74ff;}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial; background:#f6f8fb;margin:0}
    .container{max-width:1100px;margin:24px auto;padding:18px}
    .header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
    h1{margin:0;font-size:20px}
    .tabs{display:flex;gap:8px;margin-bottom:12px}
    .tab{padding:8px 12px;border-radius:8px;background:#fff;border:1px solid #e6e9ef;cursor:pointer}
    .tab.active{background:linear-gradient(90deg,#0b74ff,#0066d6);color:#fff;border-color:transparent}
    .grid{display:grid;grid-template-columns:1fr 420px;gap:12px}
    .card{background:var(--card);padding:14px;border-radius:10px;box-shadow:0 8px 20px rgba(0,0,0,0.04)}
    table{width:100%;border-collapse:collapse}
    th,td{padding:8px;border-bottom:1px solid #eee;text-align:left;font-size:14px}
    .thumb{width:64px;height:48px;object-fit:cover;border-radius:6px}
    .status-badge{padding:4px 8px;border-radius:999px;font-size:12px}
    .status-pending{background:#fff4e5;color:#a16207}
    .status-approved{background:#ecfdf5;color:#065f46}
    .status-rejected{background:#fff1f2;color:#9f1239}
    .muted{color:var(--muted);font-size:13px}
    .actions{display:flex;gap:8px}
    .btn{padding:8px 10px;border-radius:8px;border:0;cursor:pointer}
    .btn-primary{background:var(--accent);color:#fff}
    .btn-ghost{background:transparent;border:1px solid #e6e9ef}
    .small{font-size:13px;padding:6px 8px}
    .empty{color:#666;padding:12px;text-align:center}
    @media (max-width:980px){ .grid{grid-template-columns:1fr} }
  </style>
</head>
<body>
<?php include 'erp_nav.php'; ?>

<div class="container" id="app">
  <div class="header">
    <h1>Mon espace — Demandes & Paiements</h1>
    <div>
      <span class="muted">Connecté en tant que</span>
      <strong><?= htmlspecialchars($_SESSION['user']['email'] ?? ($_SESSION['user_id'] ?? '')) ?></strong>
    </div>
  </div>

  <div class="tabs" role="tablist">
    <button class="tab active" data-tab="requests">Mes demandes</button>
    <button class="tab" data-tab="payments">Mes loyers</button>
    <button class="tab" data-tab="new">Nouvelle demande</button>
  </div>

  <div id="tab-requests" class="tab-panel">
    <div class="card">
      <h3 style="margin-top:0">Mes demandes</h3>
      <div style="margin-bottom:10px;display:flex;gap:8px;align-items:center">
        <input id="searchRequests" placeholder="Rechercher produit / période..." style="flex:1;padding:8px;border:1px solid #e6e9ef;border-radius:8px">
        <button id="btnRefreshRequests" class="btn btn-ghost small">Rafraîchir</button>
      </div>
      <table id="requestsTable" aria-live="polite">
        <thead>
          <tr><th>#</th><th>Produit</th><th>Période</th><th>Qté</th><th>Statut</th><th></th></tr>
        </thead>
        <tbody></tbody>
      </table>
      <div id="requestsEmpty" class="empty" style="display:none">Aucune demande.</div>
    </div>
  </div>

  <div id="tab-payments" class="tab-panel" style="display:none">
    <div class="card">
      <h3 style="margin-top:0">Loyers & Paiements</h3>
      <p class="muted">Suivez vos loyers à payer et accédez aux quittances.</p>
      <div style="display:flex;gap:10px;margin-bottom:10px">
        <select id="filterPaymentStatus" style="padding:8px;border-radius:8px;border:1px solid #e6e9ef">
          <option value="">Tous</option>
          <option value="pending">À payer</option>
          <option value="paid">Payés</option>
        </select>
        <button id="btnRefreshPayments" class="btn btn-ghost small">Rafraîchir</button>
      </div>

      <table id="paymentsTable">
        <thead><tr><th>#</th><th>Produit</th><th>Période</th><th>Montant</th><th>Statut</th><th></th></tr></thead>
        <tbody></tbody>
      </table>
      <div id="paymentsEmpty" class="empty" style="display:none">Aucun paiement.</div>
    </div>
  </div>

  <div id="tab-new" class="tab-panel" style="display:none">
    <div class="card">
      <h3 style="margin-top:0">Nouvelle demande de location</h3>
      <form id="rentalForm">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
          <div>
            <label for="productSelect">Produit</label><br>
            <select id="productSelect" name="product_id" required style="width:100%;padding:8px;border:1px solid #e6e9ef;border-radius:6px">
              <option value="">— choisir —</option>
              <?php foreach($products as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> <?= $p['sku'] ? '('.htmlspecialchars($p['sku']).')' : '' ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="qty">Quantité</label><br>
            <input id="qty" name="quantity" type="number" min="1" value="1" required style="width:100%;padding:8px;border:1px solid #e6e9ef;border-radius:6px">
          </div>
          <div>
            <label for="startDate">Début</label><br>
            <input id="startDate" name="start_date" type="date" required style="width:100%;padding:8px;border:1px solid #e6e9ef;border-radius:6px">
          </div>
          <div>
            <label for="endDate">Fin</label><br>
            <input id="endDate" name="end_date" type="date" style="width:100%;padding:8px;border:1px solid #e6e9ef;border-radius:6px">
          </div>
        </div>
        <div style="margin-top:8px">
          <label for="message">Message / précision</label><br>
          <textarea id="message" name="message" rows="3" style="width:100%;padding:8px;border:1px solid #e6e9ef;border-radius:6px"></textarea>
        </div>
        <div style="text-align:right;margin-top:10px">
          <button type="submit" class="btn btn-primary">Envoyer la demande</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="assets/js/account.js"></script>
<script>
/* Client logic: appelle includes/account.php et includes/payments.php (adapter si besoin) */
(function(){
  const tabs = document.querySelectorAll('.tab');
  const panels = { requests: document.getElementById('tab-requests'), payments: document.getElementById('tab-payments'), new: document.getElementById('tab-new') };
  tabs.forEach(t => t.addEventListener('click', () => {
    tabs.forEach(x=>x.classList.remove('active'));
    t.classList.add('active');
    Object.values(panels).forEach(p=>p.style.display='none');
    panels[t.dataset.tab].style.display = '';
  }));

  // Requests
  const requestsTbody = document.querySelector('#requestsTable tbody');
  const requestsEmpty = document.getElementById('requestsEmpty');
  const searchRequests = document.getElementById('searchRequests');
  const btnRefreshRequests = document.getElementById('btnRefreshRequests');

  async function fetchRequests(){
    try {
      const res = await fetch('includes/account.php?action=fetch');
      if (!res.ok) throw new Error('Erreur réseau');
      const data = await res.json();
      renderRequests(data || []);
    } catch(e) {
      requestsTbody.innerHTML = '';
      requestsEmpty.style.display = '';
      requestsEmpty.textContent = 'Erreur chargement';
    }
  }

  function renderRequests(rows){
    requestsTbody.innerHTML = '';
    if (!rows.length) { requestsEmpty.style.display = ''; return; }
    requestsEmpty.style.display = 'none';
    rows.forEach(r => {
      const tr = document.createElement('tr');
      const productLabel = r.product_name ? `${escapeHtml(r.product_name)} ${r.sku ? '('+escapeHtml(r.sku)+')' : ''}` : '—';
      const period = r.start_date + (r.end_date ? ' → '+r.end_date : '');
      const statusClass = r.status === 'approved' ? 'status-approved' : (r.status === 'rejected' ? 'status-rejected' : 'status-pending');
      tr.innerHTML = `<td>${r.id}</td>
        <td>${productLabel}</td>
        <td>${escapeHtml(period)}</td>
        <td>${r.quantity}</td>
        <td><span class="status-badge ${statusClass}">${escapeHtml(r.status)}</span></td>
        <td class="actions">
          ${r.status === 'pending' ? `<button class="small btn btn-ghost" data-action="cancel" data-id="${r.id}">Annuler</button>` : `<button class="small btn btn-ghost" data-action="view" data-id="${r.id}">Détails</button>`}
        </td>`;
      requestsTbody.appendChild(tr);
    });
  }

  requestsTbody.addEventListener('click', async (e)=>{
    const btn = e.target.closest('button');
    if(!btn) return;
    const action = btn.dataset.action;
    const id = btn.dataset.id;
    if(action === 'cancel'){
      if(!confirm('Annuler cette demande ?')) return;
      try {
        const res = await fetch('includes/account.php?action=cancel', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: 'id='+encodeURIComponent(id) });
        const j = await res.json();
        if (j.ok) { alert('Demande annulée'); fetchRequests(); } else alert(j.error || 'Erreur');
      } catch(e){ alert('Erreur réseau'); }
    } else if(action === 'view'){
      alert('Détails :\\n' + JSON.stringify({ id }, null, 2));
    }
  });

  searchRequests.addEventListener('input', ()=>{
    const q = (searchRequests.value||'').toLowerCase();
    Array.from(requestsTbody.querySelectorAll('tr')).forEach(tr=>{
      const text = tr.textContent.toLowerCase();
      tr.style.display = text.includes(q) ? '' : 'none';
    });
  });

  btnRefreshRequests.addEventListener('click', fetchRequests);

  // Payments
  const paymentsTbody = document.querySelector('#paymentsTable tbody');
  const paymentsEmpty = document.getElementById('paymentsEmpty');
  const filterPaymentStatus = document.getElementById('filterPaymentStatus');
  const btnRefreshPayments = document.getElementById('btnRefreshPayments');

  async function fetchPayments(){
    try {
      const res = await fetch('includes/payments.php?action=fetch');
      if (!res.ok) throw new Error('Erreur réseau');
      const data = await res.json();
      renderPayments(data || []);
    } catch(e){
      paymentsTbody.innerHTML = '';
      paymentsEmpty.style.display = '';
      paymentsEmpty.textContent = 'Erreur chargement';
    }
  }

  function renderPayments(rows){
    paymentsTbody.innerHTML = '';
    if (!rows.length) { paymentsEmpty.style.display = ''; return; }
    paymentsEmpty.style.display = 'none';
    const filter = filterPaymentStatus.value;
    rows.filter(r => !filter || r.status === filter).forEach(r=>{
      const productLabel = r.product_name ? `${escapeHtml(r.product_name)} ${r.sku ? '('+escapeHtml(r.sku)+')' : ''}` : '—';
      const period = r.period || (r.start_date ? r.start_date + (r.end_date ? ' → ' + r.end_date : '') : '—');
      const statusLabel = r.status === 'paid' ? '<span class="status-badge status-approved">Payé</span>' : '<span class="status-badge status-pending">À payer</span>';
      const amount = typeof r.amount !== 'undefined' ? (Number(r.amount).toFixed(2)+' €') : (r.total ? (Number(r.total).toFixed(2)+' €') : '—');
      const receiptBtn = r.receipt_url ? `<button class="small btn btn-ghost" data-receipt="${encodeURI(r.receipt_url)}">Quittance</button>` : '';
      const payBtn = r.status !== 'paid' ? `<button class="small btn btn-primary" data-pay="${r.id}">Payer</button>` : '';
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${r.id}</td>
                      <td>${productLabel}</td>
                      <td>${escapeHtml(period)}</td>
                      <td style="text-align:right">${amount}</td>
                      <td>${statusLabel}</td>
                      <td class="actions">${payBtn} ${receiptBtn}</td>`;
      paymentsTbody.appendChild(tr);
    });
  }

  paymentsTbody.addEventListener('click', async (e)=>{
    const payBtn = e.target.closest('button[data-pay]');
    if (payBtn){
      const id = payBtn.dataset.pay;
      // simple simulation: call payments.php?action=pay
      if (!confirm('Procéder au paiement (simulation) ?')) return;
      try {
        const res = await fetch('includes/payments.php?action=pay', { method: 'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: 'id='+encodeURIComponent(id) });
        const j = await res.json();
        if (j.ok) { alert('Paiement enregistré'); fetchPayments(); } else alert(j.error || 'Erreur');
      } catch(e){ alert('Erreur réseau'); }
      return;
    }
    const receiptBtn = e.target.closest('button[data-receipt]');
    if (receiptBtn){
      const url = decodeURI(receiptBtn.dataset.receipt);
      // ouvrir dans un nouvel onglet si url absolute, sinon télécharger via fetch
      if (/^https?:\/\//.test(url)) {
        window.open(url, '_blank');
      } else {
        // tentative de téléchargement via fetch
        try {
          const r = await fetch(url);
          if (!r.ok) throw new Error('Not found');
          const blob = await r.blob();
          const a = document.createElement('a');
          a.href = URL.createObjectURL(blob);
          a.download = 'quittance_'+Date.now()+'.pdf';
          document.body.appendChild(a);
          a.click();
          a.remove();
        } catch(err){
          alert('Impossible de récupérer la quittance');
        }
      }
    }
  });

  filterPaymentStatus.addEventListener('change', fetchPayments);
  btnRefreshPayments.addEventListener('click', fetchPayments);

  // Submit new rental
  const rentalForm = document.getElementById('rentalForm');
  rentalForm.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(rentalForm);
    try {
      const res = await fetch('includes/account.php?action=create', { method:'POST', body: fd });
      const j = await res.json();
      if (j.ok) { alert('Demande envoyée'); rentalForm.reset(); fetchRequests(); tabs.forEach(t=>t.classList.remove('active')); document.querySelector('.tab[data-tab="requests"]').classList.add('active'); panelsRequestsShow(); } else {
        alert(j.error || 'Erreur');
      }
    } catch(e){ alert('Erreur réseau'); }
  });

  // small helpers
  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  // initial load
  fetchRequests();
  fetchPayments();

  // helper to show requests panel when using rentalForm callback
  function panelsRequestsShow(){
    panelsRequests = document.getElementById('tab-requests');
    panelsRequests.style.display = '';
    document.getElementById('tab-payments').style.display = 'none';
    document.getElementById('tab-new').style.display = 'none';
  }
})();
</script>
</body>
</html>