<?php
require_once 'includes/verify_subscriptions.php';


$page_title = "Analytics Opportunités - CRM Intelligent";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">

            
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-chart-line text-primary"></i> Analytics des Opportunités
                    </h1>
                    <div>
                        <button class="btn btn-info me-2" onclick="refreshAnalytics()">
                            <i class="fas fa-sync"></i> Actualiser
                        </button>
                        <button class="btn btn-success" onclick="exportAnalytics()">
                            <i class="fas fa-download"></i> Exporter
                        </button>
                    </div>
                </div>

                <!-- Filtres de période -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Période d'analyse</label>
                                <select class="form-control" id="period-select">
                                    <option value="month">Ce mois</option>
                                    <option value="quarter" selected>Ce trimestre</option>
                                    <option value="year">Cette année</option>
                                    <option value="custom">Personnalisé</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Commercial</label>
                                <select class="form-control" id="salesperson-filter">
                                    <option value="">Tous les commerciaux</option>
                                    <option value="1">Jean Dupont</option>
                                    <option value="2">Marie Martin</option>
                                    <option value="3">Pierre Durand</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Secteur</label>
                                <select class="form-control" id="sector-filter">
                                    <option value="">Tous les secteurs</option>
                                    <option value="tech">Technologie</option>
                                    <option value="finance">Finance</option>
                                    <option value="retail">Commerce</option>
                                    <option value="industry">Industrie</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Actions</label>
                                <div>
                                    <button class="btn btn-outline-secondary" onclick="resetFilters()">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métriques principales -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Valeur Pipeline
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="pipeline-value">€1,245,600</div>
                                        <div class="text-xs text-success">
                                            <i class="fas fa-arrow-up"></i> +12% vs période précédente
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Taux de Conversion
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="conversion-rate">28.5%</div>
                                        <div class="text-xs text-success">
                                            <i class="fas fa-arrow-up"></i> +3.2% vs période précédente
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Cycle Moyen
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="avg-cycle">42 jours</div>
                                        <div class="text-xs text-danger">
                                            <i class="fas fa-arrow-up"></i> +5 jours vs période précédente
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Valeur Moyenne
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="avg-value">€45,200</div>
                                        <div class="text-xs text-success">
                                            <i class="fas fa-arrow-up"></i> +8% vs période précédente
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calculator fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphiques d'analyse -->
                <div class="row mb-4">
                    <div class="col-xl-8">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Évolution du Pipeline</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="pipeline-evolution-chart" height="320"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Répartition par Étape</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="stage-distribution-chart" height="320"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analyse par commercial -->
                <div class="row mb-4">
                    <div class="col-xl-6">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Performance par Commercial</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="salesperson-performance-chart" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Sources d'Opportunités</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="source-analysis-chart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analyse détaillée -->
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Analyse de la Vélocité</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Étape</th>
                                                <th>Nombre d'Opportunités</th>
                                                <th>Valeur Totale</th>
                                                <th>Temps Moyen</th>
                                                <th>Taux de Conversion</th>
                                                <th>Tendance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge bg-primary">Prospection</span></td>
                                                <td>45</td>
                                                <td>€234,500</td>
                                                <td>8 jours</td>
                                                <td>68%</td>
                                                <td><i class="fas fa-arrow-up text-success"></i></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-info">Qualification</span></td>
                                                <td>31</td>
                                                <td>€189,300</td>
                                                <td>12 jours</td>
                                                <td>72%</td>
                                                <td><i class="fas fa-arrow-up text-success"></i></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-warning">Proposition</span></td>
                                                <td>22</td>
                                                <td>€156,700</td>
                                                <td>15 jours</td>
                                                <td>64%</td>
                                                <td><i class="fas fa-arrow-down text-danger"></i></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-secondary">Négociation</span></td>
                                                <td>14</td>
                                                <td>€98,400</td>
                                                <td>18 jours</td>
                                                <td>58%</td>
                                                <td><i class="fas fa-minus text-warning"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Insights IA</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success mb-3">
                                    <h6 class="alert-heading"><i class="fas fa-lightbulb"></i> Recommandation</h6>
                                    <p class="mb-0">L'étape "Proposition" montre une baisse de conversion. Considérez une formation sur la présentation.</p>
                                </div>

                                <div class="alert alert-info mb-3">
                                    <h6 class="alert-heading"><i class="fas fa-chart-line"></i> Tendance</h6>
                                    <p class="mb-0">Les opportunités de plus de €50k ont 23% plus de chances de se conclure.</p>
                                </div>

                                <div class="alert alert-warning mb-0">
                                    <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Attention</h6>
                                    <p class="mb-0">Le cycle de vente s'allonge. Focus sur la qualification précoce recommandé.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/opportunities-analytics.js"></script>
</body>
</html>
