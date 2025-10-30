<?php
include("includes/verify_subscriptions.php");
include("includes/leads.php");

$page_title = "Leads - CRM Intelligent";
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
                        <i class="fas fa-bullseye text-primary"></i> Gestion des Leads
                    </h1>
                    <div class="btn-group">
                        <a href="leads-export.php" class="btn btn-outline-primary">
                            <i class="fas fa-file-export"></i> Exporter
                        </a>
                        <a href="leads-add.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Lead
                        </a>
                    </div>
                </div>

                <!-- KPIs Leads -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Nouveaux Leads
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="new-leads"><?php echo isset($leads_kpis['new_leads']) ? intval($leads_kpis['new_leads']) : 0; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-plus fa-2x text-gray-300"></i>
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
                                            Leads Qualifiés
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="qualified-leads"><?php echo isset($leads_kpis['qualified']) ? intval($leads_kpis['qualified']) : 0; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check fa-2x text-gray-300"></i>
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
                                            Taux de Conversion
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="conversion-rate"><?php echo isset($leads_kpis['conversion_rate']) ? htmlspecialchars($leads_kpis['conversion_rate']) : '0%'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                            Score Moyen IA
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="avg-score"><?php echo isset($leads_kpis['avg_score']) ? htmlspecialchars($leads_kpis['avg_score']) : '--'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-brain fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="search-leads" placeholder="Rechercher...">
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="filter-status">
                                    <option value="">Tous les statuts</option>
                                    <option value="new">Nouveau</option>
                                    <option value="contacted">Contacté</option>
                                    <option value="qualified">Qualifié</option>
                                    <option value="unqualified">Non qualifié</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="filter-source">
                                    <option value="">Toutes les sources</option>
                                    <option value="website">Site web</option>
                                    <option value="social_media">Réseaux sociaux</option>
                                    <option value="referral">Recommandation</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="filter-score">
                                    <option value="">Tous les scores</option>
                                    <option value="hot">Chaud (80-100)</option>
                                    <option value="warm">Tiède (60-79)</option>
                                    <option value="cold">Froid (0-59)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-secondary me-2" onclick="resetFilters()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                                <button class="btn btn-info" onclick="refreshScoring()">
                                    <i class="fas fa-brain"></i> Scorer avec IA
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des leads -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list"></i> Liste des Leads
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="leadsTable">
                                <thead>
                                    <tr>
                                        <th>Score IA</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Entreprise</th>
                                        <th>Source</th>
                                        <th>Statut</th>
                                        <th>Créé le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="leads-list">
                                    <?php if (!empty($leads_list)): ?>
                                        <?php foreach ($leads_list as $lead): ?>
                                            <tr>
                                                <td><?php echo isset($lead['ai_score']) && $lead['ai_score'] !== null ? intval($lead['ai_score']) : '--'; ?></td>
                                                <td><?php echo htmlspecialchars(trim(($lead['first_name'] ?? '').' '.($lead['last_name'] ?? ''))); ?></td>
                                                <td><?php echo htmlspecialchars($lead['email'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($lead['company_name'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($lead['source'] ?? ''); ?></td>
                                                <td><span class="badge bg-<?php echo (($lead['status'] ?? '') === 'qualified') ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($lead['status'] ?? 'lead'); ?></span></td>
                                                <td><?php echo htmlspecialchars(isset($lead['created_at']) ? date('Y-m-d', strtotime($lead['created_at'])) : ''); ?></td>
                                                <td>
                                                    <a class="btn btn-sm btn-primary" href="contacts-edit.php?id=<?php echo intval($lead['id'] ?? 0); ?>"><i class="fas fa-pen"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" class="text-center text-muted">Aucun lead trouvé.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>