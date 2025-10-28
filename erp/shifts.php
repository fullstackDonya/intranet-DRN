<?php
include __DIR__ . '/includes/shifts.php';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Planning - ERP</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    :root{
      --accent:#2563eb;
      --muted:#6b7280;
      --bg:#f8fafc;
      --hour-h:60px;
      --company-blue:#2563eb;
      --company-bg:#eff6ff;
      --employee-orange:#f97316;
      --employee-bg:#fff7ed;
    }
    body{background:var(--bg);font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;}
    .container{max-width:1300px;margin:18px auto;padding:0 12px}
    h1{margin:12px 0 18px;font-size:20px}
    .controls{display:flex;gap:8px;align-items:center;margin-bottom:12px;flex-wrap:wrap}
    .btn{padding:8px 12px;border-radius:8px;border:1px solid transparent;cursor:pointer;font-weight:600}
    .btn-primary{background:var(--accent);color:#fff}
    .btn-ghost{background:#fff;border:1px solid #e6e9ef}
    .calendar-wrap{display:flex;gap:12px;background:#fff;border-radius:10px;padding:12px;box-shadow:0 6px 18px rgba(2,6,23,.06)}
    .hours-col{width:72px;border-right:1px solid #eef2f7;padding-right:8px}
    .hours-col .hour{height:var(--hour-h);display:flex;align-items:flex-start;color:var(--muted);font-size:13px;padding-top:6px}
    .days-col{flex:1;display:flex;flex-direction:column}
    .days-head{display:flex}
    .days-head .day-head{flex:1;padding:8px 10px;border-bottom:1px solid #eef2f7;font-weight:700}
    .grid{display:flex;flex:1;min-height: calc(var(--hour-h) * 14); }
    .day{flex:1;border-left:1px solid #f1f5f9;position:relative;background:linear-gradient(to bottom, transparent 0, transparent 99%, rgba(2,6,23,0.01) 100%);}
    .cell{height:var(--hour-h);border-bottom:1px dashed #f1f5f9}
    .selection{position:absolute;left:6px;right:6px;background:rgba(37,99,235,0.12);border:1px solid rgba(37,99,235,0.22);border-radius:6px;pointer-events:none;z-index:5}
    .shift{position:absolute;left:6px;right:6px;padding:6px 8px;border-radius:6px;box-shadow:0 4px 10px rgba(0,0,0,0.06);z-index:10;cursor:pointer;overflow:hidden}
    .shift.company{ background:var(--company-bg); border-left:4px solid var(--company-blue); color: #0b1220; }
    .shift.employee{ background:var(--employee-bg); border-left:4px solid var(--employee-orange); color:#2b1b00; }
    .shift .title{font-weight:700;font-size:13px}
    .shift .meta{font-size:12px;color:var(--muted);margin-top:4px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden}
    .modal{position:fixed;left:0;top:0;right:0;bottom:0;background:rgba(2,6,23,.45);display:none;align-items:center;justify-content:center;padding:12px;z-index:60}
    .modal .box{background:#fff;padding:18px;border-radius:10px;min-width:360px;max-width:720px;width:100%;box-shadow:0 8px 24px rgba(2,6,23,.16)}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .form-field{display:flex;flex-direction:column}
    .form-field label{font-weight:600;font-size:13px;margin-bottom:6px;color:#0f172a}
    .form-field input, .form-field select{width:100%;padding:8px 10px;border:1px solid #e6e9ef;border-radius:8px;background:#fff;outline:none;font-size:14px}
    .form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:12px}
    .hidden { display: none !important; }
    @media (max-width:900px){ .calendar-wrap{flex-direction:column} .hours-col{display:none} .days-head .day-head{font-size:13px;padding:8px} .grid{min-height: calc(var(--hour-h) * 10)} }
  </style>
</head>
<body>
    <?php include 'erp_nav.php'; ?>
<div class="container">
  <h1>Planning</h1>

  <div class="controls">
    <label>Sem. départ <input type="date" id="weekStart"></label>
    <button id="prevWeek" class="btn btn-ghost">‹ Semaine préc.</button>
    <button id="nextWeek" class="btn btn-ghost">Semaine suiv. ›</button>
    <button id="btnToday" class="btn btn-ghost">Aujourd'hui</button>

    <label style="margin-left:8px">Vue
      <select id="viewMode">
        <option value="employee">Personnel</option>
        <option value="company">Sociétés</option>
      </select>
    </label>

    <label style="margin-left:8px">Filtrer employé
      <select id="filterEmployee">
        <option value="">Tous</option>
        <?php foreach ($employees as $e): ?>
          <option value="<?= (int)$e['id'] ?>"><?= htmlspecialchars($e['last_name'].' '.$e['first_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label style="margin-left:8px">Filtrer société
      <select id="filterCompany">
        <option value="">Toutes</option>
        <?php foreach ($companies as $c): ?>
          <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <div style="margin-left:auto">
      <button id="btnAdd" class="btn btn-primary">Ajouter un créneau</button>
    </div>
  </div>

  <div class="calendar-wrap" id="calendarWrap" aria-live="polite">
    <div class="hours-col" id="hoursCol" aria-hidden="true"></div>
    <div class="days-col" style="flex:1">
      <div class="days-head" id="daysHead"></div>
      <div class="grid" id="grid"></div>
    </div>
  </div>
</div>

<!-- modal -->
<div class="modal" id="modal">
  <div class="box">
    <h3 id="modalTitle">Ajouter créneau</h3>
    <form id="shiftForm" novalidate>
      <input type="hidden" name="id" id="shiftId">
      <input type="hidden" name="view" id="formView" value="employee">
      <div class="form-grid">
        <div class="form-field" id="fieldEmployee">
          <label for="employeeSelect">Employé</label>
          <select name="employee_id" id="employeeSelect">
            <option value="">-- Sélectionner --</option>
            <?php foreach ($employees as $e): ?>
              <option value="<?= (int)$e['id'] ?>"><?= htmlspecialchars($e['last_name'].' '.$e['first_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-field" id="fieldCompany">
          <label for="companySelect">Société (facultatif)</label>
          <select name="company_id" id="companySelect">
            <option value="">-- Aucune --</option>
            <?php foreach ($companies as $c): ?>
              <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-field">
          <label for="startDatetime">Début</label>
          <input type="datetime-local" name="start_datetime" id="startDatetime" required>
        </div>
        <div class="form-field">
          <label for="endDatetime">Fin</label>
          <input type="datetime-local" name="end_datetime" id="endDatetime" required>
        </div>

        <div class="form-field">
          <label for="role">Rôle</label>
          <input name="role" id="role" placeholder="Ex: Réception, Support...">
        </div>
        <div class="form-field">
          <label for="notes">Notes</label>
          <input name="notes" id="notes" placeholder="Infos complémentaires">
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <button type="button" id="btnDelete" class="btn btn-danger" style="display:none">Supprimer</button>
        <button type="button" id="btnClose" class="btn btn-ghost">Fermer</button>
      </div>
    </form>
  </div>
</div>

<script>
(function(){
  const hoursCol = document.getElementById('hoursCol');
  const daysHead = document.getElementById('daysHead');
  const grid = document.getElementById('grid');
  const weekStartInput = document.getElementById('weekStart');
  const filterCompany = document.getElementById('filterCompany');
  const filterEmployee = document.getElementById('filterEmployee');
  const viewModeEl = document.getElementById('viewMode');
  const modal = document.getElementById('modal');
  const shiftForm = document.getElementById('shiftForm');
  const btnAdd = document.getElementById('btnAdd');
  const btnClose = document.getElementById('btnClose');
  const btnDelete = document.getElementById('btnDelete');
  const modalTitle = document.getElementById('modalTitle');

  const fieldEmployee = document.getElementById('fieldEmployee');
  const fieldCompany = document.getElementById('fieldCompany');
  const employeeSelect = document.getElementById('employeeSelect');
  const companySelect = document.getElementById('companySelect');
  const formView = document.getElementById('formView');

  const HOUR_HEIGHT = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--hour-h')) || 60;
  const VISIBLE_HOURS = 14;
  let visibleStartHour = 8;
  let visibleEndHour = visibleStartHour + VISIBLE_HOURS;
  let currentView = viewModeEl.value || 'employee';

  function startOfWeek(d){
    const date = new Date(d);
    const day = date.getDay() || 7;
    if (day !== 1) date.setDate(date.getDate() - (day - 1));
    date.setHours(0,0,0,0);
    return date;
  }
  function pad(n){ return String(n).padStart(2,'0'); }
  function formatDateISO(d){ return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()); }

  function buildGrid(days){
    hoursCol.innerHTML = '';
    for(let h = visibleStartHour; h < visibleEndHour; h++){
      const div = document.createElement('div');
      div.className = 'hour';
      div.textContent = `${pad(h)}:00`;
      div.style.height = HOUR_HEIGHT + 'px';
      hoursCol.appendChild(div);
    }
    daysHead.innerHTML = '';
    days.forEach(d=>{
      const hd = document.createElement('div');
      hd.className = 'day-head';
      hd.textContent = d.toLocaleDateString('fr-FR',{weekday:'short', day:'2-digit', month:'2-digit'});
      daysHead.appendChild(hd);
    });
    grid.innerHTML = '';
    days.forEach(d=>{
      const dayCol = document.createElement('div');
      dayCol.className = 'day';
      dayCol.dataset.date = formatDateISO(d);
      dayCol.style.minHeight = (HOUR_HEIGHT * (visibleEndHour - visibleStartHour)) + 'px';
      for(let h = visibleStartHour; h < visibleEndHour; h++){
        const cell = document.createElement('div');
        cell.className = 'cell';
        cell.dataset.hour = h;
        cell.style.height = HOUR_HEIGHT + 'px';
        dayCol.appendChild(cell);
      }
      attachSelectionHandlers(dayCol);
      grid.appendChild(dayCol);
    });
  }

  function minutesToPx(min){ return (min / 60) * HOUR_HEIGHT; }

  function addShiftToDOM(s){
    const day = s.start_datetime.slice(0,10);
    const dayCol = [...grid.children].find(c => c.dataset.date === day);
    if (!dayCol) return;
    const start = new Date(s.start_datetime);
    const end = new Date(s.end_datetime);
    const startMinutes = (start.getHours() * 60) + start.getMinutes();
    const endMinutes = (end.getHours() * 60) + end.getMinutes();
    const top = minutesToPx(Math.max(0, startMinutes - visibleStartHour*60));
    const height = minutesToPx(Math.max(30, endMinutes - startMinutes));
    const el = document.createElement('div');
    // class selon type (employé présent => orange, sinon company => bleu)
    el.className = 'shift ' + (s.employee_id ? 'employee' : 'company');
    el.style.top = top + 'px';
    el.style.height = height + 'px';
    el.dataset.id = s.id;

    if (currentView === 'company') {
      const title = s.company_name ? escapeHtml(s.company_name) : (escapeHtml(s.last_name || '')+' '+escapeHtml(s.first_name || ''));
      el.innerHTML = `<div class="title">${title}</div>
                      <div class="meta">${s.start_datetime.slice(11,16)} - ${s.end_datetime.slice(11,16)} ${s.last_name ? ' · '+escapeHtml(s.last_name)+' '+escapeHtml(s.first_name) : ''}</div>`;
    } else {
      const title = (s.last_name || '') + (s.first_name ? ' '+s.first_name : '');
      el.innerHTML = `<div class="title">${escapeHtml(title.trim())}</div>
                      <div class="meta">${s.start_datetime.slice(11,16)} - ${s.end_datetime.slice(11,16)} ${s.company_name ? ' · ' + escapeHtml(s.company_name) : ''}</div>`;
    }

    el.addEventListener('click', ()=> openEdit(s));
    dayCol.appendChild(el);
  }

  function clearShifts(){ [...grid.querySelectorAll('.shift')].forEach(n=>n.remove()); }

  function fetchAndRender(startDate, endDate){
    const companyParam = filterCompany.value ? '&company_id='+encodeURIComponent(filterCompany.value) : '';
    const employeeParam = filterEmployee.value ? '&employee_id='+encodeURIComponent(filterEmployee.value) : '';
    let extra = '';
    if (currentView === 'employee') extra = employeeParam;
    else extra = companyParam;
    fetch('shifts.php?action=fetch&start='+formatDateISO(startDate)+'&end='+formatDateISO(endDate) + extra)
      .then(r=>r.json())
      .then(items=>{
        clearShifts();
        items.forEach(addShiftToDOM);
      });
  }

  function loadWeek(base){
    const start = startOfWeek(base);
    const days = [];
    for(let i=0;i<7;i++){ const d = new Date(start); d.setDate(start.getDate()+i); days.push(d); }
    buildGrid(days);
    weekStartInput.value = formatDateISO(days[0]);
    fetchAndRender(days[0], days[6]);
  }

  function attachSelectionHandlers(dayCol){
    let selecting = false;
    let selStartY = 0;
    let selEl = null;

    function dayYToTime(y){
      const minutes = Math.round((y / HOUR_HEIGHT) * 60) + visibleStartHour*60;
      return Math.max(0, Math.round(minutes / 15) * 15);
    }

    dayCol.addEventListener('mousedown', (e)=>{
      // n'autorise la sélection par glisser que en vue "employee"
      if (currentView !== 'employee') return;
      if (e.button !== 0) return;
      selecting = true;
      const rect = dayCol.getBoundingClientRect();
      selStartY = e.clientY - rect.top;
      selEl = document.createElement('div');
      selEl.className = 'selection';
      selEl.style.top = (selStartY) + 'px';
      selEl.style.height = '2px';
      dayCol.appendChild(selEl);
      document.body.style.userSelect = 'none';
    });

    window.addEventListener('mousemove', (e)=>{
      if (!selecting || !selEl) return;
      const rect = dayCol.getBoundingClientRect();
      const y = Math.max(0, Math.min(rect.height, e.clientY - rect.top));
      const top = Math.min(selStartY, y);
      const bottom = Math.max(selStartY, y);
      selEl.style.top = top + 'px';
      selEl.style.height = (bottom - top) + 'px';
    });

    window.addEventListener('mouseup', (e)=>{
      if (!selecting || !selEl) return;
      const rect = dayCol.getBoundingClientRect();
      const endY = Math.max(0, Math.min(rect.height, e.clientY - rect.top));
      const startMinutes = dayYToTime(selStartY);
      const endMinutes = dayYToTime(endY);
      const dayDate = dayCol.dataset.date;
      const start = new Date(`${dayDate}T00:00:00`);
      start.setMinutes(startMinutes);
      const end = new Date(`${dayDate}T00:00:00`);
      end.setMinutes(endMinutes > startMinutes ? endMinutes : startMinutes + 60);
      openAddWithRange(start, end);
      selEl.remove();
      selEl = null;
      selecting = false;
      document.body.style.userSelect = '';
    });

    dayCol.addEventListener('click', (e)=>{
      // clic simple ouvre modal (autorisé pour les deux vues)
      if (e.detail && e.detail > 1) return;
      const rect = dayCol.getBoundingClientRect();
      const y = e.clientY - rect.top;
      const minutes = Math.round((y / HOUR_HEIGHT) * 60) + visibleStartHour*60;
      const startMin = Math.round(minutes / 15) * 15;
      const start = new Date(`${dayCol.dataset.date}T00:00:00`);
      start.setMinutes(startMin);
      const end = new Date(start.getTime() + 60*60*1000);
      openAddWithRange(start, end);
    });
  }

  function openAddWithRange(start, end){
    modal.style.display = 'flex';
    modalTitle.textContent = 'Ajouter créneau';
    document.getElementById('shiftId').value = '';
    // préremplissage selon vue et filtres
    if (currentView === 'employee') {
      employeeSelect.value = filterEmployee.value || '';
      companySelect.value = '';
    } else {
      employeeSelect.value = '';
      companySelect.value = filterCompany.value || '';
    }
    document.getElementById('startDatetime').value = toLocalInput(start);
    document.getElementById('endDatetime').value = toLocalInput(end);
    document.getElementById('role').value = '';
    document.getElementById('notes').value = '';
    btnDelete.style.display='none';
    adaptFormForView(currentView);
  }

  function openEdit(s){
    modal.style.display='flex';
    modalTitle.textContent = 'Modifier créneau';
    document.getElementById('shiftId').value = s.id;
    employeeSelect.value = s.employee_id || '';
    companySelect.value = s.company_id || '';
    document.getElementById('startDatetime').value = s.start_datetime.replace(' ', 'T').slice(0,16);
    document.getElementById('endDatetime').value = s.end_datetime.replace(' ', 'T').slice(0,16);
    document.getElementById('role').value = s.role || '';
    document.getElementById('notes').value = s.notes || '';
    btnDelete.style.display='inline-block';
    adaptFormForView(currentView);
  }

  function adaptFormForView(view){
    formView.value = view;
    if (view === 'employee') {
      fieldEmployee.classList.remove('hidden');
      employeeSelect.required = true;
      fieldCompany.classList.remove('hidden');
      companySelect.required = false;
    } else {
      fieldEmployee.classList.add('hidden');
      employeeSelect.required = false;
      fieldCompany.classList.remove('hidden');
      companySelect.required = true;
    }
  }

  function toLocalInput(d){ const dt = new Date(d); const y = dt.getFullYear(), m = pad(dt.getMonth()+1), day = pad(dt.getDate()), hh = pad(dt.getHours()), mm = pad(dt.getMinutes()); return `${y}-${m}-${day}T${hh}:${mm}`; }
  function escapeHtml(s){ return s ? s.replace(/[&<>"']/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])) : ''; }

  shiftForm.addEventListener('submit', function(e){
    e.preventDefault();
    const id = document.getElementById('shiftId').value;
    const form = new FormData(shiftForm);
    form.append('view', currentView);
    const action = id ? 'update' : 'create';
    if (id) form.append('id', id);
    fetch('shifts.php?action='+action, {method:'POST', body: form})
      .then(r=>r.json()).then(res=>{
        if (res.error){ alert(res.error); return; }
        modal.style.display='none';
        loadWeek(new Date(weekStartInput.value));
      }).catch(()=>{ alert('Erreur réseau'); });
  });

  document.getElementById('btnDelete').addEventListener('click', function(){
    if (!confirm('Supprimer ce créneau ?')) return;
    const id = document.getElementById('shiftId').value;
    const form = new FormData(); form.append('id', id);
    fetch('shifts.php?action=delete', {method:'POST', body: form})
      .then(r=>r.json()).then(()=>{ modal.style.display='none'; loadWeek(new Date(weekStartInput.value)); });
  });

  btnAdd.addEventListener('click', ()=> {
    const now = new Date();
    openAddWithRange(new Date(now.getFullYear(), now.getMonth(), now.getDate(), 9, 0), new Date(now.getFullYear(), now.getMonth(), now.getDate(), 17, 0));
  });

  btnClose.addEventListener('click', ()=> modal.style.display='none');

  document.getElementById('prevWeek').addEventListener('click', ()=> { const d = new Date(weekStartInput.value); d.setDate(d.getDate()-7); loadWeek(d); });
  document.getElementById('nextWeek').addEventListener('click', ()=> { const d = new Date(weekStartInput.value); d.setDate(d.getDate()+7); loadWeek(d); });
  document.getElementById('btnToday').addEventListener('click', ()=> loadWeek(new Date()));
  weekStartInput.addEventListener('change', ()=> loadWeek(new Date(weekStartInput.value)));

  viewModeEl.addEventListener('change', ()=> {
    currentView = viewModeEl.value;
    filterEmployee.parentElement.style.display = currentView === 'employee' ? '' : 'none';
    adaptFormForView(currentView);
    loadWeek(new Date(weekStartInput.value));
  });

  filterEmployee.addEventListener('change', ()=> loadWeek(new Date(weekStartInput.value)));
  filterCompany.addEventListener('change', ()=> loadWeek(new Date(weekStartInput.value)));

  if (currentView !== 'employee') filterEmployee.parentElement.style.display = 'none';
  adaptFormForView(currentView);

  loadWeek(new Date());
})();
</script>

</body>
</html>
