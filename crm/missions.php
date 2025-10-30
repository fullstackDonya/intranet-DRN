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
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-building text-primary"></i> Visites immobilières (Dubai) - DRN
                    </h1>
                    <a href="mission_add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle visite
                    </a>
                </div>

                <!-- Filtres et recherche -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form class="row gy-2 gx-3 align-items-end" method="get" action="missions.php">
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label">Date ≥</label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from ?? ''); ?>">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label">Date ≤</label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to ?? ''); ?>">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label">Statut</label>
                                <input type="text" name="status" class="form-control" placeholder="ex: planned" value="<?php echo htmlspecialchars($status ?? ''); ?>">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label">Agent</label>
                                <input type="text" name="agent" class="form-control" placeholder="nom de l'agent" value="<?php echo htmlspecialchars($agent ?? ''); ?>">
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <label class="form-label">Bien / Adresse / Dossier / Société</label>
                                <input type="text" name="property" class="form-control" placeholder="ex: Marina, Palm, Villa, Dossier X" value="<?php echo htmlspecialchars($property ?? ''); ?>">
                            </div>
                            <div class="col-sm-12 col-md-6 text-end">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Rechercher</button>
                                <a href="missions.php" class="btn btn-outline-secondary"><i class="fas fa-rotate"></i> Réinitialiser</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Petits tableaux de bord -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-primary"><i class="fas fa-list fa-lg"></i></div>
                                    <div>
                                        <div class="small text-muted">Total visites</div>
                                        <div class="fw-bold fs-5"><?php echo (int)$missions_kpis['total']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-success"><i class="fas fa-calendar-day fa-lg"></i></div>
                                    <div>
                                        <div class="small text-muted">Aujourd'hui</div>
                                        <div class="fw-bold fs-5"><?php echo (int)$missions_kpis['today']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-info"><i class="fas fa-calendar-plus fa-lg"></i></div>
                                    <div>
                                        <div class="small text-muted">À venir</div>
                                        <div class="fw-bold fs-5"><?php echo (int)$missions_kpis['upcoming']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-danger"><i class="fas fa-calendar-xmark fa-lg"></i></div>
                                    <div>
                                        <div class="small text-muted">Passées</div>
                                        <div class="fw-bold fs-5"><?php echo (int)$missions_kpis['past']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Liste des visites</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="missionsTable">
                                <thead>
                                    <tr>
                                        <th># Visite</th>
                                        <th>Société</th>
                                        <th># Dossier</th>
                                        <th>Point de rencontre</th>
                                        <th>Bien (adresse)</th>
                                        <th>Date/Heure de visite</th>
                                        <th>Agent</th>
                                        <th>Unité</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="missions-list">
                                    <?php foreach ($missions as $mission): ?>
                                    <?php
                                        $missionId = (int)$mission['mission_id'];
                                        $dtRaw = $mission['datetime'] ?? '';
                                        $dtFmt = $dtRaw ? date('d/m/Y H:i', strtotime($dtRaw)) : '';
                                        $propertyText = trim(($mission['arrival'] ?? ''));
                                        $meetText = trim(($mission['departure'] ?? ''));
                                        $waText = rawurlencode("Bonjour, rappel de votre visite: Ref M-$missionId, le $dtFmt, bien: $propertyText, point de rencontre: $meetText. Merci, DRN.");
                                        $mailtoSub = rawurlencode("Rappel visite M-$missionId");
                                        $mailtoBody = rawurlencode("Bonjour,\n\nRappel de votre visite:\n- Réf: M-$missionId\n- Date/Heure: $dtFmt\n- Bien: $propertyText\n- Point de rencontre: $meetText\n\nMerci,\nDRN");
                                    ?>
                                    <tr>
                                        <td>M-<?php echo htmlspecialchars($missionId); ?></td>
                                        <td><?php echo htmlspecialchars($mission['company_name']); ?></td>
                                        <td>D-<?php echo htmlspecialchars($mission['folder_id']); ?> (<?php echo htmlspecialchars($mission['folder_name']); ?>)</td>
                                        <td><?php echo htmlspecialchars($mission['departure'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($mission['arrival'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($dtFmt); ?></td>
                                        <td><?php echo htmlspecialchars($mission['driver'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($mission['vehicle'] ?? ''); ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($mission['status_name'] ?? 'Non défini'); ?>
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            <a href="mission_view.php?id=<?php echo $missionId; ?>" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>
                                            <a href="mission_edit.php?id=<?php echo $missionId; ?>" class="btn btn-sm btn-warning" title="Éditer"><i class="fas fa-edit"></i></a>
                                            <a href="mission_delete.php?id=<?php echo $missionId; ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer cette visite ?');"><i class="fas fa-trash"></i></a>
                                            <a href="https://wa.me/?text=<?php echo $waText; ?>" target="_blank" class="btn btn-sm btn-success" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                                            <a href="mailto:?subject=<?php echo $mailtoSub; ?>&body=<?php echo $mailtoBody; ?>" class="btn btn-sm btn-secondary" title="Email"><i class="fa-regular fa-envelope"></i></a>
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
