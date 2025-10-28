<?php
include 'includes/customers.php';
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
                        <i class="fas fa-user-tie text-primary"></i> Gestion des Clients
                    </h1>
                    <div class="btn-group">
                        <a href="customers-export.php" class="btn btn-outline-primary">
                            <i class="fas fa-file-export"></i> Exporter
                        </a>
                        <a href="customers-add.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Client
                        </a>
                    </div>
                </div>

                <!-- KPIs Clients -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Clients
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-customers">
                                            <?php echo (int)$total_clients; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                            Clients Actifs
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-customers">
                                            <?php echo (int)$active_clients; ?>
                                        </div>
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
                                            Revenus Moyens
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="avg-revenue">
                                            €<?php echo $avg_revenue ? number_format($avg_revenue, 2, ',', ' ') : '0'; ?>
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
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Satisfaction Moyenne
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="satisfaction-score">
                                            <?php echo $satisfaction_score ? number_format($satisfaction_score, 1, ',', ' ') : '0'; ?>/10
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des clients -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Liste de mes Clients</h6>
                        <a href="customers-add.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Ajouter
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Type</th>
                                        <th>Revenus</th>
                                        <th>Statut</th>
                                        <th>Dernière activité</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['industry']); ?></td>
                                        <td>€<?php echo $customer['annual_revenue'] ? number_format($customer['annual_revenue'], 2, ',', ' ') : '0'; ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                switch ($customer['status']) {
                                                    case 'client': echo 'success'; break;
                                                    case 'prospect': echo 'info'; break;
                                                    case 'partner': echo 'primary'; break;
                                                    default: echo 'secondary';
                                                }
                                            ?>">
                                                <?php echo htmlspecialchars($customer['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            echo isset($customer['updated_at']) ? date('d/m/Y', strtotime($customer['updated_at'])) : '';
                                            ?>
                                        </td>
                                        <td>
                                            <a href="customers-view.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>
                                            <a href="customers-edit.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-warning" title="Éditer"><i class="fas fa-edit"></i></a>
                                            <a href="customers-delete.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer ce client ?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($customers)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Aucun client trouvé.</td>
                                    </tr>
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