<?php
require_once 'includes/folders.php';
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

                <!-- Section infos utilisateur/customer/entreprises -->
                <div class="row g-3 mb-4">
                    <!-- Card 1 : Infos utilisateur -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-user"></i> </h5>
                                <?php if ($customer): ?>
                                    <p><?php echo htmlspecialchars($customer['name']); ?></p>
                                    <p><?php echo htmlspecialchars($user['username']); ?></p>
                                    <?php if (!empty($user['email'])): ?>
                                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="text-muted">Aucune info client disponible.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Card 2 : Entreprises liées -->
                    <div class="col-md-8">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-building"></i> </h5>
                                <?php if (!empty($companies)): ?>
                                    <ul class="mb-0">
                                        
                                             <li>
                                                <strong><?php echo htmlspecialchars($customer['name']); ?></strong>
                                                <?php if (!empty($customer['email'])): ?>
                                                    <span class="text-muted"> (<?php echo htmlspecialchars($customer['email']); ?>)</span>
                                                <?php endif; ?>
                                            </li>
                             
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted">Aucune entreprise liée à ce client.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin section infos utilisateur/customer/entreprises -->


                
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-folder-open text-primary"></i> Gestion des Dossiers
                    </h1>
                    <div class="btn-group">
                        <a href="folders-export.php" class="btn btn-outline-primary">
                            <i class="fas fa-file-export"></i> Exporter
                        </a>
                        <a href="folder_add.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Dossier
                        </a>
                    </div>
                </div>

                <!-- Filtres et recherche -->
                <form method="get" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher un dossier" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                    </div>
                </form>

                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Liste des Dossiers</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="foldersTable">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Entreprise</th>
                                        <th>Date de création</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($folders)): ?>
                                        <?php foreach ($folders as $folder): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($folder['name']); ?></td>
                                                <td><?php echo htmlspecialchars($folder['company_name']); ?></td>
                                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($folder['created_at']))); ?></td>
                                                <td>
                                                    <?php
                                                    $status = $folder['status_name'] ?? 'Non défini';
                                                    $badge = 'bg-secondary';
                                                    $icon = 'fa-clock';
                                                    if ($status === 'Terminée') {
                                                        $badge = 'bg-success';
                                                        $icon = 'fa-check';
                                                    } elseif ($status === 'En cours') {
                                                        $badge = 'bg-warning text-dark';
                                                        $icon = 'fa-spinner';
                                                    } elseif ($status === 'Ouverte') {
                                                        $badge = 'bg-info text-dark';
                                                        $icon = 'fa-play';
                                                    } elseif ($status === 'Facturée') {
                                                        $badge = 'bg-primary';
                                                        $icon = 'fa-file-invoice';
                                                    } elseif ($status === 'Payée') {
                                                        $badge = 'bg-success';
                                                        $icon = 'fa-euro-sign';
                                                    } elseif ($status === 'Annulée') {
                                                        $badge = 'bg-danger';
                                                        $icon = 'fa-times';
                                                    } elseif ($status === 'Devis en cours') {
                                                        $badge = 'bg-secondary';
                                                        $icon = 'fa-file-signature';
                                                    } elseif ($status === 'Devis accepté') {
                                                        $badge = 'bg-success';
                                                        $icon = 'fa-thumbs-up';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $badge; ?>">
                                                        <i class="fas <?php echo $icon; ?>"></i> <?php echo htmlspecialchars($status); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="folder_view.php?id=<?php echo $folder['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                                    <a href="folder_edit.php?id=<?php echo $folder['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                    <a href="folder_delete.php?id=<?php echo $folder['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce dossier ?');"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Aucun dossier trouvé.</td>
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