

<head>
  <meta charset="utf-8">
  <title>Planning - ERP</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">


</head>
<body>

  <div class="layout">
    <aside class="sidebar" id="sidebar" aria-hidden="false">
      <div class="brand">
        <div class="logo">ERP</div>
        <div>
          <div class="title">Webitech ERP</div>
          <div class="small" style="color:rgba(255,255,255,.7)">Gestion RH & planning</div>
        </div>
      </div>

      <div class="search" style="margin-top:8px">
        <input id="sidebarSearch" placeholder="Rechercher..." type="search" aria-label="Recherche">
      </div>

      <nav class="nav-section" aria-label="Navigation principale">
        <a class="nav-link" href="index.php"><span class="icon">🏠</span><span class="label">Dashboard</span></a>
        <a class="nav-link" href="employees.php"><span class="icon">👥</span><span class="label">Personnel</span></a>
        <a class="nav-link" href="companies.php"><span class="icon">🏢</span><span class="label">Entreprises</span></a>
        <a class="nav-link" href="shifts.php"><span class="icon">📅</span><span class="label">Planning</span></a>
        <a class="nav-link" href="products.php"><span class="icon">📦</span><span class="label">Produits</span></a>
        <a class="nav-link" href="rentals.php"><span class="icon">📦</span><span class="label">Locations</span></a>
        <a class="nav-link" href="sales.php"><span class="icon">💰</span><span class="label">Ventes</span></a>
        <a class="nav-link" href="payroll.php"><span class="icon">💶</span><span class="label">Paies</span></a>
        <a class="nav-link" href="reports.php"><span class="icon">📊</span><span class="label">Rapports</span></a>
      </nav>

      <div class="nav-divider" role="separator"></div>

      <nav class="nav-section" aria-label="Actions">
        <a class="nav-link" href="shifts.php?action=add"><span class="icon">➕</span><span class="label">Nouveau créneau</span></a>
        <a class="nav-link" href="employees.php?action=export"><span class="icon">⬇️</span><span class="label">Exporter Personnel</span></a>
      </nav>

      <div class="user" role="note">
        <div class="avatar"><?= isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'],0,1)) : 'U' ?></div>
        <div>
          <div class="name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur') ?></div>
          <div class="small" style="color:rgba(255,255,255,.6)"><?= htmlspecialchars($_SESSION['customer_name'] ?? '') ?></div>
        </div>
      </div>
    </aside>

  
  <div class="mobile-overlay" id="mobileOverlay"></div>

<script>
  // Sidebar interactivity
  (function(){
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('toggleSidebar');
    const overlay = document.getElementById('mobileOverlay');
    const openAddBtn = document.getElementById('openAdd');

    function isMobile(){ return window.innerWidth <= 900; }

    toggle.addEventListener('click', ()=>{
      if (isMobile()){
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
      } else {
        sidebar.classList.toggle('collapsed');
        document.querySelectorAll('.sidebar .label').forEach(el=> el.style.display = sidebar.classList.contains('collapsed') ? 'none' : '');
      }
    });

    overlay.addEventListener('click', ()=>{
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    });

    // close on ESC
    document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape'){ sidebar.classList.remove('open'); overlay.classList.remove('active'); } });

    // quick open add modal (if modal exists)
    openAddBtn && openAddBtn.addEventListener('click', ()=> {
      const addBtn = document.getElementById('btnAdd');
      if(addBtn) addBtn.click();
    });

    // sidebar search (simple client filter)
    const sInput = document.getElementById('sidebarSearch');
    sInput && sInput.addEventListener('input', (e)=>{
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('.nav-section .nav-link').forEach(a=>{
        a.style.display = a.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });

    // responsive collapse on resize
    window.addEventListener('resize', ()=>{
      if (!isMobile()){
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
      }
    });
  })();
</script>

</body>
</html>
