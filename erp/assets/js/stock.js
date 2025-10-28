// remplace le contenu du fichier existant
document.addEventListener('DOMContentLoaded', function() {
    const stockTbody = document.getElementById('stockTableBody');
    const btnAdd = document.getElementById('btnAdd');
    const modal = document.getElementById('stockModal');
    const stockForm = document.getElementById('stockForm');
    const inputId = document.getElementById('stockId');
    const inputName = document.getElementById('inputName');
    const inputQty = document.getElementById('inputQty');
    const inputPrice = document.getElementById('inputPrice');
    const btnCancel = document.getElementById('btnCancel');
    const btnDeleteModal = document.getElementById('btnDeleteModal');
    const modalTitle = document.getElementById('modalTitle');

    if (!stockTbody) return;

    function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

    function fetchStocks() {
        fetch('stock.php?action=fetch')
            .then(response => response.json())
            .then(data => {
                stockTbody.innerHTML = '';
                data.forEach(stock => {
                    const tr = document.createElement('tr');
                    tr.dataset.id = stock.id;
                    tr.innerHTML = `
                        <td>${stock.id}</td>
                        <td>${escapeHtml(stock.product_name || '')}</td>
                        <td>${stock.quantity}</td>
                        <td>${Number(stock.price).toFixed(2)} €</td>
                        <td>
                            <button class="btn btn-edit">Modifier</button>
                            <button class="btn btn-delete">Supprimer</button>
                        </td>`;
                    stockTbody.appendChild(tr);
                });
            })
            .catch(()=> console.error('Erreur fetch stocks'));
    }

    // event delegation for edit/delete
    stockTbody.addEventListener('click', function(e){
        const btn = e.target;
        const tr = btn.closest('tr');
        if (!tr) return;
        const id = tr.dataset.id;
        if (btn.classList.contains('btn-edit')) {
            openEdit(id, tr);
        } else if (btn.classList.contains('btn-delete')) {
            onDelete(id);
        }
    });

    btnAdd.addEventListener('click', function(){
        openCreate();
    });

    btnCancel.addEventListener('click', closeModal);

    btnDeleteModal.addEventListener('click', function(){
        const id = inputId.value;
        if (!id) return;
        if (!confirm('Supprimer cet article ?')) return;
        const form = new URLSearchParams();
        form.append('id', id);
        fetch('stock.php?action=delete', { method: 'POST', body: form })
            .then(r => r.json())
            .then(()=> { closeModal(); fetchStocks(); })
            .catch(()=> alert('Erreur suppression'));
    });

    stockForm.addEventListener('submit', function(e){
        e.preventDefault();
        const id = inputId.value;
        const url = id ? 'stock.php?action=update' : 'stock.php?action=create';
        const form = new URLSearchParams();
        if (id) form.append('id', id);
        form.append('product_name', inputName.value.trim());
        form.append('quantity', parseInt(inputQty.value) || 0);
        form.append('price', parseFloat(inputPrice.value) || 0);
        fetch(url, { method: 'POST', body: form })
            .then(r => r.json())
            .then(res => {
                if (res.error) { alert(res.error); return; }
                closeModal();
                fetchStocks();
            })
            .catch(()=> alert('Erreur réseau'));
    });

    function openCreate(){
        inputId.value = '';
        inputName.value = '';
        inputQty.value = 0;
        inputPrice.value = 0;
        modalTitle.textContent = 'Ajouter un produit';
        btnDeleteModal.style.display = 'none';
        showModal();
    }

    function openEdit(id, tr){
        inputId.value = id;
        inputName.value = tr.children[1].textContent;
        inputQty.value = parseInt(tr.children[2].textContent) || 0;
        // price cell contains "x,xx €" or "x.xx €" — extract numbers
        let priceText = tr.children[3].textContent.replace('€','').trim().replace(/\s/g,'').replace(',','.');
        inputPrice.value = parseFloat(priceText) || 0;
        modalTitle.textContent = 'Modifier le produit';
        btnDeleteModal.style.display = 'inline-block';
        showModal();
    }

    function onDelete(id){
        if (!confirm('Supprimer cet article ?')) return;
        const form = new URLSearchParams();
        form.append('id', id);
        fetch('stock.php?action=delete', { method: 'POST', body: form })
            .then(r => r.json())
            .then(()=> fetchStocks())
            .catch(()=> alert('Erreur suppression'));
    }

    function showModal(){
        modal.classList.add('show');
        modal.setAttribute('aria-hidden','false');
        inputName.focus();
    }

    function closeModal(){
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden','true');
    }

    fetchStocks();
});