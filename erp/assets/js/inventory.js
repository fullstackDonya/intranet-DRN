
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('inventoryTableBody');
    const btnAdd = document.getElementById('btnAdd');
    const modal = document.getElementById('inventoryModal');
    const form = document.getElementById('addInventoryForm');
    const inputId = document.getElementById('inventoryId');
    const inputName = document.getElementById('inventoryName');
    const inputQty = document.getElementById('inventoryQuantity');
    const inputDesc = document.getElementById('inventoryDescription');
    const btnCancel = document.getElementById('btnCancel');
    const btnDeleteModal = document.getElementById('btnDeleteModal');
    const modalTitle = document.getElementById('modalTitle');

    function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

    function fetchInventory(){
        fetch('inventory.php?action=fetch')
            .then(r=>r.json())
            .then(data=>{
                tableBody.innerHTML = '';
                data.forEach(item=>{
                    const tr = document.createElement('tr');
                    tr.dataset.id = item.id;
                    tr.innerHTML = `<td>${item.id}</td>
                                    <td>${escapeHtml(item.item_name)}</td>
                                    <td>${item.quantity}</td>
                                    <td>${escapeHtml(item.description || '')}</td>
                                    <td>
                                      <button class="btn btn-edit">Modifier</button>
                                      <button class="btn btn-delete">Supprimer</button>
                                    </td>`;
                    tableBody.appendChild(tr);
                });
            })
            .catch(()=> console.error('Erreur fetch inventory'));
    }

    tableBody.addEventListener('click', function(e){
        const btn = e.target;
        const tr = btn.closest('tr');
        if (!tr) return;
        const id = tr.dataset.id;
        if (btn.classList.contains('btn-edit')) return openEdit(id, tr);
        if (btn.classList.contains('btn-delete')) return deleteItem(id);
    });

    btnAdd.addEventListener('click', openCreate);
    btnCancel.addEventListener('click', closeModal);
    btnDeleteModal.addEventListener('click', function(){
        const id = inputId.value;
        if (!id) return;
        if (!confirm('Supprimer cet article ?')) return;
        const fd = new URLSearchParams(); fd.append('id', id);
        fetch('inventory.php?action=delete', { method: 'POST', body: fd })
            .then(r=>r.json()).then(()=>{ closeModal(); fetchInventory(); })
            .catch(()=> alert('Erreur suppression'));
    });

    form.addEventListener('submit', function(e){
        e.preventDefault();
        const id = inputId.value;
        const fd = new URLSearchParams();
        if (id) fd.append('id', id);
        fd.append('item_name', inputName.value.trim());
        fd.append('quantity', parseInt(inputQty.value) || 0);
        fd.append('description', inputDesc.value.trim());
        const url = id ? 'inventory.php?action=update' : 'inventory.php?action=create';
        fetch(url, { method: 'POST', body: fd })
            .then(r=>r.json())
            .then(res=>{
                if (res.error) { alert(res.error || 'Erreur'); return; }
                closeModal(); fetchInventory();
            })
            .catch(()=> alert('Erreur réseau'));
    });

    function openCreate(){
        inputId.value = '';
        inputName.value = '';
        inputQty.value = 0;
        inputDesc.value = '';
        modalTitle.textContent = 'Ajouter un article';
        btnDeleteModal.style.display = 'none';
        showModal();
    }

    function openEdit(id, tr){
        fetch(`inventory.php?action=get&id=${id}`)
            .then(r=>r.json())
            .then(data=>{
                if (!data) return alert('Élément introuvable');
                inputId.value = data.id;
                inputName.value = data.item_name;
                inputQty.value = data.quantity;
                inputDesc.value = data.description || '';
                modalTitle.textContent = 'Modifier l\'article';
                btnDeleteModal.style.display = 'inline-block';
                showModal();
            })
            .catch(()=> alert('Erreur chargement'));
    }

    function deleteItem(id){
        if (!confirm('Supprimer cet article ?')) return;
        const fd = new URLSearchParams(); fd.append('id', id);
        fetch('inventory.php?action=delete', { method: 'POST', body: fd })
            .then(r=>r.json())
            .then(()=> fetchInventory())
            .catch(()=> alert('Erreur suppression'));
    }

    function showModal(){
        modal.classList.add('show'); modal.setAttribute('aria-hidden','false'); inputName.focus();
    }
    function closeModal(){
        modal.classList.remove('show'); modal.setAttribute('aria-hidden','true');
    }

    fetchInventory();
});