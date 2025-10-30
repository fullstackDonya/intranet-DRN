document.addEventListener('DOMContentLoaded', function() {
    const stockTbody = document.getElementById('stockTableBody');
    const btnAdd = document.getElementById('btnAdd');
    const modal = document.getElementById('stockModal');
    const stockForm = document.getElementById('stockForm');
    const inputId = document.getElementById('stockId');
    const inputSku = document.getElementById('inputSku');
    const inputName = document.getElementById('inputName');
    const inputQty = document.getElementById('inputQty');
    const inputPrice = document.getElementById('inputPrice');
    const inputRentalRate = document.getElementById('inputRentalRate');
    const inputIsRental = document.getElementById('inputIsRental');
    const inputImage = document.getElementById('inputImage');
    const imagePreview = document.getElementById('imagePreview');
    const btnCancel = document.getElementById('btnCancel');
    const btnDeleteModal = document.getElementById('btnDeleteModal');
    const modalTitle = document.getElementById('modalTitle');

    if (!stockTbody) return;

    function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

    function fetchStocks() {
        fetch('includes/products.php?action=fetch')
            .then(response => {
                if (!response.ok) throw new Error('Network response not ok');
                return response.json();
            })
            .then(data => {
                stockTbody.innerHTML = '';
                data.forEach(stock => {
                    const img = stock.image ? 'assets/uploads/products/'+escapeHtml(stock.image) : 'assets/img/placeholder.png';
                    const tr = document.createElement('tr');
                    tr.dataset.id = stock.id;
                    tr.innerHTML = `
                        <td>${stock.id}</td>
                        <td><img class="thumb" src="${img}" alt=""></td>
                        <td>${escapeHtml(stock.sku || '')}</td>
                        <td>${escapeHtml(stock.name || '')}</td>
                        <td>${Number(stock.quantity || 0)}</td>
                        <td>${Number(stock.sale_price || 0).toFixed(2)} €</td>
                        <td>
                            <button class="btn btn-edit">Modifier</button>
                            <button class="btn btn-delete">Supprimer</button>
                        </td>`;
                    stockTbody.appendChild(tr);
                });
            })
            .catch(()=> console.error('Erreur fetch stocks'));
    }

    // preview image when selected
    if (inputImage) {
        inputImage.addEventListener('change', function(){
            const file = this.files[0];
            if (file) {
                const url = URL.createObjectURL(file);
                imagePreview.src = url;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }
        });
    }

    // edit/delete delegation...
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

    btnAdd.addEventListener('click', openCreate);
    btnCancel.addEventListener('click', closeModal);

    btnDeleteModal.addEventListener('click', function(){
        const id = inputId.value;
        if (!id) return;
        if (!confirm('Supprimer cet article ?')) return;
        const form = new URLSearchParams();
        form.append('id', id);
        fetch('includes/products.php?action=delete', { method: 'POST', body: form })
            .then(r => r.json())
            .then(()=> { closeModal(); fetchStocks(); })
            .catch(()=> alert('Erreur suppression'));
    });

    stockForm.addEventListener('submit', function(e){
        e.preventDefault();
        const id = inputId.value;
        const url = 'includes/products.php?action=' + (id ? 'update' : 'create');
        const fd = new FormData();
        if (id) fd.append('id', id);
        fd.append('sku', inputSku.value.trim());
        fd.append('name', inputName.value.trim());
        fd.append('quantity', parseInt(inputQty.value) || 0);
        fd.append('sale_price', parseFloat(inputPrice.value) || 0);
        if (inputRentalRate) fd.append('rental_rate_per_day', inputRentalRate.value ? parseFloat(inputRentalRate.value) : '');
        if (inputIsRental) fd.append('is_rental', inputIsRental.checked ? 1 : 0);
        if (inputImage && inputImage.files[0]) fd.append('image', inputImage.files[0]);

        fetch(url, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (!res) { alert('Réponse invalide du serveur'); return; }
                if (res.error) { alert(res.error); return; }
                closeModal();
                fetchStocks();
            })
            .catch(()=> alert('Erreur réseau'));
    });

    function openCreate(){
        inputId.value = '';
        if (inputSku) inputSku.value = '';
        inputName.value = '';
        inputQty.value = 0;
        inputPrice.value = 0;
        if (inputRentalRate) inputRentalRate.value = '';
        if (inputIsRental) inputIsRental.checked = false;
        if (inputImage) inputImage.value = '';
        imagePreview.style.display = 'none';
        modalTitle.textContent = 'Ajouter un produit';
        btnDeleteModal.style.display = 'none';
        showModal();
    }

    function openEdit(id, tr){
        inputId.value = id;
        if (inputSku) inputSku.value = tr.children[2].textContent.trim();
        inputName.value = tr.children[3].textContent.trim();
        inputQty.value = parseInt(tr.children[4].textContent) || 0;
        let priceText = tr.children[5].textContent.replace('€','').trim().replace(/\s/g,'').replace(',','.');
        inputPrice.value = parseFloat(priceText) || 0;
        // show existing image in preview (path from table)
        const imgEl = tr.querySelector('img.thumb');
        if (imgEl) {
            imagePreview.src = imgEl.src;
            imagePreview.style.display = 'block';
        } else {
            imagePreview.style.display = 'none';
        }
        modalTitle.textContent = 'Modifier le produit';
        btnDeleteModal.style.display = 'inline-block';
        showModal();
    }

    function onDelete(id){
        if (!confirm('Supprimer cet article ?')) return;
        const form = new URLSearchParams();
        form.append('id', id);
        fetch('includes/products.php?action=delete', { method: 'POST', body: form })
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