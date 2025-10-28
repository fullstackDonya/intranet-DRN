<?php
require_once 'includes/verify_subscriptions.php';



$page_title = "Entonnoir de Conversion";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $page_title; ?> - CRM Intelligent</title>
    
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

</head>

<body id="page-top">
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-filter text-primary"></i>
                            Entonnoir de Conversion
                        </h1>
                        <div class="btn-group">
                            <button class="btn btn-primary btn-sm" onclick="refreshFunnel()">
                                <i class="fas fa-sync-alt"></i> Actualiser
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="exportFunnelReport()">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="optimizeFunnel()">
                                <i class="fas fa-magic"></i> Optimiser IA
                            </button>
                        </div>
                    </div>

                    <!-- Alertes -->
                    <div id="alerts-container"></div>

                    <!-- Métriques de l'entonnoir -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Taux de conversion global
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="global-conversion-rate">
                                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
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
                                                Visiteurs uniques
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="unique-visitors">
                                                <div class="spinner-border spinner-border-sm" role="status"></div>
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
                                                Temps moyen dans l'entonnoir
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="avg-funnel-time">
                                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                                Opportunités perdues
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="lost-opportunities">
                                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visualisation de l'entonnoir -->
                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Entonnoir de Conversion</h6>
                                    <div class="dropdown no-arrow">
                                        <select class="form-control form-control-sm" id="funnel-period">
                                            <option value="7d">7 derniers jours</option>
                                            <option value="30d" selected>30 derniers jours</option>
                                            <option value="90d">90 derniers jours</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="funnel-visualization" class="text-center">
                                        <!-- Entonnoir interactif généré en JavaScript -->
                                        <div class="funnel-container">
                                            <div class="funnel-stage" data-stage="1">
                                                <div class="stage-bar stage-1">
                                                    <div class="stage-content">
                                                        <h5>Visiteurs</h5>
                                                        <span class="stage-count" id="stage-1-count">-</span>
                                                        <span class="stage-percentage">100%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="funnel-stage" data-stage="2">
                                                <div class="stage-bar stage-2">
                                                    <div class="stage-content">
                                                        <h5>Leads</h5>
                                                        <span class="stage-count" id="stage-2-count">-</span>
                                                        <span class="stage-percentage" id="stage-2-percentage">-%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="funnel-stage" data-stage="3">
                                                <div class="stage-bar stage-3">
                                                    <div class="stage-content">
                                                        <h5>Qualifiés</h5>
                                                        <span class="stage-count" id="stage-3-count">-</span>
                                                        <span class="stage-percentage" id="stage-3-percentage">-%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="funnel-stage" data-stage="4">
                                                <div class="stage-bar stage-4">
                                                    <div class="stage-content">
                                                        <h5>Opportunités</h5>
                                                        <span class="stage-count" id="stage-4-count">-</span>
                                                        <span class="stage-percentage" id="stage-4-percentage">-%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="funnel-stage" data-stage="5">
                                                <div class="stage-bar stage-5">
                                                    <div class="stage-content">
                                                        <h5>Clients</h5>
                                                        <span class="stage-count" id="stage-5-count">-</span>
                                                        <span class="stage-percentage" id="stage-5-percentage">-%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Points de friction identifiés</h6>
                                </div>
                                <div class="card-body">
                                    <div id="friction-points">
                                        <div class="text-center">
                                            <div class="spinner-border" role="status">
                                                <span class="sr-only">Analyse en cours...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analyse par canal -->
                    <div class="row">
                        <div class="col-xl-6 col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Conversion par canal</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="channelConversionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Évolution temporelle</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="temporalEvolutionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analyse détaillée des abandons -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Analyse des Abandons par Étape</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="abandonment-analysis" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Étape</th>
                                                    <th>Entrées</th>
                                                    <th>Sorties</th>
                                                    <th>Abandons</th>
                                                    <th>Taux d'abandon</th>
                                                    <th>Raisons principales</th>
                                                    <th>Actions recommandées</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Données chargées via JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tests A/B actifs -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Tests A/B Actifs</h6>
                                    <button class="btn btn-primary btn-sm" onclick="createABTest()">
                                        <i class="fas fa-plus"></i> Nouveau test
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="ab-tests-table" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Test</th>
                                                    <th>Étape ciblée</th>
                                                    <th>Variant A</th>
                                                    <th>Variant B</th>
                                                    <th>Conversion A</th>
                                                    <th>Conversion B</th>
                                                    <th>Signification</th>
                                                    <th>Statut</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Données chargées via JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

        </div>
     </div>

    <!-- Modal Analyse Détaillée -->
    <div class="modal fade" id="stageAnalysisModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Analyse Détaillée de l'Étape</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="stage-analysis-content">
                        <!-- Contenu chargé dynamiquement -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" onclick="implementOptimizations()">
                        Implémenter optimisations
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Création Test A/B -->
    <div class="modal fade" id="createABTestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Créer un Test A/B</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="ab-test-form">
                        <div class="form-group">
                            <label>Nom du test</label>
                            <input type="text" class="form-control" id="test-name" required>
                        </div>
                        <div class="form-group">
                            <label>Étape de l'entonnoir</label>
                            <select class="form-control" id="test-stage">
                                <option value="landing">Page d'atterrissage</option>
                                <option value="form">Formulaire de lead</option>
                                <option value="qualification">Qualification</option>
                                <option value="proposal">Proposition</option>
                                <option value="closing">Finalisation</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description variant A (contrôle)</label>
                            <textarea class="form-control" id="variant-a" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Description variant B (test)</label>
                            <textarea class="form-control" id="variant-b" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Répartition du trafic (%)</label>
                            <input type="range" class="form-control-range" id="traffic-split" min="10" max="90" value="50">
                            <small class="form-text text-muted">A: <span id="split-a">50</span>% - B: <span id="split-b">50</span>%</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="launchABTest()">Lancer le test</button>
                </div>
            </div>
        </div>
    </div>

    <style>
    .funnel-container {
        padding: 20px 0;
    }
    
    .funnel-stage {
        margin: 10px 0;
        position: relative;
    }
    
    .stage-bar {
        height: 80px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
        clip-path: polygon(0 0, calc(100% - 30px) 0, 100% 50%, calc(100% - 30px) 100%, 0 100%, 30px 50%);
    }
    
    .stage-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 100%; }
    .stage-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); width: 80%; margin: 0 auto; }
    .stage-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); width: 60%; margin: 0 auto; }
    .stage-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); width: 40%; margin: 0 auto; }
    .stage-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); width: 20%; margin: 0 auto; }
    
    .stage-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        text-align: center;
        font-weight: bold;
    }
    
    .stage-bar:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .journey-step {
        text-align: center;
        margin: 20px 0;
    }
    
    .step-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 24px;
    }
    </style>

    <!-- Scripts -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="assets/js/sb-admin-2.min.js"></script>
    <script src="assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/analytics-funnel.js"></script>
</body>
</html>
