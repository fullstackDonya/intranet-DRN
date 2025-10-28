<?php
// initialisation & vérifications
require_once 'includes/verify_subscriptions.php';

// Charger les données du pipeline/opportunités (définit $pipeline_value, $active_opportunities, $won_value, $lost_value, $close_rate, $avg_cycle, etc.)
require_once 'includes/opportunities.php';

// Titre de la page si non déjà défini
$page_title = $page_title ?? "Prévisions IA des Opportunités";
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
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header
             <include 'includes/header.php';>
            -->

            <!-- Page Content -->
            <div class="container-fluid">
                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Prévisions IA des Opportunités</h1>
                    <div class="btn-group">
                        <button class="btn btn-primary" id="refreshForecast">
                            <i class="fas fa-sync-alt"></i> Actualiser
                        </button>
                        <button class="btn btn-success" id="exportForecast">
                            <i class="fas fa-download"></i> Exporter
                        </button>
                    </div>
                </div>

                <!-- Forecast Overview Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Prévision ce mois</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">€245,600</div>
                                        <div class="text-xs text-success">
                                            <i class="fas fa-arrow-up"></i> +15% vs mois dernier
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
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Prévision trimestre</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">€892,400</div>
                                        <div class="text-xs text-info">
                                            <i class="fas fa-arrow-up"></i> +8% vs prév. initiale
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Précision modèle</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">91.2%</div>
                                        <div class="text-xs text-warning">
                                            Basé sur 6 mois de données
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-brain fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Probabilité globale</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">73%</div>
                                        <div class="text-xs text-primary">
                                            Moyenne pondérée
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Forecast Charts -->
                <div class="row mb-4">
                    <div class="col-xl-8">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Prévisions vs Réalisé</h6>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary active" data-period="month">Mois</button>
                                    <button class="btn btn-outline-primary" data-period="quarter">Trimestre</button>
                                    <button class="btn btn-outline-primary" data-period="year">Année</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="forecastChart" width="100%" height="40"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Facteurs d'Influence IA</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h4 class="small font-weight-bold">Saisonnalité <span class="float-right">85%</span></h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 85%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <h4 class="small font-weight-bold">Tendance marché <span class="float-right">72%</span></h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 72%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <h4 class="small font-weight-bold">Performance équipe <span class="float-right">68%</span></h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 68%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <h4 class="small font-weight-bold">Concurrence <span class="float-right">45%</span></h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 45%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opportunities Forecast Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Opportunités avec Prévisions IA</h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                Filtrer par probabilité
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" data-filter="all">Toutes</a>
                                <a class="dropdown-item" href="#" data-filter="high">Élevée (>70%)</a>
                                <a class="dropdown-item" href="#" data-filter="medium">Moyenne (40-70%)</a>
                                <a class="dropdown-item" href="#" data-filter="low">Faible (<40%)</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="opportunitiesTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Opportunité</th>
                                        <th>Valeur</th>
                                        <th>Probabilité IA</th>
                                        <th>Date prévue</th>
                                        <th>Facteurs clés</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>Contrat Enterprise - TechCorp</strong><br>
                                                <small class="text-muted">Contact: Marie Dubois</small>
                                            </div>
                                        </td>
                                        <td><strong>€85,000</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-success me-2">87%</span>
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar bg-success" style="width: 87%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-success">15 Nov 2024</span><br>
                                            <small class="text-muted">Dans 12 jours</small>
                                        </td>
                                        <td>
                                            <small>
                                                • Budget confirmé<br>
                                                • Décideur identifié<br>
                                                • Concurrence faible
                                            </small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-success">Finaliser</button>
                                            <button class="btn btn-sm btn-primary">Détails</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>Solution CRM - StartupXYZ</strong><br>
                                                <small class="text-muted">Contact: Pierre Martin</small>
                                            </div>
                                        </td>
                                        <td><strong>€45,000</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-warning me-2">62%</span>
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar bg-warning" style="width: 62%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-warning">28 Nov 2024</span><br>
                                            <small class="text-muted">Dans 25 jours</small>
                                        </td>
                                        <td>
                                            <small>
                                                • Intérêt confirmé<br>
                                                • Budget en évaluation<br>
                                                • Timing serré
                                            </small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning">Relancer</button>
                                            <button class="btn btn-sm btn-primary">Détails</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>Plateforme Analytics - BigData Inc</strong><br>
                                                <small class="text-muted">Contact: Sarah Johnson</small>
                                            </div>
                                        </td>
                                        <td><strong>€125,000</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-danger me-2">34%</span>
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar bg-danger" style="width: 34%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-danger">15 Déc 2024</span><br>
                                            <small class="text-muted">Dans 42 jours</small>
                                        </td>
                                        <td>
                                            <small>
                                                • Concurrence élevée<br>
                                                • Budget incertain<br>
                                                • Décision reportée
                                            </small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger">Requalifier</button>
                                            <button class="btn btn-sm btn-primary">Détails</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- AI Insights Panel -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Recommandations IA</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="alert alert-success">
                                            <h6 class="alert-heading"><i class="fas fa-lightbulb"></i> Opportunité à saisir</h6>
                                            <p class="mb-1">L'opportunité TechCorp a 87% de chance de se conclure. <strong>Action recommandée:</strong> Programmer un appel de finalisation cette semaine.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-warning">
                                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Attention requise</h6>
                                            <p class="mb-1">StartupXYZ montre des signes d'hésitation. <strong>Action recommandée:</strong> Proposer une démonstration personnalisée.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-info">
                                            <h6 class="alert-heading"><i class="fas fa-chart-line"></i> Tendance positive</h6>
                                            <p class="mb-1">Le secteur technologique montre une croissance de 15%. <strong>Focus recommandé:</strong> Prospects dans ce secteur.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

     
            <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
        </div>
        <!-- End of Wrapper -->
    </div>
     


