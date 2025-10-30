<?php
include 'includes/index.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Intelligent - Dashboard</title>
     <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"></noscript>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Power BI JavaScript SDK -->
    <script src="https://unpkg.com/powerbi-client@2.22.0/dist/powerbi.min.js"></script>
</head>
<body>
    

    <div class="wrapper">

        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header --> 
            <?php 
                // include 'includes/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="container-fluid">
                <!-- Diagnostic Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="m-0"><i class="fas fa-tools"></i> Diagnostic et Installation</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <button class="btn btn-success me-2" onclick="quickSetup()">
                                            <i class="fas fa-magic"></i> Configuration Rapide
                                        </button>
                                        <button class="btn btn-info me-2" onclick="checkDatabase()">
                                            <i class="fas fa-database"></i> Vérifier BD
                                        </button>
                                        <button class="btn btn-primary me-2" onclick="installDatabase()">
                                            <i class="fas fa-download"></i> Installer CRM
                                        </button>
                                        <button class="btn btn-warning" onclick="runFullDiagnostic()">
                                            <i class="fas fa-stethoscope"></i> Diagnostic
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="diagnostic-result" class="alert alert-info" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- KPI Cards -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Revenus Totaux
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-revenue">
                                            €<?php echo number_format($total_revenue, 2, ',', ' '); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Clients Actifs
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-clients">
                                            <?php echo intval($active_clients); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Taux de Conversion
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="conversion-rate">
                                            <?php echo number_format($conversion_rate, 1, ',', ' '); ?>%
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Opportunités
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="opportunities">
                                            <?php echo intval($opportunities); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

        
                </div>

                <!-- ici les invoices et missions -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content
                                between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-file-invoice"></i> Factures Récentes
                                </h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow">
                                        <a class="dropdown-item" href="#" onclick="refreshInvoices()">Actualiser</a>
                                        <a class="dropdown-item" href="#" onclick="createInvoice()">Créer Facture</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="invoices-list">
                                    <!-- Invoices will be loaded here -->
                                </div>
                            </div>                  
                        </div>
                    </div>          
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content
                                between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-tasks"></i> Missions Récentes
                                </h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow">
                                        <a class="dropdown-item" href="#" onclick="refreshMissions()">Actualiser</a>
                                        <a class="dropdown-item" href="#" onclick="createMission()">Créer Mission</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="missions-list">
                                    <!-- Missions will be loaded here -->
                                </div>
                            </div>                  
                        </div>
                    </div>
                </div>  
                <!-- End invoices et missions -->

                <script>
                    (async function(){
                        const API_INVOICES = 'api/invoices-list.php';
                        const API_MISSIONS = 'api/missions-list.php';
                        const POLL_MS = 30000; // polling toutes les 30s
                    
                        function q(id){ return document.getElementById(id); }
                        function emptyHtml(msg){ return `<div class="text-muted py-3">${msg}</div>`; }
                    
                        async function fetchJson(url, opts = {}) {
                            opts.credentials = 'same-origin';
                            try {
                                const res = await fetch(url, opts);
                                if (!res.ok) throw new Error('HTTP ' + res.status);
                                return await res.json();
                            } catch (e) {
                                console.error('fetch error', url, e);
                                return { success: false, error: e.message || 'network' };
                            }
                        }
                    
                        function renderInvoicesList(data) {
                            const container = q('invoices-list');
                            if(!container) return;
                            if(!data || !data.success || !Array.isArray(data.invoices) || data.invoices.length === 0){
                                container.innerHTML = emptyHtml('Aucune facture récente.');
                                return;
                            }
                            const html = data.invoices.map(inv => `
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <strong>${escapeHtml(inv.invoice_number || '—')}</strong><br>
                                        <small class="text-green">${escapeHtml(inv.company_name || '')}</small>
                                    </div>
                                    <div class="text-end">
                                        <div><strong>€${Number(inv.amount||0).toFixed(2)}</strong></div>
                                        <small class="text-green">${escapeHtml(inv.status || '')}</small>
                                    </div>
                                </div>
                                <hr class="my-2">
                            `).join('');
                            container.innerHTML = html;
                        }
                    
                        function renderMissionsList(data) {
                            const container = q('missions-list');
                            if(!container) return;
                            if(!data || !data.success || !Array.isArray(data.missions) || data.missions.length === 0){
                                container.innerHTML = emptyHtml('Aucune mission récente.');
                                return;

                            }
                            console.log("missions", data.missions);
                            
                            const html = data.missions.map(ms => `
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <strong>${escapeHtml(ms.title || '—')}</strong><br>
                                        <small class="text-muted">${escapeHtml(ms.assignee || '—')}</small>
                                    </div>
                                    <div class="text-end">
                                        <div><small class="text-muted">${escapeHtml(ms.due_date || '')}</small></div>
                                        <div><span class="badge bg-${ms.status === 'done' ? 'success' : (ms.status === 'pending' ? 'warning' : 'secondary')}">${escapeHtml(ms.status || '')}</span></div>
                                    </div>
                                </div>
                                <hr class="my-2">
                            `).join('');
                            container.innerHTML = html;
                        }
                    
                        function escapeHtml(s){
                            if (!s) return '';
                            return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
                        }
                    
                        window.refreshInvoices = async function(){
                            const res = await fetchJson(API_INVOICES);
                            console.log("📦 Invoices data:", res); // <--- ajout
                            renderInvoicesList(res);
                        };



                        window.createInvoice = function(){
                            // prefer modal if exists, otherwise redirect to add page
                            if (typeof openInvoiceModal === 'function') return openInvoiceModal();
                            window.location.href = 'invoices-add.php';
                        };

                        window.refreshMissions = async function(){
                            const res = await fetchJson(API_MISSIONS);
                            console.log("🧩 Missions data:", res); // <--- ajout
                            renderMissionsList(res);
                        };
                        window.createMission = function(){
                            if (typeof openMissionModal === 'function') return openMissionModal();
                            window.location.href = 'mission_add.php';
                        };
                    
                        // initial load
                        await Promise.all([ window.refreshInvoices(), window.refreshMissions() ]);
                    
                        // polling
                        setInterval(() => {
                            window.refreshInvoices();
                            window.refreshMissions();
                        }, POLL_MS);
                    
                        // attach auto-refresh on visibility change (optional)
                        document.addEventListener('visibilitychange', () => {
                            if (!document.hidden) {
                                window.refreshInvoices();
                                window.refreshMissions();
                            }
                        });
                    })();
                </script>


                <!-- Power BI Integration Section -->
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-bar"></i> Rapports Power BI
                                </h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow">
                                        <a class="dropdown-item" href="#" onclick="refreshPowerBI()">Actualiser</a>
                                        <a class="dropdown-item" href="#" onclick="fullscreenPowerBI()">Plein écran</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Power BI Embedded Container -->
                                <div id="powerbi-container" style="height: 500px; border: 1px solid #ccc;">
                                    <div class="text-center p-5">
                                        <i class="fas fa-spinner fa-spin fa-3x text-muted mb-3"></i>
                                        <p>Chargement du rapport Power BI...</p>
                                        <button class="btn btn-primary" onclick="loadPowerBIReport()">
                                            Charger le rapport
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Charts -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Évolution des Ventes</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Répartition par Source</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="sourceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Activités Récentes</h6>
                            </div>
                            <div class="card-body">
                                <div id="recent-activities">
                                    <!-- Activities will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        

        
        </div>
    </div>
     

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



  

    </body>
    </html>