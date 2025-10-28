
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('salesTableBody');
    const btnAdd = document.getElementById('btnAdd');
    const modal = document.getElementById('salesModal');
    const form = document.getElementById('salesForm');
    const saleId = document.getElementById('saleId');
    const selectProduct = document.getElementById('selectProduct');
    const selectEmployee = document.getElementById('selectEmployee');
    const inputQty = document.getElementById('inputQty');
    const inputTotal = document.getElementById('inputTotal');
    const btnCancel = document.getElementById('btnCancel');
    const modalTitle = document.getElementById('modalTitle');

    // élément minimum requis
    if (!tbody) {
        console.warn('sales.js: #salesTableBody introuvable — script arrêté');
        return;
    }

    function escapeHtml(s){ return (s||'').toString().replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

    function fetchSales() {
        fetch('sales.php?action=fetch')
            .then(r => r.json())
            .then(data => {
                tbody.innerHTML = '';
                data.forEach(s => {
                    const tr = document.createElement('tr');
                    tr.dataset.id = s.id;
                    tr.innerHTML = `
                        <td>${escapeHtml(s.id)}</td>
                        <td>${escapeHtml(s.product_name)}</td>
                        <td>${escapeHtml((s.first_name||'') + ' ' + (s.last_name||''))}</td>
                        <td>${escapeHtml(s.quantity)}</td>
                        <td>${escapeHtml(s.total_price)}</td>
                        <td>${escapeHtml(s.created_at)}</td>
                        <td><button class="btn btn-delete">Supprimer</button></td>`;
                    tbody.appendChild(tr);
                });
            })
            .catch(()=> console.error('Erreur fetch sales'));
    }

    // délégation d'événements sur le tbody (toujours présent)
    tbody.addEventListener('click', function(e){
        const btn = e.target;
        const tr = btn.closest('tr');
        if (!tr) return;
        const id = tr.dataset.id;
        if (btn.classList.contains('btn-delete')) {
            if (!confirm('Supprimer cette vente ?')) return;
            const fd = new URLSearchParams(); fd.append('id', id);
            fetch('sales.php?action=delete', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(()=> fetchSales())
                .catch(()=> alert('Erreur suppression'));
        }
    });

    // Le reste des interactions ne nécessite pas tbody; vérifier existence avant enregistrement d'écouteurs
    if (btnAdd) {
        btnAdd.addEventListener('click', function(){
            if (saleId) saleId.value = '';
            if (selectProduct) selectProduct.value = '';
            if (selectEmployee) selectEmployee.value = '';
            if (inputQty) inputQty.value = 1;
            if (inputTotal) inputTotal.value = '0.00';
            if (modalTitle) modalTitle.textContent = 'Ajouter une vente';
            if (modal) showModal();
        });
    } else {
        console.info('sales.js: #btnAdd introuvable — ajout désactivé');
    }

    if (btnCancel && typeof closeModal === 'function') {
        btnCancel.addEventListener('click', closeModal);
    }

    if (form) {
        form.addEventListener('submit', function(e){
            e.preventDefault();
            const url = 'sales.php?action=create';
            const fd = new URLSearchParams();
            fd.append('product_id', selectProduct ? selectProduct.value : '');
            fd.append('employee_id', selectEmployee ? selectEmployee.value : '');
            fd.append('quantity', parseInt(inputQty ? inputQty.value : 0) || 0);
            fd.append('total_price', parseFloat(inputTotal ? inputTotal.value : 0) || 0);
            fetch(url, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    if (res.error) { alert(res.error); return; }
                    if (typeof closeModal === 'function') closeModal();
                    fetchSales();
                })
                .catch(()=> alert('Erreur réseau'));
        });
    } else {
        console.info('sales.js: #salesForm introuvable — soumission désactivée');
    }

    function showModal(){ if (modal) { modal.classList.add('show'); modal.setAttribute('aria-hidden','false'); if (selectProduct) selectProduct.focus(); } }
    function closeModal(){ if (modal) { modal.classList.remove('show'); modal.setAttribute('aria-hidden','true'); } }

    fetchSales();
});