<script src="assets/vendor/chart.js/Chart.min.js"></script>
<script>
// Graphique des prévisions
const ctx = document.getElementById('forecastChart').getContext('2d');
const forecastChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
        datasets: [{
            label: 'Réalisé',
            data: [65000, 78000, 82000, 91000, 76000, 88000, 95000, 87000, 92000, 89000, null, null],
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            borderWidth: 2,
            fill: false
        }, {
            label: 'Prévision IA',
            data: [null, null, null, null, null, null, null, null, null, 89000, 94000, 98000],
            borderColor: '#36b9cc',
            backgroundColor: 'rgba(54, 185, 204, 0.1)',
            borderWidth: 2,
            borderDash: [5, 5],
            fill: false
        }, {
            label: 'Fourchette haute',
            data: [null, null, null, null, null, null, null, null, null, 95000, 102000, 108000],
            borderColor: '#f6c23e',
            backgroundColor: 'rgba(246, 194, 62, 0.05)',
            borderWidth: 1,
            fill: '+1'
        }, {
            label: 'Fourchette basse',
            data: [null, null, null, null, null, null, null, null, null, 83000, 86000, 88000],
            borderColor: '#f6c23e',
            backgroundColor: 'rgba(246, 194, 62, 0.05)',
            borderWidth: 1,
            fill: false
        }]
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 12
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return '€' + (value / 1000) + 'k';
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }]
        },
        legend: {
            display: true
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': €' + tooltipItem.yLabel.toLocaleString();
                }
            }
        }
    }
});

// Actualisation des prévisions
document.getElementById('refreshForecast').addEventListener('click', function() {
    const btn = this;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calcul...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-sync-alt"></i> Actualiser';
        btn.disabled = false;
        
        // Notification
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.innerHTML = `
            <strong>Succès!</strong> Les prévisions IA ont été actualisées.
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.container-fluid').firstChild);
        
        setTimeout(() => alert.remove(), 5000);
    }, 2000);
});

// Filtres de probabilité
document.querySelectorAll('[data-filter]').forEach(filter => {
    filter.addEventListener('click', function(e) {
        e.preventDefault();
        const filterValue = this.dataset.filter;
        
        // Logique de filtrage ici
        console.log('Filtrage par:', filterValue);
    });
});

// Boutons de période
document.querySelectorAll('[data-period]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const period = this.dataset.period;
        // Mettre à jour le graphique selon la période
        console.log('Période sélectionnée:', period);
    });
});
</script>


    
</body>
</html>