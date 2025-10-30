<?php include 'includes/folder_view.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dossier : <?php echo htmlspecialchars($folder['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success">
                    La mission a été ajoutée avec succès !
                </div>
            <?php endif; ?>
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-folder-open text-primary"></i> Détail du Dossier
                    </h1>
                    <a href="folders.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
            <div class="container mb-4">
                <div class="row g-3">
                    <!-- Card 1 : Infos dossier -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-folder"></i> Dossier</h5>
                                <p><strong>Nom :</strong> <?php echo htmlspecialchars($folder['name']); ?></p>
                                <p><strong>Description :</strong> <?php echo htmlspecialchars($folder['description']); ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- Card 2 : Entreprise et dates -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-building"></i> Entreprise</h5>
                                <p><strong>Entreprise :</strong> <?php echo htmlspecialchars($folder['company_name']); ?></p>
                                <p><strong>Date de création :</strong> <?php echo htmlspecialchars(date('d/m/Y', strtotime($folder['created_at']))); ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- Card 3 : Action facture -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm d-flex flex-column justify-content-center align-items-center">
                            <div class="card-body text-center">
                                <h5 class="card-title"><i class="fas fa-file-invoice"></i> Facturation</h5>
                                <a href="generate_invoice.php?folder_id=<?php echo $folder_id; ?>" class="btn btn-success mt-2">
                                    <i class="fas fa-file-invoice"></i> Générer la facture du dossier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <h2>Dossier : <?php echo htmlspecialchars($folder['name']); ?></h2>
                    <p><strong>Entreprise :</strong> <?php echo htmlspecialchars($folder['company_name']); ?></p>
                    <p><strong>Description :</strong> <?php echo htmlspecialchars($folder['description']); ?></p>
                    <p><strong>Date de création :</strong> <?php echo htmlspecialchars(date('d/m/Y', strtotime($folder['created_at']))); ?></p>
                    <hr>
                    <br><br><br><br><br><br><br>
                    

                    <h4>Ajouter une mission à ce dossier</h4>
                    <?php if (!empty($mission_error)): ?>
                        <div class="alert alert-danger"><?php echo $mission_error; ?></div>
                    <?php endif; ?>
                    <form method="post" class="row g-3 mb-4" id="mission-form">
                        <input type="hidden" name="folder_id" value="<?php echo htmlspecialchars($folder_id); ?>">
                        <div class="col-md-6">
                            <div id="mission-type-guide">
                                <i class="fas fa-arrow-down"></i> Sélectionnez le type de mission immobilière (Dubaï).
                            </div>
                            <select name="type" id="mission-type" class="form-control" required>
                                <option value="visite">Visite</option>
                                <option value="offre">Offre</option>
                                <option value="vente">Vente</option>
                            </select>
                        </div>
                    
                        <div class="col-md-3">
                            <input type="text" name="name" class="form-control" placeholder="Nom de la mission" required>
                        </div>
                        
                        <div class="col-md-4">
                            <select name="property_id" class="form-control" aria-label="Bien (catalogue)">
                                <option value="">— Bien (catalogue) —</option>
                                <?php foreach (($properties ?? []) as $p): ?>
                                    <?php 
                                        $label = $p['name'];
                                        if (!empty($p['community'])) { $label .= ' · '.$p['community']; }
                                        if (!empty($p['building'])) { $label .= ' · '.$p['building']; }
                                        if (!empty($p['unit_ref'])) { $label .= ' · '.$p['unit_ref']; }
                                    ?>
                                    <option value="<?php echo (int)$p['id']; ?>"><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="status_id" class="form-control" required aria-placeholder="Statut">
                                <?php foreach ($all_statuses as $stat): ?>
                                    <option value="<?php echo $stat['id']; ?>"><?php echo htmlspecialchars($stat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="dynamic-fields" class="row g-3 col-md-10"></div>

                        <div class="col-md-1">
                            <button type="submit" name="add_mission" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </form>

                    
                    <br><br><br><br><br><br><br>

                    <h4>Missions liées à ce dossier</h4>

                    <ul class="nav nav-tabs mt-3" id="missionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">Toutes</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="visite-tab" data-bs-toggle="tab" data-bs-target="#visite" type="button" role="tab">Visites</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="offre-tab" data-bs-toggle="tab" data-bs-target="#offre" type="button" role="tab">Offres</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="vente-tab" data-bs-toggle="tab" data-bs-target="#vente" type="button" role="tab">Ventes</button>
                        </li>
                    </ul>
                    <br><br>
                    <div class="tab-content pt-3">
                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Dossier</th>
                                            <th>Mission</th>
                                            <th>Type</th>
                                            <th>Projet</th>
                                            <th>Bien</th>
                                            <th>Bien (catalogue)</th>
                                            <th>Prix (AED)</th>
                                            <th>Date/Heure</th>
                                            <th>Client</th>
                                            <th>Responsable</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                            <th>Voir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($missions as $mission): ?>
                                        <tr>
                                            <td>D - <?php echo htmlspecialchars($folder_id); ?></td>
                                            <td>M -<?php echo htmlspecialchars($mission['id'] ?? ''); ?></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($mission['type'] ?? ''); ?></span></td>
                                            <td><?php echo htmlspecialchars($mission['project'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['product'] ?? ''); ?></td>
                                            <td>
                                                <?php 
                                                    $cat = '';
                                                    if (!empty($mission['property_name'])) {
                                                        $parts = [$mission['property_name']];
                                                        if (!empty($mission['property_community'])) { $parts[] = $mission['property_community']; }
                                                        if (!empty($mission['property_building'])) { $parts[] = $mission['property_building']; }
                                                        if (!empty($mission['property_unit_ref'])) { $parts[] = $mission['property_unit_ref']; }
                                                        $cat = implode(' · ', $parts);
                                                    }
                                                    echo htmlspecialchars($cat);
                                                ?>
                                            </td>
                                            <td><?php $prix = $mission['prix'] ?? ''; echo ($prix !== '' ? number_format((float)$prix, 0, ',', ' ') . ' AED' : ''); ?></td>
                                            <td><?php $datetime = $mission['datetime'] ?? ''; echo $datetime ? htmlspecialchars(date('d/m/Y H:i', strtotime($datetime))) : ''; ?></td>
                                            <td><?php echo htmlspecialchars($mission['client'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['responsible'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['status_name'] ?? ''); ?></td>
                                            <td>
                                                <a href="mission_edit.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-warning" title="Éditer"><i class="fas fa-edit"></i></a>
                                                <a href="mission_delete.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer cette mission ?');"><i class="fas fa-trash"></i></a>
                                            </td>
                                            <td>
                                                <a href="mission_view.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="visite" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mission</th>
                                            <th>Projet</th>
                                            <th>Bien</th>
                                            <th>Bien (catalogue)</th>
                                            <th>Date/Heure</th>
                                            <th>Client</th>
                                            <th>Responsable</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($missions as $mission): if(($mission['type'] ?? '') !== 'visite') continue; ?>
                                        <tr>
                                            <td>M -<?php echo htmlspecialchars($mission['id'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['project'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['product'] ?? ''); ?></td>
                                            <td>
                                                <?php 
                                                    $cat = '';
                                                    if (!empty($mission['property_name'])) {
                                                        $parts = [$mission['property_name']];
                                                        if (!empty($mission['property_community'])) { $parts[] = $mission['property_community']; }
                                                        if (!empty($mission['property_building'])) { $parts[] = $mission['property_building']; }
                                                        if (!empty($mission['property_unit_ref'])) { $parts[] = $mission['property_unit_ref']; }
                                                        $cat = implode(' · ', $parts);
                                                    }
                                                    echo htmlspecialchars($cat);
                                                ?>
                                            </td>
                                            <td><?php $datetime = $mission['datetime'] ?? ''; echo $datetime ? htmlspecialchars(date('d/m/Y H:i', strtotime($datetime))) : ''; ?></td>
                                            <td><?php echo htmlspecialchars($mission['client'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['responsible'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['status_name'] ?? ''); ?></td>
                                            <td>
                                                <a href="mission_edit.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-warning" title="Éditer"><i class="fas fa-edit"></i></a>
                                                <a href="mission_delete.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer cette mission ?');"><i class="fas fa-trash"></i></a>
                                                <a href="mission_view.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="offre" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mission</th>
                                            <th>Projet</th>
                                            <th>Bien</th>
                                            <th>Bien (catalogue)</th>
                                            <th>Prix (AED)</th>
                                            <th>Client</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($missions as $mission): if(($mission['type'] ?? '') !== 'offre') continue; ?>
                                        <tr>
                                            <td>M -<?php echo htmlspecialchars($mission['id'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['project'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['product'] ?? ''); ?></td>
                                            <td>
                                                <?php 
                                                    $cat = '';
                                                    if (!empty($mission['property_name'])) {
                                                        $parts = [$mission['property_name']];
                                                        if (!empty($mission['property_community'])) { $parts[] = $mission['property_community']; }
                                                        if (!empty($mission['property_building'])) { $parts[] = $mission['property_building']; }
                                                        if (!empty($mission['property_unit_ref'])) { $parts[] = $mission['property_unit_ref']; }
                                                        $cat = implode(' · ', $parts);
                                                    }
                                                    echo htmlspecialchars($cat);
                                                ?>
                                            </td>
                                            <td><?php $prix = $mission['prix'] ?? ''; echo ($prix !== '' ? number_format((float)$prix, 0, ',', ' ') . ' AED' : ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['client'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['status_name'] ?? ''); ?></td>
                                            <td>
                                                <a href="mission_edit.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-warning" title="Éditer"><i class="fas fa-edit"></i></a>
                                                <a href="mission_delete.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer cette mission ?');"><i class="fas fa-trash"></i></a>
                                                <a href="mission_view.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="vente" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mission</th>
                                            <th>Projet</th>
                                            <th>Bien</th>
                                            <th>Prix (AED)</th>
                                            <th>Date/Heure</th>
                                            <th>Client</th>
                                            <th>Responsable</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($missions as $mission): if(($mission['type'] ?? '') !== 'vente') continue; ?>
                                        <tr>
                                            <td>M -<?php echo htmlspecialchars($mission['id'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['project'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['product'] ?? ''); ?></td>
                                            <td><?php $prix = $mission['prix'] ?? ''; echo ($prix !== '' ? number_format((float)$prix, 0, ',', ' ') . ' AED' : ''); ?></td>
                                            <td><?php $datetime = $mission['datetime'] ?? ''; echo $datetime ? htmlspecialchars(date('d/m/Y H:i', strtotime($datetime))) : ''; ?></td>
                                            <td><?php echo htmlspecialchars($mission['client'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['responsible'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($mission['status_name'] ?? ''); ?></td>
                                            <td>
                                                <a href="mission_edit.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-warning" title="Éditer"><i class="fas fa-edit"></i></a>
                                                <a href="mission_delete.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer cette mission ?');"><i class="fas fa-trash"></i></a>
                                                <a href="mission_view.php?id=<?php echo $mission['id']; ?>" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <a href="folders.php" class="btn btn-secondary mt-3">Retour aux dossiers</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/folder_view.js" defer></script>
 
</body>
</html>