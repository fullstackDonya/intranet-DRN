document.addEventListener('DOMContentLoaded', function(){
  const tbody = document.querySelector('#requestsTable tbody');
  const form = document.getElementById('rentalForm');

  function statusBadge(status){
    if(status==='approved') return `<span class="status-badge status-approved">Approuvé</span>`;
    if(status==='rejected') return `<span class="status-badge status-rejected">Refusé</span>`;
    return `<span class="status-badge status-pending">En attente</span>`;
  }

  function fetchRequests(){
    fetch('includes/user_rentals.php?action=fetch')
      .then(r=> r.json())
      .then(data=>{
        tbody.innerHTML = '';
        if(!Array.isArray(data) || data.length===0){
          tbody.innerHTML = '<tr><td colspan="5">Aucune demande</td></tr>';
          return;
        }
        data.forEach(r=>{
          const period = r.start_date + (r.end_date ? ' → '+r.end_date : '');
          const name = r.product_name ? `${escapeHtml(r.product_name)} ${r.sku ? '('+escapeHtml(r.sku)+')' : ''}` : '—';
          const tr = document.createElement('tr');
          tr.innerHTML = `<td>${r.id}</td>
                          <td>${name}</td>
                          <td>${period}</td>
                          <td>${r.quantity}</td>
                          <td>${statusBadge(r.status)}</td>`;
          tbody.appendChild(tr);
        });
      })
      .catch(()=> { tbody.innerHTML = '<tr><td colspan="5">Erreur chargement</td></tr>'; });
  }

  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  form.addEventListener('submit', function(e){
    e.preventDefault();
    const fd = new FormData(form);
    fetch('includes/user_rentals.php?action=create', { method: 'POST', body: fd })
      .then(r=> r.json())
      .then(res=>{
        if (res && res.ok) {
          alert('Demande envoyée');
          form.reset();
          fetchRequests();
        } else {
          alert(res.error || 'Erreur');
        }
      })
      .catch(()=> alert('Erreur réseau'));
  });

  fetchRequests();
});