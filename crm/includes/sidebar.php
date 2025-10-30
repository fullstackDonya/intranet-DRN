
<!-- CRM Sidebar include (no <html>/<head>/<body>) -->
<!-- Bouton pour ouvrir la sidebar sur mobile -->
<button class="btn btn-primary d-md-none mb-2" id="sidebarToggleMobile" style="position:fixed;top:15px;left:15px;z-index:1100;">
    <i class="fas fa-bars"></i>
</button>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="sidebar-brand-text mx-3">CRM Intelligent</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">CRM</div>

    <!-- Nav Item - Contacts -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseContacts">
            <i class="fas fa-fw fa-users"></i>
            <span>Contacts</span>
        </a>
        <div id="collapseContacts" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <!-- <h6 class="collapse-header">Gestion Contacts:</h6> -->
                <a class="collapse-item" href="contacts.php">Tous les contacts</a>
                <a class="collapse-item" href="contacts-add.php">Ajouter contact</a>
                <a class="collapse-item" href="contacts-import.php">Importer contacts</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Live Chat (notifications) -->
    <li class="nav-item">
        <a class="nav-link" href="../admin.html">
            <i class="fas fa-comments"></i>
            <span>Chat en direct</span>
            <span id="crm-chat-anchor" style="position:relative;">
                <span id="crm-chat-badge" class="badge badge-danger badge-counter" style="display:none; position:absolute; top:-8px; right:-10px;"></span>
            </span>
        </a>
    </li>

    <!-- Nav Item - Leads -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLeads">
            <i class="fas fa-fw fa-bullseye"></i>
            <span>Leads</span>
        </a>
        <div id="collapseLeads" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
              
                <a class="collapse-item" href="leads.php">Tous les leads</a>
                <a class="collapse-item" href="leads-add.php">Ajouter lead</a>
                <a class="collapse-item" href="leads-import.php">Importer leads</a>
                <a class="collapse-item" href="leads-scoring.php">Scoring IA</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Folders-->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFolders">
            <i class="fas fa-fw fa-folder-open"></i>
            <span>Dossiers</span>
        </a>
        <div id="collapseFolders" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="folders.php">Tous les dossiers</a>
                <a class="collapse-item" href="folder_add.php">Ajouter dossier</a>
                <a class="collapse-item" href="folders-import.php">Importer dossiers</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Missions-->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseMissions">
            <i class="fas fa-fw fa-tasks"></i>
            <span>Missions</span>
        </a>
        <div id="collapseMissions" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">

                <a class="collapse-item" href="missions.php">Toutes les missions</a>
                <a class="collapse-item" href="mission_add.php">Ajouter mission</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Opportunities -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseOpportunities">
            <i class="fas fa-fw fa-handshake"></i>
            <span>Opportunités</span>
        </a>
        <div id="collapseOpportunities" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="opportunities.php">Pipeline</a>
                <a class="collapse-item" href="opportunities-forecast.php">Prévisions IA</a>
                <a class="collapse-item" href="opportunities-analytics.php">Analytics</a>
                <a class="collapse-item" href="opportunities-import.php">Importer opportunités</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Customers -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCustomers">
            <i class="fas fa-fw fa-user-tie"></i>
            <span>Clients</span>
        </a>
        <div id="collapseCustomers" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="customers.php">Tous les clients</a>
                <a class="collapse-item" href="customers-add.php">Ajouter client</a>
                <a class="collapse-item" href="customers-import.php">Importer clients</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Analytics & BI</div>

    <!-- Nav Item - Power BI -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePowerBI">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Power BI</span>
        </a>
        <div id="collapsePowerBI" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
     
                <a class="collapse-item" href="powerbi-sales.php">Ventes</a>
                <a class="collapse-item" href="powerbi-customers.php">Clients</a>
                <a class="collapse-item" href="powerbi-performance.php">Performance</a>
                <a class="collapse-item" href="powerbi-predictions.php">Prédictions IA</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Analytics -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAnalytics">
            <i class="fas fa-fw fa-analytics"></i>
            <span>Analytics</span>
        </a>
        <div id="collapseAnalytics" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">

                <a class="collapse-item" href="analytics-sales.php">Analyse des ventes</a>
                <a class="collapse-item" href="analytics-customer.php">Comportement client</a>
                <a class="collapse-item" href="analytics-funnel.php">Entonnoir conversion</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - AI Insights -->
    <li class="nav-item">
        <a class="nav-link" href="ai-insights.php">
            <i class="fas fa-fw fa-brain"></i>
            <span>Insights IA</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Marketing</div>

    <!-- Nav Item - Campaigns -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCampaigns">
            <i class="fas fa-fw fa-bullhorn"></i>
            <span>Campagnes</span>
        </a>
        <div id="collapseCampaigns" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="campaigns.php">Toutes les campagnes</a>
                <a class="collapse-item" href="campaigns-email.php">Email Marketing</a>
                <a class="collapse-item" href="campaigns-automation.php">Automatisation IA</a>
                <a class="collapse-item" href="campaigns-import.php">Importer campagnes</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - retour a mon compte-->
    <li class="nav-item">
        <a class="nav-link" href="../account.php">
            <i class="fas fa-fw fa-user-cog"></i>
            <span>Mon Compte</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
  
 </ul>
 <div class="sidebar-backdrop"></div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.getElementById('sidebarToggleMobile');
        function ensureBackdrop(){
            let el = document.querySelector('.sidebar-backdrop');
            if (!el){
                el = document.createElement('div');
                el.className = 'sidebar-backdrop';
                document.body.appendChild(el);
            }
            return el;
        }
        const backdrop = ensureBackdrop();
        if (toggleBtn && sidebar && backdrop){
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('active');
            });
            backdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                backdrop.classList.remove('active');
            });
        }
    });
</script>

