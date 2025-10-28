<?php
include 'includes/missions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-tasks text-primary"></i> Gestion des Missions
                    </h1>
                    <a href="mission_add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle Mission
                    </a>
                </div>

                <!-- Filtres et recherche (à compléter si besoin) -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <!-- ... filtres ... -->
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Liste des Missions</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="missionsTable">
                                <thead>
                                    <tr>
                                        <th># Mission</th>
                                        <th>Société</th>
                                        <th># Dossier</th>
                                        <th>Départ</th>
                                        <th>Arrivée</th>
                                        <th>Date/Heure</th>
                                        <th>Chauffeur</th>
                                        <th>Véhicule</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="missions-list">
                                    <?php foreach ($missions as $mission): ?>
                                    <tr>
                                        <td>M-<?php echo htmlspecialchars($mission['mission_id']); ?></td>
                                        <td><?php echo htmlspecialchars($mission['company_name']); ?></td>
                                        <td>D-<?php echo htmlspecialchars($mission['folder_id']); ?> (<?php echo htmlspecialchars($mission['folder_name']); ?>)</td>
                                        <td><?php echo htmlspecialchars($mission['departure'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($mission['arrival'] ?? ''); ?></td>
                                        <td>
                                            <?php
                                            $datetime = $mission['datetime'] ?? '';
                                            echo $datetime ? htmlspecialchars(date('d/m/Y H:i', strtotime($datetime))) : '';
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($mission['driver'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($mission['vehicle'] ?? ''); ?></td>
                                                              
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($mission['status_name'] ?? 'Non défini'); ?>
                                            </span>
                                        </td>
                                        <td>
                                           <a href="mission_view.php?id=<?php echo $mission['mission_id']; ?>" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>
                                            <a href="mission_edit.php?id=<?php echo $mission['mission_id']; ?>" class="btn btn-sm btn-warning" title="Éditer"><i class="fas fa-edit"></i></a>
                                            <a href="mission_delete.php?id=<?php echo $mission['mission_id']; ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer cette mission ?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
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