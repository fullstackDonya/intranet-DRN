document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('rentalTableBody');
    const btnAdd = document.getElementById('btnAdd');
    const modal = document.getElementById('rentalModal');
    const form = document.getElementById('rentalForm');
    const inputId = document.getElementById('rentalId');
    const inputCustomer = document.getElementById('rentalCustomer');
    const inputStart = document.getElementById('rentalStart');
    const inputEnd = document.getElementById('rentalEnd');
    const inputStatus = document.getElementById('rentalStatus');
    const inputTotal = document.getElementById('rentalTotal');
    const inputDeposit = document.getElementById('rentalDeposit');
    const btnCancel = document.getElementById('btnCancel');
    const btnDeleteModal = document.getElementById('btnDeleteModal');
    const modalTitle = document.getElementById('modalTitle');

    if (!tableBody || !btnAdd || !modal || !form) {
        console.warn('rentals.js: éléments manquants dans la page — script désactivé');
        return;
    }

    function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

    function fetchRentals(){
        fetch('includes/rentals.php?action=fetch', { credentials: 'same-origin' })
            .then(r=>{
                if (!r.ok) throw new Error('HTTP '+r.status);
                return r.json();
            })
            .then(data=>{
                tableBody.innerHTML = '';
                (data||[]).forEach(item=>{
                    const tr = document.createElement('tr');
                    tr.dataset.id = item.id;
                    tr.innerHTML = `<td>${item.id}</td>
                                    <td>${escapeHtml(item.customer_name || '')}</td>
                                    <td>${escapeHtml(item.start_date || '')}</td>
                                    <td>${escapeHtml(item.end_date || '')}</td>
                                    <td>${escapeHtml(item.status || '')}</td>
                                    <td style="text-align:right">${(item.total_price ? (Number(item.total_price).toFixed(2)+' €') : '—')}</td>
                                    <td>
                                      <button class="btn btn-edit">Modifier</button>
                                      <button class="btn btn-delete">Supprimer</button>
                                    </td>`;
                    tableBody.appendChild(tr);
                });
            })
            .catch(err=> {
                console.error('Erreur fetch rentals', err);
                tableBody.innerHTML = '<tr><td colspan="7">Erreur chargement</td></tr>';
            });
    }

    tableBody.addEventListener('click', function(e){
        const btn = e.target.closest('button');
        if (!btn) return;
        const tr = btn.closest('tr');
        if (!tr) return;
        const id = tr.dataset.id;
        if (btn.classList.contains('btn-edit')) return openEdit(id);
        if (btn.classList.contains('btn-delete')) return deleteItem(id);
    });

    btnAdd.addEventListener('click', openCreate);
    if (btnCancel) btnCancel.addEventListener('click', closeModal);
    if (btnDeleteModal) btnDeleteModal.addEventListener('click', function(){
        const id = inputId.value;
        if (!id) return;
        if (!confirm('Supprimer cette location ?')) return;
        const fd = new URLSearchParams(); fd.append('id', id);
        fetch('includes/rentals.php?action=delete', { method: 'POST', credentials: 'same-origin', body: fd })
            .then(r=>r.json()).then(()=>{ closeModal(); fetchRentals(); })
            .catch(()=> alert('Erreur suppression'));
    });

    form.addEventListener('submit', function(e){
        e.preventDefault();
        const id = inputId.value;
        const fd = new URLSearchParams();
        if (id) fd.append('id', id);
        fd.append('customer_id', inputCustomer.value || '');
        fd.append('start_date', inputStart.value || '');
        fd.append('end_date', inputEnd.value || '');
        fd.append('status', inputStatus.value || 'draft');
        fd.append('total_price', inputTotal.value || 0);
        fd.append('deposit', inputDeposit.value || 0);
        const url = id ? 'includes/rentals.php?action=update' : 'includes/rentals.php?action=create';
        fetch(url, { method: 'POST', credentials: 'same-origin', body: fd })
            .then(r=>{
                if (!r.ok) throw new Error('HTTP '+r.status);
                return r.json();
            })
            .then(res=>{
                if (res.error) { alert(res.error || 'Erreur'); return; }
                closeModal(); fetchRentals();
            })
            .catch(()=> alert('Erreur réseau'));
    });

    function openCreate(){
        inputId.value = '';
        if (inputCustomer) inputCustomer.value = '';
        if (inputStart) inputStart.value = '';
        if (inputEnd) inputEnd.value = '';
        if (inputStatus) inputStatus.value = 'draft';
        if (inputTotal) inputTotal.value = 0;
        if (inputDeposit) inputDeposit.value = 0;
        modalTitle.textContent = 'Nouvelle location';
        if (btnDeleteModal) btnDeleteModal.style.display = 'none';
        showModal();
    }

    function openEdit(id){
        fetch(`includes/rentals.php?action=get&id=${encodeURIComponent(id)}`, { credentials: 'same-origin' })
            .then(r=>{
                if (!r.ok) throw new Error('HTTP '+r.status);
                return r.json();
            })
            .then(data=>{
                if (!data) return alert('Élément introuvable');
                inputId.value = data.id || '';
                if (inputCustomer) inputCustomer.value = data.customer_id ?? '';
                if (inputStart) inputStart.value = data.start_date ?? '';
                if (inputEnd) inputEnd.value = data.end_date ?? '';
                if (inputStatus) inputStatus.value = data.status ?? 'draft';
                if (inputTotal) inputTotal.value = data.total_price ?? 0;
                if (inputDeposit) inputDeposit.value = data.deposit ?? 0;
                modalTitle.textContent = 'Modifier la location';
                if (btnDeleteModal) btnDeleteModal.style.display = 'inline-block';
                showModal();
            })
            .catch(()=> alert('Erreur chargement'));
    }

    function deleteItem(id){
        if (!confirm('Supprimer cette location ?')) return;
        const fd = new URLSearchParams(); fd.append('id', id);
        fetch('includes/rentals.php?action=delete', { method: 'POST', credentials: 'same-origin', body: fd })
            .then(r=>r.json())
            .then(()=> fetchRentals())
            .catch(()=> alert('Erreur suppression'));
    }

    function showModal(){
        modal.classList.add('show'); modal.setAttribute('aria-hidden','false');
        const first = modal.querySelector('input,select,textarea');
        if (first) first.focus();
    }
    function closeModal(){
        modal.classList.remove('show'); modal.setAttribute('aria-hidden','true');
    }

    // initial load
    fetchRentals();
});